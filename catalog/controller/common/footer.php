<?php
class ControllerCommonFooter extends Controller {
	public function index() {

        $data['facebook_page_id_FAE'] = $this->config->get('facebook_page_id');
        $data['facebook_jssdk_version_FAE'] = $this->config->get('facebook_jssdk_version');
        $data['facebook_messenger_enabled_FAE'] = $this->config->get('facebook_messenger_activated');
      
		$this->load->language('common/footer');

		$data['scripts'] = $this->document->getScripts('footer');

		$data['text_information'] = $this->language->get('text_information');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_extra'] = $this->language->get('text_extra');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_sitemap'] = $this->language->get('text_sitemap');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_voucher'] = $this->language->get('text_voucher');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_special'] = $this->language->get('text_special');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_newsletter'] = $this->language->get('text_newsletter');

		$data['block2'] = $this->load->controller('common/block2');
		$data['block3'] = $this->load->controller('common/block3');
		$data['block4'] = $this->load->controller('common/block4');

		$trustedshops_info = $this->config->get('trustedshops_info');
					$trustedshops_trustbadge = $this->config->get('trustedshops_trustbadge');
					$trustedshops_product = $this->config->get('trustedshops_product');
					$trustedshops_product_expert = $this->config->get('trustedshops_product_expert');
					
					$trustedshops_info_tsid = $trustedshops_info[$this->config->get('config_language_id')]['tsid'];
					$data['trustedshops_info_tsid'] = $trustedshops_info_tsid;
					
					$data['trustedshops_info_mode'] = $trustedshops_info[$this->config->get('config_language_id')]['mode'];
					$data['trustedshops_info_status'] = $trustedshops_info[$this->config->get('config_language_id')]['status'];
					
					$data['trustedshops_trustbadge_offset'] = $trustedshops_trustbadge[$this->config->get('config_language_id')]['offset'];
					
					if($trustedshops_trustbadge[$this->config->get('config_language_id')]['variant'] == 'reviews'){						
						$data['trustedshops_trustbadge_variant'] = $trustedshops_trustbadge[$this->config->get('config_language_id')]['variant'];
						$data['disableTrustbadge'] = "false";
					} else if($trustedshops_trustbadge[$this->config->get('config_language_id')]['variant'] == 'default'){						
						$data['trustedshops_trustbadge_variant'] = $trustedshops_trustbadge[$this->config->get('config_language_id')]['variant'];
						$data['disableTrustbadge'] = "false";
					} else if($trustedshops_trustbadge[$this->config->get('config_language_id')]['variant'] == 'hide'){						
						$data['trustedshops_trustbadge_variant'] = "reviews";
						$data['disableTrustbadge'] = "true";
					}
					
					$trustedshops_trustbadge_code = str_replace("%tsid%", $trustedshops_info_tsid, $trustedshops_trustbadge[$this->config->get('config_language_id')]['code']);
					$data['trustedshops_trustbadge_code'] = html_entity_decode($trustedshops_trustbadge_code, ENT_QUOTES, 'UTF-8');
					
					$this->load->model('localisation/language');
					$language_data = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
					if($language_data['code'] == 'de') {
						$data['locale'] = 'de-de';
						$locale = 'de_DE';
					} else {
						$data['locale'] = 'en-gb';
						$locale = 'en_GB';
					}
					
					$data['trustedshops_display_review'] = false;
					$data['trustedshops_display_rating'] = false;
					$data['collectReviews'] = false;
					if(true){
					//if($trustedshops_info[$this->config->get('config_language_id')]['status'] == 1){
					
						if($trustedshops_info[$this->config->get('config_language_id')]['mode'] == "expert"){
						
							if($trustedshops_trustbadge[$this->config->get('config_language_id')]['collect_orders'] == 1) {
								if($trustedshops_product_expert[$this->config->get('config_language_id')]['collect_reviews'] == 1) {
									if($trustedshops_product_expert[$this->config->get('config_language_id')]['review_active'] == 1) {
										$data['trustedshops_display_review'] = true;
										$data['collectReviews'] = true;
									} else {
										$data['trustedshops_display_review'] = false;
									}
									
									if($trustedshops_product_expert[$this->config->get('config_language_id')]['rating_active'] == 1) {
										$data['trustedshops_display_rating'] = true;
									} else {
										$data['trustedshops_display_rating'] = false;
									}
								} else {
									$data['trustedshops_display_review'] = false;
									$data['trustedshops_display_rating'] = false;
								}
							} else {
								$data['trustedshops_display_review'] = false;
								$data['trustedshops_display_rating'] = false;
							}

						} else {
							if($trustedshops_product[$this->config->get('config_language_id')]['collect_reviews'] == 1){
								if($trustedshops_product[$this->config->get('config_language_id')]['review_active'] == 1) {
									$data['trustedshops_display_review'] = true;
									$data['collectReviews'] = true;			
								} else {
									$data['trustedshops_display_review'] = false;
								}
								
								if($trustedshops_product[$this->config->get('config_language_id')]['rating_active'] == 1) {
									$data['trustedshops_display_rating'] = true;
								} else {
									$data['trustedshops_display_rating'] = false;
								}
							} else {
								$data['trustedshops_display_review'] = false;
								$data['trustedshops_display_rating'] = false;
							}
						
						}
					} else {
						$data['trustedshops_display_review'] = false;
						$data['trustedshops_display_rating'] = false;
						$data['collectReviews'] = false;
					}

					
					$data['trustedshops_product_expert_review_tab_name'] = $trustedshops_product_expert[$this->config->get('config_language_id')]['review_tab_name'];
					$data['trustedshops_product_review_tab_name'] = $trustedshops_product[$this->config->get('config_language_id')]['review_tab_name'];
					$data['trustedshops_product_review_border_color'] = $trustedshops_product[$this->config->get('config_language_id')]['review_border_color'];
					$data['trustedshops_product_review_star_color'] = $trustedshops_product[$this->config->get('config_language_id')]['review_star_color'];
					
					if(isset($trustedshops_product[$this->config->get('config_language_id')]['review_hide_empty']) && $trustedshops_product[$this->config->get('config_language_id')]['review_hide_empty'] == 1) {
						$data['trustedshops_product_review_hide_empty'] = "true";
					} else {
						$data['trustedshops_product_review_hide_empty'] = "false";
					}
					
					
					$data['trustedshops_product_rating_star_color'] = $trustedshops_product[$this->config->get('config_language_id')]['rating_star_color'];
					$data['trustedshops_product_rating_star_size'] = $trustedshops_product[$this->config->get('config_language_id')]['rating_star_size'];
					$data['trustedshops_product_rating_font_size'] = $trustedshops_product[$this->config->get('config_language_id')]['rating_font_size'];
					
					if(isset($trustedshops_product[$this->config->get('config_language_id')]['rating_hide_empty']) && $trustedshops_product[$this->config->get('config_language_id')]['rating_hide_empty'] == 1) {
						$data['trustedshops_product_rating_hide_empty'] = "false";
					} else {
						$data['trustedshops_product_rating_hide_empty'] = "true";
					}
					
					$trustedshops_product_review_code = str_replace("%tsid%", $trustedshops_info_tsid, $trustedshops_product[$this->config->get('config_language_id')]['review_code']);
					$trustedshops_product_review_code = str_replace("%locale%", $locale, $trustedshops_product_review_code);
					
					
					$trustedshops_product_rating_code = str_replace("%tsid%", $trustedshops_info_tsid, $trustedshops_product[$this->config->get('config_language_id')]['rating_code']);
					
								
					if(isset($this->request->get['route']) && $this->request->get['route'] != "product/product"){
						$data['trustedshops_display_review'] = false;
						$data['trustedshops_display_rating'] = false;
					}

										
					if (isset($this->request->get['product_id'])) {
						$product_id = (int)$this->request->get['product_id'];
						$product_info = $this->model_catalog_product->getProduct($product_id);
						if ($product_info) {
							$data['product_model'] = $product_info['model'];
							$trustedshops_product_review_code = str_replace("%sku%", $product_info['model'], $trustedshops_product_review_code);
							
							$trustedshops_product_rating_code = str_replace("%sku%", $product_info['model'], $trustedshops_product_rating_code);
						}
					} else {
						$product_id = 0;
						$data['product_model'] = '';
					}
					
					$data['trustedshops_product_review_code'] = html_entity_decode($trustedshops_product_review_code, ENT_QUOTES, 'UTF-8');
					$data['trustedshops_product_rating_code'] = html_entity_decode($trustedshops_product_rating_code, ENT_QUOTES, 'UTF-8');
					
					$data['collectorders'] = array();
					if(isset($this->session->data['trustedshop_collectorders'])){
						$data['collectorders'] = $this->session->data['trustedshop_collectorders'];
						
						unset($this->session->data['trustedshop_collectorders']);
						unset($this->session->data['order_total']);
					}
					//die();		

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		//get content from morpheus
		$data['top_footer'] = file_get_contents('include/26.php');

		//set variable to show top footer
		$data['check_top_footer'] = ($_GET['type_product'] != '') ? true : false;

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/account', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		return $this->load->view('common/footer', $data);
	}
}
