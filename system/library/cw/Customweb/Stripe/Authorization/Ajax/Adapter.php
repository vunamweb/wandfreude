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
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Payment/Authorization/Ajax/IAdapter.php';


/**
 * @Bean
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Authorization_Ajax_Adapter extends Customweb_Stripe_Authorization_AbstractAdapter implements 
		Customweb_Payment_Authorization_Ajax_IAdapter {

	public function preValidate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		$protocol = strtolower($this->getContainer()->getHttpRequest()->getProtocol());
		if ($protocol != 'https' && $this->getContainer()->getConfiguration()->isLiveMode()) {
			throw new Exception(Customweb_I18n_Translation::__("In Live mode the integration must be included on a page delivered using HTTPS."));
		}
		parent::preValidate($orderContext, $paymentContext);
	}

	public function createTransaction(Customweb_Payment_Authorization_Ajax_ITransactionContext $transactionContext, $failedTransaction){
		return $this->createTransactionBase($transactionContext, $failedTransaction);
	}

	public function getAjaxFileUrl(Customweb_Payment_Authorization_ITransaction $transaction){
		return (string) $this->getPaymentMethod($transaction->getTransactionContext()->getOrderContext())->getAjaxFileUrl($transaction);
	}

	public function getJavaScriptCallbackFunction(Customweb_Payment_Authorization_ITransaction $transaction){
		return $this->getPaymentMethod($transaction->getTransactionContext()->getOrderContext())->getJavaScriptCallbackFunction($transaction);
	}

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		return $this->getPaymentMethod($orderContext)->getVisibleFormFields($orderContext, $aliasTransaction, $failedTransaction,
				$paymentCustomerContext);
	}

	public function getAdapterPriority(){
		return 100;
	}

	public function getAuthorizationMethodName(){
		return self::AUTHORIZATION_METHOD_NAME;
	}
}
