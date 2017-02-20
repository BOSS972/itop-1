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

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT . "/inc/includes.php");

Html::header("Itop Configuration", $_SERVER["PHP_SELF"],
             "plugins", "Itop Configuration", "configuration");

if (isset($_POST['addItem'])) {
   if ($_POST['itemtype'] == 'null') {
      Session::addMessageAfterRedirect(__('Thank you to fill a Glpi item', 'itop'), false, ERROR, false);
      Html::back();
   }

   if (($_POST['itemtype'] == 'Computer' || $_POST['itemtype'] == 'PC' ||$_POST['itemtype'] == 'Virtual Machine')
        && $_POST['type'] == '0') {
      Session::addMessageAfterRedirect(__('Thank you to fill a type of Glpi item', 'itop')." -> ".__($_POST['itemtype']), false, ERROR, false);
      Html::back();
   }

    //$input = array('itemtype' => $_POST['itemtype'] ,'type' => $_POST['type'], 'itop_class' => $_POST['itop_class'] );
    $matching = new PluginItopMatching();
    $matching->add($_POST);

} else if (isset($_POST['addSoftwareMatch'])) {
   if ($_POST['softwares_id'] == 'null') {
      Session::addMessageAfterRedirect(__('Thank you to fill a GLPI SoftwareCategory', 'itop'), false, ERROR, false);
      Html::back();
   }

    //$input = array('itemtype' => $_POST['itemtype'] ,'type' => $_POST['type'], 'itop_class' => $_POST['itop_class'] );
    $matching = new PluginItopSoftware();
    $matching->add($_POST);
} else if (isset($_POST['addStatus'])) {

   if (!isset($_POST['status_itop'])) {
      Session::addMessageAfterRedirect(__('Thank you to fill a iTop status', 'itop'), false, ERROR, false);
      Html::back();
   }

   if (isset($_POST['states_id']) && $_POST['states_id'] == 0) {
      Session::addMessageAfterRedirect(__('Thank you to fill a Glpi status', 'itop'), false, ERROR, false);
      Html::back();
   }

    $states = new PluginItopState();
    $states->add($_POST);
    Html::back();
}


$pluginItopConfig = new PluginItopConfig();
$pluginItopConfig->showConfigForm();

Html::footer();
