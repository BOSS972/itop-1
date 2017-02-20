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

include ('../../../inc/includes.php');
global $CFG_GLPI;

$root_ajax = $CFG_GLPI['root_doc']."/plugins/itop/ajax/ajax.php";
$question = __('Are you sure you want to delete this object ?', 'itop');
$question2 = __('Are you sure you want to delete this status ?', 'itop');

$JS = <<<JAVASCRIPT

function deleteStatus(id){

	if (confirm("{$question2}")) {

		$.ajax({ // fonction permettant de faire de l'ajax
				type: "POST", // methode de transmission des données au fichier php
				url: "{$root_ajax}", // url du fichier php
				data: "action=deleteStatus&" +
					  "id=" + id, // données à transmettre
			success: function (response) { // si l'appel a bien fonctionné
				window.location.reload();
			},
			error: function () {
				alert("Ajax error");
			}
		});

	}



}

function deleteMatche(id){

	if (confirm("{$question}")) {

		$.ajax({ // fonction permettant de faire de l'ajax
				type: "POST", // methode de transmission des données au fichier php
				url: "{$root_ajax}", // url du fichier php
				data: "action=deleteMatch&" +
					  "id=" + id, // données à transmettre
			success: function (response) { // si l'appel a bien fonctionné
				window.location.reload();
			},
			error: function () {
				alert("Ajax error");
			}
		});

	}



}

function deleteSoftwareCategory(id){

	if (confirm("{$question}")) {

		$.ajax({ // fonction permettant de faire de l'ajax
				type: "POST", // methode de transmission des données au fichier php
				url: "{$root_ajax}", // url du fichier php
				data: "action=deleteSoftwareCategory&" +
					  "id=" + id, // données à transmettre
			success: function (response) { // si l'appel a bien fonctionné
				window.location.reload();
			},
			error: function () {
				alert("Ajax error");
			}
		});

	}



}

function changeTypeGlpiItem(){

	var glpiType = $("#dropdown_itemtype").find(":selected").val();
	var drop = $("#typeItemType");

	if(glpiType != 0){

		drop.empty();

		$.ajax({ // fonction permettant de faire de l'ajax
			type: "POST", // methode de transmission des données au fichier php
			url: "{$root_ajax}", // url du fichier php
			data: "action=getComboType&" +
				  "itemtype=" + glpiType, // données à transmettre
		success: function (response) { // si l'appel a bien fonctionné
			drop.html(response);
		},
		error: function () {
			drop.html("Ajax error");
		}

		});


	}


}


JAVASCRIPT;

echo $JS;
