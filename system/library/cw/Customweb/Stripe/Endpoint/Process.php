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

require_once 'Customweb/Stripe/Container.php';
require_once 'Customweb/Stripe/Communication/Charge/Adapter.php';
require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/Payment/Endpoint/Controller/Process.php';
require_once 'Customweb/Payment/Authorization/DefaultTransactionHistoryItem.php';
require_once 'Customweb/Core/Http/Response.php';
require_once 'Customweb/Stripe/Communication/Source/ResponseProcessor.php';
require_once 'Customweb/Stripe/Authorization/Transaction.php';
require_once 'Customweb/Stripe/Communication/PaymentIntent/Create/Adapter.php';
require_once 'Customweb/Payment/Endpoint/Controller/Abstract.php';
require_once 'Customweb/Core/Http/ContextRequest.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Payment/Endpoint/Annotation/ExtractionMethod.php';
require_once 'Customweb/Stripe/Communication/Charge/ResponseProcessor.php';
require_once 'Customweb/Core/Logger/Factory.php';


/**
 * @Controller("process")
 */
class Customweb_Stripe_Endpoint_Process extends Customweb_Payment_Endpoint_Controller_Process {
	/**
	 * Also used manually in payment-request.js
	 * @var string
	 */
	const ERROR_MESSAGE_PARAMETER = 'cwError';

	/**
	 * Tolerance in webhhook difference, in seconds.
	 * Default is 5 minutes.
	 * 
	 * Set to 0 to deactivate timestamp checking.
	 * 
	 * @var integer
	 */
	const TIMESTAMP_TOLERANCE = 300;

	/**
	 *
	 * @var Customweb_Core_ILogger
	 */
	private $logger;

	/**
	 * Number of tries per webhook (OptimisticLocking)
	 * @var integer
	 */
	const NUMBER_OF_RETRIES = 5;
	/**
	 * Delay in seconds for a retry (OptimisticLocking)
	 * @var integer
	 */
	const RETRY_DELAY = 2;


	public function __construct(Customweb_DependencyInjection_IContainer $container){
		if (!$container instanceof Customweb_Stripe_Container) {
			$container = new Customweb_Stripe_Container($container);
		}
		parent::__construct($container);
		$this->logger = Customweb_Core_Logger_Factory::getLogger(get_class($this));
	}

	private function failTransaction(Customweb_Stripe_Authorization_Transaction $transaction, $message){
		$this->logger->logInfo("Marking transaction {$transaction->getExternalTransactionId()} as failed: '$message'");
		if ($transaction->isAuthorizationUncertain()) {
			$transaction->setUncertainTransactionFinallyDeclined();
		}
		else if (!$transaction->isAuthorized() && !$transaction->isAuthorizationFailed()) {
			$transaction->setAuthorizationFailed($message);
			return;
		}
		$transaction->addHistoryItem(new Customweb_Payment_Authorization_DefaultTransactionHistoryItem($message, 'update'));
	}

	/**
	 * @Action("webhook")
	 *
	 * Endpoint for Stripe webhooks, to be entered in the Stripe Dashboard.
	 * Signing Secret must be configured.
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 */
	public function webhook(Customweb_Core_Http_IRequest $request){
		$this->logger->logInfo("Webhook 'process/webhook' called.");
		if (!$this->isValidWebhookRequest($request)) {
			return Customweb_Core_Http_Response::_('');
		}

		$loaded = true;
		$transaction = $this->reloadTransaction($request);
		$data = json_decode($request->getBody(), true);

		try {
			$this->verifyWebhook($request, $transaction->isLiveTransaction());
		}
		catch (Exception $e) {
			return Customweb_Core_Http_Response::_($e->getMessage())->setStatusCode(500); //TODO code 403?
		}

		for ($i = 1; $i <= self::NUMBER_OF_RETRIES; $i++) {
			try {
				if (!$loaded) {
					$transaction = $this->reloadTransaction($request);
				}
				$error = $this->processWebhook($transaction, $data);
				$message = $this->getWebhookMessage($data, $error);
				$transaction->addHistoryItem(new Customweb_Payment_Authorization_DefaultTransactionHistoryItem($message, 'update'));
				$this->getTransactionHandler()->persistTransactionObject($transaction);
				$this->getTransactionHandler()->commitTransaction();
				$this->logger->logInfo($message);
				break;
			}
			catch (Customweb_Payment_Exception_OptimisticLockingException $exc) {
				if ($i == self::NUMBER_OF_RETRIES) {
					$this->logger->logException($exc, $request);
				}
				$this->getTransactionHandler()->rollbackTransaction();
				$loaded = false;
				sleep(self::RETRY_DELAY);
			}
		}

		return Customweb_Core_Http_Response::_('');
	}

