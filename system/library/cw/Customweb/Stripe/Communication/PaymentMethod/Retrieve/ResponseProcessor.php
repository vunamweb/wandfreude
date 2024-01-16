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

require_once 'Customweb/Stripe/Communication/PaymentMethod/Attach/Adapter.php';
require_once 'Customweb/Stripe/Communication/Customer/Adapter.php';
require_once 'Customweb/Stripe/Communication/Response/TransactionProcessor.php';


/**
 * Responsible for setting alias data, and creating a stripe customer to attach it to if required.
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_PaymentMethod_Retrieve_ResponseProcessor extends Customweb_Stripe_Communication_Response_TransactionProcessor {

	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		
		if ($this->getTransaction()->getTransactionContext()->getAlias() == 'new' ||
				$this->getTransaction()->getTransactionContext()->createRecurringAlias()) {
			if (!$this->getTransaction()->getCustomerId()) { // if no customer yet, create and attach
				$customerAdapter = new Customweb_Stripe_Communication_Customer_Adapter($this->getTransaction(), $this->getContainer(), $json['id']);
				$customerAdapter->process();
			}
			else if (!isset($json['customer'])) { // otherwise attach
				$attachAdapter = new Customweb_Stripe_Communication_PaymentMethod_Attach_Adapter($this->getTransaction(),
						$this->getContainer());
				$attachAdapter->process();
			}
		}
		
		return $json['id'];
	}
}