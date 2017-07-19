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

abstract class PluginItopExportCommon extends CommonDBTM {

   //GLPI Server ID : there might be several GLPI connected to one iTop instance
   var $glpi_server_id;

   //Name of the CSV file
   var $filename;

   //Itop class name
   var $itop_class;

   //GLPI itemtype
   var $itemtype;

   function __construct($glpi_server_id) {
      $this->setGlpiServerID($glpi_server_id);
   }

   function getFilename() {
      $filename = strtolower($this->itop_class).'s';
      return str_replace(' ', '', $filename);
   }

   function getGlpiServerID() {
      return $this->glpi_server_id;
   }

   function setGlpiServerID($glpi_server_id) {
      $this->glpi_server_id = $glpi_server_id;
   }

   function getFieldsToFilter() {
      return array('name');
   }

   function getDataToExport() {
      return $this->go();
   }

   /**
   * Perform transformation on raw data
   *
   * @param array $data row coming from GLPI DB
   * @return array $data data transformed
   */
   function transformRawData($data) {
      return $data;
   }

   function go() {
      global $DB;

      $data = array();
      $query = $this->getSQLQuery();
      if (!$query) {
         return array();
      } else {
         foreach ($DB->request($query) as $result) {
            $data[] = $this->transformRawData($result);
         }
         return PluginItopToolbox::filterData($data, $this->getFieldsToFilter());
      }
   }

   function getEntityUniqueID($prefix) {
      return PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(), 'Entity', $prefix.'.entities_id')." as org_id";
   }

   function getLocationUniqueID($prefix) {
      return PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(), 'Location', $prefix.'.locations_id')." as location_id";
   }

   function getManufacturerUniqueID($prefix) {
      return PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(), 'Manufacturer', $prefix.'.manufacturers_id')." as brand_id";
   }

   function getItemUniqueID($prefix, $itemtype) {
      return PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(), $itemtype, $prefix.'.id')." as glpi_uniqueid";
   }

   function getInfocomSelectSQL() {
      return "i.buy_date as purchase_date,
              i.use_date as move2production,
              DATE_ADD(`i`.`warranty_date`,
                       INTERVAL `i`.`warranty_duration` MONTH) as end_of_warranty";
   }

   function getTypeRestriction($itemtype, $prefix, $itop_class) {
      $type_fk = strtolower($itemtype).'types_id';
      $types = PluginItopMatching::getTypesForItopClass($itop_class);
      if (empty($types)) {
         return false;
      } else {
         return " $prefix.$type_fk IN (".implode(",", $types).") ";
      }
   }
}
