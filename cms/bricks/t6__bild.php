<?php
global $img_pfad, $ausrichtung, $ausrichtungArray, $dir, $imgGal, $imageFolder, $grIMG;

$data = explode("|", $text); $imgid = $data[0]; $ausrichtung = $data[1]; if(!$ausrichtung) $ausrichtung = 1;

$w = 1500;

if($imgid) {
	$que  	= "SELECT `longtext`, itext, imgname, name FROM `morp_cms_image` i, `morp_cms_img_group` g WHERE g.gid=i.gid AND imgid=$imgid";
	$res 	= safe_query($que);
	$rw     = mysqli_fetch_object($res);
	$itext 	= $rw->itext;
	$ltext 	= $rw->longtext;
	$inm 	= $rw->imgname;
	$altText = $itext ? $itext : $ltext; if(!$altText) $altText = $morpheus["client"].' '.$inm;
	$folder	= str_replace(array(";", " / ", "/", "  ", " "), array("","-","-", "-", "-"), $rw->name);

	$grIMG = '						<img src="'.$imageFolder.$inm.'" alt="'.$altText.'" class="img-fluid" />
';
}

$morp = $inm;

$socialImage = urlencode($inm);
global $socialImg; if(!$socialImg) $socialImg = $dir.$imageFolder.urlencode($folder).'/'.($inm).'?w='.$w;

?>