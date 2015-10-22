jQuery(document).ready(function($){

	// Test Shared Count API

	$('.' + nnr_ca_data.prefix + 'shared-count-test').click(function(e){

		e.preventDefault();

		$(this).append('<i style="margin-left: 10px;" class="fa fa-spinner fa-spin"></i>');
		$('.' + nnr_ca_data.prefix + 'shared-count-test').parent().find('em').remove();

		$.post(ajaxurl, {
				'action': 'nnr_ca_shared_count_test',
				'api_key': $('.' + nnr_ca_data.prefix + 'shared-count-api-key').val(),
			}, function(response) {

			$('.' + nnr_ca_data.prefix + 'shared-count-test').find('.fa').remove();

			response = $.parseJSON(response);

			if ( response.status == 'check' ) {
				$('.' + nnr_ca_data.prefix + 'shared-count-test').append('<i style="margin-left: 10px;color:green;" class="fa fa-check"></i>');
				$('.' + nnr_ca_data.prefix + 'shared-count-test').css('border-color', 'green');
			} else {
				$('.' + nnr_ca_data.prefix + 'shared-count-test').append('<i style="margin-left: 10px;color:red;" class="fa fa-warning"></i>');
				$('.' + nnr_ca_data.prefix + 'shared-count-test').css('border-color', 'red');
			}

			$('<em class="help-block">' + response.message + '</em>').insertAfter('.' + nnr_ca_data.prefix + 'shared-count-test');

		});
	});

	$('.' + nnr_ca_data.prefix + 'shared-count-test').append('<i style="margin-left: 10px;" class="fa fa-spinner fa-spin"></i>');

	$.post(ajaxurl, {
			'action': 'nnr_ca_shared_count_test',
			'api_key': $('.' + nnr_ca_data.prefix + 'shared-count-api-key').val(),
		}, function(response) {

		$('.' + nnr_ca_data.prefix + 'shared-count-test').find('.fa').remove();

		response = $.parseJSON(response);

		if ( response.status == 'check' ) {
			$('.' + nnr_ca_data.prefix + 'shared-count-test').append('<i style="margin-left: 10px;color:green;" class="fa fa-check"></i>');
			$('.' + nnr_ca_data.prefix + 'shared-count-test').css('border-color', 'green');
		} else {
			$('.' + nnr_ca_data.prefix + 'shared-count-test').append('<i style="margin-left: 10px;color:red;" class="fa fa-warning"></i>');
			$('.' + nnr_ca_data.prefix + 'shared-count-test').css('border-color', 'red');
		}

		$('<em class="help-block">' + response.message + '</em>').insertAfter('.' + nnr_ca_data.prefix + 'shared-count-test');

		console.log(response);

	});

});