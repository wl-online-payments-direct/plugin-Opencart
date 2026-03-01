<div id="worldline-modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fal fa-close"></i></button>
				<h4 class="modal-title"><?php echo $text_worldline_title; ?></h4>
			</div>
			<div class="modal-body">
				<div id="worldline-hosted-tokenization-form">
					<?php if (!empty($error['warning'])) { ?>
					<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error['warning']; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div>
					<?php } ?>
					<?php if ($card_customer_tokens) { ?>
					<div id="worldline-card-tokens" class="worldline-card-tokens">
						<div id="worldline-card-tokens-container" class="worldline-card-tokens-container">
							<div class="worldline-card-token">
								<div type="button" class="btn card-token-button card-new-token-button selected"><i class="card-icon"></i><?php echo $text_card_new; ?></div>
							</div>
							<?php foreach ($card_customer_tokens as $card_customer_token) { ?>
							<div class="worldline-card-token">
								<div type="button" class="btn card-token-button" token="<?php echo $card_customer_token['token']; ?>"><i class="card-icon card-icon-<?php $card_brand = explode(' ', strtolower($card_customer_token['card_brand'])); echo reset($card_brand); ?>"></i><span class="card-number"><?php echo $card_customer_token['card_number']; ?></span></div><div type="button" class="btn card-token-delete-button" token="<?php echo $card_customer_token['token']; ?>"><i class="fa fal fa-close"></i></div>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
					<div id="worldline-card-form" hosted-tokenization-url="<?php echo $hosted_tokenization_url; ?>" tokens="<?php echo end($tokens); ?>"></div>
					<?php if ($logged && !$forced_tokenization) { ?>
					<div class="checkbox checkbox-save">
						<label>
							<input type="checkbox" name="worldline_card_token_save" id="worldline-card-token-save" value="1" checked /> <?php echo $entry_card_token_save; ?>
						</label>
					</div>
					<?php } ?>
					<button type="button" id="worldline-card-button" class="worldline-button btn btn-primary btn-block" disabled><?php echo $button_pay; ?></button>
				</div>
			</div>
		</div>
	</div>
</div>