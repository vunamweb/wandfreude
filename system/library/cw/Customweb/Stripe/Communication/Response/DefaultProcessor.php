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
require_once 'Customweb/Stripe/Communication/Response/IProcessor.php';

class Customweb_Stripe_Communication_Response_DefaultProcessor implements Customweb_Stripe_Communication_Response_IProcessor {

	public function process(Customweb_Core_Http_IResponse $response){
		if ($response->getStatusCode() == 200) {
			return $this->processInternal($response);
		}
		else {
			$json = json_decode($response->getBody(), true);
			if ($json !== false) {
				if (isset($json['error']['type'])) {
					$userMessage = $this->getGenericError();
					$adminMessage = null;
					switch ($json['error']['type']) {
						case 'card_error':
							if (isset($json['error']['decline_code'])) {
								$error = $this->getDeclineError($json['error']['decline_code']);
								$userMessage = $error['user'];
								$adminMessage = $error['admin'];
							}
							else {
								$userMessage = $this->getGenericError();
								$adminMessage = "";
							}
							if (isset($json['error']['code'])) {
								if (!empty($adminMessage)) {
									$adminMessage .= ' ';
								}
								$adminMessage .= $this->getCodeError($json['error']['code']);
							}
							if (isset($json['error']['message'])) {
								$userMessage = $json['error']['message'];
							}
							break;						
						case 'api_connection_error':
							$adminMessage = Customweb_I18n_Translation::__("Failure to connect to Stripe's API.");
							break;
						case 'api_error':
							$adminMessage = Customweb_I18n_Translation::__("Unkown error, possibly temporary. Please try again later.");
							break;
						case 'authentication_error':
							$adminMessage = Customweb_I18n_Translation::__("The authorization failed, please check the configured keys.");
							break;
						case 'invalid_request_error':
							$adminMessage = Customweb_I18n_Translation::__("Invalid parameters used, please contact sellxed.");
							break;
						case 'rate_limit_error':
							$adminMessage = Customweb_I18n_Translation::__("Too many requests, recommended exponential backoff.");
							break;
						case 'validation_error	':
							$adminMessage = Customweb_I18n_Translation::__("A validation error occurred.");
							break;
						default:
							$adminMessage = Customweb_I18n_Translation::__("Unkown error type '@type'.", 
									array(
										'@type' => $json['type'] 
									));
							break;
					}
					if (isset($json['error']['message'])) {
						$userMessage = $adminMessage = $json['error']['message'];
					}
					$error = new Customweb_Payment_Authorization_ErrorMessage($userMessage, $adminMessage);
					return $this->processError($error);
				}
			}
			return $this->processStatusCode($response);
		}
	}
	
