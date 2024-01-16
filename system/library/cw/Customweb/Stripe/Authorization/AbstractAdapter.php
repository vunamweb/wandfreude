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
require_once 'Customweb/Payment/Authorization/IPaymentMethod.php';
require_once 'Customweb/Payment/Authorization/IAdapter.php';
require_once 'Customweb/Stripe/AbstractAdapter.php';
require_once 'Customweb/Payment/Authorization/IOrderContext.php';
require_once 'Customweb/Core/Util/Rand.php';


/**
 *
 * @author sebastian
 *
 */
abstract class Customweb_Stripe_Authorization_AbstractAdapter extends Customweb_Stripe_AbstractAdapter implements 
		Customweb_Payment_Authorization_IAdapter {

	public function createTransactionBase($transactionContext, $failedTransaction){
		$transaction = new Customweb_Stripe_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod($this->getAuthorizationMethodName());
		$transaction->setLiveTransaction($this->getContainer()->getConfiguration()->isLiveMode());
		$transaction->setChargeIdempotencyKey(Customweb_Core_Util_Rand::getUuid());
		
		$alias = $transactionContext->getAlias();
		if ($alias instanceof Customweb_Stripe_Authorization_Transaction) {
			$transaction->setPaymentMethodId($alias->getPaymentMethodId());
			$transaction->setCustomerId($alias->getCustomerId());
		}
		
		return $transaction;
	}

	public function isAuthorizationMethodSupported(Customweb_Payment_Authorization_IOrderContext $orderContext){
		try {
			$this->getPaymentMethod($orderContext);
		}
		catch (Customweb_Payment_Authorization_Method_PaymentMethodResolutionException $e) {
			return false;
		}
		return true;
	}

	public function isDeferredCapturingSupported(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		return !in_array('capturing', $this->getPaymentMethod($orderContext)->getNotSupportedFeatures());
	}

	public function preValidate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		$this->getPaymentMethod($orderContext)->preValidate($orderContext, $paymentContext);
	}

	public function validate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext, array $formData){
		$this->getPaymentMethod($orderContext)->preValidate($orderContext, $paymentContext);
	}

	protected function getPaymentMethod($data){
		if ($data instanceof Customweb_Stripe_Authorization_Transaction) {
			$paymentMethod = $data->getPaymentMethod();
		}
		else if ($data instanceof Customweb_Payment_Authorization_IOrderContext) {
			$paymentMethod = $data->getPaymentMethod();
		}
		else if ($data instanceof Customweb_Payment_Authorization_IPaymentMethod) {
			$paymentMethod = $data;
		}
		else {
			throw new Exception("Could not extract payment method from data.");
		}
		return $this->getContainer()->getPaymentMethod($paymentMethod, $this->getAuthorizationMethodName());
	}
}
