/**
 * This file is responsible for all client side logic on the Single Post Page.
 *
 * @param  {document} document The global document.
 * @return {null}
 */
jQuery(function( $ ) {

	// Move modal to end of body.
	$( '.msa-condition-more-info' ).each( function( index, value ) {
		$( this ).find( '.msa-modal' ).appendTo( 'body' );
	});

	// Show more info modal.
	$( '.msa-condition-more-info' ).on("click", function( e ) {
		e.preventDefault();
		$( '.msa-modal[data-condition="' + $( this ).data( 'condition' ) + '"]' ).show();
	});

	// Hide modal.
	$( '.msa-modal' ).on("click", function( e ) {
		if ( $( e.target ).is( $( this ) ) ) {
			$( this ).hide();
		}
	});

	$( '.msa-modal-close' ).on("click", function() {
		$( this ).parent().parent().parent().hide();
	});
});
