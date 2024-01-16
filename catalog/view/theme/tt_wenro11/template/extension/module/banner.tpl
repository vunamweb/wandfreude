<div class="banner-module owl-style2">
<div class="container">
		<div id="banner<?php echo $module; ?>">
		  <?php foreach ($banners as $banner) { ?>
		  <div class="item">
			<?php if ($banner['link']) { ?>
			<a href="<?php echo $banner['link']; ?>">
			   <img  src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
			   <p class="title_banner"><?php echo html_entity_decode($banner['title']); ?></p>
			</a>
			<?php } else { ?>
			<img  src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
			<?php } ?>
		  </div>
		  <?php } ?>
		</div>
</div>
</div>
