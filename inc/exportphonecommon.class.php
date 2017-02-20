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

class PluginItopExportPhoneCommon extends PluginItopExportCommon
  implements PluginItopExportInterface {

   function getHeaders() {
      return array('org_id' , 'location_id' ,
                   'asset_number' , 'primary_key' , 'name' , 'description' ,
                   'serialnumber' , 'model_id' , 'brand_id', 'glpi_uniqueid',
                   'status', 'end_of_warranty');
   }

   function getFieldsToFilter() {
      return array('name', 'org_id', 'brand_id');
   }

   function getSQLQuery() {

      $condition = $this->getTypeRestriction('Phone', 'p', $this->itop_class);
      if (!$condition) {
         return false;
      }

      $table = getTableForItemType($this->itemtype);
      $query = "SELECT p.otherserial as asset_number, p.name as primary_key,
                       p.name as name, p.comment as description,
                       p.serial as serialnumber, s.status_itop as status,
                       ".$this->getItemUniqueID('p', $this->itemtype)." ,
                       ".$this->getManufacturerUniqueID('p')." ,
                       ".$this->getEntityUniqueID('p')." ,
                       ".$this->getLocationUniqueID('p').",
                       ".$this->getInfocomSelectSQL()."
                FROM $table as p
                LEFT JOIN glpi_infocoms as i ON (i.items_id=p.id AND i.itemtype='".$this->itemtype."')
                LEFT JOIN glpi_plugin_itop_states as s ON (p.states_id=s.states_id)
                WHERE $condition AND p.is_deleted='0' AND p.is_template='0' AND p.entities_id>=0";
       return $query;
   }
}
