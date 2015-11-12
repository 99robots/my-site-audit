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

		$('.msa-vertical-tabs-content-item').hide();
		$($(this).attr('href')).show();

		$('.msa-vertical-tabs-item').removeClass('msa-vertical-tabs-current');
		$(this).parent().addClass('msa-vertical-tabs-current');

		e.preventDefault();
	});

});