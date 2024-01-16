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

require_once 'Customweb/Util/Html.php';

require_once 'StripeCw/Form/FrontendRenderer.php';
require_once 'StripeCw/Language.php';
require_once 'StripeCw/Util.php';
require_once 'StripeCw/Entity/Transaction.php';
require_once 'StripeCw/Adapter/IAdapter.php';

abstract class StripeCw_Adapter_AbstractAdapter implements StripeCw_Adapter_IAdapter {
	private $interfaceAdapter;
	protected $registry;
	
	/**
	 *
	 * @var Customweb_Payment_Authorization_IOrderContext
	 */
	private $orderContext;
	
	/**
	 *
	 * @var StripeCw_PaymentMethod
	 */
	protected $paymentMethod;
	
	/**
	 *
	 * @var StripeCw_Entity_Transaction
	 */
	protected $failedTransaction = null;
	
	/**
	 *
	 * @var StripeCw_Entity_Transaction
	 */
	protected $aliasTransaction = null;
	protected $aliasTransactionId = null;

	public function setInterfaceAdapter(Customweb_Payment_Authorization_IAdapter $interface){
		$this->interfaceAdapter = $interface;
	}

	public function getInterfaceAdapter(){
		return $this->interfaceAdapter;
	}

	public function getCheckoutPageHtml(StripeCw_PaymentMethod $paymentMethod, Customweb_Payment_Authorization_IOrderContext $orderContext, $registry, $failedTransaction){
		$this->registry = $registry;
		$this->paymentMethod = $paymentMethod;
		$this->failedTransaction = $failedTransaction;
		$this->orderContext = $orderContext;
		
		$this->aliasTransaction = null;
		$this->aliasTransactionId = null;
		if (isset($_REQUEST['stripecw_alias']) && $_REQUEST['stripecw_alias'] != 'new') {
			$this->aliasTransaction = StripeCw_Entity_Transaction::loadById((int) $_REQUEST['stripecw_alias']);
			if ($this->aliasTransaction !== null) {
				$this->aliasTransactionId = $this->aliasTransaction->getTransactionId();
			}
		}
		else if(!isset($_REQUEST['stripecw_alias'])) {
			$aliasTransactions = StripeCw_Util::getAliasHandler()->getAliasTransactions($orderContext);
			if (count($aliasTransactions) > 0) {
				$this->aliasTransaction = array_shift($aliasTransactions);
				$this->aliasTransactionId = $this->aliasTransaction->getTransactionId();
			}
		}
		
		$output = '<div id="stripecw-checkout-page">';
		
		$output .= $this->getAliasDropDown();
		$output .= $this->getPaymentFormPane();
		
		$output .= '</div>';
		
		return $output;
	}

	protected function getAliasDropDown(){
		$orderContext = $this->getOrderContext();
		$handler = StripeCw_Util::getAliasHandler();
		
		if (!StripeCw_Util::isAliasManagerActive($orderContext)) {
			return '';
		}
		$aliasTransactions = $handler->getAliasTransactions($orderContext);
		if (count($aliasTransactions) <= 0) {
			return '';
		}
		
		$output = '<form class="form-horizontal stripecw-alias-manager-form"><div class="stripecw-alias-pane form-group"><label for="stripecw_alias" class="control-label col-sm-4">' .
				 StripeCw_Language::_("Use Stored Card") . '</label>';
		
		$output .= '<div class="col-sm-8 "><select name="stripecw_alias" id="stripecw_alias" class="form-control stripecw-alias-dropdown">';
		$output .= '<option value="new">' . StripeCw_Language::_("Use a new Card") . '</option>';
		foreach ($aliasTransactions as $transaction) {
			$output .= '<option ';
			if ($this->aliasTransactionId == $transaction->getTransactionId()) {
				$output .= 'selected="selected" ';
			}
			$output .= 'value="' . $transaction->getTransactionId() . '">' . $transaction->getAliasForDisplay() . '</option>';
		}
		
		$output .= '</select></div></div></form>';
		
		return $output;
	}

