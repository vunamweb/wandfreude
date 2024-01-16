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

require_once 'Customweb/Stripe/Communication/Source/ResponseProcessor.php';


/**
 * Processes a source response.
 * Checks if it is reusable, and attempts to create an alias.
 * The process() function returns true if the source can be charged.
 *
 * @author sebastian
 *
 */
class Customweb_Stripe_Communication_Source_PushResponseProcessor extends Customweb_Stripe_Communication_Source_ResponseProcessor {
	protected function processError(Customweb_Payment_Authorization_ErrorMessage $error) {
		$this->getTransaction()->setAuthorizationFailed($error);
		return false;
	}
}