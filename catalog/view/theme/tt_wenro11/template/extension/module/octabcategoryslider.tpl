<?php $tab_effect = 'wiggle'; ?>
<script type="text/javascript">
$(document).ready(function() {
	$(".<?php echo $cateogry_alias;?> .tab_content_category").hide();
	$(".<?php echo $cateogry_alias;?> .tab_content_category:first").show();
    $(".<?php echo $cateogry_alias;?> ul.tabs-categorys li:first").addClass("active");
	$(".<?php echo $cateogry_alias;?> ul.tabs-categorys li").click(function() {
		$(".<?php echo $cateogry_alias;?> ul.tabs-categorys li").removeClass("active");
		$(this).addClass("active");
		$(".<?php echo $cateogry_alias;?> .tab_content_category").hide();
		$(".<?php echo $cateogry_alias;?> .tab_content_category").removeClass("animate1 <?php echo $tab_effect;?>");
		var activeTab = $(this).attr("rel");
		$("#"+activeTab) .addClass("animate1 <?php echo $tab_effect;?>");
		$("#"+activeTab).fadeIn();
	});
});
</script>
<?php
	$row = $config_slide['f_rows'];
	if(!$row) {$row=1;}
?>
<div class="product-tabs-category-container-slider quickview-added qv-wtext <?php echo $cateogry_alias;?>">
		<div class="module-title mt1">
			<h1>Online Shop<?php // echo $title;?></h1>
		</div>
        <ul class="tabs-categorys">
    	<?php $count=0; ?>
    	<?php foreach($category_products as $cate_id => $products ){ ?>
    		<?php if (isset($array_cates[$cate_id]['name'])) {?>
    		<li rel="tab_cate<?php echo $cate_id; ?>"  >
				<?php  echo $array_cates[$cate_id]['name']; ?>
				<?php if($array_cates[$cate_id]['thumbnail_image']): ?>
					<img class="thumb-img" src="<?php echo $array_cates[$cate_id]['thumbnail_image']; ?>" alt="<?php echo $array_cates[$cate_id]['name']; ?>" />
				<?php endif; ?>
    		</li>
			<?php } ?>
    			<?php $count= $count+1; ?>
    	<?php } ?>
    	</ul>
		<div class="tab_container_category">
		<?php foreach($category_products as $cate_id => $products ){ ?>
			<div id="tab_cate<?php echo $cate_id; ?>" class="tab_content_category">
				<div class="productTabContent owl-demo-tabcate">
				<?php if($products): ?>
				<?php $count = 0; ?>
				<?php foreach ($products as $product){ ?>
					<?php if($count++ % $row ==0){  echo  "<div class='row_items'>"; } ?>
						<div class="product-layout product-grid">
						<div class="product-thumb layout1">
							<div class="image">
								<?php if($config_slide['tab_cate_show_label']): ?>
								<?php if ($product['is_new']):
									if($product['special']): ?>
										<div class="label-pro-sale"><?php echo $text_sale; ?></div>
									<?php else: ?>
										<div class="label-pro-new"><?php echo $text_new; ?></div>
									<?php endif; ?>
								<?php else: ?>
									<?php if($product['special']): ?>
										<div class="label-pro-sale"><?php echo $text_sale; ?></div>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
								<?php if ($product['thumb']) { ?>
									<a class="product-image" href="<?php echo $product['href']; ?>">
										<?php if($product['rotator_image']): ?>
											<img class="img-r lazy" src="image/catalog/loading.gif" data-original="<?php echo $product['rotator_image']; ?>" alt="<?php echo $product['name']; ?>" />
										<?php endif; ?>
										<img class="lazy" src="image/catalog/loading.gif" data-original="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
									</a>
								<?php } else { ?>
									<img src="image/cache/no_image-100x100.png" alt="<?php echo $product['name']; ?>" />
								<?php } ?>
								<div class="actions-link">
									<?php if($config_slide['tab_cate_show_addtocart']) { ?>
										<a class="btn-cart" data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>');"><span><?php echo $button_cart; ?></span></a>
									<?php } ?>
									<a class="btn-wishlist" title="<?php echo $button_wishlist; ?>" data-toggle="tooltip" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><span><?php echo $button_wishlist; ?></span></a>
									<a class="btn-compare" title="<?php echo $button_compare; ?>" data-toggle="tooltip" onclick="compare.add('<?php echo $product['product_id']; ?>');"><span><?php echo $button_compare; ?></span></a>
								</div>
								<?php if($config_slide['tab_cate_show_price']) { ?>
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
									  <h2 class="product-name">
										<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
									</h2>
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
										<!--<div class="review-count"><a href="<?php // echo $product['href']; ?>"><?php // echo $product['reviews']; ?></a></div>-->
									</div>
								<?php } ?>
								 </div>
							 </div>
						</div>
						</div>
					<?php if($count % $row == 0 ): ?>
					</div><!-- row_items-->
				 <?php else: ?>
					<?php if($count == count($products)): ?>
						</div><!-- row_items-->
					<?php endif; ?>
				 <?php endif; ?>
				<?php } ?>
				<?php else: ?>
					<p><?php echo $text_empty; ?></p>
				<?php endif; ?>
				</div><!-- productTabContent -->
			</div>
		<?php } ?>
	 </div> <!-- .tab_container_category -->
</div><!-- <?php echo $cateogry_alias;?> -->
<script type="text/javascript">
$(document).ready(function() {
  $(".<?php echo $cateogry_alias;?> .owl-demo-tabcate").owlCarousel({
	autoPlay: <?php if($config_slide['tab_cate_autoplay']) { echo 'true' ;} else { echo 'false';} ?>,
	items : <?php if($config_slide['items']) { echo $config_slide['items'] ;} else { echo 3;} ?>,
	slideSpeed : <?php if($config_slide['tab_cate_speed_slide']) { echo $config_slide['tab_cate_speed_slide'] ;} else { echo 200;} ?>,
	navigation : <?php if($config_slide['tab_cate_show_nextback']) { echo 'true' ;} else { echo 'false';} ?>,
	paginationNumbers : true,
	pagination : <?php if($config_slide['tab_cate_show_ctr']) { echo 'true' ;} else { echo 'false';} ?>,
	stopOnHover : false,
	itemsDesktop : [1400,4],
	itemsDesktopSmall : [991,2],
	itemsTablet: [700,2],
	itemsMobile : [480,1],
	navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
  });
});
</script>