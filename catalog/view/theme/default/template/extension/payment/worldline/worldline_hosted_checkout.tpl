<div id="worldline-form">
	<?php if ($surcharging_status) { ?>
	<div class="alert alert-info alert-dismissible"><i class="fa fa-info-circle"></i> <?php echo $text_surcharging; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div>
	<?php } ?>
	<div class="buttons">
		<div class="pull-right">
			<button type="button" id="button-confirm" class="worldline-button btn btn-primary"><?php echo $hosted_checkout['button_title']; ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">

$('#worldline-form #button-confirm').on('click', function() {	
	$('#worldline-form #browser-info').remove();
	
	html  = '<div id="browser-info">';
	html += '<input type="hidden" name="browser_color_depth" value="' + window.screen.colorDepth + '" />';
	html += '<input type="hidden" name="browser_screen_height" value="' + window.screen.height + '" />';
	html += '<input type="hidden" name="browser_screen_width" value="' + window.screen.width + '" />';
	html += '<input type="hidden" name="browser_timezone_offset" value="' + new Date().getTimezoneOffset() + '" />';
	html += '</div>';
	
	$('#worldline-form').append(html);
		
	$.ajax({
		type: 'post',
		url: 'index.php?route=extension/payment/worldline/confirm',
		data: $('#worldline-form input[type="hidden"]'),
		dataType: 'json',
		beforeSend: function() {
            $('#worldline-form .worldline-button').prop('disabled', true);
        },
        complete: function() {
            $('#worldline-form .worldline-button').prop('disabled', false);
        },
		success: function(json) {
			$('#worldline-form .alert-dismissible').remove();
				
			if (json['error']) {
				if (json['error']['warning']) {
					$('#worldline-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
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