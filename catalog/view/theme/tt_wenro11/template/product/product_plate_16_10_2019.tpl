<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb hide">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-12'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <div class="row">
        <?php if ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-5'; ?>
        <?php } ?>
        <?php if ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-7'; ?>
        <?php } ?>
        <div class="col-2 col-sm-3">
		   <div id="height">
				<div>
				  <label style="text-tranform: upcase">Höhe IN MM</label>
				  <input id="height1" class="small" type="text" />
				  <input id="height2" class="small" type="text" />
				  <input id="height3" class="small" type="text" />
				</div>
			</div>
			<div class="number_plates">
			  <p>ANZAHL YOURPLATES</p>
			  <div class="plate_1 padding_choose_number">1</div>
			  <div class="plate_2 padding_choose_number">2</div>
			  <div class="plate_3 padding_choose_number">3</div>
			</div>

			<div class="w_radio clearfix">
                        <p>MATERIAL AUSWAHL</p>
						<?php foreach($material as $item) { ?>
						  <div class="custom-control custom-control-inline checkbox">
                          <input id="<?php echo $item['id'] ?>" type="radio" name="check">
                          <label class="check"></label>
						  <label class="name"><?php echo $item['name'] ?></label>
						  <a href="#" data-toggle="tooltip" data-html="true" title="<?php echo $item['answer'] ?>"><i class="fas fa-info-circle"></i></a>
						  </div>
						<?php } ?>
			</div>
		   <fieldset class="form-group hide">
				<div class="custom-control custom-radio">
				  <input class="custom-control-input" name="Ausrichgung" id="horizontal" value="horizontal" type="radio" checked="">
				  <label class="custom-control-label" for="horizontal">horizontal</label>
				</div>
				<div class="custom-control custom-radio">
				  <input class="custom-control-input" name="Ausrichgung" id="vertikal" value="vertikal" type="radio">
				  <label class="custom-control-label" for="vertikal">vertical</label>
				</div>
          </fieldset>
  <div class="form-group hide">
        <legend class="col-form-label"><h4>Number of plates</h4></legend>
        <select class="form-control" id="anzahlPlatten">
          <option value="1" selected="">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
        </select>
  </div>
		  <div class="form-group">
				<!--<label class="control-label" for="input-quantity"><?php echo $entry_qty; ?></label>
				<input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control" />
				<input type="button" id="minus" value="-" class="form-control" />
				<input type="button" id="plus" value="&#43;" class="form-control"/>
				<br /> !-->
				<button class="button hide" type="button" id="button-cartzz" data-loading-text="<?php echo $text_loading; ?>"><i class="fa fa-shopping-basket"></i><span><?php echo $button_cart; ?></span></button>
			 <input type="hidden" name="product_id" id="product_id" />
			 <input type="hidden" name="product_price" id="product_price" />
			</div>
		</div>
		<div class="col-1 col-sm-6 drawimage">
          <div style="float: left;position: relative;">
			  <div id="width">
				<div>
				  <label style="margin-right: 10px;">BREITE IN MM</label>
				  <input id="width1" class="small" type="text" />
				  <input id="width2" class="small" type="text" />
				  <input id="width3" class="small" type="text" />
				</div>
			  </div>
			  <div class="top-canvas">
			     <button type="button" value="AUSSCHNITT WÄHLEN" class="button-cut versal" data-toggle="modal" data-target="#myModal"><i class="fa fa-crop"></i> Ausschnitt wählen</button>
			     <button type="button" value="AUSSCHNITT  GESPIEGELT" class="change-direction versal" data-toggle="modal" data-target="#myModal"><i class="fa fa-crop"></i> Ausschnitt gespiegelt</button>
			     <input type="hidden" id="number_plate" value="<?php echo $_GET['number_plate'] ?>" />
			  </div>
			  <canvas id="canvas" width="300" height="300"></canvas>
          </div>
		</div>
		<div class="col-sm-3 col-3-price" >
		   <p>PREIS</p>
		   <p class="price"></p>
		   <p>inkl ges Mwst</p>
		   <button class="button button-cart-plates" type="button" id="button-cart" data-loading-text="Loading..."><span>In den Warenkorb</span></button>
		   <p>VERSAND</p>
		   <p>KOSTENLOSE LIEFERUNG</p>
		   <br>
		   <p>LIEFERZEIT</p>
		   <p>6-8 ARBEITSTAGE</p>
		</div>
        <canvas id="canvas_mirror" width="900" height="600"></canvas>
        <input type="hidden" id="img_mirror" value="<?php echo $_GET['product_id'] ?>" />
        <img id="source" src="image/<?php echo $img_url; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
         <!-- Modal -->
		<div id="myModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			  </div>
			  <div class="modal-body">
				<!--<img id="sourcez" src="image/<?php echo $img_url; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /> !-->
                <img id="sourcez" src="image/<?php echo $img_url; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
                <div id="draw-line"></div>
			  </div>
			  <div class="modal-footer">
                <button type="button" class="btn btn-default btn-save" data-dismiss="modal">OK</button>
              </div>
			</div>

		  </div>
		</div>
	  </div>
	  </div>
    <?php echo $column_right; ?>
	<?php if ($products) { ?>
      <div class="related-product-container quickview-added qv-wtext">
      <div class="module-title">
      <h2>
		<?php echo $text_related; ?>
	</h2>
	</div>
      <div>
      <div class="related-product">
	  <?php foreach ($products as $product) { ?>
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
      <?php } ?>
	  </div>
	  </div>
	  </div>
      <?php } ?>
	  <?php echo $content_bottom; ?>
	</div>
