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

require_once 'Customweb/Stripe/Communication/Refund/ResponseProcessor.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/Stripe/Communication/Abstract.php';
require_once 'Customweb/Util/Invoice.php';

class Customweb_Stripe_Communication_Refund_Adapter extends Customweb_Stripe_Communication_Abstract {
	private static $FRAGMENT = 'refunds';
	private $transaction;
	private $items;
	private $close;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, array $items, $close, Customweb_DependencyInjection_IContainer $container){
		parent::__construct($container, $transaction->isLiveTransaction());
		$this->transaction = $transaction;
		$this->items = $items;
		$this->close = $close;
	}

	protected function buildUrl(){
		return $this->getContainer()->getConfiguration()->getApiUrl() . self::$FRAGMENT;
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_Refund_ResponseProcessor($this->getTransaction(), $this->items, $this->isClose());
	}

	protected function buildBody(){
		$parameters = array(
			'charge' => $this->getTransaction()->getChargeId() 
		);
		if (!empty($this->items)) {
			$amount = Customweb_Util_Invoice::getTotalAmountIncludingTax($this->items);
			$amount = Customweb_Util_Currency::formatAmount($amount, $this->getTransaction()->getCurrencyCode(), '');
			$parameters['amount'] = $amount;
		}
		
		$this->addShopId($parameters);
		
		return $parameters;
	}


	protected function getTransaction(){
		return $this->transaction;
	}

	protected function isClose(){
		return $this->close;
	}
}