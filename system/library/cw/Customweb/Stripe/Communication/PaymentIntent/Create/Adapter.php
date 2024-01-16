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
require_once 'Customweb/Stripe/Authorization/Transaction.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/Stripe/Communication/Abstract.php';
require_once 'Customweb/Stripe/Communication/PaymentIntent/Create/ResponseProcessor.php';
require_once 'Customweb/Payment/Util.php';


/**
 * Processes a PaymentIntent.create request.
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_PaymentIntent_Create_Adapter extends Customweb_Stripe_Communication_Abstract {
	protected static $FRAGMENT = 'payment_intents';
	private $transaction;
	/**
	 * If transaction should immediately be confirmed (e.g. recurring)
	 * @var bool
	 */
	private $confirm;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container, $confirm = false){
		parent::__construct($container, $transaction->isLiveTransaction());
		$this->transaction = $transaction;
		$this->confirm = $confirm;
	}

	protected function getMethod(){
		return 'POST';
	}

	protected function buildUrl(){
		return $this->getContainer()->getConfiguration()->getApiUrl() . self::$FRAGMENT;
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_PaymentIntent_Create_ResponseProcessor($this->getTransaction(), $this->getContainer());
	}

	protected function getPaymentMethod($alias = null){
		$alias = $this->getTransaction()->getTransactionContext()->getAlias();
		if ($alias instanceof Customweb_Stripe_Authorization_Transaction) {
			return $alias->getAlias();
		}
		return $this->extractPaymentMethod($this->getTransaction());
	}

	private function extractPaymentMethod(Customweb_Stripe_Authorization_Transaction $transaction){
		$method = $transaction->getPaymentMethodId();
		if ($method) {
			return $method;
		}
		$method = $transaction->getThreeDSource();
		if ($method) {
			return $method;
		}
		return $transaction->getSource();
	}

	protected function buildBody(){
		$parameters = array(
			'currency' => strtolower($this->getTransaction()->getCurrencyCode()),
			'payment_method' => $this->getPaymentMethod(),
			'amount' => Customweb_Util_Currency::formatAmount($this->getTransaction()->getAuthorizationAmount(),
					$this->getTransaction()->getCurrencyCode(), ''),
			'metadata' => array(
				'cw_transaction_id' => $this->getTransaction()->getExternalTransactionId()
			),
			'description' => Customweb_Payment_Util::applyOrderSchemaImproved($this->getContainer()->getConfiguration()->getOrderIdSchema(),
					$this->getTransaction()->getExternalTransactionId(), 100)
		);

		$token = $this->getTransaction()->getToken();
		if ($token) {
			unset($parameters['payment_method']);
			$parameters['payment_method_data'] = array(
				'type' => 'card',
				'card' => array(
					'token' => $token
				)
			);
		}

		$capture = $this->getCaptureMethod();
		if ($capture !== null) {
			$parameters['capture_method'] = $capture;
		}
		if ($this->getContainer()->getConfiguration()->isReceiptEmailActive()) {
			$parameters['receipt_email'] = $this->getTransaction()->getTransactionContext()->getOrderContext()->getCustomerEMailAddress();
		}

		if ($this->getTransaction()->getTransactionContext()->createRecurringAlias()) {
			$parameters['setup_future_usage'] = 'off_session';
		}
		else if ($this->getTransaction()->getTransactionContext()->getAlias() == 'new') {
			$parameters['setup_future_usage'] = 'on_session';
		}

		$customer = $this->getTransaction()->getCustomerId();
		if ($customer) {
			$parameters['customer'] = $customer;
		}

		if ($this->getConfirm()) {
			$parameters['confirm'] = 'true';
		}

		$this->addShopId($parameters);

		return $parameters;
	}

	private function getCaptureMethod(){
		$mode = $this->getTransaction()->getTransactionContext()->getCapturingMode();
		if ($mode === null) {
			return $this->getContainer()->getPaymentMethodByTransaction($this->getTransaction())->getCaptureMode();
		}
		else if ($mode == Customweb_Payment_Authorization_ITransactionContext::CAPTURING_MODE_DEFERRED) {
			return 'manual';
		}
		else if ($mode == Customweb_Payment_Authorization_ITransactionContext::CAPTURING_MODE_DIRECT) {
			return 'automatic';
		}
		return null;
	}

	protected function getConfirm(){
		return $this->confirm;
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}