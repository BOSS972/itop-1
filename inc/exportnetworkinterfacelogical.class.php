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

class PluginItopExportNetworkInterfaceLogical extends PluginItopExportNetworkInterfaceCommon {

   public $itop_class = 'LogicalInterface';

   function getHeaders() {
      return array('name' ,'primary_key', 'ipaddress' ,
                   'macaddress' , 'comment' , 'ipgateway' ,
                   'ipmask' , 'speed' , 'glpi_uniqueid',
                   'virtualmachine_id');
   }

   function getDataToExport() {
      global $DB;

      $types = PluginItopMatching::getTypesForItopClass('Virtual Machine');

      if (empty($types)) {
         return false;
      } else {
         $condition = " ct.id IN (".implode(",", $types).") ";
      }

      $query = "SELECT  n.id as id,
                        n.is_deleted as is_deleted,
                        c.uuid as uuid
                FROM glpi_networkports as n
                LEFT JOIN glpi_computers as c ON (c.id=n.items_id AND c.is_deleted='0')
                LEFT JOIN glpi_computertypes as ct ON (ct.id=c.computertypes_id)
                WHERE $condition
                   AND n.is_deleted='0'
                   AND c.entities_id>=0";

      $ports = array();
      foreach ($DB->request($query) as $dbport) {

         if (!$this->isVMAttachedToAnHypervisor($dbport['uuid'])) {
            continue;
         } else {
            $port = $this->getPortInfos($dbport['id'],
                                          'VirtualMachine',
                                          'virtualmachine_id');
            if ($port) {
               $ports[] = $port;
            }
         }
      }
      return $ports;
   }

   function isVMAttachedToAnHypervisor($uuid) {
      global $DB;

      $condition = $this->getTypeRestriction('Computer', 'c', 'Server');
      if (!$condition) {
         return false;
      }

      $query = "SELECT `vm`.`id`
                FROM `glpi_computervirtualmachines` as vm
                LEFT JOIN `glpi_computers`as c ON (c.id=vm.computers_id AND $condition)
                WHERE LOWER(`vm`.`uuid`) ".ComputerVirtualMachine::getUUIDRestrictRequest($uuid);
      $results = $DB->query($query);
      return ($DB->numrows($results) > 0);
   }
}
