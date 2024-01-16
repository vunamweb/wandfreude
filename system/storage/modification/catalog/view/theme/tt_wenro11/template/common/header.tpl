<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="facebook-domain-verification" content="xm90s53j43qfy0dlaz7f3otlvty3bs" />
<?php if($noindex) { ?>
<meta name="robots" content="noindex">
<?php } else { ?>
<meta name="robots" content="canonical">
<?php } ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $_GET["route"] == "product/product" ? '' : ''; echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content= "<?php echo $keywords; ?>" />
<?php } ?>
<meta name="p:domain_verify" content="040bb7468866a9303c4c468a077e9bf6"/>
<link href="catalog/view/javascript/jquery/css/jquery-ui.css" rel="stylesheet" media="screen" />
<!-- Bootstrap CSS core !-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<!-- End !-->
<!-- Bootstrap JS core !-->
<script src="catalog/view/javascript/jquery/jquery.min.js" type="text/javascript"></script>
<?php if($canonical_product) echo $canonical_product_link ?>
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/js.cookie.min.js" type="text/javascript"></script><!-- Cookie !-->
<!-- 
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/oclayerednavigation/css/oclayerednavigation.css" rel="stylesheet">
<script src="catalog/view/javascript/opentheme/oclayerednavigation/oclayerednavigation.js" type="text/javascript"></script>
!-->
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/stylesheet.css?v=<?php echo rand(0,999); ?>" rel="stylesheet">
<!-- 
<script src="catalog/view/javascript/opentheme/hozmegamenu/custommenu.js?v=12" type="text/javascript"></script>
<script src="catalog/view/javascript/opentheme/hozmegamenu/mobile_menu.js?v=12" type="text/javascript"></script>
-->
<script src="catalog/view/javascript/opentheme/ocslideshow/jquery.nivo.slider.js" type="text/javascript"></script>
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/ocslideshow/ocslideshow.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/ocslideshow/ocslideshow.css" rel="stylesheet" />
<!-- 
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/css/animate.css" rel="stylesheet" />
-->
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/css/owl.carousel.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/rcrop.min.css" rel="stylesheet" />
<script src="catalog/view/javascript/jquery.event.move.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery.twentytwenty.js" type="text/javascript"></script>
<link href="catalog/view/theme/tt_wenro11/stylesheet/twentytwenty.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/style.css?v=14" rel="stylesheet" />
<script src="catalog/view/javascript/jquery/elevatezoom/jquery.elevatezoom.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/owl-carousel/owl.carousel.js" type="text/javascript"></script>
<script src="catalog/view/javascript/imagesloaded.pkgd.min.js" type="text/javascript"></script>
<!-- 
<script src="catalog/view/javascript/opentheme/ocquickview/ocquickview.js" type="text/javascript"></script>
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/ocquickview/css/ocquickview.css" rel="stylesheet">
-->
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/javascript/common.js?v=12" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery.lazyload.min.js" type="text/javascript"></script>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($analytics as $analytic) { ?>
<?php 
// ??? whats that?
// echo $analytic; 
?>
<?php } ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
	var body_c = $('body').attr('class');
	var logo_forpage = 'image/Wandfreude-Logo.svg';
	var logo_home = 'image/Wandfreude-Logo.svg';
	var logo_src = '';
	if(body_c.search('common-home') == -1) {
		logo_src = logo_forpage;
	} else {
		logo_src = logo_home;
	}
	$('#logo img').attr('src',logo_src);
})
//]]>
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer', 'GTM-MS4JXFL');</script>
<!-- End Google Tag Manager bk -->

<?php echo $script_code;?>

</head>
<body class="<?php echo $class; ?> home11 group1 group2 <?php if(isset($_GET['type_product'])) echo 'config' ?> <?php if ( $_GET['route'] != 'product/category' ) echo 'not product' ?>">
<?php

/*
    [route] => product/product
    [product_id] => 361
    [category] => 79
    [type_product] => 1
    [category_type] => 1
    [number_plate] => 3


    [route] => product/product
    [product_id] => 342
    [category] => 79
    [type_product] => 1
    [category_type] => 1
    [number_plate] => 3

    [route] => product/product
    [category_type] => 1
    [type_product] => 1
    [number_plate] => 1

   [route] => product/category
    [choose_category] => true


            */

