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

class PluginItopState extends CommonDBTM {

   public $dohistory = true;

   /**
    * Name of the type
    *
    * @param $nb  integer  number of item in the type (default 0)
   **/
   static function getTypeName($nb=0) {
      global $LANG;
      return _n('iTop', 'iTop', $nb, 'itop');
   }

   static function canCreate() {
      return true;
   }

   static function canView() {
      return true;
   }

   static function install(Migration $migration) {
      global $DB;
      $table = getTableForItemType(__CLASS__);
      if (!TableExists($table)) {
         $query = "CREATE TABLE IF NOT EXISTS `$table` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `states_id` int(11) NOT NULL DEFAULT '0',
              `status_itop` VARCHAR (255) collate utf8_unicode_ci NOT NULL DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `states_id` (`states_id`),
              KEY `status_itop` (`status_itop`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
         $DB->query($query) or die("Error adding table $table");
      }
   }

   static function uninstall() {
      global $DB;

      $table = getTableForItemType(__CLASS__);
      $DB->query("DROP TABLE IF EXISTS `$table`");
   }

   static function getItopStatusFor($states_id) {
      global $DB;
      $query   = "SELECT `status_itop` FROM `glpi_plugin_itop_states` WHERE `states_id`='$states_id'";
      $results = $DB->query($query);
      if (!$DB->numrows($results)) {
         return false;
      } else {
         return $DB->result($results, 0, 'status_itop');
      }
   }
}
