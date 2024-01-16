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



require_once 'StripeCw/Util.php';
require_once 'StripeCw/Adapter/AbstractAdapter.php';


/**
 * @author Thomas Hunziker
 * @Bean 
 */
class StripeCw_Adapter_HiddenAdapter extends StripeCw_Adapter_AbstractAdapter {
	
	/**
	 * @var StripeCw_Entity_Transaction
	 */
	private $transaction = null;
	private $visibleFormFields = array();
	private $formActionUrl = null;
	private $hiddenFields = array();

	public function getPaymentAdapterInterfaceName() {
		return 'Customweb_Payment_Authorization_Hidden_IAdapter';
	}
	
	/**
	 * @return Customweb_Payment_Authorization_Hidden_IAdapter
	 */
	public function getInterfaceAdapter() {
		return parent::getInterfaceAdapter();
	}
	
	protected function preparePaymentFormPane() {
		$this->transaction = $this->createNewTransaction();
		$this->visibleFormFields = $this->getInterfaceAdapter()->getVisibleFormFields(
			$this->getOrderContext(),
			$this->getAliasTransactionObject(),
			$this->getFailedTransactionObject(),
			$this->transaction->getTransactionObject()->getPaymentCustomerContext()
		);
		$this->formActionUrl = $this->getInterfaceAdapter()->getFormActionUrl($this->transaction->getTransactionObject());
		$this->hiddenFields = $this->getInterfaceAdapter()->getHiddenFormFields($this->transaction->getTransactionObject());
		StripeCw_Util::getEntityManager()->persist($this->transaction);
	}
	
	protected function getVisibleFormFields() {
		return $this->visibleFormFields;
	}
	
	protected function getFormActionUrl() {
		return $this->formActionUrl;
	}
	
	protected function getHiddenFormFields() {
		return $this->hiddenFields;
	}
		
}