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

class PluginItopConfig extends CommonDBTM {

   static function canCreate() {
      return true;
   }

   static function canView() {
      return true;
   }

    /**
     * Function to return an array of itop_class in config file
     * @return array array of itop_class
     */
   static function getItopClass() {

      $configFile = PluginItopToolbox::readConfiguration();

      $itopItem = array();
      foreach (explode(",", $configFile['links']['mandatory_links']) as $value) {
         $itopItem[$value] = $value;
      }
      return $itopItem;

   }


    /**
     * Function to return an array of itop_class in config file
     * @return array array of itop_class
     */
   static function getItopStatus() {

      $configFile = PluginItopToolbox::readConfiguration();

      $itopItem = array();
      foreach (explode(",", $configFile['links']['status_link']) as $value) {
         $itopItem[$value] = $value;
      }
      return $itopItem;

   }


    /**
     * Function to return an array of itop_class in config file
     * @return array array of itop_class
     */
   static function getItopSoftwareClasses() {

      $configFile = PluginItopToolbox::readConfiguration();

      $itopItem = array();
      foreach (explode(",", $configFile['links']['software_classes']) as $value) {
         $itopItem[$value] = $value;
      }
      return $itopItem;

   }

    /**
     * Function to return an array of soft categories in config file
     * @return array array of itop_class
     */
   static function getSoftwareCategories() {
      $configFile = PluginItopToolbox::readConfiguration();

      $softCat = array();
      foreach (explode(",", $configFile['links']['software_categories']) as $value) {
         $softCat[$value] = $value;
      }
      return $softCat;

   }


   function showConfigForm() {

      self::showFormToAddType();
      self::showAddedType();
      echo "<br/><br/>";
      self::getFormToMatchStatus();
      self::showMatchedStatus();
      echo "<br/><br/>";
      self::getFormToMatchSoftwareCategories();
      self::showAddedSoftwareCategory();

   }

   static function showMatchedStatus() {
      global $CFG_GLPI;

      $statusGlpi = Ticket::getAllStatusArray();

      $configFile = PluginItopToolbox::readConfiguration();
      if (!$configFile) {
         return __("Bad configuration file", "itop");
      }

      $form = "";

       $form .= "<form method='post' action='' method='post' id='config' >";
       $form .= "<table class='tab_cadre_fixe' >";

       $form .= "<tr><th colspan='5'>" . __("Managed Status", "itop") . "</th></tr>";

       $form .= "<tr class='headerRow'>";
       $form .= "<th>" . __("iTop status", "itop") . "</th>";
       $form .= "<th>" . __("GLPI status", "itop") . "</th>";
       $form .= "<th>" . __("Delete", "itop") . "</th>";

       $form .= "</tr>";

       $status = new PluginItopState();
       $states = $status->find();

      if (count($states) == 0) {

            $form .= "<tr class='tab_bg_1'>";
            $form .= "<td class='center' colspan='4'>".__('No managed status', 'itop')."</td>";
            $form .= "</tr>";

      } else {

         foreach ($states as $state) {

             $st = new State();
             $st->getFromDB($state['states_id']);

             $form .= "<tr class='tab_bg_1'>";
             $form .= "<td class='center'>" .$state['status_itop']. "</td>";
             $form .= "<td class='center'>" .$st->fields['name']. "</td>";

             $form .= "<td class='center'><img src='".$CFG_GLPI['root_doc']."/plugins/itop/pics/bin16.png'     onclick='deleteStatus(".$state['id'].");'
		                  style='cursor: pointer;' title='" . __("Delete", "itop") . "'/></td>";

             $form .= "</tr>";

         }

      }

       $form .= "</table>";
       $form .= Html::closeForm(false);

       echo $form;
   }



   static function getFormToMatchStatus() {

      $configFile = PluginItopToolbox::readConfiguration();
      if (!$configFile) {
         return __("Bad configuration file", "itop");
      }

      $config = new self();

      $target = $config->getFormURL();
      if (isset($options['target'])) {
         $target = $options['target'];
      }

       $form = "";

       $form .= "<form method='post' action='" . $target . "' method='post' id='config' >";
       $form .= "<table class='tab_cadre_fixe' >";

       $form .= "<tr><th colspan='2'>" . __("Matching iTop and GLPI status", "itop") . "</th></tr>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td>". __("Itop status", "itop") ."</td>";
       $form .= "<td>".Dropdown::showFromArray("status_itop", self::getItopStatus(),
                                               array('display' => false,'rand' => ''))."</td>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td>". __("Glpi status", "itop") ."</td>";
       $form .= "<td>".State::dropdown(array('name' => 'states_id' , 'condition' => 'is_visible_computer','display' => false,'rand' => '','used' => self::getItopStatusAlreadyUsed()))."</td>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td ><center><input type='submit' name='addStatus' value=\""._sx('button', 'Add')."\" class='submit'></center></td>";
       $form .= "</tr>";

       $form .= "</table>";
       $form .= Html::closeForm(false);

      echo $form;
   }

