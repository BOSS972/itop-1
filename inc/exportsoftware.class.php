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

class PluginItopExportSoftware extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $itop_class = 'Software';

   function getHeaders() {
      return array('primary_key' , 'name' , 'vendor' ,
                   'version', 'glpi_uniqueid', 'type');
   }

   function getFieldsToFilter() {
      return array('name', 'vendor');
   }

   function getSQLQuery() {
      $query = "SELECT ".$this->getItemUniqueID('s', 'Software').",
                       s.name as primary_key, s.name as name,
                       m.name as vendor, sv.name as version, i.itop_class as type
                FROM glpi_softwares as s
                LEFT JOIN glpi_manufacturers as m ON (m.id=s.manufacturers_id)
                LEFT JOIN glpi_softwareversions as sv ON (sv.softwares_id=s.id)
                LEFT JOIN glpi_plugin_itop_softwares as i ON (i.softwares_id=s.softwarecategories_id)
                WHERE s.is_deleted='0'
                  AND s.entities_id>=0
                  AND s.name IS NOT NULL
                  AND s.manufacturers_id > 0
                  AND sv.name IS NOT NULL
                  AND s.softwarecategories_id IN (SELECT softwares_id FROM glpi_plugin_itop_softwares)";
      return $query;
   }
}
