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


require_once 'Customweb/Payment/Authorization/IPaymentMethod.php';

require_once 'StripeCw/Util.php';
require_once 'StripeCw/Entity/Transaction.php';
require_once 'StripeCw/PaymentMethod.php';
require_once 'StripeCw/TransactionContext.php';
require_once 'StripeCw/PaymentMethodWrapper.php';
require_once 'StripeCw/Store.php';
require_once 'StripeCw/Language.php';
require_once 'StripeCw/DefaultPaymentMethodDefinition.php';
require_once 'StripeCw/SettingApi.php';
require_once 'StripeCw/OrderContext.php';


final class StripeCw_PaymentMethod implements Customweb_Payment_Authorization_IPaymentMethod{

	/**
	 * @var StripeCw_IPaymentMethodDefinition
	 */
	private $paymentMethodDefinitions;

	/**
	 * @var StripeCw_SettingApi
	 */
	private $settingsApi;

	private static $completePaymentMethodDefinitions = array(
		'creditcard' => array(
			'machineName' => 'CreditCard',
 			'frontendName' => 'Credit Card',
 			'backendName' => 'Stripe: Credit Card',
 		),
 		'visa' => array(
			'machineName' => 'Visa',
 			'frontendName' => 'Visa',
 			'backendName' => 'Stripe: Visa',
 		),
 		'mastercard' => array(
			'machineName' => 'MasterCard',
 			'frontendName' => 'MasterCard',
 			'backendName' => 'Stripe: MasterCard',
 		),
 		'americanexpress' => array(
			'machineName' => 'AmericanExpress',
 			'frontendName' => 'American Express',
 			'backendName' => 'Stripe: American Express',
 		),
 		'discovercard' => array(
			'machineName' => 'DiscoverCard',
 			'frontendName' => 'Discover Card',
 			'backendName' => 'Stripe: Discover Card',
 		),
 		'jcb' => array(
			'machineName' => 'Jcb',
 			'frontendName' => 'JCB',
 			'backendName' => 'Stripe: JCB',
 		),
 		'diners' => array(
			'machineName' => 'Diners',
 			'frontendName' => 'Diners Club',
 			'backendName' => 'Stripe: Diners Club',
 		),
 		'ideal' => array(
			'machineName' => 'IDeal',
 			'frontendName' => 'iDEAL',
 			'backendName' => 'Stripe: iDEAL',
 		),
 		'giropay' => array(
			'machineName' => 'Giropay',
 			'frontendName' => 'giropay',
 			'backendName' => 'Stripe: giropay',
 		),
 		'sofortueberweisung' => array(
			'machineName' => 'Sofortueberweisung',
 			'frontendName' => 'Sofortüberweisung',
 			'backendName' => 'Stripe: Sofortüberweisung',
 		),
 		'btc' => array(
			'machineName' => 'Btc',
 			'frontendName' => 'Bitcoin',
 			'backendName' => 'Stripe: Bitcoin',
 		),
 		'przelewy24' => array(
			'machineName' => 'Przelewy24',
 			'frontendName' => 'Przelewy24',
 			'backendName' => 'Stripe: Przelewy24',
 		),
 		'stripepaymentrequest' => array(
			'machineName' => 'StripePaymentRequest',
 			'frontendName' => 'Apple Pay, Google Pay, Microsoft Pay',
 			'backendName' => 'Stripe: Apple Pay, Google Pay, Microsoft Pay',
 		),
 		'bcmc' => array(
			'machineName' => 'Bcmc',
 			'frontendName' => 'Bancontact',
 			'backendName' => 'Stripe: Bancontact',
 		),
 		'alipay' => array(
			'machineName' => 'Alipay',
 			'frontendName' => 'Alipay',
 			'backendName' => 'Stripe: Alipay',
 		),
 		'directdebits' => array(
			'machineName' => 'DirectDebits',
 			'frontendName' => 'Direct Debits',
 			'backendName' => 'Stripe: Direct Debits',
 		),
 	);

	public function __construct(StripeCw_IPaymentMethodDefinition $defintions) {
		$this->paymentMethodDefinitions = $defintions;
		$this->settingsApi = new StripeCw_SettingApi('stripecw_' . $this->paymentMethodDefinitions->getMachineName());
	}

