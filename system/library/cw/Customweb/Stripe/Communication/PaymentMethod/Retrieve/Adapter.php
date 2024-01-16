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

require_once 'Customweb/Stripe/Communication/PaymentMethod/Retrieve/ResponseProcessor.php';
require_once 'Customweb/Stripe/Communication/Abstract.php';


/**
 * Processes a PaymentMethod GET request.
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_PaymentMethod_Retrieve_Adapter extends Customweb_Stripe_Communication_Abstract {
	protected static $FRAGMENT = 'payment_methods';
	private $transaction;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container){
		parent::__construct($container, $transaction->isLiveTransaction());
		$this->transaction = $transaction;
	}

	protected function getMethod(){
		return 'GET';
	}

	protected function buildUrl(){
		return $this->getContainer()->getConfiguration()->getApiUrl() . self::$FRAGMENT . "/" . $this->getTransaction()->getPaymentMethodId();
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_PaymentMethod_Retrieve_ResponseProcessor($this->getTransaction(), $this->getContainer());
	}

	protected function buildBody(){
		return array();
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}