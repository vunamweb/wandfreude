<div class="randomproduct-module">
<div class="text-center">
  <p class="konfiguration">KONFIGURATOR</p>
</div>
<!-- <div class="module-title"> -->
	<ul class="schritte_li">
		<li><span>Schritt 1:</span> Wählen Sie ein Motiv</li>
		<li><span>Schritt 2:</span> Wählen Sie die Anzahl an Rückwänden</li>
		<li><span>Schritt 3:</span> Material Auswahl</li>
		<li><span>Schritt 4:</span> Maße</li>
		<li><span>Schritt 5:</span> Ausschnitt wählen</li>
	</ul>
    <ul class="list_category">
        <?php foreach ($categories as $category) { 
		  //print_r($category); die();	
		?>
            <?php if($category['count'] > 0){ ?>
               <li><a class="tab <?php if($current_category == $category[category_id]) echo 'active' ?>" href="duschrueckwand/<?php echo $category[name_url] ?>/<?php echo $category[name_product_id] ?>/<?php echo $category[product_id] ?>/<?php echo $category[category_id] ?>/<?php echo $_GET['number_plate'] ?>"><?php echo $category[name] ?></a></li>
            <?php } ?>
        <?php } ?>
    </ul>
<!-- </div> -->
<div class="clearfix"></div>
<div class="row">
<div class="random-products-slider">
  <?php
	$count = 0;
	$rows = $config_slide['f_rows'];
	if(!$rows) { $rows=1; }
  ?>
  <?php if($products): ?>
    <?php foreach ($products as $product) { ?>
    <?php if($count++ % $rows == 0 ) { echo '<div class="row_items">'; }  ?>
    <div class="product-layout product-grid layout2">
							  <div class="product-thumb">
								  <div class="image">
								  <?php if($config_slide['f_show_label']): ?>
								  <?php if($product['is_new'] && !$product['special']){ ?>
								  <div class="label-product">
									  <span><?php echo $text_new; ?></span>
								  </div>
								  <?php } if(($product['special'] && $product['is_new'])||($product['special'] && !$product['is_new'])) { ?>
								  <div class="label-product l-sale">
									  <span><?php echo round(($product['special_num']-$product['price_num'])/$product['price_num']*100)."%"; ?></span>
								  </div>
								  <?php }?>
                                  <?php endif;?>
								  <?php if($product['category_id'] != ''){
								    $href= 'duschrueckwand' . '/' . $product[category_name] . '/' .$product[name] . '/' . $product[product_id] . '/' . $product[category_id] . '/' . $_GET[number_plate];
								  ?>
                                     <a href="<?php echo $href ?>">
								  <?php if ($product['thumb']) { ?>
									<img  src="mthumb.php?h=160&amp;w=250&amp;zc=1&amp;cc=efefef&amp;q=80&amp;src=image/<?php echo urlencode($product['url_image']); ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive lazy" />
									<?php if($product['rotator_image']){ ?><img class="img-r lazy"  src="<?php echo $product['rotator_image']; ?>" alt="<?php echo $product['name']; ?>" /><?php } ?>
								  <?php } else { ?>
									<img src="image/cache/no_image-100x100.png" alt="<?php echo $product['name']; ?>" />
								  <?php } ?>
								  </a>
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
								  <h2 class="product-name hide"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h2>
								  <?php if($config_slide['f_show_des']) { ?>
									<p class="product-des"><?php echo $product['description']; ?></p>
									<?php } ?>
									<?php if (isset($product['rating'])) { ?>
									  <div class="ratings hide">
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
								  <?php if($config_slide['f_show_price']) { ?>
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

								  <div class="product-intro">
										<div class="actions-link">
										<?php if($config_slide['f_show_addtocart']) { ?>
											<a data-toggle="tooltip" title="<?php echo $button_compare; ?>"  onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-random"></i><span><?php echo $button_compare; ?></span></a>
											<a data-toggle="tooltip" title="<?php echo $button_cart; ?>"  onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-basket"></i><span><?php echo $button_cart; ?></span></a>
											<a data-toggle="tooltip" title="<?php echo $button_wishlist; ?>"  onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart-o"></i><span><?php echo $button_wishlist; ?></span></a>
											<?php } ?>
										</div>
										<div class="actions-link2">
											<?php if($config_slide['f_show_addtocart']) { ?>
												<a data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-basket"></i><span><?php echo $button_cart; ?></span></a>
											<?php } ?>
											<a data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart-o"></i><span><?php echo $button_wishlist; ?></span></a>
											<a data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-signal"></i><span><?php echo $button_compare; ?></span></a>
										</div>
									</div>
								  </div>

								</div>
							  </div>
							</div>
     <?php if($count % $rows == 0 || $count == count($products)) { echo '</div>'; }  ?>
    <?php } ?>
  <?php else: ?>
    <p><?php echo $text_empty; ?></p>
  <?php endif; ?>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $(".random-products-slider").owlCarousel({
    autoPlay: <?php if($config_slide['autoplay']) { echo 'true' ;} else { echo 'false'; } ?>,
    items : <?php if($config_slide['items']) { echo $config_slide['items'] ;} else { echo 3; } ?>,
    slideSpeed : <?php if($config_slide['f_speed']) { echo $config_slide['f_speed']; } else { echo 200;} ?>,
    navigation : <?php if($config_slide['f_show_nextback']) { echo 'true' ;} else { echo 'false'; } ?>,
    paginationNumbers : true,
    pagination : <?php if($config_slide['f_show_ctr']) { echo 'true' ;} else { echo 'false';} ?>,
    stopOnHover : false,
	navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
	itemsDesktop : [1199,6],
	itemsDesktopSmall : [991,4],
	itemsTablet: [700,4],
	itemsMobile : [480,2],
  });
});
</script></div>