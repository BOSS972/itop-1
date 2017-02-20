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

class PluginItopExportHypervisor extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itop_class = 'Hypervisor';

   function getHeaders() {
      return array('org_id' , 'primary_key' , 'name' ,
                   'description', 'glpi_uniqueid', 'server_id',
                   'move2production', 'status');
   }


   function getSQLQuery() {
      $condition = $this->getTypeRestriction('Computer', 'c', 'Server');
      if (!$condition) {
         return false;
      }

      $query = "SELECT ".$this->getEntityUniqueID('c').",
                       ".$this->getItemUniqueID('c', 'Hypervisor').",
                       ".PluginItopToolbox::getGlpiUniqueID($this->getGlpiServerID(),
                                                            'Computer',
                                                            'c.id')." as server_id,
                       c.name as primary_key, c.name as name,
                       c.comment as description, s.status_itop as status,
                       i.use_date as move2production
                FROM glpi_computers as c
                LEFT JOIN glpi_infocoms as i ON (i.items_id=c.id AND i.itemtype='Computer')
                LEFT JOIN glpi_plugin_itop_states as s ON (c.states_id=s.states_id)
                WHERE $condition
                   AND c.id IN (SELECT DISTINCT computers_id FROM glpi_computervirtualmachines)
                   AND c.is_deleted='0'
                   AND c.entities_id>=0";
       return $query;
   }
}
