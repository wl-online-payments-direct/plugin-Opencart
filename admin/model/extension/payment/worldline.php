<?php
class ModelExtensionPaymentWorldline extends Model {
					
	public function addWorldlineCustomerToken($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "worldline_customer_token` SET";

		$implode = array();
			
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
	
	public function deleteWorldlineCustomerTokens($customer_id) {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "worldline_customer_token` WHERE `customer_id` = '" . (int)$customer_id . "'");
	}
	
	public function setWorldlineCustomerMainToken($customer_id, $payment_type, $token) {
		$this->db->query("UPDATE `" . DB_PREFIX . "worldline_customer_token` SET `main_token_status` = '0' WHERE `customer_id` = '" . (int)$customer_id . "' AND `payment_type` = '" . $this->db->escape($payment_type) . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "worldline_customer_token` SET `main_token_status` = '1' WHERE `customer_id` = '" . (int)$customer_id . "' AND `payment_type` = '" . $this->db->escape($payment_type) . "' AND `token` = '" . $this->db->escape($token) . "'");
	}
	
	public function getWorldlineCustomerToken($customer_id, $payment_type, $token) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "worldline_customer_token` WHERE `customer_id` = '" . (int)$customer_id . "' AND `payment_type` = '" . $this->db->escape($payment_type) . "' AND `token` = '" . $this->db->escape($token) . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return array();
		}
	}
	
	public function getWorldlineCustomerTokens($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "worldline_customer_token` WHERE `customer_id` = '" . (int)$customer_id . "'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return array();
		}
	}

	public function editWorldlineOrder($data) {
		$sql = "UPDATE `" . DB_PREFIX . "worldline_order` SET";

		$implode = array();
		
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
		
	public function deleteWorldlineOrder($order_id) {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "worldline_order` WHERE `order_id` = '" . (int)$order_id . "'");
	}
	
	public function getWorldlineOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "worldline_order` WHERE `order_id` = '" . (int)$order_id . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return array();
		}
	}
	
	public function getWorldlineOrders($data = array()) {
		$sql = "SELECT wo.order_id, wo.transaction_id, wo.transaction_status, wo.payment_product, wo.payment_type, wo.token, wo.total, wo.amount, wo.currency_code, wo.date, wo.environment FROM `" . DB_PREFIX . "worldline_order` wo";

		$implode = array();
			
		if (!empty($data['filter_order_id'])) {
			$implode[] = "wo.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		
		if (!empty($data['filter_transaction_id'])) {
			$implode[] = "wo.transaction_id = '" . $this->db->escape($data['filter_transaction_id']) . "'";
		}
		
		if (isset($data['filter_transaction_status'])) {
			$implode[] = "wo.transaction_status = '" . $this->db->escape($data['filter_transaction_status']) . "'";
		}
		
		if (isset($data['filter_payment_product'])) {
			$implode[] = "wo.payment_product LIKE '%" . $this->db->escape($data['filter_payment_product']) . "%'";
		}
		
		if (isset($data['filter_payment_type'])) {
			$implode[] = "wo.payment_type = '" . $this->db->escape($data['filter_payment_type']) . "'";
		}
		
		if (isset($data['filter_token'])) {
			$implode[] = "wo.token = '" . $this->db->escape($data['filter_token']) . "'";
		}
		
		if (!empty($data['filter_total'])) {
			$implode[] = "wo.total = '" . (float)$data['filter_total'] . "'";
		}
		
		if (!empty($data['filter_amount'])) {
			$implode[] = "wo.amount = '" . (float)$data['filter_amount'] . "'";
		}
		
		if (!empty($data['filter_currency_code'])) {
			$implode[] = "wo.currency_code = '" . $this->db->escape($data['filter_currency_code']) . "'";
		}
				
		if (!empty($data['filter_date_from'])) {
			$implode[] = "DATE(wo.date) >= DATE('" . $this->db->escape($data['filter_date_from']) . "')";
		}
		
		if (!empty($data['filter_date_to'])) {
			$implode[] = "DATE(wo.date) <= DATE('" . $this->db->escape($data['filter_date_to']) . "')";
		}
		
		if (!empty($data['filter_environment'])) {
			$implode[] = "wo.environment = '" . $this->db->escape($data['filter_environment']) . "'";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		
		$sort_data = array(
			'wo.order_id',
			'wo.transaction_id',
			'wo.transaction_status',
			'wo.payment_product',
			'wo.payment_type',
			'wo.token',
			'wo.total',
			'wo.amount',
			'wo.currency_code',
			'wo.date',
			'wo.environment'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY wo.order_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getTotalWorldlineOrders($data = array()) {
		$sql = "SELECT COUNT(DISTINCT order_id) AS total FROM `" . DB_PREFIX . "worldline_order` wo";

		$implode = array();
			
		if (!empty($data['filter_order_id'])) {
			$implode[] = "wo.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		
		if (!empty($data['filter_transaction_id'])) {
			$implode[] = "wo.transaction_id = '" . $this->db->escape($data['filter_transaction_id']) . "'";
		}
		
		if (isset($data['filter_transaction_status'])) {
			$implode[] = "wo.transaction_status = '" . $this->db->escape($data['filter_transaction_status']) . "'";
		}
		
		if (isset($data['filter_payment_product'])) {
			$implode[] = "wo.payment_product LIKE '%" . $this->db->escape($data['filter_payment_product']) . "%'";
		}
		
		if (isset($data['filter_payment_type'])) {
			$implode[] = "wo.payment_type = '" . $this->db->escape($data['filter_payment_type']) . "'";
		}
		
		if (isset($data['filter_token'])) {
			$implode[] = "wo.token = '" . $this->db->escape($data['filter_token']) . "'";
		}
		
		if (!empty($data['filter_total'])) {
			$implode[] = "wo.total = '" . (float)$data['filter_total'] . "'";
		}
		
		if (!empty($data['filter_amount'])) {
			$implode[] = "wo.amount = '" . (float)$data['filter_amount'] . "'";
		}
		
		if (!empty($data['filter_currency_code'])) {
			$implode[] = "wo.currency_code = '" . $this->db->escape($data['filter_currency_code']) . "'";
		}
				
		if (!empty($data['filter_date_from'])) {
			$implode[] = "DATE(wo.date) > DATE('" . $this->db->escape($data['filter_date_from']) . "')";
		}
		
		if (!empty($data['filter_date_to'])) {
			$implode[] = "DATE(wo.date) < DATE('" . $this->db->escape($data['filter_date_to']) . "')";
		}
		
		if (!empty($data['filter_environment'])) {
			$implode[] = "wo.environment = '" . $this->db->escape($data['filter_environment']) . "'";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$query = $this->db->query($sql);
								
		return $query->row['total'];
	}
		
	public function checkVersion($opencart_version, $worldline_version) {
		$curl = curl_init();
			
		curl_setopt($curl, CURLOPT_URL, 'https://www.opencart.com/index.php?route=api/promotion/worldline&opencart=' . $opencart_version . '&worldline=' . $worldline_version);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
							
		$response = curl_exec($curl);
			
		curl_close($curl);
			
		$result = json_decode($response, true);
		
		if ($result) {
			return $result;
		} else {
			return false;
		}
	}
		
	public function sendSuggest($data) {
		$_config = new Config();
		$_config->load('worldline');
		
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
		
		$data['text_suggest_subject'] = $this->language->get('text_suggest_subject');
		$data['text_suggest_version'] = sprintf($this->language->get('text_suggest_version'), VERSION, $setting['extension']['version']);
		
		$data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$data['entry_company_name'] = $this->language->get('entry_company_name');
		
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo('isvpartners@worldline.com');
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($data['worldline_setting']['suggest']['company_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode($this->language->get('text_suggest_subject'), ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($this->load->view('extension/payment/worldline/suggest_mail', $data));
		$mail->send();
	}

	public function log($data, $title = '') {
		$_config = new Config();
		$_config->load('worldline');
		
		$config_setting = $_config->get('worldline_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
		
		if ($setting['advanced']['debug']) {
			$log = new Log('worldline.log');
			
			if (is_string($data)) {
				$log->write('Worldline debug (' . $title . '): ' . $data);
			} else {
				$log->write('Worldline debug (' . $title . '): ' . json_encode($data));
			}
		}
	}
	
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "worldline_customer_token` (`customer_id` INT(11) NOT NULL, `payment_type` VARCHAR(20) NOT NULL, `token` VARCHAR(50) NOT NULL, `main_token_status` TINYINT(1) NOT NULL, PRIMARY KEY (`customer_id`, `payment_type`, `token`), KEY `main_token_status` (`main_token_status`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "worldline_order` (`order_id` INT(11) NOT NULL, `transaction_id` VARCHAR(20) NOT NULL, `transaction_status` VARCHAR(20) NULL, `payment_product` VARCHAR(40) NULL, `payment_type` VARCHAR(20) NOT NULL, `token` VARCHAR(50), `total` DECIMAL(15,2) NULL, `amount` DECIMAL(15,2) NULL, `currency_code` VARCHAR(3) NULL, `country_code` VARCHAR(2) NULL, `environment` VARCHAR(20) NULL, `date` DATETIME NULL, PRIMARY KEY (`order_id`), KEY `transaction_id` (`transaction_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "worldline_customer_token`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "worldline_order`");
	}
}
