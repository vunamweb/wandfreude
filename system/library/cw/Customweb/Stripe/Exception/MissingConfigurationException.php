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
require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/I18n/Translation.php';



/**
 *
 * @author Sebastian Bossert
 */
class Customweb_Stripe_Exception_MissingConfigurationException extends Customweb_Stripe_Exception_PaymentErrorException {

	public function __construct($label){
		$userMessage = Customweb_I18n_Translation::__(
				"There seems to be a configuration issue. Please try another payment method, and contact the merchant.");
		$adminMessage = Customweb_I18n_Translation::__("The value for configuration '@label' must be set.", 
				array(
					'@label' => (string) $label 
				));
		parent::__construct(new Customweb_Payment_Authorization_ErrorMessage($userMessage, $adminMessage));
	}
}