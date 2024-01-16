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

require_once 'Customweb/Stripe/Method/CreditCard/Validator.php';
require_once 'Customweb/Stripe/Authorization/Transaction.php';
require_once 'Customweb/Stripe/Communication/PaymentIntent/Create/Adapter.php';
require_once 'Customweb/Form/Element.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Method/Default.php';
require_once 'Customweb/Form/Control/Html.php';
require_once 'Customweb/Stripe/Endpoint/Process.php';
require_once 'Customweb/Stripe/Util.php';


/**
 *
 * @author Sebastian Bossert
 * @Method(paymentMethods={'creditcard', 'visa', 'mastercard', 'americanexpress', 'discovercard', 'diners', 'jcb'})
 */
class Customweb_Stripe_Method_CreditCard_Method extends Customweb_Stripe_Method_Default {

	public function getCaptureMode(){
		if (!$this->existsPaymentMethodConfigurationValue('capturing')) {
			return null;
		}
		if ($this->getPaymentMethodConfigurationValue('capturing') == 'direct') {
			return 'automatic';
		}
		return 'manual';
	}

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		$fields = array();
		$fields[] = $this->getJavascriptField();
		
		if ($aliasTransaction instanceof Customweb_Stripe_Authorization_Transaction) {
			$control = new Customweb_Form_Control_Html('stripe-alias-display', $aliasTransaction->getAliasForDisplay());
			$element = new Customweb_Form_Element(Customweb_I18n_Translation::__('Selected card:'), $control);
			$fields[] = $element;
		}
		else {
			$fields[] = $this->getCardInputTemplateField($orderContext);
		}
		return $fields;
	}

	public function extractAliasForDisplay(array $json){
		return 'XXXX XXXX XXXX ' . $json['card']['last4'];
	}

	private function getCardInputTemplateField(Customweb_Payment_Authorization_IOrderContext $orderContext){
		$control = new Customweb_Form_Control_Html('stripe-card',
				"<div class='StripeWrapper'><div id='stripe{$this->getPaymentMethodName()}-card-element' class='StripeElement'></div></div>");
		$control->setRequired(false);
		$control->addValidator(
				new Customweb_Stripe_Method_CreditCard_Validator($control, $this->getPaymentMethodName(),
						$orderContext->getBillingAddress(), $this->getContainer()->getAssetResolver()));
		$field = new Customweb_Form_Element(Customweb_I18n_Translation::__("Card"), $control);
		$field->setRequired(false);
		
		$field->appendJavaScript($this->getMountElementsJavascript($orderContext));
		return $field;
	}

	private function getMountElementsJavascript(Customweb_Payment_Authorization_IOrderContext $orderContext){
		$prefix = 'stripe' . $this->getPaymentMethodName();
		$locale = $orderContext->getLanguage()->getIetfCode();
		$supported_brands = json_encode($this->getSupportedBrands());
		
		return "function {$prefix}InitializeCCField() {
	if(typeof document.{$prefix}Stripe === 'undefined') {
		setTimeout({$prefix}InitializeCCField, 500);
	}
	else {
		document.{$prefix}Card = document.{$prefix}Stripe.elements({locale: \"{$locale}\"}).create('card', {hidePostalCode: true});
		document.{$prefix}Card.mount('#{$prefix}-card-element');
		document.{$prefix}Card.on('change', function(event) {
			document.{$prefix}SupportedBrand = ($supported_brands.indexOf(event.brand) !== -1);
		});
	}
}
{$prefix}InitializeCCField();";
	}

	public function getJavaScriptCallbackFunction(Customweb_Payment_Authorization_ITransaction $transaction){
		if ($transaction->getTransactionContext()->getAlias() instanceof Customweb_Stripe_Authorization_Transaction) {
			return $this->getAliasCallback($transaction);
		}
		else {
			return $this->getDefaultCallback($transaction);
		}
	}

	private function getDefaultCallback(Customweb_Stripe_Authorization_Transaction $transaction){
		$context = $transaction->getTransactionContext();
		/* @var $context Customweb_Payment_Authorization_Ajax_ITransactionContext */
		$prefix = 'stripe' . $this->getPaymentMethodName();
		$success = $context->getJavaScriptSuccessCallbackFunction();
		$fail = $context->getJavaScriptFailedCallbackFunction();
		$successUrl = $transaction->getSuccessUrl();
		$createUrl = $this->getContainer()->getSecuredEndpoint($transaction, 'createintent');
		$failUrl = $this->getContainer()->getSecuredEndpoint($transaction, 'ajax-failure');
		$errorParam = Customweb_Stripe_Endpoint_Process::ERROR_MESSAGE_PARAMETER;
		
		$data = array(
			'payment_method_data' => array(
				'billing_details' => Customweb_Stripe_Util::getAddressData(
						$transaction->getTransactionContext()->getOrderContext()->getBillingAddress()) 
			),
			'shipping' => Customweb_Stripe_Util::getAddressData(
					$transaction->getTransactionContext()->getOrderContext()->getShippingAddress()) 
		);
		unset($data['shipping']['email']);
		$data = json_encode($data);
		
		$showOverlayScript = Customweb_Stripe_Util::getLoadOverlayScript($this->getContainer()->getAssetResolver());
		
		return "function (formFieldValues) {
	if(document.{$prefix}PaymentMethod) {
		$showOverlayScript
	    var request = new XMLHttpRequest();
	
	    request.open('POST', '$createUrl&paymentMethod=' + document.{$prefix}PaymentMethod);
	    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
	    request.onload = function () {
	        if (this.status >= 200 && this.status < 400) {
				var data = JSON.parse(request.responseText);
				document.{$prefix}Stripe.handleCardPayment(data.client_secret, document.{$prefix}Card, $data).then(function(data) {
					if(data.error) {
						alert(data.error.message);
						($fail)('$failUrl&$errorParam=' + encodeURI(data.error.message));
					} else {
						($success)('$successUrl');
					}
				});
	        } else {
				($fail)('$failUrl');
	        }
	    };
	    request.onerror = function() {
			($fail)('$failUrl');
	    };
	    request.send();
	}
}";
	}

	private function getAliasCallback(Customweb_Stripe_Authorization_Transaction $transaction){
		$context = $transaction->getTransactionContext();
		/* @var $context Customweb_Payment_Authorization_Ajax_ITransactionContext */
		$prefix = 'stripe' . $this->getPaymentMethodName();
		$success = $context->getJavaScriptSuccessCallbackFunction();
		$fail = $context->getJavaScriptFailedCallbackFunction();
		$successUrl = $transaction->getSuccessUrl();
		$failUrl = $this->getContainer()->getSecuredEndpoint($transaction, 'ajax-failure');
		$errorParam = Customweb_Stripe_Endpoint_Process::ERROR_MESSAGE_PARAMETER;
		
		try {
			$adapter = new Customweb_Stripe_Communication_PaymentIntent_Create_Adapter($transaction, $this->getContainer(), false);
			$intent = $adapter->process();
		}
		catch (Exception $e) {
			$transaction->setAuthorizationFailed($e->getMessage());
			return "function(formFieldValues){($fail)('$failUrl&errorParam=" . urlencode($e->getMessage()) . "');}";
		}
		$showOverlayScript = Customweb_Stripe_Util::getLoadOverlayScript($this->getContainer()->getAssetResolver());
		
		if (isset($intent['redirect'])) {
			return "function (formFieldValues) { window.location = '{$intent['redirect']}';}";
		}
		else if ($intent['status'] == 'requires_action' || $intent['status'] == 'requires_confirmation') {
			return "function (formFieldValues) {
	$showOverlayScript
	document.{$prefix}Stripe.handleCardPayment('{$intent['client_secret']}').then(function(data) {
		if(data.error) {
			($fail)('$failUrl&$errorParam=' + encodeURI(data.error.message));
		} else {
			($success)('$successUrl');
		}
	});
}";
		}
		else if ($intent['status'] == 'succeeded') {
			return "function (formFieldValues) { ($success)('$successUrl'); }";
		}
		else {
			return "function (formFieldValues) { ($fail)('$failUrl'); }";
		}
	}

	protected function getSupportedBrands(){
		if ($this->existsPaymentMethodConfigurationValue('credit_card_brands')) {
			return array_values($this->getPaymentMethodConfigurationValue('credit_card_brands'));
		}
		else {
			$parameters = $this->getPaymentMethodParameters();
			if (!isset($parameters['brand'])) {
				throw new Exception("Brand could not be determined.");
			}
			return array(
				$parameters['brand'] 
			);
		}
	}
}