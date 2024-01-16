<?php 
if($check_top_footer)
  echo $top_footer; 
?>

<footer>
  <div class="container">
    <div class="row">
    
		<div class="col-12 col-md-4">
			<?php if(isset($block2)){ echo $block2; }?>
		</div>

		<div class="col-12 col-md-4">
		  <?php if(isset($block3)){ echo $block3; }?>
		</div>

		<div class="col-12 col-md-4">
			<?php if(isset($block4)){ echo $block4; }?>
		</div>

		<div class="col-12 mt4">
		   <hr>
		   <img src="image/payments3.png" alt="Wandfreude individuelle Rückwände" class="text-center payments">
		</div>
	</div>
  </div>


        <?php if ($facebook_messenger_enabled_FAE == 'true') { ?>
        <!-- Facebook JSSDK -->
        <script>
          window.fbAsyncInit = function() {
            FB.init({
              appId            : '',
              autoLogAppEvents : true,
              xfbml            : true,
              version          : '<?= $facebook_jssdk_version_FAE ?>'
            });
          };

          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "https://connect.facebook.net/en_US/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, 'script', 'facebook-jssdk'));
        </script>
        <div
          class="fb-customerchat"
          attribution="fbe_opencart"
          page_id="<?= $facebook_page_id_FAE ?>"
        />
        <?php } ?>
      
</footer>
<div class="bottom-footer">
	<div class="container">
		<div class="row">
			<div class="follow-footer col-sm-3"></div>

		</div>
	</div>
</div>
<div id="back-top"><i class="fa fa-angle-up"></i></div>
<script type="text/javascript">
$(document).ready(function(){
	// hide #back-top first
	$("#back-top").hide();
	// fade in #back-top
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 300) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});
		// scroll body to 0px on click
		$('#back-top').click(function () {
			$('body,html').animate({scrollTop: 0}, 800);
			return false;
		});
	});
});

$(window).load(function(){
  $(".twentytwenty-container[data-orientation!='vertical']").twentytwenty({default_offset_pct: 0.5});
  $(".twentytwenty-container[data-orientation='vertical']").twentytwenty({default_offset_pct: 0.3, orientation: 'vertical'});
});

$(".sendform").on("click", function(event) {
	var data = $("#kontaktf").serializeArray();
	data = JSON.stringify(data);
	var pflicht=1;

	pr=$("#datenschutz").prop("checked"); if(pr==false) pflicht=0;
	//pr = $("#eintrag").val(); if(pr=="") pflicht=0;
	pr = $("#mail").val(); if(pr=="") pflicht=0;

	if(pflicht==1) {
		event.preventDefault();
	    request = $.ajax({
	        url: "/include/hi.php",
	        type: "post",
	        datatype:'json',
	        success: function(msg) {
                sec = msg;
                console.log(sec);

			    request = $.ajax({
			        url: "/include/sendmail.php",
			        type: "post",
			        datatype:'json',
			        data: 'mystring='+sec+'&data='+data,
			        success: function(msg) {
//		                 console.log(msg);
		                 if(msg == "Mail sent") $('#kontaktformular').html("<h1>Vielen Dank für Ihre Nachricht.</h1><p>Wir melden uns umgehend bei Ihnen.</p>");
		                 else if(msg == "Captcha") $('#kontaktformular').html("Die Anfrage wurde nicht gesendet. Es gab einen Fehler.");
		                 else $('#kontaktformular').html("Die Anfrage wurde nicht gesendet. Es gab einen Fehler: "+msg+"");
		            }
			    });
            }
	    });
	} else console.log("not sent");
});

</script>

