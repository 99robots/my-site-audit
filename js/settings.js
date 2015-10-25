jQuery(document).ready(function($){

	// Test Shared Count API

	$('.msa-shared-count-test').click(function(e){

		e.preventDefault();

		$(this).append('<i style="margin-left: 10px;" class="fa fa-spinner fa-spin"></i>');
		$('.msa-shared-count-test').parent().find('p').remove();

		$.post(ajaxurl, {
				'action': 'msa_shared_count_test',
				'api_key': $('.msa-shared-count-api-key').val(),
			}, function(response) {

			$('.msa-shared-count-test').find('.fa').remove();

			response = $.parseJSON(response);

			if ( response.status == 'check' ) {
				$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:green;" class="fa fa-check"></i>');
				$('.msa-shared-count-test').css('border-color', 'green');
			} else {
				$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:red;" class="fa fa-warning"></i>');
				$('.msa-shared-count-test').css('border-color', 'red');
			}

			$('<p class="description">' + response.message + '</p>').insertAfter('.msa-shared-count-test');

		});
	});

	$('.msa-shared-count-test').append('<i style="margin-left: 10px;" class="fa fa-spinner fa-spin"></i>');

	$.post(ajaxurl, {
			'action': 'msa_shared_count_test',
			'api_key': $('.msa-shared-count-api-key').val(),
		}, function(response) {

		$('.msa-shared-count-test').find('.fa').remove();

		response = $.parseJSON(response);

		if ( response.status == 'check' ) {
			$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:green;" class="fa fa-check"></i>');
			$('.msa-shared-count-test').css('border-color', 'green');
		} else {
			$('.msa-shared-count-test').append('<i style="margin-left: 10px;color:red;" class="fa fa-warning"></i>');
			$('.msa-shared-count-test').css('border-color', 'red');
		}

		$('<p class="description">' + response.message + '</p>').insertAfter('.msa-shared-count-test');

		console.log(response);

	});

});