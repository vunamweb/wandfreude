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

require_once 'Customweb/Form/Element.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Method/Default.php';
require_once 'Customweb/Form/Control/Select.php';



/**
 *
 * @author Sebastian Bossert
 * @Method(paymentMethods={'sofortueberweisung'})
 */
class Customweb_Stripe_Method_Sofort extends Customweb_Stripe_Method_Default {

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		$fields = parent::getVisibleFormFields($orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext);
		$fields[] = $this->getBankField();
		return $fields;
	}

	private function getBankField(){
		$options = array(
			'DE' => Customweb_I18n_Translation::__("Germany"),
			'AT' => Customweb_I18n_Translation::__("Austria"),
			'BE' => Customweb_I18n_Translation::__("Belgium"),
			'IT' => Customweb_I18n_Translation::__("Italy"),
			'NL' => Customweb_I18n_Translation::__("Netherlands"),
			'ES' => Customweb_I18n_Translation::__("Spain") 
		);
		$control = new Customweb_Form_Control_Select('stripeBankCountry', $options);
		return new Customweb_Form_Element(Customweb_I18n_Translation::__("Sofort Bank Country"), $control, 
				Customweb_I18n_Translation::__("Please select the country in which your bank account is active."));
	}

	protected function getSourceCreationData(Customweb_Stripe_Authorization_Transaction $transaction){
		$data = parent::getSourceCreationData($transaction);
		$data['sofort'] = array(
			'country' => 'CW_PLACEHOLDER_COUNTRY' 
		);
		return $data;
	}

	protected function getSourceCreationJson(Customweb_Stripe_Authorization_Transaction $transaction){
		$json = json_encode($this->getSourceCreationData($transaction));
		return str_replace('"CW_PLACEHOLDER_COUNTRY"', 'formFieldValues.stripeBankCountry', $json);
	}
}