	/**
	 * Starts a db transaction and loads the payment transaction fromt he db.
	 * 
	 * @return Customweb_Stripe_Authorization_Transaction|Customweb_Payment_Authorization_ITransaction
	 */
	private function reloadTransaction(Customweb_Core_Http_IRequest $request){
		$this->getTransactionHandler()->beginTransaction();
		return $this->loadTransaction($request);
	}

	/**
	 * Decides which actions need to be taken for the given webhook, and then processes them. Returns an error if it occurs
	 * 
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @param array $data
	 * @return null|string The error message, if one occurs.
	 */
	private function processWebhook(Customweb_Stripe_Authorization_Transaction $transaction, array $data){
		$error = null;
		try {
			switch ($data['type']) {
				case 'source.chargeable':
					$this->processChargeable($transaction, $data);
					break;
				case 'charge.succeeded':
					$this->finalizeCharge($transaction, $data);
					break;
				case 'source.canceled':
					$this->processCancelled($transaction, $data['id']);
					break;
				case 'source.failed':
				case 'charge.failed':
					$transaction->setPaymentId($data['id']);
					$this->failTransaction($transaction, $this->getFailureMessage($data['data']['object']));
					break;
				default:
					$error = Customweb_I18n_Translation::__('Unsupported webhook type.');
					break;
			}
		}
		catch (Customweb_Stripe_Exception_PaymentErrorException $e) {
			$error = $e->getErrorMessage()->getBackendMessage();
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}
		return $error;
	}

	private function getWebhookMessage(array $data, $error = null){
		$webhookType = $data['type'];

		if ($error != null) {
			$message = "Unable to process webhook '$webhookType': '$error'";
		}
		else {
			$message = "Completed processing '$webhookType'";
		}

		return Customweb_I18n_Translation::__("Webhook 'process/webhook' complete: '!message'", array(
			'!message' => $message
		));
	}

	private function getFailureMessage(array $data){
		$adminMessage = $userMessage = Customweb_I18n_Translation::__('Your payment failed and your order couldn’t be processed.');
		if (isset($data['failure_message'])) {
			$userMessage = $data['failure_message'];
		}
		else if (isset($data['failure_code'])) {
			$userMessage .= ' (' . $data['failure_code'] . ')';
		}
		if (isset($data['outcome'])) {
			if (isset($data['outcome']['seller_message'])) {
				$adminMessage = $data['outcome']['seller_message'];
			}
			if (isset($data['outcome']['network_status'])) {
				$adminMessage .= ' ' .
						Customweb_I18n_Translation::__("Network Status: !network_status.",
								array(
									'!network_status' => $data['outcome']['network_status']
								));
			}
			if (isset($data['outcome']['reason'])) {
				$adminMessage .= ' ' . Customweb_I18n_Translation::__("Reason: !reason.", array(
					'!reason' => $data['outcome']['reason']
				));
			}
			if (isset($data['outcome']['risk_level'])) {
				$adminMessage .= ' ' . Customweb_I18n_Translation::__("Risk Level: !level.", array(
					'!level' => $data['outcome']['risk_level']
				));
			}
			if (isset($data['outcome']['type'])) {
				$adminMessage .= ' ' . Customweb_I18n_Translation::__("Type: !type.", array(
					'!type' => $data['outcome']['type']
				));
			}
		}
		return new Customweb_Payment_Authorization_ErrorMessage($userMessage, $adminMessage);
	}

	private function processCancelled(Customweb_Stripe_Authorization_Transaction $transaction, $sourceId){
		if ($transaction->getThreeDSource() === null) {
			$message = Customweb_I18n_Translation::__("The threed source (@source) is now cancelled.", array(
				'@source' => $sourceId
			));
		}
		else {
			$message = Customweb_I18n_Translation::__("The source (@source) can no longer be used for new charges.", array(
				'@source' => $sourceId
			));
		}
		$this->failTransaction($transaction, $message);
	}

