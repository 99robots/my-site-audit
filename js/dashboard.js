/**
 * This file is responsible for all client site logic on the Dashboard Page.
 *
 * @param  {document} document The global document object.
 * @return {null}
 */
jQuery(function( $ ) {

	// Save sort order.
	$( '.meta-box-sortables-left' ).sortable( {
		/**
		 * Update the dashboard panel order
		 * @param  {element} event The event handler.
		 * @param  {element} ui    The UI element.
		 * @return {null}
		 */
		update: function( event, ui ) {
			msaSaveDashboardPanelOrder();
		}
	} );

	$( '.meta-box-sortables-right' ).sortable( {
		/**
		 * Update the dashboard panel order
		 * @param  {element} event The event handler.
		 * @param  {element} ui    The UI element.
		 * @return {null}
		 */
		update: function( event, ui ) {
			msaSaveDashboardPanelOrder();
		}
	} );

	/**
	 * Save the dashboard panel order
	 *
	 * @return void
	 */
	function msaSaveDashboardPanelOrder() {

		var leftOrder = $( '.meta-box-sortables-left' ).sortable( 'toArray' );
		var rightOrder = $( '.meta-box-sortables-right' ).sortable( 'toArray' );

		if ( 0 === leftOrder.length ) {
			leftOrder = 'empty';
		}

		if ( 0 === rightOrder.length ) {
			rightOrder = 'empty';
		}

		$.post( ajaxurl, {
				'action': 'msa_save_dashboard_panel_order',
				'save_dashboard_panel_order_nonce': msaDashboardData.save_dashboard_panel_order_nonce,
				'left_order': leftOrder,
				'right_order': rightOrder
			}, function( response ) {
			console.log( response );
		});
	}
});
