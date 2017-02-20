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

class PluginItopExportVirtualMachine extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itop_class = 'Virtual Machine';

   function getHeaders() {
      return array('org_id' , 'primary_key' , 'name' , 'description' ,
                   'osfamily_id' , 'osversion_id', 'glpi_uniqueid',
                   'move2production','cpu','ram', 'virtualhost_id');
   }

   function getSQLQuery() {
      return ''; }

   function getDataToExport() {
      global $DB;

      $results   = array();
      $condition = $this->getTypeRestriction('Computer', 'co', 'Server');
      if (!$condition) {
         return false;
      }

      $query = "SELECT ".$this->getEntityUniqueID('c').",
                       CONCAT_WS('-', ".$this->getGlpiServerID().",
                                 'Hypervisor', c.computers_id) as virtualhost_id,
                       c.name as primary_key, c.name as name,
                       c.comment as description, c.vcpu as cpu,
                       c.ram as ram, c.uuid as uuid
                FROM glpi_computervirtualmachines as c
                LEFT JOIN glpi_computers as co ON (co.id = c.computers_id)
                WHERE $condition
                   AND c.is_deleted='0'
                   AND c.entities_id>=0";

      $virtualmachine = new ComputerVirtualMachine();
      $computer       = new Computer();
      $infocom        = new Infocom();

      foreach ($DB->request($query) as $data) {
         $vm_id = ComputerVirtualMachine::findVirtualMachine($data);
         if ($vm_id) {
            $computer->getFromDB($vm_id);
            $data['glpi_uniqueid'] = $this->getGlpiServerID().'-ComputerVirtualMachine-'.$vm_id;
            if ($computer->fields['name'] != '') {
               $data['name'] = $data['primary_key'] = $computer->fields['name'];
            }
            if ($computer->fields['comment'] != '') {
               $data['description'] = $computer->fields['comment'];
            }
            if ($computer->fields['operatingsystems_id']) {
               $data['osfamily_id']
                  = $this->getGlpiServerID().'-OperatingSystem-'.
                       $computer->fields['operatingsystems_id'];
            }
            if ($computer->fields['operatingsystemversions_id']) {
               $data['osversion_id']
                  = $this->getGlpiServerID().'-OperatingSystemVersion-'.
                       $computer->fields['operatingsystemversions_id'];
            }
            $infocom->getFromDBforDevice('Computer', $vm_id);
            if ($infocom->fields['use_date']) {
               $data['move2production'] = $infocom->fields['use_date'];
            }
            $results[] = $data;
         } else {
            continue;
         }
      }
      return $results;
   }

}