?>
<div class="wrapper">
	<div class="new-space">
		<div class="vertrauen">
			<div class="container-full">
				<div class="row">
					<div class="col-6 col-md-4">
						<a data-toggle="tooltip" data-placement="bottom" data-html="true" title="" data-original-title="06101 – 9858619"><i class="fa fa-phone"></i> Experten am Telefon</a>
					</div>
					<div class="col-6 col-md-4">
						<a data-toggle="tooltip" data-placement="bottom" data-html="true" title="" data-original-title="mit SSL-Verschlüsselung"><i class="fa fa-lock"></i> Verschlüsselte Datenübertragung</a>
					</div>
					<div class="col-6 col-md-4">
						<a data-toggle="tooltip" data-placement="bottom" data-html="true" title="" data-original-title="Paypal, Kreditkarte, Vorkasse, Sofort-Überweisung"><i class="fa fa-institution"></i> Sichere Zahlungsweise</a>
					</div>
				</div>
			</div>
		</div>

		<div class="container-full">
			<div class="row">
				<nav id="top">
					<div id="logo">
					  <?php $myHome = 'https://www.wandfreude.de/'; if ($logo) { ?>
					  <a href="<?php echo $myHome; ?>"><img src="image/Wandfreude-Logo.svg" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="svg-logo img-responsive" /></a>
					  <?php } else { ?>
					  <h1><a href="<?php echo $myHome; ?>"><?php echo $name; ?></a></h1>
					  <?php } ?>
					</div>

					<?php echo $cart; ?>
<!-- SEARCH OFF -->
					<div class="search-container mobileOff">
						<?php if($use_ajax_login): ?>
							<a id="a-login-link" href="<?php echo $login; ?>"><i class="fa fa-user"></i></a>
						<?php else: ?>
							<a href="<?php echo $login; ?>"><i class="fa fa-user"></i></a>
						<?php endif; ?>
					</div>
					<div class="search-container mobileOff">
						<a href="kontakt/"><i class="fa fa-envelope"></i></a>
					</div>
					<div class="search-container mobileOff">
						<a href="/"><i class="fa fa-home"></i></a>
					</div>
					<div class="main-menu">
							<?php if(isset($block1)){ 
								echo $block1; } 
							?>
					</div>
				</nav>
				<!-- <div class="module-banner-home">
							<?php if(isset($block2)){ 
								echo $block2; } 
							?>
				</div> !-->
			</div>
		</div>
	</div>


	<header class="fixed-setting">
		<div class="row1">
			<div class="btn-fixsetting"><i class="fa fa-cog"></i></div>
			<div class="content-fixed-setting">
				<div class="content-inner">
					<a href="/duschrueckwand/kundenlieblinge/einsamer-strand/11906/110/1">Starte Konfigurator für 1 Platte</a>
					<a href="/duschrueckwand/kundenlieblinge/einsamer-strand/11906/110/2">Starte Konfigurator für 2 Platten</a>
					<a href="/duschrueckwand/kundenlieblinge/einsamer-strand/11906/110/3">Starte Konfigurator für 3 Platten</a>
				</div>
			</div>
		</div>
		<div class="row2">
			<div class="btn-fixsetting"><i class="fa fa-user"></i></div>
			<div class="content-fixed-setting">
			<div class="content-inner" id="top-links">
				<?php if($use_ajax_login): ?>
					<ul class="ul-account">
				<?php else: ?>
					<ul>
				<?php endif; ?>
	            <?php if ($logged) { ?>
	            <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
	            <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
	            <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
	            <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
				<li><a href="<?php echo $wishlist; ?>" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><span><?php echo $text_wishlist; ?></span></a></li>
	            <li>
					<?php if($use_ajax_login): ?>
						<a id="a-logout-link" href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a>
					<?php else: ?>
						<a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a>
					<?php endif; ?>
				</li>
	            <?php } else { ?>
	            <li>
					<?php if($use_ajax_login): ?>
						<a id="a-register-link" href="<?php echo $register; ?>"><?php echo $text_register; ?></a>
					<?php else: ?>
						<a href="<?php echo $register; ?>"><?php echo $text_register; ?></a>
					<?php endif; ?>
				</li>
	            <li>
					<?php if($use_ajax_login): ?>
						<a id="a-login-link" href="<?php echo $login; ?>"><?php echo $text_login; ?></a>
					<?php else: ?>
						<a href="<?php echo $login; ?>"><?php echo $text_login; ?></a>
					<?php endif; ?>
				</li>
	            <?php } ?>
	          </ul>
			</div>
			</div>
		</div>
		<div class="row3">
			<div class="btn-fixsetting"><i class="fa fa-phone"></i></div>
			<div class="content-fixed-setting">
				<div class="content-inner">
					<a href="tel:<?php echo $telephone; ?>"><i class="fa fa-phone"></i>&nbsp;<span><?php echo $telephone; ?></span></a>
				</div>
			</div>
		</div>
	</header>
<div class="clearfix"></div>
<div id="show_popup_cart">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        Sie benötigen noch Zubehör für die Montage Ihrer Rückwand? Hier finden Sie alles, was dazu benötigt wird
	  </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="close_modal_cart">schließen</button>
		<button type="button" class="btn btn-danger" id="go_to_shop">zum Shop</button>
      </div>

    </div>
  </div>
</div>
<!-- <div class="hinweis hide">Wegen COVID -19 haben die Spediteure teilweise die Arbeit eingestellt. Es kann dadurch bis zu 16 Werktagen dauern bis ihre Bestellung ankommt.<br/>
Wir bitten um Verständnis</div> -->
