(function($) {
	
	
	var disableElements = function() {

		// Enable all
		$('.stripecw-control-box').each(function () {
			$(this).find('input').prop('disabled', false);
			$(this).find('select').prop('disabled', false);
			$(this).find('textarea').prop('disabled', false);
		});
		
		// Disable selected
		$('.stripecw-use-default .stripecw-control-box').each(function () {
			$(this).find('input').prop('disabled', true);
			$(this).find('select').prop('disabled', true);
			$(this).find('textarea').prop('disabled', true);
		});
	};

	$(document).ready(function() {
		$(".stripecw-default-box input").click(function() {
			$(this).parents(".control-box-wrapper").toggleClass('stripecw-use-default');
			disableElements();
		});
		disableElements();
	});

})(jQuery);
