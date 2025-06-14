<?php
class ControllerInformationInformation extends Controller
{
    public function index()
    {
        //die();
        $this->load->language('information/information');

        $this->load->model('catalog/information');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
        );

        if (isset($this->request->get['information_id'])) {
            $information_id = (int) $this->request->get['information_id'];
        } else {
            $information_id = 0;
        }

        $information_info = $this->model_catalog_information->getInformation($information_id);
        //print_r($information_info);die();

        if (strpos($_SERVER['REQUEST_URI'], 'artikel?')) {
            //print_r($_GET); die();
			$information_info = $this->model_catalog_information->getInformation($_GET['artikel']);
			//print_r($information_info); die();
            $information_id = $information_info['information_id'];

            $alias = $this->model_catalog_information->getSEOInformation($information_id);
            //print_r($alias);die();
            $keyword = $alias['keyword'];

            //print_r($information_info);die();
            $url = constant('HTTPS_SERVER') . $keyword;

            Header("HTTP/1.1 301 Moved Permanently");
            Header("Location: " . $url . "");
        }

        if ($information_info) {
            $this->document->setTitle($information_info['meta_title']);
            $this->document->setDescription($information_info['meta_description']);
            $this->document->setKeywords($information_info['meta_keyword']);

            $data['breadcrumbs'][] = array(
                'text' => $information_info['title'],
                'href' => $this->url->link('information/information', 'information_id=' . $information_id),
            );

            $data['id'] = $information_info['sort_order'];

            $data['heading_title'] = $information_info['title'];

            $data['button_continue'] = $this->language->get('button_continue');

            $data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

            $data['continue'] = $this->url->link('common/home');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('information/information', $data));
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('information/information', 'information_id=' . $information_id),
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['button_continue'] = $this->language->get('button_continue');

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function agree()
    {
        $this->load->model('catalog/information');

        if (isset($this->request->get['information_id'])) {
            $information_id = (int) $this->request->get['information_id'];
        } else {
            $information_id = 0;
        }

        $output = '';

        $information_info = $this->model_catalog_information->getInformation($information_id);

        if ($information_info) {
            $output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
        }

        $this->response->setOutput($output);
    }
}
