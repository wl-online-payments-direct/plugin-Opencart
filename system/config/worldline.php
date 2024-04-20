<?php 
$_['worldline_setting'] = array(
	'extension' => array(
		'extension_id' => 'WLOP-opencart',
		'name' => 'Direct Opencart Plugin',
		'version' => '1.0.1',
		'creator' => 'Dreamvention',
		'integrator' => 'OnlinePayments'
	),
	'account' => array(
		'environment' => 'live',
		'merchant_id' => array(),
		'api_key' => array(),
		'api_secret' => array(),
		'api_endpoint' => array(
			'live' => 'https://payment.direct.worldline-solutions.com',
			'test' => 'https://payment.preprod.direct.worldline-solutions.com'
		),
		'webhook_key' => array(),
		'webhook_secret' => array(),
		'webhook_token' => '',
		'cron_token' => ''
	),
	'advanced' => array(
		'title' => array(),
		'button_title' => array(),
		'debug' => false,
		'authorization_mode' => 'sale',
		'group_cards' => true,
		'forced_tokenization' => true,
		'tds_status' => true,
		'tds_challenge_indicator' => 'challenge-required',
		'tds_exemption_request' => 'low-value',
		'template' => ''
	),
	'order_status' => array(
		'created' => array(
			'code' => 'created',
			'name' => 'text_created_status',
			'id' => 1
		),
		'cancelled' => array(
			'code' => 'cancelled',
			'name' => 'text_cancelled_status',
			'id' => 7
		),
		'rejected' => array(
			'code' => 'rejected',
			'name' => 'text_rejected_status',
			'id' => 8
		),
		'pending' =>  array(
			'code' => 'pending',
			'name' => 'text_pending_status',
			'id' => 1
		),
		'captured' => array(
			'code' => 'captured',
			'name' => 'text_captured_status',
			'id' => 5
		),
		'refunded' => array(
			'code' => 'refunded',
			'name' => 'text_refunded_status',
			'id' => 11
		)
	),
	'suggest' => array(
		'company_name' => '',
		'message' => ''
	),
	'environment' => array(
		'live' => array(
			'code' => 'live',
			'name' => 'text_live',
			'prefix' => '',
			'api_endpoint' => 'https://payment.direct.worldline-solutions.com'
		),
		'test' => array(
			'code' => 'test',
			'name' => 'text_test',
			'prefix' => '_test',
			'api_endpoint' => 'https://payment.preprod.direct.worldline-solutions.com'
		)
	),
	'authorization_mode' => array(
		'pre_authorization' => array(
			'code' => 'pre_authorization',
			'name' => 'text_pre_authorization'
		),
		'final_authorization' => array(
			'code' => 'final_authorization',
			'name' => 'text_final_authorization'
		),
		'sale' => array(
			'code' => 'sale',
			'name' => 'text_sale'
		)
	),
	'tds_challenge_indicator' => array(
		'no-preference' => array(
			'code' => 'no-preference',
			'name' => 'text_no_preference'
		),
		'no-challenge-requested' => array(
			'code' => 'no-challenge-requested',
			'name' => 'text_no_challenge_requested'
		),
		'challenge-requested' => array(
			'code' => 'challenge-requested',
			'name' => 'text_challenge_requested'
		),
		'challenge-required' => array(
			'code' => 'challenge-required',
			'name' => 'text_challenge_required'
		)
	),
	'tds_exemption_request' => array(
		'none' => array(
			'code' => 'none',
			'name' => 'text_exemption_none'
		),
		'transaction-risk-analysis' => array(
			'code' => 'transaction-risk-analysis',
			'name' => 'text_exemption_transaction_risk_analysis'
		),
		'low-value' => array(
			'code' => 'low-value',
			'name' => 'text_exemption_low_value'
		),
		'whitelist' => array(
			'code' => 'whitelist',
			'name' => 'text_exemption_whitelist'
		)
	),
	'transaction_status' => array(
		'created' => array(
			'code' => 'created',
			'name' => 'text_created'
		),
		'cancelled' => array(
			'code' => 'cancelled',
			'name' => 'text_cancelled'
		),
		'rejected' => array(
			'code' => 'rejected',
			'name' => 'text_rejected'
		),
		'rejected_capture' => array(
			'code' => 'rejected_capture',
			'name' => 'text_rejected_capture'
		),
		'pending_capture' => array(
			'code' => 'pending_capture',
			'name' => 'text_pending_capture'
		),
		'captured' => array(
			'code' => 'captured',
			'name' => 'text_captured'
		),
		'refunded' => array(
			'code' => 'refunded',
			'name' => 'text_refunded'
		),
		'authorization_requested' => array(
			'code' => 'authorization_requested',
			'name' => 'text_authorization_requested'
		),
		'capture_requested' => array(
			'code' => 'capture_requested',
			'name' => 'text_capture_requested'
		),
		'refund_requested' => array(
			'code' => 'refund_requested',
			'name' => 'text_refund_requested'
		)
	)
);
?>