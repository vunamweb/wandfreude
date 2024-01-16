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

require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Method/Default.php';


/**
 *
 * @author Sebastian Bossert
 * @Method(paymentMethods={'StripePaymentRequest'})
 */
class Customweb_Stripe_Method_PaymentRequest extends Customweb_Stripe_Method_Default {

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		return array();
	}
	
	public function getCaptureMode(){
		if (!$this->existsPaymentMethodConfigurationValue('capturing')) {
			return null;
		}
		if ($this->getPaymentMethodConfigurationValue('capturing') == 'direct') {
			return 'automatic';
		}
		return 'manual';
	}
	
	/**
	 * Returns javascript initializing PaymentRequestButton.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @return string
	 */
	public function getJavaScriptCallbackFunction(Customweb_Payment_Authorization_ITransaction $transaction){
		$totalText = Customweb_I18n_Translation::__("Order Total");
		$amountInCents = Customweb_Util_Currency::formatAmount($transaction->getAuthorizationAmount(), $transaction->getCurrencyCode(), '');
		
		$countryCode = $transaction->getTransactionContext()->getOrderContext()->getBillingAddress()->getCountryIsoCode();
		$currencyCode = strtolower($transaction->getCurrencyCode());
		
		$notAvailableText = Customweb_I18n_Translation::__("The payment method is not available, please select another option.")->toString();
		
		$createOrderUrl = $this->getContainer()->getSecuredEndpoint($transaction, 'createintent');
		$successUrl = $transaction->getSuccessUrl();
		$failedUrl = $this->getContainer()->getSecuredEndpoint($transaction, 'ajax-failure');
		$successCallback = "function(queryStr=''){(" . $transaction->getTransactionContext()->getJavascriptSuccessCallbackFunction() .
				 ")('$successUrl' + queryStr)}";
		$failCallback = "function(queryStr=''){(" . $transaction->getTransactionContext()->getJavaScriptFailedCallbackFunction() .
				 ")('$failedUrl' + queryStr)}";
		$css = (string) $this->getContainer()->getBean('Customweb_Asset_IResolver')->resolveAssetUrl('stripe.css');
		
		return "function (formFieldValues) {
	stripePaymentRequest.includeScript('{$this->getContainer()->getConfiguration()->getAjaxUrl()}');
	stripePaymentRequest.initialize('$countryCode', '$currencyCode', $amountInCents, '$totalText', '$notAvailableText', '$createOrderUrl', ($successCallback), ($failCallback), '{$this->getContainer()->getConfiguration()->getPublishableKey()}');
	stripePaymentRequest.includeCss('$css');
	stripePaymentRequest.initializeStripe();
	stripePaymentRequest.addOverlay();
	stripePaymentRequest.addButton();
}";
	}

	public function getAjaxFileUrl(Customweb_Payment_Authorization_ITransaction $transaction){
		return (string) $this->getContainer()->getBean('Customweb_Asset_IResolver')->resolveAssetUrl('payment-request.js');
	}
}