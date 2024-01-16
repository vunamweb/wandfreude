<?php
session_start();
//error_reporting(0);
# session_destroy();
//phpinfo();
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
global $dir, $navarray, $nav_h, $nav_s, $navarrayFULL, $SID, $lightbox, $lang, $lan, $hn, $sn2, $nid, $ns, $waehrung, $thwg, $product_show, $wg_txt, $navID, $img_pfad, $uri, $print, $imageFolder;
global $news_headl, $news_back, $tcolor, $mindflashID, $kompetenz, $komp_col, $lokal_pfad, $sub1_id, $qSET, $IAMIN, $urlencode, $multilang, $relative_path, $relative_url, $profile;
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #


// print_r($_REQUEST);


// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
//     1   GRUNDEINSTELLUNGEN
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .


include("nogo/config.php");
include("nogo/funktion.inc");

$dir = $morpheus["url"];
$imageurl = $morpheus["imageurl"];

$img_pfad = $imageurl."images/userfiles/image/";
$imageFolder = $imageurl."images/userfiles/image/";
$lokal_pfad = $dir;
$lan = 'de';
$lang = 1;

include("nogo/".$lan.".inc");
include("nogo/navarray_".$lan.".php");
include("nogo/navID_".$lan.".inc");
include("nogo/db.php");
dbconnect();

// SETTINGS KUNDEN LADEN
$sql = "SELECT * FROM morp_settings WHERE 1";
$res = safe_query($sql);
while($row = mysqli_fetch_object($res)) {
	$morpheus[$row->var] = $row->value;
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
// standard seite festlegen

// ID dieser Seite auswerten
$cid = $_GET["cid"];
$hn_id = $cid;


// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
//     3   CONTENT / TEXT / INHALT
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .

if ($cid) {
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	// Hier alle Variablen setzen, die ggf im Template gesetzt werden
	$output='';
	$headerImg = '';
	$footer = '';
	$zusatz = '';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

	$query = "SELECT * FROM `morp_cms_content` c LEFT JOIN morp_cms_image i ON i.imgid=c.img1, `morp_cms_nav` n WHERE c.navid=".$cid." AND n.navid=c.navid AND ton=1 ORDER BY tpos";
	$result 	= safe_query($query);

	while ($throw = mysqli_fetch_object($result)) {
		$text		= $throw->content;
		$templ_id 	= $throw->tid;
		$templ_headl= $throw->theadl;
		$templ_lnk 	= $throw->tlink;
		$anker		= $templ_lnk;
		$twidth		= $throw->twidth;
		$theight	= $throw->theight;
		$templ_bgr 	= $throw->tbackground;
		$tfoto 		= $throw->timage;
		$tcolor		= $throw->tcolor;
		$tref		= $throw->tref;
		$tende		= $throw->tende;
		$tabstand	=$throw->tabstand;
		$style 		= '';

		$templ_lnk_anz = '';
		$templ_lnk_box = '';
		$foto_lnk = '';
		$foto_url = '';

	  	# # # # auswertung text startet
		# # # # auswertung text startet

		$get = (get_cms_text($text, $lang, $dir));
		$get = str_replace("u".chr(204).chr(136), 'ü', $get);

		$get = setLink($get, '', ' class="underline"');
		$get = str_replace("u".chr(204).chr(136), 'ü', $get);

		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		// hole Template
		include("templates/". ($templ_id ? $templ_id : 1) .".php");

		$content = "output";

		//echo $content;
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		// fuelle Template mit Content / in durch $content zugeiwesene Var
		$$content .= str_replace(array("#cont#", "#col#", "#headl#", "#foto#", "#style#", "#link#", "#link_anz#", "#link_box#", "#link_pur#", "<!-- SUBNAV03 -->"), array($get, $templ_bgr, $templ_headl, $foto_url, $style, $templ_lnk, $templ_lnk_anz, $templ_lnk_box, $foto_lnk, $sub3), $template);

		$tref = '';
	# # # # # # # # # # # # # # # # # # # # # # # # # # #

	}
}

// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
//     4   AUSGABE
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .
// .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .   .


echo $output;

save_data('../include/'.$cid.'.php',$output,"w");

?>