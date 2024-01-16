<?php
/* @var $transaction StripeCw_Entity_Transaction  */

?>

<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				
				<?php if(is_object($transaction->getTransactionObject()) && $transaction->getTransactionObject()->isCapturePossible()): ?>
					<a href="<?php echo $capture; ?>" data-toggle="tooltip" class="btn btn-success" title="<?php echo StripeCw_Language::_('Capture Transaction'); ?>"><i class="fa fa-lock"></i></a>
				<?php endif; ?>
				
				
				<?php if(is_object($transaction->getTransactionObject()) && $transaction->getTransactionObject()->isRefundPossible()): ?>
					<a href="<?php echo $refund; ?>" data-toggle="tooltip" class="btn btn-danger" title="<?php echo StripeCw_Language::_('Refund Transaction'); ?>"><i class="fa fa-exchange"></i></a>
				<?php endif; ?>
				
				
				<?php if(is_object($transaction->getTransactionObject()) && $transaction->getTransactionObject()->isCancelPossible()): ?>
					<a href="<?php echo $cancel; ?>" data-toggle="tooltip" class="btn btn-danger" title="<?php echo StripeCw_Language::_('Cancel Transaction'); ?>"><i class="fa fa-trash"></i></a>
				<?php endif; ?>
				
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
		</div>
	</div>
	<div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger">
			<i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
   	<?php } else if($success) { ?>
     <div class="alert alert-success">
			<i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
    <?php }?>
    <div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
			</div>
			<div class="panel-body">
			<table class="table table-bordered table-hover">
				<tr>
					<td><?php echo StripeCw_Language::_('Authorization Status') ?></td>
					<td><?php echo $transaction->getAuthorizationStatus(); ?></td>
				</tr>

				<tr>
					<td><?php echo StripeCw_Language::_('Transaction ID') ?></td>
					<td><?php echo $transaction->getTransactionId(); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Transaction Number') ?></td>
					<td><?php echo $transaction->getTransactionExternalId(); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Order ID') ?></td>
					<td><?php echo $transaction->getOrderId(); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Created On') ?></td>
					<td><?php echo $transaction->getCreatedOn()->format(Customweb_Core_Util_System::getDefaultDateTimeFormat()); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Updated On') ?></td>
					<td><?php echo $transaction->getUpdatedOn()->format(Customweb_Core_Util_System::getDefaultDateTimeFormat()); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Customer ID') ?></td>
					<td><?php echo $transaction->getCustomerId(); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Payment ID') ?></td>
					<td><?php echo $transaction->getPaymentId(); ?></td>
				</tr>

				<?php if (is_object($transaction->getTransactionObject())):?>
				<?php foreach ($transaction->getTransactionObject()->getTransactionLabels() as $label): ?>
				<tr>
					<td><?php if (isset($label['description'])): ?>
						<span data-toggle="tooltip" data-container="#tab-general"  title="<?php echo $label['description']; ?>"><?php echo $label['label'];?></span>
						</span>
					<?php else: ?>
						<?php echo $label['label'];?>
					<?php endif; ?>
					</td>
					<td><?php echo Customweb_Core_Util_Xml::escape($label['value']);?>
					</td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>

				<?php if (is_object($transaction->getTransactionObject()) && $transaction->getTransactionObject()->isAuthorized() && $transaction->getTransactionObject()->getPaymentInformation() != null) : ?>
				<tr>
					<td><?php echo StripeCw_Language::_('Payment Information') ?></td>
					<td><?php echo Customweb_Core_Util_Html::toText($transaction->getTransactionObject()->getPaymentInformation()); ?></td>
				</tr>
				<?php endif; ?>
			</table>

			<?php if (is_object($transaction->getTransactionObject())): ?>
				<h2><?php echo StripeCw_Language::_('Customer Data'); ?></h2>
				<table class="table table-striped table-condensed table-hover table-bordered">
					<?php $context = $transaction->getTransactionObject()->getTransactionContext()->getOrderContext(); ?>
					<tr>
						<th class="col-lg-3"><?php echo StripeCw_Language::_('Customer ID') ?></th>
						<td><?php echo Customweb_Core_Util_Xml::escape($context->getCustomerId()); ?></td>
					</tr>
					<tr>
						<th class="col-lg-3"><?php echo StripeCw_Language::_('Billing Address') ?></th>
						<td>
							<?php echo Customweb_Core_Util_Xml::escape($context->getBillingFirstName() . ' ' . $context->getBillingLastName()); ?><br />
							<?php if ($context->getBillingCompanyName() !== null): ?>
								<?php echo Customweb_Core_Util_Xml::escape($context->getBillingCompanyName()); ?><br />
							<?php endif;?>
							<?php echo Customweb_Core_Util_Xml::escape($context->getBillingStreet()); ?><br />
							<?php echo Customweb_Core_Util_Xml::escape(strtoupper($context->getBillingCountryIsoCode()) . '-' . $context->getBillingPostCode() . ' ' . $context->getBillingCity()); ?><br />
							<?php if ($context->getBillingDateOfBirth() !== null) :?>
								<?php echo StripeCw_Language::_('Birthday') . ': ' . $context->getBillingDateOfBirth()->format("Y-m-d"); ?><br />
							<?php endif;?>
							<?php if ($context->getBillingPhoneNumber() !== null) :?>
								<?php echo StripeCw_Language::_('Phone') . ': ' . Customweb_Core_Util_Xml::escape($context->getBillingPhoneNumber()); ?><br />
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<th class="col-lg-3"><?php echo StripeCw_Language::_('Shipping Address') ?></th>
						<td>
							<?php echo Customweb_Core_Util_Xml::escape($context->getShippingFirstName() . ' ' . $context->getShippingLastName()); ?><br />
							<?php if ($context->getShippingCompanyName() !== null): ?>
								<?php echo Customweb_Core_Util_Xml::escape($context->getShippingCompanyName()); ?><br />
							<?php endif;?>
							<?php echo Customweb_Core_Util_Xml::escape($context->getShippingStreet()); ?><br />
							<?php echo Customweb_Core_Util_Xml::escape(strtoupper($context->getShippingCountryIsoCode()) . '-' . $context->getShippingPostCode() . ' ' . $context->getShippingCity()); ?><br />
							<?php if ($context->getShippingDateOfBirth() !== null) :?>
								<?php echo StripeCw_Language::_('Birthday') . ': ' . $context->getShippingDateOfBirth()->format("Y-m-d"); ?><br />
							<?php endif;?>
							<?php if ($context->getShippingPhoneNumber() !== null) :?>
								<?php echo StripeCw_Language::_('Phone') . ': ' . Customweb_Core_Util_Xml::escape($context->getShippingPhoneNumber()); ?><br />
							<?php endif;?>
						</td>
					</tr>
				</table>
				<br />
				<h2><?php echo StripeCw_Language::_('Products'); ?></h2>
				<table class="table table-striped table-condensed table-hover table-bordered">
					<thead>
						<tr>
							<th><?php echo StripeCw_Language::_('Name'); ?></th>
							<th><?php echo StripeCw_Language::_('SKU'); ?></th>
							<th><?php echo StripeCw_Language::_('Quantity'); ?></th>
							<th><?php echo StripeCw_Language::_('Type'); ?></th>
							<th><?php echo StripeCw_Language::_('Tax Rate'); ?></th>
							<th><?php echo StripeCw_Language::_('Amount (excl. VAT)'); ?></th>
							<th><?php echo StripeCw_Language::_('Amount (inkl. VAT)'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($transaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getInvoiceItems() as $invoiceItem):?>
						<tr>
							<td><?php echo $invoiceItem->getName() ?></td>
							<td><?php echo $invoiceItem->getSku(); ?></td>
							<td><?php echo $invoiceItem->getQuantity(); ?></td>
							<td><?php echo $invoiceItem->getType(); ?></td>
							<td><?php echo $invoiceItem->getTaxRate(); ?>%</td>
							<td><?php echo Customweb_Util_Currency::roundAmount($invoiceItem->getAmountExcludingTax(), $context->getCurrencyCode()) . ' ' . $context->getCurrencyCode(); ?></td>
							<td><?php echo Customweb_Util_Currency::roundAmount($invoiceItem->getAmountIncludingTax(), $context->getCurrencyCode()) . ' ' . $context->getCurrencyCode(); ?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<br />
			<?php endif;?>


			<?php if (is_object($transaction->getTransactionObject()) && count($transaction->getTransactionObject()->getCaptures()) > 0): ?>
			<h2><?php echo StripeCw_Language::_('Captures for this transaction'); ?></h2>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<td><?php echo StripeCw_Language::_('Date'); ?></td>
						<td><?php echo StripeCw_Language::_('Amount'); ?></td>
						<td><?php echo StripeCw_Language::_('Status'); ?></td>
						<td> </td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($transaction->getTransactionObject()->getCaptures() as $capture):?>
					<tr>
						<td><?php echo $capture->getCaptureDate()->format(Customweb_Core_Util_System::getDefaultDateTimeFormat()); ?></td>
						<td><?php echo $capture->getAmount(); ?></td>
						<td><?php echo $capture->getStatus(); ?></td>
						<td>
							<a class="btn btn-primary"
								data-toggle="tooltip"
								href="<?php echo $url->link('stripecw/transaction/view_capture', 'token=' . $token . '&transaction_id=' . $transaction->getTransactionId(). '&capture_id=' . $capture->getCaptureId(), 'SSL'); ?>"
								title="<?php echo StripeCw_Language::_('View'); ?>"
							>
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<br />
			<?php endif;?>


			<?php if (is_object($transaction->getTransactionObject()) && count($transaction->getTransactionObject()->getRefunds()) > 0): ?>
			<h2><?php echo StripeCw_Language::_('Refunds for this transaction'); ?></h2>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<td><?php echo StripeCw_Language::_('Date'); ?></td>
						<td><?php echo StripeCw_Language::_('Amount'); ?></td>
						<td><?php echo StripeCw_Language::_('Status'); ?></td>
						<td> </td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($transaction->getTransactionObject()->getRefunds() as $refund):?>
					<tr>
						<td><?php echo $refund->getRefundedDate()->format(Customweb_Core_Util_System::getDefaultDateTimeFormat()); ?></td>
						<td><?php echo $refund->getAmount(); ?></td>
						<td><?php echo $refund->getStatus(); ?></td>
						<td>
							<a class="btn btn-primary"
								data-toggle="tooltip"
								href="<?php echo $url->link('stripecw/transaction/view_refund', 'token=' . $token . '&transaction_id=' . $transaction->getTransactionId(). '&refund_id=' . $refund->getRefundId(), 'SSL'); ?>"
								title="<?php echo StripeCw_Language::_('View'); ?>"
							>
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<br />
			<?php endif;?>


			<?php if (is_object($transaction->getTransactionObject()) && count($transaction->getTransactionObject()->getHistoryItems()) > 0): ?>
			<h2><?php echo StripeCw_Language::_('Transactions History'); ?></h2>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<td><?php echo StripeCw_Language::_('Date'); ?></td>
						<td><?php echo StripeCw_Language::_('Action'); ?></td>
						<td><?php echo StripeCw_Language::_('Message'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($transaction->getTransactionObject()->getHistoryItems() as $item):?>
					<tr>
						<td><?php echo $item->getCreationDate()->format(Customweb_Core_Util_System::getDefaultDateTimeFormat()); ?></td>
						<td><?php echo $item->getActionPerformed(); ?></td>
						<td><?php echo $item->getMessage(); ?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<br />
			<?php endif;?>


			<?php if (count($relatedTransactions) > 0): ?>
			<h2><?php echo StripeCw_Language::_('Transactions related to the same order'); ?></h2>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<td><?php echo StripeCw_Language::_('Transaction Number'); ?></td>
						<td><?php echo StripeCw_Language::_('Is Authorized'); ?></td>
						<td><?php echo StripeCw_Language::_('Authorization Amount'); ?></td>
						<td></td>
					</tr>
				</thead>
				<?php foreach ($relatedTransactions as $transaction): ?>
					<?php if (is_object($transaction->getTransactionObject())) : ?>
					<tr>
						<td><?php echo $transaction->getTransactionExternalId(); ?></td>
						<td><?php echo $transaction->getTransactionObject()->isAuthorized() ? StripeCw_Language::_('yes') : StripeCw_Language::_('no'); ?></td>
						<td><?php echo $transaction->getTransactionObject()->getAuthorizationAmount(); ?></td>
						<td>
						<a class="btn btn-primary"
								data-toggle="tooltip"
								href="<?php echo $url->link('stripecw/transaction/view', 'token=' . $token . '&transaction_id=' . $transaction->getTransactionId(), 'SSL'); ?>"
								title="<?php echo StripeCw_Language::_('View'); ?>"
							>
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>
					<?php endif; ?>
				<?php endforeach;?>


			</table>
			<br />
			<?php endif; ?>

			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>