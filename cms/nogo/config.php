<?php
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# www.pixel-dusche.de / björn t. knetter / post@pixel-dusche.de / frankfurt am main, germany
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
global $morpheus;
$morpheus = array();

$morpheus["dbname"] 		= "cutout-image-shop";
$morpheus["user"]			= "root";
$morpheus["password"]		= "";
$morpheus["server"]			= "localhost";

$morpheus["imageurl"]		= "http://localhost/your-plate/cms/";
$morpheus["url"]			= "http://localhost/your-plate/";
$morpheus["search_ID"]		= array("de"=>24, "en"=>200, );

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #


$morpheus["multilang"]		= 0;
$morpheus["dfile"]			= "morpheus_db.sql";
$morpheus["home_ID"]		= array("de"=>1, "en"=>2 );
$morpheus["lan_arr"]		= array(1=>"de", 20=>"en" );
$morpheus["lan_nm_arr"]		= array("de"=>"Deutsch", "en"=>"English", );

$morpheus["img_size_news"]		= 450;
$morpheus["img_size_news_tn"]	= 120;
$morpheus["img_size_tn"]	= 35;
$morpheus["img_size_full"]	= 600;
$morpheus["img_size"]		= 600;
$morpheus["page-topic"]		= "";
$morpheus["publisher"]		= "";
$morpheus["foto"]			= 0;
$morpheus["imageName"]		= "morpheus_";
$morpheus["GaleryPath"]		= "morpheus_";

$morpheus["imageFolder"] 	= 'image/';

// NEW array for SEARCH PAGE
// Array Lang-ID => navID *********************************************

?>