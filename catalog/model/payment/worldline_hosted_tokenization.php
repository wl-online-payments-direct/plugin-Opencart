<?php
class ModelPaymentWorldlineHostedTokenization extends Model {
	
	public function getMethod($address, $total) {
		$this->load->language('payment/worldline');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('worldline_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if (($this->config->get('worldline_total') > 0) && ($this->config->get('worldline_total') > $total)) {
			$status = false;
		} elseif (!$this->config->get('worldline_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		
		$method_data = array();

		if ($status) {
			$_config = new Config();
			$_config->load('worldline');
			
			$config_setting = $_config->get('worldline_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('worldline_setting'));
			
			if ($setting['hosted_tokenization']['status']) {
				$language_id = $this->config->get('config_language_id');
						
				if (!empty($setting['hosted_tokenization']['title'][$language_id])) {
					$title = $setting['hosted_tokenization']['title'][$language_id];
				} else {
					$title = $this->language->get('text_hosted_tokenization_title');
				}
							
				$method_data = array(
					'code'       => 'worldline_hosted_tokenization',
					'title'      => $title,
					'terms'      => '',
					'sort_order' => $this->config->get('worldline_sort_order')
				);
			}
		}

		return $method_data;
	}
}