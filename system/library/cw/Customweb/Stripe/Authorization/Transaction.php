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

require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Payment/Authorization/DefaultTransaction.php';

class Customweb_Stripe_Authorization_Transaction extends Customweb_Payment_Authorization_DefaultTransaction {
	private $brand;
	private $source;
	private $threeDSource;
	private $mandateUrl;
	private $mandateReference;
	private $btcAmount;
	private $btcReceiver;
	private $btcUri;
	private $p24Reference;
	private $token;
	/**
	 * In order to prevent duplicate charges we generate a key on transaction creation which is used to identify charge requests.
	 * @var string
	 */
	private $chargeIdempotencyKey;
	private $iban;
	private $paymentIntent;
	private $paymentMethodId;
	private $aliasSecret;

	/**
	 * @return mixed
	 */
	public function getPaymentMethodId(){
		return $this->paymentMethodId;
	}

	/**
	 * @param mixed $paymentMethodId
	 */
	public function setPaymentMethodId($paymentMethodId){
		$this->paymentMethodId = $paymentMethodId;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentIntent(){
		return $this->paymentIntent;
	}

	/**
	 * @param mixed $paymentIntent
	 */
	public function setPaymentIntent($paymentIntent){
		$this->paymentIntent = $paymentIntent;
	}

	/**
	 * @return mixed
	 */
	public function getIban(){
		return $this->iban;
	}

	/**
	 * @param mixed $iban
	 */
	public function setIban($iban){
		$this->iban = $iban;
	}

	/**
	 * @return mixed
	 */
	public function getToken(){
		return $this->token;
	}

	/**
	 * @param mixed $token
	 */
	public function setToken($token){
		$this->token = $token;
	}

	/**
	 * @return mixed
	 */
	public function getChargeIdempotencyKey(){
		return $this->chargeIdempotencyKey;
	}

	/**
	 * @param mixed $chargeIdempotencyKey
	 */
	public function setChargeIdempotencyKey($chargeIdempotencyKey){
		$this->chargeIdempotencyKey = $chargeIdempotencyKey;
	}

	public function getSource(){
		if (empty($this->source)) {
			$parameters = $this->getAuthorizationParameters();
			if (isset($parameters['source']['id'])) {
				$this->source = $parameters['source']['id'];
			}
			if ($this->getToken()) {
				return $this->getToken();
			}
		}
		return $this->source;
	}

	public function setSource($source){
		$this->source = $source;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAliasSecret(){
		return $this->aliasSecret;
	}

	/**
	 * @param mixed $aliasSecret
	 */
	public function setAliasSecret($aliasSecret){
		$this->aliasSecret = $aliasSecret;
	}

	public function setThreeDSource($threeDSource){
		$this->threeDSource = $threeDSource;
		return $this;
	}

	public function getThreeDSource(){
		return $this->threeDSource;
	}

	public function getMandateUrl(){
		return $this->mandateUrl;
	}

	public function setMandateUrl($mandateUrl){
		$this->mandateUrl = $mandateUrl;
		return $this;
	}

	public function getMandateReference(){
		return $this->mandateReference;
	}

	public function setMandateReference($mandateReference){
		$this->mandateReference = $mandateReference;
		return $this;
	}

	public function getBtcAmount(){
		return $this->btcAmount;
	}

	public function setBtcAmount($btcAmount){
		$this->btcAmount = $btcAmount;
		return $this;
	}

	public function getBtcReceiver(){
		return $this->btcReceiver;
	}

	public function setBtcReceiver($btcReceiver){
		$this->btcReceiver = $btcReceiver;
		return $this;
	}

	public function getBtcUri(){
		return $this->btcUri;
	}

	public function setBtcUri($btcUri){
		$this->btcUri = $btcUri;
		return $this;
	}

	public function getPaymentInformation(){
		if ($this->getMandateReference() != null && $this->getMandateUrl() != null) {
			return Customweb_I18n_Translation::__("Mandate Reference: @reference<br/>Mandate URL: <a href='@url'>Open</a>",
					array(
						'@reference' => $this->getMandateReference(),
						'@url' => $this->getMandateUrl() 
					));
		}
		
		if ($this->getBtcAmount() != null && $this->getBtcReceiver() != null && $this->getBtcUri() != null) {
			return Customweb_I18n_Translation::__(
					"Please transfer your BTC to complete the payment process.<br/>BTC Amount: @amount<br/>Receiver: @receiver<br/>URI:<a href='!uri'>Send</a>",
					array(
						'@amount' => $this->getBtcAmount(),
						'@receiver' => $this->getBtcReceiver(),
						'!uri' => $this->getBtcUri() 
					));
		}
		
		if ($this->getP24Reference() != null) {
			return Customweb_I18n_Translation::__("P24 Reference: @reference", array(
				'@reference' => $this->getP24Reference() 
			));
		}
		
		return null;
	}

	protected function getTransactionSpecificLabels(){
		$labels = array();
		
		// general
		$labels['charge_id'] = array(
			'label' => Customweb_I18n_Translation::__("Charge ID"),
			'value' => $this->getChargeId() 
		);
		if ($this->getCustomerId() !== null) {
			$labels['customer_id'] = array(
				'label' => Customweb_I18n_Translation::__("Customer ID"),
				'value' => $this->getCustomerId() 
			);
		}
		if ($this->getPaymentIntent()) {
			$labels['payment_intent'] = array(
				'label' => Customweb_I18n_Translation::__("Payment Intent"),
				'value' => $this->getPaymentIntent() 
			);
		}
		if ($this->getPaymentMethodId()) {
			$labels['payment_method_id'] = array(
				'label' => Customweb_I18n_Translation::__("Payment Method ID"),
				'value' => $this->getPaymentMethodId() 
			);
		}
		if ($this->getSource()) {
			$labels['source'] = array(
				'label' => Customweb_I18n_Translation::__("Source ID"),
				'value' => $this->getSource() 
			);
		}
		
		// card
		if ($this->getThreeDSource() !== null) {
			$labels['three_d_source'] = array(
				'label' => Customweb_I18n_Translation::__("3D source"),
				'value' => $this->getThreeDSource() 
			);
		}
		
		// sepa
		if ($this->getMandateReference() !== null) {
			$labels['mandate_reference'] = array(
				'label' => Customweb_I18n_Translation::__("Mandate reference"),
				'value' => $this->getMandateReference() 
			);
		}
		if ($this->getMandateUrl() !== null) {
			$labels['mandate_url'] = array(
				'label' => Customweb_I18n_Translation::__("Mandate URL"),
				'value' => $this->getMandateUrl() 
			);
		}
		
		// bitcoin
		if ($this->getBtcAmount() !== null) {
			$labels['btc_amount'] = array(
				'label' => Customweb_I18n_Translation::__("Amount in BTC"),
				'value' => $this->getBtcAmount() 
			);
		}
		if ($this->getBtcReceiver() !== null) {
			$labels['btc_receiver'] = array(
				'label' => Customweb_I18n_Translation::__("Bitcoin receiver"),
				'value' => $this->getBtcReceiver() 
			);
		}
		if ($this->getBtcUri() !== null) {
			$labels['btc_uri'] = array(
				'label' => Customweb_I18n_Translation::__("Bitcoin transfer URI"),
				'value' => $this->getBtcUri() 
			);
		}
		
		// p24
		if ($this->getP24Reference() !== null) {
			$labels['p24_reference'] = array(
				'label' => Customweb_I18n_Translation::__("P24 reference"),
				'value' => $this->getP24Reference() 
			);
		}
		
		// deprecated
		if ($this->getCardBrand() !== null) {
			$labels['brand'] = array(
				'label' => Customweb_I18n_Translation::__("Card brand"),
				'value' => $this->getCardBrand() 
			);
		}
		
		return $labels;
	}
	/*
	 * below is deprecated
	 */
	private $chargeId;
	private $customerId;
	private $cardId;
	private $expiryMonth;
	private $expiryYear;
	private $processAuthorizationUrl = null;

	public function getChargeId(){
		return $this->chargeId;
	}

	public function getPaymentId(){
		return $this->getChargeId();
	}

	public function setChargeId($chargeId){
		$this->chargeId = $chargeId;
		return $this;
	}

	public function getCustomerId(){
		if ($this->customerId == null) {
			$map = $this->getPaymentCustomerContext()->getMap();
			if (isset($map['customer_id_' . $this->isLiveTransaction()])) {
				$this->customerId = $map['customer_id_' . $this->isLiveTransaction()];
			}
		}
		return $this->customerId;
	}

	public function setCustomerId($customerId){
		$this->customerId = $customerId;
		$map = $this->getPaymentCustomerContext()->getMap();
		$map['customer_id_' . $this->isLiveTransaction()] = $customerId;
		$this->getPaymentCustomerContext()->updateMap($map);
		return $this;
	}

	public function getCardId(){
		return $this->cardId;
	}

	public function setCardId($cardId){
		$this->cardId = $cardId;
		return $this;
	}

	public function getExpiryMonth(){
		return $this->expiryMonth;
	}

	public function setExpiryMonth($expiryMonth){
		$this->expiryMonth = $expiryMonth;
		return $this;
	}

	public function getExpiryYear(){
		return $this->expiryYear;
	}

	public function setExpiryYear($expiryYear){
		$this->expiryYear = $expiryYear;
		return $this;
	}

	public function getCardBrand(){
		$parameters = $this->getAuthorizationParameters();
		if (isset($parameters['card']['brand'])) {
			return $parameters['card']['brand'];
		}
		else if (isset($parameters['source']['brand'])) {
			return $parameters['source']['brand'];
		}
		else {
			return null;
		}
	}

	public function getBrand(){
		return $this->brand;
	}

	public function setBrand($brand){
		$this->brand = $brand;
		return $this;
	}

	public function getP24Reference(){
		return $this->p24Reference;
	}

	public function setP24Reference($p24Reference){
		$this->p24Reference = $p24Reference;
		return $this;
	}
}