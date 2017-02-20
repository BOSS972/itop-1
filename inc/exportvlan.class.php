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

class PluginItopExportVlan extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itemtype   = 'Vlan';
   public $itop_class = 'VLAN';

   function getHeaders() {
      return array('primary_key', 'vlan_tag', 'org_id',
                   'glpi_uniqueid', 'description');
   }

   function getFieldsToFilter() {
      return array('vlan_tag', 'org_id');
   }

   function getSQLQuery() {
      $query = "SELECT `t`.`tag` as vlan_tag,
                       `t`.`tag` as primary_key,
                       `t`.`name` as description,
      				   ".$this->getEntityUniqueID('t').",
                 ".$this->getItemUniqueID('t', 'Vlan')."
              FROM `glpi_vlans` AS t
              WHERE t.entities_id>=0";
      return $query;
   }
}
