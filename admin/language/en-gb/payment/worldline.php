<?php
// Heading
$_['heading_title']		 						= 'Worldline';

// Text
$_['text_extensions']   						= 'Extensions';
$_['text_edit']        							= 'Edit Worldline';
$_['text_version']								= 'New Worldline version available. You can download it <a href="%s" target="_blank" class="alert-link">here</a>!';
$_['text_tab_account']				 			= 'Account Settings';
$_['text_tab_advanced']				 			= 'Advanced Settings';
$_['text_tab_order_status']						= 'Order Status';
$_['text_tab_transaction']						= 'Transactions';
$_['text_tab_suggest']							= 'Suggest new feature';
$_['text_live']   								= 'Live';
$_['text_test']   								= 'Test';
$_['text_api_credential_alert']					= 'To retrieve the API Key and API Secret in your PSPID, follow these steps:<br />1) Login to the Merhcant Portal<br />2) Go to Developer> Payment API > Show API Key<br />3) If you have not configured anything yet, create both API Key and API Secret click on "Add API Key". Please keep a note of API secret since, it will not be visible after save.';
$_['text_webhook_credential_alert']				= 'To retrieve the webhooks credentials, follow these steps:<br />1) Login to the Merchant Portal<br />2) Go to Developer> Webhooks > Show Webhooks Key.<br />3) Click on "Generate Webhook Keys" if no keys are present.<br />4) Copy & Paste the WebhooksKeySecret immediately since it will be not be visible after save.<br />5) In "Endpoints URLs", paste the Webhook URL of your store using "Add webhook endpoint"<br />5) Click on "SAVE" to confirm your settings';
$_['text_pre_authorization']   					= 'Pre Authorization';
$_['text_final_authorization']   				= 'Final Authorization';
$_['text_sale']   								= 'Sale';
$_['text_no_preference']						= 'No Preference';
$_['text_no_challenge_requested']				= 'No Challenge Requested';
$_['text_challenge_requested']					= 'Challenge Requested';
$_['text_challenge_required']					= 'Challenge Required';
$_['text_exemption_none']						= 'No exemption flagging';
$_['text_exemption_transaction_risk_analysis']	= 'Transaction is of low risk';
$_['text_exemption_low_value']					= 'The value of the transaction is below 30 EUR';
$_['text_exemption_whitelist']					= 'Whitelisted by the customer';
$_['text_created_status']						= 'Created Status';
$_['text_cancelled_status']						= 'Cancelled Status';
$_['text_rejected_status']						= 'Rejected Status';
$_['text_pending_status']						= 'Pending Status';
$_['text_captured_status']						= 'Captured Status';
$_['text_refunded_status']						= 'Refunded Status';
$_['text_created']								= 'Created';
$_['text_cancelled']							= 'Cancelled';
$_['text_rejected']								= 'Rejected';
$_['text_rejected_capture']						= 'Rejected Capture';
$_['text_pending_capture']						= 'Pending Capture';
$_['text_captured']								= 'Captured';
$_['text_refunded']								= 'Refunded';
$_['text_authorization_requested']				= 'Authorization Requested';
$_['text_capture_requested']					= 'Capture Requested';
$_['text_refund_requested']						= 'Refund Requested';
$_['text_suggest_subject']						= 'Suggest a new feature';
$_['text_suggest_version']						= 'OpenCart %s. Worldline %s.';
$_['text_transaction_created']					= 'The transaction has been created and is still in a pending status.';
$_['text_transaction_cancelled']				= 'The transaction/authorisation has been cancelled by either your customer or yourself.';
$_['text_transaction_rejected']					= 'The authorisation/refund request has been rejected by the acquirer.';
$_['text_transaction_rejected_capture']			= 'The capture request has been rejected by the acquirer.';
$_['text_transaction_pending_capture']			= 'The authorisation request was successful, but you still need to capture it to receive the funds.';
$_['text_transaction_captured']					= 'The capture request has been successful. You can expect to receive the funds for this transaction from your acquirer.';
$_['text_transaction_refunded']					= 'The refund request has been successful. Your customer can expect to receive the funds for this transaction from her/his issuers.';
$_['text_transaction_authorization_requested']	= 'Worldline is processing the authorisation request and waiting for the result.';
$_['text_transaction_refund_requested']			= 'Worldline is processing the refund request and waiting for the result.';
$_['text_transaction_capture_requested']		= 'Worldline is processing the capture request and waiting for the result.';
$_['text_payment_information']					= 'Payment Information';
$_['text_transaction_id']						= 'Transaction ID';
$_['text_transaction_status']					= 'Transaction Status';
$_['text_transaction_description']				= 'Transaction Description';
$_['text_payment_product']						= 'Payment Method';
$_['text_total']								= 'Total';
$_['text_amount']								= 'Amount Paid';
$_['text_currency_code']						= 'Currency';
$_['text_date']									= 'Date';
$_['text_environment']							= 'Environment';
$_['text_card_bin']								= 'Card BIN';
$_['text_card_number']							= 'Card Number';
$_['text_transaction_action']					= 'Action';
$_['text_fraud_information']					= 'Fraud Information';
$_['text_fraud_result']							= 'Fraud Result';
$_['text_liability']							= 'Liability for 3DS';
$_['text_exemption']							= 'Exemption';
$_['text_authentication_status']				= 'Authentication Status';

