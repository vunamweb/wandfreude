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

require_once 'Customweb/Stripe/Communication/Source/ResponseProcessor.php';
require_once 'Customweb/Stripe/Communication/Abstract.php';



/**
 * Processes a GET request for the source on the given transaction, and sets the current data on the transaction.
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_Source_Adapter extends Customweb_Stripe_Communication_Abstract {
	protected static $FRAGMENT = 'sources';
	private $transaction;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container){
		parent::__construct($container, $transaction->isLiveTransaction());
		if ($transaction->getSource() == null) {
			throw new InvalidArgumentException("Source must be set on transaction.");
		}
		$this->transaction = $transaction;
	}

	protected function getMethod(){
		return 'GET';
	}

	protected function buildUrl(){
		return $this->getContainer()->getConfiguration()->getApiUrl() . self::$FRAGMENT . '/' . $this->getTransaction()->getSource();
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_Source_ResponseProcessor($this->getTransaction(), $this->getContainer());
	}

	protected function buildBody(){
		return '';
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}