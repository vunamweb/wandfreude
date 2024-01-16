<?php 
/* @var $transaction StripeCw_Entity_Transaction  */
/* @var $capture Customweb_Payment_Authorization_ITransactionCapture */
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

			<table class="table table-bordered table-hover">
				<tr>
					<td><?php echo StripeCw_Language::_('Capture ID') ?></td>
					<td><?php echo $capture->getCaptureId(); ?></td>
				</tr>

				<tr>
					<td><?php echo StripeCw_Language::_('Capture Date') ?></td>
					<td><?php echo $capture->getCaptureDate()->format(Customweb_Core_Util_System::getDefaultDateTimeFormat()); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Capture Amount') ?></td>
					<td><?php echo $capture->getAmount(); ?></td>
				</tr>
				<tr>
					<td><?php echo StripeCw_Language::_('Status') ?></td>
					<td><?php echo $capture->getStatus(); ?></td>
				</tr>
				<?php foreach ($capture->getCaptureLabels() as $label): ?>
				<tr>
					<td><?php echo $label['label'];?> 
					<?php if (isset($label['description'])): ?> 
						<span class="help">
						<?php echo $label['description']; ?>
						</span>
					<?php endif; ?>
					</td>
					<td><?php echo Customweb_Core_Util_Xml::escape($label['value']);?>
					</td>
				</tr>
				<?php endforeach;?>
			</table>
			
						
			<h2><?php echo StripeCw_Language::_('Captured Items'); ?></h2>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<td><?php echo StripeCw_Language::_('Name'); ?></td>
						<td><?php echo StripeCw_Language::_('SKU'); ?></td>
						<td><?php echo StripeCw_Language::_('Quantity'); ?></td>
						<td><?php echo StripeCw_Language::_('Tax Rate'); ?></td>
						<td><?php echo StripeCw_Language::_('Total Amount (excl. Tax)'); ?></td>
						<td><?php echo StripeCw_Language::_('Total Amount (incl. Tax)'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($capture->getCaptureItems() as $item):?>
						<tr>
							<td><?php echo $item->getName(); ?></td>
							<td><?php echo $item->getSku(); ?></td>
							<td><?php echo $item->getQuantity(); ?></td>
							<td><?php echo $item->getTaxRate(); ?></td>
							<td><?php echo $item->getAmountExcludingTax(); ?></td>
							<td><?php echo $item->getAmountIncludingTax(); ?></td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<br />
			</div>
		</div>
	</div>
</div>

<?php echo $footer; ?>