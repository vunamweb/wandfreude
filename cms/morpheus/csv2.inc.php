<?php

function get_csv($data, $trenn, $one=0) {
	global $val_arr;
	
	$data = explode($trenn, $data);
	$arr = array();
	$x = 0;
	
	foreach($data as $val) {
		$sp = explode(";", $val);
		
		if($x < 1) {
			//print_r($sp);
			$i = 0;
			foreach($sp as $wert) {
				$val_arr[] = trim($wert);
				$nm = "v".$i;
				$$nm = trim($wert);
				$i++;
			}
		}
		else {
			$i = 0;
			$tmp_arr = array();
			foreach($sp as $wert) {
				$nm = "v".$i;
				$tmp_arr[$$nm] = ($wert);
				$i++;
			}
			$arr[] = $tmp_arr;
			
			if($one) return $arr;
		}
		$x++;
	}
	return $arr;
}	

function ersetze($wort) {
	$ersetzung = array(
		"Ÿ" => "ü",
		"š" => "ö",
		"§" => "ß",
		"Š" => "ä",
	);
	$wort = str_replace(array_keys($ersetzung), array_values($ersetzung), $wort);
	return $wort;
}

?>