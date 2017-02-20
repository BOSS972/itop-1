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

//Export data in itop synchronization table
class PluginItopOutputSql implements PluginItopOutputInterface {

   function export(PluginItopExportInterface $data_class, $config = array()) {
      global $DB;

      $type   = $data_class->itop_class;

      $index  = 0;
      $DBItop = new PluginItopDbItop();

      if (!empty($config['itop_datasources'])
        && isset($config['itop_datasources'][$type])) {
         $class         = $data_class;
         $synchro_table = $config['itop_datasources'][$type];
         $processed     = array();

         $classname      = $config['mappings'][$type];
           $glpi_server_id = $config['general']['glpi_server_id'];
           $itop_class     = new $classname($glpi_server_id);
         $classname::forceTable($synchro_table);

         $headers     = $itop_class->getHeaders();
         $headers_sql = implode(',', $headers);

         $DBold = $DB;
         $DB    = $DBItop;
         $DB->query("DELETE FROM `$synchro_table`");
         $DB = $DBold;

         $valuestoexport = $itop_class->getDataToExport();
         if (empty($valuestoexport)) {
            return $index;
         }

         foreach ($valuestoexport as $data) {
            $DBold = $DB;
            $DB    = $DBItop;

            $tmp   = array();

            foreach ($headers as $header) {
               if (isset($data[$header])) {
                     $tmp[$header] = addslashes($data[$header]);
               }
            }

                $itop_class->add($tmp);

            $DB = $DBold;
            $index++;
         }

         $result = $DBItop->query('SELECT id FROM priv_sync_datasource WHERE database_table_name="'.$synchro_table.'" LIMIT 1');
         $aRow = $result->fetch_assoc();

         if (isset($aRow['id'])) {
            $this->synchronizeWithiTop($aRow['id']);
         }

         return $index;
      }
   }

   function synchronizeWithiTop($data_source_id) {

      $config = PluginItopToolbox::readItopConfiguration();
      if (isset($config['itop']['active'])
      && $config['itop']['active']) {
         $cmd = $config['itop']['php_path']." ".
          $config['itop']['itop_path']."synchro/synchro_exec.php --auth_user=".
          $config['itop']['itop_user']." --auth_pwd=".
          $config['itop']['itop_pwd']." --data_sources=".$data_source_id;

         $aOutput = array();
         $iRetCode = 0;
         exec($cmd, $aOutput, $iRetCode);
         if ($iRetCode != 0) {
             echo "Error: retcode=".$iRetCode;
         }
      }
   }
}
