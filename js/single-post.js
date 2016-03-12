/**
 * This file is responsible for all client side logic on the Single Post Page.
 *
 * @param  {document} document The global document.
 * @return {null}
 */
jQuery( document ).ready( function( $ ) {

	// Move modal to end of body.
	$( '.msa-condition-more-info' ).each( function( index, value ) {
		$( this ).find( '.msa-modal' ).appendTo( 'body' );
	});

	// Show more info modal.
	$( '.msa-condition-more-info' ).click( function( e ) {
		e.preventDefault();
		$( '.msa-modal[data-condition="' + $( this ).data( 'condition' ) + '"]' ).show();
	});

	// Hide modal.
	$( '.msa-modal' ).click( function( e ) {
		if ( $( e.target ).is( $( this ) ) ) {
			$( this ).hide();
		}
	});

	$( '.msa-modal-close' ).click( function() {
		$( this ).parent().parent().parent().hide();
	});
});