// Entry
$_['entry_environment']							= 'Environment';
$_['entry_merchant_id']							= 'Merchant ID (PSPID)';
$_['entry_api_key']								= 'API Key';
$_['entry_api_secret']							= 'API Secret';
$_['entry_api_endpoint']						= 'API Endpoint';
$_['entry_webhook_key']							= 'Webhook Key';
$_['entry_webhook_secret']						= 'Webhook Secret';
$_['entry_merchant_id_test']					= 'Test Merchant ID (PSPID)';
$_['entry_api_key_test']						= 'Test API Key';
$_['entry_api_secret_test']						= 'Test API Secret';
$_['entry_api_endpoint_test']					= 'Test API Endpoint';
$_['entry_webhook_key_test']					= 'Test Webhook Key';
$_['entry_webhook_secret_test']					= 'Test Webhook Secret';
$_['entry_webhook_url']   						= 'Webhook URL';
$_['entry_cron_url']	  						= 'Cron URL';
$_['entry_status']       						= 'Status';
$_['entry_title']								= 'Payment Title';
$_['entry_button_title']						= 'Payment Button Title';
$_['entry_authorization_mode']					= 'Authorization Mode';
$_['entry_group_cards']							= 'Group Cards';
$_['entry_forced_tokenization']					= 'Forced Tokenization';
$_['entry_tds_status']							= '3DS Status';
$_['entry_tds_challenge_indicator']				= '3DS Challenge Indicator';
$_['entry_tds_exemption_request']				= '3DS Exemption Request';
$_['entry_template']							= 'Template File Name';
$_['entry_debug']				 				= 'Debug Logging';
$_['entry_total']		 						= 'Total';
$_['entry_geo_zone']     						= 'Geo Zone';
$_['entry_sort_order']   						= 'Sort Order';
$_['entry_final_order_status']					= 'Final Order Status';
$_['entry_order_id']							= 'Order ID';
$_['entry_transaction_id']						= 'Transaction ID';
$_['entry_transaction_status']					= 'Transaction Status';
$_['entry_payment_product']						= 'Payment Method';
$_['entry_amount']								= 'Amount Paid';
$_['entry_currency']							= 'Currency';
$_['entry_date_from']							= 'Date From';
$_['entry_date_to']								= 'Date To';
$_['entry_company_name']   						= 'Company Name';
$_['entry_message']   							= 'Message';

// Help
$_['help_webhook_url']		  					= 'Set webhooks in Worldline Back Office to call this URL.';
$_['help_cron_url']		  						= 'Set a cron to call this URL.';
$_['help_template']								= 'If you are using a customized template, please enter the name here. If empty, the standard payment page will be displayed. Payment page look and feel can be customized on Worldline Back Office.';
$_['help_total']         						= 'The checkout total the order must reach before this payment method becomes active.';
$_['help_final_order_status']					= 'Set the order status to final and Worldline will not be able to change it.';
$_['help_company_name']          				= 'Your company name.';
$_['help_message']          					= 'Please explain how our payment plugin can be further improved.';

// Column
$_['column_order_id']							= 'Order ID';
$_['column_transaction_id']						= 'Transaction ID';
$_['column_transaction_status']					= 'Transaction Status';
$_['column_payment_product']					= 'Payment Method';
$_['column_total']								= 'Total';
$_['column_amount']								= 'Amount Paid';
$_['column_currency_code']						= 'Currency';
$_['column_date']								= 'Date';
$_['column_environment']						= 'Environment';
$_['column_action']								= 'Action';

// Button
$_['button_save']  								= 'Save';
$_['button_sign_up']							= 'Create Account / Sign Up';
$_['button_contact_us']							= 'Contact Us';
$_['button_view']								= 'View';
$_['button_reset_api_endpoint']					= 'Reset API Endpoint';
$_['button_copy_url']							= 'Copy URL to clipboard';
$_['button_filter']								= 'Filter';
$_['button_send_suggest']						= 'Send';
$_['button_capture']							= 'Capture';
$_['button_cancel']								= 'Cancel';
$_['button_refund']								= 'Refund';
$_['button_title']								= 'Pay with Worldline';

// Success
$_['success_save']								= 'Success: You have modified Worldline!';
$_['success_send_suggest']						= 'Success: Your information have been successfully sent to Worldline!';
$_['success_capture']							= 'The transaction has been captured.';
$_['success_cancel']							= 'The transaction/authorisation has been cancelled.';
$_['success_refund']							= 'The transaction has been refunded.';

// Error
$_['error_warning']          					= 'Warning: Please check the form carefully for errors!';
$_['error_permission'] 							= 'Warning: You do not have permission to modify payment Worldline!';
$_['error_merchant_id']          				= 'Merchant ID is incorrect!';
$_['error_api_key']          					= 'API Key is incorrect!';
$_['error_api_secret']          				= 'API Secret is incorrect!';
$_['error_webhook_key']          				= 'Webhook Key is incorrect!';
$_['error_webhook_secret']          			= 'Webhook Secret is incorrect!';
$_['error_merchant_id_test']          			= 'Test Merchant ID is incorrect!';
$_['error_api_key_test']          				= 'Test API Key is incorrect!';
$_['error_api_secret_test']          			= 'Test API Secret is incorrect!';
$_['error_webhook_key_test']          			= 'Test Webhook Key is incorrect!';
$_['error_webhook_secret_test']          		= 'Test Webhook Secret is incorrect!';
$_['error_template']          					= 'Template File Name is incorrect! It must have the extension .htm, .html or .dhtml!';
$_['error_company_name']          				= 'Company Name must be between 3 and 32 characters!';
$_['error_message']          					= 'Message must be between 20 and 1000 characters!';