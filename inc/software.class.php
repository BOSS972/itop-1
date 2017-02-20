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

class PluginItopSoftware extends CommonDBTM {

   public $dohistory = true;

   /**
    * Name of the type
    *
    * @param $nb  integer  number of item in the type (default 0)
   **/
   static function getTypeName($nb=0) {
      return _n('iTop', 'iTop', $nb, 'itop');
   }

   static function canCreate() {
      return true;
   }

   static function canView() {
      return true;
   }

   public function defineTabs($options=array()) {
      return array(1 => __("iTop", 'itop'));
   }

   public function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      if (get_class($item) ==  'Software') {
         if ($item->getField('id') && !$withtemplate) {

            if ($_SESSION['glpishow_count_on_tabs']) {
               return self::createTabEntry(__("iTop", "itop"), self::countForItem($item));
            }
            return __("iTop", "itop");
         }
      }
      return '';
   }

   public static function countForItem(CommonDBTM $item) {
      return countElementsInTable('glpi_plugin_itop_softwares',
                                  "`softwares_id` = '".$item->getID()."'");
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      $soft = new self();
      if ($item->getType()=='Software') {
         $soft->showForm($item);
      }
      return true;
   }
   static function install(Migration $migration) {
      global $DB;
      $table = getTableForItemType(__CLASS__);
      if (!TableExists($table)) {
         $query = "CREATE TABLE IF NOT EXISTS `$table` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `softwares_id` int(11) NOT NULL DEFAULT '0',
              `itop_class` VARCHAR (255) collate utf8_unicode_ci NOT NULL DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `softwares_id` (`softwares_id`),
              KEY `itop_class` (`itop_class`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
         $DB->query($query) or die("Error adding table $table");
      }
   }

   static function uninstall() {
      global $DB;

      $table = getTableForItemType(__CLASS__);
      $DB->query("DROP TABLE IF EXISTS `$table`");
   }

   static function getSoftwares($itop_class = false) {
      global $DB;

      if ($itop_class) {
         $condition = " WHERE `itop_class`='$itop_class'";
      } else {
         $condition = "";
      }
      return getAllDatasFromTable($itop_class, $condition);
   }

   function getSearchOptions() {

      $tab['common']           = __('iTop', 'itop');

      $tab[1]['table']         = 'glpi_plugin_itop_softwares';
      $tab[1]['field']         = 'itop_class';
      $tab[1]['name']          = __('iTop class', 'itop');
      $tab[1]['massiveaction'] = false;
      $tab[1]['joinparams']    = array('jointype' => 'child');
      return $tab;
   }

   function showForm(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $LANG;

      $found = $this->find('softwares_id = ' . $item->getID());

      if (!empty($found)) {
         $row = array_shift($found);
         $id    = $row["id"];
         $this->getFromDB($row['id']);
      } else {
         $id    = 0;
         $this->getEmpty();
      }

      $software_classes = PluginItopConfig::getItopSoftwareClasses();
      $this->showFormHeader();

      echo '<tr class="tab_bg_2">';
      echo '<td width="20%">';
      echo __('itop class', 'itop');
      echo '</td>';
      echo '<td>';
      Dropdown::showFromArray('itop_class', $software_classes);
      echo '</td>';
      echo '</tr>';

      $this->showFormButtons();

      return true;
   }
}
