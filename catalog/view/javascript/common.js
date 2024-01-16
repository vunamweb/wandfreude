function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}
$(function(){
	$('img.lazy').lazyload({
		event : "sporty",
	});
});
$(document).ajaxStop(function(){
	$("img.lazy").lazyload({
        event : "sporty",
    });
});
$(window).bind("load", function(){
	try{
		var timeout = setTimeout(function(){
			$("img.lazy").trigger("sporty")
		}, 500);
	}
	catch(err){
		console.log(err.message);
	}
});
function isEmpty( el ){
  return !$.trim(el.html())
}
$(document).ajaxStop(function() {
	function isEmpty( el ){
		return !$.trim(el.html())
	}
	if (!isEmpty($('#product'))) {
		  $('#product').addClass('has-option');
	}
});
$(document).ajaxStop(function(){
	$("img.lazy").lazyload({
		event : "sporty",
	});
	var timeout = setTimeout(function() {
		$("img.lazy").trigger("sporty")
	}, 1000);
	// replace tags
	$('.tags-product a').each(function(){
		$(this).text(function(index, text){
			return text.replace('_', ' ');
		});
	});
	$('.tags-product2 a').each(function(){
		$(this).text(function(index, text){
			return text.replace('_', ' ');
		});
	});
	$('.product-name + p a').each(function(){
		$(this).text(function(index, text){
			return text.replace('_', ' ');
		});
	});
});
// sticky menu
function stickyMenu(){
  var winInnW = window.innerWidth;
  var winScrT = $(window).scrollTop();
  var s0w991  = winScrT>300 && winInnW>991;
  $("#top").toggleClass("fix-header", s0w991);
}

/*
stickyMenu();
$(window).on('resize scroll', stickyMenu);
*/

