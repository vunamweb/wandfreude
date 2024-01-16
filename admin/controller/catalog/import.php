<?php
class ControllerCatalogImport extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/faq');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/import');

		$this->getForm();
	}

	public function add() {
		$id_material = $_POST['material'];

        move_uploaded_file($_FILES['file']['tmp_name'], './controller/catalog/import/'.$_FILES['file']['name']);


        $xlsx = SimpleXLSX::parse('./controller/catalog/import/'.$_FILES['file']['name']);

        $items = $xlsx->rows();

        $this->load->model('catalog/import');


        $this->model_catalog_import->deletePriceMaterial($id_material);

        //print_r($xlsx->rows()); die();

        // echo count($xlsx->rows()); die();
        foreach($items as $item) {
            // print_r($item);

			if(isset($item[1]) && $item[0] && $item[0] != 'qm') {
                $price = $item[1];
                $qm = $item[0];
                $this->model_catalog_import->addPriceMaterial($id_material, $qm, $price);
            }
        }

        // echo 'dd';die();

        $this->response->redirect($this->url->link('catalog/import', 'success=success&' . 'token=' . $this->session->data['token'] . $url, true));
    }

public function edit() {
		$this->load->language('catalog/faq');

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

    protected function getForm() {
		$data['heading_title'] = 'Import';

		$data['text_form'] = 'Import';
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
			'href' => $this->url->link('catalog/import', 'token=' . $this->session->data['token'] . $url, true)
		);


		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (!isset($this->request->get['faq_id'])) {
			$data['action'] = $this->url->link('catalog/import/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/import/edit', 'token=' . $this->session->data['token'] . '&faq_id=' . $this->request->get['faq_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/import', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['faq_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$faq_info = $this->model_catalog_material->getfaq($this->request->get['faq_id']);
		}

		$data['token'] = $this->session->data['token'];

        $this->load->model('catalog/import');

		$data['materials'] = $this->model_catalog_import->getMaterial();
        //print_r($data['material']);die();

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/import_form', $data));
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
