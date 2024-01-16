<?php
/**
  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */

require_once DIR_SYSTEM . '/library/cw/StripeCw/init.php';


require_once 'StripeCw/Language.php';
require_once 'StripeCw/Util.php';
require_once 'StripeCw/IPaymentMethodDefinition.php';
require_once 'StripeCw/Entity/Transaction.php';
require_once 'StripeCw/PaymentMethod.php';
require_once 'StripeCw/AbstractController.php';


abstract class ControllerPaymentStripeCwAbstract extends StripeCw_AbstractController implements StripeCw_IPaymentMethodDefinition
{
	protected function getModuleBasePath() {
		return 'payment/stripecw';
	}
	
	protected function getModuleParentPath() {
		return 'extension/payment';
	}
	
	
	public function index() {
		$data = array();
		$this->load->model('stripecw/setting');
		$paymentMethod = new StripeCw_PaymentMethod($this);

		// Store the configuration
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_stripecw_setting->saveSettings($paymentMethod->getSettingsApi(), $this->request->post);
			$data['success'] = StripeCw_Language::_("Save was successful.");
		}

		$this->document->addStyle('view/stylesheet/stripecw.css');
		$this->document->addScript('view/javascript/stripecw.js');


		$heading = StripeCw_Language::_("Configurations for !method (Stripe)", array('!method' => $paymentMethod->getPaymentMethodDisplayName()));
		$this->document->setTitle($heading);

		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => StripeCw_Language::_('Modules'),
			'href'      => $this->url->link($this->getModuleParentPath(), 'type=payment&token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => $heading,
			'href'      => $this->url->link($this->getModuleBasePath() . $this->code, 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);


		$data['more_link'] = $this->url->link($this->getModuleBasePath() . '/form_overview', 'token=' . $this->session->data['token'], 'SSL');
		$data['heading_title'] = $heading;
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');


		$data['action'] = $this->url->link($this->getModuleBasePath() . '_' . strtolower($paymentMethod->getPaymentMethodName()), 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link($this->getModuleParentPath(), 'type=payment&token=' . $this->session->data['token'], 'SSL');

		$data['tabs'] = $this->model_stripecw_setting->renderStoreTabs($this->url->link($this->getModuleBasePath() . '_'  . strtolower($paymentMethod->getPaymentMethodName()), 'token=' . $this->session->data['token'], 'SSL'));
		$data['form'] = $this->model_stripecw_setting->render($paymentMethod->getSettingsApi());
		$data['text_edit'] = $heading;

		if (version_compare(VERSION, '2.0.0.0') >= 0) {
			$this->document->addScript('view/javascript/bootstrap-tab.min.js');
		}
		
		$this->response->setOutput($this->renderView('stripecw/settings.tpl', $data, array(
			'common/header',
			'common/footer',
		)));
	}

	public function install() {
		StripeCw_Util::migrate();
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/stripecw')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function orderAction() {
		$orderId = $this->request->get['order_id'];

		$transactions = StripeCw_Entity_Transaction::getTransactionsByOrderId($orderId);

		$data = array();
		$data['transactions'] = $transactions;
		$this->response->setOutput($this->renderView('stripecw/order_form.tpl', $data));
	}

}