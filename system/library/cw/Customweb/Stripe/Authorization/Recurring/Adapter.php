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

require_once 'Customweb/Stripe/Authorization/AbstractAdapter.php';
require_once 'Customweb/Stripe/Communication/PaymentIntent/Create/Adapter.php';
require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Payment/Exception/RecurringPaymentErrorException.php';
require_once 'Customweb/Payment/Authorization/Recurring/IAdapter.php';


/**
 * @Bean
 * @author sebastian
 */
class Customweb_Stripe_Authorization_Recurring_Adapter extends Customweb_Stripe_Authorization_AbstractAdapter implements 
		Customweb_Payment_Authorization_Recurring_IAdapter {

	public function isPaymentMethodSupportingRecurring(Customweb_Payment_Authorization_IPaymentMethod $paymentMethod){
		try {
			return $this->getPaymentMethod($paymentMethod)->isRecurringPaymentSupported();
		}
		catch (Customweb_Payment_Authorization_Method_PaymentMethodResolutionException $e) {
		}
		return false;
	}

	public function createTransaction(Customweb_Payment_Authorization_Recurring_ITransactionContext $transactionContext){
		$transaction = $this->createTransactionBase($transactionContext, null);
		$transaction->setCustomerId($transactionContext->getInitialTransaction()->getCustomerId());
		if ($transactionContext->getInitialTransaction()->getThreeDSource()) {
			$transaction->setPaymentMethodId($transactionContext->getInitialTransaction()->getThreeDSource());
		}
		else if ($transactionContext->getInitialTransaction()->getSource()) {
			$transaction->setPaymentMethodId($transactionContext->getInitialTransaction()->getSource());
		}
		if ($transactionContext->getInitialTransaction()->getPaymentMethodId()) {
			$transaction->setPaymentMethodId($transactionContext->getInitialTransaction()->getPaymentMethodId());
		}
		return $transaction;
	}

	public function process(Customweb_Payment_Authorization_ITransaction $transaction){
		try {
			$adapter = new Customweb_Stripe_Communication_PaymentIntent_Create_Adapter($transaction, $this->getContainer(), true);
			$intent = $adapter->process();
		}
		catch (Customweb_Stripe_Exception_PaymentErrorException $e) {
			$transaction->setAuthorizationFailed($e->getErrorMessage());
		}
		catch (Exception $e) {
			$transaction->setAuthorizationFailed(
					new Customweb_Payment_Authorization_ErrorMessage(Customweb_I18n_Translation::__("The transaction failed."),
							Customweb_I18n_Translation::__("@message", array(
								"@message" => $e->getMessage() 
							))));
		}
		if ($transaction->isAuthorizationFailed()) {
			throw new Customweb_Payment_Exception_RecurringPaymentErrorException(end($transaction->getErrorMessages()));
		}
	}

	public function getAdapterPriority(){
		return 200;
	}

	public function getAuthorizationMethodName(){
		return self::AUTHORIZATION_METHOD_NAME;
	}
}