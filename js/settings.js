/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/29/15
 * Package: Javascript/Settings
 * File: settings.js
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

jQuery(document).ready(function($){

	// Tabs

	$('.msa-vertical-tabs-item a').click(function(e){
		//e.preventDefault();
		$('.msa-vertical-tabs-content-item').hide();
		$($(this).attr('href')).show();

		$('.msa-vertical-tabs-item').removeClass('msa-vertical-tabs-current');
		$(this).parent().addClass('msa-vertical-tabs-current');
	});

	// Test Shared Count API

	$('.msa-shared-count-test').click(function(e){

		e.preventDefault();

		$(this).append('<i style="margin-left: 10px;" class="fa fa-spinner fa-spin"></i>');
		$('.msa-shared-count-test').parent().find('p').remove();

		$.post(ajaxurl, {
				'action': 'msa_shared_count_test',
				'api_key': $('.msa-shared-count-api-key').val(),
			}, function(response) {

			$('.msa-shared-count-test').find('.fa').remove();

			response = $.parseJSON(response);

			if ( response.status == 'check' ) {
				$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:green;" class="fa fa-check"></i>');
				$('.msa-shared-count-test').css('border-color', 'green');
			} else {
				$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:red;" class="fa fa-warning"></i>');
				$('.msa-shared-count-test').css('border-color', 'red');
			}

			$('<p class="description">' + response.message + '</p>').insertAfter('.msa-shared-count-test');

		});
	});

	$('.msa-shared-count-test').append('<i style="margin-left: 10px;" class="fa fa-spinner fa-spin"></i>');

	$.post(ajaxurl, {
			'action': 'msa_shared_count_test',
			'api_key': $('.msa-shared-count-api-key').val(),
		}, function(response) {

		$('.msa-shared-count-test').find('.fa').remove();

		response = $.parseJSON(response);

		if ( response.status == 'check' ) {
			$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:green;" class="fa fa-check"></i>');
			$('.msa-shared-count-test').css('border-color', 'green');
		} else {
			$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:red;" class="fa fa-warning"></i>');
			$('.msa-shared-count-test').css('border-color', 'red');
		}

		$('<p class="description">' + response.message + '</p>').insertAfter('.msa-shared-count-test');

		console.log(response);

	});

});