<?php 

/* @var $form Customweb_Payment_BackendOperation_IForm */

?>

<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right input-group">
				<a href="<?php echo $back; ?>" data-toggle="tooltip" class="btn btn-default" title="<?php echo StripeCw_Language::_('Back'); ?>"><i class="fa fa-reply"></i></a>
				
				<?php if (count($stores) > 1) :?>
					<form method="POST"  class="col-sm-8">
						<select name="storeId"  class="form-control " onchange="this.form.submit()">
						<?php foreach($stores as $storeId => $storeName): ?>
							<?php 
								echo '<option value="' . $storeId . '" ';
								if ($storeId === $current_store_id) { 
									echo 'selected="selected"'; 
								}
								echo '>';
								echo $storeName;
								echo '</option>';
							?>
						<?php endforeach;?>
						</select>
					</form>
				<?php endif;?>
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
			<div class="panel-body backend-form">
				<?php if (count($forms) > 0):?>
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<td class="left"><?php echo StripeCw_Language::_('Item'); ?></td>
							<td style="align: right">Action</td>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($forms as $form): ?>
						<tr>
				            <td class="left"><?php echo $form->getTitle(); ?></td>
				            <td style="align: right">
				            	<a class="btn btn-primary" 
				            	href="<?php echo $url->link( $module_base_path . '/form_view', 'token=' . $token . '&form=' . $form->getMachineName(), 'SSL') ?>" 
				            	title="<?php echo StripeCw_Language::_('View'); ?>"><i class="fa fa-eye"></i></a>
				            </td>
				          </tr>
					<?php endforeach;?>
				</tbody>
				</table>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>

<?php echo $footer; ?>