   static function getFormToMatchSoftwareCategories() {

      $configFile = PluginItopToolbox::readConfiguration();
      if (!$configFile) {
         return __("Bad configuration file", "itop");
      }

      $config = new self();

      $target = $config->getFormURL();
      if (isset($options['target'])) {
         $target = $options['target'];
      }

       $form = "";

       $form .= "<form method='post' action='" . $target . "' method='post' id='config' >";
       $form .= "<table class='tab_cadre_fixe' >";

       $form .= "<tr><th colspan='2'>" . __("Matching iTop and GLPI SoftwareCategory", "itop") . "</th></tr>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td>". __("GLPI Software Category", "itop") ."</td>";
       $form .= "<td>".SoftwareCategory::dropdown(array('name' => 'softwares_id', 'display' => false))."</td>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td>". __("iTop Software Type", "itop") ."</td>";
       $form .= "<td>".Dropdown::showFromArray("itop_class", self::getItopSoftwareClasses(),
                                               array('display' => false))."</td>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td ><center><input type='submit' name='addSoftwareMatch' value=\""._sx('button', 'Add')."\" class='submit'></center></td>";
       $form .= "</tr>";

       $form .= "</table>";
       $form .= Html::closeForm(false);

      echo $form;
   }

    /**
     * Function to show table with type in BDD
     * @return String return HTML table
     */
   static function showAddedSoftwareCategory() {
      global $CFG_GLPI;

      $configFile = PluginItopToolbox::readConfiguration();
      if (!$configFile) {
         return __("Bad configuration file", "itop");
      }

      $form = "";

       $form .= "<form method='post' action='' method='post' id='config' >";
       $form .= "<table class='tab_cadre_fixe' >";

       $form .= "<tr><th colspan='5'>" . __("Managed Item", "itop") . "</th></tr>";

       $form .= "<tr class='headerRow'>";
       $form .= "<th>" . __("Itop class", "itop") . "</th>";
       $form .= "<th>" . __("GLPI Software Category", "itop") . "</th>";
       $form .= "<th>" . __("Delete", "itop") . "</th>";

       $form .= "</tr>";

       $matching = new PluginItopSoftware();
       $matches = $matching->find('', 'itop_class');

      if (count($matches) == 0) {

            $form .= "<tr class='tab_bg_1'>";
            $form .= "<td class='center' colspan='4'>".__('No managed items', 'itop')."</td>";
            $form .= "</tr>";

      } else {

         foreach ($matches as $matche) {
             $st = new SoftwareCategory();
             $st->getFromDB($matche['softwares_id']);

             $form .= "<tr class='tab_bg_1'>";
             $form .= "<td class='center'>" .$matche['itop_class']. "</td>";
             $form .= "<td class='center'>" .$st->fields['name']. "</td>";
             $form .= "<td class='center'><img src='".$CFG_GLPI['root_doc']."/plugins/itop/pics/bin16.png'
		                  onclick='deleteSoftwareCategory(".$matche['id'].");'
		                  style='cursor: pointer;' title='" . __("Delete", "itop") . "'/></td>";

             $form .= "</tr>";

         }

      }

       $form .= "</table>";
       $form .= Html::closeForm(false);

       echo $form;
   }

    /**
     * Function to show table with type in BDD
     * @return String return HTML table
     */
   static function showAddedType() {
      global $CFG_GLPI;

      $configFile = PluginItopToolbox::readConfiguration();
      if (!$configFile) {
         return __("Bad configuration file", "itop");
      }

      $form = "";

       $form .= "<form method='post' action='' method='post' id='config' >";
       $form .= "<table class='tab_cadre_fixe' >";

       $form .= "<tr><th colspan='5'>" . __("Managed Item", "itop") . "</th></tr>";

       $form .= "<tr class='headerRow'>";
       $form .= "<th>" . __("Itop class", "itop") . "</th>";
       $form .= "<th>" . __("Glpi item", "itop") . "</th>";
       $form .= "<th>" . __("Type", "itop") . "</th>";
       $form .= "<th>" . __("Delete", "itop") . "</th>";

       $form .= "</tr>";

       $matching = new PluginItopMatching();
       $matches = $matching->find('', 'itop_class');

      if (count($matches) == 0) {

            $form .= "<tr class='tab_bg_1'>";
            $form .= "<td class='center' colspan='4'>".__('No managed items', 'itop')."</td>";
            $form .= "</tr>";

      } else {

         foreach ($matches as $matche) {

             $form .= "<tr class='tab_bg_1'>";
             $form .= "<td class='center'>" .$matche['itop_class']. "</td>";
             $form .= "<td class='center'>" .$matche['itemtype']::getTypeName(). "</td>";
             $form .= "<td class='center'>" .self::getNameOfType($matche['itemtype'], $matche['type']). "</td>";

             $form .= "<td class='center'><img src='".$CFG_GLPI['root_doc']."/plugins/itop/pics/bin16.png'
		                  onclick='deleteMatche(".$matche['id'].");'
		                  style='cursor: pointer;' title='" . __("Delete", "itop") . "'/></td>";

             $form .= "</tr>";

         }

      }

       $form .= "</table>";
       $form .= Html::closeForm(false);

       echo $form;
   }




