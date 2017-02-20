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

ini_set('display_errors', '1');

//Export data in a CSV file
class PluginItopOutputCsvWithImport implements PluginItopOutputInterface {

   static function getFileSuffix($config) {
      if (!isset($config['general']['add_file_suffix'])
         || $config['general']['add_file_suffix'] != 1) {
         return '';
      } else {
         $time = $_SESSION['glpi_currenttime'];
         $time = str_replace(array('-',':',' '), '_', $time);
         return '-'.$time;
      }
   }

   function export(PluginItopExportInterface $data_class, $config = array()) {
      global $DB;
      $DBItop = new PluginItopDbItop();

      //Number of line written on the filesystem
      $index = 0;

      //Open destination file for writing
      if (!isset($config['general']['output_directory'])
      || $config['general']['output_directory'] == ''
       || !is_dir($config['general']['output_directory'])) {
         $directory = PLUGIN_ITOP_CSV_DIR;
      } else {
         $directory = $config['general']['output_directory'];
      }
      if ($directory[strlen($directory)-1] != '/') {
         $directory.= '/';
      }

      $filename  = $directory.$data_class->getFilename();
      $filename .= self::getFileSuffix($config);
      //$filename .= '.csv';

      $idx = 0;
      $iFileIndex = null;
      $CSVFiles = array();

      //Write data, line by line
      foreach ($data_class->getDataToExport() as $line) {
         $tmp = array();
         foreach ($data_class->getHeaders() as $header) {
            if (isset($line[$header])) {
               $tmp[$header] = $line[$header];
            } else {
               $tmp[$header] = '';
            }
         }

         if (($idx == 0) || (($config['general']['chunk_size'] > 0) && (($idx % $config['general']['chunk_size']) == 0))) {
            $this->NextCSVFile($CSVFiles, $iFileIndex, $filename);
            fputcsv($CSVFiles[$iFileIndex], $data_class->getHeaders(), $config['general']['separator']);
         }

         $this->AddRow($CSVFiles[$iFileIndex], $tmp, $config['general']['separator']);
         $idx++;

         self::deleteCSVFiles($config, $data_class, $filename);

         $index++;
      }

      if ($iFileIndex !== null) {
         fclose($CSVFiles[$iFileIndex]);
      }

      $result = $DBItop->query('SELECT id FROM priv_sync_datasource WHERE database_table_name="'.$config['itop_datasources'][$data_class->itop_class].'" LIMIT 1');
      $aRow = $result->fetch_assoc();

      if (isset($aRow['id'])) {
         $this->synchronizeWithiTop($DBItop, $aRow['id'], $config['itop_datasources'][$data_class->itop_class], $data_class, $filename);
      }

      return $index;
   }

   function AddRow($file, $row, $separator) {
      fputcsv($file, $row, $separator);
   }

   function OpenCSVFile(&$files, &$index, $filename) {
      $files[$index] = fopen($filename.'-'.(1+$index).'.csv', 'wb');
   }

   function NextCSVFile(&$files, &$index, $filename) {
      if ($index !== null) {
         fclose($files[$index]);
         $files[$index] = false;
         $index++;
      } else {
         $index = 0;
      }

      $this->OpenCSVFile($files, $index, $filename);
   }

   function synchronizeWithiTop($oDbHandler, $sDataSrcId, $sDBTable, $oDataClass, $sFilename) {
      $config = PluginItopToolbox::readItopConfiguration();

      // Get columns names in csv as parameter-friendly form
      $columns = implode(',', $oDataClass->getHeaders());

      if (isset($config['itop']['active']) && $config['itop']['active']) {
         // Empty the data source table
         $oDbHandler->query("DELETE FROM `".$sDBTable."`");

         // Build the first query : export the csv to the table (in CLI mode)
         $sFileToDB = "sh ".GLPI_ROOT."/plugins/itop/tools/export.sh ".$oDbHandler->dbuser.
         " ".$oDbHandler->dbpassword.
         " ".$oDbHandler->dbdefault.
         " ".$sFilename.
         " ".$sDBTable.
         " \"".$columns."\"";

         exec ($sFileToDB.' > /dev/null 2>/dev/null &');
      }
   }

   static function deleteCSVFiles($config, $data_class, $latest_csv_file) {
      //If no file suffix is to be added, then there's no need for old files deletion
      if (!isset($config['general']['add_file_suffix']) || !$config['general']['add_file_suffix']) {
         return true;
      }

      $files_retention = 10;
      if (isset($config['general']['files_retention'])) {
         $files_retention = $config['general']['files_retention'];
      }

      $index    = 1;
      $filename = $data_class->getFilename();
      $files    = glob ($config['general']['output_directory']."/$filename*");
      $rfiles   = array_reverse($files);
      foreach ($rfiles as $csvfile) {
         if ($index > $files_retention && $csvfile != $latest_csv_file) {
            unlink($csvfile);
         }
         $index++;
      }
   }
}
