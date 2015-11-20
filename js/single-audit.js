/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/13/15
 * Package: Javascript/Single Audit
 * File: single-audit.js
 * Author: Kyle Benk
 *
 *
 * Copyright 2015
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * ================================================================= */

jQuery(document).ready(function($) {

	// Move show columns around to relate to the condition categories

	$('#adv-settings h5').remove();

	$.each(msa_single_audit_data.condition_categories, function(index, value){
		$('<div class="msa-condition-category-column msa-condition-category-column-' + index + '">' +
			'<h5>' + value.name + '</h5>' +
		+ '</div>').insertBefore('form .metabox-prefs');
	});

	// Attributes

	$('<h5>' + msa_single_audit_data.attribute_title + '</h5>').insertBefore('form .metabox-prefs');

	$.each(msa_single_audit_data.conditions, function(index, value){
		$('label[for="' + index + '-hide"]').appendTo('.msa-condition-category-column-' + value.category);
	});

	// Add Filters

	$('.msa-filter-button').click(function(e) {
		e.preventDefault();

		var parameters = '';

		$('.msa-filter').each(function(index, value) {

			if ( $(value).length != 0 && $(value).val() != '' ) {
				parameters += "&" + $(value).attr('name') + "=" + $(value).val();
			}
		});

	    window.location += parameters;
	});

	// Clear Filters

	$('.msa-clear-filters-button').click(function(e){
		e.preventDefault();
		window.location = msa_single_audit_data.audit_page + "&audit=" + msa_get_url_parameter('audit');
	});

	// Hide and show the columns

	$('.hide-column-tog').each(function(index, value){

		$(value).prop('checked', false);
		$('.column-' + $(value).val()).hide();
		$('.filter-' + $(value).val()).hide();

		var column = $(value);

		$.each(msa_single_audit_data.show_columns, function(index, value) {

			if ( column.val() == value ) {
				column.prop('checked', true);
				$('.column-' + column.val()).show();
				$('.filter-' + column.val()).show();
				return false;
			}
		});
	});

	$('.hide-column-tog').change(function(){

		if ( $(this).prop('checked') ) {
			$('.column-' + $(this).val()).show();
			$('.filter-' + $(this).val()).show();

			msa_show_column('add', $(this).val());

		} else {
			$('.column-' + $(this).val()).hide();
			$('.filter-' + $(this).val()).hide();

			msa_show_column('remove', $(this).val());
		}
	});

	/**
	 * Either add or remove a column from the all posts table
	 *
	 * @access public
	 * @param mixed action
	 * @param mixed column
	 * @return void
	 */
	function msa_show_column(action, column) {

		$.post(ajaxurl, {
				'action': 'msa_show_column',
				'action_needed': action,
				'column': column,
			}, function(response) {
			console.log(response);
		});
	}

	/**
	 * Get the URL parameter
	 * Credit: http://stackoverflow.com/questions/19491336/get-url-parameter-jquery
	 *
	 * @access public
	 * @param mixed sParam
	 * @return void
	 */
	function msa_get_url_parameter(sParam) {
	    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	        sURLVariables = sPageURL.split('&'),
	        sParameterName,
	        i;

	    for (i = 0; i < sURLVariables.length; i++) {
	        sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] === sParam) {
	            return sParameterName[1] === undefined ? true : sParameterName[1];
	        }
	    }
	}

});