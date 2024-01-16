<?php
class ControllerCommonHome extends Controller
{
    public function index()
    {
        //echo "dd"; die();
        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        $addLink = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $uri = $_SERVER['REQUEST_URI'];

        if (str_replace('?', '', $uri) != $uri) {
            $this->document->addLink($addLink, 'noindex');
        } else {
            $this->document->addLink($addLink, 'canonical');
        }

        if (isset($this->request->get['route'])) {
            $this->document->addLink($this->config->get('config_url'), 'canonical');
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top_new');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('common/home', $data));
    }
}
