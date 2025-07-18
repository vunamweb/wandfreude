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

require_once 'Customweb/Payment/Endpoint/Dispatcher.php';
require_once 'Customweb/Core/Http/Response.php';

require_once 'StripeCw/Util.php';
require_once 'StripeCw/HttpRequest.php';
require_once 'StripeCw/AbstractController.php';


require_once 'Customweb/Licensing/StripeCw/License.php';
Customweb_Licensing_StripeCw_License::run('13urfitl9ca4benb');