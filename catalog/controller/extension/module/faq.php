<?php
class ControllerExtensionModuleFaq extends Controller {
	public function index() {
		$this->load->language('extension/module/faq');

		$data['heading_title'] = $this->language->get('heading_title');
		$data['faq_page'] = $this->language->get('faq_page');

		$this->load->model('tool/image');
		$this->load->model('extension/module/faq');

		$data['faq_link'] = $this->url->link('product/faq');

		$data['faqs'] = array();

		$faqs = $this->model_extension_module_faq->getFaqs($this->config->get('faq_limit'));
		$data['faq_limit'] = $this->language->get('faq_limit');
		$faq_limit = $this->config->get('faq_limit');
		foreach ($faqs as $faq) {
			$data['faqs'][] = array(
				'faq_id' => $faq['faq_id'],
				'question'        => $faq['question'],
				'answer'    => html_entity_decode($faq['answer'], ENT_QUOTES, 'UTF-8'),
				'image'        => $this->model_tool_image->resize($faq['image'],$this->config->get('faq_width'),$this->config->get('faq_height'))
			);
		}

		return $this->load->view('extension/module/faq', $data);
	}
}