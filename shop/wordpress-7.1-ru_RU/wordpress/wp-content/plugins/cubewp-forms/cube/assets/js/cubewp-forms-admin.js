	jQuery(document).ready(function($) {
		if( jQuery('[name="cwp[form][settings][mailchimp]"]').length > 0 ){
			// Add change event listener to the checkbox
			$('[name="cwp[form][settings][mailchimp]"]').change(function() {
				// Check if the checkbox is checked
				if ($(this).is(':checked')) {
					// If checked, hide all parent tr elements with class mailchimp-based
					$('.mailchimp-based').closest('tr').show();
					$('.field-mailchimp-list').closest('.setting-block').show();
				} else {
					// If not checked, show all parent tr elements with class mailchimp-based
				   $('.mailchimp-based').closest('tr').hide();
				   $('.field-mailchimp-list').closest('.setting-block').hide();
				}
			});
			$('[name="cwp[form][settings][mailchimp]"]').change();
		}
    });
	
	jQuery(document).ready(function($) {
		$(document).on( 'click' , '.cubewp-forms-clear-mailchimp-logs' , function() {
			// Confirm before clearing the logs
			if (confirm('Are you sure you want to clear the logs?')) {
				$.ajax({
					url: ajaxurl, // Use the WordPress AJAX URL
					type: 'POST',
					data: {
						action: 'clear_mailchimp_logs', // WordPress AJAX action name
                        'nonce': cwp_vars_params.nonce
					},
					success: function(response) {
						// Reload the page after clearing the logs
						location.reload();
					}
				});
			}
		});
	});
	
	jQuery(document).ready(function($) {
		// Function to make the AJAX request and append data to the element
		function getEmailTemplateData(selectedOptions) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'get_email_template_data',
					selectedOptions: selectedOptions,
					'nonce': cwp_vars_params.nonce
				},
				success: function(response) {
					// Append the response data to the specified element
					if ($('.cubewp-email-template-shortcode-ajax').length > 0) {
						$('.cubewp-email-template-shortcode-ajax').remove();
					}
					$('.cubewp-email-template-shortcodes').append(response);
				}
			});
		}

		// Load data by default
		if($('body').hasClass('post-new-php') && $('body').hasClass('post-type-email_template_forms')){
			var defaultSelectedOptions = $('#forms_types').val();
			getEmailTemplateData(defaultSelectedOptions);
		}
		
		// Listen for changes in the select field
		$('.post-type-email_template_forms.post-new-php #forms_types').on('change', function() {
			// Get the selected option values
			var selectedOptions = $(this).val();

			// Make AJAX request and append data
			getEmailTemplateData(selectedOptions);
		});
	});
	
	if (jQuery(".cwp-leads-templates-main").length > 0) {
        jQuery(document).on('click', '.cwp-forms-import-template-confirmed', function(e) {
            e.preventDefault(); 
			jQuery(this).append('<div class="loader"></div>');
			jQuery(this).addClass('processing');
			jQuery(this).prop( "disabled", 1 );
			jQuery.ajax({
				type: 'POST',
				url: cwp_vars_params.ajax_url,
				data:'action=cwp_import_dummy_data&data_type=dummy&nonce='+cwp_vars_params.nonce,
				dataType: 'json',
				success: function (response) {
					if( response.success === 'true' ){
						if(response.redirectURL != null && response.redirectURL != ''){
							window.location.href = response.redirectURL;
						}else if(response.success_message != null && response.success_message != ''){
							jQuery(response.success_message.selecter).text(response.success_message.message);
							jQuery(response.success_message.selecter).addClass('done');
						}
					}else if( response.success === 'false' ){
						alert(response.msg);
						jQuery(this).prop( "disabled", 0 );
					}
				}
			});
		});
    }
	
	jQuery(document).ready(function($){
		$('.cwp-popup').css('display','block');
		function setCookie(cookieName, cookieValue, hoursToExpire) {
			var now = new Date();
			var expirationDate = new Date(now.getTime() + hoursToExpire * 3600 * 1000);
			document.cookie = cookieName + "=" + encodeURIComponent(cookieValue) + ";expires=" + expirationDate.toUTCString() + ";path=/";
		}
		//open popup
		$('.cwp-templates-grids-buttons-form').on('click', function(event){
			event.preventDefault();
			var importUrl = jQuery(this).data('import');
			setCookie('cubewp-forms-template-style', importUrl, 1);
			$('.cwp-popup').addClass('is-visible');
		});
		
		//close popup
		$('.cwp-popup').on('click', function(event){
			if( $(event.target).is('.cwp-popup-close') || $(event.target).is('.cwp-popup-no') ) {
				event.preventDefault();
				$(this).removeClass('is-visible');
			}
		});
	});