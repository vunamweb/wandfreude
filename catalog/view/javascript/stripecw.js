(function ($) {
	
	var attachEventHandlers = function() {
		if (typeof stripecw_ajax_submit_callback != 'undefined') {
			$('.stripecw-confirmation-buttons input').each(function () {
				$(this).click(function() {
					StripeCwHandleAjaxSubmit();
				});
			});
		}
	};
	
	var getFieldsDataArray = function () {
		var fields = {};
		
		var data = $('#stripecw-confirmation-ajax-authorization-form').serializeArray();
		$(data).each(function(index, value) {
			fields[value.name] = value.value;
		});
		
		return fields;
	};
	
	var StripeCwHandleAjaxSubmit = function() {
		
		if (typeof cwValidateFields != 'undefined') {
			cwValidateFields(StripeCwHandleAjaxSubmitValidationSuccess, function(errors, valid){alert(errors[Object.keys(errors)[0]]);});
			return false;
		}
		StripeCwHandleAjaxSubmitValidationSuccess(new Array());
		
	};
	
	var StripeCwHandleAjaxSubmitValidationSuccess = function(valid) {
		
		if (typeof stripecw_ajax_submit_callback != 'undefined') {
			stripecw_ajax_submit_callback(getFieldsDataArray());

		}
		else {
			alert("No JavaScript callback function defined.");
		}
	}
		
	$( document ).ready(function() {
		attachEventHandlers();
		
		$('#stripecw_alias').change(function() {
			$('#stripecw-checkout-form-pane').css({
				opacity: 0.5,
			});
			$.ajax({
				type: 		'POST',
				url: 		'index.php?route=checkout/confirm',
				data: 		'stripecw_alias=' + $('#stripecw_alias').val(),
				success: 	function( response ) {
					var htmlCode = '';
					try {
						var jsonObject = jQuery.parseJSON(response);
						htmlCode = jsonObject.output;
					}
					catch (err){
						htmlCode = response;
					}
					
					var newPane = $("#stripecw-checkout-form-pane", $(htmlCode));
					if (newPane.length > 0) {
						var newContent = newPane.html();
						$('#stripecw-checkout-form-pane').html(newContent);
						attachEventHandlers();
					}
					
					$('#stripecw-checkout-form-pane').animate({
						opacity : 1,
						duration: 100, 
					});
				},
			});
		});
		
	});
	
}(jQuery));