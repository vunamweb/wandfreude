<?php
/**
  * You are allowed to use this API in your web application.
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

require_once 'Customweb/Payment/Authorization/Hidden/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Server/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Ajax/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/PaymentPage/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Iframe/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/Widget/ITransactionContext.php';
require_once 'Customweb/Payment/Authorization/IUpdateTransactionContext.php';

require_once 'StripeCw/Util.php';
require_once 'StripeCw/Entity/Transaction.php';


class StripeCw_TransactionContext implements Customweb_Payment_Authorization_PaymentPage_ITransactionContext,
Customweb_Payment_Authorization_Hidden_ITransactionContext, Customweb_Payment_Authorization_Server_ITransactionContext,
Customweb_Payment_Authorization_Iframe_ITransactionContext, Customweb_Payment_Authorization_Ajax_ITransactionContext,
Customweb_Payment_Authorization_IUpdateTransactionContext, Customweb_Payment_Authorization_Widget_ITransactionContext
{
	protected $aliasTransactionId = NULL;
	protected $paymentCustomerContext = null;
	protected $orderContext;
	protected $databaseTransactionId = NULL;
	private $databaseTransaction = NULL;

	public function __construct(StripeCw_Entity_Transaction $transaction, StripeCw_OrderContext $orderContext, $aliasTransactionId = NULL) {

		$aliasTransactionIdCleaned = NULL;
		if (StripeCw_Util::isAliasManagerActive($orderContext)) {
			if ($aliasTransactionId === NULL || $aliasTransactionId === 'new') {
				$aliasTransactionIdCleaned = 'new';
			}
			else {
				$aliasTransactionIdCleaned = intval($aliasTransactionId);
			}
		}
		$this->aliasTransactionId = $aliasTransactionIdCleaned;
		$this->paymentCustomerContext = StripeCw_Util::getPaymentCustomerContext($orderContext->getCustomerId());
		$this->orderContext = $orderContext;
		$this->databaseTransaction = $transaction;
		$this->databaseTransactionId = $transaction->getTransactionId();
	}

	/**
	 * @return StripeCw_Entity_Transaction
	 */
	public function getDatabaseTransaction() {
		if ($this->databaseTransaction === NULL) {
			$this->databaseTransaction = StripeCw_Entity_Transaction::loadById($this->databaseTransactionId);
		}

		return $this->databaseTransaction;
	}

	public function getCapturingMode() {
		return null;
	}

	public function getJavaScriptSuccessCallbackFunction() {
		return '
		function (redirectUrl) {
			window.location = redirectUrl
		}';
	}

	public function getJavaScriptFailedCallbackFunction() {
		return '
		function (redirectUrl) {
			window.location = redirectUrl
		}';
	}

	public function __sleep() {
		return array('aliasTransactionId', 'paymentCustomerContext', 'orderContext', 'databaseTransactionId');
	}

	public function getOrderContext() {
		return $this->orderContext;
	}

	public function getTransactionId() {
		return $this->getDatabaseTransaction()->getTransactionId();
	}
	
	public function getOrderId() {
		return $this->getDatabaseTransaction()->getOrderId();
	}
	
	public function isOrderIdUnique() {
		return false;
	}
	

	public function createRecurringAlias() {
		return false;
	}

	public function getAlias() {
		if ($this->aliasTransactionId === 'new') {
			return 'new';
		}

		if ($this->aliasTransactionId !== null) {
			$transcation = StripeCw_Entity_Transaction::loadById($this->aliasTransactionId);
			if ($transcation !== null && $transcation->getTransactionObject() !== null && $transcation->getCustomerId() == $this->getOrderContext()->getCustomerId()) {
				return $transcation->getTransactionObject();
			}
		}

		return null;
	}

	public function getCustomParameters() {
		return array(
			'cw_transaction_id' => $this->getDatabaseTransaction()->getTransactionId(),
		);
	}

	public function getSuccessUrl() {
		return StripeCw_Util::getUrl('process', 'success', array(), true);
	}

	public function getFailedUrl() {
		return StripeCw_Util::getUrl('process', 'failed', array(), true);
	}

	public function getPaymentCustomerContext() {
		return $this->paymentCustomerContext;
	}

	public function getNotificationUrl() {
		return StripeCw_Util::getUrl('process', 'notify', array(), true);
	}

	public function getIframeBreakOutUrl() {
		return StripeCw_Util::getUrl('process', 'iframe_breakout', array(), true);
	}

	public function getUpdateUrl() {
		return StripeCw_Util::getUrl('update', 'notify', array(), true);
	}

}