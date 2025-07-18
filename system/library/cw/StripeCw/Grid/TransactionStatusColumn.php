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

require_once 'Customweb/Grid/Column.php';



class StripeCw_Grid_TransactionStatusColumn extends Customweb_Grid_Column {

	public function getContent($rowData) {
		switch($rowData['authorizationStatus']) {
			default:
			case 'pending':
				$class = 'label-info';
				break;
			case 'successful':
				$class = 'label-success';
				break;
			case 'failed': 
				$class = 'label-danger';
				break;
		}
		return '<span class="label ' . $class . '">' . $rowData['authorizationStatus'] . '</span>';
	}
	
}