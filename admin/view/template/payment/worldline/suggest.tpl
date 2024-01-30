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
					<li class="nav-tab"><a href="<?php echo $href_advanced; ?>" class="tab"><i class="fa fa-cogs"></i> <?php echo $text_tab_advanced; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_order_status; ?>" class="tab"><i class="fa fa-shopping-cart"></i> <?php echo $text_tab_order_status; ?></a></li>
					<li class="nav-tab"><a href="<?php echo $href_transaction; ?>" class="tab"><i class="fa fa-money"></i> <?php echo $text_tab_transaction; ?></a></li>
					<li class="nav-tab active"><a href="<?php echo $href_suggest; ?>" class="tab"><i class="fa fa-envelope-o"></i> <?php echo $text_tab_suggest; ?></a></li>
				</ul>
				<div class="tab-content">
					<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-payment" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $entry_merchant_id; ?></span></label>
							<label class="col-sm-10 control-label"><div class="text-left"><?php echo $setting['account']['merchant_id'][$setting['account']['environment']]; ?></div></label>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-suggest-company-name"><span data-toggle="tooltip" title="<?php echo $help_company_name; ?>"><?php echo $entry_company_name; ?></span></label>
							<div class="col-sm-10">
								<input type="text" name="worldline_setting[suggest][company_name]" value="<?php echo $setting['suggest']['company_name']; ?>" placeholder="<?php echo $entry_company_name; ?>" id="input-suggest-company-name" class="form-control" />
							</div>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-suggest-message"><span data-toggle="tooltip" title="<?php echo $help_message; ?>"><?php echo $entry_message; ?></span></label>
							<div class="col-sm-10">
								<textarea name="worldline_setting[suggest][message]" placeholder="<?php echo $entry_message; ?>" id="input-suggest-message" class="form-control"><?php echo $setting['suggest']['message']; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="button" class="btn btn-primary button-send-suggest"><?php echo $button_send_suggest; ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$('.payment-worldline').on('click', '.button-send-suggest', function() {
	$.ajax({
		type: 'post',
		url: '<?php echo $suggest_url; ?>',
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