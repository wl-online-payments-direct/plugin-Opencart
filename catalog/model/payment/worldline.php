<?php
namespace Opencart\Catalog\Model\Extension\Worldline\Payment;
class Worldline extends \Opencart\System\Engine\Model {
	
	public function getMethod(array $address): array {
		$this->load->language('extension/worldline/payment/worldline');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_worldline_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->cart->hasSubscription()) {
			$status = false;
		} elseif (!$this->config->get('payment_worldline_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = [];

		if ($status) {
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
			$_config->load('worldline');
			
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
			
			$language_id = $this->config->get('config_language_id');
					
			if (!empty($setting['advanced']['title'][$language_id])) {
				$title = $setting['advanced']['title'][$language_id];
			} else {
				$title = $this->language->get('text_title');
			}
						
			$method_data = [
				'code'       => 'worldline',
				'title'      => $title,
				'terms'      => '',
				'sort_order' => $this->config->get('payment_worldline_sort_order')
			];
		}

		return $method_data;
	}
	
	public function getMethods(array $address = []): array {
		$this->load->language('extension/worldline/payment/worldline');

		if ($this->cart->hasSubscription()) {
			$status = false;
		} elseif (!$this->config->get('config_checkout_payment_address')) {
			$status = true;
		} elseif (!$this->config->get('payment_worldline_geo_zone_id')) {
			$status = true;
		} else {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_worldline_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

			if ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}
		}

		$method_data = [];

		if ($status) {
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
			$_config->load('worldline');
			
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
			
			$language_id = $this->config->get('config_language_id');
					
			if (!empty($setting['advanced']['title'][$language_id])) {
				$title = $setting['advanced']['title'][$language_id];
			} else {
				$title = $this->language->get('text_title');
			}
				
			$option_data['worldline'] = [
				'code' => 'worldline.worldline',
				'name' => $title
			];
				
			$method_data = [
				'code'       => 'worldline',
				'name'       => $title,
				'option'     => $option_data,
				'sort_order' => $this->config->get('payment_worldline_sort_order')
			];
		}

		return $method_data;
	}
	
	public function addWorldlineCustomerToken(array $data): void {
		$sql = "INSERT INTO `" . DB_PREFIX . "worldline_customer_token` SET";

		$implode = [];
			
		if (!empty($data['customer_id'])) {
			$implode[] = "`customer_id` = '" . (int)$data['customer_id'] . "'";
		}
		
		if (!empty($data['payment_type'])) {
			$implode[] = "`payment_type` = '" . $this->db->escape($data['payment_type']) . "'";
		}
		
		if (!empty($data['token'])) {
			$implode[] = "`token` = '" . $this->db->escape($data['token']) . "'";
		}
										
		if ($implode) {
			$sql .= implode(", ", $implode);
		}
		
		$this->db->query($sql);
	}
	
