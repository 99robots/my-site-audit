/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/29/15
 * Package: Javascript/Dashboard
 * File: dashboard.js
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

	// Save sort order

	$('.meta-box-sortables-left').sortable({
        update: function(event, ui) {
			msa_save_dashboard_panel_order();
        }
    });

    $('.meta-box-sortables-right').sortable({
        update: function(event, ui) {
			msa_save_dashboard_panel_order();
        }
    });

    /**
     * Save the dashboard panel order
     *
     * @access public
     * @return void
     */
    function msa_save_dashboard_panel_order() {

		var left_order = $('.meta-box-sortables-left').sortable('toArray');
	    var right_order = $('.meta-box-sortables-right').sortable('toArray');

	    if ( left_order.length == 0 ) {
			left_order = 'empty';
	    }

	    if ( right_order.length == 0 ) {
			right_order = 'empty';
	    }

	    $.post(ajaxurl, {
				'action': 'msa_save_dashboard_panel_order',
				'left_order': left_order,
				'right_order': right_order,
			}, function(response) {
			console.log(response);
		});

    }

});