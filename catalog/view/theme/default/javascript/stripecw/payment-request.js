var stripePaymentRequest =  new function () {
	this.includeCss = function(url){
		var cssId = "stripe-overlay";
		if (!document.getElementById(cssId))
		{
		    var head  = document.getElementsByTagName("head")[0];
		    var link  = document.createElement("link");
		    link.id   = cssId;
		    link.rel  = "stylesheet";
		    link.type = "text/css";
		    link.href = url;
		    link.media = "all";
		    head.appendChild(link);
		}
	}
	
	this.includeScript = function(url) {
		if(typeof document.stripeJsInclude === "undefined") {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = url;
			document.head.appendChild(script);
			document.stripeJsInclude = true;
		}
	}
	
	this.initializeStripe = function() {
		if(typeof Stripe === "undefined") {
			setTimeout(stripePaymentRequest.initializeStripe, 500);
		}
		else {
			document.stripePaymentRequestStripe = Stripe(stripePaymentRequest.publishableKey);
		}
	}
	
	this.addOverlay = function(){
		var overlay = document.createElement("div");
		overlay.setAttribute("class", "stripe-flex-overlay");
		var wrapper = document.createElement("div")
		wrapper.setAttribute("class", "StripeWrapper");
		var button = document.createElement("div");
		button.id = "stripe-payment-request-button";
		button.style.display = "none";
		wrapper.appendChild(button);
		overlay.appendChild(wrapper);
		var spinner = document.createElement("div");
		spinner.setAttribute("class", "stripe-loader");
		spinner.id = "stripe-spinner";
		wrapper.appendChild(spinner);
		var body = document.getElementsByTagName("body")[0];
		body.appendChild(overlay);
		body.setAttribute("class", body.getAttribute("class") + " stripe-flex-noscroll");
	}

	this.initialize = function(country, currency, total, totalText, notAvailableText, createOrderUrl, successCallback, failCallback, publishableKey) {
		stripePaymentRequest.options = {
			country: country,
			currency: currency,
			total: {
				"label": totalText,
				"amount": total
			},
			requestShipping: false,
			requestPayerName: false,
			requestPayerEmail: false,
			requestPayerPhone: false
		};
		stripePaymentRequest.notAvailableText = notAvailableText;
		stripePaymentRequest.createOrderUrl = createOrderUrl;
		stripePaymentRequest.successCallback = function() {
			stripePaymentRequest.paymentRequestEvent.complete("success");
			successCallback();
		};
		stripePaymentRequest.failCallback = function(queryStr = "") {
			if(typeof stripePaymentRequest.paymentRequestEvent !== "undefined") {
				stripePaymentRequest.paymentRequestEvent.complete("fail");
			}
			failCallback(queryStr);
		};
		stripePaymentRequest.publishableKey = publishableKey;
	}
	
	this.addButton = function() {
		if(typeof document.stripePaymentRequestStripe === "undefined") {
			setTimeout(stripePaymentRequest.addButton, 500);
		}
		else {
			stripePaymentRequest.paymentRequest = document.stripePaymentRequestStripe.paymentRequest(stripePaymentRequest.options);
			var button = document.stripePaymentRequestStripe.elements().create("paymentRequestButton", {
				paymentRequest: stripePaymentRequest.paymentRequest
			});
			stripePaymentRequest.paymentRequest.canMakePayment().then(function(result) {
				if(result) {
					button.mount("#stripe-payment-request-button");
					document.getElementById("stripe-payment-request-button").style.display = "block";
					document.getElementById("stripe-spinner").style.display = "none";
				} else {
					stripePaymentRequest.failCallback("&cwError=" + stripePaymentRequest.notAvailableText);
				}
			});
			stripePaymentRequest.paymentRequest.on("token", function(ev) {
				document.getElementById("stripe-payment-request-button").style.display = "none";
				document.getElementById("stripe-spinner").style.display = "block";
				stripePaymentRequest.paymentRequestEvent = ev;
				stripePaymentRequest.createOrder();
			});
		}
	}
	
	this.createOrder = function() {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if(this.readyState == 4) {
				if(this.status == 200) {
					stripePaymentRequest.successCallback();
				}else {
					stripePaymentRequest.failCallback();
				}
			}
		};
		xhttp.open("POST", stripePaymentRequest.createOrderUrl + "&token=" + stripePaymentRequest.paymentRequestEvent.token.id, true);
		xhttp.send();
	}
}