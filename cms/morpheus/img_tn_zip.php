<?
$src  	= ImageCreateFromJPEG($dir.$old);

$w = ImageSx($src);
$h = ImageSy($src);

if ($mx) {
	if ($w > $h) {
		if ($w > $mx) $wert = $mx/$w;
		else $wert = $w/$mx;
	}
	else {
		if ($h > $mx) $wert = $mx/$h;
		else $wert = $h/$mx;
	}
	
	if ($wert > 1) {
		$x = $w/$wert;
		$y = $h/$wert;
	}
	else {
		$x = $w*$wert;
		$y = $h*$wert;
	}

	$posx 	= ($mx - $x)/2;
	$posy 	= ($mx - $y)/2;
	$dst 	= ImageCreateTrueColor($mx,$mx);	# quadratisches format
	$white 	= ImageColorAllocate($dst, 255, 255, 255);
	
	imagefill($dst, 0, 0, $white);
	
	ImageCopyResampled($dst, $src, $posx, $posy, 0, 0, $x, $y, $w, $h);
	imageJPEG($dst, $dir."$nm", 70);
}

?>
