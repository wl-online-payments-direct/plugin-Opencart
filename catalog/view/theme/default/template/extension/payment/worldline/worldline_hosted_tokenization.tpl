<div id="worldline-form">
	<?php if ($surcharging_status) { ?>
	<div class="alert alert-info alert-dismissible"><i class="fa fa-info-circle"></i> <?php echo $text_surcharging; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div>
	<?php } ?>
	<div class="buttons">
		<div class="pull-right">
			<button type="button" id="button-confirm" class="worldline-button btn btn-primary"><?php echo $hosted_tokenization['button_title']; ?></button>
		</div>
	</div>
</div>
<style type="text/css">

#worldline-hosted-tokenization-form iframe {
    width: 100%;
	border: none;
}

</style>
<script type="text/javascript">

var readyWorldlineSDK = function() {
	if (typeof window.Tokenizer === 'undefined') {
		setTimeout(readyWorldlineSDK, 100);
	} else {
		initWorldlineSDK();
	}
}

var initWorldlineSDK = function() {
	$('#worldline-form #button-confirm').on('click', function() {	
		$('#worldline-form .worldline-button').prop('disabled', true);
			
		$('#worldline-modal').remove();
	
		$('body').append('<div id="worldline-modal" class="modal fade"></div>');
	
		$('#worldline-modal').load('index.php?route=extension/payment/worldline_hosted_tokenization/modal #worldline-modal >', function() {		
			$('#worldline-form .worldline-button').prop('disabled', false);
			
			$('#worldline-modal').modal('show');
					
			if ($('#worldline-card-form').attr('hosted-tokenization-url')) {											
				var tokenizer = new Tokenizer($('#worldline-card-form').attr('hosted-tokenization-url'), 'worldline-card-form', {hideCardholderName: false });
		
				tokenizer.initialize().then(() => { 
					$('#worldline-hosted-tokenization-form .worldline-button').prop('disabled', false);
				}).catch(reason => {
					$('#worldline-hosted-tokenization-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + reason + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
													
					console.log(reason);
				});	

				$('#worldline-hosted-tokenization-form .card-token-button').on('click', function() {
					$('#worldline-hosted-tokenization-form .card-token-button').removeClass('selected');
					
					$(this).addClass('selected');
					
					if ($(this).attr('token')) {
						tokenizer.useToken($(this).attr('token'));
						
						$('#worldline-hosted-tokenization-form .checkbox-save').addClass('hidden');
					} else {
						tokenizer.useToken();
						
						$('#worldline-hosted-tokenization-form .checkbox-save').removeClass('hidden');
					}
				});
				
				$('#worldline-hosted-tokenization-form .card-token-delete-button').on('click', function() {
					if (!$('#worldline-card-tokens-container').hasClass('disabled')) {
						var worldline_card_token = $(this).parents('.worldline-card-token');
						var token = worldline_card_token.find('.card-token-button').attr('token');
														
						worldline_card_token.addClass('worldline-spinner');
						$('#worldline-card-tokens-container').addClass('disabled');
									
						$.ajax({
							type: 'post',
							url: 'index.php?route=extension/payment/worldline/deleteCustomerToken',
							data: {'token': token},
							dataType: 'json',
							success: function(json) {
								if (json['error']) {
									if (json['error']['warning']) {
										$('#worldline-hosted-tokenization-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
									}
								}
									
								if (json['success']) {
									worldline_card_token.remove();
								} else {
									worldline_card_token.removeClass('worldline-spinner');
								}
								
								$('#worldline-card-tokens-container').removeClass('disabled');
								
								$('#worldline-hosted-tokenization-form .card-new-token-button').trigger('click');
							},
							error: function(xhr, ajaxOptions, thrownError) {
								console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});	
					}
				});	
						
				$('#worldline-hosted-tokenization-form #worldline-card-button').on('click', function() {
					tokenizer.submitTokenization().then((result) => {
						if (result.success) {
							$('#worldline-hosted-tokenization-form #browser-info').remove();
	
							html  = '<div id="browser-info">';
							html += '<input type="hidden" name="browser_color_depth" value="' + window.screen.colorDepth + '" />';
							html += '<input type="hidden" name="browser_screen_height" value="' + window.screen.height + '" />';
							html += '<input type="hidden" name="browser_screen_width" value="' + window.screen.width + '" />';
							html += '<input type="hidden" name="browser_timezone_offset" value="' + new Date().getTimezoneOffset() + '" />';
							html += '</div>';
							
							$('#worldline-hosted-tokenization-form').append(html);
							
							var card_token_save = ($('#worldline-hosted-tokenization-form #worldline-card-token-save:checked').length ? $('#worldline-hosted-tokenization-form #worldline-card-token-save:checked').val() : 0);
							
							$.ajax({
								type: 'post',
								url: 'index.php?route=extension/payment/worldline/confirm&hostedTokenizationId=' + result.hostedTokenizationId + '&cardTokenSave=' + card_token_save,
								data: $('#worldline-hosted-tokenization-form input[type="hidden"]'),
								dataType: 'json',
								beforeSend: function() {
									$('#worldline-hosted-tokenization-form .worldline-button').prop('disabled', true)
								},
								complete: function() {
								   $('#worldline-hosted-tokenization-form .worldline-button').prop('disabled', true)
								},
								success: function(json) {
									$('#worldline-hosted-tokenization-form .alert-dismissible').remove();
										
									if (json['error']) {
										if (json['error']['warning']) {
											$('#worldline-hosted-tokenization-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
										}
									}

									if (json['redirect']) {
										location = json['redirect'];	
									}
								},
								error: function(xhr, ajaxOptions, thrownError) {
									console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
								}
							});
						} else {
							$('#worldline-hosted-tokenization-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + result.error.message + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						}
					});
				});	
			}			
		});
	});
}

if (typeof window.Tokenizer === 'undefined') {
	var worldline_style = document.createElement('link');
	worldline_style.type = 'text/css';
	worldline_style.rel = 'stylesheet';
	worldline_style.href = 'catalog/view/theme/default/stylesheet/worldline/worldline.css';
    document.querySelector('head').appendChild(worldline_style);
	
	var worldline_script = document.createElement('script');
	worldline_script.type = 'text/javascript';
	worldline_script.setAttribute('src', '<?php echo $api_url; ?>/hostedtokenization/js/client/tokenizer.min.js');
	worldline_script.async = false;
	worldline_script.onload = readyWorldlineSDK();
    document.querySelector('body').appendChild(worldline_script);
} else {
	initWorldlineSDK();
}

</script>