	private function finalizeCharge(Customweb_Stripe_Authorization_Transaction $transaction, array $eventObject){
		if ($transaction->isCaptured()) {
			$this->logger->logInfo("Skipping finalize because it is already captured.", $eventObject);
			return;
		}
		if ($eventObject['data']['object']['object'] != 'charge') {
			$this->logger->logError("Expected data.object to be of type 'charge'.", $eventObject);
			throw new InvalidArgumentException("Expected data.object to be of type 'charge'.");
		}

		$chargeObject = $eventObject['data']['object'];

		$simulatedResponse = Customweb_Core_Http_Response::_(json_encode($chargeObject))->setStatusCode(200);
		$chargeResponseProcessor = new Customweb_Stripe_Communication_Charge_ResponseProcessor($transaction,
				$this->getContainer()->getBean('Customweb_Payment_BackendOperation_Adapter_Shop_ICapture'));
		$chargeResponseProcessor->process($simulatedResponse);
	}

	private function processChargeable(Customweb_Stripe_Authorization_Transaction $transaction, array $parameters){
		if ($transaction->isAuthorized() && !$transaction->isAuthorizationUncertain()) {
			$this->logger->logInfo("Skipping chargeable because it is already authorized (and not uncertain).", $parameters);
			return; // e.g. reusable sources, delayed webhooks etc.
		}
		if ($parameters['data']['object']['object'] != 'source') {
			$this->logger->logError("Expected data.object to be of type 'source'.", $parameters);
			throw new InvalidArgumentException("Expected data.object to be of type 'source'.");
		}
		$simulatedSourceResponse = Customweb_Core_Http_Response::_(json_encode($parameters['data']['object']));
		$simulatedSourceResponse->setStatusCode(200);

		$sourceProcessor = new Customweb_Stripe_Communication_Source_ResponseProcessor($transaction, $this->getContainer());
		$sourceProcessor->process($simulatedSourceResponse);

		$chargeAdapter = new Customweb_Stripe_Communication_Charge_Adapter($transaction, $this->getContainer());
		$chargeAdapter->process();
	}

	/**
	 * @Action("ajax-failure")
	 *
	 * Fails the given transaction, adds an error message if supplied.
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 */
	public function ajaxFailure(Customweb_Core_Http_IRequest $request, Customweb_Stripe_Authorization_Transaction $transaction){
		$this->logger->logInfo("Webhook 'process/ajax-failure' called.");

		$this->verifySecuritySignature($transaction, $request, 'ajax-failure');
		$parameters = $request->getParameters();

		$message = Customweb_I18n_Translation::__("There was an error processing your transaction.");
		if (isset($parameters[self::ERROR_MESSAGE_PARAMETER])) {
			$message = strip_tags($parameters[self::ERROR_MESSAGE_PARAMETER]);
		}

		$transaction->setAuthorizationFailed($message);

		$this->logger->logInfo("Webhook 'process/ajax-failure' complete.");

		return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
	}

	/**
	 * @ExtractionMethod
	 * (non-PHPdoc)
	 *
	 * @see Customweb_Payment_Endpoint_Controller_Abstract::getTransactionId()
	 */
	public function getTransactionId(Customweb_Core_Http_IRequest $request){
		$parameters = $request->getParameters();

		if (isset($parameters['cw_transaction_id'])) {
			return array(
				'id' => $parameters['cw_transaction_id'],
				'key' => Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY
			);
		}

		$parameters = json_decode($request->getBody(), true);

		if (isset($parameters['data']['object']['metadata']['cw_transaction_id'])) {
			return array(
				'id' => $parameters['data']['object']['metadata']['cw_transaction_id'],
				'key' => Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY
			);
		}

		if (isset($parameters['data']['object']['source']['metadata']['cw_transaction_id'])) {
			return array(
				'id' => $parameters['data']['object']['metadata']['cw_transaction_id'],
				'key' => Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY
			);
		}

		$idPattern = "/cw_transaction_id=(\S*?)(?>\s|&|$)/";
		$matches = array();
		preg_match($idPattern, $request->getBody(), $matches);

		if (!empty($matches)) {
			return array(
				'id' => $matches[1],
				'key' => Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY
			);
		}

		throw new Exception("No transaction id present in the request.");
	}

