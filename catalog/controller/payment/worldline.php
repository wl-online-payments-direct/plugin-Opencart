<?php
namespace Opencart\Catalog\Controller\Extension\Worldline\Payment;
class Worldline extends \Opencart\System\Engine\Controller {
	private $error = [];
	private $separator = '';
		
	public function __construct($registry) {
		parent::__construct($registry);
		
		if (version_compare(VERSION, '4.0.2.0', '>=')) {
			$this->separator = '.';
		} else {
			$this->separator = '|';
		}
	}
							
	public function index(): string {			
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));

		$environment = $setting['account']['environment'];
		
		if ($setting['account']['api_key'][$environment] && $setting['account']['api_secret'][$environment] && !$this->callback() && !$this->webhook() && !$this->cron()) {
			if (version_compare(VERSION, '4.0.2.0', '>=')) {
				if (!empty($this->session->data['payment_method']['code'])) {
					if ($this->session->data['payment_method']['code'] == 'worldline.hosted_tokenization') {
						return $this->load->controller('extension/worldline/payment/worldline_hosted_tokenization');
					}
				}
			}

			$this->load->language('extension/worldline/payment/worldline');	

			$language_id = $this->config->get('config_language_id');
		
			$data['surcharging_status'] = $setting['advanced']['surcharging_status'];
			
			$data['hosted_checkout']['status'] = $setting['hosted_checkout']['status'];
		
			if (!empty($setting['hosted_checkout']['button_title'][$language_id])) {
				$data['hosted_checkout']['button_title'] = $setting['hosted_checkout']['button_title'][$language_id];
			} else {
				$data['hosted_checkout']['button_title'] = $this->language->get('button_hosted_checkout_title');
			}
			
			$data['api_url'] = $setting['account']['api_endpoint'][$environment];
			
			$data['separator'] = $this->separator;
						
			return $this->load->view('extension/worldline/payment/worldline_hosted_checkout', $data);
		}
		
		return '';
	}
	
	public function deleteCustomerToken(): void {
		$this->load->language('extension/worldline/payment/worldline');
		
		$this->load->model('extension/worldline/payment/worldline');

		if ($this->customer->isLogged() && isset($this->request->post['token'])) {
			$this->model_extension_worldline_payment_worldline->deleteWorldlineCustomerToken($this->customer->getId(), 'card', $this->request->post['token']);
					
			$data['success'] = true;
		}
		
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
		
	public function confirm(): void {					
		$this->load->language('extension/worldline/payment/worldline');
		
		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('checkout/order');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/country');
				
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
			
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
						
		$extension = $setting['extension'];
		$environment = $setting['account']['environment'];
		$merchant_id = $setting['account']['merchant_id'][$environment];
		$api_key = $setting['account']['api_key'][$environment];
		$api_secret = $setting['account']['api_secret'][$environment];
		$api_endpoint = $setting['account']['api_endpoint'][$environment];
		$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
		$date_captured = '';
		
		if (($authorization_mode == 'PRE_AUTHORIZATION') || ($authorization_mode == 'FINAL_AUTHORIZATION')) {
			$capture_days = $setting['capture_installation'][$setting['advanced']['capture_installation']]['days'];
			
			if ($capture_days) {
				$date_captured = new DateTime('now');
				
				$date_captured->modify('+' . $capture_days . ' day');
				
				$date_captured = $date_captured->format('Y-m-d 00:00:00');
			}
		}
		
		$language_code = explode('-', $this->config->get('config_language'));
		$language_code_1 = reset($language_code);
		$language_code_2 = end($language_code);
																	
		$currency_code = $this->session->data['currency'];
		$currency_value = $this->currency->getValue($this->session->data['currency']);
		$decimal_place = $this->currency->getDecimalPlace($this->session->data['currency']);
										
		$order_id = $this->session->data['order_id'];
		
		$order_info = $this->model_checkout_order->getOrder($order_id);
			
		$order_total = number_format($order_info['total'] * $currency_value * 100, 0, '', '');
		
		require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
		$connection = new \OnlinePayments\Sdk\DefaultConnection();

		$shopping_cart_extension = new \OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

		$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
		$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

		$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
        $client = new \OnlinePayments\Sdk\Client($communicator);
		       		
		$line_items = [];
		
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
									
			$order_line_details = new \OnlinePayments\Sdk\Domain\OrderLineDetails();
			$order_line_details->setProductCode($product['model']);
			$order_line_details->setProductName($product['name']);
			$order_line_details->setProductPrice($product_price);
			$order_line_details->setQuantity($product['quantity']);
			$order_line_details->setTaxAmount($product_tax);
			
			$item_amount_of_money = new \OnlinePayments\Sdk\Domain\AmountOfMoney();
			$item_amount_of_money->setAmount($product_total);
			$item_amount_of_money->setCurrencyCode($currency_code);
						
			$line_item = new \OnlinePayments\Sdk\Domain\LineItem();
			$line_item->setOrderLineDetails($order_line_details);
			$line_item->setAmountOfMoney($item_amount_of_money);

			$line_items[] = $line_item;
			
			$item_total += $product_total;
		}
		
		$personal_name = new \OnlinePayments\Sdk\Domain\PersonalName();
		
		if ($order_info['firstname']) {
			$personal_name->setFirstName($order_info['firstname']);
		}
		
		if ($order_info['lastname']) {
			$personal_name->setSurname($order_info['lastname']);
		}
		
		$personal_information = new \OnlinePayments\Sdk\Domain\PersonalInformation();
		$personal_information->setName($personal_name);
		
		$contact_details = new \OnlinePayments\Sdk\Domain\ContactDetails();
		
		if ($order_info['email']) {
			$contact_details->setEmailAddress($order_info['email']);
		}
		
		if ($order_info['telephone']) {
			$contact_details->setPhoneNumber($order_info['telephone']);
		}

        $billing_address = new \OnlinePayments\Sdk\Domain\Address();
       								
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
		
		$browser_data = new \OnlinePayments\Sdk\Domain\BrowserData();
		$browser_data->setColorDepth($this->request->post['browser_color_depth']);
		$browser_data->setScreenHeight($this->request->post['browser_screen_height']);
		$browser_data->setScreenWidth($this->request->post['browser_screen_width']);
		$browser_data->setJavaScriptEnabled(false);
			
		$customer_device = new \OnlinePayments\Sdk\Domain\CustomerDevice();
		$customer_device->setBrowserData($browser_data);
		$customer_device->setIpAddress($this->request->server['REMOTE_ADDR']);
		$customer_device->setAcceptHeader($this->request->server['HTTP_ACCEPT']);
		$customer_device->setLocale($language_code_1 . '_' . strtoupper($language_code_2));
		$customer_device->setTimezoneOffsetUtcMinutes($this->request->post['browser_timezone_offset']);
		$customer_device->setUserAgent($this->request->server['HTTP_USER_AGENT']);
		
		$customer = new \OnlinePayments\Sdk\Domain\Customer();
		$customer->setPersonalInformation($personal_information);
        $customer->setContactDetails($contact_details);
        $customer->setBillingAddress($billing_address);
		$customer->setDevice($customer_device);
								
		if ($this->cart->hasShipping()) {
			$shipping_price = 0;
			$shipping_total = 0;
			$shipping_tax = 0;
			
			if (isset($this->session->data['shipping_method'])) {
				if (version_compare(VERSION, '4.0.2.0', '>=')) {
					$shipping = explode('.', $this->session->data['shipping_method']['code']);
				} else {
					$shipping = explode('.', $this->session->data['shipping_method']);
				}

				if (isset($shipping_method[0]) && isset($shipping_method[1]) && isset($this->session->data['shipping_methods'][$shipping_method[0]]['quote'][$shipping_method[1]])) {
					$shipping_method_info = $this->session->data['shipping_methods'][$shipping_method[0]]['quote'][$shipping_method[1]];
					
					$shipping_price = number_format($shipping_method_info['cost'] * $currency_value * 100, 0, '', '');
					$shipping_total = number_format($this->tax->calculate($shipping_method_info['cost'], $shipping_method_info['tax_class_id'], true) * $currency_value * 100, 0, '', '');
					$shipping_tax = $shipping_total - $shipping_price;
				}
			}
						
			$personal_name = new \OnlinePayments\Sdk\Domain\PersonalName();
		
			if ($order_info['shipping_firstname']) {
				$personal_name->setFirstName($order_info['shipping_firstname']);
			}
		
			if ($order_info['shipping_lastname']) {
				$personal_name->setSurname($order_info['shipping_lastname']);
			}
			
			$shipping_address = new \OnlinePayments\Sdk\Domain\AddressPersonal();
		
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
			
			$shipping = new \OnlinePayments\Sdk\Domain\Shipping();
			$shipping->setShippingCost($shipping_price);
			$shipping->setShippingCostTax($shipping_tax);
			$shipping->setAddress($shipping_address);
			
			$item_total += $shipping_total;
		} else {			
			$personal_name = new \OnlinePayments\Sdk\Domain\PersonalName();
		
			if ($order_info['payment_firstname']) {
				$personal_name->setFirstName($order_info['payment_firstname']);
			}
		
			if ($order_info['payment_lastname']) {
				$personal_name->setSurname($order_info['payment_lastname']);
			}
			
			$shipping_address = new \OnlinePayments\Sdk\Domain\AddressPersonal();
		
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
			
			$shipping = new \OnlinePayments\Sdk\Domain\Shipping();
			$shipping->setAddress($shipping_address);
		}
		
		$tokens = [];
		
		if ($this->customer->isLogged()) {
			$worldline_customer_tokens = $this->model_extension_worldline_payment_worldline->getWorldlineCustomerTokens($this->customer->getId());
			
			foreach ($worldline_customer_tokens as $worldline_customer_token) {
				$tokens[] = $worldline_customer_token['token'];
			}
		}
		
		$order_references = new \OnlinePayments\Sdk\Domain\OrderReferences();
		$order_references->setMerchantReference($order_info['order_id'] . '_' . date('Ymd_His'));
		$order_references->setDescriptor($this->config->get('config_name'));
 
       if ($item_total < $order_total) {
			$order_line_details = new \OnlinePayments\Sdk\Domain\OrderLineDetails();
			$order_line_details->setProductCode('handling');
			$order_line_details->setProductName($this->language->get('text_handling'));
			$order_line_details->setProductPrice($order_total - $item_total);
			$order_line_details->setQuantity(1);
			$order_line_details->setTaxAmount(0);
						
			$item_amount_of_money = new \OnlinePayments\Sdk\Domain\AmountOfMoney();
			$item_amount_of_money->setAmount($order_total - $item_total);
			$item_amount_of_money->setCurrencyCode($currency_code);
									
			$line_item = new \OnlinePayments\Sdk\Domain\LineItem();
			$line_item->setOrderLineDetails($order_line_details);
			$line_item->setAmountOfMoney($item_amount_of_money);
			
			$line_items[] = $line_item;
		}
				
		if ($item_total > $order_total) {
			$discount = new \OnlinePayments\Sdk\Domain\Discount();
			$discount->setAmount($item_total - $order_total);
		}

		$amount_of_money = new \OnlinePayments\Sdk\Domain\AmountOfMoney();
        $amount_of_money->setAmount($order_total);
        $amount_of_money->setCurrencyCode($currency_code);     		
		
		$shopping_cart = new \OnlinePayments\Sdk\Domain\ShoppingCart();
		$shopping_cart->setItems($line_items);

		$order = new \OnlinePayments\Sdk\Domain\Order();
		$order->setCustomer($customer);
		$order->setShipping($shipping);
		$order->setReferences($order_references);
		$order->setAmountOfMoney($amount_of_money);
		$order->setShoppingCart($shopping_cart);
		
		if (!empty($discount)) {
			$order->setDiscount($discount);
		}
		
		if ($setting['advanced']['surcharging_status']) {
			$surcharge_specific_input = new \OnlinePayments\Sdk\Domain\SurchargeSpecificInput();
			$surcharge_specific_input->setMode('on-behalf-of');
			
			$order->setSurchargeSpecificInput($surcharge_specific_input);
		}
		
		$redirection_data = new \OnlinePayments\Sdk\Domain\RedirectionData();
		$redirection_data->setReturnUrl(str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'callback', 'language=' . $this->config->get('config_language'))));
				
		$three_d_secure = new \OnlinePayments\Sdk\Domain\ThreeDSecure();
		$three_d_secure->setRedirectionData($redirection_data);
		$three_d_secure->setChallengeIndicator($setting['advanced']['tds_challenge_indicator']);
		$three_d_secure->setExemptionRequest($setting['advanced']['tds_exemption_request']);
		$three_d_secure->setSkipAuthentication(false);
		
		$payment_product_130_three_d_secure = new \OnlinePayments\Sdk\Domain\PaymentProduct130SpecificThreeDSecure();
		$payment_product_130_three_d_secure->setUsecase('single-amount');
		$payment_product_130_three_d_secure->setNumberOfItems(5);
		
		$payment_product_130_specific_input = new \OnlinePayments\Sdk\Domain\PaymentProduct130SpecificInput();
		$payment_product_130_specific_input->setThreeDSecure($payment_product_130_three_d_secure);
						
		$card_payment_method_specific_input = new \OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInput();
		$card_payment_method_specific_input->setAuthorizationMode($authorization_mode);
		$card_payment_method_specific_input->setTransactionChannel('ECOMMERCE');
		$card_payment_method_specific_input->setReturnUrl(str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'callback', 'language=' . $this->config->get('config_language'))));
		$card_payment_method_specific_input->setPaymentProduct130SpecificInput($payment_product_130_specific_input);
		
		if ($setting['advanced']['tds_status']) {
			$card_payment_method_specific_input->setSkipAuthentication(false);
			$card_payment_method_specific_input->setThreeDSecure($three_d_secure);
		} else {
			$card_payment_method_specific_input->setSkipAuthentication(true);
		}
				
		if (!empty($this->request->get['hostedTokenizationId'])) {
			$hosted_tokenization_id = $this->request->get['hostedTokenizationId'];
			$card_token_save = $this->request->get['cardTokenSave'];
			
			if ($card_token_save || $setting['advanced']['forced_tokenization']) {
				$tokenize = 1;
			} else {
				$tokenize = 0;
			}
						
			$create_payment_request = new \OnlinePayments\Sdk\Domain\CreatePaymentRequest();
			$create_payment_request->setHostedTokenizationId($hosted_tokenization_id);
			$create_payment_request->setOrder($order);
			$create_payment_request->setCardPaymentMethodSpecificInput($card_payment_method_specific_input);
						
			$errors = [];

			try {
				$create_payment_response = $client->merchant($merchant_id)->payments()->createPayment($create_payment_request);
				
			} catch (\OnlinePayments\Sdk\ResponseException $exception) {		
				$errors = $exception->getResponse()->getErrors();
									
				if ($errors) {
					$error_messages = [];
						
					foreach ($errors as $error) {
						$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
						
						$error_messages[] = $error->getMessage() . ' (' . $error->getCode() . ')';
					}	
					
					$this->error['warning'] = implode('. ', $error_messages);
				}
			}
			
			if (!empty($this->error['warning'])) {
				$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
			}
			
			if (!$errors) {
				$merchant_action = $create_payment_response->getMerchantAction();
				$transaction_id = $create_payment_response->getPayment()->getId();
								
				$this->model_extension_worldline_payment_worldline->deleteWorldlineOrder($order_id);
											
				$worldline_order_data = [
					'order_id' => $order_id,
					'transaction_id' => $transaction_id,
					'tokenize' => $tokenize,
					'total' => ($order_info['total'] * $currency_value),
					'currency_code' => $currency_code,
					'country_code' => (!empty($country_info['iso_code_2']) ? $country_info['iso_code_2'] : ''),
					'environment' => $environment,
					'date_captured' => $date_captured
				];

				$this->model_extension_worldline_payment_worldline->addWorldlineOrder($worldline_order_data);
				
				if (!empty($merchant_action) && ($merchant_action->getActionType() == 'REDIRECT')) {
					$data['redirect'] = $merchant_action->getRedirectData()->getRedirectURL();
				} else {
					$data['redirect'] = str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'callback', 'paymentId=' . $transaction_id . '&language=' . $this->config->get('config_language')));
				}
			}
		}	
								
		if (empty($this->request->get['hostedTokenizationId'])) {
			$tokenize = 1;
			
			if ($setting['advanced']['forced_tokenization']) {
				$card_payment_method_specific_input->setTokenize(true);
			} else {
				$card_payment_method_specific_input->setTokenize(false);
			}
			
			$redirect_payment_product_900_specific_input = new \OnlinePayments\Sdk\Domain\RedirectPaymentProduct900SpecificInput();
			
			if (($authorization_mode == 'PRE_AUTHORIZATION') || ($authorization_mode == 'FINAL_AUTHORIZATION')) {
				$redirect_payment_product_900_specific_input->setCaptureTrigger($setting['hosted_checkout']['wero_capture_trigger']);
			}
			
			$redirect_payment_product_5408_specific_input = new \OnlinePayments\Sdk\Domain\RedirectPaymentProduct5408SpecificInput();
			$redirect_payment_product_5408_specific_input->setInstantPaymentOnly(true);
			
			$redirect_payment_method_specific_input = new \OnlinePayments\Sdk\Domain\RedirectPaymentMethodSpecificInput();
			$redirect_payment_method_specific_input->setPaymentProduct900SpecificInput($redirect_payment_product_900_specific_input);						
			$redirect_payment_method_specific_input->setRedirectionData($redirection_data);
			$redirect_payment_method_specific_input->setPaymentProduct5408SpecificInput($redirect_payment_product_5408_specific_input);
			$redirect_payment_method_specific_input->setReturnUrl(str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'callback', 'language=' . $this->config->get('config_language'))));
			
			if ($authorization_mode == 'SALE') {
				$redirect_payment_method_specific_input->setRequiresApproval(false);
			} else {
				$redirect_payment_method_specific_input->setRequiresApproval(true);
			}
							
			if ($setting['advanced']['forced_tokenization']) {
				$redirect_payment_method_specific_input->setTokenize(true);
			} else {
				$redirect_payment_method_specific_input->setTokenize(false);
			}
				
			$mobile_payment_method_specific_input = new \OnlinePayments\Sdk\Domain\MobilePaymentMethodSpecificInput();
			$mobile_payment_method_specific_input->setAuthorizationMode($authorization_mode);
			
			$card_payment_method_specific_input_for_hosted_checkout = new \OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInputForHostedCheckout();
			$card_payment_method_specific_input_for_hosted_checkout->setGroupCards((bool)$setting['hosted_checkout']['group_cards']);
			
			$hosted_checkout_specific_input = new \OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput();
			$hosted_checkout_specific_input->setLocale($language_code_1 . '_' . strtoupper($language_code_2));
			$hosted_checkout_specific_input->setReturnUrl(str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'callback', 'language=' . $this->config->get('config_language'))));
			$hosted_checkout_specific_input->setCardPaymentMethodSpecificInput($card_payment_method_specific_input_for_hosted_checkout);
			
			if ($setting['hosted_checkout']['template']) {
				$hosted_checkout_specific_input->setVariant($setting['hosted_checkout']['template']);
			}
			
			if ($tokens) {
				$hosted_checkout_specific_input->setTokens(implode(',', $tokens));
			}
			
			$create_hosted_checkout_request = new \OnlinePayments\Sdk\Domain\CreateHostedCheckoutRequest();
			$create_hosted_checkout_request->setOrder($order);
			$create_hosted_checkout_request->setCardPaymentMethodSpecificInput($card_payment_method_specific_input);
			$create_hosted_checkout_request->setRedirectPaymentMethodSpecificInput($redirect_payment_method_specific_input);
			$create_hosted_checkout_request->setMobilePaymentMethodSpecificInput($mobile_payment_method_specific_input);
			$create_hosted_checkout_request->setHostedCheckoutSpecificInput($hosted_checkout_specific_input);
			
			$errors = [];

			try {
				$create_hosted_checkout_response = $client->merchant($merchant_id)->hostedCheckout()->createHostedCheckout($create_hosted_checkout_request);
			} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
				$errors = $exception->getResponse()->getErrors();
									
				if ($errors) {
					$error_messages = [];
						
					foreach ($errors as $error) {
						$this->model_extension_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
						
						$error_messages[] = $error->getMessage() . ' (' . $error->getCode() . ')';
					}	
					
					$this->error['warning'] = implode('. ', $error_messages);
				}
			}
			
			if (!empty($this->error['warning'])) {
				$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
			}
			
			if (!$errors) {
				$hosted_checkout_id = $create_hosted_checkout_response->getHostedCheckoutId();
				$hosted_checkout_url = $create_hosted_checkout_response->getRedirectUrl();
				
				$this->model_extension_worldline_payment_worldline->deleteWorldlineOrder($order_id);
											
				$worldline_order_data = [
					'order_id' => $order_id,
					'tokenize' => $tokenize,
					'total' => ($order_info['total'] * $currency_value),
					'currency_code' => $currency_code,
					'country_code' => (!empty($country_info['iso_code_2']) ? $country_info['iso_code_2'] : ''),
					'environment' => $environment,
					'date_captured' => $date_captured
				];

				$this->model_extension_worldline_payment_worldline->addWorldlineOrder($worldline_order_data);
				
				$data['redirect'] = $hosted_checkout_url;
			}
		}
							
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
	
	public function callback(): bool {
		if (!empty($this->request->get['hostedCheckoutId']) || !empty($this->request->get['paymentId'])) {
			$this->load->model('extension/worldline/payment/worldline');
					
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
			$_config->load('worldline');
			
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
						
			$environment = $setting['account']['environment'];
			$merchant_id = $setting['account']['merchant_id'][$environment];
			$api_key = $setting['account']['api_key'][$environment];
			$api_secret = $setting['account']['api_secret'][$environment];
			$api_endpoint = $setting['account']['api_endpoint'][$environment];
			$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
			require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
			$connection = new \OnlinePayments\Sdk\DefaultConnection();	

			$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

			$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
			$client = new \OnlinePayments\Sdk\Client($communicator);
			
			$errors = [];
			
			$transaction_id = '';
			
			if (!empty($this->request->get['hostedCheckoutId'])) {
				$hosted_checkout_id = $this->request->get['hostedCheckoutId'];
				
				try {
					$hosted_checkout_response = $client->merchant($merchant_id)->hostedCheckout()->getHostedCheckout($hosted_checkout_id);
				} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
					$errors = $exception->getResponse()->getErrors();
									
					if ($errors) {
						foreach ($errors as $error) {
							$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
						}	
					}
				}
				
				if (!$errors && !empty($hosted_checkout_response->getCreatedPaymentOutput())) {
					$transaction_id = $hosted_checkout_response->getCreatedPaymentOutput()->getPayment()->getId();
				}
			} else {
				$transaction_id = $this->request->get['paymentId'];
			}
			
			try {
				$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($transaction_id);
			} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
				$errors = $exception->getResponse()->getErrors();
						
				if ($errors) {
					$error_messages = [];
			
					foreach ($errors as $error) {
						$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
			
						$error_messages[] = $error->getMessage() . ' (' . $error->getCode() . ')';
					}	
		
					$this->error['warning'] = implode('. ', $error_messages);
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
				$card_brand = '';
				$card_last_digits = '';
				$card_expiry = '';
										
				if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
					$payment_product_id = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
					$token = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
					$payment_type = 'card';
					$card_last_digits = str_replace('*', '', $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getCardNumber());
					$card_expiry = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getExpiryDate();
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
										
				$invoice_id = $merchant_reference;
				$invoice_array = explode('_', $invoice_id);
				$order_id = reset($invoice_array);
					
				$this->load->model('checkout/order');
					
				$worldline_order_info = $this->model_extension_worldline_payment_worldline->getWorldlineOrder($order_id);
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
						$this->model_checkout_order->addHistory($order_id, $order_status_id, '', true);
					}
						
					if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
						$payment_product = $worldline_order_info['payment_product'];
						$tokenize = $worldline_order_info['tokenize'];
						
						if (!$token) $tokenize = 0;
						if (!$tokenize) $token = '';
																		
						if (!$worldline_order_info['transaction_status']) {
							$payment_product_params = new \OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
							$payment_product_params->setCurrencyCode($currency_code);
							$payment_product_params->setCountryCode($worldline_order_info['country_code']);							
					
							try {
								$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
							} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
								$errors = $exception->getResponse()->getErrors();
							
								if ($errors) {
									foreach ($errors as $error) {
										$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
									}
								}
							}
			
							if (!$errors) {
								if (!empty($payment_product_response->getDisplayHints())) {
									if (!empty($payment_product_response->getPaymentProductGroup())) {
										$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
									}
					
									$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
									
									if ($payment_type == 'card') {
										$card_brand = $payment_product_response->getDisplayHints()->getLabel();
									}
								}
							}
						}
							
						$worldline_order_data = [
							'order_id' => $order_id,
							'transaction_id' => $transaction_id,
							'transaction_status' => $transaction_status,
							'payment_product' => $payment_product,
							'payment_type' => $payment_type,
							'tokenize' => $tokenize,
							'token' => $token,
							'card_brand' => $card_brand,
							'card_last_digits' => $card_last_digits,
							'card_expiry' => $card_expiry,
							'total' => $total,
							'amount' => $amount,
							'currency_code' => $currency_code
						];
							
						$this->model_extension_worldline_payment_worldline->editWorldlineOrder($worldline_order_data);
							
						if ($this->customer->isLogged() && $token) {
							$customer_id = $this->customer->getId();
							
							$worldline_customer_token_info = $this->model_extension_worldline_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
							
							if (!$worldline_customer_token_info) {
								$worldline_customer_token_data = [
									'customer_id' => $customer_id,
									'payment_type' => $payment_type,
									'token' => $token,
									'card_brand' => $card_brand,
									'card_last_digits' => $card_last_digits,
									'card_expiry' => $card_expiry
								];
								
								$this->model_extension_worldline_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
							}
							
							$this->model_extension_worldline_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
						}
					}
						
					if (($transaction_status == 'pending_capture') || ($transaction_status == 'captured')) {
						$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
					}
						
					if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
						$this->response->redirect($this->url->link('extension/worldline/payment/worldline' . $this->separator . 'failurePage', 'language=' . $this->config->get('config_language')));
					}
				}
			}
			
			$this->response->redirect($this->url->link('extension/worldline/payment/worldline' . $this->separator . 'waitingPage', 'language=' . $this->config->get('config_language')));
			
			return true;
		}
		
		return false;
	}
	
	public function waitingPage(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('text_waiting_page_title'));
						
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'waitingPage', 'language=' . $this->config->get('config_language'))
		];

		$data['text_title'] = $this->language->get('text_waiting_page_title');
		$data['text_message'] = $this->language->get('text_waiting_page_message');
		
		if (!empty($this->session->data['order_id'])) {
			$data['order_id'] = $this->session->data['order_id'];
		}
		
		$data['separator'] = $this->separator;
								
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/worldline/payment/waiting_page', $data));
	}
		
	public function pendingPage(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('text_pending_page_title'));
						
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'pendingPage', 'language=' . $this->config->get('config_language'))
		];
				
		$data['text_title'] = $this->language->get('text_pending_page_title');
		$data['text_message'] = $this->language->get('text_pending_page_message');
		
		$data['continue'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/worldline/payment/pending_page', $data));
	}
	
	public function failurePage(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('text_failure_page_title'));
						
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'failurePage', 'language=' . $this->config->get('config_language'))
		];
		
		$data['text_title'] = $this->language->get('text_failure_page_title');
		$data['text_message'] = sprintf($this->language->get('text_failure_page_message'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
		
		if (!empty($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
			
			$this->load->model('extension/worldline/payment/worldline');
			
			$worldline_order_info = $this->model_extension_worldline_payment_worldline->getWorldlineOrder($order_id);
					
			if ($worldline_order_info) {
				$transaction_id = $worldline_order_info['transaction_id'];
				$transaction_status = $worldline_order_info['transaction_status'];
				
				if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
					$data['text_message'] = $this->language->get('text_transaction_' . strtolower($transaction_status));
				}
			}
		}
		
		$data['continue'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/worldline/payment/failure_page', $data));
	}
	
	public function getPaymentInfo(): void {
		if (!empty($this->request->post['order_id'])) {
			$order_id = $this->request->post['order_id'];
			
			$this->load->model('extension/worldline/payment/worldline');
			$this->load->model('checkout/order');
			
			$worldline_order_info = $this->model_extension_worldline_payment_worldline->getWorldlineOrder($order_id);
			$order_info = $this->model_checkout_order->getOrder($order_id);
			
			if ($worldline_order_info && $order_info) {
				$transaction_id = $worldline_order_info['transaction_id'];
				$transaction_status = $worldline_order_info['transaction_status'];
				
				$data['redirect'] = '';
				
				if (($transaction_status == 'pending_capture') || ($transaction_status == 'captured')) {
					$data['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'));
				}
						
				if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
					$data['redirect'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'failurePage', 'language=' . $this->config->get('config_language'));
				}
			
				if (!$data['redirect']) {
					$_config = new \Opencart\System\Engine\Config();
					$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
					$_config->load('worldline');
			
					$config_setting = $_config->get('worldline_setting');
		
					$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
						
					$environment = $setting['account']['environment'];
					$merchant_id = $setting['account']['merchant_id'][$environment];
					$api_key = $setting['account']['api_key'][$environment];
					$api_secret = $setting['account']['api_secret'][$environment];
					$api_endpoint = $setting['account']['api_endpoint'][$environment];
					$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
					require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
					$connection = new \OnlinePayments\Sdk\DefaultConnection();	

					$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

					$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
					$client = new \OnlinePayments\Sdk\Client($communicator);
			
					$errors = [];
			
					try {
						$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($transaction_id);
					} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
						$errors = $exception->getResponse()->getErrors();
								
						if ($errors) {
							$error_messages = [];
					
							foreach ($errors as $error) {
								$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
					
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
						$card_brand = '';
						$card_last_digits = '';
						$card_expiry = '';
						
						if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
							$payment_product_id = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
							$token = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
							$payment_type = 'card';
							$card_last_digits = str_replace('*', '', $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getCardNumber());
							$card_expiry = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getExpiryDate();
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
							$this->model_checkout_order->addHistory($order_id, $order_status_id, '', true);
						}
						
						if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
							$payment_product = $worldline_order_info['payment_product'];
							$tokenize = $worldline_order_info['tokenize'];
						
							if (!$token) $tokenize = 0;
							if (!$tokenize) $token = '';
							
							if (!$worldline_order_info['transaction_status']) {
								$payment_product_params = new \OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
								$payment_product_params->setCurrencyCode($currency_code);
								$payment_product_params->setCountryCode($worldline_order_info['country_code']);							
						
								try {
									$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
								} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
									$errors = $exception->getResponse()->getErrors();
								
									if ($errors) {
										foreach ($errors as $error) {
											$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
										}
									}
								}
				
								if (!$errors) {
									if (!empty($payment_product_response->getDisplayHints())) {
										if (!empty($payment_product_response->getPaymentProductGroup())) {
											$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
										}
						
										$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
										
										if ($payment_type == 'card') {
											$card_brand = $payment_product_response->getDisplayHints()->getLabel();
										}
									}
								}
							}
							
							$worldline_order_data = [
								'order_id' => $order_id,
								'transaction_status' => $transaction_status,
								'payment_product' => $payment_product,
								'payment_type' => $payment_type,
								'tokenize' => $tokenize,
								'token' => $token,
								'card_brand' => $card_brand,
								'card_last_digits' => $card_last_digits,
								'card_expiry' => $card_expiry,
								'total' => $total,
								'amount' => $amount,
								'currency_code' => $currency_code
							];
							
							$this->model_extension_worldline_payment_worldline->editWorldlineOrder($worldline_order_data);
							
							if ($this->customer->isLogged() && $token) {
								$customer_id = $this->customer->getId();
								
								$worldline_customer_token_info = $this->model_extension_worldline_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
								
								if (!$worldline_customer_token_info) {
									$worldline_customer_token_data = [
										'customer_id' => $customer_id,
										'payment_type' => $payment_type,
										'token' => $token,
										'card_brand' => $card_brand,
										'card_last_digits' => $card_last_digits,
										'card_expiry' => $card_expiry
									];
									
									$this->model_extension_worldline_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
								}
								
								$this->model_extension_worldline_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
							}
						}
						
						if (($transaction_status == 'pending_capture') || ($transaction_status == 'captured')) {
							$data['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'));
						}
						
						if (($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded')) {
							$data['redirect'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'failurePage', 'language=' . $this->config->get('config_language'));
						}
					}
				}
				
				if (!$data['redirect']) {
					$data['redirect'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'pendingPage', 'language=' . $this->config->get('config_language'));
				}
			}
		}
		
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
					
	public function webhook(): bool {									
		if (!empty($this->request->get['webhook_token'])) {
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
			$_config->load('worldline');
		
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
		
			$webhook_info = json_decode(html_entity_decode(file_get_contents('php://input')), true);
			
			if (hash_equals($setting['account']['webhook_token'], $this->request->get['webhook_token']) && !empty($webhook_info['payment']['id']) && !empty($webhook_info['payment']['status'])) {	
				$this->load->language('extension/worldline/payment/worldline');
		
				$this->load->model('extension/worldline/payment/worldline');
				$this->load->model('checkout/order');
		
				$this->model_extension_worldline_payment_worldline->log($webhook_info, 'Webhook');
											
				$environment = $setting['account']['environment'];
				$merchant_id = $setting['account']['merchant_id'][$environment];
				$api_key = $setting['account']['api_key'][$environment];
				$api_secret = $setting['account']['api_secret'][$environment];
				$api_endpoint = $setting['account']['api_endpoint'][$environment];
				$webhook_key = $setting['account']['webhook_key'][$environment];
				$webhook_secret = $setting['account']['webhook_secret'][$environment];
				$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
				require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
				$secret_key_store = new \OnlinePayments\Sdk\Webhooks\InMemorySecretKeyStore([$webhook_key => $webhook_secret]);
				$helper = new \OnlinePayments\Sdk\Webhooks\WebhooksHelper($secret_key_store);
							
				try {
					$event = $helper->unmarshal(file_get_contents('php://input'), $this->getallheaders());
				} catch (\OnlinePayments\Sdk\Webhooks\SignatureValidationException $exception) {
					$errors = $exception->getResponse()->getErrors();
								
					if ($errors) {
						foreach ($errors as $error) {
							$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
						}
					}

					return false;
				}
				
				$connection = new \OnlinePayments\Sdk\DefaultConnection();	

				$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

				$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
				$client = new \OnlinePayments\Sdk\Client($communicator);
						
				$errors = [];
			
				try {
					$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($webhook_info['payment']['id']);
				} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
					$errors = $exception->getResponse()->getErrors();
								
					if ($errors) {
						foreach ($errors as $error) {
							$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
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
					$card_brand = '';
					$card_last_digits = '';
					$card_expiry = '';
					
					if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
						$payment_product_id = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
						$token = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
						$payment_type = 'card';
						$card_last_digits = str_replace('*', '', $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getCardNumber());
						$card_expiry = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getExpiryDate();
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
									
					$invoice_id = $merchant_reference;
					$invoice_array = explode('_', $invoice_id);
					$order_id = reset($invoice_array);
									
					$transaction_id = $webhook_info['payment']['id'];
										
					$worldline_order_info = $this->model_extension_worldline_payment_worldline->getWorldlineOrder($order_id);
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
					
						if ($order_status_id && ($order_info['order_status_id'] != $order_status_id) && !in_array($order_info['order_status_id'], $setting['final_order_status'])) {
							$this->model_checkout_order->addHistory($order_id, $order_status_id, '', true);
						}
						
						if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
							$payment_product = $worldline_order_info['payment_product'];
							$tokenize = $worldline_order_info['tokenize'];
						
							if (!$token) $tokenize = 0;
							if (!$tokenize) $token = '';
							
							if (!$worldline_order_info['transaction_status']) {
								$payment_product_params = new \OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
								$payment_product_params->setCurrencyCode($currency_code);
								$payment_product_params->setCountryCode($worldline_order_info['country_code']);							
						
								try {
									$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
								} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
									$errors = $exception->getResponse()->getErrors();
								
									if ($errors) {
										foreach ($errors as $error) {
											$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
										}
									}
								}
				
								if (!$errors) {
									if (!empty($payment_product_response->getDisplayHints())) {
										if (!empty($payment_product_response->getPaymentProductGroup())) {
											$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
										}
						
										$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
										
										if ($payment_type == 'card') {
											$card_brand = $payment_product_response->getDisplayHints()->getLabel();
										}
									}
								}
							}
							
							$worldline_order_data = [
								'order_id' => $order_id,
								'transaction_status' => $transaction_status,
								'payment_product' => $payment_product,
								'payment_type' => $payment_type,
								'tokenize' => $tokenize,
								'token' => $token,
								'card_brand' => $card_brand,
								'card_last_digits' => $card_last_digits,
								'card_expiry' => $card_expiry,
								'total' => $total,
								'amount' => $amount,
								'currency_code' => $currency_code
							];
							
							$this->model_extension_worldline_payment_worldline->editWorldlineOrder($worldline_order_data);
							
							if (!empty($order_info['customer_id']) && $token) {
								$customer_id = $order_info['customer_id'];
								
								$worldline_customer_token_info = $this->model_extension_worldline_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
								
								if (!$worldline_customer_token_info) {
									$worldline_customer_token_data = [
										'customer_id' => $customer_id,
										'payment_type' => $payment_type,
										'token' => $token,
										'card_brand' => $card_brand,
										'card_last_digits' => $card_last_digits,
										'card_expiry' => $card_expiry
									];
									
									$this->model_extension_worldline_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
								}
								
								$this->model_extension_worldline_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
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
	
	public function cron(): bool {
		if (!empty($this->request->get['cron_token'])) {
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
			$_config->load('worldline');
		
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
			
			if (hash_equals($setting['account']['cron_token'], $this->request->get['cron_token'])) {
				$this->load->model('extension/worldline/payment/worldline');
				$this->load->model('checkout/order');
	
				$waiting_worldline_orders = $this->model_extension_worldline_payment_worldline->getWaitingWorldlineOrders();
				$waiting_capture_worldline_orders = $this->model_extension_worldline_payment_worldline->getWaitingCaptureWorldlineOrders();
			
				if ($waiting_worldline_orders) {
					$environment = $setting['account']['environment'];
					$merchant_id = $setting['account']['merchant_id'][$environment];
					$api_key = $setting['account']['api_key'][$environment];
					$api_secret = $setting['account']['api_secret'][$environment];
					$api_endpoint = $setting['account']['api_endpoint'][$environment];
					$authorization_mode = strtoupper($setting['advanced']['authorization_mode']);
		
					require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
					$connection = new \OnlinePayments\Sdk\DefaultConnection();	

					$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, 'OnlinePayments');	

					$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
					$client = new \OnlinePayments\Sdk\Client($communicator);

					foreach ($waiting_worldline_orders as $waiting_worldline_order) {
						$order_id = $waiting_worldline_order['order_id'];
						$transaction_id = $waiting_worldline_order['transaction_id'];
						
						$order_info = $this->model_checkout_order->getOrder($order_id);
										
						$errors = [];
			
						try {
							$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($transaction_id);
						} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
							$errors = $exception->getResponse()->getErrors();
								
							if ($errors) {
								foreach ($errors as $error) {
									$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
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
							$card_brand = '';
							$card_last_digits = '';
							$card_expiry = '';
							
							if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
								$payment_product_id = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
								$token = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
								$payment_type = 'card';
								$card_last_digits = str_replace('*', '', $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getCardNumber());
								$card_expiry = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getExpiryDate();
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
							
							if ($order_status_id && ($order_info['order_status_id'] != $order_status_id) && !in_array($order_info['order_status_id'], $setting['final_order_status'])) {
								$this->model_checkout_order->addHistory($order_id, $order_status_id, '', true);
							}
						
							if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {
								$payment_product = $waiting_worldline_order['payment_product'];
								$tokenize = $waiting_worldline_order['tokenize'];
						
								if (!$token) $tokenize = 0;
								if (!$tokenize) $token = '';
							
								if (!$waiting_worldline_order['transaction_status']) {
									$payment_product_params = new \OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
									$payment_product_params->setCurrencyCode($currency_code);
									$payment_product_params->setCountryCode($waiting_worldline_order['country_code']);							
						
									try {
										$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($payment_product_id, $payment_product_params);
									} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
										$errors = $exception->getResponse()->getErrors();
								
										if ($errors) {
											foreach ($errors as $error) {
												$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
											}
										}
									}
				
									if (!$errors) {
										if (!empty($payment_product_response->getDisplayHints())) {
											if (!empty($payment_product_response->getPaymentProductGroup())) {
												$payment_product .= $payment_product_response->getPaymentProductGroup() . ' ';
											}
						
											$payment_product .= $payment_product_response->getDisplayHints()->getLabel();
											
											if ($payment_type == 'card') {
												$card_brand = $payment_product_response->getDisplayHints()->getLabel();
											}
										}
									}
								}
							
								$worldline_order_data = [
									'order_id' => $order_id,
									'transaction_status' => $transaction_status,
									'payment_product' => $payment_product,
									'payment_type' => $payment_type,
									'tokenize' => $tokenize,
									'token' => $token,
									'card_brand' => $card_brand,
									'card_last_digits' => $card_last_digits,
									'card_expiry' => $card_expiry,
									'total' => $total,
									'amount' => $amount,
									'currency_code' => $currency_code
								];
							
								$this->model_extension_worldline_payment_worldline->editWorldlineOrder($worldline_order_data);
							
								if (!empty($order_info['customer_id']) && $token) {
									$customer_id = $order_info['customer_id'];
								
									$worldline_customer_token_info = $this->model_extension_worldline_payment_worldline->getWorldlineCustomerToken($customer_id, $payment_type, $token);
								
									if (!$worldline_customer_token_info) {
										$worldline_customer_token_data = [
											'customer_id' => $customer_id,
											'payment_type' => $payment_type,
											'token' => $token,
											'card_brand' => $card_brand,
											'card_last_digits' => $card_last_digits,
											'card_expiry' => $card_expiry
										];
									
										$this->model_extension_worldline_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
									}
								
									$this->model_extension_worldline_payment_worldline->setWorldlineCustomerMainToken($customer_id, $payment_type, $token);	
								}
							}
						}
					}
					
					foreach ($waiting_capture_worldline_orders as $waiting_capture_worldline_order) {
						$order_id = $waiting_capture_worldline_order['order_id'];
						$transaction_id = $waiting_capture_worldline_order['transaction_id'];
						
						$order_info = $this->model_checkout_order->getOrder($order_id);
						 
						$errors = [];
						
						$capture_amount = 0;
			
						try {
							$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($transaction_id);
						} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
							$errors = $exception->getResponse()->getErrors();
											
							if ($errors) {
								$error_messages = [];
								
								foreach ($errors as $error) {
									$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
								}	
							}
						}

						if (!$errors) {
							$amount = $payment_response->getPaymentOutput()->getAcquiredAmount()->getAmount();
						
							$amount_captured = 0;
											
							foreach ($payment_response->getOperations() as $operation) {
								if (($operation->getStatus() == 'CAPTURED') && ($operation->getStatusOutput()->getStatusCategory() == 'COMPLETED')) { 
									$amount_captured += $operation->getAmountOfMoney()->getAmount();
								}
							}
						
							$capture_amount = $amount - $amount_captured;
						}
							
						if ($capture_amount) {
							$capture_payment_request = new \OnlinePayments\Sdk\Domain\CapturePaymentRequest();
							$capture_payment_request->setAmount($capture_amount);
							
							try {
								$capture_response = $client->merchant($merchant_id)->payments()->capturePayment($transaction_id, $capture_payment_request);
							} catch (\OnlinePayments\Sdk\ResponseException $exception) {
								$errors = $exception->getResponse()->getErrors();
												
								if ($errors) {
									$error_messages = [];
									
									foreach ($errors as $error) {
										$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
									
										$error_messages[] = $error->getMessage() . ' (' . $error->getCode() . ')';
									}	
								
									$this->error['warning'] = implode('. ', $error_messages);
								}
							}
						}
			
						if (!$errors) {
							try {
								$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($transaction_id);
							} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
								$errors = $exception->getResponse()->getErrors();
												
								if ($errors) {
									$error_messages = [];
									
									foreach ($errors as $error) {
										$this->model_extension_worldline_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
									}	
								}
							}
							
							if (!$errors) {
								$transaction_status = strtolower($payment_response->getStatus());
								$total = $payment_response->getPaymentOutput()->getAmountOfMoney()->getAmount() / 100;
								$amount = $payment_response->getPaymentOutput()->getAcquiredAmount()->getAmount() / 100;
								$currency_code = $payment_response->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode();
											
								if (($transaction_status == 'created') || ($transaction_status == 'pending_capture') || ($transaction_status == 'captured') || ($transaction_status == 'cancelled') || ($transaction_status == 'rejected') || ($transaction_status == 'rejected_capture') || ($transaction_status == 'refunded') || ($transaction_status == 'authorization_requested') || ($transaction_status == 'capture_requested') || ($transaction_status == 'refund_requested')) {					
									$worldline_order_data = [
										'order_id' => $order_id,
										'transaction_status' => $transaction_status,
										'total' => $total,
										'amount' => $amount,
										'currency_code' => $currency_code
									];
											
									$this->model_extension_worldline_payment_worldline->editWorldlineOrder($worldline_order_data);
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
	
	public function extension_get_extensions_by_type_after(string $route, array $data, array &$output): void {
		if ($this->config->get('payment_worldline_status')) {
			$type = $data[0];
			
			if ($type == 'payment') {
				$_config = new \Opencart\System\Engine\Config();
				$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
				$_config->load('worldline');
			
				$config_setting = $_config->get('worldline_setting');
		
				$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
				
				if ($setting['hosted_tokenization']['status']) {
					$this->config->set('payment_worldline_hosted_tokenization_status', 1);
					
					$output[] = [
						'extension_id' => 0,
						'extension' => 'worldline',
						'type' => 'payment',
						'code' => 'worldline_hosted_tokenization'
					];
				}
			}
		}
	}
	
	public function extension_get_extension_by_code_after(string $route, array $data, array &$output): void {
		if ($this->config->get('payment_worldline_status')) {
			$type = $data[0];
			$code = $data[1];
			
			if ($type == 'payment') {			
				$_config = new \Opencart\System\Engine\Config();
				$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
				$_config->load('worldline');
			
				$config_setting = $_config->get('worldline_setting');
		
				$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
				
				if (($code == 'worldline_hosted_tokenization') && $setting['hosted_tokenization']['status']) {
					$this->config->set('payment_worldline_hosted_tokenization_status', 1);
					
					$output = [
						'extension_id' => 0,
						'extension' => 'worldline',
						'type' => 'payment',
						'code' => 'worldline_hosted_tokenization'
					];
				}
			}
		}
	}
			
	public function order_delete_order_before(string $route, array $data): void {
		$this->load->model('extension/worldline/payment/worldline');

		$order_id = $data[0];

		$this->model_extension_worldline_payment_worldline->deleteWorldlineOrder($order_id);
	}
	
	private function getallheaders() {		
		if (function_exists('getallheaders')) {
			return getallheaders();
		} else {
			$headers = [];
			
			foreach ($_SERVER as $name => $value) {
				if (substr($name, 0, 5) == 'HTTP_') {
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			
			return $headers;
		}
	}
}