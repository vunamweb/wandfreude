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

class Customweb_Stripe_Communication_Refund_ResponseProcessor extends Customweb_Stripe_Communication_Response_DefaultProcessor {
	private $transaction;
	private $items;
	private $close;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, array $items, $close){
		$this->transaction = $transaction;
		$this->items = $items;
		$this->close = $close;
	}

	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		if ($json['status'] == 'failed') {
			throw new Customweb_Stripe_Exception_PaymentErrorException(
					Customweb_I18n_Translation::__("The refund failed, although no error occurred."));
		}
		
		if($this->getItems()){
			$refundItem = $this->getTransaction()->refundByLineItems($this->getItems(), $this->isClose());
			$refundItem->setRefundId($json['id']);
		}
		
		return true;
	}

	protected function isClose(){
		return $this->close;
	}

	protected function getItems(){
		return $this->items;
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}