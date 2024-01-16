<?php
if ($png) $src  	= ImageCreateFromPNG($dir.$img);
else 	  $src  	= ImageCreateFromJPEG($dir.$img);

$w = ImageSx($src);
$h = ImageSy($src);

if ($mx) {
	if ($w > $h) {
		if ($w > $mx) $wert = $mx/$w;
		else $wert = $w/$mx;
		$quer = 1;
	}
	else {
		if ($h > $mx) $wert = $mx/$h;
		else $wert = $h/$mx;
		$hoch = 1;
	}
	
	if ($wert > 1) {
		$x = $w/$wert;
		$y = $h/$wert;
	}
	else {
		$x = $w*$wert;
		$y = $h*$wert;

		if ($quer && $y < $mx) {
			$nS = $mx/$y;
			$x = $x*$nS;
			$y = $y*$nS;
			$new = $h;
		}
		elseif ($hoch && $x < $mx) {
			$nS = $mx/$x;
			$x = $x*$nS;
			$y = $y*$nS;
			$new = $w;
		}
	}

	$posx 	= ($mx - $x)/2;
	$posy 	= ($mx - $y)/2;
	$dst 	= ImageCreateTrueColor($mx,$mx);	# quadratisches format
	$white 	= ImageColorAllocate($dst, 255, 255, 255);
	
	imagefill($dst, 0, 0, $white);
	
	ImageCopyResampled($dst, $src, $posx, $posy, 0, 0, $x, $y, $new, $new);
	#imagefilter($dst, IMG_FILTER_MEAN_REMOVAL);
	#imagefilter($dst, IMG_FILTER_CONTRAST, 210);
	imageJPEG($dst, $dir."$nm", 99);
}

?>
