<?php
class ControllerPaymentWorldline extends Controller {
	private $error = array();
							
	public function index() {
		$_config = new Config();
		$_config->load('worldline');
		
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));

		$environment = $setting['account']['environment'];
		
		if ($setting['account']['api_key'][$environment] && $setting['account']['api_secret'][$environment] && !$this->callback() && !$this->webhook() && !$this->cron()) {
			$this->load->language('payment/worldline');

			$data['text_loading'] = $this->language->get('text_loading');
						
			$data['button_confirm'] = $this->language->get('button_confirm');			

			$language_id = $this->config->get('config_language_id');
		
			if (!empty($setting['advanced']['button_title'][$language_id])) {
				$data['button_title'] = $setting['advanced']['button_title'][$language_id];
			} else {
				$data['button_title'] = $this->language->get('button_title');
			}

			return $this->load->view('payment/worldline/worldline', $data);
		}
		
		return '';
	}
		
	public function confirm() {					
		$this->load->language('payment/worldline');
		
		$this->load->model('payment/worldline');
		$this->load->model('checkout/order');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/country');
				
		$_config = new Config();
		$_config->load('worldline');
			
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
						
		$extension = $setting['extension'];
		$environment = $setting['account']['environment'];
		$merchant_id = $setting['account']['merchant_id'][$environment];
		$api_key = $setting['account']['api_key'][$environment];
		$api_secret = $setting['account']['api_secret'][$environment];
		$api_endpoint = $setting['account']['api_endpoint'][$environment];
		$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
													
		$language_code = explode('-', $this->session->data['language']);
		$language_code = reset($language_code);
				
		$currency_code = $this->session->data['currency'];
		$currency_value = $this->currency->getValue($this->session->data['currency']);
		$decimal_place = $this->currency->getDecimalPlace($this->session->data['currency']);
										
		$order_id = $this->session->data['order_id'];
		
		$order_info = $this->model_checkout_order->getOrder($order_id);
			
		$order_total = number_format($order_info['total'] * $currency_value * 100, 0, '', '');
		
		require_once DIR_SYSTEM . 'library/worldline/OnlinePayments.php';
				
		$connection = new OnlinePayments\Sdk\DefaultConnection();

		$shopping_cart_extension = new OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

		$communicator_configuration = new OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
		$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

		$communicator = new OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
        $client = new OnlinePayments\Sdk\Client($communicator);
		       		
		$line_items = array();
		
		$item_total = 0;
		
		foreach ($this->cart->getProducts() as $product) {
			$product_price = number_format($product['price'] * $currency_value * 100, 0, '', '');
			
			$product_tax = 0;
			
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					$product_tax += $tax_rate['amount'];
				}
			}
			
			$product_tax = number_format($product_tax * $currency_value * 100, 0, '', '');
			$product_total = number_format(($product_price + $product_tax) * $product['quantity'], 0, '', '');
									
			$order_line_details = new OnlinePayments\Sdk\Domain\OrderLineDetails();
			$order_line_details->setProductCode($product['model']);
			$order_line_details->setProductName($product['name']);
			$order_line_details->setProductPrice($product_price);
			$order_line_details->setQuantity($product['quantity']);
			$order_line_details->setTaxAmount($product_tax);
			
			$item_amount_of_money = new OnlinePayments\Sdk\Domain\AmountOfMoney();
			$item_amount_of_money->setAmount($product_total);
			$item_amount_of_money->setCurrencyCode($currency_code);
						
			$line_item = new OnlinePayments\Sdk\Domain\LineItem();
			$line_item->setOrderLineDetails($order_line_details);
			$line_item->setAmountOfMoney($item_amount_of_money);

			$line_items[] = $line_item;
			
			$item_total += $product_total;
		}
		
		$personal_name = new OnlinePayments\Sdk\Domain\PersonalName();
		
		if ($order_info['firstname']) {
			$personal_name->setFirstName($order_info['firstname']);
		}
		
		if ($order_info['lastname']) {
			$personal_name->setSurname($order_info['lastname']);
		}
		
		$personal_information = new OnlinePayments\Sdk\Domain\PersonalInformation();
		$personal_information->setName($personal_name);
		
		$contact_details = new OnlinePayments\Sdk\Domain\ContactDetails();
		
		if ($order_info['email']) {
			$contact_details->setEmailAddress($order_info['email']);
		}
		
		if ($order_info['telephone']) {
			$contact_details->setPhoneNumber($order_info['telephone']);
		}

        $billing_address = new OnlinePayments\Sdk\Domain\Address();
       								
		if ($order_info['payment_country_id']) {
			$country_info = $this->model_localisation_country->getCountry($order_info['payment_country_id']);
			
			if ($country_info) {
				$billing_address->setCountryCode($country_info['iso_code_2']);
			}
		}
		
		if ($order_info['payment_zone_id']) {
			$zone_info = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);
			
			if ($zone_info) {
				$billing_address->setState($zone_info['name']);
			}
		}
		
		if ($order_info['payment_city']) {
			$billing_address->setCity($order_info['payment_city']);
		}
		
		if ($order_info['payment_postcode']) {
			$billing_address->setZip($order_info['payment_postcode']);
		}
				
		if ($order_info['payment_address_1']) {
			$billing_address->setStreet($order_info['payment_address_1']);
		}
		
		$browser_data = new OnlinePayments\Sdk\Domain\BrowserData();
		$browser_data->setColorDepth($this->request->post['browser_color_depth']);
		$browser_data->setScreenHeight($this->request->post['browser_screen_height']);
		$browser_data->setScreenWidth($this->request->post['browser_screen_width']);
			
		$customer_device = new OnlinePayments\Sdk\Domain\CustomerDevice();
		$customer_device->setBrowserData($browser_data);
		$customer_device->setIpAddress($this->request->server['REMOTE_ADDR']);
		
		$customer = new OnlinePayments\Sdk\Domain\Customer();
		$customer->setPersonalInformation($personal_information);
        $customer->setContactDetails($contact_details);
        $customer->setBillingAddress($billing_address);
		$customer->setDevice($customer_device);
				
		if ($this->cart->hasShipping()) {
			$shipping_price = 0;
			$shipping_total = 0;
			$shipping_tax = 0;
			
			if (isset($this->session->data['shipping_method'])) {
				$shipping_price = number_format($this->session->data['shipping_method']['cost'] * $currency_value * 100, 0, '', '');
				$shipping_total = number_format($this->tax->calculate($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id'], true) * $currency_value * 100, 0, '', '');
				$shipping_tax = $shipping_total - $shipping_price;
			}
			
			$personal_name = new OnlinePayments\Sdk\Domain\PersonalName();
		
			if ($order_info['shipping_firstname']) {
				$personal_name->setFirstName($order_info['shipping_firstname']);
			}
		
			if ($order_info['shipping_lastname']) {
				$personal_name->setSurname($order_info['shipping_lastname']);
			}
			
			$shipping_address = new OnlinePayments\Sdk\Domain\AddressPersonal();
		
			$shipping_address->setName($personal_name);
			
			if ($order_info['shipping_country_id']) {
				$country_info = $this->model_localisation_country->getCountry($order_info['shipping_country_id']);
			
				if ($country_info) {
					$shipping_address->setCountryCode($country_info['iso_code_2']);
				}
			}
		
			if ($order_info['shipping_zone_id']) {
				$zone_info = $this->model_localisation_zone->getZone($order_info['shipping_zone_id']);
			
				if ($zone_info) {
					$shipping_address->setState($zone_info['name']);
				}
			}
		
			if ($order_info['shipping_city']) {
				$shipping_address->setCity($order_info['shipping_city']);
			}
		
			if ($order_info['shipping_postcode']) {
				$shipping_address->setZip($order_info['shipping_postcode']);
			}
			
			if ($order_info['shipping_address_1']) {
				$shipping_address->setStreet($order_info['shipping_address_1']);
			}
			
			$shipping = new OnlinePayments\Sdk\Domain\Shipping();
			$shipping->setShippingCost($shipping_price);
			$shipping->setShippingCostTax($shipping_tax);
			$shipping->setAddress($shipping_address);
			
			$item_total += $shipping_total;
		} else {			
			$personal_name = new OnlinePayments\Sdk\Domain\PersonalName();
		
			if ($order_info['payment_firstname']) {
				$personal_name->setFirstName($order_info['payment_firstname']);
			}
		
			if ($order_info['payment_lastname']) {
				$personal_name->setSurname($order_info['payment_lastname']);
			}
			
			$shipping_address = new OnlinePayments\Sdk\Domain\AddressPersonal();
		
			$shipping_address->setName($personal_name);
			
			if ($order_info['payment_country_id']) {
				$country_info = $this->model_localisation_country->getCountry($order_info['payment_country_id']);
			
				if ($country_info) {
					$shipping_address->setCountryCode($country_info['iso_code_2']);
				}
			}
		
			if ($order_info['payment_zone_id']) {
				$zone_info = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);
			
				if ($zone_info) {
					$shipping_address->setState($zone_info['name']);
				}
			}
		
			if ($order_info['payment_city']) {
				$shipping_address->setCity($order_info['payment_city']);
			}
		
			if ($order_info['payment_postcode']) {
				$shipping_address->setZip($order_info['payment_postcode']);
			}
			
			if ($order_info['payment_address_1']) {
				$shipping_address->setStreet($order_info['payment_address_1']);
			}
			
			$shipping = new OnlinePayments\Sdk\Domain\Shipping();
			$shipping->setAddress($shipping_address);
		}
		
		$tokens = array();
		
		if ($this->customer->isLogged()) {
			$worldline_customer_tokens = $this->model_payment_worldline->getWorldlineCustomerTokens($this->customer->getId());
			
			foreach ($worldline_customer_tokens as $worldline_customer_token) {
				$tokens[] = $worldline_customer_token['token'];
			}
		}
		
		$order_references = new OnlinePayments\Sdk\Domain\OrderReferences();
		$order_references->setMerchantReference($order_info['order_id'] . '_' . date('Ymd_His'));
		
		if ($item_total < $order_total) {
			$order_line_details = new OnlinePayments\Sdk\Domain\OrderLineDetails();
			$order_line_details->setProductCode('handling');
			$order_line_details->setProductName($this->language->get('text_handling'));
			$order_line_details->setProductPrice($order_total - $item_total);
			$order_line_details->setQuantity(1);
			$order_line_details->setTaxAmount(0);
						
			$item_amount_of_money = new OnlinePayments\Sdk\Domain\AmountOfMoney();
			$item_amount_of_money->setAmount($order_total - $item_total);
			$item_amount_of_money->setCurrencyCode($currency_code);
									
			$line_item = new OnlinePayments\Sdk\Domain\LineItem();
			$line_item->setOrderLineDetails($order_line_details);
			$line_item->setAmountOfMoney($item_amount_of_money);
			
			$line_items[] = $line_item;
		}
				
		if ($item_total > $order_total) {
			$discount = new OnlinePayments\Sdk\Domain\Discount();
			$discount->setAmount($item_total - $order_total);
		}	
		
		$amount_of_money = new OnlinePayments\Sdk\Domain\AmountOfMoney();
        $amount_of_money->setAmount($order_total);
        $amount_of_money->setCurrencyCode($currency_code);     		
		
		$shopping_cart = new OnlinePayments\Sdk\Domain\ShoppingCart();
		$shopping_cart->setItems($line_items);

		$order = new OnlinePayments\Sdk\Domain\Order();
		$order->setCustomer($customer);
		$order->setShipping($shipping);
		$order->setReferences($order_references);
		$order->setAmountOfMoney($amount_of_money);
		$order->setShoppingCart($shopping_cart);
		
		if (!empty($discount)) {
			$order->setDiscount($discount);
		}
		
		$three_d_secure = new OnlinePayments\Sdk\Domain\ThreeDSecure();
		$three_d_secure->setChallengeIndicator($setting['advanced']['tds_challenge_indicator']);
		$three_d_secure->setExemptionRequest($setting['advanced']['tds_exemption_request']);
						
		$card_payment_method_specific_input = new OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInput();
		$card_payment_method_specific_input->setAuthorizationMode($authorization_mode);
		$card_payment_method_specific_input->setTransactionChannel('ECOMMERCE');
		
		if ($setting['advanced']['tds_status']) {
			$card_payment_method_specific_input->setSkipAuthentication(false);
			$card_payment_method_specific_input->setThreeDSecure($three_d_secure);
		} else {
			$card_payment_method_specific_input->setSkipAuthentication(true);
		}
				
		if ($setting['advanced']['forced_tokenization']) {
			$card_payment_method_specific_input->setTokenize(true);
		} else {
			$card_payment_method_specific_input->setTokenize(false);
		}
						
		$redirect_payment_method_specific_input = new OnlinePayments\Sdk\Domain\RedirectPaymentMethodSpecificInput();
		
		if ($authorization_mode == 'SALE') {
			$redirect_payment_method_specific_input->setRequiresApproval(false);
		} else {
			$redirect_payment_method_specific_input->setRequiresApproval(true);
		}
		
		if ($this->customer->isLogged() && $setting['advanced']['forced_tokenization']) {
			$redirect_payment_method_specific_input->setTokenize(true);
		} else {
			$redirect_payment_method_specific_input->setTokenize(false);
		}
				
		$mobile_payment_method_specific_input = new OnlinePayments\Sdk\Domain\MobilePaymentMethodSpecificInput();
		$mobile_payment_method_specific_input->setAuthorizationMode($authorization_mode);
		
		$card_payment_method_specific_input_for_hosted_checkout = new OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInputForHostedCheckout();
		$card_payment_method_specific_input_for_hosted_checkout->setGroupCards((bool)$setting['advanced']['group_cards']);
				
		$hosted_checkout_specific_input = new OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput();
		$hosted_checkout_specific_input->setLocale($language_code . '-' . strtoupper($language_code));
		$hosted_checkout_specific_input->setReturnUrl(str_replace('&amp;', '&', $this->url->link('payment/worldline/callback', '', true)));
		$hosted_checkout_specific_input->setCardPaymentMethodSpecificInput($card_payment_method_specific_input_for_hosted_checkout);

        if ($setting['advanced']['template']) {
			$hosted_checkout_specific_input->setVariant($setting['advanced']['template']);
		}
		
		if ($tokens) {
			$hosted_checkout_specific_input->setTokens(implode(',', $tokens));
		}
		
		$create_hosted_checkout_request = new OnlinePayments\Sdk\Domain\CreateHostedCheckoutRequest();
		$create_hosted_checkout_request->setOrder($order);
		$create_hosted_checkout_request->setCardPaymentMethodSpecificInput($card_payment_method_specific_input);
		$create_hosted_checkout_request->setRedirectPaymentMethodSpecificInput($redirect_payment_method_specific_input);
		$create_hosted_checkout_request->setMobilePaymentMethodSpecificInput($mobile_payment_method_specific_input);
		$create_hosted_checkout_request->setHostedCheckoutSpecificInput($hosted_checkout_specific_input);
		
        $errors = array();

		try {
			$create_hosted_checkout_response = $client->merchant($merchant_id)->hostedCheckout()->createHostedCheckout($create_hosted_checkout_request);
		} catch (OnlinePayments\Sdk\ResponseException $exception) {			
			$errors = $exception->getResponse()->getErrors();
								
			if ($errors) {
				$error_messages = array();
					
				foreach ($errors as $error) {
					$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
					
					$error_messages[] = $error->getMessage() . ' (' . $error->getCode() . ')';
				}	
				
				$this->error['warning'] = implode('. ', $error_messages);
			}
		}
		
		if (!empty($this->error['warning'])) {
			$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', '', true));
		}
		
		if (!$errors) {
			$hosted_checkout_id = $create_hosted_checkout_response->getHostedCheckoutId();
			$hosted_checkout_url = $create_hosted_checkout_response->getRedirectUrl();
			
			$this->model_payment_worldline->deleteWorldlineOrder($order_id);
										
			$worldline_order_data = array(
				'order_id' => $order_id,
				'transaction_id' => $hosted_checkout_id,
				'total' => ($order_info['total'] * $currency_value),
				'currency_code' => $currency_code,
				'country_code' => (!empty($country_info['iso_code_2']) ? $country_info['iso_code_2'] : ''),
				'environment' => $environment
			);

			$this->model_payment_worldline->addWorldlineOrder($worldline_order_data);
			
			$data['redirect'] = $hosted_checkout_url;
		}
							
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
	
	public function callback() {
		if (!empty($this->request->get['hostedCheckoutId'])) {
			$hosted_checkout_id = $this->request->get['hostedCheckoutId'];
			
			$_config = new Config();
			$_config->load('worldline');
			
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
						
			$environment = $setting['account']['environment'];
			$merchant_id = $setting['account']['merchant_id'][$environment];
			$api_key = $setting['account']['api_key'][$environment];
			$api_secret = $setting['account']['api_secret'][$environment];
			$api_endpoint = $setting['account']['api_endpoint'][$environment];
			$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
			require_once DIR_SYSTEM . 'library/worldline/OnlinePayments.php';
				
			$connection = new OnlinePayments\Sdk\DefaultConnection();	

			$communicator_configuration = new OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

			$communicator = new OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
			$client = new OnlinePayments\Sdk\Client($communicator);
			
			$errors = array();
			
			try {
				$hosted_checkout_response = $client->merchant($merchant_id)->hostedCheckout()->getHostedCheckout($hosted_checkout_id);
			} catch (OnlinePayments\Sdk\ResponseException $exception) {			
				$errors = $exception->getResponse()->getErrors();
								
				if ($errors) {
					foreach ($errors as $error) {
						$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
					}	
				}
			}
			
			if (!$errors) {
				if (!empty($hosted_checkout_response->getCreatedPaymentOutput())) {
					$merchant_reference = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getReferences()->getMerchantReference();
					$transaction_status = strtolower($hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getStatus());
					$total = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getAmountOfMoney()->getAmount() / 100;
					$amount = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getAcquiredAmount()->getAmount() / 100;
					$currency_code = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode();
					
					$payment_product_id = '';
					$payment_type = '';
					$token = '';
										
					if (!empty($hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
						$payment_product_id = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
						$token = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
						$payment_type = 'card';
					}
				
					if (!empty($hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getMobilePaymentMethodSpecificOutput())) {
						$payment_product_id = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getPaymentProductId();
					}
				
					if (!empty($hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput())) {
						$payment_product_id = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getPaymentProductId();
						$token = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getToken();
						$payment_type = 'redirect';
					}
				
					if (!empty($hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput())) {
						$payment_product_id = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput()->getPaymentProductId();
					}
										
					$invoice_id = explode('_', $merchant_reference);
					$order_id = reset($invoice_id);
					
					$this->load->model('payment/worldline');
					$this->load->model('checkout/order');
					
					$worldline_order_info = $this->model_payment_worldline->getWorldlineOrder($order_id);
					$order_info = $this->model_checkout_order->getOrder($order_id);
					
					if ($worldline_order_info && $order_info) {
						$order_status_id = 0;
					
						if ($transaction_status == 'created') {
							$order_status_id = $setting['order_status']['created']['id'];
						}
					
						if (($transaction_status == 'cancelled') && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['cancelled']['id'];
						}
					
						if ((($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture')) && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['rejected']['id'];
						}
					
						if ($transaction_status == 'pending_capture') {
							$order_status_id = $setting['order_status']['pending']['id'];
						}
					
						if ($transaction_status == 'captured') {
							$order_status_id = $setting['order_status']['captured']['id'];
						}
				
						if (($transaction_status == 'refunded') && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['refunded']['id'];
						}
					
						if ($order_status_id && ($order_info['order_status_id'] != $order_status_id)) {
							$this->model_checkout_order->addOrderHistory($order_id, $order_status_id, '', true);
						}
						
						if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
							$payment_product = $worldline_order_info['payment_product'];
							
							if (!$worldline_order_info['transaction_status']) {
								$payment_product_params = new OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
								$payment_product_params->setCurrencyCode($currency_code);
								$payment_product_params->setCountryCode($worldline_order_info['country_code']);							
						
								try {
									$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
								} catch (OnlinePayments\Sdk\ResponseException $exception) {			
									$errors = $exception->getResponse()->getErrors();
								
									if ($errors) {
										foreach ($errors as $error) {
											$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
										}
									}
								}
				
								if (!$errors) {
									if (!empty($payment_product_response->getDisplayHints())) {
										if (!empty($payment_product_response->getPaymentProductGroup())) {
											$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
										}
						
										$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
									}
								}
							}
							
							$worldline_order_data = array(
								'order_id' => $order_id,
								'transaction_status' => $transaction_status,
								'payment_product' => $payment_product,
								'payment_type' => $payment_type,
								'token' => $token,
								'total' => $total,
								'amount' => $amount,
								'currency_code' => $currency_code
							);
							
							$this->model_payment_worldline->editWorldlineOrder($worldline_order_data);
							
							if ($this->customer->isLogged() && $token) {
								$customer_id = $this->customer->getId();
								
								$worldline_customer_token_info = $this->model_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
								
								if (!$worldline_customer_token_info) {
									$worldline_customer_token_data = array(
										'customer_id' => $customer_id,
										'payment_type' => $payment_type,
										'token' => $token
									);
									
									$this->model_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
								}
								
								$this->model_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
							}
						}
						
						if (($transaction_status == 'pending_capture') || ($transaction_status == 'captured')) {
							$this->response->redirect($this->url->link('checkout/success', '', true));
						}
						
						if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
							$this->response->redirect($this->url->link('payment/worldline/failurePage', '', true));
						}
					}
				}
				
				$this->response->redirect($this->url->link('payment/worldline/waitingPage', '', true));
			}
			
			return true;
		}
		
		return false;
	}
	
	public function waitingPage() {
		$this->load->language('payment/worldline');

		$this->document->setTitle($this->language->get('text_waiting_page_title'));
						
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('payment/worldline/waitingPage', '', true)
		);

		$data['text_title'] = $this->language->get('text_waiting_page_title');
		$data['text_message'] = $this->language->get('text_waiting_page_message');
		
		if (!empty($this->session->data['order_id'])) {
			$data['order_id'] = $this->session->data['order_id'];
		}
				
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('payment/worldline/waiting_page', $data));
	}
		
	public function pendingPage() {
		$this->load->language('payment/worldline');

		$this->document->setTitle($this->language->get('text_pending_page_title'));
						
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('payment/worldline/pendingPage', '', true)
		);
				
		$data['text_title'] = $this->language->get('text_pending_page_title');
		$data['text_message'] = $this->language->get('text_pending_page_message');
		
		$data['button_continue'] = $this->language->get('button_continue');
		
		$data['continue'] = $this->url->link('common/home');
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('payment/worldline/pending_page', $data));
	}
	
	public function failurePage() {
		$this->load->language('payment/worldline');

		$this->document->setTitle($this->language->get('text_failure_page_title'));
						
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('payment/worldline/failurePage', '', true)
		);
		
		$data['text_title'] = $this->language->get('text_failure_page_title');
		$data['text_message'] = sprintf($this->language->get('text_failure_page_message'), $this->url->link('information/contact', '', true));
		
		if (!empty($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
			
			$this->load->model('payment/worldline');
			
			$worldline_order_info = $this->model_payment_worldline->getWorldlineOrder($order_id);
					
			if ($worldline_order_info) {
				$transaction_id = $worldline_order_info['transaction_id'];
				$transaction_status = $worldline_order_info['transaction_status'];
				
				if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
					$data['text_message'] = $this->language->get('text_transaction_' . strtolower($transaction_status));
				}
			}
		}
		
		$data['button_continue'] = $this->language->get('button_continue');
		
		$data['continue'] = $this->url->link('common/home');
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('payment/worldline/failure_page', $data));
	}
	
	public function getPaymentInfo() {
		if (!empty($this->request->post['order_id'])) {
			$order_id = $this->request->post['order_id'];
			
			$this->load->model('extension/payment/worldline');
			$this->load->model('checkout/order');
			
			$worldline_order_info = $this->model_payment_worldline->getWorldlineOrder($order_id);
			$order_info = $this->model_checkout_order->getOrder($order_id);
			
			if ($worldline_order_info && $order_info) {
				$transaction_id = $worldline_order_info['transaction_id'];
				$transaction_status = $worldline_order_info['transaction_status'];
				
				$data['redirect'] = '';
				
				if (($transaction_status == 'pending_capture') || ($transaction_status == 'captured')) {
					$data['redirect'] = $this->url->link('checkout/success', '', true);
				}
						
				if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
					$data['redirect'] = $this->url->link('payment/worldline/failurePage', '', true);
				}
			
				if (!$data['redirect']) {
					$_config = new Config();
					$_config->load('worldline');
			
					$config_setting = $_config->get('worldline_setting');
		
					$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
						
					$environment = $setting['account']['environment'];
					$merchant_id = $setting['account']['merchant_id'][$environment];
					$api_key = $setting['account']['api_key'][$environment];
					$api_secret = $setting['account']['api_secret'][$environment];
					$api_endpoint = $setting['account']['api_endpoint'][$environment];
					$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
					require_once DIR_SYSTEM . 'library/worldline/OnlinePayments.php';
				
					$connection = new OnlinePayments\Sdk\DefaultConnection();	

					$communicator_configuration = new OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

					$communicator = new OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
					$client = new OnlinePayments\Sdk\Client($communicator);
			
					$errors = array();
			
					try {
						$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($transaction_id . '_0');
					} catch (OnlinePayments\Sdk\ResponseException $exception) {			
						$errors = $exception->getResponse()->getErrors();
								
						if ($errors) {
							$error_messages = array();
					
							foreach ($errors as $error) {
								$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
					
								$error_messages[] = $error->getMessage() . ' (' . $error->getCode() . ')';
							}	
				
							$this->error['warning'] = implode('. ', $error_messages);
						}
					}
			
					if (!$errors) {
						$transaction_status = strtolower($payment_response->getStatus());
						$total = $payment_response->getPaymentOutput()->getAmountOfMoney()->getAmount() / 100;
						$amount = $payment_response->getPaymentOutput()->getAcquiredAmount()->getAmount() / 100;
						$currency_code = $payment_response->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode();
						
						$payment_product_id = '';
						$payment_type = '';
						$token = '';
											
						if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
							$payment_product_id = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
							$token = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
							$payment_type = 'card';
						}
				
						if (!empty($payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput())) {
							$payment_product_id = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getPaymentProductId();
						}
				
						if (!empty($payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput())) {
							$payment_product_id = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getPaymentProductId();
							$token = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getToken();
							$payment_type = 'redirect';
						}
				
						if (!empty($payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput())) {
							$payment_product_id = $payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput()->getPaymentProductId();
						}
									
						$order_status_id = 0;
					
						if ($transaction_status == 'created') {
							$order_status_id = $setting['order_status']['created']['id'];
						}
					
						if (($transaction_status == 'cancelled') && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['cancelled']['id'];
						}
					
						if ((($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture')) && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['rejected']['id'];
						}
					
						if ($transaction_status == 'pending_capture') {
							$order_status_id = $setting['order_status']['pending']['id'];
						}
					
						if ($transaction_status == 'captured') {
							$order_status_id = $setting['order_status']['captured']['id'];
						}
				
						if (($transaction_status == 'refunded') && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['refunded']['id'];
						}
					
						if ($order_status_id && ($order_info['order_status_id'] != $order_status_id)) {
							$this->model_checkout_order->addOrderHistory($order_id, $order_status_id, '', true);
						}
						
						if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
							$payment_product = $worldline_order_info['payment_product'];
							
							if (!$worldline_order_info['transaction_status']) {
								$payment_product_params = new OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
								$payment_product_params->setCurrencyCode($currency_code);
								$payment_product_params->setCountryCode($worldline_order_info['country_code']);							
						
								try {
									$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
								} catch (OnlinePayments\Sdk\ResponseException $exception) {			
									$errors = $exception->getResponse()->getErrors();
								
									if ($errors) {
										foreach ($errors as $error) {
											$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
										}
									}
								}
				
								if (!$errors) {
									if (!empty($payment_product_response->getDisplayHints())) {
										if (!empty($payment_product_response->getPaymentProductGroup())) {
											$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
										}
						
										$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
									}
								}
							}
							
							$worldline_order_data = array(
								'order_id' => $order_id,
								'transaction_status' => $transaction_status,
								'payment_product' => $payment_product,
								'payment_type' => $payment_type,
								'token' => $token,
								'total' => $total,
								'amount' => $amount,
								'currency_code' => $currency_code
							);
							
							$this->model_payment_worldline->editWorldlineOrder($worldline_order_data);
							
							if ($this->customer->isLogged() && $token) {
								$customer_id = $this->customer->getId();
								
								$worldline_customer_token_info = $this->model_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
								
								if (!$worldline_customer_token_info) {
									$worldline_customer_token_data = array(
										'customer_id' => $customer_id,
										'payment_type' => $payment_type,
										'token' => $token
									);
									
									$this->model_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
								}
								
								$this->model_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
							}
						}
						
						if (($transaction_status == 'pending_capture') || ($transaction_status == 'captured')) {
							$data['redirect'] = $this->url->link('checkout/success', '', true);
						}
						
						if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
							$data['redirect'] = $this->url->link('payment/worldline/failurePage', '', true);
						}
					}
				}
				
				if (!$data['redirect']) {
					$data['redirect'] = $this->url->link('payment/worldline/pendingPage', '', true);
				}
			}
		}
		
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
					
	public function webhook() {									
		if (!empty($this->request->get['webhook_token'])) {
			$_config = new Config();
			$_config->load('worldline');
		
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
		
			$webhook_info = json_decode(html_entity_decode(file_get_contents('php://input')), true);
			
			if (hash_equals($setting['account']['webhook_token'], $this->request->get['webhook_token']) && !empty($webhook_info['payment']['id']) && !empty($webhook_info['payment']['status'])) {	
				$this->load->language('payment/worldline');
		
				$this->load->model('payment/worldline');
				$this->load->model('checkout/order');
		
				$this->model_payment_worldline->log($webhook_info, 'Webhook');
											
				$environment = $setting['account']['environment'];
				$merchant_id = $setting['account']['merchant_id'][$environment];
				$api_key = $setting['account']['api_key'][$environment];
				$api_secret = $setting['account']['api_secret'][$environment];
				$api_endpoint = $setting['account']['api_endpoint'][$environment];
				$webhook_key = $setting['account']['webhook_key'][$environment];
				$webhook_secret = $setting['account']['webhook_secret'][$environment];
				$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
				require_once DIR_SYSTEM . 'library/worldline/OnlinePayments.php';
				
				$secret_key_store = new OnlinePayments\Sdk\Webhooks\InMemorySecretKeyStore(array($webhook_key => $webhook_secret));
				$helper = new OnlinePayments\Sdk\Webhooks\WebhooksHelper($secret_key_store);
					
				try {
					$event = $helper->unmarshal(file_get_contents('php://input'), $this->getallheaders());
				} catch (OnlinePayments\Sdk\Webhooks\SignatureValidationException $exception) {$this->log->write('45');
					$errors = $exception->getResponse()->getErrors();
								
					if ($errors) {
						foreach ($errors as $error) {
							$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
						}
					}

					return false;
				}
				
				$connection = new OnlinePayments\Sdk\DefaultConnection();	

				$communicator_configuration = new OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

				$communicator = new OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
				$client = new OnlinePayments\Sdk\Client($communicator);
						
				$errors = array();
			
				try {
					$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($webhook_info['payment']['id']);
				} catch (OnlinePayments\Sdk\ResponseException $exception) {			
					$errors = $exception->getResponse()->getErrors();
								
					if ($errors) {
						foreach ($errors as $error) {
							$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
						}
					}
				}
			
				if (!$errors) {
					$merchant_reference = $payment_response->getPaymentOutput()->getReferences()->getMerchantReference();
					$transaction_status = strtolower($payment_response->getStatus());
					$total = $payment_response->getPaymentOutput()->getAmountOfMoney()->getAmount() / 100;
					$amount = $payment_response->getPaymentOutput()->getAcquiredAmount()->getAmount() / 100;
					$currency_code = $payment_response->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode();
					
					$payment_product_id = '';
					$payment_type = '';
					$token = '';
											
					if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
						$payment_product_id = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
						$token = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
						$payment_type = 'card';
					}
				
					if (!empty($payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput())) {
						$payment_product_id = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getPaymentProductId();
					}
				
					if (!empty($payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput())) {
						$payment_product_id = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getPaymentProductId();
						$token = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getToken();
						$payment_type = 'redirect';
					}
				
					if (!empty($payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput())) {
						$payment_product_id = $payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput()->getPaymentProductId();
					}
				
					$invoice_id = explode('_', $merchant_reference);
					$order_id = reset($invoice_id);
					
					$payment_id = explode('_', $webhook_info['payment']['id']);
					$transaction_id = reset($payment_id);
					
					$worldline_order_info = $this->model_payment_worldline->getWorldlineOrder($order_id);
					$order_info = $this->model_checkout_order->getOrder($order_id);
					
					if ($worldline_order_info && ($worldline_order_info['transaction_id'] == $transaction_id) && $order_info) {
						$order_status_id = 0;
					
						if ($transaction_status == 'created') {
							$order_status_id = $setting['order_status']['created']['id'];
						}
					
						if (($transaction_status == 'cancelled') && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['cancelled']['id'];
						}
					
						if ((($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture')) && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['rejected']['id'];
						}
					
						if ($transaction_status == 'pending_capture') {
							$order_status_id = $setting['order_status']['pending']['id'];
						}
					
						if ($transaction_status == 'captured') {
							$order_status_id = $setting['order_status']['captured']['id'];
						}
				
						if (($transaction_status == 'refunded') && ($order_info['order_status_id'] != 0)) {
							$order_status_id = $setting['order_status']['refunded']['id'];
						}
					
						if ($order_status_id && ($order_info['order_status_id'] != $order_status_id)) {
							$this->model_checkout_order->addOrderHistory($order_id, $order_status_id, '', true);
						}
						
						if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
							$payment_product = $worldline_order_info['payment_product'];
							
							if (!$worldline_order_info['transaction_status']) {
								$payment_product_params = new OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
								$payment_product_params->setCurrencyCode($currency_code);
								$payment_product_params->setCountryCode($worldline_order_info['country_code']);							
						
								try {
									$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
								} catch (OnlinePayments\Sdk\ResponseException $exception) {			
									$errors = $exception->getResponse()->getErrors();
								
									if ($errors) {
										foreach ($errors as $error) {
											$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
										}
									}
								}
				
								if (!$errors) {
									if (!empty($payment_product_response->getDisplayHints())) {
										if (!empty($payment_product_response->getPaymentProductGroup())) {
											$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
										}
						
										$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
									}
								}
							}
							
							$worldline_order_data = array(
								'order_id' => $order_id,
								'transaction_status' => $transaction_status,
								'payment_product' => $payment_product,
								'payment_type' => $payment_type,
								'token' => $token,
								'total' => $total,
								'amount' => $amount,
								'currency_code' => $currency_code
							);
							
							$this->model_payment_worldline->editWorldlineOrder($worldline_order_data);
							
							if (!empty($order_info['customer_id']) && $token) {
								$customer_id = $order_info['customer_id'];
								
								$worldline_customer_token_info = $this->model_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
								
								if (!$worldline_customer_token_info) {
									$worldline_customer_token_data = array(
										'customer_id' => $customer_id,
										'payment_type' => $payment_type,
										'token' => $token
									);
									
									$this->model_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
								}
								
								$this->model_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
							}
						}
					}
				}

				header('HTTP/1.1 200 OK');
	
				return true;
			}
		}
		
		return false;
	}
	
	public function cron() {
		if (!empty($this->request->get['cron_token'])) {
			$_config = new Config();
			$_config->load('worldline');
		
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
			
			if (hash_equals($setting['account']['cron_token'], $this->request->get['cron_token'])) {
				$this->load->model('payment/worldline');
				$this->load->model('checkout/order');
	
				$waiting_worldline_orders = $this->model_payment_worldline->getWaitingWorldlineOrders();
			
				if ($waiting_worldline_orders) {
					$environment = $setting['account']['environment'];
					$merchant_id = $setting['account']['merchant_id'][$environment];
					$api_key = $setting['account']['api_key'][$environment];
					$api_secret = $setting['account']['api_secret'][$environment];
					$api_endpoint = $setting['account']['api_endpoint'][$environment];
					$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
					require_once DIR_SYSTEM . 'library/worldline/OnlinePayments.php';
				
					$connection = new OnlinePayments\Sdk\DefaultConnection();	

					$communicator_configuration = new OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

					$communicator = new OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
					$client = new OnlinePayments\Sdk\Client($communicator);

					foreach ($waiting_worldline_orders as $waiting_worldline_order) {
						$order_id = $waiting_worldline_order['order_id'];
						$transaction_id = $waiting_worldline_order['transaction_id'];
						
						$order_info = $this->model_checkout_order->getOrder($order_id);
						
						$errors = array();
			
						try {
							$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($transaction_id . '_0');
						} catch (OnlinePayments\Sdk\ResponseException $exception) {			
							$errors = $exception->getResponse()->getErrors();
								
							if ($errors) {
								foreach ($errors as $error) {
									$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
								}	
							}
						}
			
						if ($order_info && !$errors) {
							$transaction_status = strtolower($payment_response->getStatus());
							$total = $payment_response->getPaymentOutput()->getAmountOfMoney()->getAmount() / 100;
							$amount = $payment_response->getPaymentOutput()->getAcquiredAmount()->getAmount() / 100;
							$currency_code = $payment_response->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode();
							
							$payment_product_id = '';
							$payment_type = '';
							$token = '';
											
							if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
								$payment_product_id = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
								$token = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
								$payment_type = 'card';
							}
				
							if (!empty($payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput())) {
								$payment_product_id = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getPaymentProductId();
							}
				
							if (!empty($payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput())) {
								$payment_product_id = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getPaymentProductId();
								$token = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getToken();
								$payment_type = 'redirect';
							}
				
							if (!empty($payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput())) {
								$payment_product_id = $payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput()->getPaymentProductId();
							}
									
							$order_status_id = 0;
					
							if ($transaction_status == 'created') {
								$order_status_id = $setting['order_status']['created']['id'];
							}
					
							if (($transaction_status == 'cancelled') && ($order_info['order_status_id'] != 0)) {
								$order_status_id = $setting['order_status']['cancelled']['id'];
							}
					
							if ((($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture')) && ($order_info['order_status_id'] != 0)) {
								$order_status_id = $setting['order_status']['rejected']['id'];
							}
					
							if ($transaction_status == 'pending_capture') {
								$order_status_id = $setting['order_status']['pending']['id'];
							}
					
							if ($transaction_status == 'captured') {
								$order_status_id = $setting['order_status']['captured']['id'];
							}
					
							if (($transaction_status == 'refunded') && ($order_info['order_status_id'] != 0)) {
								$order_status_id = $setting['order_status']['refunded']['id'];
							}
					
							if ($order_status_id && ($order_info['order_status_id'] != $order_status_id)) {
								$this->model_checkout_order->addOrderHistory($order_id, $order_status_id, '', true);
							}
						
							if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
								$payment_product = $waiting_worldline_order['payment_product'];
							
								if (!$waiting_worldline_order['transaction_status']) {
									$payment_product_params = new OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
									$payment_product_params->setCurrencyCode($currency_code);
									$payment_product_params->setCountryCode($waiting_worldline_order['country_code']);							
						
									try {
										$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
									} catch (OnlinePayments\Sdk\ResponseException $exception) {			
										$errors = $exception->getResponse()->getErrors();
								
										if ($errors) {
											foreach ($errors as $error) {
												$this->model_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
											}
										}
									}
				
									if (!$errors) {
										if (!empty($payment_product_response->getDisplayHints())) {
											if (!empty($payment_product_response->getPaymentProductGroup())) {
												$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
											}
						
											$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
										}
									}
								}
							
								$worldline_order_data = array(
									'order_id' => $order_id,
									'transaction_status' => $transaction_status,
									'payment_product' => $payment_product,
									'payment_type' => $payment_type,
									'token' => $token,
									'total' => $total,
									'amount' => $amount,
									'currency_code' => $currency_code
								);
							
								$this->model_payment_worldline->editWorldlineOrder($worldline_order_data);
							
								if (!empty($order_info['customer_id']) && $token) {
									$customer_id = $order_info['customer_id'];
								
									$worldline_customer_token_info = $this->model_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
								
									if (!$worldline_customer_token_info) {
										$worldline_customer_token_data = array(
											'customer_id' => $customer_id,
											'payment_type' => $payment_type,
											'token' => $token
										);
									
										$this->model_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
									}
								
									$this->model_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
								}
							}
						}
					}
				}
			
				return true;
			}
		}
		
		return false;
	}
			
	public function order_delete_order_before($route, $order_id) {
		$this->load->model('payment/worldline');

		$this->model_payment_worldline->deleteWorldlineOrder($order_id);
	}
	
	private function getallheaders() {		
		if (function_exists('getallheaders')) {
			return getallheaders();
		} else {
			$headers = array();
			
			foreach ($_SERVER as $name => $value) {
				if (substr($name, 0, 5) == 'HTTP_') {
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			
			return $headers;
		}
	}
}