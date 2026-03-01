<?php 
$_['worldline_setting'] = [
	'extension' => [
		'extension_id' => 'WLOP-opencart',
		'name' => 'Direct Opencart Plugin',
		'version' => '2.0.0',
		'creator' => 'Dreamvention',
		'integrator' => 'OnlinePayments'
	],
	'account' => [
		'environment' => 'live',
		'merchant_id' => [],
		'api_key' => [],
		'api_secret' => [],
		'api_endpoint' => [
			'live' => 'https://payment.direct.worldline-solutions.com',
			'test' => 'https://payment.preprod.direct.worldline-solutions.com'
		],
		'webhook_key' => [],
		'webhook_secret' => [],
		'webhook_token' => '',
		'cron_token' => ''
	],
	'advanced' => [
		'debug' => false,
		'authorization_mode' => 'sale',
		'capture_installation' => 'manually',
		'forced_tokenization' => true,
		'surcharging_status' => false,
		'tds_status' => true,
		'tds_challenge_indicator' => 'challenge-required',
		'tds_exemption_request' => 'low-value'
	],
	'hosted_checkout' => [
		'status' => true,
		'title' => [],
		'button_title' => [],
		'group_cards' => true,
		'template' => '',
		'wero_capture_trigger' => 'shipping'
	],
	'hosted_tokenization' => [
		'status' => true,
		'title' => [],
		'button_title' => [],
		'template' => ''
	],
	'order_status' => [
		'created' => [
			'code' => 'created',
			'name' => 'text_created_status',
			'id' => 1
		],
		'cancelled' => [
			'code' => 'cancelled',
			'name' => 'text_cancelled_status',
			'id' => 7
		],
		'rejected' => [
			'code' => 'rejected',
			'name' => 'text_rejected_status',
			'id' => 8
		],
		'pending' =>  [
			'code' => 'pending',
			'name' => 'text_pending_status',
			'id' => 1
		],
		'captured' => [
			'code' => 'captured',
			'name' => 'text_captured_status',
			'id' => 5
		],
		'refunded' => [
			'code' => 'refunded',
			'name' => 'text_refunded_status',
			'id' => 11
		]
	],
	'final_order_status' => [],
	'suggest' => [
		'company_name' => '',
		'message' => ''
	],
	'environment' => [
		'live' => [
			'code' => 'live',
			'name' => 'text_live',
			'prefix' => '',
			'api_endpoint' => 'https://payment.direct.worldline-solutions.com'
		],
		'test' => [
			'code' => 'test',
			'name' => 'text_test',
			'prefix' => '_test',
			'api_endpoint' => 'https://payment.preprod.direct.worldline-solutions.com'
		]
	],
	'authorization_mode' => [
		'pre_authorization' => [
			'code' => 'pre_authorization',
			'name' => 'text_pre_authorization'
		],
		'final_authorization' => [
			'code' => 'final_authorization',
			'name' => 'text_final_authorization'
		],
		'sale' => [
			'code' => 'sale',
			'name' => 'text_sale'
		]
	],
	'capture_installation' => [
		'manually' => [
			'code' => 'manually',
			'name' => 'text_manually',
			'days' => 0
		],
		'next_day' => [
			'code' => 'next_day',
			'name' => 'text_next_day',
			'days' => 1
		],
		'after_two_day' => [
			'code' => 'after_one_day',
			'name' => 'text_after_one_day',
			'days' => 2
		],
		'after_two_days' => [
			'code' => 'after_two_days',
			'name' => 'text_after_two_days',
			'days' => 3
		],
		'after_three_days' => [
			'code' => 'after_three_days',
			'name' => 'text_after_three_days',
			'days' => 4
		],
		'after_four_days' => [
			'code' => 'after_four_days',
			'name' => 'text_after_four_days',
			'days' => 5
		],
		'after_five_days' => [
			'code' => 'after_five_days',
			'name' => 'text_after_five_days',
			'days' => 6
		],
		'after_six_days' => [
			'code' => 'after_six_days',
			'name' => 'text_after_six_days',
			'days' => 7
		],
		'after_seven_days' => [
			'code' => 'after_seven_days',
			'name' => 'text_after_seven_days',
			'days' => 8
		]
	],
	'tds_challenge_indicator' => [
		'no-preference' => [
			'code' => 'no-preference',
			'name' => 'text_no_preference'
		],
		'no-challenge-requested' => [
			'code' => 'no-challenge-requested',
			'name' => 'text_no_challenge_requested'
		],
		'challenge-requested' => [
			'code' => 'challenge-requested',
			'name' => 'text_challenge_requested'
		],
		'challenge-required' => [
			'code' => 'challenge-required',
			'name' => 'text_challenge_required'
		]
	],
	'tds_exemption_request' => [
		'none' => [
			'code' => 'none',
			'name' => 'text_exemption_none'
		],
		'transaction-risk-analysis' => [
			'code' => 'transaction-risk-analysis',
			'name' => 'text_exemption_transaction_risk_analysis'
		],
		'low-value' => [
			'code' => 'low-value',
			'name' => 'text_exemption_low_value'
		],
		'whitelist' => [
			'code' => 'whitelist',
			'name' => 'text_exemption_whitelist'
		]
	],
	'wero_capture_trigger' => [
		'shipping' => [
			'code' => 'shipping',
			'name' => 'text_capture_trigger_shipping'
		],
		'delivery' => [
			'code' => 'delivery',
			'name' => 'text_capture_trigger_delivery'
		],
		'availability' => [
			'code' => 'availability',
			'name' => 'text_capture_trigger_availability'
		],
		'service_fulfilment' => [
			'code' => 'serviceFulfilment',
			'name' => 'text_capture_trigger_service_fulfilment'
		],		
		'other' => [
			'code' => 'other',
			'name' => 'text_capture_trigger_other'
		]
	],
	'transaction_status' => [
		'created' => [
			'code' => 'created',
			'name' => 'text_created'
		],
		'cancelled' => [
			'code' => 'cancelled',
			'name' => 'text_cancelled'
		],
		'rejected' => [
			'code' => 'rejected',
			'name' => 'text_rejected'
		],
		'rejected_capture' => [
			'code' => 'rejected_capture',
			'name' => 'text_rejected_capture'
		],
		'pending_capture' => [
			'code' => 'pending_capture',
			'name' => 'text_pending_capture'
		],
		'captured' => [
			'code' => 'captured',
			'name' => 'text_captured'
		],
		'refunded' => [
			'code' => 'refunded',
			'name' => 'text_refunded'
		],
		'authorization_requested' => [
			'code' => 'authorization_requested',
			'name' => 'text_authorization_requested'
		],
		'capture_requested' => [
			'code' => 'capture_requested',
			'name' => 'text_capture_requested'
		],
		'refund_requested' => [
			'code' => 'refund_requested',
			'name' => 'text_refund_requested'
		]
	]
];
?>