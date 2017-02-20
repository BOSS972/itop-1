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

class PluginItopExportNetworkInterfaceCommon extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itop_class = '';

   function getHeaders() {
      return array();
   }

   function getPortInfos($networkports_id, $itemtype, $field) {
      global $DB;

      $port  = new NetworkPort();
      $nname = new NetworkName();
      $ipntw = new IPAddress_IPNetwork();
      $ipn   = new IPNetwork();

      $port->getFromDB($networkports_id);
      $inst = $port->getInstantiation();
      if ($port->fields['name'] == ''
        || $port->fields['itemtype'] != $itemtype) {
         return false;
      }

      $tmp['name']          = $port->fields['name'];
      $tmp['primary_key']   = $port->fields['name'];
      $tmp['macaddress']    = $port->fields['mac'];
      $tmp['comment']       = $port->fields['comment'];
      $tmp['glpi_uniqueid'] = $this->getGlpiServerID().'-NetworkPort-'.$port->getID();
      $tmp[$field]
         = $this->getGlpiServerID().'-'.$port->fields['itemtype'].'-'.$port->fields['items_id'];
      if ($inst
         && isset($inst->fields['speed'])
            && $inst->fields['speed'] != '') {
         $tmp['speed'] = $inst->fields['speed'];
      }
      foreach ($DB->request('glpi_networknames',
                            array('itemtype' => 'NetworkPort',
                                  'items_id' => $networkports_id)) as $dataname) {
         foreach ($DB->request('glpi_ipaddresses',
                               array('itemtype' => 'NetworkName',
                                     'items_id' => $networkports_id,
                                     'version'  => 4)) as $data) {
            $tmp['ipaddress'] = $data['name'];
            $opposite         = $ipntw->getOppositeByTypeAndID('IPAddress', $data['id']);
            if ($opposite) {
               $tmp['ipgateway'] = $opposite->fields['gateway'];
               $tmp['ipmask']    = $opposite->fields['netmask'];
            }
            break;
         }
      }
      return $tmp;
   }

   function getFieldsToFilter() {
      return array('name');
   }


   function getSQLQuery() {
   }
}
