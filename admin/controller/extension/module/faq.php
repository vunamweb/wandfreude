<?php
class ControllerExtensionModuleFaq extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/faq');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('faq', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_limit'] = $this->language->get('entry_limit');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('breadcrumb_title'),
			'href' => $this->url->link('extension/module/faq', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/module/faq', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->post['faq_status'])) {
			$data['faq_status'] = $this->request->post['faq_status'];
		} else {
			$data['faq_status'] = $this->config->get('faq_status');
		}


		if (isset($this->request->post['faq_limit'])) {
			$data['faq_limit'] = $this->request->post['faq_limit'];
		} else if($this->config->get('faq_limit')){
			$data['faq_limit'] = $this->config->get('faq_limit');
		} else {

			$data['faq_limit'] = 5;
		}

		if (isset($this->request->post['faq_width'])) {
			$data['faq_width'] = $this->request->post['faq_width'];
		} else if($this->config->get('faq_width')){
			$data['faq_width'] = $this->config->get('faq_width');
		} else {

			$data['faq_width'] = 100;
		}


		if (isset($this->request->post['faq_height'])) {
			$data['faq_height'] = $this->request->post['faq_height'];
		} else if($this->config->get('faq_height')){
			$data['faq_height'] = $this->config->get('faq_height');
		} else {

			$data['faq_height'] = 100;
		}

		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/faq', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/faq')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	
	public function install() {
	   
		$this->load->model('extension/module/faq');

		$this->model_extension_module_faq->install();
	}

	public function uninstall() {
		$this->load->model('extension/module/faq');

		$this->model_extension_module_faq->uninstall();
	}
}