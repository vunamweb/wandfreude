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

require_once DIR_SYSTEM . '/library/cw/StripeCw/init.php';


require_once 'StripeCw/Language.php';
require_once 'StripeCw/IPaymentMethodDefinition.php';
require_once 'StripeCw/Template.php';
require_once 'StripeCw/PaymentMethod.php';
require_once 'StripeCw/AbstractController.php';


abstract class ControllerPaymentStripeCwAbstract extends StripeCw_AbstractController implements StripeCw_IPaymentMethodDefinition
{
	
	public function index()
	{
		
		// Translations:
		$this->load->model('checkout/order');
		$orderId = $this->session->data['order_id'];
		if (!empty($orderId)) {
			$order_info = $this->model_checkout_order->getOrder($orderId);
			
			$failedTransaction = null;
			$paymentMethod = new StripeCw_PaymentMethod($this);
			$orderContext = $paymentMethod->newOrderContext($order_info, $this->registry);
			$adapter = $paymentMethod->getPaymentAdapterByOrderContext($orderContext);
			
			$data = $adapter->getCheckoutPageHtml($paymentMethod, $orderContext, $this->registry, $failedTransaction);
			
			require_once 'Customweb/Licensing/StripeCw/License.php';
Customweb_Licensing_StripeCw_License::run('9dq9i0ilhqpu7q3i');
			$vars = array();
			$vars['checkout_form'] = $data;
			return $this->renderView(StripeCw_Template::resolveTemplatePath(StripeCw_Template::PAYMENT_FORM_TEMPLATE), $vars);
		}
		else {
			return 'The order ID is not set in the session. This happens when the order could not be 
					created in the database. A common cause is a not completely executed OpenCart database schema migration.';
		}
	}
	
}

