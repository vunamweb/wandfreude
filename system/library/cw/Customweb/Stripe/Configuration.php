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

require_once 'Customweb/Stripe/Exception/MissingConfigurationException.php';
require_once 'Customweb/I18n/Translation.php';


/**
 * @Bean
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Configuration {
	public function getAppInfo(){
		return array(
			'name' => 'OpenCart sellxed',
			'version' => '4.0.139',
			'url' => 'https://www.sellxed.com/shop',
			'partner_id' => 'pp_partner_DQAgQiS0lYOeKe'
		);
	}
	
	/**
	 *        		 		   	 	 		 
	 *
	 * @var Customweb_Payment_IConfigurationAdapter
	 */
	private $configurationAdapter = null;

	public function __construct(Customweb_Payment_IConfigurationAdapter $configurationAdapter){
		$this->configurationAdapter = $configurationAdapter;
	}

	/**
	 * Returns whether the gateway is in test mode or in live mode.
	 *        		 		   	 	 		 
	 *
	 * @return boolean True if the system is in live mode. Else return false.
	 */
	public function isLiveMode(){
		return $this->getSetting('operation_mode') == 'live';
	}
	
	public function getWaitingPageText($language) {
		return $this->getSetting("waiting_page_text", Customweb_I18n_Translation::__("Waiting Page Text") . " - " . $language, $language);
	}

	public function getPublishableKey(){
		return $this->getToggleableSetting(Customweb_I18n_Translation::__("Publishable Key"), "publishable_key", $this->isLiveMode());
	}

	public function getSecretKey($isLive){
		return $this->getToggleableSetting(Customweb_I18n_Translation::__("Secret Key"), "secret_key", $isLive);
	}

	public function getAjaxUrl(){
		return "https://js.stripe.com/v3/";
	}

	public function getApiUrl(){
		return "https://api.stripe.com/v1/";
	}

	public function getOrderIdSchema(){
		return $this->getSetting('order_id_schema');
	}

	public function isReceiptEmailActive(){
		return $this->getSetting('receipt_email') == 'true';
	}

	public function getWebhookSecret($isLive){
		return $this->getToggleableSetting(Customweb_I18n_Translation::__("Webhook Secret"), 'webhook_secret', $isLive);
	}

	public function getShopId(){
		return $this->getSetting('shop_id');
	}

	public function getWebhookHashMethod(){
		return 'SHA256';
	}

	private function getToggleableSetting($label, $key, $isLive, $language = null){
		if ($this->isLiveMode()) {
			$label = Customweb_I18n_Translation::__("@label (Live)", array(
				'@label' => $label 
			));
			$key .= '_live';
		}
		else {
			$label = Customweb_I18n_Translation::__("@label (Test)", array(
				'@label' => $label 
			));
			$key .= '_test';
		}
		return $this->getSetting($key, $label, $language);
	}

	/**
	 * Returns a trimmed configuration value.
	 * If the $label parameter is set, and the value is empty an exception is thrown.
	 *
	 * @param string $key
	 * @param string $label
	 * @param string $language
	 * @throws Customweb_Stripe_Exception_MissingConfigurationException
	 * @return string
	 */
	private function getSetting($key, $label = null, $language = null){
		$value = $this->getConfigurationAdapter()->getConfigurationValue($key, $language);
		$value = trim($value);
		if (empty($value) && !empty($label)) {
			throw new Customweb_Stripe_Exception_MissingConfigurationException($label);
		}
		return $value;
	}

	private function getConfigurationAdapter(){
		return $this->configurationAdapter;
	}
}