	public function setWorldlineCustomerMainToken(int $customer_id, string $payment_type, string $token): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "worldline_customer_token` SET `main_token_status` = '0' WHERE `customer_id` = '" . (int)$customer_id . "' AND `payment_type` = '" . $this->db->escape($payment_type) . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "worldline_customer_token` SET `main_token_status` = '1' WHERE `customer_id` = '" . (int)$customer_id . "' AND `payment_type` = '" . $this->db->escape($payment_type) . "' AND `token` = '" . $this->db->escape($token) . "'");
	}
	
	public function getWorldlineCustomerToken(int $customer_id, string $payment_type, string $token): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "worldline_customer_token` WHERE `customer_id` = '" . (int)$customer_id . "' AND `payment_type` = '" . $this->db->escape($payment_type) . "' AND `token` = '" . $this->db->escape($token) . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}
	
	public function getWorldlineCustomerTokens(int $customer_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "worldline_customer_token` WHERE `customer_id` = '" . (int)$customer_id . "'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}
		
	public function addWorldlineOrder(array $data): void {
		$sql = "INSERT INTO `" . DB_PREFIX . "worldline_order` SET";

		$implode = [];
			
		if (!empty($data['order_id'])) {
			$implode[] = "`order_id` = '" . (int)$data['order_id'] . "'";
		}
		
		if (!empty($data['transaction_id'])) {
			$implode[] = "`transaction_id` = '" . $this->db->escape($data['transaction_id']) . "'";
		}
		
		if (!empty($data['transaction_status'])) {
			$implode[] = "`transaction_status` = '" . $this->db->escape($data['transaction_status']) . "'";
		}
		
		if (!empty($data['payment_product'])) {
			$implode[] = "`payment_product` = '" . $this->db->escape($data['payment_product']) . "'";
		}
		
		if (!empty($data['payment_type'])) {
			$implode[] = "`payment_type` = '" . $this->db->escape($data['payment_type']) . "'";
		}
		
		if (!empty($data['token'])) {
			$implode[] = "`token` = '" . $this->db->escape($data['token']) . "'";
		}
		
		if (!empty($data['total'])) {
			$implode[] = "`total` = '" . (float)$data['total'] . "'";
		}
				
		if (!empty($data['amount'])) {
			$implode[] = "`amount` = '" . (float)$data['amount'] . "'";
		}
		
		if (!empty($data['currency_code'])) {
			$implode[] = "`currency_code` = '" . $this->db->escape($data['currency_code']) . "'";
		}
		
		if (!empty($data['country_code'])) {
			$implode[] = "`country_code` = '" . $this->db->escape($data['country_code']) . "'";
		}
		
		if (!empty($data['environment'])) {
			$implode[] = "`environment` = '" . $this->db->escape($data['environment']) . "'";
		}
		
		if (!empty($data['token'])) {
			$implode[] = "`token` = '" . $this->db->escape($data['token']) . "'";
		}
		
		if ($implode) {
			$sql .= implode(", ", $implode);
		}
		
		$this->db->query($sql);
	}
	
	public function editWorldlineOrder(array $data): void {
		$sql = "UPDATE `" . DB_PREFIX . "worldline_order` SET";

		$implode = [];
		
		if (!empty($data['transaction_id'])) {
			$implode[] = "`transaction_id` = '" . $this->db->escape($data['transaction_id']) . "'";
		}
					
		if (!empty($data['transaction_status'])) {
			$implode[] = "`transaction_status` = '" . $this->db->escape($data['transaction_status']) . "'";
		}
		
		if (!empty($data['payment_product'])) {
			$implode[] = "`payment_product` = '" . $this->db->escape($data['payment_product']) . "'";
		}
		
		if (!empty($data['payment_type'])) {
			$implode[] = "`payment_type` = '" . $this->db->escape($data['payment_type']) . "'";
		}
		
		if (!empty($data['token'])) {
			$implode[] = "`token` = '" . $this->db->escape($data['token']) . "'";
		}
		
		if (!empty($data['total'])) {
			$implode[] = "`total` = '" . (float)$data['total'] . "'";
		}
		
		if (!empty($data['amount'])) {
			$implode[] = "`amount` = '" . (float)$data['amount'] . "'";
		}
		
		if (!empty($data['currency_code'])) {
			$implode[] = "`currency_code` = '" . $this->db->escape($data['currency_code']) . "'";
		}
		
		if (!empty($data['country_code'])) {
			$implode[] = "`country_code` = '" . $this->db->escape($data['country_code']) . "'";
		}
		
		if (!empty($data['environment'])) {
			$implode[] = "`environment` = '" . $this->db->escape($data['environment']) . "'";
		}
				
		$implode[] = "`date` = COALESCE(`date`, NOW())";
		
		if ($implode) {
			$sql .= implode(", ", $implode);
		}

		$sql .= " WHERE `order_id` = '" . (int)$data['order_id'] . "'";
		
		$this->db->query($sql);
	}
		
	public function deleteWorldlineOrder(int $order_id): void {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "worldline_order` WHERE `order_id` = '" . (int)$order_id . "'");
	}
	
	public function getWorldlineOrder(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "worldline_order` WHERE `order_id` = '" . (int)$order_id . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}
	
	public function getWaitingWorldlineOrders(): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "worldline_order` WHERE `transaction_status` = '' OR `transaction_status` = 'created' OR `transaction_status` = 'authorization_requested' OR `transaction_status` = 'capture_requested' OR `transaction_status` = 'refund_requested'");
					
		return $query->rows;
	}
		
	public function log(string|array $data, string $title = ''): void {
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'worldline/system/config/');
		$_config->load('worldline');
			
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_worldline_setting'));
		
		if ($setting['advanced']['debug']) {
			$log = new \Opencart\System\Library\Log('worldline.log');
			
			if (is_string($data)) {
				$log->write('Worldline debug (' . $title . '): ' . $data);
			} else {
				$log->write('Worldline debug (' . $title . '): ' . json_encode($data));
			}
		}
	}
}