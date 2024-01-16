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
<?php if($noindex) { ?>
<meta name="robots" content="noindex">
<?php } else { ?>
<meta name="robots" content="canonical">
<?php } ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $_GET["route"] == "product/product" ? 'Konfigurator - ' : ''; echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content= "<?php echo $keywords; ?>" />
<?php } ?>
<link href="catalog/view/javascript/jquery/css/jquery-ui.css" rel="stylesheet" media="screen" />
<!-- Bootstrap CSS core !-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<!-- End !-->
<!-- Bootstrap JS core !-->
<script src="catalog/view/javascript/jquery/jquery.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery-ui.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- End !-->
<script src="https://cdn.jsdelivr.net/npm/js-cookie@beta/dist/js.cookie.min.js"></script><!-- Cookie !-->
<!-- End !-->
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/oclayerednavigation/css/oclayerednavigation.css" rel="stylesheet">
<script src="catalog/view/javascript/opentheme/oclayerednavigation/oclayerednavigation.js" type="text/javascript"></script>
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Lato:700" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Poppins:400,500,600,700,300" rel="stylesheet" type="text/css">
<link href="catalog/view/theme/tt_wenro11/stylesheet/stylesheet.css?v=14" rel="stylesheet">
<script src="catalog/view/javascript/opentheme/hozmegamenu/custommenu.js?v=14" type="text/javascript"></script>
<script src="catalog/view/javascript/opentheme/hozmegamenu/mobile_menu.js?v=14" type="text/javascript"></script>
<script src="catalog/view/javascript/opentheme/ocslideshow/jquery.nivo.slider.js" type="text/javascript"></script>
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/ocslideshow/ocslideshow.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/ocslideshow/ocslideshow.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/css/animate.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/css/owl.carousel.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/rcrop.min.css" rel="stylesheet" />
<script src="catalog/view/javascript/jquery.event.move.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery.twentytwenty.js" type="text/javascript"></script>
<link href="catalog/view/theme/tt_wenro11/stylesheet/twentytwenty.css" rel="stylesheet" />
<link href="catalog/view/theme/tt_wenro11/stylesheet/style.css?v=14" rel="stylesheet" />
<script src="catalog/view/javascript/jquery/elevatezoom/jquery.elevatezoom.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/owl-carousel/owl.carousel.js" type="text/javascript"></script>
<script src="catalog/view/javascript/opentheme/ocquickview/ocquickview.js" type="text/javascript"></script>
<link href="catalog/view/theme/tt_wenro11/stylesheet/opentheme/ocquickview/css/ocquickview.css" rel="stylesheet">
<meta name="google-site-verification" content="dFrEiSi1Pxz6aX16kFTST591Dza43TR68g6qJpGvue0" />
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/javascript/common.js?v=14" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery.lazyload.min.js" type="text/javascript"></script>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
	var body_c = $('body').attr('class');
	var logo_forpage = 'image/yourplate-logo.svg';
	var logo_home = 'image/yourplate-logo.svg';
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
})(window,document,'script','dataLayer','GTM-P597VRD');</script>
<!-- End Google Tag Manager -->
<?php //echo $gaenhpro_gettrackcode;?>
<?php echo $script_code;?>
</head>
<body class="<?php echo $class; ?> home11 group1 group2 <?php if(isset($_GET['type_product'])) echo 'config' ?> <?php if ( $_GET['route'] != 'product/category' ) echo 'not product' ?>">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P597VRD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
<div class="container">
<div class="row">
<nav id="top">
	<div id="logo">
	  <?php $myHome = 'https://www.yourplate.de/'; if ($logo) { ?>
	  <a href="<?php echo $myHome; ?>"><img src="image/yourplate-logo.svg" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="svg-logo img-responsive" /></a>
	  <?php } else { ?>
	  <h1><a href="<?php echo $myHome; ?>"><?php echo $name; ?></a></h1>
	  <?php } ?>
	</div>

	<?php echo $cart; ?>
	<div class="search-container">
		<a data-toggle="modal" data-target="#myModal2" class="mobileOff"><i class="fa fa-search"></i></a>
		<div class="container-popup modal" id="myModal2">
			<div class="container">
				<a class="close" data-dismiss="modal"><span><?php echo $text_close; ?></span></a>
				<div class="logo2">
				  <a href="<?php echo $myHome; ?>"><img src="image/yourplate-logo.svg" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="svg-logo svg-logo2 img-responsive" /></a>
				</div>
				<?php echo $search; ?>
			</div>
		</div>
	</div>
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
			<?php if(isset($block1)){ echo $block1; } ?>
	</div>
</nav>
</div>
</div>
</div>
<header class="fixed-setting">
	<div class="row1">
		<div class="btn-fixsetting"><i class="fa fa-cog"></i></div>
		<div class="content-fixed-setting">
			<div class="content-inner">
				<a href="/konfigurator/1">Starte Konfigurator für 1 Platte</a>
				<a href="/konfigurator/2">Starte Konfigurator für 2 Platten</a>
				<a href="/konfigurator/3">Starte Konfigurator für 3 Platten</a>
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
<!-- <div class="hinweis hide">Wegen COVID -19 haben die Spediteure teilweise die Arbeit eingestellt. Es kann dadurch bis zu 16 Werktagen dauern bis ihre Bestellung ankommt.<br/>
Wir bitten um Verständnis</div> -->
