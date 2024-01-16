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

require_once 'Customweb/Payment/Authorization/IPaymentMethod.php';

require_once 'StripeCw/IPaymentMethodDefinition.php';
require_once 'StripeCw/PaymentMethod.php';


class StripeCw_PaymentMethodWrapper implements Customweb_Payment_Authorization_IPaymentMethod, StripeCw_IPaymentMethodDefinition {
	
	private $machineName = "";
	private $backendName = "";
	private $frontendName = "";
	
	/**
	 * @var StripeCw_PaymentMethod
	 */
	private $method = null;
	
	public function __construct(StripeCw_PaymentMethod $method) {
		$this->machineName = $method->getPaymentMethodName();
		$this->method = $method;
		$this->frontendName = $method->getPaymentMethodDisplayName();
		$this->backendName = $method->getBackendPaymentMethodName();
	}
	
	public function getPaymentMethodName() {
		if ($this->getMethod() != null) {
			return $this->getMethod()->getPaymentMethodName();
		}
		else {
			return $this->machineName;
		}
	}
	
	public function getPaymentMethodDisplayName() {
		if ($this->getMethod() != null) {
			return $this->getMethod()->getPaymentMethodDisplayName();
		}
		else {
			return $this->frontendName;
		}
	}
	
	public function getPaymentMethodConfigurationValue($key, $languageCode = null) {
		if ($this->getMethod() != null) {
			return $this->getMethod()->getPaymentMethodConfigurationValue($key, $languageCode);
		}
		else {
			return "";
		}
	}
	
	public function existsPaymentMethodConfigurationValue($key, $languageCode = null) {
		if ($this->getMethod() != null) {
			return $this->getMethod()->existsPaymentMethodConfigurationValue($key, $languageCode);
		}
		else {
			return false;
		}
	}
	
	public function getMachineName() {
		return $this->machineName;
	}
	public function getBackendName() {
		return $this->backendName;
	}
	public function getFrontendName() {
		return $this->frontendName;
	}
	
	public function __sleep() {
		return array('machineName', 'backendName', 'frontendName');
	}
	
	public function __wakeup() {
	}
	
	/**
	 * @return  StripeCw_PaymentMethod
	 */
	protected function getMethod() {
		if ($this->method === null) {
			$this->method = new StripeCw_PaymentMethod($this);
		}
		return $this->method;
	}
	
}
	