</div>
<script type="text/javascript"><!--
$('select[name=\'recurring_id\'], input[name="quantity"]').change(function(){
	$.ajax({
		url: 'index.php?route=product/product/getRecurringDescription',
		type: 'post',
		data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#recurring-description').html('');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();

			if (json['success']) {
				$('#recurring-description').html(json['success']);
			}
		}
	});
});
//--></script>
<script type="text/javascript"><!--
$('#button-cart').on('click', function() {
	var product_description = new Array();
	product_description[1] = {name:"<?php echo $heading_title ?>", meta_title:"<?php echo $heading_title ?>"};
	product_description[2] = {name:"<?php echo $heading_title ?>", meta_title:"<?php echo $heading_title ?>"};

	var product_store = [];
	product_store[0] = 0;

	var canvas = document.getElementById('canvas');
	var imagedata = canvas.toDataURL('image/png');
	var imgdata = imagedata.replace(/^data:image\/(png|jpg);base64,/, "");
	var filename = Math.random().toString(36).substr(2, 9) + '.png';

	$.ajax({
		url: 'index.php?route=product/product/add',
		type: 'post',
		data:{
			    imgdata:imgdata,
				product_description: product_description,
				model: 'model',
				tax_class_id: 0,
				quantity: 100,
				price: $('#product_price').val(),
				minimum: 1,
				subtract: 1,
				stock_status_id: 6,
				shipping: 1,
				date_available: '2019-08-24',
				length_class_id: 1,
				weight_class_id: 1,
				status: 1,
				type_product: 1,
				sort_order: 1,
				manufacturer_id: 0,
				product_store: product_store,
				filename: filename,
				image: 'uploads/'+filename+''
				//image: 'catalog/product/yzv4zrvut.png'

			},
		dataType: 'json',
		beforeSend: function() {
			//$('#button-cart').button('loading');
		},
		complete: function() {
			//$('#button-cart').button('reset');
		},
		success: function(json) {
			$('#product_id').val(json);
			$('#button-cartzz').click();

			//alert(json);
		},
        error: function(xhr, ajaxOptions, thrownError) {
            //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
});

$('#button-cartzz').on('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('input[name=\'product_id\'], #input-quantity, #product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-cart').button('loading');
		},
		complete: function() {
			$('#button-cart').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}

			if (json['success']) {
				$('body').before('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

				$('#cart-total').html(json['total']);

				$('html, body').animate({ scrollTop: 0 }, 'slow');

				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}
		},
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
});
//--></script>
<script src="catalog/view/javascript/rcrop.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/start.js" type="text/javascript"></script>

<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});

$('button[id^=\'button-upload\']').on('click', function() {
	var node = this;

	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script>
<script type="text/javascript"><!--
$('#review').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();

    $('#review').fadeOut('slow');

    $('#review').load(this.href);

    $('#review').fadeIn('slow');
});

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').on('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: $("#form-review").serialize(),
		beforeSend: function() {
			$('#button-review').button('loading');
		},
		complete: function() {
			$('#button-review').button('reset');
		},
		success: function(json) {
			$('.alert-success, .alert-danger').remove();

			if (json['error']) {
				$('#review').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}

			if (json['success']) {
				$('#review').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').prop('checked', false);
			}
		}
	});
});
var minimum = <?php echo $minimum; ?>;
  $("#input-quantity").change(function(){
    if ($(this).val() < minimum) {
      alert("Minimum Quantity: "+minimum);
      $("#input-quantity").val(minimum);
    }
  });
  // increase number of product
  function minus(minimum){
      var currentval = parseInt($("#input-quantity").val());
      $("#input-quantity").val(currentval-1);
      if($("#input-quantity").val() <= 0 || $("#input-quantity").val() < minimum){
          alert("Minimum Quantity: "+minimum);
          $("#input-quantity").val(minimum);
     }
  };
  // decrease of product
  function plus(){
      var currentval = parseInt($("#input-quantity").val());
     $("#input-quantity").val(currentval+1);
  };
  $('#minus').click(function(){
    minus(minimum);
  });
  $('#plus').click(function(){
    plus();
  });
  // zoom
	$(".thumbnails img").elevateZoom({
		zoomType : "window",
		cursor: "crosshair",
		gallery:'gallery_01',
		galleryActiveClass: "active",
		imageCrossfade: true,
		responsive: true,
		zoomWindowOffetx: 0,
		zoomWindowOffety: 0,
	});
	// slider
	$(".image-additional").owlCarousel({
		navigation: true,
		pagination: false,
		slideSpeed : 500,
		goToFirstSpeed : 1500,
		items : 4,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [1024,3],
		itemsTablet: [640,3],
		itemsMobile : [480,3],
		navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
		afterInit: function(){
			$('#gallery_01 .owl-wrapper .owl-item:first-child').addClass('active');
		},
		beforeInit: function(){
			$(".image-additional .thumbnail").show();
		}
	});
	$('#gallery_01 .owl-item .thumbnail').each(function(){
		$(this).click(function(){
			$('#gallery_01 .owl-item').removeClass('active');
			$(this).parent().addClass('active');
		});
	});
	// related products
	$(".related-product").owlCarousel({
		navigation: true,
		pagination: false,
		slideSpeed : 500,
		goToFirstSpeed : 1500,
		items : 4,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [991,2],
		itemsTablet: [700,2],
		itemsMobile : [480,1],
		navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
	});
//--></script>
<?php echo $footer; ?>
