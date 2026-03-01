<div class="row">
	<div class="col-lg-6">
		<table class="table table-bordered">
			<thead>
				<tr>
					<td colspan="2"><?php echo $text_payment_information; ?></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $text_transaction_id; ?></td>
					<td><a href="<?php echo $transaction_url; ?>" target="_blank"><?php echo $transaction_number; ?></a></td>
				</tr>
				<tr>
					<td><?php echo $text_transaction_status; ?></td>
					<td><?php echo $transaction_status; ?></td>
				</tr>
				<tr>
					<td><?php echo $text_transaction_description; ?></td>
					<td><?php echo ${'text_transaction_' . $transaction_status}; ?></td>
				</tr>
				<tr>
					<td><?php echo $text_payment_product; ?></td>
					<td><?php echo $payment_product; ?></td>
				</tr>
				<tr>
					<td><?php echo $text_total; ?></td>
					<td><?php echo $total; ?></td>
				</tr>
				<tr>
					<td><?php echo $text_amount; ?></td>
					<td><?php echo $amount; ?></td>
				</tr>
				<?php if ($amount_captured > 0) { ?>
				<tr>
					<td><?php echo $text_amount_captured; ?></td>
					<td><?php echo $amount_captured; ?></td>
				</tr>
				<?php } ?>
				<?php if ($amount_refunded > 0) { ?>
				<tr>
					<td><?php echo $text_amount_refunded; ?></td>
					<td><?php echo $amount_refunded; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td><?php echo $text_currency_code; ?></td>
					<td><?php echo $currency_code; ?></td>
				</tr>
				<?php if ($card_bin) { ?>
				<tr>
					<td><?php echo $text_card_bin; ?></td>
					<td><?php echo $card_bin; ?></td>
				</tr>
				<?php } ?>
				<?php if ($card_number) { ?>
				<tr>
					<td><?php echo $text_card_number; ?></td>
					<td><?php echo $card_number; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td><?php echo $text_date_created; ?></td>
					<td><?php echo $date_created;; ?></td>
				</tr>
				<tr>
					<td><?php echo $text_environment; ?></td>
					<td><?php echo $environment; ?></td>
				</tr>
				<?php if (($transaction_status == 'created') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured')) { ?>
				<tr>
					<td><?php echo $text_transaction_action; ?></td>
					<td>
						<?php if ($transaction_status == 'pending_capture') { ?>
						<?php if ($payment_product == 'TWINTWL') { ?>
						<div class="twint">{{ text_twint }}</div>
						<?php } ?>
						<div class="row" style="margin: 0px -4px">
							<div class="col-lg-4" style="padding: 4px 4px;">
								<button type="button" class="btn btn-primary btn-block button-capture"><?php echo $button_capture; ?></button>
							</div>
							<div class="col-lg-4" style="padding: 4px 4px;">
								<input type="text" name="capture_amount" value="<?php echo $capture_amount; ?>" id="input_capture_amount" class="form-control" />
							</div>
						</div>
						<?php } ?>
						<?php if (($transaction_status == 'created') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'pending_capture')) { ?>
						<div class="row" style="margin: 0px -4px">
							<div class="col-lg-4" style="padding: 4px 4px;">
								<button type="button" class="btn btn-primary btn-block button-cancel"><?php echo $button_cancel; ?></button>
							</div>
							<div class="col-lg-4" style="padding: 4px 4px;">
								<input type="text" name="cancel_amount" value="<?php echo $cancel_amount; ?>" id="input_cancel_amount" class="form-control" />
							</div>
						</div>
						<?php } ?>
						<?php if ($transaction_status == 'captured') { ?>
						<div class="row" style="margin: 0px -4px">
							<div class="col-lg-4" style="padding: 4px 4px;">
								<button type="button" class="btn btn-primary btn-block button-refund"><?php echo $button_refund; ?></button>
							</div>
							<div class="col-lg-4" style="padding: 4px 4px;">
								<input type="text" name="refund_amount" value="<?php echo $refund_amount; ?>" id="input_refund_amount" class="form-control" />
							</div>
						</div>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="col-lg-6">
		<?php if ($fraud_result || $liability || $exemption || $authentication_status) { ?>
		<table class="table table-bordered">
			<thead>
				<tr>
					<td colspan="2"><?php echo $text_fraud_information; ?></td>
				</tr>
			</thead>
			<tbody>
				<?php if ($fraud_result) { ?>
				<tr>
					<td><?php echo $text_fraud_result; ?></td>
					<td><?php echo $fraud_result; ?></td>
				</tr>
				<?php } ?>
				<?php if ($liability) { ?>
				<tr>
					<td><?php echo $text_liability; ?></td>
					<td><?php echo $liability; ?></td>
				</tr>
				<?php } ?>
				<?php if ($exemption) { ?>
				<tr>
					<td><?php echo $text_exemption; ?></td>
					<td><?php echo $exemption; ?></td>
				</tr>
				<?php } ?>
				<?php if ($authentication_status) { ?>
				<tr>
					<td><?php echo $text_authentication_status; ?></td>
					<td><?php echo $authentication_status; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">

$('#tab-worldline').on('click', '.button-capture', function() {
	$.ajax({
		type: 'post',
		url: '<?php echo $capture_url; ?>',
		data: {'order_id' : '<?php echo $order_id; ?>', 'transaction_id' : '<?php echo $transaction_id; ?>', 'capture_amount': $('#tab-worldline #input_capture_amount').val()},
		dataType: 'json',
		beforeSend: function() {
			$('#tab-worldline .btn').prop('disabled', true);
		},
		complete: function() {
			$('#tab-worldline .btn').prop('disabled', false);
		},
		success: function(json) {
			$('.alert-dismissible').remove();
			
			if (json['error'] && json['error']['warning']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('#content > .container-fluid .alert-danger').offset().top}, 'slow');
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('#content > .container-fluid .alert-success').offset().top}, 'slow');
				
				$('#tab-worldline').load('<?php echo $info_url; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#tab-worldline').on('click', '.button-cancel', function() {
	$.ajax({
		type: 'post',
		url: '<?php echo $cancel_url; ?>',
		data: {'order_id' : '<?php echo $order_id; ?>', 'transaction_id' : '<?php echo $transaction_id; ?>', 'cancel_amount': $('#tab-worldline #input_cancel_amount').val()},
		dataType: 'json',
		beforeSend: function() {
			$('#tab-worldline .btn').prop('disabled', true);
		},
		complete: function() {
			$('#tab-worldline .btn').prop('disabled', false);
		},
		success: function(json) {
			$('.alert-dismissible').remove();
			
			if (json['error'] && json['error']['warning']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('#content > .container-fluid .alert-danger').offset().top}, 'slow');
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('#content > .container-fluid .alert-success').offset().top}, 'slow');
				
				$('#tab-worldline').load('<?php echo $info_url; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#tab-worldline').on('click', '.button-refund', function() {
	$.ajax({
		type: 'post',
		url: '<?php echo $refund_url; ?>',
		data: {'order_id' : '<?php echo $order_id; ?>', 'transaction_id' : '<?php echo $transaction_id; ?>', 'refund_amount': $('#tab-worldline #input_refund_amount').val()},
		dataType: 'json',
		beforeSend: function() {
			$('#tab-worldline .btn').prop('disabled', true);
		},
		complete: function() {
			$('#tab-worldline .btn').prop('disabled', false);
		},
		success: function(json) {
			$('.alert-dismissible').remove();
			
			if (json['error'] && json['error']['warning']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('#content > .container-fluid .alert-danger').offset().top}, 'slow');
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('#content > .container-fluid .alert-success').offset().top}, 'slow');
				
				$('#tab-worldline').load('<?php echo $info_url; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

</script>