<?php
class ControllerCatalogMaterial extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/material');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/material');

		$this->getList();
	}

	public function add() {
		//echo "d" ;die();
        $this->load->language('catalog/material');

		$this->document->setTitle($this->language->get('heading_title'));
        
        //echo "dfgg" ;die();

		$this->load->model('catalog/material');
        
        //echo "dzzz" ;die();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			//echo "d" ;die();
            
            $this->model_catalog_material->addFaq($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['question'])) {
				$url .= '&question=' . urlencode(html_entity_decode($this->request->get['question'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['answer'])) {
				$url .= '&answer=' . urlencode(html_entity_decode($this->request->get['answer'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('catalog/material', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

     public function edit() {
		$this->load->language('catalog/material');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/material');
        //die();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_material->editFaq($this->request->get['faq_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

		
			if (isset($this->request->get['question'])) {
				$url .= '&question=' . urlencode(html_entity_decode($this->request->get['question'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['answer'])) {
				$url .= '&answer=' . urlencode(html_entity_decode($this->request->get['answer'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('catalog/material', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}
    
    public function delete() {
		$this->load->language('catalog/material');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/material');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_catalog_material->deleteFaq($id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['question'])) {
				$url .= '&question=' . urlencode(html_entity_decode($this->request->get['title'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['answer'])) {
				$url .= '&answer=' . urlencode(html_entity_decode($this->request->get['answer'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('catalog/material', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
	    
		if (isset($this->request->get['question'])) {
			$question = $this->request->get['question'];
		} else {
			$question = '';
		}

		if (isset($this->request->get['answer'])) {
			$answer = $this->request->get['answer'];
		} else {
			$answer = '';
		}

		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['question'])) {
			$url .= '&question=' . urlencode(html_entity_decode($this->request->get['question'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['answer'])) {
			$url .= '&answer=' . urlencode(html_entity_decode($this->request->get['answer'], ENT_QUOTES, 'UTF-8'));
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/faq', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/material/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/material/delete', 'token=' . $this->session->data['token'] . $url, true);

		

		$filter_data = array(
			'question'    => $question,
			'answer'     => $answer,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

        $this->load->model('tool/image');
		$faq_total = $this->model_catalog_material->getTotalFaqs($filter_data);

		$results = $this->model_catalog_material->getFaqs($filter_data);
       
        $data['faqs']=array();
		
		foreach ($results as $result) {
			$data['faqs'][] = array(
				'faq_id'  => $result['faq_id'],
				'question'       => $result['question'],
				'answer'     => strip_tags(html_entity_decode($result['answer'], ENT_QUOTES, 'UTF-8')),
				'image'     => $result['image'],
				'thumb'     => $this->model_tool_image->resize($result['image'], 100, 100),
				'edit'       => $this->url->link('catalog/material/edit', 'token=' . $this->session->data['token'] . '&faq_id=' . $result['faq_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_question'] = $this->language->get('column_question');
		$data['column_answer'] = $this->language->get('column_answer');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_image'] = $this->language->get('column_image');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_question'] = $this->language->get('entry_question');
		$data['entry_answer'] = $this->language->get('entry_answer');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

        
		$url = '';

		if (isset($this->request->get['question'])) {
			$url .= '&question=' . urlencode(html_entity_decode($this->request->get['question'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['answer'])) {
			$url .= '&answer=' . urlencode(html_entity_decode($this->request->get['answer'], ENT_QUOTES, 'UTF-8'));
		}

		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_question'] = $this->url->link('catalog/faq', 'token=' . $this->session->data['token'] . '&sort=f.question' . $url, true);
		$data['sort_answer'] = $this->url->link('catalog/faq', 'token=' . $this->session->data['token'] . '&sort=f.answer' . $url, true);
		
		$url = '';

		if (isset($this->request->get['question'])) {
			$url .= '&question=' . urlencode(html_entity_decode($this->request->get['question'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['answer'])) {
			$url .= '&answer=' . urlencode(html_entity_decode($this->request->get['answer'], ENT_QUOTES, 'UTF-8'));
		}

		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $faq_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/material', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($faq_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($faq_total - $this->config->get('config_limit_admin'))) ? $faq_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $faq_total, ceil($faq_total / $this->config->get('config_limit_admin')));

		$data['question'] = $question;
		$data['answer'] = $answer;
		

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/material_list', $data));
	}
	
	
	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_question'] = $this->language->get('entry_question');
		$data['entry_answer'] = $this->language->get('entry_answer');
		
		$data['entry_column'] = $this->language->get('entry_column');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['question'])) {
			$data['error_question'] = $this->error['question'];
		} else {
			$data['error_question'] = '';
		}

		if (isset($this->error['answer'])) {
			$data['error_answer'] = $this->error['answer'];
		} else {
			$data['error_answer'] = '';
		}
			
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/material', 'token=' . $this->session->data['token'] . $url, true)
		);
		
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (!isset($this->request->get['faq_id'])) {
			$data['action'] = $this->url->link('catalog/material/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/material/edit', 'token=' . $this->session->data['token'] . '&faq_id=' . $this->request->get['faq_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/material', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['faq_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$faq_info = $this->model_catalog_material->getfaq($this->request->get['faq_id']);
		}

		$data['token'] = $this->session->data['token'];

        if (isset($this->request->post['faq_description'])) {
			$data['faq_description'] = $this->request->post['faq_description'];
		} elseif (isset($this->request->get['faq_id'])) { 
			//die();
            $data['faq_description'] = $this->model_catalog_material->getfaqDescription($this->request->get['faq_id']);
            //print_r($data['faq_description']);
		} else {
			$data['faq_description'] = array();
		}

    
        if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($faq_info)) {
			$data['image'] = $faq_info['image'];
		} else {
			$data['image'] = '';
		}

        if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($faq_info)) {
			$data['status'] = $faq_info['status'];
		} else {
			$data['status'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
		    
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
			
		} elseif (!empty($faq_info) && is_file(DIR_IMAGE . $faq_info['image'])) {
		    
			$data['thumb'] = $this->model_tool_image->resize($faq_info['image'], 100, 100);
			
		} else {
		    
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		

		$this->response->setOutput($this->load->view('catalog/material_form', $data));
	}

	protected function validateForm() {
		
		if (!$this->user->hasPermission('modify', 'catalog/faq')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['faq_description'] as $language_id => $value) {
			if ((utf8_strlen($value['question']) < 2) || (utf8_strlen($value['question']) > 255)) {
				$this->error['question'][$language_id] = $this->language->get('error_question');
			}

			if ((utf8_strlen($value['answer']) < 3)) {
				$this->error['answer'][$language_id] = $this->language->get('error_answer');
			}
		}
		
		return true; //!$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/faq')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