	protected function getOrderContext(){
		return $this->orderContext;
	}

	/**
	 *
	 * @return StripeCw_Entity_Transaction
	 */
	protected function createNewTransaction(){
		$orderContext = $this->getOrderContext();
		return $this->paymentMethod->newTransaction($this->getOrderContext(), $this->aliasTransactionId, $this->getFailedTransactionObject());
	}

	protected function getAliasTransactionObject(){
		$aliasTransactionObject = null;
		$orderContext = $this->getOrderContext();
		if (StripeCw_Util::isAliasManagerActive($orderContext)) {
			$aliasTransactionObject = 'new';
			if ($this->aliasTransaction !== null && $this->aliasTransaction->getCustomerId() == $orderContext->getCustomerId()) {
				$aliasTransactionObject = $this->aliasTransaction->getTransactionObject();
			}
		}
		
		return $aliasTransactionObject;
	}

	protected function getFailedTransactionObject(){
		$failedTransactionObject = null;
		$orderContext = $this->getOrderContext();
		if ($this->failedTransaction !== null && $this->failedTransaction->getCustomerId() == $orderContext->getCustomerId()) {
			$failedTransactionObject = $this->failedTransaction->getTransactionObject();
		}
		return $failedTransactionObject;
	}

	protected function getPaymentFormPane(){
		$this->preparePaymentFormPane();
		
		$output = '<div id="stripecw-checkout-form-pane">';
		
		$actionUrl = $this->getFormActionUrl();
		
		if ($actionUrl !== null && !empty($actionUrl)) {
			$output .= '<form action="' . $actionUrl . '" method="POST" class="stripecw-confirmation-form form-horizontal">';
		}
		
		$visibleFormFields = $this->getVisibleFormFields();
		if ($visibleFormFields !== null && count($visibleFormFields) > 0) {
			$renderer = new StripeCw_Form_FrontendRenderer();
			$renderer->setCssClassPrefix('stripecw-');
			$output .= $renderer->renderElements($visibleFormFields);
		}
		
		$hiddenFormFields = $this->getHiddenFormFields();
		if ($hiddenFormFields !== null && count($hiddenFormFields) > 0) {
			$output .= Customweb_Util_Html::buildHiddenInputFields($hiddenFormFields);
		}
		
		$output .= $this->getAdditionalFormHtml();
		
		$output .= $this->getOrderConfirmationButton();
		
		if ($actionUrl !== null && !empty($actionUrl)) {
			$output .= '</form>';
		}
		
		$output .= '</div>';
		
		return $output;
	}

	protected function getAdditionalFormHtml(){
		return '';
	}

	/**
	 * Method to load some data before the payment pane is rendered.
	 */
	protected function preparePaymentFormPane(){}

	protected function getVisibleFormFields(){
		return array();
	}

	protected function getFormActionUrl(){
		return null;
	}

	protected function getHiddenFormFields(){
		return array();
	}

	protected function getOrderConfirmationButton(){
		$confirmText = $this->paymentMethod->getPaymentMethodConfigurationValue('confirm_button_name', 
				StripeCw_Language::getCurrentLanguageCode());
		
		if ($confirmText === null || empty($confirmText)) {
			$confirmText = StripeCw_Language::_('button_confirm');
		}
		
		$backButton = '';
		if ($this->failedTransaction !== null) {
			$backUrl = StripeCw_Util::getUrl('checkout', '', array(), true, 'checkout');
			$backButton = '<div class="pull-left left">
			       		<a href="' .
					 $backUrl . '" id="back-button"  class="button btn btn-danger">' . StripeCw_Language::_('Cancel') . '</a>
			    </div>';
		}
		
		return '<div class="buttons stripecw-confirmation-buttons">
			    
				' . $backButton . '
				
				<div class="pull-right right">
			       		<input type="submit" value="' . $confirmText . '" id="button-confirm"  class="button btn btn-primary" />
			    </div>
			</div>';
	}
}