	private function verifyWebhook(Customweb_Core_Http_IRequest $request, $isLive){
		$headers = $request->getParsedHeaders();
		if (!isset($headers['Stripe-Signature'])) {
			$this->logger->logError("Header 'Stripe-Signature' not set.", $headers);
			throw new Exception("Invalid signature."); // TODO catch exception, return 500er error (prevent db log rollback)
		}

		if ($request instanceof Customweb_Core_Http_ContextRequest) {
			$header = $headers['Stripe-Signature'];
		}
		else {
			// interface definition is array, ContextRequest implementations do not return array.
			$header = $headers['Stripe-Signature'];
			if (is_array($header)) {
				$header = $header[0];
			}
		}

		$pairs = explode(',', $header);
		$timestamp = null;
		$expectedSignature = null;
		foreach ($pairs as $pair) {
			list($key, $value) = explode('=', $pair);
			if ($key == 't') {
				$timestamp = $value;
			}
			else if ($key == 'v1') {
				$expectedSignature = $value;
			}
		}

		if ($timestamp == null || $expectedSignature == null) {
			$this->logger->logError("Timestamp or signature could not be extracted.",
					array(
						'Headers' => $headers,
						'Header' => $header,
						'Timestamp' => $timestamp,
						'Expected Signature' => $expectedSignature
					));
			throw new Exception("Invalid signature.");
		}

		$payload = $timestamp . '.' . $request->getBody();

		$actualSignature = hash_hmac($this->getContainer()->getConfiguration()->getWebhookHashMethod(), $payload,
				$this->getContainer()->getConfiguration()->getWebhookSecret($isLive));

		if (!$this->hashEquals($expectedSignature, $actualSignature)) {
			$this->logger->logError("Calculated signature does not match expected one.",
					array(
						'Headers' => $headers,
						'Body' => $request->getBody(),
						'Calculated' => $actualSignature,
						'Expected' => $expectedSignature
					));
			throw new Exception("Invalid signature.");
		}

		if (self::TIMESTAMP_TOLERANCE > 0) {
			$current = time();
			if ($current > $timestamp + self::TIMESTAMP_TOLERANCE || $current < $timestamp - self::TIMESTAMP_TOLERANCE) {
				$this->logger->logError("Signature matched, but timestamp is out of tolerance range.",
						array(
							"Server Timestamp" => $current,
							"Stripe Timstamp" => $timestamp,
							"Tolerance" => self::TIMESTAMP_TOLERANCE
						));
				throw new Exception("Invalid signature.");
			}
		}
	}

	/**
	 * Timing safe string comparison
	 * http://php.net/manual/en/function.hash-equals.php#115635
	 * asphp at dsgml dot com
	 * 
	 * @param unknown $str1
	 * @param unknown $str2
	 * @return boolean|unknown
	 */
	private function hashEquals($str1, $str2){
		// hash_equals available with PHP 5.6
		if (!function_exists('hash_equals')) {
			if (strlen($str1) != strlen($str2)) {
				return false;
			}
			else {
				$res = $str1 ^ $str2;
				$ret = 0;
				for ($i = strlen($res) - 1; $i >= 0; $i--)
					$ret |= ord($res[$i]);
				return !$ret;
			}
		}
		else {
			return hash_equals($str1, $str2);
		}
	}

	private function verifySecuritySignature(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_Core_Http_IRequest $request, $action){
		$parameters = $request->getParameters();
		if (!isset($parameters[Customweb_Stripe_Container::HASH_PARAMETER])) {
			throw new Exception("Hash not set.");
		}
		$transaction->checkSecuritySignature("process/$action", $parameters[Customweb_Stripe_Container::HASH_PARAMETER]);
	}

	private function loadTransaction(Customweb_Core_Http_IRequest $request){
		$id = $this->getTransactionId($request);
		if ($id['key'] != Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY) {
			throw new Exception(
					"Expected key to be of type '" . Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY .
					"', received '" . $id['key'] . "'.");
		}

		$externalTransactionId = $id['id'];

		$transaction = $this->getTransactionHandler()->findTransactionByTransactionExternalId($externalTransactionId, false);
		if (!$transaction instanceof Customweb_Stripe_Authorization_Transaction) {
			throw new Exception('Could not load transaction.');
		}

		return $transaction;
	}