	public static function getPaymentMethod($paymentMethodMachineName) {
		$paymentMethodMachineName = strtolower($paymentMethodMachineName);

		if (isset(self::$completePaymentMethodDefinitions[$paymentMethodMachineName])) {
			$def = self::$completePaymentMethodDefinitions[$paymentMethodMachineName];
			return new StripeCw_PaymentMethod(new StripeCw_DefaultPaymentMethodDefinition($def['machineName'], $def['backendName'], $def['frontendName']));
		}
		else {
			throw new Exception("No payment method found with name '" . $paymentMethodMachineName . "'.");
		}
	}

	/**
	 * @return StripeCw_SettingApi
	 */
	public function getSettingsApi() {
		return $this->settingsApi;
	}

	/**
	 * (non-PHPdoc)
	 * @see Customweb_Payment_Authorization_IPaymentMethod::getPaymentMethodName()
	 */
	public function getPaymentMethodName() {
		return strtolower($this->paymentMethodDefinitions->getMachineName());
	}

	public function getPaymentMethodDisplayName() {
		$title = $this->getSettingsApi()->getValue('title');
		$langId = StripeCw_Language::getCurrentLanguageId();
		if (!empty($title) && isset($title[$langId]) && !empty($title[$langId])) {
			return $title[$langId];
		}
		else {
			return $this->paymentMethodDefinitions->getFrontendName();
		}
	}

	public function getPaymentMethodConfigurationValue($key, $languageCode = null) {

		if ($languageCode === null) {
			return $this->getSettingsApi()->getValue($key);
		}
		else {
			$languageId = null;
			$languageCode = (string)$languageCode;
			foreach (StripeCw_Util::getLanguages() as $language) {
				if ($language['code'] == $languageCode) {
					$languageId = $language['language_id'];
					break;
				}
			}

			if ($languageId === null) {
				throw new Exception("Could not find language with language code '" . $languageCode . "'.");
			}

			return $this->getSettingsApi()->getValue($key, null, $languageId);
		}
	}

	public function existsPaymentMethodConfigurationValue($key, $languageCode = null) {
		return $this->getSettingsApi()->isSettingPresent($key);
	}

	public function getBackendPaymentMethodName() {
		return $this->paymentMethodDefinitions->getBackendName();
	}

	/**
	 * @param Customweb_Payment_Authorization_IOrderContext $context
	 * @return StripeCw_Adapter_IAdapter
	 */
	public function getPaymentAdapterByOrderContext(Customweb_Payment_Authorization_IOrderContext $context) {
		$paymentAdapter = StripeCw_Util::getAuthorizationAdapterFactory()->getAuthorizationAdapterByContext($context);
		return StripeCw_Util::getShopAdapterByPaymentAdapter($paymentAdapter);

	}

	/**
	 * @param StripeCw_Entity_Transaction $transaction
	 * @return StripeCw_Adapter_IAdapter
	 */
	public function getPaymentAdapterByTransaction(StripeCw_Entity_Transaction $transaction) {
		$paymentAdapter = StripeCw_Util::getAuthorizationAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationType());
		return StripeCw_Util::getShopAdapterByPaymentAdapter($paymentAdapter);
	}


	/**
	 * @return StripeCw_Entity_Transaction
	 */
	public function newTransaction(StripeCw_OrderContext $orderContext, $aliasTransactionId = null, $failedTransactionObject = null) {
		$transaction = new StripeCw_Entity_Transaction();

		$orderInfo = $orderContext->getOrderInfo();
		$transaction->setOrderId($orderInfo['order_id'])->setCustomerId($orderInfo['customer_id']);
		$transaction->setStoreId(StripeCw_Store::getStoreId());
		StripeCw_Util::getEntityManager()->persist($transaction);

		$transactionContext = new StripeCw_TransactionContext($transaction, $orderContext, $aliasTransactionId);
		$transactionObject = $this->getPaymentAdapterByOrderContext($orderContext)->getInterfaceAdapter()->createTransaction($transactionContext, $failedTransactionObject);
		
		unset($_SESSION['stripecw_checkout_id'][$orderContext->getPaymentMethod()->getPaymentMethodName()]);
		
		$transaction->setTransactionObject($transactionObject);
		StripeCw_Util::getEntityManager()->persist($transaction);

		return $transaction;
	}

	public function newOrderContext($orderInfo, $registry) {
		$order_totals = StripeCw_Util::getOrderTotals($registry);
		return new StripeCw_OrderContext(new StripeCw_PaymentMethodWrapper($this), $orderInfo, $order_totals);
	}
}