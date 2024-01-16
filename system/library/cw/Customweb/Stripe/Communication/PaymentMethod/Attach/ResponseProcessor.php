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

require_once 'Customweb/Stripe/Communication/Response/TransactionProcessor.php';


/**
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_PaymentMethod_Attach_ResponseProcessor extends Customweb_Stripe_Communication_Response_TransactionProcessor {

	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		
		if ($this->getTransaction()->getTransactionContext()->getAlias() == 'new' ||
				 $this->getTransaction()->getTransactionContext()->createRecurringAlias()) {
			$this->getTransaction()->setAlias($json['id']);
			$this->getTransaction()->setAliasForDisplay(
					$this->getContainer()->getPaymentMethodByTransaction($this->getTransaction())->extractAliasForDisplay($json));
		}
		
		return $json['id'];
	}
}