<?php
	if (!isset($_COOKIE["disclaimer21"])){
	// print_r($_COOKIE);
		$wl = "Datenschutzerklärung";
		$ac = "Notwendige zustimmen";
		$ab = "Ablehnen";
		$past = time() - 3600;
		$_COOKIE["marketing"]='';
    	setcookie( "marketing", '', $past, '/' );
?>

    <div id="cookie_disclaimer" class="show">
        <p><b>AUCH WIR VERWENDEN COOKIES:</b></p>
		<table class="mb1">
	 		<tr>
				 <td><h4 class="mb-0">Notwendige Cookies</h4>
				 	sind erforderlich, um alle Funktionen dieser Website bereitzustellen und strandardmäßig aktiviert
				 </td>
				<td valign="bottom"><input type="checkbox" id="komfort" checked="" disabled></td>
			</tr>
			<tr>
				<td><h4 class="mt-2 mb-0">Marketing-Cookies</h4>
					und ähnliche Technologien würden wir gerne verwenden, um die Präferenzen unserer Besucher besser zu verstehen und auch auf anderen Plattformen personalisierte Informationen bereitstellen zu können.
				</td>
				<td valign="bottom"><input type="checkbox" id="marketing" <?php echo $track ? 'checked' : ''; ?>></td>
			</tr>
		</table>
			<p>Details erfahren Sie in der <a href="/datenschutz">Datenschutzerklärung</a>.</p>
        <a href="javascript:void(0)" id="acceptall" class="btn btn-info btn-small">Alle akzeptieren</a> 
		<a href="javascript:void(0)" id="cookie_stop" class="btn btn-info btn-small acc"><?php echo $ac; ?></a>

    </div>

	<style>
	/********COOKIES*******/
	#cookie_disclaimer{
	    position: fixed;
	    bottom: 10px;
	    z-index: 9999999;
	    width: 90%;
		max-width: 600px;
	    background: #fff;
	    left:15px;
	    background: #fff;
	    color: #666;padding: 10px; line-height: 1.5em; -webkit-box-shadow: 0px 6px 22px 0px rgba(0,0,0,0.75); -moz-box-shadow: 0px 6px 22px 0px rgba(0,0,0,0.75); box-shadow: 0px 6px 22px 0px rgba(0,0,0,0.75);  }
	#cookie_disclaimer a {  }
	#cookie_disclaimer h4 { font-size: 1em; }
	#cookie_disclaimer p, #cookie_disclaimer td { font-weight: 200; font-size: .9em; vertical-align: bottom; }
	.btn.acc { background: transparent; color: #c70039 !important; border: solid 1px #c70039; }
	.cookie{float: right;
	    padding: 5px 35px;
	    background: #009FE3;
	    color: #fff !important; text-decoration: none !important;
	    border-radius: 10px; margin-right: 50px; margin-top: 20px; text-decoration: none; }
	.nocookie{float: right;
	    padding: 5px 35px;
	    color: #999 !important; text-decoration: none !important;
	    border-radius: 10px; margin-right: 50px; margin-top: 20px; text-decoration: none; }
		.acc { margin-left: 30px; background: transparent; color: #987f68; border: none; }
		.btn-small { padding: 6px; }
	@media (max-width: 1200px) {
	}
	@media (max-width: 990px) {
	}
	@media (max-width: 540px) {
		#cookie_disclaimer{
	    	width: 90%;
		}
		#cookie_disclaimer{ bottom: 20px; width: 100%; left:0; padding: 20px ;  line-height: 1.15em;  }
		#cookie_disclaimer .inner { padding: 20px !important; }
	}		
	/********END*******/
	</style>
	<!-- Cookies -->
	<script type="text/javascript">
	$(function(){
	     $('#acceptall').click(function(){
	        $('#cookie_disclaimer').slideUp("slow");

	        var nDays = 60;
	        var aDays = 720;
	        var cookieValue = "true";
	        var today = new Date();
	        var expire = new Date();
	        var expireDel = new Date();
	        expire.setTime(today.getTime() + 3600000*24*nDays);
	        expireDel.setTime(today.getTime() - 3600000*24*100);

	        var cookieName = "disclaimer21";
			document.cookie = cookieName+"="+escape(cookieValue)+";expires="+expire.toGMTString()+";path=/";
	        var cookieName = "marketing";
			expire.setTime(today.getTime() + 3600000*24*aDays);
			document.cookie = cookieName+"="+escape(cookieValue)+";expires="+expire.toGMTString()+";path=/";

			location.reload();
	     });
	     $('#cookie_stop').click(function(){
		    var komfort =$('#komfort').prop('checked');
			var marketing =$('#marketing').prop('checked');

	        $('#cookie_disclaimer').slideUp("slow");

	        var nDays = 60;
	        var aDays = 720;
	        var cookieValue = "true";
	        var today = new Date();
	        var expire = new Date();
	        var expireDel = new Date();
	        expire.setTime(today.getTime() + 3600000*24*nDays);
	        expireDel.setTime(today.getTime() - 3600000*24*100);

		        var cookieName = "disclaimer21";
				document.cookie = cookieName+"="+escape(cookieValue)+";expires="+expire.toGMTString()+";path=/";

			if(marketing==true) {
		        var cookieName = "marketing";
				expire.setTime(today.getTime() + 3600000*24*aDays);
				document.cookie = cookieName+"="+escape(cookieValue)+";expires="+expire.toGMTString()+";path=/";
				window.location.reload();
			}
			else if(marketing==false) {
		        var cookieName = "marketing";
				document.cookie = cookieName+"='';expires="+expireDel.toGMTString()+";path=/";
			}
	     });
	});

	</script>
	<!-- END COOKIES-->
<?php 
}

/*
	$past = time() - 3600;
	foreach ( $_COOKIE as $key => $value ) {
	    setcookie( $key, $value, $past, '/' );
	}
*/

if (isset($_COOKIE["marketingXXXX"]) && isset($_COOKIE["disclaimer21"])) { ?>

<script>
var gaProperty = 'UA-197568101-1';
var disableStr = 'ga-disable-' + gaProperty;
if (document.cookie.indexOf(disableStr + '=true') > -1) {
	window[disableStr] = true;
}
function gaOptout() {
	document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
	window[disableStr] = true;
	alert('Das Tracking durch Google Analytics wurde in Ihrem Browser für diese Website deaktiviert.');
}
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-197568101-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-197568101-1', { 'anonymize_ip' : true } );
</script>

<?php }?>


<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//-->

<!-- Theme created by Welford Media for OpenCart 2.0 www.welfordmedia.co.uk -->
</div>

<?php if (true) : ?>	
	<?php if ($collectorders) : ?>
		<div id="trustedShopsCheckout" style="display: none;">
			<span id="tsCheckoutOrderNr"><?php echo $collectorders['order_id']; ?></span>
			<span id="tsCheckoutBuyerEmail"><?php echo $collectorders['email']; ?></span>
			<span id="tsCheckoutOrderAmount"><?php echo $collectorders['total']; ?></span>
			<span id="tsCheckoutOrderCurrency"><?php echo $collectorders['currency_code']; ?></span>
			<span id="tsCheckoutOrderPaymentType"><?php echo $collectorders['payment_method']; ?></span>
			<span id="tsCheckoutOrderEstDeliveryDate"><?php echo $collectorders['day_checkout']; ?></span>
			<?php if ($collectReviews) : ?>
				<!-- product reviews start -->
				<!-- for each product in the basket full set of data is required -->
				<?php foreach ($collectorders['order_products'] as $product) : ?>
					<span class="tsCheckoutProductItem">
						<span class="tsCheckoutProductUrl"><?php echo $product['href']; ?></span>
						<span class="tsCheckoutProductImageUrl"><?php echo $product['image']; ?></span>
						<span class="tsCheckoutProductName"><?php echo $product['name']; ?></span>
						
						<span class="tsCheckoutProductSKU"><?php echo $product['model'] ?></span>
						<span class="tsCheckoutProductGTIN"><?php echo $product['ean'] ?></span>
						<span class="tsCheckoutProductMPN"><?php echo $product['mpn'] ?></span>						
						<span class="tsCheckoutProductBrand"><?php echo $product['brand'] ?></span>
					</span>
				<?php endforeach; ?>
				<!-- product reviews end -->
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?> 	


<?php if ($trustedshops_info_status) : ?>
    <?php if ($trustedshops_info_mode == "expert") : ?>
        <?php echo $trustedshops_trustbadge_code; ?>
    <?php else : ?>
        <script type="text/javascript">
            (function () {
                var _tsid = '<?php echo $trustedshops_info_tsid; ?>';
                _tsConfig = {
                    'yOffset': '<?php echo $trustedshops_trustbadge_offset; ?>', /* offset from page bottom */
                    'variant': '<?php echo $trustedshops_trustbadge_variant; ?>', /* text, default, small, reviews, custom, custom_reviews */
                    'customElementId': '', /* required for variants custom and custom_reviews */
                    'trustcardDirection': '', /* for custom variants: topRight, topLeft, bottomRight, bottomLeft */
                    'customBadgeWidth': '', /* for custom variants: 40 - 90 (in pixels) */
                    'customBadgeHeight': '', /* for custom variants: 40 - 90 (in pixels) */
                    'disableResponsive': 'false', /* deactivate responsive behaviour */
                    'disableTrustbadge': '<?php echo $disableTrustbadge; ?>', /* deactivate trustbadge */
                    'trustCardTrigger': 'mouseenter', /* set to 'click' if you want the trustcard to be opened on click instead */
                    'customCheckoutElementId': '' /* required for custom trustcard */
                };
                var _ts = document.createElement('script');
                _ts.type = 'text/javascript';
                _ts.charset = 'utf-8';
                _ts.async = true;
                _ts.src = '//widgets.trustedshops.com/js/' + _tsid + '.js';
                var __ts = document.getElementsByTagName('script')[0];
                __ts.parentNode.insertBefore(_ts, __ts);
            })();
        </script>
    <?php endif; ?>
<?php endif; ?> 

<?php /*Product Review */ ?>

<?php if ($trustedshops_display_review) : ?>
    <?php if ($trustedshops_info_mode == "expert") : ?>
         <?php echo $trustedshops_product_review_code; ?>
    <?php else : ?>
        <script type="text/javascript">
            _tsProductReviewsConfig = {
                tsid: '<?php echo $trustedshops_info_tsid; ?>',
                sku: ['<?php echo $product_model; ?>'],
                variant: 'productreviews',
                borderColor: '<?php echo $trustedshops_product_review_border_color; ?>',
                locale: '<?php echo $locale; ?>',
                backgroundColor: ' #ffffff',
                starColor: '<?php echo $trustedshops_product_review_star_color; ?>',
                starSize: '15px',
                ratingSummary: 'false',
                maxHeight: '1200px',
                'element': '#tab-trustedshop-reviews',
                hideEmptySticker: '<?php echo $trustedshops_product_review_hide_empty; ?>',
                introtext: '' /* optional */
            };
            var scripts = document.getElementsByTagName('SCRIPT'),
                me = scripts[scripts.length - 1];
            var _ts = document.createElement('SCRIPT');
            _ts.type = 'text/javascript';
            _ts.async = true;
            _ts.charset = 'utf-8';
            _ts.src
                = '//widgets.trustedshops.com/reviews/tsSticker/tsProductSticker.js';
            me.parentNode.insertBefore(_ts, me);
            _tsProductReviewsConfig.script = _ts;
        </script>
    <?php endif; ?>
<?php endif; ?>

<?php /*Product Rating */ ?>
<?php if ($trustedshops_display_rating) : ?>
    <?php if ($trustedshops_info_mode == "expert") : ?>
       <?php echo $trustedshops_product_rating_code; ?>
    <?php else : ?>
        <script type="text/javascript"
                src="//widgets.trustedshops.com/reviews/tsSticker/tsProductStickerSummary.js"></script>
        <script type="text/javascript">
            var summaryBadge = new productStickerSummary();
            summaryBadge.showSummary(
                {
                    'tsId': '<?php echo $trustedshops_info_tsid; ?>',
                    'sku': ['<?php echo $product_model; ?>'],
                    'element': '#ts_product_widget',
                    'starColor': '<?php echo $trustedshops_product_rating_star_color; ?>',
                    'starSize': '<?php echo $trustedshops_product_rating_star_size; ?>',
                    'fontSize': '<?php echo $trustedshops_product_rating_font_size; ?>',
                    'showRating': true,
                    'scrollToReviews': false,
					'enablePlaceholder': <?php echo $trustedshops_product_rating_hide_empty; ?>
                }
            );
        </script>
    <?php endif; ?>
<?php endif; ?>

<div id="waitbg"></div>
<div id="nest5"></div>
<script>
$('body').imagesLoaded()
.always( function( instance ) {
	$("#nest5, #waitbg").addClass("hide");
	//	 console.log('all images successfully loaded');
})
.done( function( instance ) {
	//	 console.log('all images successfully loaded');
}
);
</script>
<script>
	function init() {
	var imgDefer = document.getElementsByTagName('img');
	for (var i=0; i<imgDefer.length; i++) {
	if(imgDefer[i].getAttribute('data-src')) {
	imgDefer[i].setAttribute('src',imgDefer[i].getAttribute('data-src'));
	} } }
	window.onload = init;
</script>
</body></html>