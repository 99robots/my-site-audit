/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/16/15
 * Package: Javascript/ Extensions
 * File: licensing.js
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

	// Define the variables

	var license_activate_button = '<span class="button button-default ' +
		'msa-activate-license">' +
		msa_licensing_data.activate_text + '</span>';

	var license_deactivate_button = '<span class="button button-default ' +
		'msa-deactivate-license">' +
		msa_licensing_data.deactivate_text + '</span>';

	var check = '<span class="fa fa-check form-control-feedback" aria-hidden="true"></span>';
	var error = '<span class="fa fa-times form-control-feedback" aria-hidden="true"></span>';
	var spinner = '<span class="msa-spinner"><img src="' + msa_licensing_data.site_url + '/wp-admin/images/spinner-2x.gif"/></span>';

	// Check license key

	$('.msa-license-key').each(function(index, value) {
		msa_check_license($(value).data('extension'), $(value).val(), $(value));
	});

	// Activate License

	$(document).on('click', '.msa-activate-license', function(e){

		var value = $(e.target).parent().find('input');
		msa_activate_license($(value).data('extension'), $(value).val(), $(value));

	});

	// Deactivate License

	$(document).on('click', '.msa-deactivate-license', function(e){

		var value = $(e.target).parent().find('input');
		msa_deactivate_license($(value).data('extension'), $(value).val(), $(value));

	});

	/**
	 * Check the license key for an extension
	 *
	 * @access public
	 * @param mixed license_key
	 * @return void
	 */
	function msa_check_license(extension, license_key, element) {

		// Check if we even have an extension or license key

		if ( extension != '' && license_key != '') {

			element.parent().append(spinner);

			var data = {
				'action': 'msa_license_action',
				'license_action': 'check_license',
				'extension': extension,
				'license_key': license_key,
			};

			$.post(ajaxurl, data, function(response) {

				element.parent().find('.msa-spinner').remove();

				if ( response == 'valid' ) {

					element.parent().append(license_deactivate_button);
					element.parent().append('<p class="description msa-activation-message msa-license-' + response + '">' + msa_licensing_data.activation_valid + '</p>');

				}  else if ( response == 'expired' ) {

					element.parent().append(license_activate_button);
					element.parent().append('<p class="description msa-activation-message msa-license-' + response + '">' + msa_licensing_data.expired + '</p>');

				} else if ( response == 'inactive' ) {

					element.parent().append(license_activate_button);
					element.parent().append('<p class="description msa-activation-message msa-license-' + response + '">' + msa_licensing_data.inactive + '</p>');

				} else {

					element.parent().append(license_activate_button);
					element.parent().append('<p class="description msa-activation-message msa-license-' + response + '">' + msa_licensing_data.activation_error + '</p>');

				}

				console.log(response);
			});
		} else {
			element.addClass('msa-license-key-valid');
			element.parent().append(license_activate_button);
			element.parent().append('<p class="description msa-activation-message">' + msa_licensing_data.no_license_key + '</p>');
		}

	}

	/**
	 * Activate License
	 *
	 * @access public
	 * @param mixed extension
	 * @param mixed license_key
	 * @return void
	 */
	function msa_activate_license(extension, license_key, element) {

		element.parent().append(spinner);

		element.parent().find('.msa-activation-message').remove();
		element.parent().find('.msa-activate-license').remove();
		element.parent().find('.msa-deactivate-license').remove();

		var data = {
			'action': 'msa_license_action',
			'license_action': 'activate_license',
			'extension': extension,
			'license_key': license_key,
		};

		$.post(ajaxurl, data, function(response) {

			element.parent().find('.msa-spinner').remove();

			if ( response == 'valid' ) {

				element.parent().append(license_deactivate_button);
				element.parent().append('<p class="description msa-activation-message msa-license-' + response + '">' + msa_licensing_data.activation_valid + '</p>');

			} else {

				element.parent().append(license_activate_button);
				element.parent().append('<p class="description msa-activation-message msa-license-' + response + '">' + msa_licensing_data.activation_error + '</p>');

			}

			console.log(response);
		});
	}

	/**
	 * Deactivate a license key
	 *
	 * @access public
	 * @param mixed extension
	 * @param mixed license_key
	 * @return void
	 */
	function msa_deactivate_license(extension, license_key, element) {

		element.parent().append(spinner);

		element.parent().find('.msa-activation-message').remove();
		element.parent().find('.msa-activate-license').remove();
		element.parent().find('.msa-deactivate-license').remove();

		var data = {
			'action': 'msa_license_action',
			'license_action': 'deactivate_license',
			'extension': extension,
			'license_key': license_key,
		};

		$.post(ajaxurl, data, function(response) {

			element.parent().find('.msa-spinner').remove();

			if ( response == 'deactivated' ) {

				element.val('');
				element.parent().append(license_activate_button);
				element.parent().append('<p class="description msa-activation-message">' + msa_licensing_data.deactivation_valid + '</p>');

			} else if ( response == 'failed' ) {

				element.parent().append('<p class="description msa-activation-message">' + msa_licensing_data.deactivation_error + '</p>');

			} else {

				element.parent().append('<p class="description msa-activation-message">' + msa_licensing_data.deactivation_error + '</p>');
			}

			console.log(response);
		});
	}

});