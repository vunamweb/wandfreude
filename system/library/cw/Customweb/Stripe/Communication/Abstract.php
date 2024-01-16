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

require_once 'Customweb/Stripe/AbstractAdapter.php';
require_once 'Customweb/Core/Http/Client/Factory.php';
require_once 'Customweb/Core/Util/Rand.php';
require_once 'Customweb/Core/Http/Authorization/Basic.php';
require_once 'Customweb/Stripe/Communication/Response/DefaultProcessor.php';
require_once 'Customweb/Core/Http/Request.php';

abstract class Customweb_Stripe_Communication_Abstract extends Customweb_Stripe_AbstractAdapter {
	private $idempotencyKey;
	private $request;
	private $response;
	private $responseParameters;
	private $responseProcessor;
	private $isLive;

	public function __construct(Customweb_DependencyInjection_IContainer $container, $isLive){
		parent::__construct($container);
		$this->isLive = $isLive;
	}

	protected abstract function buildBody();

	protected abstract function buildUrl();

	/**
	 * Checks if a shop id is set in the configuration, and adds it to the given parameters.
	 * 
	 * @param array $parameters
	 */
	protected function addShopId(array &$parameters){
		$shopId = $this->getContainer()->getConfiguration()->getShopId();
		if (!empty($shopId)) {
			$parameters['metadata']['cw_shop_id'] = $shopId;
		}
	}

	protected function getMethod(){
		return 'POST';
	}

	protected function getIdempotencyKey(){
		if ($this->idempotencyKey === null) {
			$this->idempotencyKey = Customweb_Core_Util_Rand::getUuid();
		}
		return $this->idempotencyKey;
	}
	
	protected function setIdempotencyKey($key) {
		$this->idempotencyKey = $key;
	}

	protected function buildAuthorization(){
		return new Customweb_Core_Http_Authorization_Basic($this->getContainer()->getConfiguration()->getSecretKey($this->isLive));
	}

	protected function buildRequest(){
		$request = new Customweb_Core_Http_Request();
		$request->setAuthorization($this->buildAuthorization());
		$request->appendHeader("X-Stripe-Client-User-Agent: " . $this->getXClientUserAgent());
		$request->setUserAgent($request->getUserAgent() . " " . $this->getFormattedAppInfo());
		$request->setUrl($this->buildUrl());
		$request->setBody($this->buildBody());
		$request->setMethod($this->getMethod());
		$request->appendHeader('Idempotency-Key:' . $this->getIdempotencyKey());
		return $request;
	}

	protected function getXClientUserAgent(){
		return json_encode(
				array(
					'bindings_version' => '4.0.139',
					'lang' => 'php',
					'lang_version' => phpversion(),
					'uname' => php_uname(),
					'publisher' => 'customweb',
					'application' => $this->getContainer()->getConfiguration()->getAppInfo() 
				));
	}

	protected function getFormattedAppInfo(){
		$appInfo = $this->getContainer()->getConfiguration()->getAppInfo();
		return $appInfo['name'] . "/" . $appInfo['version'] . "/" . $appInfo['url'];
	}

	/**
	 *
	 * @return Customweb_Stripe_Communication_Response_IProcessor
	 */
	protected function getResponseProcessor(){
		if ($this->responseProcessor == null) {
			$this->responseProcessor = $this->instatiateResponseProcessor();
		}
		return $this->responseProcessor;
	}

	protected function instatiateResponseProcessor(){
		return new Customweb_Stripe_Communication_Response_DefaultProcessor();
	}
	
	protected function isLive() {
		return $this->isLive;
	}

	public function process($cache = true){
		if (!$cache || $this->response === null) {
			$this->request = $this->buildRequest();
			$this->response = Customweb_Core_Http_Client_Factory::createClient()->send($this->request);
		}
		return $this->getResponseProcessor()->process($this->response);
	}
}