// custom responsive
function customResponsive(){
	var window_w = parseInt($(window).width());
	if(window_w <= 991){
		$('.banner-2 .row2 > div.first').before($('.banner-2 .row2 > div.last'));
	} else{
		$('.banner-2 .row2 > div.first').after($('.banner-2 .row2 > div.last'));
	}
	if(window_w <= 767){
		$('.follow-footer').before($('.copyright-text'));
	} else{
		$('.copyright-text').before($('.follow-footer'));
	}
	if(window_w <= 640){
		$('footer .footer-title').each(function(){
			if($(this).next().is('.box-content')){
				$(this).next().addClass('collapse');
				$(this).addClass('ex-footer');
				$(this).attr('data-toggle','collapse');
			}
		});
	} else{
		$('footer .footer-title').each(function(){
			if($(this).next().is('.box-content')){
				$(this).next().removeClass('collapse');
				$(this).removeAttr('data-toggle');
				$(this).next().removeAttr('style');
			}
		});
	}

	if(window_w <= 640){
		$('.home9 #logo').after($('.lang-cur'));
		$('.home13 #logo').after($('.lang-cur'));
	} else {
		$('.home9 #logo').before($('.lang-cur'));
		$('.home13 #logo').before($('.lang-cur'));
	}

}
$(window).resize(function() {
	customResponsive();
});
function onPlayerReady(event) {
	document.getElementById('demoIFame').style.opacity = 1;
}
function onYouTubeIframeAPIReady() {
	player = new YT.Player('demoIFame', {
		events: {
		  'onReady': onPlayerReady,
		  'onStateChange': onPlayerStateChange
		}
	});
}
function onPlayerReady(playerStatus) {
	document.getElementById('demoIFame').style.opacity = "1";
}
function changeBorderColor(playerStatus) {
	var number;
	if (playerStatus == -1) {
	  number = "1"; // unstarted
	} else if (playerStatus == 0) {
	  number = "1"; // ended
	  // $('.video-link').css('z-index','1');
	} else if (playerStatus == 1) {
	  number = "1"; // playing
	} else if (playerStatus == 2) {
	  number = "1"; // paused
	} else if (playerStatus == 3) {
	  number = "1"; // buffering
	} else if (playerStatus == 5) {
	  number = "1"; // video cued
	}
	if (number) {
	  document.getElementById('demoIFame').style.opacity = number;
	}
}
function onPlayerStateChange(event) {
	changeBorderColor(event.data);
}
$(document).ready(function() {
	customResponsive();

	if (!isEmpty($('#product'))) {
		  $('#product').addClass('has-option');
	}
	if (!isEmpty($('#product2'))) {
		  $('#product2').addClass('has-option');
	}

	// replace tags
	$('.tags-product a').each(function(){
		$(this).text(function(index, text){
			return text.replace('_', ' ');
		});
	});
	$('.tags-product2 a').each(function(){
		$(this).text(function(index, text){
			return text.replace('_', ' ');
		});
	});
	$('.product-name + p a').each(function(){
		$(this).text(function(index, text){
			return text.replace('_', ' ');
		});
	});

	// load video
	$('.video-link').click(function(e){

		e.preventDefault();
		if (isEmpty($('#player'))) {
			$('.loading').show();
			$('.playvideo').hide();

			var vcode = $(this).attr('href').split('v=');
			url = 'https://www.youtube.com/embed/'+vcode[vcode.length-1]+'?rel=0&autoplay=1&enablejsapi=1';

			$('#player').prepend('<iframe id="demoIFame" src="' + url + '" frameborder="0" ></iframe>');
			var $iFrame = $('#demoIFame');
			$iFrame.on('load', function(){
				$('.loading').hide();
				$('.playvideo').show();
			})

			var tag = document.createElement('script');
			  tag.id = 'iframe-demo';
			  tag.src = 'https://www.youtube.com/iframe_api';
			  var firstScriptTag = document.getElementsByTagName('script')[0];
			  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

			  var player;
		} else if (!isEmpty($('#player'))){
			$('#demoIFame').css('opacity','1');
			$('.video-link').css('z-index','0');
		}
	});

	// move cms block
	$('.home1 .follow-footer, .home2 .follow-footer, .home3 .follow-footer, .home8 .follow-footer').append($('.footer-social-icons'));
	$('.group2 .static-footer .s-menu').append($('.group2 .s-menu-item'));
	$('.home6 .top-footer').prepend($('.about-static3'));
	$('.home10 .top-footer').prepend($('.about-static4'));
	$('.home10 .cmsblock-opentimes').prepend($('.static-opentimes'));

	// move header social
	$('#top').prepend($('.home3 .follow-header'));

	// move alert success
	$('.alert-success').prependTo($('body'));

	// move breadcrumbs
	$('.group1 #top').after('<div class="breadcrumbs"><div class="container"></div></div>');
	var breadcrumb = $('ul.breadcrumb');
	var breadcrumbs_container = $('.breadcrumbs .container');
	breadcrumb.appendTo(breadcrumbs_container);
	$('.breadcrumbs').before($('.category-name'));
	$('.breadcrumbs').before($('.col-2 > .product-name:first-child'));
	if(!isEmpty($('.opc-hidden'))){
		$('.breadcrumbs').before($('.opc-hidden + h1'));
		$('.breadcrumbs').before($('.opc-hidden + h2'));
	}else {
		$('.breadcrumbs').before($('#content > .h1:first-child'));
		$('.breadcrumbs').before($('#content > .h2:first-child'));
	}


	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	// Currency
	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').attr('value', $(this).attr('name'));

		$('#form-currency').submit();
	});

	// Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').attr('value', $(this).attr('name'));

		$('#form-language').submit();
	})

	/* Search */
	$('#search input[name=\'search\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';

		var value = $('#search input[name=\'search\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#search input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#search input[name=\'search\']').parent().find('button').trigger('click');
		}
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 5) + 'px');
		}
	});

	// Product List
	$('#list-view').click(function() {
		$('.product-thumb.layout7').each(function(){
			$(this).find(productname_pos).after($(this).find(rating_pos));
		});
	});
	$('#grid-view').click(function() {
		$('.product-thumb.layout7').each(function(){
			$(this).find(productname_pos).before($(this).find(rating_pos));
		});
	});
	$('#list-view').click(function() {
		$(this).addClass('selected');
		$('#grid-view').removeClass('selected');
		$('#content .product-grid > .clearfix').remove();

		//$('#content .product-layout').attr('class', 'product-layout product-list col-xs-12');
		$('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
		$('#content .product-list .product-inner').addClass('col-xs-8');
		$('#content .product-list .image').addClass('col-xs-4');


		localStorage.setItem('display', 'list');
	});

	// Product Grid
	$('#grid-view').click(function() {
		$(this).addClass('selected');
		$('#list-view').removeClass('selected');
		// What a shame bootstrap does not take into account dynamically loaded columns
		cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-md-6 col-sm-12 col-xs-6 two-items');
		} else if (cols == 1) {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-md-4 col-sm-6 col-xs-6 three-items');
		} else {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-md-3 col-sm-6 col-xs-6 four-items');
		}
		$('#content .product-grid .product-inner').removeClass('col-xs-8');
		$('#content .product-grid .image').removeClass('col-xs-4');


		 localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {

		$('#list-view').trigger('click');
	} else {
		$('#grid-view').trigger('click');
	}

	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', animation: false});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body', animation: false});
	});
});

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart-total').button('loading');
			},
			complete: function() {
				$('#cart-total').button('reset');
			},
			success: function(json) {
				$('.alert, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('body').before('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('#cart-total').html(json['total']);
					}, 100);

					$('html, body').animate({ scrollTop: 0 }, 'slow');

					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart-total').button('loading');
			},
			complete: function() {
				$('#cart-total').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart-total').html(json['total']);
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart-total').button('loading');
			},
			complete: function() {
				$('#cart-total').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart-total').html(json['total']);
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart-total').button('loading');
			},
			complete: function() {
				$('#cart-total').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart-total').html(json['total']);
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('body').before('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();

				if (json['success']) {
					$('body').before('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);
