<?php
class ControllerCommonMaintenance extends Controller {
	public function index() {

        // added in defensive checks to ensure the
        // controller class is available and accessible
        if (is_file(
          DIR_APPLICATION . '/controller/extension/facebookpageshopcheckoutredirect.php')) {
          $target = 'facebook/facebookproduct/directcheckout';
          if (isset($this->request->get['route'])
            && substr((string)$this->request->get['route'], 0, strlen($target)) == $target) {
            return new Action('extension/facebookpageshopcheckoutredirect');
          }
        }

        if (is_file(
          DIR_APPLICATION . '/controller/extension/facebookeventparameters.php')) {
          // preparing the data for pixel events to be fired
          $this->load->controller('extension/facebookeventparameters');
        }
      
		$this->load->language('common/maintenance');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
			$this->response->addHeader('HTTP/1.1 503 Service Unavailable');
		} else {
			$this->response->addHeader('HTTP/1.0 503 Service Unavailable');
		}

		$this->response->addHeader('Retry-After: 3600');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_maintenance'),
			'href' => $this->url->link('common/maintenance')
		);

		$data['message'] = $this->language->get('text_message');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/maintenance', $data));
	}
}
