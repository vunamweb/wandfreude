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


require_once 'Customweb/Core/Stream/Input/File.php';
require_once 'Customweb/Util/Html.php';

require_once 'StripeCw/Language.php';
require_once 'StripeCw/Util.php';
require_once 'StripeCw/Exception/SettingNotFoundException.php';
require_once 'StripeCw/Database.php';
require_once 'StripeCw/OrderStatus.php';
require_once 'StripeCw/Store.php';


class StripeCw_SettingApi {
	
	/**
	 * @var StripeCw_Database
	 */
	private $database;
	private $space;
	
	private static $cache = array();
	
	private static $settingDefinitions = null;
	
	public function __construct($space) {
		$this->database = StripeCw_Database::getInstance();
		$this->space = strtolower($space);
	}
	
	public function getSpaceSettingDefintion() {
		$defintions = self::getSettingDefinitions();
		
		$space = strtolower($this->space);
		if (!isset($defintions[$space])) {
			throw new Exception("For the settings space '" . $space . "' there are no settings defintions.");
		}
		$spaceDefinitions = $defintions[$space];
		
		if ($this->space == 'stripecw') {
			return array_merge(self::getAdditionalBaseSettings(), $spaceDefinitions);
		}
		else {
			return array_merge(self::getAdditionalMethodSettings(), $spaceDefinitions);
		}
	}
	
	public function getSpace() {
		return $this->space;
	}
	
	public function setValue($key, $storeId, $value) {
		self::writeSetting($storeId, $this->space, $key, $value);
	}
	
	public function removeValue($key, $storeId) {
		self::removeSetting($storeId, $this->space, $key);
	}
	
	public function getValue($key, $storeId = null, $langId = null) {
		if ($storeId === null) {
			$storeId = StripeCw_Store::getStoreId();
		}
		$storeId = strval($storeId);
		
		$this->loadSettingsIntoCache($storeId);
		$key = strtolower($key);
		
		if (array_key_exists($key, self::$cache[$this->space][$storeId])) {
			$configValue = self::$cache[$this->space][$storeId][$key];
		}
		else {
			throw new Exception("Could not find setting value for key '" . $key . "' for store '" . $storeId . "' in space '" . $this->space . "'.");
		}
		
		if ($langId !== null) {
			$langId = (string)$langId;
			if (isset($configValue[$langId])) {
				return $configValue[$langId];
			}
			else {
				return reset($configValue);
			}
		}
		else {
			return $configValue;
		}
	}
	
	public function isSettingPresent($key, $storeId = null, $langId = null) {
		if ($storeId === null) {
			$storeId = StripeCw_Store::getStoreId();
		}
	
		$this->loadSettingsIntoCache($storeId);
	
		if (isset(self::$cache[$this->space][$storeId][$key])) {
			return true;
		}
		else {
			return false;
		}
	}
	
	private function loadSettingsIntoCache($storeId) {
		$storeId = strval($storeId);
		
		if (!isset(self::$cache[$this->space][$storeId])) {
				
			// Load first all default configurations:
			$defaultValues = array();
			if ($storeId !== StripeCw_Store::DEFAULT_STORE_ID && !isset(self::$cache[$this->space][StripeCw_Store::DEFAULT_STORE_ID])) {
				$defaultValues = $this->loadAllValuesPerStore(StripeCw_Store::DEFAULT_STORE_ID);
			}
			else if (isset(self::$cache[$this->space][StripeCw_Store::DEFAULT_STORE_ID])){
				$defaultValues = self::$cache[$this->space][StripeCw_Store::DEFAULT_STORE_ID];
			}
			$storeValues = $this->loadAllValuesPerStore($storeId);
			
			foreach ($this->getSpaceSettingDefintion() as $key => $definitions) {
				$key = strtolower($key);
				$type = strtolower($definitions['type']);
				if ($type == 'file') {
					$storeValue = null;
					if (isset($storeValues[$key]) && !empty($storeValues[$key])) {
						$path = StripeCw_Util::getFileUploadDir() . $storeValues[$key];
						if (file_exists($path)) {
							$storeValue = new Customweb_Core_Stream_Input_File($path);
						}
					}
					else if (isset($defaultValues[$key])) {
						$storeValue = $defaultValues[$key];
					}
					
					if ($storeValue === null) {
						if (isset($definitions['default'])) {
							try {
								$storeValue = StripeCw_Util::getAssetResolver()->resolveAssetStream($definitions['default']);
							} catch(Customweb_Asset_Exception_UnresolvableAssetException $e) {
							}
						}
					}
				}
				else if (isset($storeValues[$key])) {
					$storeValue = $storeValues[$key];
				}
				else if (isset($defaultValues[$key])) {
					$storeValue = $defaultValues[$key];
				}
				else {
					
					if ($type == 'multiselect' || $type == 'currencyselect') {
						$storeValue = explode(',', $definitions['default']);
					}
					else if ($type == 'multilangfield') {
						$storeValue = array();
						foreach(StripeCw_Util::getLanguages() as $language) {
							$storeValue[$language['language_id']] = $definitions['default'];
						}
					}
					else if ($type == 'orderstatusselect') {
						if ($definitions['default'] == 'authorized' || $definitions['default'] == 'uncertain' || $definitions['default'] == 'cancelled') {
							$storeValue = StripeCw_OrderStatus::getStatusIdByKey($definitions['default']);
						}
						else {
							$storeValue = $definitions['default'];
						}
					}
					else {
						$storeValue = $definitions['default'];
					}
				}
				self::$cache[$this->space][$storeId][$key] = $storeValue;
			}
		}
	}
	
