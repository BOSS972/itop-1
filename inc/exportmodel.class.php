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

class PluginItopExportModel extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itop_class = 'Model';

   function getSQLQuery() {
      return ''; }

   function getHeaders() {
      return array('primary_key', 'name', 'brand_id',
                   'type', 'glpi_uniqueid');
   }

   function getFieldsToFilter() {
      return array('name', 'brand_id');
   }

   function getDataToExport() {
      $models = array();
      $classes = array('Server'        => 'Computer',
                       'PC'            => 'Computer',
                       'NetworkDevice' => 'NetworkEquipment',
                       'Phone'         => 'Phone',
                       'IPPhone'       => 'Phone',
                       'MobilePhone'   => 'Phone',
                       'Printer'       => 'Printer',
                       'Tablet'        => 'Computer');
      foreach ($classes as $itopClass => $itemtype) {
         $tmp = $this->getModelForOneClass($this->getGlpiServerID(), $itopClass, $itemtype);
         if ($tmp) {
            $models = array_merge($models, $tmp);
         }
      }
      return $models;
   }

   function getModelForOneClass($glpi_server_id, $itop_class, $itemtype) {
      global $DB;

      $models      = array();
      $table       = getTableForItemtype($itemtype);
      $type_table  = 'glpi_'.strtolower($itemtype).'types';
      $model_table = 'glpi_'.strtolower($itemtype).'models';
      $model_fk    = strtolower($itemtype).'models_id';
      $type_fk     = strtolower($itemtype).'types_id';

      $condition = $this->getTypeRestriction($itemtype, 'c', $itop_class);
      if (!$condition) {
         return false;
      }

      $query = "SELECT m.name as primary_key,
                                m.name as name,
                                '$itop_class' as type,
                ".$this->getItemUniqueID('m', $itemtype.'Model').",
                ".$this->getManufacturerUniqueID('c', 'Manufacturer')."
                FROM $table as c, $model_table as m, $type_table as t
                WHERE t.id=c.$type_fk
                   AND m.id=c.$model_fk
                   AND c.manufacturers_id>0
                   AND $condition GROUP BY m.name";

      foreach ($DB->request($query) as $data) {
         $models[] = $data;
      }
      return PluginItopToolbox::filterData($models, $this->getFieldsToFilter());
   }
}
