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

abstract class PluginItopExportComputerCommon extends PluginItopExportCommon
  implements PluginItopExportInterface {

   var $check_os   = false;

   function getFieldsToFilter() {
      return array('name', 'model_id', 'brand_id', 'osfamily_id',
                   'osversion_id', 'location_id');
   }

   function getHeaders() {
      return array('org_id' , 'location_id' ,
                   'asset_number' , 'primary_key' , 'name' , 'description' ,
                   'serialnumber' , 'model_id' , 'brand_id' , 'osfamily_id' ,
                   'osversion_id', 'glpi_uniqueid', 'purchase_date',
                   'move2production', 'status', 'end_of_warranty');
   }

   function getSQLQuery() {
      $condition = $this->getTypeRestriction('Computer', 'c', $this->itop_class);
      if (!$condition) {
         return false;
      }

      $query = "SELECT ".$this->getEntityUniqueID('c').",
                       ".$this->getLocationUniqueID('c').",
                       ".$this->getManufacturerUniqueID('c').",
                       ".$this->getItemUniqueID('c', 'Computer').",
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'OperatingSystem',
                                                            'c.operatingsystems_id')." as osfamily_id,
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'OperatingSystemVersion',
                                                            'c.operatingsystemversions_id')." as osversion_id,
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'ComputerModel',
                                                            'c.computermodels_id')." as model_id,
                       c.name as primary_key, c.otherserial as asset_number, c.name as name,
                       c.comment as description, c.serial as serialnumber, s.status_itop as status,"
                       .$this->getInfocomSelectSQL()."
                FROM glpi_computers as c
                LEFT JOIN glpi_infocoms as i ON (i.items_id=c.id AND i.itemtype='Computer')
                LEFT JOIN glpi_plugin_itop_states as s ON (c.states_id=s.states_id)
                WHERE $condition
                   AND c.is_deleted='0' AND c.is_template='0'
                   AND c.entities_id>=0";

      if ($this->check_os) {
         $query.= " AND c.operatingsystems_id>0";
      }
      return $query;
   }
}
