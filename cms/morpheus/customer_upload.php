<?php
session_start();

if(isset($_COOKIE['enviro'])) {
	$SID  	= $_COOKIE['enviro'];
} else {	
	$SID  	= session_id();
	$tm 	= time() + (60*60*24*7*4);
	setcookie("enviro", $SID, $tm);
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # 
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
#                                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # # 

global $kat_arr;

function show_doks ($user) {
	$query  = "SELECT wname, wid FROM  morp_customer_wiegeschein WHERE custid=$user";
	$result = safe_query($query);
	$arr	= array();
	while ($row = mysqli_fetch_object($result)) {
		$echo .= '<p><a href="?del='.$row->wid.'&edit='.$user.'"><img src="images/delete.gif" alt="" width="9" height="10" border="0"></a> &nbsp; '.$row->wname.'</p>';
	}
	
	return $echo;
}


$multiupload = 1;
include("cms_include.inc");

echo "\n\n<div id=content_big class=text>\n";

$arr 	= array("schein"=>"Wiegeschein", "name"=>"Name", "usr"=>"Login Name", "pwd"=>"Passwort");
$arr 	= array_flip($arr);

$neu 	= $_REQUEST["neu"];
$edit 	= $_REQUEST["edit"];
$save 	= $_REQUEST["save"];
$del	= $_REQUEST["del"];
$delete	= $_REQUEST["delete"];

$tab	= "morp_customer_wiegeschein";
$spid	= "wid";

$col 	= array("FFFFFF","cccccc", "cde6fa");

if ($del) {
	die("<p><font color=#ff0000><b>Wollen Sie den Wiegeschein wirklich löschen?</b></font></p>
				<p>&nbsp; &nbsp; &nbsp; <a href=\"?delete=$del&edit=$edit\">" .ilink() ." ja</a> &nbsp; &nbsp; &nbsp; <a href=\"?edit=$edit\">" .backlink() ." nein</a></p></body></html>");
}

elseif ($delete) {
	$query = "SELECT wname FROM $tab WHERE $spid=$delete";
	$res   = safe_query($query);
	$row   = mysqli_fetch_object($res);
	unlink("../wiegeschein/xLx/ups24.Y/".$row->wname);

	$query = "delete from $tab where $spid=$delete";
	safe_query($query);
}

elseif ($save) {
	foreach ($arr as $key=>$val) {
		$set .= "$val='". $_REQUEST[$val]."', ";	
	}
	
	if ($neu) 	$sql = "INSERT ";
	else 		$sql = "UPDATE ";
	
	$sql .= $tab." set ".substr($set, 0, -2);

	if ($edit) $sql .= " WHERE $spid=$edit";

	$res = safe_query($sql);			
	
	// -------------- dok zuweisung erstellen
	
	if (!$neu) {
		$dok = array();
			
		foreach ($_POST as $key=>$val) {
			if (preg_match("/^dl_/", $key)) {
				$sql = "SELECT id FROM morp_download WHERE benutzer=$edit AND datei='". $val."'";	
				$res = safe_query($sql);			
				$dok[] = $val;
				
				if (mysqli_num_rows($res) > 0) {}
				else {
					$sql = "INSERT morp_download set benutzer=$edit, datei='". $val."'";	
					$res = safe_query($sql);			
				}
			}
		}
	
		// ----- pruefen, ob zuviele doks zugewiesen sind
		$sql = "SELECT datei FROM morp_download WHERE benutzer=$edit";	
		$res = safe_query($sql);			
		$arr_chk = array();
		while($row = mysqli_fetch_object($res)) {
			$arr_chk[] = $row->datei;
		}
		$dok = array_diff($arr_chk, $dok);
	
		if (count($dok) > 0) {
			foreach($dok as $val) {
				$sql = "DELETE FROM morp_download WHERE benutzer=$edit AND datei='". $val."'";	
				safe_query($sql);			
			}
		}	
	}
	
	unset($edit);
	unset($neu);
}







	echo '<p><a href="customer.php?edit='.$edit.'"><strong>&laquo; zur&uuml;ck</strong></a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="?edit='.$edit.'"><strong>Ansicht aktualisieren</strong></a></p>';
	
	echo '
		<div class="container">
		<p>&nbsp;</p>
		<h2>Wiegescheine uploaden</h2>

		<div>
			<form action="server/script.php" method="post" enctype="multipart/form-data" id="form-demo" name="'.$edit.'">
			<input type="radio" id="custid" value="'.$edit.'" style="visibility:hidden;">

		<div id="status" class="hide">
			<div>
				<strong class="overall-title"></strong><br />
				<img src="assets/progress-bar/bar.gif" class="progress overall-progress" />
			</div>
			<div>
				<strong class="current-title"></strong><br />
				<img src="assets/progress-bar/bar.gif" class="progress current-progress" />
			</div>
			<div class="current-text"></div>
			<p>&nbsp;</p>
			<p>
				<a href="#" id="browse" style="border: solid 1px #626562; margin-right: 10px; padding: 2px 6px 2px 6px; background:#f5f5f5;">PDF ausw&auml;hlen</a> 
				<a href="#" id="upload" style="border: solid 1px #626562; margin-right: 40px; padding: 2px 6px 2px 6px; background:#f5f5f5;"><strong>&raquo; Start Upload</strong></a>
				<a href="#" id="clear">Upload Liste l&ouml;schen</a> 
			</p>
		</div>

	<ul id="list"></ul>

	<fieldset id="fallback">
	</fieldset>
</form>		</div>


	</div>
	';
	
	echo '<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
	<form method="post"><h2>Vorhandene Wiegeschein zum Download</h2><table>';
	if ($edit) echo show_doks($edit);
	echo '
	</form>';

?>

<?
include("footer.php");
?>