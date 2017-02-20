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

function plugin_itop_install() {
   if (!is_dir(PLUGIN_ITOP_CSV_DIR)) {
      @ mkdir(PLUGIN_ITOP_CSV_DIR)
        or die(sprintf(__('%1$s %2$s'), __("Can't create folder", 'itop'),
                       PLUGIN_ITOP_CSV_DIR));
   }

   include (GLPI_ROOT."/plugins/itop/inc/export.class.php");
   include (GLPI_ROOT."/plugins/itop/inc/matching.class.php");
   include (GLPI_ROOT."/plugins/itop/inc/state.class.php");
   include (GLPI_ROOT."/plugins/itop/inc/software.class.php");
   $migration = new Migration("0.85");
   PluginItopExport::install($migration);
   PluginItopMatching::install($migration);
   PluginItopState::install($migration);
   PluginItopSoftware::install($migration);
   return true;
}

function plugin_itop_uninstall() {
   include (GLPI_ROOT."/plugins/itop/inc/export.class.php");
   include (GLPI_ROOT."/plugins/itop/inc/matching.class.php");
   include (GLPI_ROOT."/plugins/itop/inc/software.class.php");
   PluginItopExport::uninstall();
   PluginItopMatching::uninstall();
   PluginItopState::uninstall();
   PluginItopSoftware::uninstall();

   if (is_dir(PLUGIN_ITOP_CSV_DIR)) {
      Toolbox::deleteDir(PLUGIN_ITOP_CSV_DIR);
   }
   return true;
}
