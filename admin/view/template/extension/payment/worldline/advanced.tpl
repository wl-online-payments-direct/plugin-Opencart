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
					<li class="nav-tab"><a href="<?php echo $href_account; ?>" class="tab"><i class="fa fa-user"></i> <?php echo $text_tab_account; ?></a></li>
					<li class="nav-tab active"><a href="<?php echo $href_advanced; ?>" class="tab"><i class="fa fa-cogs"></i> <?php echo $text_tab_advanced; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_order_status; ?>" class="tab"><i class="fa fa-shopping-cart"></i> <?php echo $text_tab_order_status; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_transaction; ?>" class="tab"><i class="fa fa-money"></i> <?php echo $text_tab_transaction; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_suggest; ?>" class="tab"><i class="fa fa-envelope-o"></i> <?php echo $text_tab_suggest; ?></a></li>
				</ul>
				<div class="tab-content">
					<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-payment" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
								<div class="input-group">
									<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
									<input type="text" name="worldline_setting[advanced][title][<?php echo $language['language_id']; ?>]" value="<?php if (!empty($setting['advanced']['title'][$language['language_id']])) { ?><?php echo $setting['advanced']['title'][$language['language_id']]; ?><?php } ?>" placeholder="<?php echo $entry_title; ?>" id="input-title-<?php echo $language['language_id']; ?>" class="form-control" />
								</div>
								<?php } ?>
							</div>
                        </div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-button-title"><?php echo $entry_button_title; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
								<div class="input-group">
									<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
									<input type="text" name="worldline_setting[advanced][button_title][<?php echo $language['language_id']; ?>]" value="<?php if (!empty($setting['advanced']['button_title'][$language['language_id']])) { ?><?php echo $setting['advanced']['button_title'][$language['language_id']]; ?><?php } ?>" placeholder="<?php echo $entry_button_title; ?>" id="input-button-title-<?php echo $language['language_id']; ?>" class="form-control" />
								</div>
								<?php } ?>
							</div>
                        </div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-authorization-mode"><?php echo $entry_authorization_mode; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[advanced][authorization_mode]" id="input-authorization-mode" class="form-control">
								<?php foreach ($setting['authorization_mode'] as $authorization_mode) { ?>
								<?php if ($authorization_mode['code'] == $setting['advanced']['authorization_mode']) { ?>
								<option value="<?php echo $authorization_mode['code']; ?>" selected="selected"><?php echo ${$authorization_mode['name']}; ?></option>
								<?php } else { ?>
								<option value="<?php echo $authorization_mode['code']; ?>"><?php echo  ${$authorization_mode['name']}; ?></option>
								<?php } ?>
								<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-group-cards"><?php echo $entry_group_cards; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[advanced][group_cards]" id="input-group-cards" class="form-control">
									<?php if ($setting['advanced']['group_cards']) { ?>
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
							<label class="col-sm-2 control-label" for="input-forced-tokenization"><?php echo $entry_forced_tokenization; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[advanced][forced_tokenization]" id="input-forced-tokenization" class="form-control">
									<?php if ($setting['advanced']['forced_tokenization']) { ?>
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
							<label class="col-sm-2 control-label" for="input-tds-status"><?php echo $entry_tds_status; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[advanced][tds_status]" id="input-tds-status" class="form-control">
									<?php if ($setting['advanced']['tds_status']) { ?>
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
							<label class="col-sm-2 control-label" for="input-tds-challenge-indicator"><?php echo $entry_tds_challenge_indicator; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[advanced][tds_challenge_indicator]" id="input-tds-challenge-indicator" class="form-control">
									<?php foreach ($setting['tds_challenge_indicator'] as $tds_challenge_indicator) { ?>
									<?php if ($tds_challenge_indicator['code'] == $setting['advanced']['tds_challenge_indicator']) { ?>
									<option value="<?php echo $tds_challenge_indicator['code']; ?>" selected="selected"><?php echo ${$tds_challenge_indicator['name']}; ?></option>
									<?php } else { ?>
									<option value="<?php echo $tds_challenge_indicator['code']; ?>"><?php echo ${$tds_challenge_indicator['name']}; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-tds-exemption-request"><?php echo $entry_tds_exemption_request; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[advanced][tds_exemption_request]" id="input-tds-exemption-request" class="form-control">
									<?php foreach ($setting['tds_exemption_request'] as $tds_exemption_request) { ?>
									<?php if ($tds_exemption_request['code'] == $setting['advanced']['tds_exemption_request']) { ?>
									<option value="<?php echo $tds_exemption_request['code']; ?>" selected="selected"><?php echo ${$tds_exemption_request['name']}; ?></option>
									<?php } else { ?>
									<option value="<?php echo $tds_exemption_request['code']; ?>"><?php echo ${$tds_exemption_request['name']}; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-template"><span data-toggle="tooltip" title="<?php echo $help_template; ?>"><?php echo $entry_template; ?></span></label>
							<div class="col-sm-10">
								<input type="text" name="worldline_setting[advanced][template]" value="<?php echo $setting['advanced']['template']; ?>" placeholder="<?php echo $entry_template; ?>" id="input-template" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-advanced-debug"><?php echo $entry_debug; ?></label>
							<div class="col-sm-10">
								<select name="worldline_setting[advanced][debug]" id="input-advanced-debug" class="form-control">
									<?php if ($setting['advanced']['debug']) { ?>
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
							<label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
							<div class="col-sm-10">
								<input type="text" name="worldline_total" value="<?php echo $total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
							<div class="col-sm-10">
								<select name="worldline_geo_zone_id" id="input-geo-zone" class="form-control">
									<option value="0"><?php echo $text_all_zones; ?></option>
									<?php foreach ($geo_zones as $geo_zone) { ?>
									<?php if ($geo_zone['geo_zone_id'] == $geo_zone_id) { ?>
									<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
							<div class="col-sm-10">
								<input type="text" name="worldline_sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

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