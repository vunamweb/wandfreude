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

require_once 'Customweb/Payment/Authorization/ITransactionContext.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Communication/Abstract.php';
require_once 'Customweb/Stripe/Communication/Charge/ResponseProcessor.php';
require_once 'Customweb/Stripe/Util.php';
require_once 'Customweb/Payment/Util.php';

class Customweb_Stripe_Communication_Charge_Adapter extends Customweb_Stripe_Communication_Abstract {
	private static $FRAGMENT = 'charges';
	private $transaction;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container){
		parent::__construct($container, $transaction->isLiveTransaction());
		$this->transaction = $transaction;
	}

	public function process($cache = true){
		// never create charges for zero transactions.
		if (Customweb_Stripe_Util::isZeroTransaction($this->getTransaction())) {
			if (!$this->getTransaction()->isAuthorized()) {
				$this->getTransaction()->authorize(Customweb_I18n_Translation::__('Zero Transaction successful.'));
				$this->getTransaction()->setChargeId(Customweb_I18n_Translation::__('Zero Charge ID'));
				$this->getTransaction()->capture();
			}
		}
		else {
			return parent::process($cache);
		}
	}

	protected function buildUrl(){
		return $this->getContainer()->getConfiguration()->getApiUrl() . self::$FRAGMENT;
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_Charge_ResponseProcessor($this->getTransaction(),
				$this->getContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Shop_ICapture'));
	}

	protected function buildBody(){
		$parameters = array(
			'currency' => strtolower($this->getTransaction()->getCurrencyCode()),
			'description' => Customweb_Payment_Util::applyOrderSchemaImproved($this->getContainer()->getConfiguration()->getOrderIdSchema(),
					$this->getTransaction()->getExternalTransactionId(), 100),
			'amount' => Customweb_Util_Currency::formatAmount($this->getTransaction()->getAuthorizationAmount(),
					$this->getTransaction()->getCurrencyCode(), ''),
			'metadata' => array(
				'cw_transaction_id' => $this->getTransaction()->getExternalTransactionId() 
			) 
		);
		
		$parameters['source'] = $this->getTransaction()->getSource();
		
		$capture = $this->getCapture();
		if ($capture !== null) {
			$parameters['capture'] = $capture;
		}
		if ($this->getContainer()->getConfiguration()->isReceiptEmailActive()) {
			$parameters['receipt_email'] = $this->getTransaction()->getTransactionContext()->getOrderContext()->getCustomerEMailAddress();
		}
		if ($this->getTransaction()->getCustomerId() != null) {
			$parameters['customer'] = $this->getTransaction()->getCustomerId();
		}
		
		$this->addShopId($parameters);
		
		return $parameters;
	}

	private function getCapture(){
		$mode = $this->getTransaction()->getTransactionContext()->getCapturingMode();
		if ($mode === null) {
			return $this->getContainer()->getPaymentMethodByTransaction($this->getTransaction())->getCaptureMode();
		}
		else if ($mode == Customweb_Payment_Authorization_ITransactionContext::CAPTURING_MODE_DEFERRED) {
			return 'false';
		}
		else if ($mode == Customweb_Payment_Authorization_ITransactionContext::CAPTURING_MODE_DIRECT) {
			return 'true';
		}
		return null;
	}

	protected function getTransaction(){
		return $this->transaction;
	}

	protected function getIdempotencyKey(){
		return $this->getTransaction()->getChargeIdempotencyKey();
	}
}