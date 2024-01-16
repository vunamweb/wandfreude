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
?>

<?php echo $header; ?>

<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb): ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php endforeach; ?>
	</div>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
			</div>
		</div>
		<div class="content">
			<?php foreach ($templateUrls as $url):?>
				<a href="<?php echo $url; ?>" target="_blank"><?php echo $url?></a><br />
			<?php endforeach;?>
		</div>
	</div>
</div>

<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 

<?php echo $footer; ?>