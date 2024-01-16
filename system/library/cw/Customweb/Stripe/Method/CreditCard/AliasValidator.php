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


/**
 */
class Customweb_Stripe_Method_CreditCard_AliasValidator implements Customweb_Form_Validator_IValidator {

	public function __construct(Customweb_Form_Control_IControl $control, $paymentMethodName){
		$this->control = $control;
		$this->paymentMethodName = $paymentMethodName;
	}

	/**
	 * The control object on which the validation is executed.
	 *
	 * @return Customweb_Form_Control_IControl
	 */
	public function getControl(){
		return $this->control;
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
		$sourceUnavailable = Customweb_I18n_Translation::__("The source is not available yet. Please wait a few seconds and try again.");
		$prefix = 'stripe' . $this->paymentMethodName;
		$js = "function (resultCallback, element) {
	if(typeof document.{$prefix}Source === 'undefined') {
		resultCallback(false, '$sourceUnavailable');
	}
	else {
		resultCallback(true);
	}
}";
		return $js;
	}
}