jQuery(document).ready(function($){

	// Define the variables

	var license_activate_button = '<span class="btn btn-primary ' +
		nnr_ca_updates_data.prefix_dash + 'activate-license">' +
		nnr_ca_updates_data.activate_text + '</span>';

	var license_deactivate_button = '<span class="btn btn-default ' +
		nnr_ca_updates_data.prefix_dash + 'deactivate-license">' +
		nnr_ca_updates_data.deactivate_text + '</span>';

	var check = '<span class="fa fa-check form-control-feedback" aria-hidden="true"></span>';
	var error = '<span class="fa fa-times form-control-feedback" aria-hidden="true"></span>';
	var spinner = '<i class="' + nnr_ca_updates_data.prefix_dash + 'license-key-status fa fa-spinner fa-spin form-control-feedback"></i>';

	// Check license key

	if ( $('.' + nnr_ca_updates_data.prefix_dash + 'license-key').val() != '' ) {

		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(spinner);

		var data = {
			'action': 'nnr_ca_license_action',
			'license_action': 'check_license',
			'license_key': $('.' + nnr_ca_updates_data.prefix_dash + 'license-key').val(),
		};

		$.post(ajaxurl, data, function(response) {

			$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().find('.fa').remove();
			$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').removeClass('has-success');
			$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').removeClass('has-error');

			if ( response == 'valid' ) {

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').addClass(nnr_ca_updates_data.prefix_dash + 'license-key-valid');
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(check);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').addClass('has-success');

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append(license_deactivate_button);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.activation_valid + '</em>');

			}  else if ( response == 'expired' ) {

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').addClass(nnr_ca_updates_data.prefix_dash + 'license-key-valid');
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(error);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').addClass('has-error');

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append(license_activate_button);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.expired + '</em>');

			} else {

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').addClass(nnr_ca_updates_data.prefix_dash + 'license-key-valid');
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(error);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').addClass('has-error');

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append(license_activate_button);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.activation_error + '</em>');

			}

			console.log(response);
		});
	} else {
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').addClass(nnr_ca_updates_data.prefix_dash + 'license-key-valid');
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(error);
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').addClass('has-error');

		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append(license_activate_button);
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.no_license_key + '</em>');
	}

	// Activate License

	$(document).on('click', '.' + nnr_ca_updates_data.prefix_dash + 'activate-license', function(){

		$('.' + nnr_ca_updates_data.prefix_dash + 'activation-message').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().find('.fa').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(spinner);
		$('.' + nnr_ca_updates_data.prefix_dash + 'activate-license').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'deactivate-license').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').removeClass('has-success');
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').removeClass('has-error');

		var data = {
			'action': 'nnr_ca_license_action',
			'license_action': 'activate_license',
			'license_key': $('.' + nnr_ca_updates_data.prefix_dash + 'license-key').val(),
		};

		$.post(ajaxurl, data, function(response) {

			$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().find('.fa').remove();

			if ( response == 'valid' ) {

				$('.alert').remove();

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').addClass(nnr_ca_updates_data.prefix_dash + 'license-key-valid');
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(check);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').addClass('has-success');

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append(license_deactivate_button);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.activation_valid + '</em>');

			} else {

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').addClass(nnr_ca_updates_data.prefix_dash + 'license-key-valid');
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(error);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').addClass('has-error');

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append(license_activate_button);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.activation_error + '</em>');

			}

			console.log(response);
		});

	});

	// Deactivate License

	$(document).on('click', '.' + nnr_ca_updates_data.prefix_dash + 'deactivate-license', function(){

		$('.' + nnr_ca_updates_data.prefix_dash + 'activation-message').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().find('.fa').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(spinner);
		$('.' + nnr_ca_updates_data.prefix_dash + 'activate-license').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'deactivate-license').remove();
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').removeClass('has-success');
		$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').removeClass('has-error');

		var data = {
			'action': 'nnr_ca_license_action',
			'license_action': 'deactivate_license',
			'license_key': $('.' + nnr_ca_updates_data.prefix_dash + 'license-key').val(),
		};

		$.post(ajaxurl, data, function(response) {

			$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().find('.fa').remove();

			if ( response == 'deactivated' ) {

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').val('');
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append(license_activate_button);
				///$('.' + nnr_ca_updates_data.prefix_dash + 'license-key-feedback').addClass('has-success');

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().append(check);
				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.deactivation_valid + '</em>');

			} else if ( response == 'failed' ) {

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.deactivation_error + '</em>');

			} else {

				$('.' + nnr_ca_updates_data.prefix_dash + 'license-key').parent().parent().append('<em class="help-block ' + nnr_ca_updates_data.prefix_dash + 'activation-message">' + nnr_ca_updates_data.deactivation_error + '</em>');
			}

			console.log(response);
		});

	});

});