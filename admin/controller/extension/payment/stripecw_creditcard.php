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

require_once DIR_APPLICATION . '/controller/stripecw/abstract_method.php';

class ControllerExtensionPaymentStripeCwCreditCard extends ControllerPaymentStripeCwAbstract
{
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->getModuleBasePath() . '_'  . strtolower($this->getMachineName()))) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
	
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function getModuleBasePath() {
		return 'extension/payment/stripecw';
	}
	
	protected function getModuleParentPath() {
		return 'extension/extension';
	}
	
	public function getMachineName() {
		return 'CreditCard';
	}
	public function getBackendName() {
		return 'Stripe: Credit Card';
	}
	public function getFrontendName() {
		return 'Credit Card';
	}
}