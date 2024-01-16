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

require_once 'Customweb/Licensing/StripeCw/License.php';
require_once 'Customweb/IForm.php';
require_once 'Customweb/Form.php';

require_once 'StripeCw/Language.php';
require_once 'StripeCw/Util.php';
require_once 'StripeCw/SettingApi.php';
require_once 'StripeCw/Form/BackendRenderer.php';
require_once 'StripeCw/AbstractController.php';
require_once 'StripeCw/Store.php';


class StripeCw_AbstractModuleController extends StripeCw_AbstractController
{
	
	protected function getModuleBasePath() {
		return 'module/stripecw';
	}
	
	protected function getModuleParentPath() {
		return 'extension/module';	
	}
	
	public function index()
	{
		$this->load->model('stripecw/setting');

		$data = array();
		
		// Store the configuration
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_stripecw_setting->saveSettings(new StripeCw_SettingApi('stripecw'), $this->request->post);
			$data['success'] = StripeCw_Language::_("Save was successful.");
		}

		$this->document->addStyle('view/stylesheet/stripecw.css');
		$this->document->addScript('view/javascript/stripecw.js');

		$heading = StripeCw_Language::_("Main Configurations for Stripe");
		$this->document->setTitle($heading);
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);

		$data['breadcrumbs'][] = array(
				'text'      => StripeCw_Language::_('Modules'),
				'href'      => $this->url->link($this->getModuleParentPath(), 'type=module&token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
				'text'      => StripeCw_Language::_("Main Configurations for Stripe"),
				'href'      => $this->url->link($this->getModuleBasePath(), 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);


		$data['heading_title'] = $heading;
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['action'] = $this->url->link($this->getModuleBasePath(), 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link($this->getModuleParentPath(), 'type=module&token=' . $this->session->data['token'], 'SSL');

		$data['more_link'] = $this->url->link($this->getModuleBasePath() . '/form_overview', 'token=' . $this->session->data['token'], 'SSL');

		$data['tabs'] = $this->model_stripecw_setting->renderStoreTabs($this->url->link($this->getModuleBasePath(), 'token=' . $this->session->data['token'], 'SSL'));

		$info = $this->model_stripecw_setting->render(new StripeCw_SettingApi('stripecw'));
		
		require_once 'Customweb/Licensing/StripeCw/License.php';
Customweb_Licensing_StripeCw_License::run('1hclhmnggtq6mm0t');
		
		if (version_compare(VERSION, '2.0.0.0') >= 0) {
			$this->document->addScript('view/javascript/bootstrap-tab.min.js');
		}
		
		$data['form'] = $info;
		$data['text_edit'] = $heading;
		
		$this->response->setOutput($this->renderView('stripecw/main_settings.tpl', $data, array(
			'common/header',
			'common/footer',
		)));
	}
	

	private function validate()
	{
		if (!$this->user->hasPermission('modify', $this->getModuleBasePath())) {
			// magic getter is by value, thus setting a key as $this->error['warning'] = 'foo' will not work
			$error = $this->error;
			$error['warning'] = $this->language->get('error_permission');
			$this->error = $error;
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function form_overview() {
		$data = array();
		if (isset($_POST['storeId'])) {
			$_SESSION['currentStripeCwStoreId'] = $_POST['storeId'];
			$data['success'] = StripeCw_Language::_("Store has been changed.");
		}
		
		$this->handleStoreSelection();
		
		$this->document->addStyle('view/stylesheet/stripecw_form.css');
		
		$heading = StripeCw_Language::_("Stripe");
		$this->document->setTitle($heading);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => StripeCw_Language::_('Modules'),
			'href'      => $this->url->link($this->getModuleParentPath(), 'type=module&token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);
		
		$data['breadcrumbs'][] = array(
			'text'      => StripeCw_Language::_("Stripe"),
			'href'      => $this->url->link($this->getModuleBasePath(), 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['back'] = $this->url->link($this->getModuleBasePath(), 'token=' . $this->session->data['token'], 'SSL');
		
		$data['current_store_id'] = StripeCw_Store::getStoreId();
		$data['stores'] = StripeCw_Store::getStores();

		$forms = array();
		$adapter = $this->getBackendFormAdapter();
		if ($adapter !== null) {
			$forms = $adapter->getForms();
		}
		$data['forms'] = $forms;
		
		$data['heading_title'] = $heading;
		
		if (!isset($data['success'])) {
			$data['success'] = false;
		}
		
		$data['url'] = $this->url;
		$data['module_base_path'] = $this->getModuleBasePath();
		$data['token'] = $this->session->data['token'];
		
		$this->response->setOutput($this->renderView('stripecw/form/overview.tpl', $data, array(
			'common/header',
			'common/footer',
		)));
		
	}

	public function form_view($data = array()) {
		if (isset($_POST['storeId'])) {
			$_SESSION['currentStripeCwStoreId'] = $_POST['storeId'];
			$data['success'] = StripeCw_Language::_("Store has been changed.");
		}
		
		$this->handleStoreSelection();
		$form = $this->getCurrentForm();
		
		$this->document->addStyle('view/stylesheet/stripecw_form.css');
		
		$heading = StripeCw_Language::_("Stripe") . ': ' . $form->getTitle();
		$this->document->setTitle($heading);
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
				'text'      => StripeCw_Language::_('Modules'),
				'href'      => $this->url->link($this->getModuleParentPath(), 'type=module&token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => StripeCw_Language::_("Stripe"),
			'href'      => $this->url->link($this->getModuleBasePath(), 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => StripeCw_Language::_("More"),
			'href'      => $this->url->link($this->getModuleBasePath() . '/form_overview', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);
		
		$data['back'] = $this->url->link($this->getModuleBasePath() . '/form_overview', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['stores'] = StripeCw_Store::getStores();
		
		if ($form->isProcessable()) {
			$form = new Customweb_Form($form);
			$form->setTargetUrl($this->url->link($this->getModuleBasePath() . '/form_save', 'token=' . $this->session->data['token'] . '&form=' . $form->getMachineName(), 'SSL'))->setRequestMethod(Customweb_IForm::REQUEST_METHOD_POST);
		}
		
		$data['current_store_id'] = StripeCw_Store::getStoreId();
		
		$renderer = new StripeCw_Form_BackendRenderer();
		$data['form'] = $form;
		$data['formHtml'] = $renderer->renderForm($form);
		
		$data['heading_title'] = $heading;
		
		if (!isset($data['success'])) {
			$data['success'] = false;
		}
		
		$data['url'] = $this->url;
		$data['token'] = $this->session->data['token'];
		
		$this->response->setOutput($this->renderView('stripecw/form/view.tpl', $data, array(
			'common/header',
			'common/footer',
		)));
		
	}
	
	public function form_save() {
		$this->handleStoreSelection();
		$form = $this->getCurrentForm();
		
		$params = $_REQUEST;
		if (isset($params['pressed_button']) || isset($params['button'])) {
			$pressedButton = null;
			if (isset($params['pressed_button'])) {
				$buttonName = $_REQUEST['pressed_button'];
				foreach ($form->getButtons() as $button) {
					if ($button->getMachineName() == $buttonName) {
						$pressedButton = $button;
					}
				}
			}
			else if (isset($params['button'])) {
				$pressedButton = null;
				foreach ($params['button'] as $buttonName => $value) {
					foreach ($form->getButtons() as $button) {
						if ($button->getMachineName() == $buttonName) {
							$pressedButton = $button;
						}
					}
				}
			}
			
			if ($pressedButton === null) {
				throw new Exception("Could not find pressed button.");
			}
			$this->getBackendFormAdapter()->processForm($form, $pressedButton, $params);
		}
		$this->form_view(array('success' => StripeCw_Language::_('Successful saved.')));
	}
	
	/**
	 * @return Customweb_IForm
	 */
	private function getCurrentForm() {
		$adapter = $this->getBackendFormAdapter();
	
		if ($adapter !== null && isset($_GET['form'])) {
			$forms = $adapter->getForms();
			$formName = $_GET['form'];
			$currentForm = null;
			foreach ($forms as $form) {
				if ($form->getMachineName() == $formName) {
					return $form;
				}
			}
		}
	
		die('No form is set.');
	}
	
	/**
	 * @return Customweb_Payment_BackendOperation_Form_IAdapter
	 */
	private function getBackendFormAdapter() {
		try {
			return StripeCw_Util::getContainer()->getBean('Customweb_Payment_BackendOperation_Form_IAdapter');
		}
		catch(Customweb_DependencyInjection_Exception_BeanNotFoundException $e) {
			return null;
		}
	}
	
	private function handleStoreSelection() {
		$currentStoreId = StripeCw_Store::getStoreId();
		if (isset($_SESSION['currentStripeCwStoreId'])) {
			$currentStoreId = $_SESSION['currentStripeCwStoreId'];
			StripeCw_Store::forceStoreId($currentStoreId);
		}
	}


	public function install() {
		
		// Add the modification entry.
		if (version_compare(VERSION, '2.0.0.0') >= 0) {
			$modificationName = 'stripecw';
			$rs = StripeCw_Util::getDriver()->query("SELECT * FROM " . DB_PREFIX . "modification WHERE name = >name");
			$rs->execute(array('>name' => $modificationName));
			if ($rs->fetch() !== false) {
				//Delete existing modification otherwise it will never be updated
				$rs = StripeCw_Util::getDriver()->query("DELETE IGNORE FROM " . DB_PREFIX . "modification WHERE name = >name");
				$rs->execute(array('>name' => $modificationName));				
			}
			$this->load->model('extension/modification');
			$data = array(
				'name' => $modificationName,
				'author' => 'customweb ltd',
				'version' => '1.0.0',
				'link' => 'https://www.sellxed.com',
				'code' => file_get_contents(DIR_SYSTEM . '/library/cw/StripeCw/Modification/stripecw.xml'), //Opencart 2.0.0.0 uses the 'code' field instead of 'xml'
				'status' => '1',
				'xml' => file_get_contents(DIR_SYSTEM . '/library/cw/StripeCw/Modification/stripecw.xml'),
			);
			$this->model_extension_modification->addModification($data);
		
		}
		
		StripeCw_Util::migrate();
	}

	public function uninstall() {

	}
	
	
}