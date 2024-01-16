<div id="instagram_block_home" class="block">
	<div class="container">
	<div class="title_block">
		<h2><?php echo $heading_title2; ?></h2>
		<h5><span><?php echo $text_des; ?></span><a href="https://www.instagram.com/<?php echo $username; ?>/" target="_blank"><?php echo $text_follow; ?><i class="fa fa-angle-double-right"></i></a></h5>		
	</div>
	</div>
	<div class="content_block">
		<?php foreach($instagrams as $instagram) {?>
			<a rel="gallery" class="fancybox" href="<?php echo $instagram['image']; ?>" style="display: block;"><img src="<?php echo $instagram['image'] ?>" alt="" /></a>
		<?php } ?>
	</div>	
	<h5><span><?php echo $text_copyright; ?></span></h5>
</div>
<?php if($config_slide['f_view_mode'] == 'slider'){ ?>
<script type="text/javascript"><!--			
	$("#instagram_block_home .content_block").owlCarousel({
		items : <?php echo $config_slide['items']; ?>,
        itemsDesktop : [1199, 5],
        itemsDesktopSmall : [991, 4],
        itemsTablet : [768, 4],
        itemsMobile : [480, 3],
		navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
	});
--></script>
<?php } ?>
<script type="text/javascript"><!--
	$('.content_block').magnificPopup({
		type:'image',
		delegate: 'a',
		gallery: {
			enabled:true
		}
	});
--></script>
