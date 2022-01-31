/**
 * This file is responsible for all client site logic on the Single Audit Page.
 *
 * @param  {document} document The global document object.
 * @return {null}
 */
jQuery(function( $ ) {

	// Move show columns around to relate to the condition categories.
	$( '#adv-settings h5' ).remove();
	$.each( msaSingleAuditData.condition_categories, function( index, value ) {
		$( '<div class="msa-condition-category-column msa-condition-category-column-' + index + '">' +
			'<h5>' + value.name + '</h5>' +
		'</div>' ).insertBefore( 'form .metabox-prefs' );
	} );

	// Attributes.
	$( '<h5>' + msaSingleAuditData.attribute_title + '</h5>' ).insertBefore( 'form .metabox-prefs' );
	$.each( msaSingleAuditData.conditions, function( index, value ) {
		$( 'input#' + index + '-hide' ).parent().appendTo( '.msa-condition-category-column-' + value.category );
	} );

	// Add Filters.
	$( '.msa-filter-button' ).on("click", function( e ) {
		var parameters = '';
		e.preventDefault();

		$( '.msa-filter' ).each( function( index, value ) {
			if ( 0 !== $( value ).length ) {
				parameters += '&' + $( value ).attr( 'name' ) + '=' + $( value ).val();
			}
		});

		window.location += parameters;
	} );

	// Clear Filters.
	$( '.msa-clear-filters-button' ).on("click", function( e ) {
		e.preventDefault();
		window.location = msaSingleAuditData.audit_page;
	} );

	// Hide and show the columns.
	$( '.hide-column-tog' ).each( function( index, value ) {
		var column = $( value );
		column.prop( 'checked', false );
		$( '.column-' + column.val() ).hide();
		$( '.filter-' + column.val() ).hide();

		$.each( msaSingleAuditData.show_columns, function( index, value ) {
			if ( column.val() === value ) {
				column.prop( 'checked', true );
				$( '.column-' + column.val() ).show();
				$( '.filter-' + column.val() ).show();
				return false;
			}
		} );
	} );

	$( '.hide-column-tog' ).on("change", function() {
		if ( $( this ).prop( 'checked' ) ) {
			$( '.column-' + $( this ).val() ).show();
			$( '.filter-' + $( this ).val() ).show();

			msaShowColumn( 'add', $( this ).val() );
		} else {
			$( '.column-' + $( this ).val() ).hide();
			$( '.filter-' + $( this ).val() ).hide();

			msaShowColumn( 'remove', $( this ).val() );
		}
	} );

	/**
	 * Either add or remove a column from the all posts table
	 *
	 * @param mixed action
	 * @param mixed column
	 * @return void
	 */
	function msaShowColumn( action, column ) {

		$.post( ajaxurl, {
				'action': 'msaShowColumn',
				'action_needed': action,
				'show_column_nonce': msaSingleAuditData.show_column_nonce,
				'column': column
			}, function( response ) {
			console.log( response );
		});
	}

	/**
	 * Get the URL parameter
	 * Credit: http://stackoverflow.com/questions/19491336/get-url-parameter-jquery
	 *
	 * @param mixed sParam
	 * @return void
	 */
	function msaGetUrlParameter( sParam ) {
		var sPageURL = decodeURIComponent( window.location.search.substring( 1 ) ),
			sURLVariables = sPageURL.split( '&' ),
			sParameterName,
			i;

		for ( i = 0; i < sURLVariables.length; i++ ) {
			sParameterName = sURLVariables[i].split( '=' );

			if ( sParameterName[0] === sParam ) {
				return undefined === sParameterName[1] ? true : sParameterName[1];
			}
		}
	}
});
