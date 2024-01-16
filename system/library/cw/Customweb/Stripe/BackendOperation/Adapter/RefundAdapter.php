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

require_once 'Customweb/Stripe/Authorization/Transaction.php';
require_once 'Customweb/Core/Exception/CastException.php';
require_once 'Customweb/Stripe/Exception/PaymentErrorException.php';
require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/Stripe/Communication/Refund/Adapter.php';
require_once 'Customweb/Payment/BackendOperation/Adapter/Service/IRefund.php';
require_once 'Customweb/Stripe/AbstractAdapter.php';


/**
 *
 * @author sebastian
 * @Bean
 *
 */
class Customweb_Stripe_BackendOperation_Adapter_RefundAdapter extends Customweb_Stripe_AbstractAdapter implements 
		Customweb_Payment_BackendOperation_Adapter_Service_IRefund {

	public function refund(Customweb_Payment_Authorization_ITransaction $transaction){
		$items = $transaction->getTransactionContext()->getOrderContext()->getInvoiceItems();
		return $this->partialRefund($transaction, $items, true);
	}

	public function partialRefund(Customweb_Payment_Authorization_ITransaction $transaction, $items, $close){
		if (!($transaction instanceof Customweb_Stripe_Authorization_Transaction)) {
			throw new Customweb_Core_Exception_CastException("Customweb_Stripe_Authorization_Transaction");
		}
		
		$transaction->refundByLineItemsDry($items, $close);
		
		try {
			$refund = new Customweb_Stripe_Communication_Refund_Adapter($transaction, $items, $close, $this->getContainer());
			$refund->process();
		}
		catch (Customweb_Stripe_Exception_PaymentErrorException $e) {
			$message = new Customweb_Payment_Authorization_ErrorMessage($e->getErrorMessage()->getBackendMessage());
			throw new Customweb_Stripe_Exception_PaymentErrorException($message);
		}
	}
}