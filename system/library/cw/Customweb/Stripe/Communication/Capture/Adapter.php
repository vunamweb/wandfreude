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

require_once 'Customweb/Stripe/Communication/Capture/ResponseProcessor.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/Stripe/Communication/Abstract.php';
require_once 'Customweb/Util/Invoice.php';

class Customweb_Stripe_Communication_Capture_Adapter extends Customweb_Stripe_Communication_Abstract {
	private $transaction;
	private $items;
	private static $FRAGMENT = 'charges/{id}/capture';
	private static $FRAGMENT_INTENT = 'payment_intents/{id}/capture';

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, array $items, Customweb_DependencyInjection_IContainer $container){
		parent::__construct($container, $transaction->isLiveTransaction());
		$this->transaction = $transaction;
		$this->items = $items;
	}

	protected function buildUrl(){
		$fragment = str_replace('{id}', $this->getFragmentId(), $this->getFragment());
		return $this->getContainer()->getConfiguration()->getApiUrl() . $fragment;
	}

	protected function getFragmentId(){
		if ($this->getTransaction()->getPaymentIntent()) {
			return $this->getTransaction()->getPaymentIntent();
		}
		return $this->getTransaction()->getChargeId();
	}

	protected function getFragment(){
		if ($this->getTransaction()->getPaymentIntent()) {
			return self::$FRAGMENT_INTENT;
		}
		return self::$FRAGMENT;
	}

	protected function getAmountParameterName(){
		if ($this->getTransaction()->getPaymentIntent()) {
			return 'amount_to_capture';
		}
		return 'amount';
	}

	protected function buildBody(){
		$body = array();
		if (!empty($this->items)) {
			$body[$this->getAmountParameterName()] = Customweb_Util_Currency::formatAmount(
					Customweb_Util_Invoice::getTotalAmountIncludingTax($this->items), $this->getTransaction()->getCurrencyCode(), '');
		}
		return $body;
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_Capture_ResponseProcessor($this->getTransaction(), $this->items);
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}