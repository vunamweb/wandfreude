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

require_once 'Customweb/Payment/Authorization/ITransactionHistoryItem.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Communication/PaymentMethod/Retrieve/Adapter.php';
require_once 'Customweb/Payment/Authorization/DefaultTransactionHistoryItem.php';
require_once 'Customweb/Stripe/Communication/Response/TransactionProcessor.php';


/**
 * Processes a payment intent create response.
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_PaymentIntent_Create_ResponseProcessor extends Customweb_Stripe_Communication_Response_TransactionProcessor {

	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		$this->getTransaction()->setPaymentIntent($json['id']);
		
		if (isset($json['payment_method'])) {
			$this->getTransaction()->setPaymentMethodId($json['payment_method']);
		}
		
		if ($this->getTransaction()->getPaymentMethodId()) {
			$processor = new Customweb_Stripe_Communication_PaymentMethod_Retrieve_Adapter($this->getTransaction(), $this->getContainer());
			$processor->process();
		}
		
		$status = $this->getStatus($json);
		$additional = $this->processStatus($status, $json);
		
		$this->trySetChargeId($json);
		
		return array_merge(array(
			'id' => $json['id'],
			'status' => $this->getStatus($json),
			'client_secret' => $json['client_secret'] 
		), $additional);
	}

	private function trySetChargeId(array $intent){
		if (isset($intent['charges']) && isset($intent['charges']['total_count'])) {
			$count = intval($intent['charges']['total_count']);
			if ($count && isset($intent['charges']['data'][$count - 1]['id'])) {
				$this->getTransaction()->setChargeId($intent['charges']['data'][$count - 1]['id']);
			}
		}
	}

	private function processStatus($status, array $intent){
		switch ($status) {
			case "requires_payment_method":
				$this->logger->logError("requires_payment_method not supported", $intent);
				// should not happen, created in stripe.createPaymentMethod in client js
				throw new Exception("requires_payment_method flow not supported.");
				break;
			case "requires_confirmation":
// 				$this->logger->logError("requires_confirmation not supported", $intent);
// 				call stripe.handleCardPayment() in client js
				break;
			case "requires_action":
				// call stripe.handleCardAction if redirect is not set, otherwise redirect
				if (isset($intent['next_action']) && isset($intent['next_action']['type']) && isset($intent['next_action']['redirect_to_url']) &&
						 isset($intent['next_action']['redirect_to_url']['url']) && $intent['next_action']['type'] == 'redirect') {
					return array(
						'redirect' => $intent['next_action']['redirect_to_url']['url'] 
					);
				}
				break;
			case "processing":
				$this->processing($intent);
				break;
			case "canceled":
				$this->canceled($intent);
				break;
			case "succeeded":
				$this->succeeded($intent);
				break;
			case "requires_capture":
				$this->requiresCapture($intent);
				break;
		}
		return array();
	}

	private function processing(array $intent){
		$this->getTransaction()->addHistoryItem(
				new Customweb_Payment_Authorization_DefaultTransactionHistoryItem(
						Customweb_I18n_Translation::__("The PaymentIntent is now processing.", array(
							"@status" => $status 
						)), Customweb_Payment_Authorization_ITransactionHistoryItem::ACTION_LOG));
		//TODO is currently not implemented, will be used for async payment methods
		// 				if(!$this->getTransaction()->isAuthorizationFailed() && !$this->getTransaction()->isAuthorized()){
		// 					$this->getTransaction()->authorize();
		// 				}
		// 				if($this->getTransaction()->isAuthorized()) {
		// 					$this->getTransaction()->setAuthorizationUncertain(true);
		// 				}
	}

	private function canceled(array $intent){
		$message = Customweb_I18n_Translation::__("PaymentIntent is canceled.");
		if ($this->getTransaction()->isCancelPossible()) {
			$this->getTransaction()->cancel($message);
		}
		else if ($this->getTransaction()->isAuthorized()) {
			$this->getTransaction()->setUncertainTransactionFinallyDeclined();
		}
		else if (!$this->getTransaction()->isAuthorizationFailed()) {
			$this->getTransaction()->setAuthorizationFailed($message);
		}
	}

	private function succeeded(array $intent){
		if ($this->getTransaction()->isAuthorizationFailed()) {
			$this->addInvalidStatusHistory("succeeded");
			return;
		}
		if (!$this->getTransaction()->isAuthorized()) {
			$this->getTransaction()->authorize();
		}
		if ($this->getTransaction()->isCapturePossible()) {
			$this->getTransaction()->capture();
		}
		if (!$this->getTransaction()->isCaptured()) {
			$this->addInvalidStatusHistory("succeeded");
		}
	}

	private function requiresCapture(array $intent){
		if ($this->getTransaction()->isAuthorizationFailed()) {
			$this->addInvalidStatusHistory("requires_capture");
			return;
		}
		if (!$this->getTransaction()->isAuthorized()) {
			$this->getTransaction()->authorize();
		}
		if ($this->getContainer()->getPaymentMethodByTransaction($this->getTransaction())->getCaptureMode() == 'automatic') {
			if ($this->getTransaction()->isCapturePossible()) {
				$this->getTransaction()->addHistoryItem(
						new Customweb_Payment_Authorization_DefaultTransactionHistoryItem(
								Customweb_I18n_Translation::__("The PaymentIntent is in state requires_capture, please capture the transaction."),
								Customweb_Payment_Authorization_ITransactionHistoryItem::ACTION_AUTHORIZATION));
			}
			else {
				$this->addInvalidStatusHistory("requires_capture");
			}
		}
	}

	/**
	 * Convert old SDK states to new equivalents
	 * 
	 * @param array $intent
	 * @return string
	 */
	private function getStatus(array $intent){
		switch ($intent['status']) {
			case 'requires_payment_method':
			case 'requires_source':
				return 'requires_payment_method';
			case 'requires_action':
			case 'requires_source_action':
				return 'requires_action';
			default:
				return $intent['status'];
		}
	}

	private function addInvalidStatusHistory($status){
		$this->getTransaction()->addHistoryItem(
				new Customweb_Payment_Authorization_DefaultTransactionHistoryItem(
						Customweb_I18n_Translation::__(
								"The PaymentIntent is in state @status which could not be automatically applied. Please manually review the transaction.",
								array(
									"@status" => $status 
								)), Customweb_Payment_Authorization_ITransactionHistoryItem::ACTION_LOG));
	}
}