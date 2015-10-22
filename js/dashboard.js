jQuery(document).ready(function($){

	// Get all share count data for posts

	$('.' + nnr_ca_data.prefix + 'share-count[data-post]').each(function(index, value){

		$.post(ajaxurl, {
				'action': 'nnr_ca_share_count_post',
				'post' : $(value).data('post'),
			}, function(response) {

			response = $.parseJSON(response);

			$('.' + nnr_ca_data.prefix + 'share-count[data-post="' + response.post + '"]').html(response.count);

			$($.bootstrapSortable);

		});
	});

});