    /**
     * Function to show form to add type in BDD
     * @return String return HTML form
     */
   static function showFormToAddType() {

      $configFile = PluginItopToolbox::readConfiguration();
      if (!$configFile) {
         return __("Bad configuration file", "itop");
      }

      $config = new self();

      $target = $config->getFormURL();
      if (isset($options['target'])) {
         $target = $options['target'];
      }

       $params = array('display' => false);

       $form = "";

       $form .= "<form method='post' action='" . $target . "' method='post' id='config' >";
       $form .= "<table class='tab_cadre_fixe' >";

       $form .= "<tr><th colspan='2'>" . __("Add Type To Synchronize", "itop") . "</th></tr>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td>". __("Itop class", "itop") ."</td>";
       $form .= "<td>".Dropdown::showFromArray("itop_class", self::getItopClass(),
                                               array('display' => false,'rand' => ''))."</td>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td>". __("Glpi item", "itop") ."</td>";
       $form .= "<td>".Dropdown::showFromArray("itemtype", self::getAllGlpiItemType(true),
                                               array('display' => false ,
                                               'on_change' => 'changeTypeGlpiItem()',
                                               'rand' => ''))."</td>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td>". __("Type glpi item", "itop") ."</td>";
       $form .= "<td id='typeItemType' ></td>";

       $form .= "<tr class='tab_bg_1'>";
       $form .= "<td ><center><input type='submit' name='addItem' value=\""._sx('button', 'Add')."\" class='submit'></center></td>";
       $form .= "</tr>";

       $form .= "</table>";
       $form .= Html::closeForm(false);

      echo $form;
   }

    /**
     * Function to get all glpi Item managed by plugin and where need type
     * @param  boolean $emptyLabel display or not an empty choice
     * @return array               array of glpi item
     */
   static function getAllGlpiItemType($emptyLabel = false) {
      $types = array('null' => Dropdown::EMPTY_VALUE);
      foreach (array('Computer', 'NetworkEquipment',
                      'Phone', 'Printer') as $itemtype) {
         $types[$itemtype] = $itemtype::getTypeName();
      }

      return $types;
   }


    /**
     * Function to get Name of type switch itemType
     * @param  string $itemtype itemtype
     * @param  string $type_id  id of type
     * @return string           Name of type
     */
   static function getNameOfType($itemtype,$type_id) {

      $classType = $itemtype."Type";

      if (class_exists($classType)) {
         $type = new $classType();
          $type->getFromDB($type_id);
          return $type->fields['name'];
      } else {
         return Dropdown::EMPTY_VALUE;
      }
   }


    /**
     * Function to auto generate dropdown switch itemType
     * @param  string  $itemtype          itemType
     * @param  boolean $displayValueUsed  Display or not values already used
     * @return string                     return HTML dropdown or error message
     */
   static function getDropdownByItemType($itemtype , $displayValueUsed = true) {
      $classType = $itemtype."Type";
      if (class_exists($classType)) {
         $alreadyUsed = array();
         /*if(!$displayValueUsed){
         $matching = new PluginItopMatching();
         foreach ($matching->find() as $m) {
         if($m['itemtype'] == $itemtype) $alreadyUsed[] = $m['type'];
         }
         }*/

          return $classType::Dropdown(array('name'    => 'type',
                                  'display' => false,
                                  'used'    => $alreadyUsed));
      } else {
         return __("No type for this item");
      }
   }


        /**
     * Function to auto generate dropdown switch itemType
     * @param  string  $itemtype          itemType
     * @param  boolean $displayValueUsed  Display or not values already used
     * @return string                     return HTML dropdown or error message
     */
   static function getGlpiStatusAlreadyUsed() {
      $alreadyUsed = array();
      $status      = new PluginItopState();
       $states      = $status->find();

      foreach ($states as $state) {
            $alreadyUsed[$state['states_id']] = $state['states_id'];
      }

       return $alreadyUsed;

   }

            /**
     * Function to auto generate dropdown switch itemType
     * @param  string  $itemtype          itemType
     * @param  boolean $displayValueUsed  Display or not values already used
     * @return string                     return HTML dropdown or error message
     */
   static function getItopStatusAlreadyUsed() {

      $alreadyUsed = array();
      $status      = new PluginItopState();
       $states      = $status->find();

      foreach ($states as $state) {
            $alreadyUsed[$state['status_itop']] = $state['status_itop'];
      }
       return $alreadyUsed;
   }
}
