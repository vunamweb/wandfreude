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
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content= "<?php echo $keywords; ?>" />
<?php } ?>
<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />
<link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet">
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/javascript/common.js" type="text/javascript"></script>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>
<?php echo $gaenhpro_gettrackcode;?>

        <script type="text/javascript">
          function facebook_loadScript(url, callback) {
            var script = document.createElement("script");
            script.type = "text/javascript";
            if(script.readyState) {  // only required for IE <9
              script.onreadystatechange = function() {
                if (script.readyState === "loaded" || script.readyState === "complete") {
                  script.onreadystatechange = null;
                  if (callback) {
                    callback();
                  }
                }
              };
            } else {  //Others
              if (callback) {
                script.onload = callback;
              }
            }

            script.src = url;
            document.getElementsByTagName("head")[0].appendChild(script);
          }
        </script>

        <script type="text/javascript">
          (function() {
            var enableCookieBar = '<?= $facebook_enable_cookie_bar ?>';
            if (enableCookieBar === 'true') {
              facebook_loadScript("catalog/view/javascript/facebook/cookieconsent.min.js");

              // loading the css file
              var css = document.createElement("link");
              css.setAttribute("rel", "stylesheet");
              css.setAttribute("type", "text/css");
              css.setAttribute(
                "href",
                "catalog/view/theme/css/facebook/cookieconsent.min.css");
              document.getElementsByTagName("head")[0].appendChild(css);

              window.addEventListener("load", function(){
                function setConsent() {
                  fbq(
                    'consent',
                    this.hasConsented() ? 'grant' : 'revoke'
                  );
                }
                window.cookieconsent.initialise({
                  palette: {
                    popup: {
                      background: '#237afc'
                    },
                    button: {
                      background: '#fff',
                      text: '#237afc'
                    }
                  },
                  cookie: {
                    name: fbq.consentCookieName
                  },
                  type: 'opt-out',
                  showLink: false,
                  content: {
                    dismiss: 'Agree',
                    deny: 'Opt Out',
                    header: 'Our Site Uses Cookies',
                    message: 'By clicking Agree, you agree to our <a class="cc-link" href="https://www.facebook.com/legal/terms/update" target="_blank">terms of service</a>, <a class="cc-link" href="https://www.facebook.com/policies/" target="_blank">privacy policy</a> and <a class="cc-link" href="https://www.facebook.com/policies/cookies/" target="_blank">cookies policy</a>.'
                  },
                  layout: 'basic-header',
                  location: true,
                  revokable: true,
                  onInitialise: setConsent,
                  onStatusChange: setConsent,
                  onRevokeChoice: setConsent
                }, function (popup) {
                  // If this isn't open, we know that we can use cookies.
                  if (!popup.getStatus() && !popup.options.enabled) {
                    popup.setStatus(cookieconsent.status.dismiss);
                  }
                });
              });
            }
          })();
        </script>

        <script type="text/javascript">
          (function() {
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');

            var enableCookieBar = '<?= $facebook_enable_cookie_bar ?>';
            if (enableCookieBar === 'true') {
              fbq.consentCookieName = 'fb_cookieconsent_status';

              (function() {
                function getCookie(t){var i=("; "+document.cookie).split("; "+t+"=");if(2==i.length)return i.pop().split(";").shift()}
                var consentValue = getCookie(fbq.consentCookieName);
                fbq('consent', consentValue === 'dismiss' ? 'grant' : 'revoke');
              })();
            }

            <?php if ($facebook_pixel_id_FAE) { ?>
              facebook_loadScript(
                "catalog/view/javascript/facebook/facebook_pixel.js",
                function() {
                  var params = <?= $facebook_pixel_params_FAE ?>;
                  _facebookAdsExtension.facebookPixel.init(
                    '<?= $facebook_pixel_id_FAE ?>',
                    <?= $facebook_pixel_pii_FAE ?>,
                    params);
                  <?php if ($facebook_pixel_event_params_FAE) { ?>
                    _facebookAdsExtension.facebookPixel.firePixel(
                      JSON.parse('<?= $facebook_pixel_event_params_FAE ?>'));
                  <?php } ?>
                });
            <?php } ?>
          })();
        </script>
      
</head>
<body class="<?php echo $class; ?>">
<nav id="top">
  <div class="container">
    <?php echo $currency; ?>
    <?php echo $language; ?>
    <div id="top-links" class="nav pull-right">
      <ul class="list-inline">
        <li><a href="<?php echo $contact; ?>"><i class="fa fa-phone"></i></a> <span class="hidden-xs hidden-sm hidden-md"><?php echo $telephone; ?></span></li>
        <li class="dropdown"><a href="<?php echo $account; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_account; ?></span> <span class="caret"></span></a>
          <ul class="dropdown-menu dropdown-menu-right">
            <?php if ($logged) { ?>
            <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
            <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
            <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
            <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
            <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
            <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
            <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
            <?php } ?>
          </ul>
        </li>
        <li><a href="<?php echo $wishlist; ?>" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_wishlist; ?></span></a></li>
        <li><a href="<?php echo $shopping_cart; ?>" title="<?php echo $text_shopping_cart; ?>"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_shopping_cart; ?></span></a></li>
        <li><a href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>"><i class="fa fa-share"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_checkout; ?></span></a></li>
      </ul>
    </div>
  </div>
</nav>
<header>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
        <div id="logo">
          <?php if ($logo) { ?>
          <a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" /></a>
          <?php } else { ?>
          <h1><a href="<?php echo $home; ?>"><?php echo $name; ?></a></h1>
          <?php } ?>
        </div>
      </div>
      <div class="col-sm-5"><?php echo $search; ?>
      </div>
      <div class="col-sm-3"><?php echo $cart; ?></div>
    </div>
  </div>
</header>
<?php if ($categories) { ?>
<div class="container">
  <nav id="menu" class="navbar">
    <div class="navbar-header"><span id="category" class="visible-xs"><?php echo $text_category; ?></span>
      <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><i class="fa fa-bars"></i></button>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
      <ul class="nav navbar-nav">
        <?php foreach ($categories as $category) { ?>
        <?php if ($category['children']) { ?>
        <li class="dropdown"><a href="<?php echo $category['href']; ?>" class="dropdown-toggle" data-toggle="dropdown"><?php echo $category['name']; ?></a>
          <div class="dropdown-menu">
            <div class="dropdown-inner">
              <?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
              <ul class="list-unstyled">
                <?php foreach ($children as $child) { ?>
                <li><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
                <?php } ?>
              </ul>
              <?php } ?>
            </div>
            <a href="<?php echo $category['href']; ?>" class="see-all"><?php echo $text_all; ?> <?php echo $category['name']; ?></a> </div>
        </li>
        <?php } else { ?>
        <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
        <?php } ?>
        <?php } ?>
      </ul>
    </div>
  </nav>
</div>
<?php } ?>
