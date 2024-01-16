<div id="instagram_block_home" class="block">
	<div class="title_block">
		<h2><?php echo $heading_title; ?></h2>
	</div>
	<div class="content_block">
		<?php foreach($instagrams as $instagram) {?>
			<a rel="gallery" class="fancybox" href="<?php echo $instagram['image']; ?>" style="display: block;"><img src="<?php echo $instagram['image'] ?>" alt="" /></a>
		<?php } ?>
	</div>
	<a href="https://www.instagram.com/<?php echo $user_id?>/" target="_blank">Follow our instagram</a>
</div>
<?php if($config_slide['f_status_carousel']){ ?>
<script type="text/javascript"><!--			
	$("#instagram_block_home .content_block").owlCarousel({
		items : <?php echo $config_slide['items']; ?>,
        itemsDesktop : [1199, 5],
        itemsDesktopSmall : [979, 4],
        itemsTablet : [768, 2],
        itemsMobile : [479, 1],
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
