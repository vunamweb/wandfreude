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

require_once 'Customweb/Payment/IConfigurationAdapter.php';

require_once 'StripeCw/Util.php';
require_once 'StripeCw/SettingApi.php';
require_once 'StripeCw/OrderStatus.php';
require_once 'StripeCw/Store.php';


/**
 * 
 * @author Thomas Hunziker
 * @Bean
 */
class StripeCw_Configuration implements Customweb_Payment_IConfigurationAdapter{
	
	/**
	 * @var StripeCw_SettingApi
	 */
	private static $config = null;
	
	/**
	 * @return StripeCw_SettingApi
	 */
	private static function getSetting() {
		if (self::$config === null) {
			self::$config = new StripeCw_SettingApi('stripecw');
		}
		return self::$config;
	}
	
	public function getConfigurationValue($key, $languageCode = null) {
		return self::getSetting()->getValue($key, null, $languageCode);
	}
	
	public function existsConfiguration($key, $languageCode = null) {
		return self::getSetting()->isSettingPresent($key, null, $languageCode);
	}
	
	public function getLanguages($currentStore = false) {
		$langs = array();
		foreach (StripeCw_Util::getLanguages() as $language) {
			$langs[$language['code']] = $language['name'];
		}
		return $langs;
	}

	public function getStoreHierarchy() {
		$storeId = StripeCw_Store::getStoreId();
		$hierarchy = array(
			StripeCw_Store::DEFAULT_STORE_ID => StripeCw_Store::getStoreName(StripeCw_Store::DEFAULT_STORE_ID),
		);
		if ($storeId === StripeCw_Store::DEFAULT_STORE_ID) {
			return $hierarchy;
		}
		$hierarchy[$storeId] = StripeCw_Store::getStoreName($storeId);
		return $hierarchy;
	}

	public function useDefaultValue(Customweb_Form_IElement $element, array $formData) {
		$controlName = implode('_', $element->getControl()->getControlNameAsArray());
		return (isset($formData['default'][$controlName]) && $formData['default'][$controlName] == 'default');
	}

	public function getOrderStatus() {
		return StripeCw_OrderStatus::getOrderStatuses();
	}

	public static function getLoggingLevel(){
		return self::getSetting()->getValue('log_level');
		
	}
}