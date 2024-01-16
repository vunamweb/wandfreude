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

require_once 'Customweb/Stripe/Communication/Response/DefaultProcessor.php';
require_once 'Customweb/Core/Logger/Factory.php';

class Customweb_Stripe_Communication_Response_TransactionProcessor extends Customweb_Stripe_Communication_Response_DefaultProcessor {
	private $transaction;
	private $container;
	
	/**
	 *
	 * @var Customweb_Core_ILogger
	 */
	protected $logger;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_Stripe_Container $container){
		$this->transaction = $transaction;
		$this->container = $container;
		$this->logger = Customweb_Core_Logger_Factory::getLogger(get_class($this));
	}

	protected function getTransaction(){
		return $this->transaction;
	}

	protected function getContainer(){
		return $this->container;
	}
}