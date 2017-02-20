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

class PluginItopExportPrinter extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itop_class = 'Printer';
   public $filename   = 'printer';

   function getHeaders() {
      return array('org_id' , 'location_id' ,
                   'asset_number' , 'primary_key' , 'name' , 'description' ,
                   'serialnumber' , 'model_id' , 'brand_id' ,
                   'glpi_uniqueid', 'purchase_date',
                   'move2production', 'status', 'end_of_warranty');
   }

   function getSQLQuery() {
      $condition = $this->getTypeRestriction('Printer', 'p', 'Printer');
      if (!$condition) {
         return false;
      }

      $query = "SELECT ".$this->getEntityUniqueID('p').",
                       ".$this->getLocationUniqueID('p').",
                       ".$this->getManufacturerUniqueID('p').",
                       ".$this->getItemUniqueID('p', 'Printer').",
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'PrinterModel',
                                                            'p.printermodels_id')." as model_id,
                       p.name as primary_key, p.otherserial as asset_number, p.name as name,
                       p.comment as description, p.serial as serialnumber, s.status_itop as status,"
                       .$this->getInfocomSelectSQL()."
                FROM glpi_printers as p
                LEFT JOIN glpi_infocoms as i ON (i.items_id=p.id AND i.itemtype='Printer')
                LEFT JOIN glpi_plugin_itop_states as s ON (p.states_id=s.states_id)
                WHERE $condition
                   AND p.is_deleted='0'
                   AND p.is_template='0'
                   AND p.entities_id>=0";
       return $query;
   }
}
