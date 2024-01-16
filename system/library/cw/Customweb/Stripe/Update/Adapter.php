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

require_once 'Customweb/Stripe/Authorization/Transaction.php';
require_once 'Customweb/Core/Exception/CastException.php';
require_once 'Customweb/Payment/Update/IAdapter.php';
require_once 'Customweb/Stripe/AbstractAdapter.php';
require_once 'Customweb/Stripe/Communication/Source/PushAdapter.php';


/**
 * @Bean
 */
class Customweb_Stripe_Update_Adapter extends Customweb_Stripe_AbstractAdapter implements Customweb_Payment_Update_IAdapter
{
	public function updateTransaction(Customweb_Payment_Authorization_ITransaction $transaction) {
		if (!($transaction instanceof Customweb_Stripe_Authorization_Transaction)) {
			throw new Customweb_Core_Exception_CastException('Customweb_Stripe_Authorization_Transaction');
		}
		$pushAdapter = new Customweb_Stripe_Communication_Source_PushAdapter($transaction, $this->getContainer());
		$pushAdapter->process();
		$transaction->setUpdateExecutionDate(null);
	}
}