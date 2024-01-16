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

require_once 'Customweb/Stripe/Authorization/Transaction.php';
require_once 'Customweb/Stripe/Exception/MissingConfigurationException.php';
require_once 'Customweb/Form/Element.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/Stripe/Method/Default.php';
require_once 'Customweb/Form/Control/Html.php';
require_once 'Customweb/Stripe/Method/CreditCard/AliasValidator.php';
require_once 'Customweb/Form/ElementFactory.php';



/**
 *
 * @author Sebastian Bossert
 * @Method(paymentMethods={'directdebits'})
 */
class Customweb_Stripe_Method_DirectDebits extends Customweb_Stripe_Method_Default {

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext){
		$fields = parent::getVisibleFormFields($orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext);
		//TODO fix alias
// 		if($aliasTransaction instanceof Customweb_Stripe_Authorization_Transaction) {
// 			$control = new Customweb_Form_Control_Html('stripe-alias-display', $aliasTransaction->getAliasForDisplay());
// 			$control->addValidator(new Customweb_Stripe_Method_CreditCard_AliasValidator($control, $this->getPaymentMethodName()));
// 			$element = new Customweb_Form_Element(Customweb_I18n_Translation::__('Selected card:'), $control);
// 			$element->appendJavaScript($this->getAliasSourceJavascript($aliasTransaction));
// 			$fields[] = $element;
// 		}else{
			$fields[] = Customweb_Form_ElementFactory::getIbanNumberElement('stripeIban');
// 		}
		$fields[] = $this->getMandateElement();
		return $fields;
	}

	public function extractAliasForDisplay(array $json){
		return Customweb_I18n_Translation::__('Account ending in @last4', array(
			'@last4' => $json['sepa_debit']['last4'] 
		));
	}

	public function processPaymentInformation(Customweb_Stripe_Authorization_Transaction $transaction, array $json){
// 		$transaction->setIban($json[])
		$transaction->setMandateReference($json['sepa_debit']['mandate_reference']);
		$transaction->setMandateUrl($json['sepa_debit']['mandate_url']);
	}

	protected function getSourceCreationData(Customweb_Stripe_Authorization_Transaction $transaction){
		$data = parent::getSourceCreationData($transaction);
		unset($data['redirection']);
		$data['sepa_debit'] = array(
			'iban' => 'CW_PLACEHOLDER_IBAN' 
		);
		return $data;
	}

	protected function getSourceCreationJson(Customweb_Stripe_Authorization_Transaction $transaction){
		$json = json_encode($this->getSourceCreationData($transaction));
		return str_replace('"CW_PLACEHOLDER_IBAN"', 'formFieldValues.stripeIban', $json);
	}

	private function getMandateElement(){
		$control = new Customweb_Form_Control_Html('stripeMandate', 
				Customweb_I18n_Translation::__(
						"By providing your IBAN and confirming this payment, you are authorizing @descriptor and Stripe, our payment service provider, to send instructions to your bank to debit your account and your bank to debit your account in accordance with those instructions. You are entitled to a refund from your bank under the terms and conditions of your agreement with your bank. A refund must be claimed within 8 weeks starting from the date on which your account was debited.", 
						array(
							"@descriptor" => $this->getDescriptor() 
						)));
		return new Customweb_Form_Element(Customweb_I18n_Translation::__("Mandate"), $control);
	}

	private function getDescriptor(){
		$descriptor = trim($this->getPaymentMethodConfigurationValue('merchant_name'));
		if (empty($descriptor)) {
			throw new Customweb_Stripe_Exception_MissingConfigurationException(Customweb_I18n_Translation::__("Merchant Name"));
		}
		return $descriptor;
	}
}