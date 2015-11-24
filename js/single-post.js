/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 11/18/15
 * Package: Javascript/ Single Post
 * File: single-post.js
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

	// Move modal to end of body

	$('.msa-condition-more-info').each(function(index,value){
		$(this).find('.msa-modal').appendTo('body');
	});

	// Show more info modal

	$('.msa-condition-more-info').click(function(e){

		e.preventDefault();

		$('.msa-modal[data-condition="' + $(this).data('condition') + '"]').show();

	});

	// Hide modal

	$('.msa-modal').click(function(e){

		if ( $(e.target).is($(this)) ) {
			$(this).hide();
		}

	});

	$('.msa-modal-close').click(function(){
		$(this).parent().parent().parent().hide();
	});

});