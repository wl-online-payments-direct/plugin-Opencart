<?php echo $header; ?>
<style type="text/css">

.worldline-spinner {
	position: relative;
	min-height: 100px;
}
.worldline-spinner:before {
	content: '';
	position: absolute;
	display: block;
	width: 50px;
	height: 50px;
	top: 50%;
	left: 50%;
	margin-top: -25px;
	margin-left: -25px;
	border: 2.5px solid #545454;
	border-right-color: #545454;
	border-right-color: transparent;
	border-radius: 50%;
	-webkit-animation: worldline-spinner .75s linear infinite;
	animation: worldline-spinner .75s linear infinite;
	z-index: 1000;
}
@keyframes worldline-spinner {
	to {
		transform: rotate(360deg); 
	}
}

</style>
<div id="payment-worldline" class="container">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<h1><?php echo $text_title; ?></h1>
			<?php echo $text_message; ?>
			<div class="worldline-spinner"></div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php if ($order_id) { ?>
<script type="text/javascript">

function getWorldlinePaymentInfo() {
	setTimeout(function() {
		$.ajax({
			method: 'post',
			url: 'index.php?route=extension/payment/worldline/getPaymentInfo',
			data: {'order_id' : '<?php echo $order_id; ?>'},
			dataType: 'json',
			success: function(json) {			
				if (json['redirect']) {
					location = json['redirect'];
				}
				
				if (json['error'] && json['error']['warning']) {
					$('#payment-worldline').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i><button type="button" class="close data-dismiss="alert">&times;</button> ' + json['error']['warning'] + '</div>');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}, 10000);
}

window.addEventListener('load', function () {
	getWorldlinePaymentInfo();
});
		
</script>
<?php } ?>
<?php echo $footer; ?>