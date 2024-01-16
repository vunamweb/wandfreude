<?php 
/* @var $transaction StripeCw_Entity_Transaction  */
/* @var $item Customweb_Payment_Authorization_IInvoiceItem */

?>


<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $back; ?>" data-toggle="tooltip" class="btn btn-default" title="<?php echo StripeCw_Language::_('Back to Transaction'); ?>"><i class="fa fa-reply"></i></a>
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

			<?php if ($transaction->getTransactionObject()->isPartialRefundPossible()):?>
				<h2><?php echo StripeCw_Language::_('Partial Refund'); ?></h2>
				<p><?php echo StripeCw_Language::_('With the following form you can perform a partial refund.'); ?></p>
				<form action="<?php $refundConfirmUrl; ?>" method="POST" class="stripecw-line-item-grid" id="refund-form">
				
					<input type="hidden" id="stripecw-decimal-places" value="<?php echo Customweb_Util_Currency::getDecimalPlaces($transaction->getTransactionObject()->getCurrencyCode()); ?>" />
					<input type="hidden" id="stripecw-currency-code" value="<?php echo strtoupper($transaction->getTransactionObject()->getCurrencyCode()); ?>" />
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="left"><?php echo StripeCw_Language::_('Name'); ?></td>
								<td class="left"><?php echo StripeCw_Language::_('SKU'); ?></td>
								<td class="left"><?php echo StripeCw_Language::_('Type'); ?></td>
								<td class="left"><?php echo StripeCw_Language::_('Tax Rate'); ?></td>
								<td style="text-align: right;"><?php echo StripeCw_Language::_('Quantity'); ?></td>
								<td style="text-align: right;"><?php echo StripeCw_Language::_('Total Amount (excl. Tax)'); ?></td>
								<td style="text-align: right;"><?php echo StripeCw_Language::_('Total Amount (incl. Tax)'); ?></td>
								</tr>
						</thead>
					
						<tbody>
						<?php foreach ($transaction->getTransactionObject()->getNonRefundedLineItems() as $index => $item):?>
							<?php 
								$amountExcludingTax = Customweb_Util_Currency::formatAmount($item->getAmountExcludingTax(), $transaction->getTransactionObject()->getCurrencyCode());
								$amountIncludingTax = Customweb_Util_Currency::formatAmount($item->getAmountIncludingTax(), $transaction->getTransactionObject()->getCurrencyCode());
								if ($item->getType() == Customweb_Payment_Authorization_IInvoiceItem::TYPE_DISCOUNT) {
									$amountExcludingTax = $amountExcludingTax * -1;
									$amountIncludingTax = $amountIncludingTax * -1;
								}
							?>
							
							<tr id="line-item-row-<?php echo $index ?>" class="line-item-row" data-line-item-index="<?php echo $index; ?>" >
								<td class="left"><?php echo $item->getName(); ?></td>
								<td class="left"><?php echo $item->getSku();?></td>
								<td class="left"><?php echo $item->getType(); ?></td>
								<td class="left"><?php echo $item->getTaxRate();?> %<input type="hidden" class="tax-rate" value="<?php echo $item->getTaxRate(); ?>" /></td>
								<td style="text-align: right;"><input type="text" class="line-item-quantity form-control" name="quantity[<?php echo $index;?>]" value="<?php echo $item->getQuantity(); ?>" /></td>
								<td style="text-align: right;"><input type="text" class="line-item-price-excluding form-control" name="price_excluding[<?php echo $index;?>]" value="<?php echo $amountExcludingTax; ?>" /></td>
								<td style="text-align: right;"><input type="text" class="line-item-price-including form-control" name="price_including[<?php echo $index;?>]" value="<?php echo $amountIncludingTax; ?>" /></td>
							</tr>
						<?php endforeach;?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="6" style="text-align: right;"><?php echo StripeCw_Language::_('Total Refund Amount'); ?>:</td>
								<td id="line-item-total" style="text-align: right;">
								<?php echo Customweb_Util_Currency::formatAmount($transaction->getTransactionObject()->getRefundableAmount(), $transaction->getTransactionObject()->getCurrencyCode()); ?> 
								<?php echo strtoupper($transaction->getTransactionObject()->getCurrencyCode());?>
							</tr>
						</tfoot>
					</table>
					<?php if ($transaction->getTransactionObject()->isRefundClosable()):?>
						<div class="closable-box">
							<label for="close-transaction">
								<input id="close-transaction" type="checkbox" name="close" value="on" />
								<?php echo StripeCw_Language::_('Close transaction for further refunds'); ?></label>
							
						</div>
					<?php endif;?>
					
					<div style="text-align: right;">
						<input type="submit" class="btn btn-success" value="<?php echo StripeCw_Language::_('Refund'); ?>" />
					</div>
				</form>
			<?php endif;?>
			</div>
		</div>
	</div>
</div>

<?php echo $footer; ?>