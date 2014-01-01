jQuery(document).ready(function($) {
	setInterval(function() {
		$.ajax({
			url: wp.ajaxurl,
			type: 'POST',
			data: {
				action: 'count',
			},
			success: function(response) {
				var existingResponse = $('.plugin-download-count').html();
				if (existingResponse == response) {
				} else {				
					$('.plugin-download-count').fadeOut(function() {
						$(this).html(response).fadeIn();
					});
				}
			}
		});
		
	}, wp.interval);
});