	protected function loadAllValuesPerStore($storeId) {
		$prefix = $this->space . '_';
		$rs = $this->database->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$storeId . "' AND `key` LIKE '" . $this->database->escape(strtolower($prefix)) . "%'");
		$rows = $this->database->fetchAll($rs);
		$data = array();
		foreach ($rows as $result) {
			$key = str_replace($prefix, '', $result['key']);
			if (!$result['serialized']) {
				$data[$key] = $result['value'];
			} else {
				$data[$key] = unserialize($result['value']);
			}
		}
		
		return $data;
	}
	
	public static function removeSetting($storeId, $group, $key) {
		$db = StripeCw_Database::getInstance();
		$dbKey = strtolower($group . '_' . $key);
		$db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$storeId . "' AND `key` = '" . $db->escape($dbKey) . "'");
	}
	
	public static function writeSetting($storeId, $group, $key, $value) {
		
		$isSerialized = '0';
		if (is_array($value)) {
			
			foreach ($value as $c => $v) {
				$value[$c] = Customweb_Util_Html::unescapeXml($v);
			}
			
			$value = serialize($value);
			$isSerialized = '1';
		}
		else {
			$value = Customweb_Util_Html::unescapeXml($value);
		}
		$dbKey = strtolower($group . '_' . $key);
		
		$data = array(
			'key' => $dbKey,
			'store_id' => $storeId,
			'value' => $value,
			'serialized' => $isSerialized,
		);
		
		if (version_compare(VERSION, '2.3.0.0') >= 0) {
			// From Version 2.3.x on the group column is called code.
			$data['code'] = 'stripecw';
		}
		else {
			$data['group'] = 'stripecw';
		}
		
		$db = StripeCw_Database::getInstance();
		$rs = $db->query("SELECT setting_id FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$storeId . "' AND `key` = '" . $db->escape($dbKey) . "'");
		if ($rs === null) {
			StripeCw_Database::getInstance()->insert(DB_PREFIX . 'setting', $data);
		}
		else {
			$row = $db->fetch($rs);
			$db->update(DB_PREFIX . 'setting', $data, array('setting_id' => $row['setting_id']));
		}
	}
	
	public static function readSetting($storeId, $group, $key) {
		$db = StripeCw_Database::getInstance();
		$dbKey = strtolower($group . '_' . $key);
		$rs = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$storeId . "' AND `key` = '" . $db->escape($dbKey) . "'");
		
		if ($rs !== null) {
			$row = $db->fetch($rs);
			if ($row['serialized'] == '1') {
				return unserialize($row['value']);
			}
			else {
				return $row['value'];
			}
		}
		else {
			throw new StripeCw_Exception_SettingNotFoundException("Setting with key '" . $key . "' not found.");
		}
	}
	
	private static function getAdditionalMethodSettings() {
		$settings = array();
		
		$settings['status'] = array(
			'title' => StripeCw_Language::_('Status'),
			'type' => 'select',
			'description' => StripeCw_Language::_('Active payment methods can be used by customers to pay their orders.'),
			'options' => array(
				'1' => StripeCw_Language::_('Enabled'),
				'0' => StripeCw_Language::_('Disabled'),
			),
			'default' => 'disabled',
		);
		
		$settings['sort_order'] = array(
			'title' => StripeCw_Language::_('Sort Order'),
			'description' => StripeCw_Language::_("The sort order of the payment method."),
			'type' => 'textfield',
			'default' => '0',
		);
		
// 		$settings['show_logos'] = array(
// 			'title' => StripeCw_Language::_('Payment Logos'),
// 			'type' => 'select',
// 			'description' => StripeCw_Language::_("Should the payment logos be shown on the checkout page?"),
// 			'options' => array(
// 				'yes' => StripeCw_Language::_('Show payment logos on the checkout page'),
// 				'no' => StripeCw_Language::_('No'),
// 			),
// 			'default' => 'yes',
// 		);
		
		$zones = array();
		foreach (StripeCw_Util::getZones() as $zoneId => $zone) {
			$zones[$zoneId] = $zone['name'];
		}
		$settings['allowed_zones'] = array (
			'title' => StripeCw_Language::_('Allowed Zones'),
			'description' => StripeCw_Language::_('The payment method is available for the selected zones. If none is selected, the payment method is available for all zones.'),
			'type' => 'multiselect',
			'options' => $zones,
			'default' => '',
		);
		
		$settings['title'] = array(
			'title' => StripeCw_Language::_('Method Title'),
			'type' => 'multilangfield',
			'description' => StripeCw_Language::_("This controls the title which the user sees during checkout. If not set the default title is used."),
			'default' => '',
		);
		
		$settings['description'] = array(
			'title' => StripeCw_Language::_('Description'),
			'type' => 'multilangfield',
			'description' => StripeCw_Language::_('This controls the description which the user sees during checkout.'),
			'default' => '',
		);
		
		$settings['confirm_button_name'] = array(
			'title' => StripeCw_Language::_('Order Confirm Button'),
			'type' => 'multilangfield',
			'description' => StripeCw_Language::_("The customer may confirm the order on the checkout page. This field allows the modification of the displayed name. If the field is empty, the default value is used."),
			'default' => '',
		);
				
		$settings['min_total'] = array(
			'title' => StripeCw_Language::_('Minimal Order Total'),
			'type' => 'textfield',
			'description' => StripeCw_Language::_('Set here the minimal order total for which this payment method is available. If it is set to zero, it is always available.'),
			'default' => 0,
		);
		
		$settings['max_total'] = array(
			'title' => StripeCw_Language::_('Maximal Order Total'),
			'type' => 'textfield',
			'description' => StripeCw_Language::_('Set here the maximal order total for which this payment method is available. If it is set to zero, it is always available.'),
			'default' => 0,
		);
		
		return $settings;
	}
	
	private static function getAdditionalBaseSettings() {
		$settings = array();

		return $settings;
	}
	
	private static function getSettingDefinitions() {
		if (self::$settingDefinitions !== null) {
			return self::$settingDefinitions;
		}
	
		$definitions = array(
			'stripecw' => array(
				'operation_mode' => array(
					'title' => StripeCw_Language::_("Operation Mode"),
 					'description' => StripeCw_Language::_("The operation mode controls whether the live or the test credentials are used"),
 					'type' => 'SELECT',
 					'options' => array(
						'live' => StripeCw_Language::_("Live Mode"),
 						'test' => StripeCw_Language::_("Test Mode"),
 					),
 					'default' => 'test',
 				),
 				'secret_key_test' => array(
					'title' => StripeCw_Language::_("Secret Key Test"),
 					'description' => StripeCw_Language::_("The test secret key for the test environment is provided byStripe You can find it under Your Account API Keys This key is used when you set the operation mode to test"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'publishable_key_test' => array(
					'title' => StripeCw_Language::_("Publishable Key Test"),
 					'description' => StripeCw_Language::_("The publishable key for the test environment is provided by Stripe You can find it under Your Account API Keys This key is used when you set the operation mode to test"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'secret_key_live' => array(
					'title' => StripeCw_Language::_("Secret Key Live"),
 					'description' => StripeCw_Language::_("The test secret key for the live environment is provided by Stripe You can find it under Your Account API Keys"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'publishable_key_live' => array(
					'title' => StripeCw_Language::_("Publishable Key Live"),
 					'description' => StripeCw_Language::_("The publishable key for the live environment is provided by Stripe You can find it under Your Account API Keys"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'order_id_schema' => array(
					'title' => StripeCw_Language::_("Order Prefix"),
 					'description' => StripeCw_Language::_("Here you can insert an order prefix The prefix allows you to change the order number that is transmitted to Authorizenet The prefix must contain the tag id It will then be replaced by the order number eg name_id"),
 					'type' => 'TEXTFIELD',
 					'default' => '{id}',
 				),
 				'receipt_email' => array(
					'title' => StripeCw_Language::_("Receipt Email"),
 					'description' => StripeCw_Language::_("If this setting is active Stripe will send email receipt to the customer"),
 					'type' => 'SELECT',
 					'options' => array(
						'true' => StripeCw_Language::_("Active"),
 						'false' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'true',
 				),
 				'shop_id' => array(
					'title' => StripeCw_Language::_("Shop ID"),
 					'description' => StripeCw_Language::_("Here you can define a Shop ID This is only necessary if you wish to operate several shops with one account"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'webhook_secret_live' => array(
					'title' => StripeCw_Language::_("Webhook Secret Live"),
 					'description' => StripeCw_Language::_("Here you can define your webhook secret for live transactions which can be found in the Stripe dashboard under your webhook configuration"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'webhook_secret_test' => array(
					'title' => StripeCw_Language::_("Webhook Secret Test"),
 					'description' => StripeCw_Language::_("Here you can define your webhook secret for test transactions which can be found in the Stripe dashboard under your webhook configuration"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'waiting_page_text' => array(
					'title' => StripeCw_Language::_("Waiting Page Text"),
 					'description' => StripeCw_Language::_("Here you can define the text which is displayed to the customer while we are still processing their order"),
 					'type' => 'MULTILANGFIELD',
 					'default' => 'Dear customer, it appears to us that your payment was
				successful, but we are still waiting for confirmation. You can wait
				on this page and we will redirect you after we received the
				confirmation. Or you can close this window and we will send out an
				order confirmation email.',
 				),
 				'log_level' => array(
					'title' => StripeCw_Language::_("Log Level"),
 					'description' => StripeCw_Language::_("Messages of this or a higher level will be logged"),
 					'type' => 'SELECT',
 					'options' => array(
						'error' => StripeCw_Language::_("Error"),
 						'info' => StripeCw_Language::_("Info"),
 						'debug' => StripeCw_Language::_("Debug"),
 					),
 					'default' => 'error',
 				),
 			),
 			'stripecw_creditcard' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'credit_card_brands' => array(
					'title' => StripeCw_Language::_("Credit Card Brands"),
 					'description' => StripeCw_Language::_("The brand of the credit card is detected by the card number if hidden authorization is used If the payment page is used the user has to select the brand The allowed credit card brands can be restricted by this setting"),
 					'type' => 'MULTISELECT',
 					'options' => array(
						'visa' => StripeCw_Language::_("VISA"),
 						'mastercard' => StripeCw_Language::_("MasterCard"),
 						'amex' => StripeCw_Language::_("American Express"),
 						'discover' => StripeCw_Language::_("Discovercard"),
 						'diners' => StripeCw_Language::_("Diners Club"),
 						'jcb' => StripeCw_Language::_("JCB"),
 					),
 					'default' => 'visa,mastercard,amex,discover,diners,jcb',
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_visa' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_mastercard' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_americanexpress' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_diners' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_jcb' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_discovercard' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_ideal' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'EUR',
 					),
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 			'stripecw_sofortueberweisung' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'EUR',
 					),
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 			'stripecw_giropay' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'EUR',
 					),
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 			'stripecw_directdebits' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'EUR',
 					),
 				),
 				'merchant_name' => array(
					'title' => StripeCw_Language::_("Merchant Name"),
 					'description' => StripeCw_Language::_("The name of the merchant which should be displayed on the information text"),
 					'type' => 'TEXTFIELD',
 					'default' => '',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 				'alias_manager' => array(
					'title' => StripeCw_Language::_("Alias Manager"),
 					'description' => StripeCw_Language::_("The alias manager allows the customer to select from a credit card previously stored The sensitive data is stored by Stripe"),
 					'type' => 'SELECT',
 					'options' => array(
						'active' => StripeCw_Language::_("Active"),
 						'inactive' => StripeCw_Language::_("Inactive"),
 					),
 					'default' => 'inactive',
 				),
 			),
 			'stripecw_bcmc' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'EUR',
 					),
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 			'stripecw_przelewy24' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'EUR',
 						1 => 'PLN',
 					),
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 			'stripecw_alipay' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AUD',
 						1 => 'CAD',
 						2 => 'EUR',
 						3 => 'GBP',
 						4 => 'HKD',
 						5 => 'JPY',
 						6 => 'NZD',
 						7 => 'SGD',
 						8 => 'USD',
 					),
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 			'stripecw_btc' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'USD',
 					),
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 			'stripecw_stripepaymentrequest' => array(
				'active_currencies' => array(
					'title' => StripeCw_Language::_("Allowed Currencies"),
 					'description' => StripeCw_Language::_("This payment method is only active for the selected currencies If none is selected the method is active for all currencies"),
 					'type' => 'currencyselect',
 					'default' => '',
 					'allowedCurrencies' => array(
						0 => 'AED',
 						1 => 'AFN',
 						2 => 'ALL',
 						3 => 'AMD',
 						4 => 'ANG',
 						5 => 'AOA',
 						6 => 'ARS',
 						7 => 'AUD',
 						8 => 'AWG',
 						9 => 'AZN',
 						10 => 'BAM',
 						11 => 'BBD',
 						12 => 'BDT',
 						13 => 'BGN',
 						14 => 'BHD',
 						15 => 'BIF',
 						16 => 'BMD',
 						17 => 'BND',
 						18 => 'BOB',
 						19 => 'BOV',
 						20 => 'BRL',
 						21 => 'BSD',
 						22 => 'BTN',
 						23 => 'BWP',
 						24 => 'BYR',
 						25 => 'BZD',
 						26 => 'CAD',
 						27 => 'CDF',
 						28 => 'CHE',
 						29 => 'CHF',
 						30 => 'CHW',
 						31 => 'CLF',
 						32 => 'CLP',
 						33 => 'CNY',
 						34 => 'COP',
 						35 => 'COU',
 						36 => 'CRC',
 						37 => 'CUC',
 						38 => 'CUP',
 						39 => 'CVE',
 						40 => 'CZK',
 						41 => 'DJF',
 						42 => 'DKK',
 						43 => 'DOP',
 						44 => 'DZD',
 						45 => 'EGP',
 						46 => 'ERN',
 						47 => 'ETB',
 						48 => 'EUR',
 						49 => 'FJD',
 						50 => 'FKP',
 						51 => 'GBP',
 						52 => 'GEL',
 						53 => 'GHS',
 						54 => 'GIP',
 						55 => 'GMD',
 						56 => 'GNF',
 						57 => 'GTQ',
 						58 => 'GYD',
 						59 => 'HKD',
 						60 => 'HNL',
 						61 => 'HRK',
 						62 => 'HTG',
 						63 => 'HUF',
 						64 => 'IDR',
 						65 => 'ILS',
 						66 => 'INR',
 						67 => 'IQD',
 						68 => 'IRR',
 						69 => 'ISK',
 						70 => 'JMD',
 						71 => 'JOD',
 						72 => 'JPY',
 						73 => 'KES',
 						74 => 'KGS',
 						75 => 'KHR',
 						76 => 'KMF',
 						77 => 'KPW',
 						78 => 'KRW',
 						79 => 'KWD',
 						80 => 'KYD',
 						81 => 'KZT',
 						82 => 'LAK',
 						83 => 'LBP',
 						84 => 'LKR',
 						85 => 'LRD',
 						86 => 'LSL',
 						87 => 'LTL',
 						88 => 'LVL',
 						89 => 'LYD',
 						90 => 'MAD',
 						91 => 'MDL',
 						92 => 'MGA',
 						93 => 'MKD',
 						94 => 'MMK',
 						95 => 'MNT',
 						96 => 'MOP',
 						97 => 'MRO',
 						98 => 'MUR',
 						99 => 'MVR',
 						100 => 'MWK',
 						101 => 'MXN',
 						102 => 'MXV',
 						103 => 'MYR',
 						104 => 'MZN',
 						105 => 'NAD',
 						106 => 'NGN',
 						107 => 'NIO',
 						108 => 'NOK',
 						109 => 'NPR',
 						110 => 'NZD',
 						111 => 'OMR',
 						112 => 'PAB',
 						113 => 'PEN',
 						114 => 'PGK',
 						115 => 'PHP',
 						116 => 'PKR',
 						117 => 'PLN',
 						118 => 'PYG',
 						119 => 'QAR',
 						120 => 'RON',
 						121 => 'RSD',
 						122 => 'RUB',
 						123 => 'RWF',
 						124 => 'SAR',
 						125 => 'SBD',
 						126 => 'SCR',
 						127 => 'SDG',
 						128 => 'SEK',
 						129 => 'SGD',
 						130 => 'SHP',
 						131 => 'SLL',
 						132 => 'SOS',
 						133 => 'SRD',
 						134 => 'SSP',
 						135 => 'STD',
 						136 => 'SYP',
 						137 => 'SZL',
 						138 => 'THB',
 						139 => 'TJS',
 						140 => 'TMT',
 						141 => 'TND',
 						142 => 'TOP',
 						143 => 'TRY',
 						144 => 'TTD',
 						145 => 'TWD',
 						146 => 'TZS',
 						147 => 'UAH',
 						148 => 'UGX',
 						149 => 'USD',
 						150 => 'USN',
 						151 => 'USS',
 						152 => 'UYI',
 						153 => 'UYU',
 						154 => 'UZS',
 						155 => 'VEF',
 						156 => 'VND',
 						157 => 'VUV',
 						158 => 'WST',
 						159 => 'XAF',
 						160 => 'XBA',
 						161 => 'XBB',
 						162 => 'XBC',
 						163 => 'XBD',
 						164 => 'XCD',
 						165 => 'XDR',
 						166 => 'XFU',
 						167 => 'XOF',
 						168 => 'XPF',
 						169 => 'YER',
 						170 => 'ZAR',
 						171 => 'ZMW',
 					),
 				),
 				'capturing' => array(
					'title' => StripeCw_Language::_("Capturing"),
 					'description' => StripeCw_Language::_("By setting the capturing the reservation can be captured directly after the order or later manually over the backend of the store"),
 					'type' => 'SELECT',
 					'options' => array(
						'direct' => StripeCw_Language::_("Direct Capture"),
 						'deferred' => StripeCw_Language::_("Deferred Capture"),
 					),
 					'default' => 'direct',
 				),
 				'status_authorized' => array(
					'title' => StripeCw_Language::_("Authorized Status"),
 					'description' => StripeCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'authorized',
 				),
 				'status_uncertain' => array(
					'title' => StripeCw_Language::_("Uncertain Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for new orders that have an uncertain authorisation status"),
 					'type' => 'ORDERSTATUSSELECT',
 					'default' => 'uncertain',
 				),
 				'status_cancelled' => array(
					'title' => StripeCw_Language::_("Cancelled Status"),
 					'description' => StripeCw_Language::_("You can specify the order status when an order is cancelled"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'cancelled',
 				),
 				'status_captured' => array(
					'title' => StripeCw_Language::_("Captured Status"),
 					'description' => StripeCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 					'type' => 'ORDERSTATUSSELECT',
 					'options' => array(
						'no_status_change' => StripeCw_Language::_("Dont change order status"),
 					),
 					'default' => 'no_status_change',
 				),
 				'authorizationMethod' => array(
					'title' => StripeCw_Language::_("Authorization Method"),
 					'description' => StripeCw_Language::_("Select the authorization method to use for processing this payment method"),
 					'type' => 'SELECT',
 					'options' => array(
						'AjaxAuthorization' => StripeCw_Language::_("Ajax Authorization"),
 					),
 					'default' => 'AjaxAuthorization',
 				),
 			),
 		);
	
		self::$settingDefinitions = $definitions;
		return self::$settingDefinitions;
	}
	
}