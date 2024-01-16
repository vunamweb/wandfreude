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

require_once 'Customweb/DependencyInjection/Container/Extendable.php';


/**
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Container extends Customweb_DependencyInjection_Container_Extendable {
	const HASH_PARAMETER = 'securityHash';

	/**
	 *
	 * @return Customweb_Stripe_Configuration
	 */
	public function getConfiguration(){
		return $this->getBean('Customweb_Stripe_Configuration');
	}

	public function getPaymentMethodByTransaction(Customweb_Stripe_Authorization_Transaction $transaction){
		return $this->getPaymentMethod($transaction->getPaymentMethod(), $transaction->getAuthorizationMethod());
	}

	public function getPaymentMethod(Customweb_Payment_Authorization_IPaymentMethod $method, $authorizationMethodName){
		return $this->getPaymentMethodFactory()->getPaymentMethod($method, $authorizationMethodName);
	}

	/**
	 *
	 * @return Customweb_Payment_Endpoint_IAdapter
	 */
	public function getEndpointAdapter(){
		return $this->getBean('Customweb_Payment_Endpoint_IAdapter');
	}

	/**
	 *
	 * @return Customweb_Core_Http_IRequest
	 */
	public function getHttpRequest(){
		return $this->getBean('Customweb_Core_Http_IRequest');
	}
	
	/**
	 * 
	 * @return Customweb_Asset_IResolver
	 */
	public function getAssetResolver() {
		return $this->getBean('Customweb_Asset_IResolver');
	}

	/**
	 *
	 * @return Customweb_Stripe_Method_Factory
	 */
	private function getPaymentMethodFactory(){
		return $this->getBean('Customweb_Stripe_Method_Factory');
	}

	/**
	 * Creates an URL for an endpoint which includes a security hash.
	 * 
	 * @param Customweb_Stripe_Authorization_Transaction $transaction
	 * @param string $action
	 * @param string $controller
	 * @param string $separator
	 * @param string $parameterName
	 */
	public function getSecuredEndpoint(Customweb_Stripe_Authorization_Transaction $transaction, $action, $controller = 'process', $separator = '/', $parameterName = null, $parameters = array()){
		if ($parameterName == null) {
			$parameterName = self::HASH_PARAMETER;
		}
		$parameters['cw_transaction_id'] = $transaction->getExternalTransactionId();
		$parameters[$parameterName] = $transaction->getSecuritySignature($controller.$separator.$action);
		return $this->getEndpointAdapter()->getUrl($controller, $action, $parameters);
	}
}