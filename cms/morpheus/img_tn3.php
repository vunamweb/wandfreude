<?php
$src  	= ImageCreateFromJPEG($dir.$img);

$w = ImageSx($src);
$h = ImageSy($src);

if ($mx) {
	if ($w > $h) {
		if ($w > $wi) $wert = $wi/$w;
		else $wert = $w/$wi;
	}
	else {
		if ($h > $he) $wert = $he/$h;
		else $wert = $h/$he;
	}
	
	if ($wert > 1) {
		$x = $w/$wert;
		$y = $h/$wert;
	}
	else {
		$x = $w*$wert;
		$y = $h*$wert;
	}

	$posx 	= ($wi - $x)/2;
	$posy 	= ($he - $y)/2;
	$dst 	= ImageCreateTrueColor($wi,$he);	# quadratisches format
	$white 	= ImageColorAllocate($dst, 255, 255, 255);
	$black 	= ImageColorAllocate($dst, 0,0,0);
	
//	imagefill($dst, 0, 0, $white);
	imagefill($dst, 0, 0, $black);
	
	ImageCopyResampled($dst, $src, $posx, $posy, 0, 0, $x, $y, $w, $h);
	imageJPEG($dst, $dir."$nm", 85);
}

?>
