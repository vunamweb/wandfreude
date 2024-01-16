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
require_once 'Customweb/Stripe/Method/Abstract.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Communication/Customer/Adapter.php';
require_once 'Customweb/Stripe/Communication/Source/Adapter.php';
require_once 'Customweb/Stripe/Communication/Response/DefaultProcessor.php';
require_once 'Customweb/Core/Logger/Factory.php';


/**
 * Processes a source response.
 * Checks if it is reusable, and attempts to create an alias.
 * The process() function returns true if the source can be charged.
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_Source_ResponseProcessor extends Customweb_Stripe_Communication_Response_DefaultProcessor {
	private $transaction;
	private $container;
	
	/**
	 *
	 * @var Customweb_Core_ILogger
	 */
	private $logger;

	public function __construct(Customweb_Stripe_Authorization_Transaction $transaction, Customweb_Stripe_Container $container){
		$this->transaction = $transaction;
		$this->container = $container;
		$this->logger = Customweb_Core_Logger_Factory::getLogger(get_class($this));
	}

	/**
	 * Processes the source response.
	 * This may authorize transactions depending on payment method (isSourcePendingAuthorized), and set them uncertain.
	 * It will fail any invalid sources, and create an alias if required.
	 * If a payment method requires specific information to be set on the transaction, use the method
	 *
	 * @see Customweb_Stripe_Method_Abstract::processPaymentInformation(array $json)
	 *
	 * Returns true if the source is chargeable.
	 *
	 * (non-PHPdoc)
	 *
	 * @see Customweb_Stripe_Communication_Response_DefaultProcessor::processInternal()
	 */
	protected function processInternal(Customweb_Core_Http_IResponse $response){
		$json = json_decode($response->getBody(), true);
		
		$this->getTransaction()->setUpdateExecutionDate(null);
		$this->getTransaction()->setSource($json['id']);
		
		$chargeable = false;
		
		switch ($json['status']) {
			case 'chargeable':
				$chargeable = true;
				break;
			case 'consumed':
				break;
			case 'pending':
				// e.g. SEPA the source may be pending for multiple days.
				if ($this->getContainer()->getPaymentMethodByTransaction($this->getTransaction())->isSourcePendingAuthorized()) {
					if (!$this->getTransaction()->isAuthorized()) {
						$this->getTransaction()->authorize();
						$this->getTransaction()->setAuthorizationUncertain();
					}
				}
				break;
			case 'failed':
				if (!$this->getTransaction()->isAuthorizationFailed()) {
					$this->getTransaction()->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment has failed.'));
				}
				if ($this->getTransaction()->isAuthorized() && $this->getTransaction()->isAuthorizationUncertain()) {
					$this->getTransaction()->setUncertainTransactionFinallyDeclined();
				}
				break;
			case 'canceled':
				if (!$this->getTransaction()->isAuthorizationFailed()) {
					$this->getTransaction()->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment has been canceled.'));
				}
				if ($this->getTransaction()->isAuthorized() && $this->getTransaction()->isAuthorizationUncertain()) {
					$this->getTransaction()->setUncertainTransactionFinallyDeclined();
				}
				break;
		}
		
		if ($this->getTransaction()->getTransactionContext()->getAlias() == 'new' ||
				 $this->getTransaction()->getTransactionContext()->createRecurringAlias()) {
			$this->processAlias($this->getTransaction(), $json);
		}
		
		if ($this->getTransaction()->getCustomerId() === null) {
			if (isset($json['customer'])) {
				$this->getTransaction()->setCustomerId($json['customer']);
			}
			else {
				try {
					$customerAdapter = new Customweb_Stripe_Communication_Customer_Adapter($this->getTransaction(), $this->getContainer(), $this->getTransaction()->getSource(), 'source');
					$customerAdapter->process();
				}
				catch (Customweb_Stripe_Exception_PaymentErrorException $e) {
					if ($e->getMessage() != 'The source you provided has already been attached to a customer.') {
						$this->logger->logInfo($e->getMessage(), $e);
						return $this->processError($e->getErrorMessage());
					}
				}
			}
		}
		
		$this->getContainer()->getPaymentMethodByTransaction($this->getTransaction())->processPaymentInformation($this->getTransaction(), $json);
		
		return $chargeable;
	}

	private function processAlias(Customweb_Stripe_Authorization_Transaction $transaction, $json){
		$this->logger->logInfo('Creating alias for transaction ' . $transaction->getExternalTransactionId(), $json);
		
		if ($json['usage'] == 'reusable') {
			$transaction->setAlias($json['id']);
			$transaction->setAliasForDisplay($this->getContainer()->getPaymentMethodByTransaction($transaction)->extractAliasForDisplay($json));
			$transaction->setAliasSecret($json['client_secret']);
		}
		else if ($json['type'] == 'three_d_secure') {
			$this->logger->logInfo('Alias based on 3D-Secure source.');
			//$transaction->setAlias($json['three_d_secure']['card']);
			//$transaction->setThreeDSource($json['three_d_secure']['card']);
			$transaction->setSource($json['three_d_secure']['card']);
			$adapter = new Customweb_Stripe_Communication_Source_Adapter($transaction, $this->getContainer());
			$adapter->process(false);
		}
		else {
			throw new Exception(Customweb_I18n_Translation::__('An alias could not be created - the source is not reusable.'));
		}
		$this->logger->logInfo('Created alias for transaction ' . $this->getTransaction()->getExternalTransactionId());
	}

	protected function processError(Customweb_Payment_Authorization_ErrorMessage $error){
		$this->logger->logInfo('Set authorization failed for transaction ' . $this->getTransaction()->getExternalTransactionId());
		$this->getTransaction()->setAuthorizationFailed($error);
		throw new Customweb_Stripe_Exception_PaymentErrorException($error);
	}

	protected function getContainer(){
		return $this->container;
	}

	protected function getTransaction(){
		return $this->transaction;
	}
}