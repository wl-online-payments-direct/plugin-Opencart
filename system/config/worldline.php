<?php 
$_['worldline_setting'] = array(
	'extension' => array(
		'extension_id' => 'WLOP-opencart',
		'name' => 'Direct Opencart Plugin',
		'version' => '2.0.0',
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
		'debug' => false,
		'authorization_mode' => 'sale',
		'capture_installation' => 'manually',
		'forced_tokenization' => true,
		'surcharging_status' => false,
		'tds_status' => true,
		'tds_challenge_indicator' => 'challenge-required',
		'tds_exemption_request' => 'low-value',
	),
	'hosted_checkout' => array(
		'status' => true,
		'title' => array(),
		'button_title' => array(),
		'group_cards' => true,
		'template' => '',
		'wero_capture_trigger' => 'shipping'
	),
	'hosted_tokenization' => array(
		'status' => true,
		'title' => array(),
		'button_title' => array(),
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
	'final_order_status' => array(),
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
	'capture_installation' => array(
		'manually' => array(
			'code' => 'manually',
			'name' => 'text_manually',
			'days' => 0
		),
		'next_day' => array(
			'code' => 'next_day',
			'name' => 'text_next_day',
			'days' => 1
		),
		'after_two_day' => array(
			'code' => 'after_one_day',
			'name' => 'text_after_one_day',
			'days' => 2
		),
		'after_two_days' => array(
			'code' => 'after_two_days',
			'name' => 'text_after_two_days',
			'days' => 3
		),
		'after_three_days' => array(
			'code' => 'after_three_days',
			'name' => 'text_after_three_days',
			'days' => 4
		),
		'after_four_days' => array(
			'code' => 'after_four_days',
			'name' => 'text_after_four_days',
			'days' => 5
		),
		'after_five_days' => array(
			'code' => 'after_five_days',
			'name' => 'text_after_five_days',
			'days' => 6
		),
		'after_six_days' => array(
			'code' => 'after_six_days',
			'name' => 'text_after_six_days',
			'days' => 7
		),
		'after_seven_days' => array(
			'code' => 'after_seven_days',
			'name' => 'text_after_seven_days',
			'days' => 8
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
	'wero_capture_trigger' => array(
		'shipping' => array(
			'code' => 'shipping',
			'name' => 'text_capture_trigger_shipping'
		),
		'delivery' => array(
			'code' => 'delivery',
			'name' => 'text_capture_trigger_delivery'
		),
		'availability' => array(
			'code' => 'availability',
			'name' => 'text_capture_trigger_availability'
		),
		'service_fulfilment' => array(
			'code' => 'serviceFulfilment',
			'name' => 'text_capture_trigger_service_fulfilment'
		),		
		'other' => array(
			'code' => 'other',
			'name' => 'text_capture_trigger_other'
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