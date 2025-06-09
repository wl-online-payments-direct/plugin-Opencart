<div id="worldline-form">
	<div class="buttons">
		<div class="pull-right">
			<button type="button" id="worldline-button-confirm" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>"><?php echo $button_title; ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">

$('#worldline-form #worldline-button-confirm').on('click', function() {	
	$('#worldline-form #browser-info').remove();
	
	html  = '<div id="browser-info">';
	html += '<input type="hidden" name="browser_color_depth" value="' + window.screen.colorDepth + '" />';
	html += '<input type="hidden" name="browser_screen_height" value="' + window.screen.height + '" />';
	html += '<input type="hidden" name="browser_screen_width" value="' + window.screen.width + '" />';
	html += '</div>';
	
	$('#worldline-form').append(html);
	
	$.ajax({
		type: 'post',
		url: 'index.php?route=extension/payment/worldline/confirm',
		data: $('#worldline-form input[type="hidden"]'),
		dataType: 'json',
		beforeSend: function() {
            $('#worldline-button-confirm').prop('disabled', true).button('loading');
        },
        complete: function() {
           $('#worldline-button-confirm').prop('disabled', false).button('reset');
        },
		success: function(json) {
			$('#worldline-form .alert-dismissible').remove();
				
			if (json['error']) {
				if (json['error']['warning']) {
					$('#worldline-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> ' + json['error']['warning'] + '</div>');
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
});

</script>