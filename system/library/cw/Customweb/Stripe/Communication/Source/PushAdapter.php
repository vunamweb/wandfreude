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

require_once 'Customweb/Stripe/Communication/Source/PushResponseProcessor.php';
require_once 'Customweb/Stripe/Communication/Source/Adapter.php';



/**
 * Used to add transaction id to source metadata (credit cards).
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_Source_PushAdapter extends Customweb_Stripe_Communication_Source_Adapter {

	protected function getMethod(){
		return 'POST';
	}

	protected function buildBody(){
		$body = array(
			'metadata' => array(
				'cw_transaction_id' => $this->getTransaction()->getExternalTransactionId() 
			) 
		);
		
		$this->addShopId($body);
		
		return $body;
	}
	
	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_Source_PushResponseProcessor($this->getTransaction(), $this->getContainer());
	}
}