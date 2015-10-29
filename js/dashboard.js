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

	// Get all share count data for posts

	$('.' + nnr_ca_data.prefix + 'share-count[data-post]').each(function(index, value){

		$.post(ajaxurl, {
				'action': 'nnr_ca_share_count_post',
				'post' : $(value).data('post'),
			}, function(response) {

			response = $.parseJSON(response);

			$('.' + nnr_ca_data.prefix + 'share-count[data-post="' + response.post + '"]').html(response.count);

			$($.bootstrapSortable);

		});
	});

});