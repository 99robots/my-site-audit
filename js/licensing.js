/**
 * This file is responsible for all client site logic on the Licenses Page.
 *
 * @param  {document} document The global document object.
 * @return {null}
 */
jQuery(function( $ ) {

	// Define the variables.
	var licenseActivateButton = '<span class="button button-default ' +
		'msa-activate-license">' +
		msaLicensingData.activate_text + '</span>';

	var licenseDeactivateButton = '<span class="button button-default ' +
		'msa-deactivate-license">' +
		msaLicensingData.deactivate_text + '</span>';

	var check = '<span class="fa fa-check form-control-feedback" aria-hidden="true"></span>';
	var error = '<span class="fa fa-times form-control-feedback" aria-hidden="true"></span>';
	var spinner = '<span class="msa-spinner"><img src="' + msaLicensingData.site_url + '/wp-admin/images/spinner-2x.gif"/></span>';

	// Check license key.
	$( '.msa-license-key' ).each( function( index, value ) {
		msaCheckLicense( $( value ).data( 'extension' ), $( value ).val(), $( value ) );
	} );

	// Activate License.
	$( document ).on( 'click', '.msa-activate-license', function( e ) {
		var value = $( e.target ).parent().find( 'input' );
		msaActivateLicense( $( value ).data( 'extension' ), $( value ).val(), $( value ) );
	});

	// Deactivate License.
	$( document ).on( 'click', '.msa-deactivate-license', function( e ) {
		var value = $( e.target ).parent().find( 'input' );
		msaDeactivateLicense( $( value ).data( 'extension' ), $( value ).val(), $( value ) );
	});

	/**
	 * Check the license key for an extension
	 *
	 * @param mixed licenseKey
	 * @return void
	 */
	function msaCheckLicense( extension, licenseKey, element ) {
		var data = {
			'action': 'msa_license_action',
			'license_action': 'check_license',
			'extension': extension,
			'license_key': licenseKey
		};

		// Check if we even have an extension or license key.
		if ( '' !== extension && '' !== licenseKey ) {
			element.parent().append( spinner );

			$.post( ajaxurl, data, function( response ) {
				element.parent().find( '.msa-spinner' ).remove();
				if ( 'valid' === response ) {
					element.parent().append( licenseDeactivateButton );
					element.parent().append( '<p class="description msa-activation-message msa-license-' + response + '">' + msaLicensingData.activation_valid + '</p>' );
				}  else if ( 'expired' === response ) {
					element.parent().append( licenseActivateButton );
					element.parent().append( '<p class="description msa-activation-message msa-license-' + response + '">' + msaLicensingData.expired + '</p>' );
				} else if ( 'inactive' === response ) {
					element.parent().append( licenseActivateButton );
					element.parent().append( '<p class="description msa-activation-message msa-license-' + response + '">' + msaLicensingData.inactive + '</p>' );
				} else {
					element.parent().append( licenseActivateButton );
					element.parent().append( '<p class="description msa-activation-message msa-license-' + response + '">' + msaLicensingData.activation_error + '</p>' );
				}

				console.log( response );
			});
		} else {
			element.addClass( 'msa-license-key-valid' );
			element.parent().append( licenseActivateButton );
			element.parent().append( '<p class="description msa-activation-message">' + msaLicensingData.no_license_key + '</p>' );
		}
	}

	/**
	 * Activate License
	 *
	 * @param mixed extension
	 * @param mixed licenseKey
	 * @return void
	 */
	function msaActivateLicense( extension, licenseKey, element ) {
		var data = {
			'action': 'msa_license_action',
			'license_action': 'activate_license',
			'extension': extension,
			'license_key': licenseKey
		};

		element.parent().append( spinner );
		element.parent().find( '.msa-activation-message' ).remove();
		element.parent().find( '.msa-activate-license' ).remove();
		element.parent().find( '.msa-deactivate-license' ).remove();

		$.post( ajaxurl, data, function( response ) {
			element.parent().find( '.msa-spinner' ).remove();
			if ( 'valid' === response ) {
				element.parent().append( licenseDeactivateButton );
				element.parent().append( '<p class="description msa-activation-message msa-license-' + response + '">' + msaLicensingData.activation_valid + '</p>' );
			} else {
				element.parent().append( licenseActivateButton );
				element.parent().append( '<p class="description msa-activation-message msa-license-' + response + '">' + msaLicensingData.activation_error + '</p>' );
			}

			console.log( response );
		});
	}

	/**
	 * Deactivate a license key
	 *
	 * @param mixed extension
	 * @param mixed licenseKey
	 * @return void
	 */
	function msaDeactivateLicense( extension, licenseKey, element ) {
		var data = {
			'action': 'msa_license_action',
			'license_action': 'deactivate_license',
			'extension': extension,
			'license_key': licenseKey
		};

		element.parent().append( spinner );
		element.parent().find( '.msa-activation-message' ).remove();
		element.parent().find( '.msa-activate-license' ).remove();
		element.parent().find( '.msa-deactivate-license' ).remove();

		$.post( ajaxurl, data, function( response ) {
			element.parent().find( '.msa-spinner' ).remove();
			if ( 'deactivated' === response ) {
				element.val( '' );
				element.parent().append( licenseActivateButton );
				element.parent().append( '<p class="description msa-activation-message">' + msaLicensingData.deactivation_valid + '</p>' );
			} else if ( 'failed' === response ) {
				element.parent().append( '<p class="description msa-activation-message">' + msaLicensingData.deactivation_error + '</p>' );
			} else {
				element.parent().append( '<p class="description msa-activation-message">' + msaLicensingData.deactivation_error + '</p>' );
			}

			console.log( response );
		});
	}
});
