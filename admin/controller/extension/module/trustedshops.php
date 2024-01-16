<?php
class ControllerExtensionModuleTrustedshops extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/trustedshops');
		
		$this->document->setTitle($this->language->get('heading_title_bar'));
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('trustedshops', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->cache->delete('trustedshops');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', 'SSL'));
		}
		 
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_standard'] = $this->language->get('text_standard');
		$data['text_expert'] = $this->language->get('text_expert');
		$data['text_with_review_star'] = $this->language->get('text_with_review_star');
		$data['text_without_review_star'] = $this->language->get('text_without_review_star');
		$data['text_hide_trustbadge'] = $this->language->get('text_hide_trustbadge');
		$data['text_trustbadge_code'] = $this->language->get('text_trustbadge_code');
		$data['text_product_reviews'] = $this->language->get('text_product_reviews');
		$data['text_product_review_sticker'] = $this->language->get('text_product_review_sticker');
		$data['text_product_review_stars'] = $this->language->get('text_product_review_stars');
		$data['tips_product_reviews'] = $this->language->get('tips_product_reviews');
		$data['tips_additional_product_attributes'] = $this->language->get('tips_additional_product_attributes');
		$data['text_additional_product_attributes'] = $this->language->get('text_additional_product_attributes');
		$data['text_additional_model'] = $this->language->get('text_additional_model');
		$data['text_additional_sku'] = $this->language->get('text_additional_sku');
		$data['text_additional_ean'] = $this->language->get('text_additional_ean');
		$data['text_additional_mpn'] = $this->language->get('text_additional_mpn');
		
		$data['button_get_your_account'] = $this->language->get('button_get_your_account');
		$data['get_your_account_url'] = $this->language->get('get_your_account_url');
		
		$this->load->model('localisation/language');
		$language_data = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
		
		$data['trustedshops_info_intro'] = 'view/image/trustedshops/trustedshops_'.$language_data['code'].'.jpg';
		$data['help_tsid'] = $this->language->get('help_tsid');
		$data['help_y_offset'] = $this->language->get('help_y_offset');
		$data['help_review_sticker'] = $this->language->get('help_review_sticker');
		$data['help_rating'] = $this->language->get('help_rating');
		$data['help_code'] = $this->language->get('help_code');
		$data['placeholder_trustbadge_review_sticker_border_color'] = $this->language->get('placeholder_trustbadge_review_sticker_border_color');
		$data['placeholder_trustbadge_review_sticker_star_color'] = $this->language->get('placeholder_trustbadge_review_sticker_star_color');
		$data['placeholder_trustbadge_review_star_color'] = $this->language->get('placeholder_trustbadge_review_star_color');
		$data['placeholder_trustbadge_review_star_size'] = $this->language->get('placeholder_trustbadge_review_star_size');
		$data['placeholder_trustbadge_review_font_size'] = $this->language->get('placeholder_trustbadge_review_font_size');
		$data['product_reviews_info'] = $this->language->get('product_reviews_info');
		
		$data['entry_trustedshops_info_tsid'] = $this->language->get('entry_trustedshops_info_tsid');
		$data['entry_trustedshops_info_mode'] = $this->language->get('entry_trustedshops_info_mode');
		$data['entry_trustedshops_info_status'] = $this->language->get('entry_trustedshops_info_status');
		
		$data['entry_trustedshops_trustbadge_variant'] = $this->language->get('entry_trustedshops_trustbadge_variant');
		$data['entry_trustedshops_trustbadge_offset'] = $this->language->get('entry_trustedshops_trustbadge_offset');
		
		$data['entry_trustedshops_trustbadge_code'] = $this->language->get('entry_trustedshops_trustbadge_code');
		$data['entry_trustedshops_trustbadge_collect_orders'] = $this->language->get('entry_trustedshops_trustbadge_collect_orders');
		
		$data['entry_trustedshops_product_collect_reviews'] = $this->language->get('entry_trustedshops_product_collect_reviews');
		$data['trustedshops_product_expert_collect_reviews'] = $this->language->get('entry_trustedshops_product_collect_reviews');
		
		$data['entry_trustedshops_product_review_active'] = $this->language->get('entry_trustedshops_product_review_active');
		$data['entry_trustedshops_product_review_tab_name'] = $this->language->get('entry_trustedshops_product_review_tab_name');
		$data['product_review_tab_name_tips'] = $this->language->get('product_review_tab_name_tips');
		$data['entry_trustedshops_product_review_border_color'] = $this->language->get('entry_trustedshops_product_review_border_color');
		$data['entry_trustedshops_product_review_star_color'] = $this->language->get('entry_trustedshops_product_review_star_color');
		$data['entry_trustedshops_product_review_hide_empty'] = $this->language->get('entry_trustedshops_product_review_hide_empty');
		$data['product_review_hide_empty_tips'] = $this->language->get('product_review_hide_empty_tips');
		
		$data['entry_trustedshops_product_rating_active'] = $this->language->get('entry_trustedshops_product_rating_active');
		$data['entry_trustedshops_product_rating_star_color'] = $this->language->get('entry_trustedshops_product_rating_star_color');
		$data['entry_trustedshops_product_rating_star_size'] = $this->language->get('entry_trustedshops_product_rating_star_size');
		$data['entry_trustedshops_product_rating_font_size'] = $this->language->get('entry_trustedshops_product_rating_font_size');
		$data['entry_trustedshops_product_rating_hide_empty'] = $this->language->get('entry_trustedshops_product_rating_hide_empty');
		$data['product_rating_hide_empty_tips'] = $this->language->get('product_rating_hide_empty_tips');
		
		$data['entry_trustedshops_product_expert_collect_reviews'] = $this->language->get('entry_trustedshops_product_expert_collect_reviews');
		
		$data['entry_trustedshops_trustbadge_sku'] = $this->language->get('entry_trustedshops_trustbadge_sku');
		$data['entry_trustedshops_trustbadge_gtin'] = $this->language->get('entry_trustedshops_trustbadge_gtin');
		$data['entry_trustedshops_trustbadge_mpn'] = $this->language->get('entry_trustedshops_trustbadge_mpn');
		
		$data['entry_trustedshops_product_expert_review_active'] = $this->language->get('entry_trustedshops_product_expert_review_active');
		$data['entry_trustedshops_product_review_code'] = $this->language->get('entry_trustedshops_product_review_code');
		
		$data['entry_trustedshops_product_expert_rating_active'] = $this->language->get('entry_trustedshops_product_expert_rating_active');
		$data['entry_trustedshops_product_rating_code'] = $this->language->get('entry_trustedshops_product_rating_code');		
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}		

		if (isset($this->error['tsid'])) {
			$data['error_tsid'] = $this->error['tsid'];
		} else {
			$data['error_tsid'] = array();
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_bar'),
			'href' => $this->url->link('extension/module/trustedshops', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('extension/module/trustedshops', 'token=' . $this->session->data['token'], 'SSL');
				
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		
		if (isset($this->request->post['trustedshops_info'])) {
			$data['trustedshops_info'] = $this->request->post['trustedshops_info'];
		} else {
			$data['trustedshops_info'] = $this->config->get('trustedshops_info');
		}
		//echo '<pre>test'.print_r($data['trustedshops_info'], true).'</pre>';
		
		if (isset($this->request->post['trustedshops_trustbadge'])) {
			$data['trustedshops_trustbadge'] = $this->request->post['trustedshops_trustbadge'];
		} else {
			$data['trustedshops_trustbadge'] = $this->config->get('trustedshops_trustbadge');
		}
		//echo '<pre>test2'.print_r($data['trustedshops_trustbadge'], true).'</pre>';
		
		if (isset($this->request->post['trustedshops_product'])) {
			$data['trustedshops_product'] = $this->request->post['trustedshops_product'];
		} else {
			$data['trustedshops_product'] = $this->config->get('trustedshops_product');
		}
		//echo '<pre>test3'.print_r($data['trustedshops_product'], true).'</pre>';
		
		if (isset($this->request->post['trustedshops_product_expert'])) {
			$data['trustedshops_product_expert'] = $this->request->post['trustedshops_product_expert'];
		} else {
			$data['trustedshops_product_expert'] = $this->config->get('trustedshops_product_expert');
		}
		//echo '<pre>test4'.print_r($data['trustedshops_product_expert'], true).'</pre>';
		
		$data['variant_list']	= array("reviews"=>$this->language->get('text_variant1'),"default"=>$this->language->get('text_variant2'),"hide"=>$this->language->get('text_variant3'));	

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/trustedshops.tpl', $data));
	}	

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/trustedshops')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		//$this->error['test'] ='test';
		foreach ($this->request->post['trustedshops_info'] as $language_id => $value) {
			if (!$value['tsid']) {
				$this->error['tsid'][$language_id] = $this->language->get('error_trustedshops_info_tsid');
			}
		}

		return !$this->error;
	}
	
	public function install(){
		
		/*$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_info_tsid', `value` = ''");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_info_mode', `value` = 'standard'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_info_status', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_trustbadge_variant', `value` = 'default'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_trustbadge_offset', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_trustbadge_code', `value` = ''");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_trustbadge_collect_orders', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_collect_reviews', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_expert_collect_reviews', `value` = '0'");
		
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_review_active', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_review_border_color', `value` = '#FFDC0F'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_review_star_color', `value` = '#C0C0C0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_rating_active', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_rating_star_color', `value` = '#FFDC0F'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_rating_star_size', `value` = '15px'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_rating_font_size', `value` = '12px'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_expert_review_active', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_review_code', `value` = ''");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_expert_rating_active', `value` = '0'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'trustedshops', `key` = 'trustedshops_product_rating_code', `value` = ''");*/
		
	}
	public function uninstall(){
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('trustedshops');
		
	}
}