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

require_once 'Customweb/Stripe/Exception/PaymentErrorException.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Communication/Response/DefaultProcessor.php';

class Customweb_Stripe_Communication_Capture_ResponseProcessor extends Customweb_Stripe_Communication_Response_DefaultProcessor {
	private $transaction;
	private $items;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, array $items){
		$this->transaction = $transaction;
		$this->items = $items;
	}

	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		if ((isset($json['captured']) && !$json['captured']) && (isset($json['status']) && $json['status'] != 'succeeded')) {
			throw new Customweb_Stripe_Exception_PaymentErrorException(
					Customweb_I18n_Translation::__("The charge is not captured, although no error occurred."));
		}
		$this->getTransaction()->partialCaptureByLineItems($this->getItems(), true);
		return true;
	}

	protected function getItems(){
		return $this->items;
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}