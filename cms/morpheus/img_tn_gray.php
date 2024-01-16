<?
$src  	= ImageCreateFromJPEG($dir.$img);

$w = ImageSx($src);
$h = ImageSy($src);

if ($mx) {
	if ($h > $mx) $wert = $mx/$h;
	else $wert = $h/$mx;
	
	if ($wert > 1) {
		$x = $w/$wert;
		$y = $h/$wert;
	}
	else {
		$x = $w*$wert;
		$y = $h*$wert;
	}
	#$dst = ImageCreateTrueColor($x,$y);
	$dst = ImageCreateTrueColor($mx,$mx);	# quadratisches format
	ImageCopyResampled($dst, $src, 0, 0, 0, 0, $x, $y, $w, $h);
	#header("Content-type: image/jpeg");
	imagefilter($dst, IMG_FILTER_GRAYSCALE);
	imageJPEG($dst, $dir.$nm, 70);
}

?>
