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

include('../../../inc/includes.php');

if (isset($_POST['action'])) {

    global $CFG_GLPI;

   switch ($_POST['action']) {


      case 'getComboType':
         echo PluginItopConfig::getDropdownByItemType($_POST['itemtype'], false);
        break;

      case 'deleteMatch':
         $matching = new PluginItopMatching();
         $matching->getFromDB($_POST['id']);
         echo $matching->delete($_POST);
        break;

      case 'deleteStatus':
         $status = new PluginItopState();
         $status->getFromDB($_POST['id']);
         echo $status->delete($_POST);
        break;

      case 'deleteSoftwareCategory':
         $softwarecategory = new PluginItopSoftware();
         $softwarecategory->getFromDB($_POST['id']);
         echo $softwarecategory->delete($_POST);
        break;

      default:
         echo 0;

   }

} else {
   echo 0;
}