	protected function processError(Customweb_Payment_Authorization_ErrorMessage $message) {
		throw new Customweb_Stripe_Exception_PaymentErrorException($message);
	}

	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		return $json['id'];
	}

	protected function getGenericError(){
		return Customweb_I18n_Translation::__("An unkown error occurred.");
	}

	/**
	 * Fallback method if no specific error is supplied, get basic error from status code.
	 *
	 * @param Customweb_Core_Http_IResponse $response
	 * @throws Exception
	 * @throws Customweb_Stripe_Exception_PaymentErrorException
	 */
	private function processStatusCode(Customweb_Core_Http_IResponse $response){
		$headers = $response->getParsedHeaders();
		$idempotencyKey = Customweb_I18n_Translation::__("Could not be extracted.");
		$requestID = Customweb_I18n_Translation::__("Could not be extracted.");
		if (isset($headers['Idempotency-Key'])) {
			$idempotencyKey = $headers['Idempotency-Key'];
		}
		if (isset($headers['Request-ID'])) {
			$requestID = $headers['Request-ID'];
		}
		
		$statusses = array(
			200 => "Invalid workflow, status code 200 must be accepted.",
			400 => Customweb_I18n_Translation::__("Invalid request, presumably missing parameter."),
			401 => Customweb_I18n_Translation::__("The authorization failed, please check the configured keys."),
			402 => Customweb_I18n_Translation::__("Request failed. but parameters were valid."),
			409 => Customweb_I18n_Translation::__("Request conflict, please check idempotency key '@key'.", 
					array(
						'@key' => $idempotencyKey 
					)),
			429 => Customweb_I18n_Translation::__("Too many requests, recommended exponential backoff."),
			500 => Customweb_I18n_Translation::__("Stripe internal error."),
			502 => Customweb_I18n_Translation::__("Stripe internal error."),
			503 => Customweb_I18n_Translation::__("Stripe internal error."),
			504 => Customweb_I18n_Translation::__("Stripe internal error.") 
		);
		$statusPrefix = Customweb_I18n_Translation::__("Request @requestId | HTTP status @code: ", 
				array(
					'@requestId' => $requestID,
					'@code' => $response->getStatusCode() 
				));
		if (isset($statusses[$response->getStatusCode()])) {
			$specificError = $statusses[$response->getStatusCode()];
		}
		else {
			$specificError = Customweb_I18n_Translation::__("Unkown error occurred.");
		}
		$this->throwPaymentException($statusPrefix . $specificError);
	}

	protected function throwPaymentException($adminMessage, $userMessage = null){
		if ($userMessage === null) {
			$userMessage = Customweb_I18n_Translation::__("The payment method is currently unavailable, please try again later.");
		}
		throw new Customweb_Stripe_Exception_PaymentErrorException(
				new Customweb_Payment_Authorization_ErrorMessage($userMessage, $adminMessage));
	}

	private function getCodeError($code){
		switch ($code) {
			case 'invalid_number':
				return Customweb_I18n_Translation::__("The card number is not a valid credit card number.");
			case 'invalid_expiry_month':
				return Customweb_I18n_Translation::__("The card's expiration month is invalid.");
			case 'invalid_expiry_year':
				return Customweb_I18n_Translation::__("The card's expiration year is invalid.");
			case 'invalid_cvc':
				return Customweb_I18n_Translation::__("The card's security code is invalid.");
			case 'invalid_swipe_data':
				return Customweb_I18n_Translation::__("The card's swipe data is invalid.");
			case 'incorrect_number':
				return Customweb_I18n_Translation::__("The card number is incorrect.");
			case 'expired_card':
				return Customweb_I18n_Translation::__("The card has expired.");
			case 'incorrect_cvc':
				return Customweb_I18n_Translation::__("The card's security code is incorrect.");
			case 'incorrect_zip':
				return Customweb_I18n_Translation::__("The card's zip code failed validation.");
			case 'card_declined':
				return Customweb_I18n_Translation::__("The card was declined.");
			case 'missing':
				return Customweb_I18n_Translation::__("There is no card on a customer that is being charged.");
			case 'processing_error':
				return Customweb_I18n_Translation::__("An error occurred while processing the card.");
		}
	}

	private function getDeclineError($declineCode){
		$codes = array(
			'approve_with_id' => array(
				'admin' => Customweb_I18n_Translation::__("The payment cannot be authorized."),
				'user' => Customweb_I18n_Translation::__(
						"There was an error during the payment attempt. Please attempt the payment process again. If it subsequently fails please contact your bank.") 
			),
			'call_issuer' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'card_not_supported' => array(
				'admin' => Customweb_I18n_Translation::__("The card does not support this type of purchase."),
				'user' => Customweb_I18n_Translation::__("Please contact your bank to verify you card can process this type of purchase..") 
			),
			'card_velocity_exceeded' => array(
				'admin' => Customweb_I18n_Translation::__("The customer has exceeded the balance or credit limit available on their card."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'currency_not_supported' => array(
				'admin' => Customweb_I18n_Translation::__("The card does not support the specified currency."),
				'user' => Customweb_I18n_Translation::__(
						"Your card does not support the used currency, please contact your bank for further information.") 
			),
			'do_not_honor' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'do_not_try_again' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'duplicate_transaction' => array(
				'admin' => Customweb_I18n_Translation::__(
						"A transaction with identical amount and credit card information was submitted very recently."),
				'user' => Customweb_I18n_Translation::__(
						"The transaction was detected as a duplicate, please check if a similar transaction already exists.") 
			),
			'expired_card' => array(
				'admin' => Customweb_I18n_Translation::__("The card has expired."),
				'user' => Customweb_I18n_Translation::__("Please use another card.") 
			),
			'fraudulent	' => array(
				'admin' => Customweb_I18n_Translation::__("The payment has been declined as the issuer suspects it is fraudulent."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'generic_decline' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'incorrect_number' => array(
				'admin' => Customweb_I18n_Translation::__("The card number is incorrect."),
				'user' => Customweb_I18n_Translation::__("Please check the entered card number and try again.") 
			),
			'incorrect_cvc' => array(
				'admin' => Customweb_I18n_Translation::__("The CVC number is incorrect."),
				'user' => Customweb_I18n_Translation::__("Please check your entered CVC and try again.") 
			),
			'incorrect_pin' => array( // should never occur, card reader error
				'admin' => Customweb_I18n_Translation::__("The PIN entered is incorrect."),
				'user' => Customweb_I18n_Translation::__("The customer should try again using the correct PIN.") 
			),
			'incorrect_zip' => array(
				'admin' => Customweb_I18n_Translation::__("The ZIP/postal code is incorrect."),
				'user' => Customweb_I18n_Translation::__("Please check your entered ZIP/postal code and try again.") 
			),
			'insufficient_funds' => array(
				'admin' => Customweb_I18n_Translation::__("The card has insufficient funds to complete the purchase."),
				'user' => Customweb_I18n_Translation::__("The payment method is currently unavailable, please pay using a different method.") 
			),
			'invalid_account' => array(
				'admin' => Customweb_I18n_Translation::__("The card, or account the card is connected to, is invalid."),
				'user' => Customweb_I18n_Translation::__(
						"Please contact your bank to ensure your card is working correctly, and is connected to your account.") 
			),
			'invalid_cvc' => array(
				'admin' => Customweb_I18n_Translation::__("The CVC number is invalid."),
				'user' => Customweb_I18n_Translation::__("Please check your entered CVC and try again.") 
			),
			'invalid_expiry_year' => array(
				'admin' => Customweb_I18n_Translation::__("The expiration year invalid."),
				'user' => Customweb_I18n_Translation::__("Please check your entered card expiry date and try again") 
			),
			'invalid_number' => array(
				'admin' => Customweb_I18n_Translation::__("The card number is invalid."),
				'user' => Customweb_I18n_Translation::__("Please check the entered card number and try again.") 
			),
			'invalid_pin' => array( // should never occur, card reader error
				'admin' => Customweb_I18n_Translation::__("The PIN entered is invalid."),
				'user' => Customweb_I18n_Translation::__("The customer should try again using the correct PIN.") 
			),
			'issuer_not_available' => array(
				'admin' => Customweb_I18n_Translation::__("The card issuer could not be reached, so the payment could not be authorized."),
				'user' => Customweb_I18n_Translation::__("Please try the payment again, and if it fails, contact your bank.") 
			),
			'lost_card	' => array(
				'admin' => Customweb_I18n_Translation::__("The payment has been declined because the card is reported lost."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'new_account_information_available	' => array(
				'admin' => Customweb_I18n_Translation::__("The card, or account the card is connected to, is invalid."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'no_action_taken' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'not_permitted' => array(
				'admin' => Customweb_I18n_Translation::__("The payment is not permitted."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'pickup_card' => array(
				'admin' => Customweb_I18n_Translation::__(
						"The card cannot be used to make this payment (it is possible it has been reported lost or stolen)."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'pin_try_exceeded' => array(
				'admin' => Customweb_I18n_Translation::__("The allowable number of PIN tries has been exceeded."),
				'user' => Customweb_I18n_Translation::__("Please use another card or payment method.") 
			),
			'processing_error' => array(
				'admin' => Customweb_I18n_Translation::__("An error occurred while processing the card."),
				'user' => Customweb_I18n_Translation::__("The payment could not currently be processed, please attempt again later.") 
			),
			'reenter_transaction' => array(
				'admin' => Customweb_I18n_Translation::__("The payment could not be processed by the issuer for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("Please try the payment again, and if it fails, contact your bank.") 
			),
			'restricted_card' => array(
				'admin' => Customweb_I18n_Translation::__(
						"The card cannot be used to make this payment (it is possible it has been reported lost or stolen)."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'revocation_of_all_authorizations' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'security_violation' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'service_not_allowed' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'stolen_card' => array(
				'admin' => Customweb_I18n_Translation::__("The payment has been declined because the card is reported stolen."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'stop_payment_order' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("An error occurred, please contact your bank for further information.") 
			),
			'testmode_decline' => array(
				'admin' => Customweb_I18n_Translation::__("A Stripe test card number was used."),
				'user' => Customweb_I18n_Translation::__("A genuine card must be used to make a payment.") 
			),
			'try_again_later' => array(
				'admin' => Customweb_I18n_Translation::__("The card has been declined for an unknown reason."),
				'user' => Customweb_I18n_Translation::__("Please try the payment again, and if it fails, contact your bank.") 
			),
			'withdrawal_count_limit_exceeded' => array(
				'admin' => Customweb_I18n_Translation::__("The customer has exceeded the balance or credit limit available on their card."),
				'user' => Customweb_I18n_Translation::__("Please use a different payment method.") 
			),
			'test_mode_live_card' => array(
				'admin' => Customweb_I18n_Translation::__("A non-test card was used in test mode."),
				'user' => Customweb_I18n_Translation::__("Please use a card found under https://stripe.com/docs/testing.")
			)
		);
		if (isset($codes[$declineCode])) {
			return $codes[$declineCode];
			return new Customweb_Payment_Authorization_ErrorMessage($codes[$declineCode]['user'], $codes[$declineCode]['admin']);
		}
		else {
			return array(
				'admin' => Customweb_I18n_Translation::__("An unkown code was returned: @code.", array('@code' => $declineCode)),
				'user' => Customweb_I18n_Translation::__("An unkown error occurred.")
			);
		}
	}
}