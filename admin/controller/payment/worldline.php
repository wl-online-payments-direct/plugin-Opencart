<?php
namespace Opencart\Admin\Controller\Extension\Worldline\Payment;
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
		
	public function index(): void {
		$this->account();
	}
	
	public function account(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['href_account'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'account', 'user_token=' . $this->session->data['user_token']);
		$data['href_advanced'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'advanced', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_checkout'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_checkout', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_tokenization'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_tokenization', 'user_token=' . $this->session->data['user_token']);
		$data['href_order_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'order_status', 'user_token=' . $this->session->data['user_token']);
		$data['href_transaction'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token']);
		$data['href_suggest'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'suggest', 'user_token=' . $this->session->data['user_token']);
				
		$data['server'] = HTTP_SERVER;
		$data['catalog'] = HTTP_CATALOG;
        					
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$data['setting'] = $_config->get('worldline_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_worldline_setting'));
				
		if (!$data['setting']['account']['webhook_token']) {
			$data['setting']['account']['webhook_token'] = sha1(uniqid(mt_rand(), 1));
		}
		
		if (!$data['setting']['account']['cron_token']) {
			$data['setting']['account']['cron_token'] = sha1(uniqid(mt_rand(), 1));
		}
			
		$data['save'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
		$data['sign_up'] = 'https://signup.direct.preprod.worldline-solutions.com/';
		$data['contact_us'] = 'https://docs.direct.worldline-solutions.com/en/about/contact/index';		
		$data['webhook_url'] = $data['catalog'] . 'index.php?route=extension/worldline/payment/worldline&webhook_token=' . $data['setting']['account']['webhook_token'];
		$data['cron_url'] = $data['catalog'] . 'index.php?route=extension/worldline/payment/worldline&cron_token=' . $data['setting']['account']['cron_token'];	

		$data['status'] = $this->config->get('payment_worldline_status');	
										
		$result = $this->model_extension_worldline_payment_worldline->checkVersion(VERSION, $data['setting']['extension']['version']);
		
		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/worldline/payment/account', $data));
	}
	
	public function advanced(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['href_account'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'account', 'user_token=' . $this->session->data['user_token']);
		$data['href_advanced'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'advanced', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_checkout'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_checkout', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_tokenization'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_tokenization', 'user_token=' . $this->session->data['user_token']);
		$data['href_order_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'order_status', 'user_token=' . $this->session->data['user_token']);
		$data['href_transaction'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token']);
		$data['href_suggest'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'suggest', 'user_token=' . $this->session->data['user_token']);
								
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$data['setting'] = $_config->get('worldline_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_worldline_setting'));
							
		$data['save'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
		$data['sign_up'] = 'https://signup.direct.preprod.worldline-solutions.com/';
		$data['contact_us'] = 'https://docs.direct.worldline-solutions.com/en/about/contact/index';					
		
		$data['geo_zone_id'] = $this->config->get('payment_worldline_geo_zone_id');
		$data['sort_order'] = $this->config->get('payment_worldline_sort_order');
				
		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
										
		$result = $this->model_extension_worldline_payment_worldline->checkVersion(VERSION, $data['setting']['extension']['version']);
		
		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/worldline/payment/advanced', $data));
	}
	
	public function hosted_checkout(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['href_account'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'account', 'user_token=' . $this->session->data['user_token']);
		$data['href_advanced'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'advanced', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_checkout'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_checkout', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_tokenization'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_tokenization', 'user_token=' . $this->session->data['user_token']);
		$data['href_order_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'order_status', 'user_token=' . $this->session->data['user_token']);
		$data['href_transaction'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token']);
		$data['href_suggest'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'suggest', 'user_token=' . $this->session->data['user_token']);
									
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$data['setting'] = $_config->get('worldline_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_worldline_setting'));
							
		$data['save'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
		$data['sign_up'] = 'https://signup.direct.preprod.worldline-solutions.com/';
		$data['contact_us'] = 'https://docs.direct.worldline-solutions.com/en/about/contact/index';					
				
		$this->load->model('localisation/language');
		
		$data['languages'] = [];
		
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			$language_code = explode('-', $language['code']);
			$language_code = strtoupper(reset($language_code));
			
			$data['languages'][] = [
				'language_id' => $language['language_id'],
				'language_code' => $language_code,
				'code' => $language['code'],
				'name' => $language['name']
			];
			
			$_language = new \Opencart\System\Library\Language($language['code']);
			$_language->addPath(DIR_EXTENSION . 'worldline/admin/language/');
			$_language->load('payment/worldline');
			
			if (file_exists(DIR_EXTENSION . 'worldline/admin/language/' . $language['code'] . '/payment/worldline.php')) {
				if (empty($data['setting']['hosted_checkout']['title'][$language['language_id']])) {
					$data['setting']['hosted_checkout']['title'][$language['language_id']] = $_language->get('text_hosted_checkout_title');
				}
			
				if (empty($data['setting']['hosted_checkout']['button_title'][$language['language_id']])) {
					$data['setting']['hosted_checkout']['button_title'][$language['language_id']] = $_language->get('button_hosted_checkout_title');
				}
			} else {
				if (empty($data['setting']['hosted_checkout']['title'][$language['language_id']])) {
					$data['setting']['hosted_checkout']['title'][$language['language_id']] = $this->language->get('text_hosted_checkout_title');
				}
			
				if (empty($data['setting']['hosted_checkout']['button_title'][$language['language_id']])) {
					$data['setting']['hosted_checkout']['button_title'][$language['language_id']] = $this->language->get('button_hosted_checkout_title');
				}
			}
		}
								
		$result = $this->model_extension_worldline_payment_worldline->checkVersion(VERSION, $data['setting']['extension']['version']);
		
		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/worldline/payment/hosted_checkout', $data));
	}
	
	public function hosted_tokenization(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['href_account'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'account', 'user_token=' . $this->session->data['user_token']);
		$data['href_advanced'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'advanced', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_checkout'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_checkout', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_tokenization'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_tokenization', 'user_token=' . $this->session->data['user_token']);
		$data['href_order_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'order_status', 'user_token=' . $this->session->data['user_token']);
		$data['href_transaction'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token']);
		$data['href_suggest'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'suggest', 'user_token=' . $this->session->data['user_token']);
									
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$data['setting'] = $_config->get('worldline_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_worldline_setting'));
							
		$data['save'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
		$data['sign_up'] = 'https://signup.direct.preprod.worldline-solutions.com/';
		$data['contact_us'] = 'https://docs.direct.worldline-solutions.com/en/about/contact/index';					
				
		$this->load->model('localisation/language');
		
		$data['languages'] = [];
		
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			$language_code = explode('-', $language['code']);
			$language_code = strtoupper(reset($language_code));
			
			$data['languages'][] = [
				'language_id' => $language['language_id'],
				'language_code' => $language_code,
				'code' => $language['code'],
				'name' => $language['name']
			];
			
			$_language = new \Opencart\System\Library\Language($language['code']);
			$_language->addPath(DIR_EXTENSION . 'worldline/admin/language/');
			$_language->load('payment/worldline');
			
			if (file_exists(DIR_EXTENSION . 'worldline/admin/language/' . $language['code'] . '/payment/worldline.php')) {
				if (empty($data['setting']['hosted_tokenization']['title'][$language['language_id']])) {
					$data['setting']['hosted_tokenization']['title'][$language['language_id']] = $_language->get('text_hosted_tokenization_title');
				}
			
				if (empty($data['setting']['hosted_tokenization']['button_title'][$language['language_id']])) {
					$data['setting']['hosted_tokenization']['button_title'][$language['language_id']] = $_language->get('button_hosted_tokenization_title');
				}
			} else {
				if (empty($data['setting']['hosted_tokenization']['title'][$language['language_id']])) {
					$data['setting']['hosted_tokenization']['title'][$language['language_id']] = $this->language->get('text_hosted_tokenization_title');
				}
			
				if (empty($data['setting']['hosted_tokenization']['button_title'][$language['language_id']])) {
					$data['setting']['hosted_tokenization']['button_title'][$language['language_id']] = $this->language->get('button_hosted_tokenization_title');
				}
			}
		}
								
		$result = $this->model_extension_worldline_payment_worldline->checkVersion(VERSION, $data['setting']['extension']['version']);
		
		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/worldline/payment/hosted_tokenization', $data));
	}
	
	public function order_status(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['href_account'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'account', 'user_token=' . $this->session->data['user_token']);
		$data['href_advanced'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'advanced', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_checkout'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_checkout', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_tokenization'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_tokenization', 'user_token=' . $this->session->data['user_token']);
		$data['href_order_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'order_status', 'user_token=' . $this->session->data['user_token']);
		$data['href_transaction'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token']);
		$data['href_suggest'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'suggest', 'user_token=' . $this->session->data['user_token']);
									
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$data['setting'] = $_config->get('worldline_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_worldline_setting'));
							
		$data['save'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
		$data['sign_up'] = 'https://signup.direct.preprod.worldline-solutions.com/';
		$data['contact_us'] = 'https://docs.direct.worldline-solutions.com/en/about/contact/index';					
		
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
								
		$result = $this->model_extension_worldline_payment_worldline->checkVersion(VERSION, $data['setting']['extension']['version']);
		
		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/worldline/payment/order_status', $data));
	}
	
	public function transaction(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
		
		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_transaction_id'])) {
			$url .= '&filter_transaction_id=' . $this->request->get['filter_transaction_id'];
		}

		if (isset($this->request->get['filter_transaction_status'])) {
			$url .= '&filter_transaction_status=' . $this->request->get['filter_transaction_status'];
		}
		
		if (isset($this->request->get['filter_payment_product'])) {
			$url .= '&filter_payment_product=' . $this->request->get['filter_payment_product'];
		}
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		}
		
		if (isset($this->request->get['filter_currency_code'])) {
			$url .= '&filter_currency_code=' . $this->request->get['filter_currency_code'];
		}

		if (isset($this->request->get['filter_date_created_from'])) {
			$url .= '&filter_date_created_from=' . $this->request->get['filter_date_created_from'];
		}

		if (isset($this->request->get['filter_date_created_to'])) {
			$url .= '&filter_date_created_to=' . $this->request->get['filter_date_created_to'];
		}
		
		if (isset($this->request->get['filter_environment'])) {
			$url .= '&filter_environment=' . $this->request->get['filter_environment'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['href_account'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'account', 'user_token=' . $this->session->data['user_token']);
		$data['href_advanced'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'advanced', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_checkout'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_checkout', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_tokenization'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_tokenization', 'user_token=' . $this->session->data['user_token']);
		$data['href_order_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'order_status', 'user_token=' . $this->session->data['user_token']);
		$data['href_transaction'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token']);
		$data['href_suggest'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'suggest', 'user_token=' . $this->session->data['user_token']);
										
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$data['setting'] = $_config->get('worldline_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_worldline_setting'));
							
		$data['action'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
		$data['sign_up'] = 'https://signup.direct.preprod.worldline-solutions.com/';
		$data['contact_us'] = 'https://docs.direct.worldline-solutions.com/en/about/contact/index';	
		$data['capture_url'] =  str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'capturePayment', 'user_token=' . $this->session->data['user_token']));
		$data['cancel_url'] =  str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'cancelPayment', 'user_token=' . $this->session->data['user_token']));
		$data['refund_url'] =  str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'refundPayment', 'user_token=' . $this->session->data['user_token']));

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
		
		if (isset($this->request->get['filter_transaction_id'])) {
			$filter_transaction_id = $this->request->get['filter_transaction_id'];
		} else {
			$filter_transaction_id = null;
		}
		
		if (isset($this->request->get['filter_transaction_status'])) {
			$filter_transaction_status = $this->request->get['filter_transaction_status'];
		} else {
			$filter_transaction_status = null;
		}
		
		if (isset($this->request->get['filter_payment_product'])) {
			$filter_payment_product = $this->request->get['filter_payment_product'];
		} else {
			$filter_payment_product = null;
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}
		
		if (isset($this->request->get['filter_amount'])) {
			$filter_amount = $this->request->get['filter_amount'];
		} else {
			$filter_amount = null;
		}
		
		if (isset($this->request->get['filter_currency_code'])) {
			$filter_currency_code = $this->request->get['filter_currency_code'];
		} else {
			$filter_currency_code = null;
		}
		
		if (isset($this->request->get['filter_date_created_from'])) {
			$filter_date_created_from = date('Y-m-d', strtotime($this->request->get['filter_date_created_from']));
		} else {
			$filter_date_created_from = null;
		}
		
		if (isset($this->request->get['filter_date_created_to'])) {
			$filter_date_created_to = date('Y-m-d', strtotime($this->request->get['filter_date_created_to']));
		} else {
			$filter_date_created_to = null;
		}
		
		if (isset($this->request->get['filter_environment'])) {
			$filter_environment = $this->request->get['filter_environment'];
		} else {
			$filter_environment = null;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'wo.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$data['orders'] = [];

		$filter_data = [
			'filter_order_id'     			=> $filter_order_id,
			'filter_transaction_id'     	=> $filter_transaction_id,
			'filter_transaction_status'     => $filter_transaction_status,
			'filter_payment_product'     	=> $filter_payment_product,
			'filter_total'         			=> $filter_total,
			'filter_amount'         		=> $filter_amount,
			'filter_currency_code'         	=> $filter_currency_code,
			'filter_date_created_from'    	=> $filter_date_created_from,
			'filter_date_created_to'		=> $filter_date_created_to,
			'filter_environment'	 	   	=> $filter_environment,
			'sort'                		    => $sort,
			'order'                			=> $order,
			'start'                			=> ($page - 1) * (int)$this->config->get('config_pagination_admin'),
			'limit'                			=> (int)$this->config->get('config_pagination_admin')
		];
		
		$order_total = $this->model_extension_worldline_payment_worldline->getTotalWorldlineOrders($filter_data);
		
		$results = $this->model_extension_worldline_payment_worldline->getWorldlineOrders($filter_data);

		foreach ($results as $result) {
			$transaction_number = preg_replace('/_[0-9]+/', '', $result['transaction_id']);
			
			if ($result['environment'] == 'production') {
				$transaction_url = 'https://merchant-portal.worldline-solutions.com/transactions/online/' . $transaction_number;
			} else {
				$transaction_url = 'https://merchant-portal.preprod.worldline-solutions.com/transactions/online/' . $transaction_number;
			}
			
			if ($result['date_created']) {
				$date_created = date('Y-m-d H:i', strtotime($result['date_created']));
			} else {
				$date_created = '';
			}

			$data['orders'][] = [
				'order_id'     		    => $result['order_id'],
				'transaction_id' 		=> $result['transaction_id'],
				'transaction_number' 	=> $transaction_number,
				'transaction_status' 	=> $result['transaction_status'],
				'payment_product' 		=> $result['payment_product'],
				'total'         		=> $result['total'],
				'amount'         		=> $result['amount'],
				'currency_code'         => $result['currency_code'],
				'environment'			=> $result['environment'],
				'date_created'    		=> $date_created,
				'order_url'          	=> $this->url->link('sale/order' . $this->separator . 'info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id']),
				'transaction_url'       => $transaction_url
			];
		}
				
		$data['user_token'] = $this->session->data['user_token'];
		
		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_transaction_id'])) {
			$url .= '&filter_transaction_id=' . $this->request->get['filter_transaction_id'];
		}

		if (isset($this->request->get['filter_transaction_status'])) {
			$url .= '&filter_transaction_status=' . $this->request->get['filter_transaction_status'];
		}
		
		if (isset($this->request->get['filter_payment_product'])) {
			$url .= '&filter_payment_product=' . $this->request->get['filter_payment_product'];
		}
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		}
		
		if (isset($this->request->get['filter_currency_code'])) {
			$url .= '&filter_currency_code=' . $this->request->get['filter_currency_code'];
		}

		if (isset($this->request->get['filter_date_created_from'])) {
			$url .= '&filter_date_created_from=' . $this->request->get['filter_date_created_from'];
		}

		if (isset($this->request->get['filter_date_created_to'])) {
			$url .= '&filter_date_created_to=' . $this->request->get['filter_date_created_to'];
		}
		
		if (isset($this->request->get['filter_environment'])) {
			$url .= '&filter_environment=' . $this->request->get['filter_environment'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order_id'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.order_id' . $url);
		$data['sort_transaction_id'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.transaction_id' . $url);
		$data['sort_transaction_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.transaction_status' . $url);
		$data['sort_payment_product'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.payment_product' . $url);
		$data['sort_total'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.total' . $url);
		$data['sort_amount'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.amount' . $url);
		$data['sort_currency_code'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.currency_code' . $url);
		$data['sort_date_created'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.date_created' . $url);
		$data['sort_environment'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . '&sort=wo.environment' . $url);
		
		$url = '';
		
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_transaction_id'])) {
			$url .= '&filter_transaction_id=' . $this->request->get['filter_transaction_id'];
		}

		if (isset($this->request->get['filter_transaction_status'])) {
			$url .= '&filter_transaction_status=' . $this->request->get['filter_transaction_status'];
		}
		
		if (isset($this->request->get['filter_payment_product'])) {
			$url .= '&filter_payment_product=' . $this->request->get['filter_payment_product'];
		}
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		}
		
		if (isset($this->request->get['filter_currency_code'])) {
			$url .= '&filter_currency_code=' . $this->request->get['filter_currency_code'];
		}

		if (isset($this->request->get['filter_date_created_from'])) {
			$url .= '&filter_date_created_from=' . $this->request->get['filter_date_created_from'];
		}

		if (isset($this->request->get['filter_date_created_to'])) {
			$url .= '&filter_date_created_to=' . $this->request->get['filter_date_created_to'];
		}
		
		if (isset($this->request->get['filter_environment'])) {
			$url .= '&filter_environment=' . $this->request->get['filter_environment'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $order_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($order_total - $this->config->get('config_pagination_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $order_total, ceil($order_total / $this->config->get('config_pagination_admin')));
		
		$data['filter_order_id'] = $filter_order_id;
		$data['filter_transaction_id'] = $filter_transaction_id;
		$data['filter_transaction_status'] = $filter_transaction_status;
		$data['filter_payment_product'] = $filter_payment_product;
		$data['filter_total'] = $filter_total;
		$data['filter_amount'] = $filter_amount;
		$data['filter_currency_code'] = $filter_currency_code;
		$data['filter_date_created_from'] = $filter_date_created_from;
		$data['filter_date_created_to'] = $filter_date_created_to;
		$data['filter_environment'] = $filter_environment;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['page'] = $page;
		
		$this->load->model('localisation/currency');
		
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
								
		$result = $this->model_extension_worldline_payment_worldline->checkVersion(VERSION, $data['setting']['extension']['version']);

		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['separator'] = $this->separator;
		
		if (version_compare(VERSION, '4.1.0.0', '>=')) {
			$data['daterangepicker'] = false;
		} else {
			$data['daterangepicker'] = true;
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/worldline/payment/transaction', $data));
	}
	
	public function suggest(): void {
		$this->load->language('extension/worldline/payment/worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/worldline/payment/worldline', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['href_account'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'account', 'user_token=' . $this->session->data['user_token']);
		$data['href_advanced'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'advanced', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_checkout'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_checkout', 'user_token=' . $this->session->data['user_token']);
		$data['href_hosted_tokenization'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'hosted_tokenization', 'user_token=' . $this->session->data['user_token']);
		$data['href_order_status'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'order_status', 'user_token=' . $this->session->data['user_token']);
		$data['href_transaction'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'transaction', 'user_token=' . $this->session->data['user_token']);
		$data['href_suggest'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'suggest', 'user_token=' . $this->session->data['user_token']);
									
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$data['setting'] = $_config->get('worldline_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_worldline_setting'));
							
		$data['save'] = $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
		$data['sign_up'] = 'https://signup.direct.preprod.worldline-solutions.com/';
		$data['contact_us'] = 'https://docs.direct.worldline-solutions.com/en/about/contact/index';	
		$data['suggest_url'] = str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'sendSuggest', 'user_token=' . $this->session->data['user_token']));								
		$result = $this->model_extension_worldline_payment_worldline->checkVersion(VERSION, $data['setting']['extension']['version']);
		
		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/worldline/payment/suggest', $data));
	}
	
	public function save(): void {
		$this->load->language('extension/worldline/payment/worldline');
		
		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('setting/setting');
						
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSave()) {
			$setting = $this->model_setting_setting->getSetting('payment_worldline');
			
			if (!empty($this->request->post['payment_worldline_setting']['order_status'])) {
				$setting['payment_worldline_setting']['final_order_status'] = [];
			}
			
			$setting = array_replace_recursive($setting, $this->request->post);
						
			$this->model_setting_setting->editSetting('payment_worldline', $setting);
														
			$data['success'] = $this->language->get('success_save');
		}
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function sendSuggest(): void {
		$this->load->language('extension/worldline/payment/worldline');
		
		$this->load->model('extension/worldline/payment/worldline');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSendSuggest()) {
			$this->model_extension_worldline_payment_worldline->sendSuggest($this->request->post);
			
			$data['success'] = $this->language->get('success_send_suggest');
		}
		
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
	
	public function install(): void {		
		$this->load->model('extension/worldline/payment/worldline');
		
		$this->model_extension_worldline_payment_worldline->install();
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('worldline_order_info');
		$this->model_setting_event->deleteEventByCode('worldline_extension_get_extensions_by_type');
		$this->model_setting_event->deleteEventByCode('worldline_extension_get_extension_by_code');
		$this->model_setting_event->deleteEventByCode('worldline_order_delete_order');
		$this->model_setting_event->deleteEventByCode('worldline_customer_delete_customer');
		
		if (version_compare(VERSION, '4.0.2.0', '>=')) {
			$this->model_setting_event->addEvent(['code' => 'worldline_order_info', 'description' => '', 'trigger' => 'admin/view/sale/order_info/before', 'action' => 'extension/worldline/payment/worldline.order_info_before', 'status' => true, 'sort_order' => 1]);
			$this->model_setting_event->addEvent(['code' => 'worldline_order_delete_order', 'description' => '', 'trigger' => 'catalog/model/checkout/order/deleteOrder/before', 'action' => 'extension/worldline/payment/worldline.order_delete_order_before', 'status' => true, 'sort_order' => 2]);
			$this->model_setting_event->addEvent(['code' => 'worldline_customer_delete_custome', 'description' => '', 'trigger' => 'admin/model/customer/customer/deleteCustomer/before', 'action' => 'extension/worldline/payment/worldline.customer_delete_customer_before', 'status' => true, 'sort_order' => 3]);
		} elseif (version_compare(VERSION, '4.0.1.0', '>=')) {
			$this->model_setting_event->addEvent(['code' => 'worldline_order_info', 'description' => '', 'trigger' => 'admin/view/sale/order_info/before', 'action' => 'extension/worldline/payment/worldline|order_info_before', 'status' => true, 'sort_order' => 1]);
			$this->model_setting_event->addEvent(['code' => 'worldline_extension_get_extensions_by_type', 'description' => '', 'trigger' => 'catalog/model/setting/extension/getExtensionsByType/after', 'action' => 'extension/worldline/payment/worldline|extension_get_extensions_by_type_after', 'status' => true, 'sort_order' => 2]);
			$this->model_setting_event->addEvent(['code' => 'worldline_extension_get_extension_by_code', 'description' => '', 'trigger' => 'catalog/model/setting/extension/getExtensionByCode/after', 'action' => 'extension/worldline/payment/worldline|extension_get_extension_by_code_after', 'status' => true, 'sort_order' => 3]);
			$this->model_setting_event->addEvent(['code' => 'worldline_order_delete_order', 'description' => '', 'trigger' => 'catalog/model/checkout/order/deleteOrder/before', 'action' => 'extension/worldline/payment/worldline|order_delete_order_before', 'status' => true, 'sort_order' => 4]);
			$this->model_setting_event->addEvent(['code' => 'worldline_customer_delete_customer', 'description' => '', 'trigger' => 'admin/model/customer/customer/deleteCustomer/before', 'action' => 'extension/worldline/payment/worldline|customer_delete_customer_before', 'status' => true, 'sort_order' => 5]);
		} else {
			$this->model_setting_event->addEvent('worldline_order_info', '', 'admin/view/sale/order_info/before', 'extension/worldline/payment/worldline|order_info_before', true, 1);
			$this->model_setting_event->addEvent('worldline_extension_get_extensions_by_type', '', 'catalog/model/setting/extension/getExtensionsByType/after', 'extension/worldline/payment/worldline|extension_get_extensions_by_type_after', true, 2);
			$this->model_setting_event->addEvent('paypal_extension_get_extension_by_code', '', 'catalog/model/setting/extension/getExtensionByCode/after', 'extension/worldline/payment/worldline|extension_get_extension_by_code_after', true, 3);
			$this->model_setting_event->addEvent('worldline_order_delete_order', '', 'catalog/model/checkout/order/deleteOrder/before', 'extension/worldline/payment/worldline|order_delete_order_before', true, 4);
			$this->model_setting_event->addEvent('worldline_customer_delete_customer', '', 'admin/model/customer/customer/deleteCustomer/before', 'extension/worldline/payment/worldline|customer_delete_customer_before', true, 5);
		}
		
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
			
		$config_setting = $_config->get('worldline_setting');
				
		$setting['worldline_version'] = $config_setting['extension']['version'];
		
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->editSetting('worldline_version', $setting);
	}
		
	public function uninstall(): void {
		$this->load->model('extension/worldline/payment/worldline');
		
		$this->model_extension_worldline_payment_worldline->uninstall();
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('worldline_order_info');
		$this->model_setting_event->deleteEventByCode('worldline_extension_get_extensions_by_type');
		$this->model_setting_event->deleteEventByCode('worldline_extension_get_extension_by_code');
		$this->model_setting_event->deleteEventByCode('worldline_order_delete_order');
		$this->model_setting_event->deleteEventByCode('worldline_customer_delete_customer');
		
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->deleteSetting('worldline_version');
	}
	
	public function customer_delete_customer_before(string $route, array &$data): void {
		$this->load->model('extension/worldline/payment/worldline');

		$customer_id = $data[0];

		$this->model_extension_worldline_payment_worldline->deleteWorldlineCustomerTokens($customer_id);
	}
	
	public function order_info_before(string $route, array &$data): void {
		if ($data['tabs']) {
			foreach ($data['tabs'] as $tab_key => $tab) {
				if ($tab['code'] == 'worldline') {
					unset($data['tabs'][$tab_key]);
				}
			}
		}
		
		if ($this->config->get('payment_worldline_status') && !empty($this->request->get['order_id'])) {
			$this->load->language('extension/worldline/payment/worldline');

			$content = $this->getPaymentDetails($this->request->get['order_id']);
			
			if ($content) {												
				$data['tabs'][] = [
					'code'    => 'worldline',
					'title'   => $this->language->get('heading_title'),
					'content' => $content
				];
			}
			
			$this->load->language('sale/order');
		}
	}
	
	public function getPaymentInfo(): void {
		$content = '';
		
		if (!empty($this->request->get['order_id'])) {
			$this->load->language('extension/worldline/payment/worldline');
			
			$content = $this->getPaymentDetails($this->request->get['order_id']);
		}
		
		$this->response->setOutput($content);
	}
	
	private function getPaymentDetails(int $order_id): string {
		$this->load->language('extension/worldline/payment/worldline');
			
		$this->load->model('extension/worldline/payment/worldline');
		$this->load->model('sale/order');
		$this->load->model('localisation/country');
					
		$order_info = $this->model_sale_order->getOrder($order_id);
		$worldline_order_info = $this->model_extension_worldline_payment_worldline->getWorldlineOrder($order_id);
	
		if ($order_info && $worldline_order_info) {
			$data['order_id'] = $order_id;
			$data['transaction_id'] = $worldline_order_info['transaction_id'];
			$data['transaction_number'] = preg_replace('/_[0-9]+/', '', $worldline_order_info['transaction_id']);
			$data['transaction_status'] = $worldline_order_info['transaction_status'];
			$data['payment_product'] = $worldline_order_info['payment_product'];
			$data['tokenize'] = $worldline_order_info['tokenize'];
			$data['total'] = $worldline_order_info['total'];
			$data['amount'] = $worldline_order_info['amount'];
			$data['currency_code'] = $worldline_order_info['currency_code'];
			$data['date_created'] = date('Y-m-d H:i', strtotime($worldline_order_info['date_created']));
			$data['environment'] = $worldline_order_info['environment'];
										
			if ($worldline_order_info['environment'] == 'production') {
				$data['transaction_url'] = 'https://merchant-portal.worldline-solutions.com/transactions/online/' . $data['transaction_number'];
			} else {
				$data['transaction_url'] = 'https://merchant-portal.preprod.worldline-solutions.com/transactions/online/' . $data['transaction_number'];
			}
				
			$data['info_url'] =  str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'getPaymentInfo', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $data['order_id']));
			$data['capture_url'] =  str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'capturePayment', 'user_token=' . $this->session->data['user_token']));
			$data['cancel_url'] =  str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'cancelPayment', 'user_token=' . $this->session->data['user_token']));
			$data['refund_url'] =  str_replace('&amp;', '&', $this->url->link('extension/worldline/payment/worldline' . $this->separator . 'refundPayment', 'user_token=' . $this->session->data['user_token']));
				
			$data['amount_captured'] = '';
			$data['amount_refunded'] = '';
			$data['cancel_amount'] = '';
			$data['capture_amount'] = '';
			$data['refund_amount'] = '';
			
			$data['payment_product_id'] = '';
			$data['card_bin'] = '';
			$data['card_number'] = '';
			$data['fraud_result'] = '';
			$data['liability'] = '';
			$data['exemption'] = '';
			$data['authentication_status'] = '';
			
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
		
			require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
			$connection = new \OnlinePayments\Sdk\DefaultConnection();	

			$shopping_cart_extension = new \OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

			$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
			$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

			$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
			$client = new \OnlinePayments\Sdk\Client($communicator);
			
			$errors = [];
			
			try {
				$payment_response = $client->merchant($merchant_id)->payments()->getPaymentDetails($data['transaction_id']);
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
				$total = $payment_response->getPaymentOutput()->getAmountOfMoney()->getAmount();
				$amount = $payment_response->getPaymentOutput()->getAcquiredAmount()->getAmount();
				
				$amount_captured = 0;
				$amount_refunded = 0;
				
				foreach ($payment_response->getOperations() as $operation) {
					if (($operation->getStatus() == 'CAPTURED') && ($operation->getStatusOutput()->getStatusCategory() == 'COMPLETED')) { 
						$amount_captured += $operation->getAmountOfMoney()->getAmount();
					}
					
					if (($operation->getStatus() == 'REFUNDED') && ($operation->getStatusOutput()->getStatusCategory() == 'REFUNDED')) { 
						$amount_refunded += $operation->getAmountOfMoney()->getAmount();
					}
				}
				
				$data['transaction_status'] = strtolower($payment_response->getStatus());
				$data['total'] = number_format($total / 100, 2, '.', '');
				$data['amount'] = number_format($amount / 100, 2, '.', '');
				$data['amount_captured'] = number_format($amount_captured / 100, 2, '.', '');
				$data['amount_refunded'] = number_format($amount_refunded / 100, 2, '.', '');
				$data['cancel_amount'] = number_format(($amount - $amount_captured) / 100, 2, '.', '');
				$data['capture_amount'] = number_format(($amount - $amount_captured) / 100, 2, '.', '');
				$data['refund_amount'] = number_format(($amount_captured - $amount_refunded) / 100, 2, '.', '');
				$data['currency_code'] = $payment_response->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode();

				$data['payment_product_id'] = '';
				$data['payment_type'] = '';
				$data['token'] = '';
				$data['card_brand'] = '';
				$data['card_last_digits'] = '';
				$data['card_expiry'] = '';
				
				if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput())) {
					$data['payment_product_id'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getPaymentProductId();
					$data['token'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
					$data['payment_type'] = 'card';
					$data['card_last_digits'] = str_replace('*', '', $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getCardNumber());
					$data['card_expiry'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getExpiryDate();
					$data['card_bin'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getBin();
					$data['card_number'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getCard()->getCardNumber();					
					$data['liability'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getThreeDSecureResults()->getLiability();
					$data['exemption'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getThreeDSecureResults()->getAppliedExemption();
					$data['authentication_status'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getThreeDSecureResults()->getAuthenticationStatus();
					
					if (!empty($payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getFraudResults())) {
						$data['fraud_result'] = $payment_response->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getFraudResults()->getFraudServiceResult();
					}
				}
				
				if (!empty($payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput())) {
					$data['payment_product_id'] = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getPaymentProductId();					
					$data['liability'] = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getThreeDSecureResults()->getLiability();
					$data['exemption'] = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getThreeDSecureResults()->getAppliedExemption();
					$data['authentication_status'] = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getThreeDSecureResults()->getAuthenticationStatus();
					
					if (!empty($payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getFraudResults())) {
						$data['fraud_result'] = $payment_response->getPaymentOutput()->getMobilePaymentMethodSpecificOutput()->getFraudResults()->getFraudServiceResult();
					}
				}
				
				if (!empty($payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput())) {
					$data['payment_product_id'] = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getPaymentProductId();					
					$data['token'] = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getToken();
					$data['payment_type'] = 'redirect';
					
					if (!empty($payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getFraudResults())) {
						$data['fraud_result'] = $payment_response->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getFraudResults()->getFraudServiceResult();
					}
				}
				
				if (!empty($payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput())) {
					$data['payment_product_id'] = $payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput()->getPaymentProductId();
					
					if (!empty($payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput()->getFraudResults())) {
						$data['fraud_result'] = $payment_response->getPaymentOutput()->getSepaDirectDebitPaymentMethodSpecificOutput()->getFraudResults()->getFraudServiceResult();
					}
				}
															
				if (($data['transaction_status'] == 'created') || ($data['transaction_status'] == 'pending_capture') || ($data['transaction_status'] == 'captured') || ($data['transaction_status'] == 'cancelled') || ($data['transaction_status'] == 'rejected') || ($data['transaction_status'] == 'rejected_capture') || ($data['transaction_status'] == 'refunded') || ($data['transaction_status'] == 'authorization_requested') || ($data['transaction_status'] == 'capture_requested') || ($data['transaction_status'] == 'refund_requested')) {							
					if (!$data['token']) $data['tokenize'] = 0;
					if (!$data['tokenize']) $data['token'] = '';
					
					if (!$worldline_order_info['transaction_status']) {
						$payment_product_params = new \OnlinePayments\Sdk\Merchant\Products\GetPaymentProductParams();
						$payment_product_params->setCurrencyCode($data['currency_code']);
						$payment_product_params->setCountryCode($worldline_order_info['country_code']);	
				
						try {
							$payment_product_response = $client->merchant($merchant_id)->products()->getPaymentProduct($data['payment_product_id'], $payment_product_params);
						} catch (\OnlinePayments\Sdk\ResponseException $exception) {			
							$errors = $exception->getResponse()->getErrors();
								
							if ($errors) {
								foreach ($errors as $error) {
									$this->model_extension_payment_worldline->log($error->getMessage() . ' (' . $error->getCode() . ')', 'Error');
								}
							}
						}
				
						if (!$errors) {
							if (!empty($payment_product_response->getDisplayHints())) {
								if (!empty($payment_product_response->getPaymentProductGroup())) {
									$data['payment_product'] .= $payment_product_response->getPaymentProductGroup() . ' ';
								}
						
								$data['payment_product'] .= $payment_product_response->getDisplayHints()->getLabel();
								
								if ($data['payment_type'] == 'card') {
									$data['card_brand'] = $payment_product_response->getDisplayHints()->getLabel();
								}
							}
						}
					}
					
					$worldline_order_data = [
						'order_id' => $worldline_order_info['order_id'],
						'transaction_status' => $data['transaction_status'],
						'payment_product' => $data['payment_product'],
						'payment_type' => $data['payment_type'],
						'tokenize' => $data['tokenize'],
						'token' => $data['token'],
						'card_brand' => $data['card_brand'],
						'card_last_digits' => $data['card_last_digits'],
						'card_expiry' => $data['card_expiry'],
						'total' => $data['total'],
						'amount' => $data['amount'],
						'currency_code' => $data['currency_code']
					];
						
					$this->model_extension_worldline_payment_worldline->editWorldlineOrder($worldline_order_data);
					
					if (!empty($order_info['customer_id']) && $data['token']) {
						$customer_id = $order_info['customer_id'];
						
						$worldline_customer_token_info = $this->model_extension_worldline_payment_worldline->getWorldlineCustomerToken($customer_id, $data['payment_type'], $data['token']);
								
						if (!$worldline_customer_token_info) {
							$worldline_customer_token_data = [
								'customer_id' => $customer_id,
								'payment_type' => $data['payment_type'],
								'token' => $data['token'],
								'card_brand' => $data['card_brand'],
								'card_last_digits' => $data['card_last_digits'],
								'card_expiry' => $data['card_expiry'],
							];
									
							$this->model_extension_worldline_payment_worldline->addWorldlineCustomerToken($worldline_customer_token_data);
						}
								
						$this->model_extension_worldline_payment_worldline->setWorldlineCustomerMainToken($customer_id, $data['payment_type'], $data['token']);	
					}
				}				
			}
			
			return $this->load->view('extension/worldline/payment/order', $data);
		}
		
		return '';
	}
	
	public function capturePayment(): void {					
		if (!empty($this->request->post['order_id']) && !empty($this->request->post['transaction_id'])) {
			$this->load->language('extension/worldline/payment/worldline');
			
			$this->load->model('extension/worldline/payment/worldline');
			$this->load->model('sale/order');
			
			$order_id = $this->request->post['order_id'];
			$transaction_id = $this->request->post['transaction_id'];
			
			$order_info = $this->model_sale_order->getOrder($order_id);
			
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
			
			require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
			$connection = new \OnlinePayments\Sdk\DefaultConnection();	

			$shopping_cart_extension = new \OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

			$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
			$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

			$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
			$client = new \OnlinePayments\Sdk\Client($communicator);
						
			$errors = [];
			
			$capture_amount = 0;
			
			if (!empty($this->request->post['capture_amount'])) {
				$capture_amount = number_format((float)$this->request->post['capture_amount'] * 100, 0, '', '');
			} else {
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
					
					$data['success'] = $this->language->get('success_capture');
				}
			}
		}
				
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
	
	public function cancelPayment(): void {
		if (!empty($this->request->post['order_id']) && !empty($this->request->post['transaction_id'])) {
			$this->load->language('extension/worldline/payment/worldline');
			
			$this->load->model('extension/worldline/payment/worldline');
			$this->load->model('sale/order');
			
			$order_id = $this->request->post['order_id'];
			$transaction_id = $this->request->post['transaction_id'];
			
			$order_info = $this->model_sale_order->getOrder($order_id);
						
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
			
			require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
			$connection = new \OnlinePayments\Sdk\DefaultConnection();	

			$shopping_cart_extension = new \OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

			$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
			$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

			$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
			$client = new \OnlinePayments\Sdk\Client($communicator);

			$errors = [];

			$cancel_amount = 0;
			
			if (!empty($this->request->post['capture_amount'])) {
				$cancel_amount = number_format((float)$this->request->post['cancel_amount'] * 100, 0, '', '');
			} else {
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
				
					$cancel_amount = $amount - $amount_captured;
				}
			}
					
			if ($cancel_amount) {
				$amount_of_money = new \OnlinePayments\Sdk\Domain\AmountOfMoney();
				$amount_of_money->setCurrencyCode($order_info['currency_code']);
				$amount_of_money->setAmount($cancel_amount);
				
				$cancel_payment_request = new \OnlinePayments\Sdk\Domain\CancelPaymentRequest();
				$cancel_payment_request->setAmountOfMoney($amount_of_money);
					
				try {
					$cancel_response = $client->merchant($merchant_id)->payments()->cancelPayment($transaction_id, $cancel_payment_request);
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
			
					$data['success'] = $this->language->get('success_cancel');
				}
			}
		}
				
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
		
	public function refundPayment(): void {
		if (!empty($this->request->post['order_id']) && !empty($this->request->post['transaction_id'])) {
			$this->load->language('extension/worldline/payment/worldline');
			
			$this->load->model('extension/worldline/payment/worldline');
			$this->load->model('sale/order');
			
			$order_id = $this->request->post['order_id'];
			$transaction_id = $this->request->post['transaction_id'];
			
			$order_info = $this->model_sale_order->getOrder($order_id);
						
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
			
			require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';
				
			$connection = new \OnlinePayments\Sdk\DefaultConnection();	

			$shopping_cart_extension = new \OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

			$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
			$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

			$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
			$client = new \OnlinePayments\Sdk\Client($communicator);
			
			$errors = [];
			
			$refund_amount = 0;
			
			if (!empty($this->request->post['refund_amount'])) {
				$refund_amount = number_format((float)$this->request->post['refund_amount'] * 100, 0, '', '');
			} else {
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
					$amount_refunded = 0;
									
					foreach ($payment_response->getOperations() as $operation) {
						if (($operation->getStatus() == 'CAPTURED') && ($operation->getStatusOutput()->getStatusCategory() == 'COMPLETED')) { 
							$amount_captured += $operation->getAmountOfMoney()->getAmount();
						}
						
						if (($operation->getStatus() == 'REFUNDED') && ($operation->getStatusOutput()->getStatusCategory() == 'REFUNDED')) { 
							$amount_refunded += $operation->getAmountOfMoney()->getAmount();
						}
					}
				
					$refund_amount = $amount_captured - $amount_refunded;
				}
			}
					
			if ($refund_amount) {
				$amount_of_money = new \OnlinePayments\Sdk\Domain\AmountOfMoney();
				$amount_of_money->setCurrencyCode($order_info['currency_code']);
				$amount_of_money->setAmount($refund_amount);
				
				$refund_request = new \OnlinePayments\Sdk\Domain\RefundRequest();
				$refund_request->setAmountOfMoney($amount_of_money);
												
				try {
					$refund_response = $client->merchant($merchant_id)->payments()->refundPayment($transaction_id, $refund_request);
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
																		
					$data['success'] = $this->language->get('success_refund');
				}
			}
		}
				
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
									
	private function validateSave(): array|bool {
		if (!$this->user->hasPermission('modify', 'extension/worldline/payment/worldline')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
		
		$config_setting = $_config->get('worldline_setting');
		
		$setting = $this->request->post['payment_worldline_setting'];
				
		if (!empty($setting['account'])) {
			$extension = $config_setting['extension'];
			$environment = $setting['account']['environment'];
		
			$setting['account']['merchant_id'][$environment] = trim($setting['account']['merchant_id'][$environment]);

			if (!$setting['account']['merchant_id'][$environment]) {
				$this->error['merchant_id' . $config_setting['environment'][$environment]['prefix']] = $this->language->get('error_merchant_id' . $config_setting['environment'][$environment]['prefix']);
				$this->error['warning'] = $this->language->get('error_warning');
			}
		
			$setting['account']['api_key'][$environment] = trim($setting['account']['api_key'][$environment]);

			if (!$setting['account']['api_key'][$environment]) {
				$this->error['api_key' . $config_setting['environment'][$environment]['prefix']] = $this->language->get('error_api_key' . $config_setting['environment'][$environment]['prefix']);
				$this->error['warning'] = $this->language->get('error_warning');
			} 
		
			$setting['account']['api_secret'][$environment] = trim($setting['account']['api_secret'][$environment]);

			if (!$setting['account']['api_secret'][$environment]) {
				$this->error['api_secret' . $config_setting['environment'][$environment]['prefix']] = $this->language->get('error_api_secret' . $config_setting['environment'][$environment]['prefix']);
				$this->error['warning'] = $this->language->get('error_warning');
			} 
		
			$setting['account']['webhook_key'][$environment] = trim($setting['account']['webhook_key'][$environment]);

			if (!$setting['account']['webhook_key'][$environment]) {
				$this->error['webhook_key' . $config_setting['environment'][$environment]['prefix']] = $this->language->get('error_webhook_key' . $config_setting['environment'][$environment]['prefix']);
				$this->error['warning'] = $this->language->get('error_warning');
			} 
		
			$setting['account']['webhook_secret'][$environment] = trim($setting['account']['webhook_secret'][$environment]);

			if (!$setting['account']['webhook_secret'][$environment]) {
				$this->error['webhook_secret' . $config_setting['environment'][$environment]['prefix']] = $this->language->get('error_webhook_secret' . $config_setting['environment'][$environment]['prefix']);
				$this->error['warning'] = $this->language->get('error_warning');
			} 
		
			$this->request->post['payment_worldline_setting'] = $setting;
		
			if (!$this->error && $setting['account']['merchant_id'][$environment] && $setting['account']['api_key'][$environment] && $setting['account']['api_secret'][$environment]) {										
				$_config = new \Opencart\System\Engine\Config();
				$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
				$_config->load('worldline');
		
				$config_setting = $_config->get('worldline_setting');
		
				$merchant_id = $setting['account']['merchant_id'][$environment];
				$api_key = $setting['account']['api_key'][$environment];
				$api_secret = $setting['account']['api_secret'][$environment];
				$api_endpoint = $setting['account']['api_endpoint'][$environment];
		
				require_once DIR_EXTENSION . 'worldline/system/library/worldline/OnlinePayments.php';

				try {
					$connection = new \OnlinePayments\Sdk\DefaultConnection();	

					$shopping_cart_extension = new \OnlinePayments\Sdk\Domain\ShoppingCartExtension($extension['creator'], $extension['name'], $extension['version'], $extension['extension_id']);

					$communicator_configuration = new \OnlinePayments\Sdk\CommunicatorConfiguration($api_key, $api_secret, $api_endpoint, $extension['integrator']);	
					$communicator_configuration->setShoppingCartExtension($shopping_cart_extension);

					$communicator = new \OnlinePayments\Sdk\Communicator($connection, $communicator_configuration);
 
					$client = new \OnlinePayments\Sdk\Client($communicator);
				
					$client->merchant($merchant_id)->services()->testConnection();		
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
		}
		
		if (!empty($setting['hosted_checkout'])) {
			$setting['hosted_checkout']['template'] = trim($setting['hosted_checkout']['template']);

			if (($setting['hosted_checkout']['template'] != '') && (substr($setting['hosted_checkout']['template'], -4, 4) != '.htm') && (substr($setting['hosted_checkout']['template'], -5, 5) != '.html') && (substr($setting['hosted_checkout']['template'], -6, 6) != '.dhtml')) {
				$this->error['template'] = $this->language->get('error_template');
				$this->error['warning'] = $this->language->get('error_warning');
			}
		}
		
		if (!empty($setting['hosted_tokenization'])) {
			$setting['hosted_tokenization']['template'] = trim($setting['hosted_tokenization']['template']);

			if (($setting['hosted_tokenization']['template'] != '') && (substr($setting['hosted_tokenization']['template'], -4, 4) != '.htm') && (substr($setting['hosted_tokenization']['template'], -5, 5) != '.html') && (substr($setting['hosted_tokenization']['template'], -6, 6) != '.dhtml')) {
				$this->error['template'] = $this->language->get('error_template');
				$this->error['warning'] = $this->language->get('error_warning');
			}
		}

		return !$this->error;
	}
	
	private function validateSendSuggest(): array|bool {
		if (!$this->user->hasPermission('modify', 'extension/worldline/payment/worldline')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$setting = $this->request->post['payment_worldline_setting'];
		
		if (!empty($setting['suggest'])) {
			$setting['suggest']['company_name'] = trim($setting['suggest']['company_name']);

			if (!$setting['suggest']['company_name']) {
				$this->error['suggest_company_name'] = $this->language->get('error_company_name');
				$this->error['warning'] = $this->language->get('error_warning');
			}
		
			$setting['suggest']['message'] = trim($setting['suggest']['message']);

			if (!$setting['suggest']['message']) {
				$this->error['suggest_message'] = $this->language->get('error_message');
				$this->error['warning'] = $this->language->get('error_warning');
			}
		
			$this->request->post['payment_worldline_setting'] = $setting;
		}

		return !$this->error;
	}
}