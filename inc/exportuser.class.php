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

class PluginItopExportPerson extends PluginItopExportCommon
  implements PluginItopExportInterface {

   public $filename   = 'persons';
   public $itop_class = 'Person';

   function getHeaders() {
      return array('org_id' , 'primary_key', 'name' ,
                   'first_name' , 'email' , 'phone' , 'employee_number' , 'mobile_phone' ,
                   'function' , 'location_id', 'glpi_uniqueid');
   }

   function getFieldsToFilter() {
      return array('name', 'first_name', 'org_id');
   }

   function getSQLQuery() {
      return "SELECT DISTINCT ".$this->getItemUniqueID('u', 'User').",
             ".$this->getEntityUniqueID('u').",
             ".$this->getLocationUniqueID('u').",
                       u.name as primary_key, u.realname as name, u.firstname as first_name,
                       um.email as email, u.phone as phone, u.registration_number as employee_number,
                       u.mobile as mobile_phone, uc.name as function
                FROM glpi_users as u
                LEFT JOIN glpi_useremails as um ON (um.users_id=u.id AND um.is_default='1')
                LEFT JOIN glpi_usercategories as uc ON (uc.id=u.usercategories_id)
                LEFT JOIN glpi_profiles_users as pu ON (pu.users_id=u.id)
                WHERE u.name IS NOT NULL AND u.firstname IS NOT NULL AND u.realname IS NOT NULL ";
   }
}