	private function isValidWebhookRequest(Customweb_Core_Http_IRequest $request){
		if ($this->isExternalIdUnavailable($request)) {
			$this->logger->logInfo("Webhook 'process/webhook' skipped due to (expected) missing externalTransactionId.", $request);
			return false;
		}
		if (!$this->isCorrectShopId($request)) {
			$this->logger->logInfo("Webhook 'process/webhook' skipped due to wrong shop id.");
			return false;
		}
		return true;
	}

	/**
	 * Checks if a shop ID is set on the transaction, and if it matches the current configuration.
	 * 
	 * @param Customweb_Core_Http_IRequest $request
	 * @return boolean
	 */
	private function isCorrectShopId(Customweb_Core_Http_IRequest $request){
		$shopId = null;
		$result = array();
		preg_match_all('/"cw_shop_id": "(.+?)"/', $request->getBody(), $result);
		if (isset($result[1])) {
			$inner = $result[1];
			if (isset($inner[0])) {
				$shopId = $inner[0];
			}
		}
		return $shopId == $this->getContainer()->getConfiguration()->getShopId();
	}

	/**
	 * If the notification is expected to not have an external transaction id yet.
	 * Enables multistore / multi-endpoint without 500 errors in Stripe.
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @return boolean
	 */
	private function isExternalIdUnavailable(Customweb_Core_Http_IRequest $request){
		$parameters = json_decode($request->getBody(), true);
		if (isset($parameters['data']['object']['metadata']['cw_transaction_id'])) {
			if (substr($parameters['data']['object']['metadata']['cw_transaction_id'], 0, 7) == 'not_set') {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return Customweb_Stripe_Container
	 */
	protected function getContainer(){
		$container = parent::getContainer();
		if (!$container instanceof Customweb_Stripe_Container) {
			$container = new Customweb_Stripe_Container($container);
		}
		return $container;
	}

	/**
	 * @Action("createintent")
	 * 
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @param Customweb_Core_Http_IRequest $request
	 */
	public function createIntent(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_Core_Http_IRequest $request){
		$this->logger->logInfo("Endpint 'process/createintent' called.");
		$this->verifySecuritySignature($transaction, $request, 'createintent');
		$parameters = $request->getParameters();
		if(isset($parameters['paymentMethod'])) {
			$transaction->setPaymentMethodId($parameters['paymentMethod']);
			$confirm = false;
		}
		else if(isset($parameters['token'])) {
			$transaction->setToken($parameters['token']);
			$confirm = true;
		}
		$adapter = new Customweb_Stripe_Communication_PaymentIntent_Create_Adapter($transaction, $this->getContainer(), $confirm);
		$json = $adapter->process(); // save this json
		return Customweb_Core_Http_Response::_(json_encode($json));
	}
	
	/**
	 * Creates an order. Similar to ajax-authorize, but does not return a redirection.
	 *
	 * @Action("create")
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @param Customweb_Core_Http_IRequest $request
	 */
	public function create(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_Core_Http_IRequest $request){
		$this->logger->logInfo("Webhook 'create' called.");
		
		$this->verifySecuritySignature($transaction, $request, 'create');
		try {
			$parameters = $request->getParameters();
			if (isset($parameters['token'])) {
				$transaction->setToken($parameters['token']);
			}
			else if (isset($parameters['source'])) {
				$transaction->setSource($parameters['source']);
			}
			else {
				throw new Exception("Expecting source or token in parameters.");
			}
			$charge = new Customweb_Stripe_Communication_Charge_Adapter($transaction, $this->getContainer());
			$charge->process();
			$this->logger->logInfo("Webhook 'create' completed successfully.");
			return Customweb_Core_Http_Response::_("")->setStatusCode(200);
		}
		catch (Exception $e) {
			$this->logger->logError("Webhook 'create' failed.", $e);
			$transaction->setAuthorizationFailed($e->getMessage());
			return Customweb_Core_Http_Response::_("")->setStatusCode(500);
		}
	}
}