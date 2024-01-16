<?php

/**
 *  * You are allowed to use this API in your web application.
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

require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Communication/Response/DefaultProcessor.php';

class Customweb_Stripe_Communication_Charge_ResponseProcessor extends Customweb_Stripe_Communication_Response_DefaultProcessor {
	/**
	 *
	 * @var Customweb_Stripe_Authorization_Transaction
	 */
	private $transaction;
	/**
	 *
	 * @var Customweb_Payment_BackendOperation_Adapter_Shop_ICapture
	 */
	private $captureAdapter;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_Payment_BackendOperation_Adapter_Shop_ICapture $captureAdapter){
		$this->transaction = $transaction;
		$this->captureAdapter = $captureAdapter;
	}

	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		
		$this->getTransaction()->setChargeId($json['id']);
		
		if ($json['status'] == 'succeeded') {
			$callAdapter = true;
			if (!$this->getTransaction()->isAuthorized()) {
				$this->getTransaction()->authorize();
				$callAdapter = false;
			}
			if ($json['captured']) {
				if ($this->getTransaction()->isCapturePossible()) {
					$this->getTransaction()->capture();
					if ($callAdapter) {
						$this->getCaptureAdapter()->capture($this->getTransaction());
					}
				}
			}
			$this->getTransaction()->setAuthorizationUncertain(false);
			return true;
		}
		if ($json['status'] == 'pending') {
			if (!$this->getTransaction()->isAuthorized()) {
				$this->getTransaction()->authorize();
			}
			$this->getTransaction()->setAuthorizationUncertain();
			return true;
		}
		if ($json['status'] == 'failed') {
			if (!$this->getTransaction()->isAuthorized() && !$this->getTransaction()->isAuthorizationFailed()) {
				$this->getTransaction()->setAuthorizationFailed(Customweb_I18n_Translation::__('The charge has failed.'));
			}
			else if ($this->getTransaction()->isAuthorizationUncertain()) {
				$this->getTransaction()->setUncertainTransactionFinallyDeclined();
			}
			return false;
		}
	}
	
	protected function processError(Customweb_Payment_Authorization_ErrorMessage $message) {
		$this->getTransaction()->setAuthorizationFailed($message);
		return false;
	}

	private function getCaptureAdapter(){
		return $this->captureAdapter;
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}