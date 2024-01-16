<?php
class ControllerProductFaq extends Controller {
	public function index() {
		$this->load->language('product/faq');

		$data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('tool/image');
		$this->load->model('catalog/faq');


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get($this->config->get('config_theme') . '_product_limit');
		}

		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		
		$filter_data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);


		$data['faqs'] = array();

		$faqs = $this->model_catalog_faq->getFaqs($filter_data);

		foreach ($faqs as $faq) {
			$data['faqs'][] = array(
				'faq_id' => $faq['faq_id'],
				'question'        => $faq['question'],
				'answer'    => html_entity_decode($faq['answer'], ENT_QUOTES, 'UTF-8'),
				'image'        => $this->model_tool_image->resize($faq['image'],$this->config->get('faq_width'),$this->config->get('faq_height'))

			);
		}


			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
		
			$faq_total = $this->model_catalog_faq->getTotalFaqs();
			

			$pagination = new Pagination();
			$pagination->total = $faq_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			
			$pagination->url = $this->url->link('product/faq', $url . '&page={page}');
			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($faq_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($faq_total - $limit)) ? $faq_total : ((($page - 1) * $limit) + $limit), $faq_total, ceil($faq_total / $limit));

			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

		return $this->response->setOutput($this->load->view('product/faq', $data));
	}
}