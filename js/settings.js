/**
 * This file is responsible for all client side logic on the Settings Page
 *
 * @param  {document} document The global document object.
 * @return {null}
 */
jQuery( document ).ready( function( $ ) {

	// Tabs.
	$( '.msa-vertical-tabs-item a' ).click( function( e ) {
		$( '.msa-vertical-tabs-content-item' ).hide();
		$( $( this ).attr( 'href' ) ).show();

		$( '.msa-vertical-tabs-item' ).removeClass( 'msa-vertical-tabs-current' );
		$( this ).parent().addClass( 'msa-vertical-tabs-current' );

		e.preventDefault();
	});
});
