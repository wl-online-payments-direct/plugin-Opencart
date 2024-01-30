<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="payment-worldline">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $sign_up; ?>" target="_blank" class="btn btn-primary"><?php echo $button_sign_up; ?></a>
				<a href="<?php echo $contact_us; ?>" target="_blank" class="btn btn-primary"><?php echo $button_contact_us; ?></a>
				<button type="button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary button-save"><i class="fa fa-save"></i></button>
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
					<li class="nav-tab active"><a href="<?php echo $href_account; ?>" class="tab"><i class="fa fa-user"></i> <?php echo $text_tab_account; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_advanced; ?>" class="tab"><i class="fa fa-cogs"></i> <?php echo $text_tab_advanced; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_order_status; ?>" class="tab"><i class="fa fa-shopping-cart"></i> <?php echo $text_tab_order_status; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_transaction; ?>" class="tab"><i class="fa fa-money"></i> <?php echo $text_tab_transaction; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_suggest; ?>" class="tab"><i class="fa fa-envelope-o"></i> <?php echo $text_tab_suggest; ?></a></li>
				</ul>
				<div class="tab-content">
					<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-payment" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="worldline_status" id="input-status" class="form-control">
									<?php if ($status) { ?>
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
									<option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-environment"><?php echo $entry_environment; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[account][environment]" id="input-environment" class="form-control">
									<?php foreach ($setting['environment'] as $environment) { ?>
									<?php if ($environment['code'] == $setting['account']['environment']) { ?>
									<option value="<?php echo $environment['code']; ?>" selected="selected"><?php echo ${$environment['name']}; ?></option>
									<?php } else { ?>
									<option value="<?php echo $environment['code']; ?>"><?php echo ${$environment['name']}; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<br />
						<p class="alert alert-info"><?php echo $text_api_credential_alert; ?></p>
						<?php foreach ($setting['environment'] as $environment) { ?>
						<div class="form-group required environment-<?php echo $environment['code']; ?> <?php if ($environment['code'] != $setting['account']['environment']) { ?>hidden<?php } ?>">
							<label class="col-sm-2 control-label" for="input-merchant-id<?php echo str_replace('_', '-', $environment['prefix']); ?>"><?php echo ${'entry_merchant_id' . $environment['prefix']}; ?></label>
							<div class="col-sm-10">
								<input type="text" name="worldline_setting[account][merchant_id][<?php echo $environment['code']; ?>]" value="<?php if (!empty($setting['account']['merchant_id'][$environment['code']])) { ?><?php echo $setting['account']['merchant_id'][$environment['code']]; ?><?php } ?>" placeholder="<?php echo ${'entry_merchant_id' . $environment['prefix']}; ?>" id="input-merchant-id<?php echo str_replace('_', '-', $environment['prefix']); ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required environment-<?php echo $environment['code']; ?> <?php if ($environment['code'] != $setting['account']['environment']) { ?>hidden<?php } ?>">
							<label class="col-sm-2 control-label" for="input-api-key<?php echo str_replace('_', '-', $environment['prefix']); ?>"><?php echo ${'entry_api_key' . $environment['prefix']}; ?></label>
							<div class="col-sm-10">
								<input type="password" name="worldline_setting[account][api_key][<?php echo $environment['code']; ?>]" value="<?php if (!empty($setting['account']['api_key'][$environment['code']])) { ?><?php echo $setting['account']['api_key'][$environment['code']]; ?><?php } ?>" placeholder="<?php echo ${'entry_api_key' . $environment['prefix']}; ?>" id="input-api-key<?php echo str_replace('_', '-', $environment['prefix']); ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required environment-<?php echo $environment['code']; ?> <?php if ($environment['code'] != $setting['account']['environment']) { ?>hidden<?php } ?>">
							<label class="col-sm-2 control-label" for="input-api-secret<?php echo str_replace('_', '-', $environment['prefix']); ?>"><?php echo ${'entry_api_secret' . $environment['prefix']}; ?></label>
							<div class="col-sm-10">
								<input type="password" name="worldline_setting[account][api_secret][<?php echo $environment['code']; ?>]" value="<?php if (!empty($setting['account']['api_secret'][$environment['code']])) { ?><?php echo $setting['account']['api_secret'][$environment['code']]; ?><?php } ?>" placeholder="<?php echo ${'entry_api_secret' . $environment['prefix']}; ?>" id="input-api-secret<?php echo str_replace('_', '-', $environment['prefix']); ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required environment-<?php echo $environment['code']; ?> <?php if ($environment['code'] != $setting['account']['environment']) { ?>hidden<?php } ?>">
							<label class="col-sm-2 control-label" for="input-api-endpoint<?php echo str_replace('_', '-', $environment['prefix']); ?>"><?php echo ${'entry_api_endpoint' . $environment['prefix']}; ?></label>
							<div class="col-sm-10">
								<div class="input-group">
									<input type="text" name="worldline_setting[account][api_endpoint][<?php echo $environment['code']; ?>]" value="<?php echo $setting['account']['api_endpoint'][$environment['code']]; ?>" id="input-api-endpoint<?php echo str_replace('_', '-', $environment['prefix']); ?>" class="form-control" />
									<span class="input-group-btn">
										<button type="button" data-toggle="tooltip" title="<?php echo $button_reset_api_endpoint; ?>" class="btn btn-default reset-api-endpoint" api_endpoint="<?php echo $setting['environment'][$environment['code']]['api_endpoint']; ?>" field_id="input-api-endpoint<?php echo str_replace('_', '-', $environment['prefix']); ?>"><i class="fa fa-undo"></i></button>
									</span>
								</div>
							</div>
						</div>
						<?php } ?>
						<br />
						<p class="alert alert-info"><?php echo $text_webhook_credential_alert; ?></p>
						<?php foreach ($setting['environment'] as $environment) { ?>
						<div class="form-group required environment-<?php echo $environment['code']; ?> <?php if ($environment['code'] != $setting['account']['environment']) { ?>hidden<?php } ?>">
							<label class="col-sm-2 control-label" for="input-webhook-key<?php echo str_replace('_', '-', $environment['prefix']); ?>"><?php echo ${'entry_webhook_key' . $environment['prefix']}; ?></label>
							<div class="col-sm-10">
								<input type="password" name="worldline_setting[account][webhook_key][<?php echo $environment['code']; ?>]" value="<?php if (!empty($setting['account']['webhook_key'][$environment['code']])) { ?><?php echo $setting['account']['webhook_key'][$environment['code']]; ?><?php } ?>" placeholder="<?php echo ${'entry_webhook_key' . $environment['prefix']}; ?>" id="input-webhook-key<?php echo str_replace('_', '-', $environment['prefix']); ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required environment-<?php echo $environment['code']; ?> <?php if ($environment['code'] != $setting['account']['environment']) { ?>hidden<?php } ?>">
							<label class="col-sm-2 control-label" for="input-webhook-secret<?php echo str_replace('_', '-', $environment['prefix']); ?>"><?php echo ${'entry_webhook_secret' . $environment['prefix']}; ?></label>
							<div class="col-sm-10">
								<input type="password" name="worldline_setting[account][webhook_secret][<?php echo $environment['code']; ?>]" value="<?php if (!empty($setting['account']['webhook_secret'][$environment['code']])) { ?><?php echo $setting['account']['webhook_secret'][$environment['code']]; ?><?php } ?>" placeholder="<?php echo ${'entry_webhook_secret' . $environment['prefix']}; ?>" id="input-webhook-secret<?php echo str_replace('_', '-', $environment['prefix']); ?>" class="form-control" />
							</div>
						</div>
						<?php } ?>
						<div class="form-group">
                            <label class="col-sm-2 control-label" for="input-webhook-url"><span data-toggle="tooltip" title="<?php echo $help_webhook_url; ?>"><?php echo $entry_webhook_url; ?></span></label>
                            <div class="col-sm-10">
                                <input type="hidden" name="worldline_setting[account][webhook_token]" value="<?php echo $setting['account']['webhook_token']; ?>" />
								<div class="input-group">
									<input type="text" value="<?php echo $webhook_url; ?>" readonly="readonly" id="input-webhook-url" class="form-control" />
									<span class="input-group-btn">
										<button type="button" data-toggle="tooltip" title="<?php echo $button_copy_url; ?>" class="btn btn-default copy-webhook-url" field_id="input-webhook-url"><i class="fa fa-clipboard"></i></button>
									</span>
								</div>
                            </div>
                        </div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-cron-url"><span data-toggle="tooltip" title="<?php echo $help_cron_url; ?>"><?php echo $entry_cron_url; ?></span></label>
							<div class="col-sm-10">
								<input type="hidden" name="worldline_setting[account][cron_token]" value="<?php echo $setting['account']['cron_token']; ?>" />
								<div class="input-group">
									<input type="text" value="<?php echo $cron_url; ?>" readonly="readonly" id="input-cron-url" class="form-control" />
									<span class="input-group-btn">
										<button type="button" data-toggle="tooltip" title="<?php echo $button_copy_url; ?>" class="btn btn-default copy-cron-url" field_id="input-cron-url"><i class="fa fa-clipboard"></i></button>
									</span>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$('#input-environment').on('change', function() {
	var environment = $(this).val();
		
	if (environment == 'live') {
		$('.environment-live').removeClass('hidden');
		$('.environment-test').addClass('hidden');
	} else {
		$('.environment-test').removeClass('hidden');
		$('.environment-live').addClass('hidden');
	}	
});

$('#form-payment').delegate('.reset-api-endpoint', 'click', function(event) {
	event.preventDefault();
	
	$('#' + $(this).attr('field_id')).val($(this).attr('api_endpoint'));
});

$('#form-payment').delegate('.copy-webhook-url', 'click', function(event) {
	event.preventDefault();
	
	navigator.clipboard.writeText($('#' + $(this).attr('field_id')).val());
});

$('#form-payment').delegate('.copy-cron-url', 'click', function(event) {
	event.preventDefault();
	
	navigator.clipboard.writeText($('#' + $(this).attr('field_id')).val());
});

$('.payment-worldline').on('click', '.button-save', function() {
    $.ajax({
		type: 'post',
		url: $('#form-payment').attr('action'),
		data: $('#form-payment').serialize(),
		dataType: 'json',
		success: function(json) {
			$('.payment-worldline .alert-dismissible, .payment-worldline .text-danger').remove();
			$('.payment-worldline .form-group').removeClass('has-error');
						
			if (json['error']) {
				if (json['error']['warning']) {
					$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					
					$('html, body').animate({scrollTop: $('.payment-worldline > .container-fluid .alert-danger').offset().top}, 'slow');
				}				
				
				for (i in json['error']) {
					var element = $('#input-' + i.replaceAll('_', '-'));

					if (element.parent().hasClass('input-group')) {
                   		$(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
					} else {
						$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
					}
				}				
				
				$('.payment-worldline .text-danger').parentsUntil('.form-group').parent().addClass('has-error');
			}
			
			if (json['success']) {
				$('.payment-worldline > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				$('html, body').animate({scrollTop: $('.payment-worldline > .container-fluid .alert-success').offset().top}, 'slow');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
    });  
});

</script>
<?php echo $footer; ?>