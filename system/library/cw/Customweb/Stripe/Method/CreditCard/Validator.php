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

require_once 'Customweb/Form/Validator/IValidator.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Util.php';


/**
 */
class Customweb_Stripe_Method_CreditCard_Validator implements Customweb_Form_Validator_IValidator {
	private $control;
	private $paymentMethodName;
	private $shipping;
	private $billing;
	private $receiptEmail;
	private $secret;
	
	private $assetResolver;

	public function __construct(Customweb_Form_Control_IControl $control, $paymentMethodName, Customweb_Payment_Authorization_OrderContext_IAddress $billing, Customweb_Asset_IResolver $assetResolver){
		$this->control = $control;
		$this->paymentMethodName = $paymentMethodName;
		$this->assetResolver = $assetResolver;
		$this->billing = $billing;
	}

	/**
	 * The control object on which the validation is executed.
	 *
	 * @return Customweb_Form_Control_IControl
	 */
	public function getControl(){
		return $this->control;
	}
	
	private function getAddressData(Customweb_Payment_Authorization_OrderContext_IAddress $address){
		$data =  array(
			'name' => $address->getFirstName() . ' ' . $address->getLastName(),
			'email' => $address->getEMailAddress(),
			'address' => array(
				'line1' => $address->getStreet(),
				'city' => $address->getCity(),
				'postal_code' => $address->getPostCode(),
				'state' => $address->getState(),
				'country' => $address->getCountryIsoCode()
			)
		);
		if($address->getPhoneNumber()) {
			$data['phone'] = $address->getPhoneNumber();
		}
		return $data;
	}
	
	/**
	 * This method must return the JavaScript code required to
	 * execute the validation.
	 * The code must return a anonymous
	 * JS function which accepts two arguments:
	 *
	 * <ol>
	 * <li>resultCallback: A callback function which accepts two arguments:
	 * The first argument is the result of the validation
	 * The second argument is an error message if the validation failed
	 *
	 * <li>element: The HTML element (control field) on which the validation
	 * should be exectued on.</li>
	 * </ol>
	 */
	public function getCallbackJs(){
		$options = array(
			'billing_details' => Customweb_Stripe_Util::getAddressData($this->billing),
		);
		$options = Customweb_Stripe_Util::simpleJSEncode($options);
		$prefix = 'stripe' . $this->paymentMethodName;
		$brandNotSupported = Customweb_I18n_Translation::__("The credit card is not of a supported brand.");
		
		$showOverlay = Customweb_Stripe_Util::getLoadOverlayScript($this->assetResolver);
		$hideOverlay = Customweb_Stripe_Util::getRemoveOverlayScript();
		
		$js = "function (resultCallback, element) {
	if(!document.{$prefix}SupportedBrand) {
		resultCallback(false, \"$brandNotSupported\");
	}
	else {
		$showOverlay
		var options = $options;
		document.{$prefix}Stripe.createPaymentMethod('card', document.{$prefix}Card, options).then(function(result) {
			$hideOverlay
			if(result.error) {
				resultCallback(false, result.error.message);
			}
			else {
				document.{$prefix}PaymentMethod = result.paymentMethod.id;
				resultCallback(true);
			}
		});
	}
}";
		return $js;
	}
}