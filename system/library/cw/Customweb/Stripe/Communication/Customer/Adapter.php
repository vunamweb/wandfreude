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

require_once 'Customweb/Stripe/Communication/Abstract.php';
require_once 'Customweb/Stripe/Communication/Customer/ResponseProcessor.php';

class Customweb_Stripe_Communication_Customer_Adapter extends Customweb_Stripe_Communication_Abstract {
	private static $FRAGMENT = 'customers';
	private $isNew = true;
	private $source;
	private $transaction;
	private $sourceParameter;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container, $source = null, $sourceParameter = 'payment_method'){
		parent::__construct($container, $transaction->isLiveTransaction());
		
		$customer = $transaction->getCustomerId();
		if($customer) {
			$this->isNew = false;
		}
		$this->source = empty($source) ? $transaction->getSource() : $source;
		$this->transaction = $transaction;
		$this->sourceParameter = $sourceParameter;
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_Customer_ResponseProcessor($this->getTransaction(), $this->getContainer());
	}

	protected function buildUrl(){
		$url = $this->getContainer()->getConfiguration()->getApiUrl() . self::$FRAGMENT;
		if (!$this->isNew) {
			$url .= '/' . $this->getTransaction()->getCustomerId() . '/sources';
		}
		return $url;
	}

	protected function buildBody(){
		$parameters = array(
			$this->getSourceParameter() => $this->getSource() 
		);
		if ($this->isNew) {
			$parameters['description'] = $this->getDescription();
			$email = $this->getEmailAddress();
			if (!empty($email)) {
				$parameters['email'] = $email;
			}
		}
		
		$this->addShopId($parameters);
		
		return $parameters;
	}
	
	protected function getSource(){
		return $this->source;
	}
	
	
	protected function getSourceParameter(){
		return $this->sourceParameter;
	}
	
	protected function getTransaction(){
		return $this->transaction;
	}

	private function getEmailAddress(){
		$email = $this->getTransaction()->getTransactionContext()->getOrderContext()->getCustomerEMailAddress();
		if (empty($email)) {
			$email = $this->getTransaction()->getTransactionContext()->getOrderContext()->getBillingAddress()->getEMailAddress();
		}
		if (empty($email)) {
			$email = $this->getTransaction()->getTransactionContext()->getOrderContext()->getShippingAddress()->getEMailAddress();
		}
		return $email;
	}

	private function getDescription(){
		$address = $this->getTransaction()->getTransactionContext()->getOrderContext()->getBillingAddress();
		return $address->getFirstName() . ' ' . $address->getLastName();
	}
}