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

require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/I18n/ILocalizableString.php';



/**
 * Re-implementation of Customweb_Payment_Exception_PaymentErrorException, with the difference that the standard message is the user message.
 *
 * @author Sebastian Bossert
 */
class Customweb_Stripe_Exception_PaymentErrorException extends Exception {
	/**
	 *
	 * @var Customweb_Payment_Authorization_IErrorMessage
	 */
	private $internalMessage = null;

	public function __construct($message){
		if (is_string($message)) {
			$message = new Customweb_Payment_Authorization_ErrorMessage(
					new Customweb_I18n_Translation("@message", array(
						"@message" => $message 
					)));
		}
		else if ($message instanceof Customweb_I18n_ILocalizableString) {
			$message = new Customweb_Payment_Authorization_ErrorMessage($message);
		}
		else if ($message instanceof Customweb_Payment_Authorization_ErrorMessage) {
		}
		else {
			$message = new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__("Invalid error message provided (@message).", array(
						"@message" => $message 
					)));
		}
		parent::__construct($message->getUserMessage());
		$this->internalMessage = $message;
	}

	/**
	 *
	 * @return Customweb_Payment_Authorization_IErrorMessage
	 */
	final public function getErrorMessage(){
		return $this->internalMessage;
	}
}