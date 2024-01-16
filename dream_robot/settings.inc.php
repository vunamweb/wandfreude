<?php
header('Content-type: text/html; charset=UTF-8');
ini_set('memory_limit', '256M');

include_once(__DIR__ . '/classes/class_curl_request.php');
$log_each_curl = TRUE;
$curl_request = new Curl_request($log_each_curl);
//Hinweis:	Standard ist die Curl_request->option "json" aktiviert, die die Datenarrays beim absenden in JSON-Objekte umwandelt (Und hinterher wieder zurück).
// 			Sie können diese Option aber auch deaktivieren(FALSE) und stattdessen die Option "xml" auf z.B. "request" setzen. Wenn Sie über XML kommunizieren möchten.
//Attention:At default the Curl_request->option "json" is activated. This option transforms the dataarray into JSON String when sending the request and decodes the response.
//			You could turn off this option(FALSE) and activate the option "xml" with value e.g. "request" instead.

//$curl_request->set_options(array('json' => FALSE, 'xml' => 'request'));


//Fangen Sie hier an, indem Sie Ihre Zugangsdaten einfügen:
//Start here by inserting your provided Client Credentials:

//DR-Kunden:
//DR-User:
$api_username = 'api_juergen_wiedemann_wandfreude_de_15246';//z.B. api_some_testaccount_12345
$api_password = 'AZEmYtanUjEJAgu2EgeqYbYVUHURYSUPUjupEQUM';//z.B. uPyNageVyDupAXYmasdSADuBfsy7UJubgJyvaquv
//DR-Partner:
//DR-Clients:
$client_id	  = 'XXXXX';//z.B. your_software
$client_pw	  = 'XXXXX';//z.B. uPyNageVyDupAXYmasdSADuBfsy7UJubgJyvaquv


//Im Laufe der verschiedenen Beispiele werden Sie Werte erhalten die Sie in Ihrer Software speichern sollten. In diesem Beispiel genügt es, wenn Sie diese hier eintragen.
//In the following examples you will get some ids which you should save in your softwares database. In this example you can save them in this variables.
//$access_token		= '';
$portal_account_id	= '24255';//e.g. 27346
//$order_id			= '6_2020_05';//e.g. 123456_2017_01
$stock_id			= 'XXXXX';//e.g. SKU_My-Personal-Stock-keeping-unit / EAN_1234567890123 / ID_1234567890 / 1234
$description_id 	= '12345';//e.g. 12345
$shipping_group_id 	= '12345';//e.g. 12345
$condition_id 		= '1000';//e.g. 1000
$currency_id 		= 'EUR';//e.g. EUR
$event_handler_id	= 'XXXXX';//e.g. 1234
$customer_id		= 'XXXXX';//e.g. 1234

$rest_version		= 'v1.1';
$rest_host			= 'https://api.dreamrobot.de/rest/' . $rest_version . '/';
$authorization_host	= 'https://api.dreamrobot.de/rest/' . $rest_version . '/';


//	Je nachdem, wie kommuniziert werden soll, bitte die 3 Header "Content-Type", "Accept" and "Accept-Charset" setzen. Die API kann "JSON/XML" und "utf-8/iso-8859-15"
//je verstehen und ausgeben. Wenn nichts übergeben wird, werden jeweils JSON und UTF-8 als Standard angenommen.
//	Depending on your preferred communication, set "Content-Type", "Accept" and "Accept-Charset". The API can "JSON/XML" and "utf-8/iso-8859-15" each understand and speak.
//If omitted, the values default to JSON and UTF-8.
$rest_header		= array(
	'Authorization: Bearer ' . $access_token,
	'Content-Type: application/json encoding=utf-8',
//	'Content-Type: application/json encoding=iso-8859-15',
//	'Content-Type: application/xml encoding=utf-8',
//	'Content-Type: application/xml encoding=iso-8859-15',
	'Accept: application/json',
//	'Accept: text/xml',
	'Accept-Charset: utf-8',
//	'Accept-Charset: iso-8859-15',
);
$token_header		= array(
	'Content-Type: application/json encoding=utf-8',
    //'Authorization: Basic '
	// ...
);