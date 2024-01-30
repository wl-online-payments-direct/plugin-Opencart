<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="payment-worldline">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $sign_up; ?>" target="_blank" class="btn btn-primary"><?php echo $button_sign_up; ?></a>
				<a href="<?php echo $contact_us; ?>" target="_blank" class="btn btn-primary"><?php echo $button_contact_us; ?></a>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($text_version) { ?>
		<div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_version; ?></div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<ul class="nav nav-tabs">
					<li class="nav-tab"><a href="<?php echo $href_account; ?>" class="tab"><i class="fa fa-user"></i> <?php echo $text_tab_account; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_advanced; ?>" class="tab"><i class="fa fa-cogs"></i> <?php echo $text_tab_advanced; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_order_status; ?>" class="tab"><i class="fa fa-shopping-cart"></i> <?php echo $text_tab_order_status; ?></a></li>
					<li class="nav-tab active"><a href="<?php echo $href_transaction; ?>" class="tab"><i class="fa fa-money"></i> <?php echo $text_tab_transaction; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_suggest; ?>" class="tab"><i class="fa fa-envelope-o"></i> <?php echo $text_tab_suggest; ?></a></li>
				</ul>
				<div class="tab-content">
					<div class="well">
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="input-order-id"><?php echo $entry_order_id; ?></label>
									<input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" id="input-order-id" class="form-control" />
								</div>
								<div class="form-group">
									<label class="control-label" for="input-transaction-id"><?php echo $entry_transaction_id; ?></label>
									<input type="text" name="filter_transaction_id" value="<?php echo $filter_transaction_id; ?>" id="input-transaction-id" class="form-control" />
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label" for="input-transaction-status"><?php echo $entry_transaction_status; ?></label>
									<select name="filter_transaction_status" id="input-transaction-status" class="form-control">
										<option value="*"></option>
										<?php foreach ($setting['transaction_status'] as $transaction_status) { ?>
										<?php if ($transaction_status['code'] == $filter_transaction_status) { ?>
										<option value="<?php echo $transaction_status['code']; ?>" selected="selected"><?php echo ${$transaction_status['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $transaction_status['code']; ?>"><?php echo ${$transaction_status['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label class="control-label" for="input-payment-product"><?php echo $entry_payment_product; ?></label>
									<input type="text" name="filter_payment_product" value="<?php echo $filter_payment_product; ?>" id="input-payment-product" class="form-control" />
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label" for="input_date_from"><?php echo $entry_date_from; ?></label>
									<div class="input-group date">
										<input type="text" name="filter_date_from" value="<?php echo $filter_date_from; ?>" data-date-format="YYYY-MM-DD" id="input_date_from" class="form-control" />
										<span class="input-group-btn">
											<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label" for="input_date_to"><?php echo $entry_date_to; ?></label>
									<div class="input-group date">
										<input type="text" name="filter_date_to" value="<?php echo $filter_date_to; ?>" data-date-format="YYYY-MM-DD" id="input_date_to" class="form-control" />
										<span class="input-group-btn">
											<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label" for="inputtotal"><?php echo $entry_total; ?></label>
									<input type="text" name="filter_total" value="<?php echo $filter_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
								</div>
								<div class="form-group">
									<label class="control-label" for="input-amount"><?php echo $entry_amount; ?></label>
									<input type="text" name="filter_amount" value="<?php echo $filter_amount; ?>" placeholder="<?php echo $entry_amount; ?>" id="input-amount" class="form-control" />
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label class="control-label" for="input-currency"><?php echo $entry_currency; ?></label>
									<select name="filter_currency_code" id="input-currency" class="form-control">
										<option value="*"></option>
										<?php foreach ($currencies as $currency) { ?>
										<?php if ($currency['code'] == $filter_currency_code) { ?>
										<option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['code']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $currency['code']; ?>"><?php echo $currency['code']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label class="control-label" for="input-environment"><?php echo $entry_environment; ?></label>
									<select name="filter_environment" id="input-environment" class="form-control">
										<option value="*"></option>
										<?php foreach ($setting['environment'] as $environment) { ?>
										<?php if ($environment['code'] == $filter_environment) { ?>
										<option value="<?php echo $environment['code']; ?>" selected="selected"><?php echo ${$environment['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $environment['code']; ?>"><?php echo ${$environment['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
								<br/>
								<button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
							</div>
						</div>
					</div>
					<div id="orders" class="orders">
						<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-order">
							<div class="table-responsive">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<td class="text-center">
												<?php if ($sort == 'wo.order_id') { ?>
												<a href="<?php echo $sort_order_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_order_id; ?>"><?php echo $column_order_id; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.transaction_id') { ?>
												<a href="<?php echo $sort_transaction_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_transaction_id; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_transaction_id; ?>"><?php echo $column_transaction_id; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.transaction_status') { ?>
												<a href="<?php echo $sort_transaction_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_transaction_status; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_transaction_status; ?>"><?php echo $column_transaction_status; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.payment_product') { ?>
												<a href="<?php echo $sort_payment_product; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_payment_product; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_payment_product; ?>"><?php echo $column_payment_product; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.date') { ?>
												<a href="<?php echo $sort_date; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_date; ?>"><?php echo $column_date; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.total') { ?>
												<a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.amount') { ?>
												<a href="<?php echo $sort_amount; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_amount; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_amount; ?>"><?php echo $column_amount; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.currency_code') { ?>
												<a href="<?php echo $sort_currency_code; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_currency_code; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_currency_code; ?>"><?php echo $column_currency_code; ?></a>
												<?php } ?>
											</td>
											<td class="text-center">
												<?php if ($sort == 'wo.environment') { ?>
												<a href="<?php echo $sort_environment; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_environment; ?></a>
												<?php } else { ?>
												<a href="<?php echo $sort_environment; ?>"><?php echo $column_environment; ?></a>
												<?php } ?>
											</td>
											<td class="text-center"><?php echo $column_action; ?></td>
										</tr>
									</thead>
									<tbody>
										<?php if ($orders) { ?>
										<?php foreach ($orders as $order_info) { ?>
										<tr>
											<td class="text-center"><a href="<?php echo $order_info['order_url']; ?>" target="_blank"><?php echo $order_info['order_id']; ?></a></td>
											<td class="text-center"><a href="<?php echo $order_info['transaction_url']; ?>" target="_blank"><?php echo $order_info['transaction_id']; ?></a></td>
											<td class="text-center"><?php echo $order_info['transaction_status']; ?></td>
											<td class="text-center"><?php echo $order_info['payment_product']; ?></td>
											<td class="text-center"><?php echo $order_info['date']; ?></td>
											<td class="text-center"><?php echo $order_info['total']; ?></td>
											<td class="text-center"><?php echo $order_info['amount']; ?></td>
											<td class="text-center"><?php echo $order_info['currency_code']; ?></td>
											<td class="text-center"><?php echo $order_info['environment']; ?></td>
											<td class="text-center">
												<?php if ($order_info['transaction_status'] == 'pending_capture') { ?>
												<button type="button" class="btn btn-primary btn-block button-capture" order_id="<?php echo $order_info['order_id']; ?>" transaction_id="<?php echo $order_info['transaction_id']; ?>"><?php echo $button_capture; ?></button>
												<?php } ?>
												<?php if (($order_info['transaction_status'] == 'created') || ($order_info['transaction_status'] == 'rejected') || ($order_info['transaction_status'] == 'rejected_capture') || ($order_info['transaction_status'] == 'pending_capture')) { ?>
												<button type="button" class="btn btn-primary btn-block button-cancel" order_id="<?php echo $order_info['order_id']; ?>" transaction_id="<?php echo $order_info['transaction_id']; ?>"><?php echo $button_cancel; ?></button>
												<?php } ?>
												<?php if ($order_info['transaction_status'] == 'captured') { ?>
												<button type="button" class="btn btn-primary btn-block button-refund" order_id="<?php echo $order_info['order_id']; ?>" transaction_id="<?php echo $order_info['transaction_id']; ?>"><?php echo $button_refund; ?></button>
												<?php } ?>
											</td>	
										</tr>
										<?php } ?>
										<?php } else { ?>
										<tr>
											<td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<div class="row">
								<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
								<div class="col-sm-6 text-right"><?php echo $results; ?></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$('.payment-worldline .date').datetimepicker({
	pickDate: true,
	pickTime: false
});
	
$('.payment-worldline .orders').delegate('.button-capture', 'click', function(event) {
	var order_id = $(this).attr('order_id');
	var transaction_id = $(this).attr('transaction_id');
		
	$.ajax({
		type: 'post',
		url: '<?php echo $capture_url; ?>',
		data: {'order_id' : order_id, 'transaction_id' : transaction_id},
		dataType: 'json',
		beforeSend: function() {
			$('.payment-worldline .orders .btn').prop('disabled', true);
		},
		complete: function() {
			$('.payment-worldline .orders .btn').prop('disabled', false);
		},
		success: function(json) {
			$('.payment-worldline .alert-dismissible').remove();
			
			if (json['error'] && json['error']['warning']) {
				$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('.payment-worldline > .container-fluid .alert-danger').offset().top}, 'slow');
			}
			
			if (json['success']) {
				$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('.payment-worldline > .container-fluid .alert-success').offset().top}, 'slow');
				
				$('.payment-worldline .orders').load($('.payment-worldline #form-order').attr('action') + ' #form-order');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('.payment-worldline .orders').delegate('.button-cancel', 'click', function(event) {
	var order_id = $(this).attr('order_id');
	var transaction_id = $(this).attr('transaction_id');
		
	$.ajax({
		type: 'post',
		url: '<?php echo $cancel_url; ?>',
		data: {'order_id' : order_id, 'transaction_id' : transaction_id},
		dataType: 'json',
		beforeSend: function() {
			$('.payment-worldline .orders .btn').prop('disabled', true);
		},
		complete: function() {
			$('.payment-worldline .orders .btn').prop('disabled', false);
		},
		success: function(json) {
			$('.payment-worldline .alert-dismissible').remove();
			
			if (json['error'] && json['error']['warning']) {
				$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('.payment-worldline > .container-fluid .alert-danger').offset().top}, 'slow');
			}
			
			if (json['success']) {
				$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('.payment-worldline > .container-fluid .alert-success').offset().top}, 'slow');
			
				$('.payment-worldline .orders').load($('.payment-worldline #form-order').attr('action') + ' #form-order');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('.payment-worldline .orders').delegate('.button-refund', 'click', function(event) {
	var order_id = $(this).attr('order_id');
	var transaction_id = $(this).attr('transaction_id');
		
	$.ajax({
		type: 'post',
		url: '<?php echo $refund_url; ?>',
		data: {'order_id' : order_id, 'transaction_id' : transaction_id},
		dataType: 'json',
		beforeSend: function() {
			$('.payment-worldline .orders .btn').prop('disabled', true);
		},
		complete: function() {
			$('.payment-worldline .orders .btn').prop('disabled', false);
		},
		success: function(json) {
			$('.payment-worldline .alert-dismissible').remove();
			
			if (json['error'] && json['error']['warning']) {
				$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('.payment-worldline > .container-fluid .alert-danger').offset().top}, 'slow');
			}
			
			if (json['success']) {
				$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({ scrollTop: $('.payment-worldline > .container-fluid .alert-success').offset().top}, 'slow');
				
				$('.payment-worldline .orders').load($('.payment-worldline #form-order').attr('action') + ' #form-order');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('.payment-worldline').on('click', '#button-filter', function() {
	url = 'index.php?route=extension/payment/worldline/transaction&token=<?php echo $token; ?>';
				
	var filter_order_id = $('input[name=\'filter_order_id\']').val();
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	
	var filter_transaction_id = $('input[name=\'filter_transaction_id\']').val();
	
	if (filter_transaction_id) {
		url += '&filter_transaction_id=' + encodeURIComponent(filter_transaction_id);
	}
	
	var filter_transaction_status = $('select[name=\'filter_transaction_status\']').val();
	
	if (filter_transaction_status != '*') {
		url += '&filter_transaction_status=' + encodeURIComponent(filter_transaction_status);
	}
	
	var filter_payment_product = $('input[name=\'filter_payment_product\']').val();
	
	if (filter_payment_product) {
		url += '&filter_payment_product=' + encodeURIComponent(filter_payment_product);
	}
	
	var filter_total = $('input[name=\'filter_total\']').val();
	
	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}
	
	var filter_amount = $('input[name=\'filter_amount\']').val();
	
	if (filter_amount) {
		url += '&filter_amount=' + encodeURIComponent(filter_amount);
	}
	
	var filter_currency_code = $('select[name=\'filter_currency_code\']').val();
	
	if (filter_currency_code != '*') {
		url += '&filter_currency_code=' + encodeURIComponent(filter_currency_code);
	}
	
	var filter_date_from = $('input[name=\'filter_date_from\']').val();
	
	if (filter_date_from) {
		url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
	}
	
	var filter_date_to = $('input[name=\'filter_date_to\']').val();
	
	if (filter_date_to) {
		url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
	}
		
	var filter_environment = $('select[name=\'filter_environment\']').val();
	
	if (filter_environment != '*') {
		url += '&filter_environment=' + encodeURIComponent(filter_environment);
	}
	
	location = url;
});

</script>
<?php echo $footer; ?>							