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

class PluginItopToolbox {

   static function filterData($values, $fields = array('name')) {
      $filteredData = array();

      foreach ($values as $data) {
         foreach ($fields as $field) {
            if (isset($data[$field])) {
               if ($data[$field] == '') {
                  continue;
               } else if ($field != 'org_id'
                     && preg_match("/[0-9]-[a-zA-Z]+-0/", $data[$field])) {
                  $data[$field] = '';
               }
            }
         }

         $filteredData[] = $data;
      }
      return $filteredData;
   }


   static function readConfiguration() {
      //config-local.ini is a user defined configuration file
      //if present it is loaded instead of the dfault config.ini file
      if (file_exists(GLPI_ROOT.'/plugins/itop/config/config-local.ini')) {
         return self::readConfigurationFile('config-local.ini');
      } else {
         return self::readConfigurationFile('config.ini');
      }
   }

   static function readItopConfiguration() {
      return self::readConfigurationFile('itop.ini');
   }

   static function readConfigurationFile($file) {
      $config = parse_ini_file(GLPI_ROOT.'/plugins/itop/config/'.$file, true);
      if (empty($config)) {
         return false;
      }
      return $config;
   }

   static function getGlpiUniqueID($glpi_server_id, $itemtype, $glpi_id) {
      return "CONCAT_WS('-', $glpi_server_id, '$itemtype', $glpi_id)";
   }
}
