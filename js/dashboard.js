/**
 * This file is responsible for all client site logic on the Dashboard Page.
 *
 * @param  {document} document The global document object.
 * @return {null}
 */
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
				'save_dashboard_panel_order_nonce': msa_dashboard_data.save_dashboard_panel_order_nonce,
				'left_order': left_order,
				'right_order': right_order,
			}, function(response) {
			console.log(response);
		});

	}

});
