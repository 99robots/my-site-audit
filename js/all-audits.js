/**
 * This file is responsible for all client site logic on the All Audits Page.
 *
 * @param  {document} document The global document object.
 * @return {null}
 */
jQuery(function( $ ) {

	// Show the modal.
	$( '.msa-audit-conditions-button' ).on("click", function( e ) {
		e.preventDefault();

		$( '.msa-audit-conditions-modal' ).hide();
		$( '.msa-audit-conditions-modal[data-id="' + $( this ).data( 'id' ) + '"]' ).show();
	} );

	// Move modal to end of body.
	$( '.msa-audit-conditions-modal' ).each( function( index, value ) {
		$( $( this ) ).appendTo( 'body' );
	} );

	// Close Modal.
	$( '.msa-audit-conditions-modal' ).on("click", function( e ) {
		if ( $( e.target ).is( $( this ) ) ) {
			$( this ).hide();
		}
	} );

	// Datepicker.
	if ( 0 !== $( '.msa-datepicker' ).length ) {
		$( '.msa-datepicker' ).daterangepicker( {
			presetRanges: [{
				text: 'Last Month',
				/**
				 * Set the start date
				 * @return {moment} The start date.
				 */
				dateStart: function() {
					return moment().subtract( 'months', 1 );
				},
				/**
				 * Set the end date
				 * @return {moment} The end date.
				 */
				dateEnd: function() {
					return moment();
				}
			}, {
				text: 'Last 6 Months',
				/**
				 * Set the start date
				 * @return {moment} The start date.
				 */
				dateStart: function() {
					return moment().subtract( 'months', 6 );
				},
				/**
				 * Set the end date
				 * @return {moment} The end date.
				 */
				dateEnd: function() {
					return moment();
				}
			}, {
				text: 'Last Year',
				/**
				 * Set the start date
				 * @return {moment} The start date.
				 */
				dateStart: function() {
					return moment().subtract( 'years', 1 );
				},
				/**
				 * Set the end date
				 * @return {moment} The end date.
				 */
				dateEnd: function() {
					return moment();
				}
			}, {
				text: 'All Time',
				/**
				 * Set the start date
				 * @return {moment} The start date.
				 */
				dateStart: function() {
					return moment().subtract( 'years', 20 );
				},
				/**
				 * Set the end date
				 * @return {moment} The end date.
				 */
				dateEnd: function() {
					return moment();
				}
			}],
			applyOnMenuSelect: false
		 });
		$( '.msa-datepicker' ).daterangepicker( 'setRange', {
			start: new Date( $( '.msa-datepicker' ).data( 'start-date' ) ),
			end: new Date( $( '.msa-datepicker' ).data( 'end-date' ) )
		} );
	}

	// Hide and show the create new settings.
	$( '.msa-add-new-audit' ).on("click", function() {
		if ( 'none' !== $( '.msa-create-audit-wrap' ).css( 'display' ) ) {
			$( '.msa-create-audit-wrap' ).slideUp();
		} else {
			$( '.msa-create-audit-wrap' ).slideDown();
		}
	});
});
