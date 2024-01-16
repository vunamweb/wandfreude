<div class="most-products-container">

<div class="module-title"><h2><?php echo $title; ?></h2></div>
 <?php
	$count = 0;
	$rows = $config_slide['f_rows'];
	if(!$rows) { $rows=1; }
  ?>
	<div>
		<div class="most-viewed-products-slider quickview-added qv-wtext">
			<?php if($products): ?>
		  <?php foreach ($products as $product) { ?>
    <?php  if($count % $rows == 0 ) { echo '<div class="row_items">'; } $count++; ?>
    <div class="product-layout product-grid">
			<div class="product-thumb layout1">
			  <div class="image">
				  <?php if($product['is_new'] && !$product['special']){ ?>
				  <div class="label-product">
					  <span><?php echo $text_new; ?></span>
				  </div>
				  <?php } if(($product['special'] && $product['is_new'])||($product['special'] && !$product['is_new'])) { ?>
				  <div class="label-product l-sale">
					  <span><?php echo round(($product['special_num']-$product['price_num'])/$product['price_num']*100)."%"; ?></span>
				  </div>
				  <?php }?>
				  <a href="<?php echo $product['href']; ?>">
				  <?php if ($product['thumb']) { ?>
					<img src="image/catalog/loading.gif" data-original="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive lazy" />
					<?php if($product['rotator_image']){ ?>
						<img class="img-r lazy" src="image/catalog/loading.gif" data-original="<?php echo $product['rotator_image']; ?>" alt="<?php echo $product['name']; ?>" />
					<?php } ?>
				  <?php } else { ?>
					<img src="image/cache/no_image-100x100.png" alt="<?php echo $product['name']; ?>" />
				  <?php } ?>
				  </a>
				  <div class="actions-link">
						<a class="btn-cart" data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-basket"></i><span class="button"><?php echo $button_cart; ?></span></a>
						<a class="btn-wishlist" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>"  onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart" aria-hidden="true"></i></a>
						<a class="btn-compare" data-toggle="tooltip" title="<?php echo $button_compare; ?>"  onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange" aria-hidden="true"></i></a>
					</div>
				  <?php if ($product['price']) { ?>
					<p class="price">
					  <?php if (!$product['special']) { ?>
					  <?php echo $product['price']; ?>
					  <?php } else { ?>
					  <span class="price-new"><?php echo $product['special']; ?></span>
					  <span class="price-old"><?php echo $product['price']; ?></span>
					  <?php } ?>
					</p>
					<?php } ?>
				</div>
				<div class="product-inner">
				  <div class="product-caption">
				  <?php if ($product['tags']) { ?>
					  <p class="tags-product">
						<?php for ($i = 0; $i < count($product['tags']); $i++) { ?>
						<?php if ($i < (count($product['tags']) - 1)) { ?>
						<a href="<?php echo $product['tags'][$i]['href']; ?>"><?php echo $product['tags'][$i]['tag']; ?></a>,
						<?php } else { ?>
						<a href="<?php echo $product['tags'][$i]['href']; ?>"><?php echo $product['tags'][$i]['tag']; ?></a>
						<?php } ?>
						<?php } ?>
					  </p>
				  <?php } ?>
				  <h2 class="product-name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h2>
					<p class="product-des"><?php echo $product['description']; ?></p>

				  <?php if (isset($product['rating'])) { ?>
					  <div class="ratings">
						  <div class="rating-box">
							  <?php for ($i = 0; $i <= 5; $i++) { ?>
								  <?php if ($product['rating'] == $i) {
									  $class_r= "rating".$i;
									  echo '<div class="'.$class_r.'">rating</div>';
								  }
							  }  ?>
						  </div>
					  </div>
				  <?php } ?>

				  <div class="product-intro">

					</div>
				  </div>
				</div>
			  </div>
			</div>
    <?php if($count % $rows == 0 ): ?>
    	</div>
     <?php else: ?>
    	<?php if($count == count($products)): ?>
    		</div>
    	<?php endif; ?>
     <?php endif; ?>
    <?php } ?>
			<?php else: ?>
				<p><?php echo $text_empty; ?></p>
			<?php endif; ?>
		</div>
	</div>
	</div>
<script type="text/javascript">
$(document).ready(function() {
  	$(".most-viewed-products-slider").owlCarousel({
	  autoPlay: <?php if($config_slide['autoplay']) { echo 'true' ;} else { echo 'false'; } ?>,
		items : <?php if($config_slide['items']) { echo $config_slide['items'] ;} else { echo 3; } ?>,
		slideSpeed : <?php if($config_slide['f_speed']) { echo $config_slide['f_speed']; } else { echo 200;} ?>,
		navigation : <?php if($config_slide['f_show_nextback']) { echo 'true' ;} else { echo 'false'; } ?>,
		paginationNumbers : true,
		pagination : <?php if($config_slide['f_show_ctr']) { echo 'true' ;} else { echo 'false';} ?>,
		stopOnHover : false,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [991,2],
		itemsTablet: [700,2],
		itemsMobile : [480,1],
		navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
  	});

});
</script>