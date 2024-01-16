<?php
/******************************************************
 * @package GTM Enhanced Ecommerce
 * @version 1.0
 * @author Ahsan Shakeel
 * @link https://www.flux.pk
 * @copyright Copyright (C) 2018. Flux (Pvt) Ltd. All rights reserved.
 * @email:info@flux.pk 
 * $date: 19 Dec 2018
*******************************************************/

class ControllerExtensionModuleTagManager extends Controller {
	
	private $error = array();
	
	public function index() {
		
		// Edit these next three lines to suit your setup and extension ID
		$data['oc_licensing_home'] = 'https://www.enemstore.com/'; // Important to have trailing slash at the end of the SSL URL!
		$data['extension_id'] = 12345;   // Replace extension ID with your extension ID
		$admin_support_email = 'info@flux.pk';

		$this->load->language('extension/module/oc_licensing');
		$this->load->language('extension/module/tagmanager');
		

		if(isset($this->request->get['emailmal'])){
			$data['emailmal'] = true;
		}

		if(isset($this->request->get['regerror'])){	
		    if($this->request->get['regerror']=='emailmal'){
		    	$this->error['warning'] = $this->language->get('regerror_email');
		    }elseif($this->request->get['regerror']=='orderidmal'){
		    	$this->error['warning'] = $this->language->get('regerror_orderid');
		    }elseif($this->request->get['regerror']=='noreferer'){
		    	$this->error['warning'] = $this->language->get('regerror_noreferer');
		    }elseif($this->request->get['regerror']=='localhost'){
		    	$this->error['warning'] = $this->language->get('regerror_localhost');
		    }elseif($this->request->get['regerror']=='licensedupe'){
		    	$this->error['warning'] = $this->language->get('regerror_licensedupe');
		    }
		}

		$domainssl = explode("//", HTTPS_SERVER);
		$domainnonssl = explode("//", HTTP_SERVER);
		$domain = ($domainssl[1] != '' ? $domainssl[1] : $domainnonssl[1]);

		$data['licensed'] = @file_get_contents($data['oc_licensing_home'] . 'licensed.php?domain=' . $domain . '&extension=' . $data['extension_id']);

		if(!$data['licensed'] || $data['licensed'] == ''){
			if(extension_loaded('curl')) {
		        $post_data = array('domain' => $domain, 'extension' => $data['extension_id']);
		        $curl = curl_init();
		        curl_setopt($curl, CURLOPT_HEADER, false);
		        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		        curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		        $follow_allowed = ( ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;
		        if ($follow_allowed) {
		            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		        }
		        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 9);
		        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		        curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
		        curl_setopt($curl, CURLOPT_VERBOSE, 1);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($curl, CURLOPT_URL, $data['oc_licensing_home'] . 'licensed.php');
		        curl_setopt($curl, CURLOPT_POST, true);
		        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
		        $data['licensed'] = curl_exec($curl);
		        curl_close($curl);
		    }else{
		        $data['licensed'] = 'curl';
		    }
		}

		$data['licensed_md5'] = md5($data['licensed']);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		//$this->updateDatabase(); // FOR UPGRADE

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_tagmanager', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			


			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tagmanager', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/tagmanager', 'user_token=' . $this->session->data['user_token'] , true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['token'] = $this->session->data['user_token'];
		$data['user_token'] = $this->session->data['user_token'];
		if (isset($this->request->post['module_tagmanager_status'])) {
			$data['module_tagmanager_status'] = $this->request->post['module_tagmanager_status'];
		} else {
			$data['module_tagmanager_status'] = $this->config->get('module_tagmanager_status');
		}				
		if (isset($this->request->post['module_tagmanager_code'])) {
			$data['module_tagmanager_code'] = $this->request->post['module_tagmanager_code'];
		} else {
			$data['module_tagmanager_code'] = $this->config->get('module_tagmanager_code');
		}
		if (isset($this->request->post['module_license_code'])) {
			$data['module_license_code'] = $this->request->post['module_license_code'];
		} else {
			$data['module_license_code'] = $this->config->get('module_license_code');
		}
		if (isset($this->request->post['module_tagmanager_admin'])) {
			$data['module_tagmanager_admin'] = $this->request->post['module_tagmanager_admin'];
		} else {
			$data['module_tagmanager_admin'] = $this->config->get('module_tagmanager_admin');
		}
		if (isset($this->request->post['module_tagmanager_adword'])) {
			$data['module_tagmanager_adword'] = $this->request->post['module_tagmanager_adword'];
		} else {
			$data['module_tagmanager_adword'] = $this->config->get('module_tagmanager_adword');
		}
		if (isset($this->request->post['module_tagmanager_conversion_id'])) {
			$data['module_tagmanager_conversion_id'] = $this->request->post['module_tagmanager_conversion_id'];
		} else {
			$data['module_tagmanager_conversion_id'] = $this->config->get('module_tagmanager_conversion_id');
		}
		
		if (isset($this->request->post['module_tagmanager_conversion_label'])) {
			$data['module_tagmanager_conversion_label'] = $this->request->post['module_tagmanager_conversion_label'];
		} else {
			$data['module_tagmanager_conversion_label'] = $this->config->get('module_tagmanager_conversion_label');
		}
		if (isset($this->request->post['module_tagmanager_remarketing'])) {
			$data['module_tagmanager_remarketing'] = $this->request->post['module_tagmanager_remarketing'];
		} else {
			$data['module_tagmanager_remarketing'] = $this->config->get('module_tagmanager_remarketing');
		}
		if (isset($this->request->post['module_tagmanager_userid_status'])) {
			$data['module_tagmanager_userid_status'] = $this->request->post['module_tagmanager_userid_status'];
		} else {
			$data['module_tagmanager_userid_status'] = $this->config->get('module_tagmanager_userid_status');
		}
		if (isset($this->request->post['module_tagmanager_product'])) {
			$data['module_tagmanager_product'] = $this->request->post['module_tagmanager_product'];
		} else {
			$data['module_tagmanager_product'] = $this->config->get('module_tagmanager_product');
		}
		if (isset($this->request->post['module_tagmanager_ptitle'])) {
			$data['module_tagmanager_ptitle'] = $this->request->post['module_tagmanager_ptitle'];
		} else {
			$data['module_tagmanager_ptitle'] = $this->config->get('module_module_tagmanager_ptitle');
		}
		if (isset($this->request->post['module_tagmanager_gid'])) {
			$data['module_tagmanager_gid'] = $this->request->post['module_tagmanager_gid'];
		} else {
			$data['module_tagmanager_gid'] = $this->config->get('module_tagmanager_gid');
		}
		if (isset($this->request->post['module_tagmanager_mp'])) {
			$data['module_tagmanager_mp'] = $this->request->post['module_tagmanager_mp'];
		} else {
			$data['module_tagmanager_mp'] = $this->config->get('module_tagmanager_mp');
		}
		
		$data['product_map'] = array ('product_id','model','sku','model_product_id','product_id_currency');
		$data['product_title'] = array ('name','brand_model');
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tagmanager', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tagmanager')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['module_tagmanager_code']) {
			$this->error['code'] = $this->language->get('error_code');
		}			

		return !$this->error;
	}
	
	public function install(){
        $this->updateDatabase();

    }

	 public function uninstall() {
     

    }

	 private function updateDatabase() {
     
     $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "analytics_tracking` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `order_id` int(11) DEFAULT NULL,
           `cid` varchar(128) DEFAULT NULL,
		   `uid` varchar(64) DEFAULT NULL,
		   `ip` varchar(64) DEFAULT NULL,
		   `geoid` varchar(64) DEFAULT NULL,
		   `sr` varchar(64) DEFAULT NULL,
		   `vp` varchar(64) DEFAULT NULL,
		   `ul` varchar(64) DEFAULT NULL,
		   `dr` varchar(250) DEFAULT NULL,
           `hit` tinyint(1) NOT NULL DEFAULT '0',
		   `tid` varchar(24) DEFAULT NULL,	
		   `user_agent` varchar(250) DEFAULT NULL,
		   `currency_code` varchar(11) DEFAULT NULL,
		   `currency_id` int(11) DEFAULT NULL,
             PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
          
   }
  
  private function columnExistsInTable($table, $column) {
        $query = $this->db->query("DESC `" . DB_PREFIX . $table . "`;");
        foreach($query->rows as $row) {
            if($row['Field'] == $column) {
                return true;
            }
        }
        return false;
    }
}
?>