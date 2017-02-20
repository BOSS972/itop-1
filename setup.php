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

if (!defined("PLUGIN_ITOP_CSV_DIR")) {
   define("PLUGIN_ITOP_CSV_DIR", GLPI_PLUGIN_DOC_DIR."/itop/");
}

function plugin_init_itop() {
   global $PLUGIN_HOOKS,$CFG_GLPI;
   $PLUGIN_HOOKS['csrf_compliant']['itop'] = true;

   $plugin = new Plugin();
   if ($plugin->isInstalled('itop') && $plugin->isActivated('itop')) {

      if (Session::getLoginUserID() && Session::haveRight("config", UPDATE)) {
         $PLUGIN_HOOKS['config_page']['itop'] = 'front/config.form.php';
      }

      Plugin::registerClass('PluginItopSoftware', array(
         'addtabon' => 'Software')
      );

      $PLUGIN_HOOKS['add_javascript']['itop'][] = 'js/itop.js.php';
   }
}

// Get the name and the version of the plugin - Needed
function plugin_version_itop() {
   return array ('name'           => __("iTop Connector for GLPI", "itop"),
                 'version'        => '1.0',
                 'author'         => "<a href='www.teclib.com'>TECLIB'</a>",
                 'homepage'       => 'https://github.com/teclib/itop',
                 'license'        => "MIT",                 
                 'minGlpiVersion' => '9.1.1');
}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_itop_check_prerequisites() {
   if (version_compare(GLPI_VERSION, '9.1.1', 'lt') || version_compare(GLPI_VERSION, '9.2', 'ge')) {
      echo "This plugin requires GLPI 9.1.1 or higher";
      return false;
   }
   return true;
}

// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_itop_check_config() {
   return true;
}
