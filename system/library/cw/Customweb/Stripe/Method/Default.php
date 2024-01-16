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

require_once 'Customweb/Payment/Endpoint/Controller/DelayedNotification.php';
require_once 'Customweb/Util/Currency.php';
require_once 'Customweb/Stripe/Method/Abstract.php';
require_once 'Customweb/Form/HiddenElement.php';
require_once 'Customweb/Stripe/Endpoint/Process.php';
require_once 'Customweb/Form/Control/HiddenHtml.php';
require_once 'Customweb/Stripe/Communication/Source/Adapter.php';
require_once 'Customweb/Util/JavaScript.php';



/**
 *
 * @author Sebastian Bossert
 * @Method()
 */
class Customweb_Stripe_Method_Default extends Customweb_Stripe_Method_Abstract {

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		$fields = array();
		$fields[] = $this->getJavascriptField();
		return $fields;
	}

	protected function getJavascriptField(){
		$script = $this->getInclusionScript($this->getContainer()->getConfiguration()->getAjaxUrl()) . $this->getInitializationScript();
		$control = new Customweb_Form_Control_HiddenHtml('stripe-initialization-script', '');
		$control->setRequired(false);
		$field = new Customweb_Form_HiddenElement($control);
		$field->setJavaScript($script);
		return $field;
	}
	
	protected function getInclusionScript($url){
		$cssUrl = $this->getContainer()->getBean('Customweb_Asset_IResolver')->resolveAssetUrl('stripe.css');
		// include only once for all payment methods.
		return "
if(typeof document.stripeJsInclude === 'undefined') {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = '$url';

	var style = document.createElement('link');
	style.href = '$cssUrl';
	style.rel = 'stylesheet';
	
	document.head.appendChild(style);
	document.head.appendChild(script);
	document.stripeJsInclude = true;
}
";
	}
	
	protected function getAliasSourceJavascript(Customweb_Stripe_Authorization_Transaction $transaction){
		if(!$transaction->getAliasSecret()) {
			$adapter = new Customweb_Stripe_Communication_Source_Adapter($transaction, $this->getContainer());
			$adapter->process();
		}
		
		$prefix = 'stripe' . $this->getPaymentMethodName();
		$javascript = "function {$prefix}AliasSourceLoad() {
	if(typeof document.{$prefix}Stripe == 'undefined') {
		setTimeout({$prefix}AliasSourceLoad, 500);
	}
	else {
		document.{$prefix}Stripe.retrieveSource({id: '{$transaction->getAlias()}', client_secret: '{$transaction->getAliasSecret()}'}).then(function(result) {
			if(result.error) {
				console.dir(result.error);
				alert(result.error.message);
			}
			else {
				document.{$prefix}Source = result.source;
			}
		});
	}
}
{$prefix}AliasSourceLoad();
";
		return $javascript;
	}

	protected function getInitializationScript(){
		$publicKey = $this->getContainer()->getConfiguration()->getPublishableKey();
		$prefix = 'stripe' . $this->getPaymentMethodName();
		return "
function {$prefix}Initialize() {
	if(typeof Stripe !== 'undefined') {
		document.{$prefix}Stripe = Stripe('$publicKey');
	}
	if(typeof Stripe === 'undefined' || typeof document.{$prefix}Stripe === 'undefined') {
		setTimeout({$prefix}Initialize, 500);
	}
}
{$prefix}Initialize();
";
	}
	
	/**
	 * Returns script used to create source object.
	 * Cannot be moved to validator as transaction is required for ID and redirection URL.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @return string
	 */
	public function getJavaScriptCallbackFunction(Customweb_Payment_Authorization_ITransaction $transaction){
		$promise = $this->getJavascriptPromiseFunction($transaction);
		$data = $this->getSourceCreationJson($transaction);
		$precondition = $this->getJavascriptPrecondition($transaction);
		$prefix = 'stripe' . $this->getPaymentMethodName();
		$stripeElement = "document.{$prefix}Stripe";
		
		if($transaction->getTransactionContext()->getOrderContext()->isAjaxReloadRequired()) {
			$key = $this->getContainer()->getConfiguration()->getPublishableKey();
			$stripeElement = "(new Stripe('{$key}'))";
		}
		
		return "function (formFieldValues) {
		$precondition
		{$stripeElement}.createSource(
			$data
		).then(
			$promise
		);
	}";
	}
	

	public function getSourceCreationOwner(Customweb_Stripe_Authorization_Transaction $transaction){
		$address = $transaction->getTransactionContext()->getOrderContext()->getBillingAddress();
		
		$owner = array(
			'name' => $address->getFirstName() . ' ' . $address->getLastName(),
			'address' => array(
				'city' => $address->getCity(),
				'country' => $address->getCountryIsoCode(),
				'line1' => $address->getStreet(),
				'postal_code' => $address->getPostCode() 
			) 
		);
		if ($address->getState() != null) {
			$owner['address']['state'] = $address->getState();
		}
		if ($address->getMobilePhoneNumber() != null) {
			$owner['phone'] = $address->getMobilePhoneNumber();
		}
		if ($address->getPhoneNumber() != null) {
			$owner['phone'] = $address->getPhoneNumber();
		}
		if ($address->getEMailAddress() != null) {
			$owner['email'] = $address->getEMailAddress();
		}
		else if ($transaction->getTransactionContext()->getOrderContext()->getCustomerEMailAddress() != null) {
			$owner['email'] = $transaction->getTransactionContext()->getOrderContext()->getCustomerEMailAddress();
		}
		
		return $owner;
	}

	/**
	 * Javascript string which is evaluated prior to source creation.
	 *
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @return string
	 */
	protected function getJavascriptPrecondition(Customweb_Stripe_Authorization_Transaction $transaction){
		return '';
	}

	/**
	 * Returns the JSON used to create the source.
	 *
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @return string
	 */
	protected function getSourceCreationJson(Customweb_Stripe_Authorization_Transaction $transaction){
		return json_encode($this->getSourceCreationData($transaction));
	}

	/**
	 * Returns the data to used to create the source, as array.
	 *
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @return array
	 */
	protected function getSourceCreationData(Customweb_Stripe_Authorization_Transaction $transaction){
		$parameters = $this->getPaymentMethodParameters();
		$data = array(
			'type' => $parameters['paymentMethodType'],
			'amount' => Customweb_Util_Currency::formatAmount($transaction->getAuthorizationAmount(), $transaction->getCurrencyCode(), ''),
			'currency' => $transaction->getCurrencyCode(),
			'owner' => $this->getSourceCreationOwner($transaction),
			'metadata' => array(
				'cw_transaction_id' => $transaction->getExternalTransactionId() 
			),
			'redirect' => array(
				'return_url' => $this->getContainer()->getSecuredEndpoint($transaction, 'index', 'waiting', '', 
						Customweb_Payment_Endpoint_Controller_DelayedNotification::HASH_PARAMETER) 
			) 
		);
		
		$shopId = $this->getContainer()->getConfiguration()->getShopId();
		if (!empty($shopId)) {
			$data['metadata']['cw_shop_id'] = $shopId;
		}
		
		return $data;
	}
	
	protected function getPollingJs(Customweb_Stripe_Authorization_Transaction $transaction, $successFunction){
		//@formatter:off
		$pollingFunction =  '
			(function cwStatusChecker() {
				if(typeof window.jQuery == "undefined") {
					window.jQuery = cwJQuery;
				}
			    setTimeout(function() {
			        window.jQuery.ajax({
			            url: "' . $this->getContainer()->getSecuredEndpoint($transaction, 'check', 'waiting', '', Customweb_Payment_Endpoint_Controller_DelayedNotification::HASH_PARAMETER). '",
			            type: "POST",
			            success: function(data) {
			                if(data.status == "complete") {
			            		'.$successFunction.'();
			            		return;
			            	}
			            	cwStatusChecker();
			            },
			            error: function(request, message, code) {
			            	cwStatusChecker();
			            },
			            dataType: "json",
			           	cache: false,
			            timeout: 30000
			        })
			    },  2000);
			})';
		//@formatter:on
		return Customweb_Util_JavaScript::getLoadJQueryCode(null, 'cwJQuery', $pollingFunction);
	}
	
	/**
	 * Returns the promise called after the creation of the source.
	 * Must return a javascript implementation for function(result).
	 *
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @return string
	 */
	protected function getJavascriptPromiseFunction(Customweb_Stripe_Authorization_Transaction $transaction){
		$failedUrl = $this->getContainer()->getSecuredEndpoint($transaction, 'ajax-failure');
		$context = $transaction->getTransactionContext();
		/* @var $context Customweb_Payment_Authorization_Ajax_ITransactionContext */
		$failedFunction = $context->getJavaScriptFailedCallbackFunction();
		$successFunction = $context->getJavaScriptSuccessCallbackFunction();
		$successUrl = $transaction->getSuccessUrl();
		
		$errorMessageParameter = Customweb_Stripe_Endpoint_Process::ERROR_MESSAGE_PARAMETER;
		return "function(result) {
		if(result.error) {
			var suffix = '';
			if(result.error.message) {
				suffix = '&$errorMessageParameter=' + encodeURIComponent(result.error.message);
			}
			($failedFunction)('$failedUrl' + suffix);
		}
		else {
			if(result.source.flow == 'redirect') {
				($successFunction)(result.source.redirect.url);
			}
			else {
				{$this->getPollingJs($transaction, "((" . $successFunction . ")('$successUrl'))")};
			}
		}
	}";
	}

	public function getAjaxFileUrl(Customweb_Payment_Authorization_ITransaction $transaction){
		return $this->getContainer()->getBean('Customweb_Asset_IResolver')->resolveAssetUrl('dummy.js');
	}
}