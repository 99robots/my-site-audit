/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/29/15
 * Package: Javascript/All Audits
 * File: all-audits.js
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

	// Show the modal

	$('.msa-audit-conditions-button').click(function(e){
		e.preventDefault();

		$('.msa-audit-conditions-modal').hide();
		$('.msa-audit-conditions-modal[data-id="' + $(this).data('id') + '"]').show();
	});

	// Move modal to end of body

	$('.msa-audit-conditions-modal').each(function(index, value){

		$($(this)).appendTo('body');

	});

	// Close Modal

	$('.msa-audit-conditions-modal').click(function(e){

		if ( $(e.target).is($(this)) ) {
			$(this).hide();
		}

	});

});