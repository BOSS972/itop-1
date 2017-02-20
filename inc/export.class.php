<?php
/*
   ------------------------------------------------------------------------
   MIT License

   Copyright (c) 2017 Teclib'

   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software and associated documentation files (the "Software"), to deal
   in the Software without restriction, including without limitation the rights
   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   copies of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:

   The above copyright notice and this permission notice shall be included in all
   copies or substantial portions of the Software.

   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
   SOFTWARE.

   ------------------------------------------------------------------------

   @package   iTop Plugin
   @author    Teclib'
   @copyright Copyright (c) 2017 Teclib'
   @license   MIT
              https://opensource.org/licenses/MIT
   @link      https://github.com/pluginsGLPI/itop
   @since     2017

   ------------------------------------------------------------------------
 */
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

//Class to manage data export from GLPI to iTop
//The class is composed of a GLPI crontask, performing the export
class PluginItopExport extends CommonDBTM {

   static function cronItopExport($task) {

      //Total of export lines
      $index = 0;

      //$config = parse_ini_file(GLPI_ROOT.'/plugins/itop/config/config.ini', true);
      $config = PluginItopToolbox::readConfiguration();
      if (!$config) {
         return true;
      }

      //Get the class to call to export data
      //for the moment, only CSV output, but webservice in the future ?
      $export_class = new $config['general']['output_method'];

      if (is_a($export_class, 'PluginItopOutputCsvWithImport')) {
         // Call to exec.php in the itop collector for data source table creation/update
         exec ("curl ".$config['general']['collector_executable_url']."?configure_only");
      }

      $iFilesCheck = array();

      //Check if export is enabled in config.ini
      if ($config['general']['active']) {

         //Get all itemtypes to export from the ini file
         foreach ($config['mappings'] as $type => $classname) {

            //Export data using the right method
            $data_class = new $classname($config['general']['glpi_server_id']);

            //Export and add exported line to the counter
            $temp_index = $export_class->export($data_class, $config);
            $index += $temp_index;

            $iFilesCheck[] = ceil($temp_index / $config['general']['chunk_size']);
         }

         if (is_a($export_class, 'PluginItopOutputCsvWithImport')) {
            $tables = array();
            $data_sources_ids = array();

            global $DB;
            $DBItop = new PluginItopDbItop();

            foreach ($config['itop_datasources'] as $itop_class => $table) {
               $result = $DBItop->query('SELECT id FROM priv_sync_datasource WHERE database_table_name="'.$table.'" LIMIT 1');
               $aRow = $result->fetch_assoc();

               if (isset($aRow['id'])) {
                  $data_sources_ids[] = $aRow['id'];
               }
            }

            $directory = $config['general']['output_directory'];

            if ($directory[strlen($directory)-1] != '/') {
               $directory.= '/';
            }

            $directory .= '*.csv';

            $config = PluginItopToolbox::readItopConfiguration();

            // Build the last query : exec sync in itop
            $sExecSync = "curl ".
            $config['itop']['itop_path']."synchro/synchro_exec.php --data \"auth_user=".
            $config['itop']['itop_user']."&auth_pwd=".
            $config['itop']['itop_pwd']."&data_sources=".implode(',', $data_sources_ids)."\"";

            $files = glob($directory);
            if ($files !== false) {
               $filecount = count( $files );
            }

            $fileCheck = array_sum($iFilesCheck);
            $iterations = 0;

            while ($filecount < $fileCheck) {
               sleep(2);

               $files = glob($directory);
               if ($files !== false) {
                  $filecount = count( $files );
               }
            }

            exec ($sExecSync);
            echo $sExecSync;
         }
      }

      //Advise GLPI of the number of exported lines
      $task->addVolume($index);
      return true;
   }

   static function cronInfo($name) {
      return array('description' => __("Itop export", "itop"));
   }


   static function install(Migration $migration) {
      $cron = new CronTask;
      if (!$cron->getFromDBbyName(__CLASS__, 'itopExport')) {
         CronTask::Register(__CLASS__, 'itopExport', 7 * DAY_TIMESTAMP,
                            array('param' => 24, 'mode' => CronTask::MODE_EXTERNAL));
      }
   }

   static function uninstall() {
      CronTask::Unregister(__CLASS__);
   }
}
