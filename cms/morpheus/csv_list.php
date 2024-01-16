<?php
# # # # # # # # # # # # # # # # # # # # # # # # # # # 
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
# start 12/2003                                     #
# edit 27.11.2006                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # # 

session_start();
#$box = 1;
//error_reporting(E_ALL);
include("cms_include.inc");
include('csv2.inc.php');

global $val_arr;

// print_r($_REQUEST);
?>
<style>
* {
	font-family: arial;
	font-size: 10px;
}
</style>

<div id=vorschau>
<?php 
	$edit = $_GET["edit"];
?>
	<table border="1">
<?php 
	//$vt = $_GET["vt"];	
	$vt = "APOWebExport.csv";
	$csv = read_data('../import/'.$vt);
	$csv = get_csv($csv, "\n");
	// print_r($csv);
	$a = 0;
	
	$head = '<tr><td></td>';
	
	foreach($val_arr as $val) {
		$head .= '<td><b>'.$val.'</b></td>';
	}
	
	echo $head .= '</tr>';
	
	$b = 0;
	
// firma;strasse;plz;ort;email;internet;anrede
	foreach($csv as $val) {
		$a++;
		$b++;
		
		$sp = explode(",", $val);
//		print_r($val);
		
		if($b == 10) {
			$b = 0;
			echo $head;
		}
		echo '<tr><td>'.$a.'</td>';
	
		foreach($val_arr as $get) {
			echo '<td>'.$val[$get].'</td>';
		}
		
		echo '</tr>';
	}

?>
	</table>
</div>

</body>
</html>