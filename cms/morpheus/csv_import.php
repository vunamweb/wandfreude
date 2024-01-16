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

 mail('xacffm@gmx.de', 'CSV Import // Apothekerkammer', 'CSV Import // START');

include_once ("../nogo/config.php");
include_once("../nogo/funktion.inc");
include_once("../nogo/db.php");
include('csv2.inc.php');

dbconnect();


global $val_arr;

// print_r($_REQUEST);
?>

<div id=vorschau>

<?php
	$table 	= "morp_kunden";
	$getid 	= "kid";
	$mnr	= "mnr";

	//$vt = $_GET["vt"];
echo	$vt = "APOWebExport.csv";
	$csv = read_data('../import/'.$vt);
	$csv = get_csv($csv, "\n");
	//echo "<pre>";
	$tmp = print_r($csv,1);
	//echo "</pre>";

	//********** UPDATE Table inlist = 0 - pruefe ob Mitglied noch aktiv **********/

if($csv) {
	 mail('xacffm@gmx.de', 'CSV Import // Apothekerkammer', 'CSV Daten // START'.substr($tmp,0,100) );

	$sql = "UPDATE $table set inlist=0 WHERE 1";
	$res = safe_query($sql);


	//********** Lese CSV aus **********/
	foreach($csv as $val) {

		//********** Mitgliedernummer **********/
		$mitglied = $val["DebNr"];

		$sql = "SELECT * FROM $table WHERE $getid='$mitglied'";
		$res = safe_query($sql);
		$ct  = mysqli_num_rows($res);

		//********** Mitglieder bereits in DB **********/
		if($ct > 0) {
			$newEntry = "";
			foreach($val_arr as $get) {
				if($get == "DebNr" || $get == "PNr" || preg_match("/PNr/", $get)  || $get == "Nachname" || $get == "Vorname" || $get == "KM1" || $get == "eMail") {}
				else $newEntry .= strtolower($get)."='".addslashes($val[$get])."', ";
			}
			$sql = "UPDATE $table set $newEntry inlist=1 WHERE $getid='$mitglied'";
			$res = safe_query($sql);
		}
		else {
			$newEntry = "";
			foreach($val_arr as $get) {
				if($get == "DebNr" || $get == "PNr" || preg_match("/PNr/", $get)) {}
				else $newEntry .= strtolower($get)."='".addslashes($val[$get])."', ";
			}
			$sql = "INSERT $table set $newEntry $getid='$mitglied', inlist=1";
//			echo "<br><br>".$sql."<br>";

			$res = safe_query($sql);
		}

	}
}
echo "FERTIG  - ".time();
 mail('xacffm@gmx.de', 'CSV Import // Apothekerkammer', 'CSV Import // ENDE ---- '.time());

?>
</div>

</body>
</html>