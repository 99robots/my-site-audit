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

	var msa_audit_time = 0;
	var msa_audit_time_interval;

	// Get all the posts for the audit

	$(".msa-create-audit-form").submit(function(e){

		if ( $(this).find('.msa-creating-audit').length != 0 ) {
			return;
		}

		e.preventDefault();

		// Add a loading icon and a progress bar

		$('<span class="msa-creating-audit"><img src="' + msa_all_audits_data.site_url + '/wp-admin/images/spinner-2x.gif"/></span>').insertAfter('#submit');

		$(this).append('<div class="msa-info updated"><p>' + msa_all_audits_data.info + '</p></div>');
		$(this).append('<div class="msa-progress-bar-container"><div class="msa-progress-bar" data-current="1" data-max="0"></div></div>');
		$(this).append('<div class="msa-posts-completed">0/0</div>');

		// Audit a post

		$.post(ajaxurl, {
				'action': 'msa_get_post_ids_for_audit',
				'data': $(".msa-create-audit-form").serialize(),
			}, function(response) {

			response = $.parseJSON(response);
			var posts = response.post_ids;

			$('.msa-estimated-time span').html("N/A");
			$('.msa-posts-completed').html('0/' + posts.length);
			$('.msa-progress-bar').attr('data-max', posts.length);
			$(".msa-create-audit-form").append('<span style="display-none;" class="msa-audit-score" data-audit-id="' + response.audit_id + '" data-num-posts="' + posts.length + '" data-score="0"></span>');

			//msa_add_post_to_audit(response.audit_id, posts[0]);

			for ( var i = 0; i < posts.length; i++) {
				msa_add_post_to_audit(response.audit_id, posts[i]);
			}
		});
	});

	/**
	 * Add a post to the audit
	 *
	 * @access public
	 * @param mixed audit_id
	 * @param mixed post
	 * @return void
	 */
	function msa_add_post_to_audit(audit_id, post) {

		$.post(ajaxurl, {
				'action': 'msa_add_post_to_audit',
				'audit_id': audit_id,
				'post_id': post,
			}, function(response) {

				console.log('post completed');

				msa_update_progress_bar();

				var score = $('.msa-audit-score').attr('data-score');
				$('.msa-audit-score').attr('data-score', parseFloat(score) + parseFloat(response));
				msa_update_audit_score($('.msa-audit-score').attr('data-audit-id'), $('.msa-audit-score').attr('data-num-posts'), $('.msa-audit-score').attr('data-score'));
		});

	}

	/**
	 * Update the audit score
	 *
	 * @access public
	 * @param mixed audit_id
	 * @param mixed num_posts
	 * @param mixed score
	 * @return void
	 */
	function msa_update_audit_score(audit_id, num_posts, score) {

		// Update the audit score

		$.post(ajaxurl, {
				'action': 'msa_update_audit_score',
				'audit_id': audit_id,
				'score': score,
				'num_posts': num_posts,
			}, function(response) {
				//console.log('Score has been updated: ' + response);
		});

	}

	/**
	 * Update the progress bar on screen
	 *
	 * @access public
	 * @return void
	 */
	function msa_update_progress_bar() {

		$('.msa-progress-bar').attr('data-current', parseInt($('.msa-progress-bar').attr('data-current')) + 1);
		$('.msa-posts-completed').html(parseInt($('.msa-progress-bar').attr('data-current')) + '/' + parseInt($('.msa-progress-bar').attr('data-max')));

		var width = parseInt($('.msa-progress-bar').attr('data-current')) / parseInt($('.msa-progress-bar').attr('data-max'));

		// Process is complete

		if ( width >= 1 ) {
			width = 1;

			$('.msa-info').remove();
			$('.msa-posts-completed').remove();
			$('.msa-creating-audit').remove();
			$('.msa-progress-bar-container').remove();
			clearInterval(msa_audit_time_interval);

			// Add message saying that the process has been completed

			var audit_id = $('.msa-audit-score').attr('data-audit-id');
			$(".msa-create-audit-form").append('<div class="updated"><p>' + msa_all_audits_data.success_message + '<a href="' + msa_all_audits_data.admin_url + 'admin.php?page=msa-all-audits&audit=' + audit_id + '">here.</a></p></div>');
		}

		$('.msa-progress-bar').css('width', 100 * width + '%' );

	}

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

	// Datepicker

	$(".msa-datepicker").datepicker();

	// Hide and show the create new settings

	$('.msa-add-new-audit').click(function(){

		if ( $('.msa-create-audit-wrap').css('display') != 'none' ) {
			$('.msa-create-audit-wrap').slideUp();
		} else {
			$('.msa-create-audit-wrap').slideDown();
		}
	});

	// Hide and show the columns

	$('.hide-column-tog').change(function(){

		if ( $(this).prop('checked') ) {
			$('#' + $(this).val()).show();
		} else {
			$('#' + $(this).val()).hide();
		}
	});

});