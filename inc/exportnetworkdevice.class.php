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

class PluginItopExportNetworkDevice extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itop_class = 'NetworkDevice';

   function getHeaders() {
      return array('org_id' , 'location_id' ,
                   'asset_number' , 'primary_key' , 'name' , 'description' ,
                   'serialnumber' , 'model_id' , 'brand_id' , 'networkdevicetype_id',
                   'iosversion_id', 'ram', 'glpi_uniqueid');
   }

   function getFieldsToFilter() {
      return array('name', 'org_id', 'networkdevicetype_id', 'iosversion_id', 'location_id');
   }

   function getSQLQuery() {
      $condition = $this->getTypeRestriction('NetworkEquipment', 'n', 'NetworkDevice');
      if (!$condition) {
         return false;
      }

      $query = "SELECT n.otherserial as asset_number, n.name as primary_key, n.name as name,
                       n.comment as description, n.serial as serialnumber, n.ram as ram,
                       s.status_itop as status,
                       ".$this->getItemUniqueID('n', 'NetworkEquipment').",
                       ".$this->getEntityUniqueID('n').",
                       ".$this->getManufacturerUniqueID('n').",
                       ".$this->getLocationUniqueID('n').",
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'NetworkEquipmentFirmware',
                                                            'n.networkequipmentfirmwares_id')." as iosversion_id,
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'NetworkEquipmentType',
                                                            'n.networkequipmenttypes_id')." as networkdevicetype_id,
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'NetworkEquipmentModel',
                                                            'n.networkequipmentmodels_id')." as model_id
                FROM glpi_networkequipments as n
                LEFT JOIN glpi_plugin_itop_states as s ON (n.states_id=s.states_id)
                WHERE $condition
                   AND n.is_deleted='0' AND n.is_template='0'
                   AND n.entities_id>=0
                   AND n.networkequipmenttypes_id>0";
       return $query;
   }
}
