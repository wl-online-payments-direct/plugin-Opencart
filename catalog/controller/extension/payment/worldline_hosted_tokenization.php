<?php
class ControllerExtensionPaymentWorldlineHostedTokenization extends Controller {
	private $error = array();
			
	public function index() {			
		$_config = new Config();
		$_config->load('worldline');
		
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));

		$environment = $setting['account']['environment'];
		
		if ($setting['account']['api_key'][$environment] && $setting['account']['api_secret'][$environment]) {
			$this->load->language('extension/payment/worldline');	

			$language_id = $this->config->get('config_language_id');
			
			$data['surcharging_status'] = $setting['advanced']['surcharging_status'];
			
			$data['hosted_tokenization']['status'] = $setting['hosted_checkout']['status'];
			
			if (!empty($setting['hosted_tokenization']['button_title'][$language_id])) {
				$data['hosted_tokenization']['button_title'] = $setting['hosted_tokenization']['button_title'][$language_id];
			} else {
				$data['hosted_tokenization']['button_title'] = $this->language->get('button_hosted_tokenization_title');
			}
						
			$data['api_url'] = $setting['account']['api_endpoint'][$environment];
						
			return $this->load->view('extension/payment/worldline/worldline_hosted_tokenization', $data);
		}
		
		return '';
	}
	
	public function modal() {
		$this->load->language('extension/payment/worldline');
		
		$this->load->model('extension/payment/worldline');
				
		$_config = new Config();
		$_config->load('worldline');
			
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
		
		$extension = $setting['extension'];
		$environment = $setting['account']['environment'];
		$merchant_id = $setting['account']['merchant_id'][$environment];
		$api_key = $setting['account']['api_key'][$environment];
		$api_secret = $setting['account']['api_secret'][$environment];
		$api_endpoint = $setting['account']['api_endpoint'][$environment];
		
		$data['forced_tokenization'] = $setting['advanced']['forced_tokenization'];
		
		$data['logged'] = $this->customer->isLogged();
		
		$language_id = $this->config->get('config_language_id');
					
		if (!empty($setting['hosted_tokenization']['title'][$language_id])) {
			$data['text_worldline_title'] = $setting['hosted_tokenization']['title'][$language_id];
		} else {
			$data['text_worldline_title'] = $this->language->get('text_hosted_tokenization_title');
		}		
				
		require_once DIR_SYSTEM . 'library/worldline/OnlinePayments.php';
				
		$connection = new OnlinePayments\Sdk\DefaultConnection();

		$shopping_cart_extension = new OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

		$communicator_configuration = new OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
		$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

		$communicator = new OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
        $client = new OnlinePayments\Sdk\Client($communicator);
		
		$create_hosted_tokenization_request = new OnlinePayments\Sdk\Domain\CreateHostedTokenizationRequest();
					
		$tokens = array();
		
		if ($this->customer->isLogged()) {
			$card_customer_tokens = $this->model_extension_payment_worldline->getWorldlineCustomerTokens($this->customer->getId(), 'card');
			
			foreach ($card_customer_tokens as $card_customer_token) {				
				$tokens[] = $card_customer_token['token'];
			}
		}
		
		if ($tokens) {
			$create_hosted_tokenization_request->setTokens(implode(',', $tokens));
		}
			
		if ($setting['hosted_tokenization']['template']) {
			$create_hosted_tokenization_request->setVariant($setting['hosted_tokenization']['template']);
		}
					
		$errors = array();

		try {
			$create_hosted_tokenization_response = $client->merchant($merchant_id)->hostedTokenization()->createHostedTokenization($create_hosted_tokenization_request);
		} catch (OnlinePayments\Sdk\ResponseException $exception) {			
			$errors = $exception->getResponse()->getErrors();
								
			if ($errors) {
				$error_messages = array();
					
				foreach ($errors as $error) {
					$this->model_extension_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
					
					$error_messages[] = $error->getMessage() . ' (' . $error->getCode() . ')';
				}	
				
				$this->error['warning'] = implode('. ', $error_messages);
			}
		}
		
		if (!empty($this->error['warning'])) {
			$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', '', true));
		}
		
		if (!$errors) {			
			$data['hosted_tokenization_url'] = $create_hosted_tokenization_response->getHostedTokenizationUrl();
									
			$data['card_customer_tokens'] = array();
			
			if ($tokens) {
				$invalid_tokens = $create_hosted_tokenization_response->getInvalidTokens();
				
				foreach ($card_customer_tokens as $card_customer_token) {				
					if (!in_array($card_customer_token['token'], $invalid_tokens)) {
						$data['card_customer_tokens'][] = array(
							'token' => $card_customer_token['token'],
							'card_brand' => $card_customer_token['card_brand'],
							'card_number' => sprintf($this->language->get('text_card_masked_number'), $card_customer_token['card_brand'], $card_customer_token['card_last_digits'])
						);
					}
				}
			}
		}
																						
		$data['error'] = $this->error;

		$this->response->setOutput($this->load->view('extension/payment/worldline/worldline_hosted_tokenization_modal', $data));
	}
}