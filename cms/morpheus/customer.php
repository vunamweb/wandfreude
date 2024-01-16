<?php
session_start();
# # # # # # # # # # # # # # # # # # # # # # # # # # # 
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
#                                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # # 

global $kat_arr;

function show_doks ($user) {
	$sql = "SELECT * FROM morp_download_historie h LEFT JOIN morp_download d ON h.mdid=d.id WHERE h.benutzer=$user";
	$res = safe_query($sql);
	if ($res) {
		while($row = mysqli_fetch_object($res)) {
			#print_r($row);
			if ($row->pid) {
				$sq = "SELECT * FROM `morp_cms_pdf` WHERE pid=".$row->pid;
				$rs = safe_query($sq);
				$rw = mysqli_fetch_object($rs);
				if ($rw->pname) $echo .= '<p><span style="float:left; width: 300px;">'.$rw->pname.'&nbsp; </span><span>'. $row->dldate .'&nbsp;</span></p>';
			}
			// $echo .= '<p><span style="float:left; width: 300px;">'.$rw->pname.'&nbsp; </span><span>'. $row->dldate .'&nbsp;</span></p>';
		}
	}
	
	return $echo;
}


function filter ($kat="", $filter="filter", $katfilter="katfilter", $hidden="") {
	$sql = "SELECT * FROM morp_customer_kat WHERE 1";
	$res = safe_query($sql);

	$echo = '<form method="get" onsubmit="" name="'.$katfilter.'"><select name="'.$filter.'" onchange="document.'.$katfilter.'.submit();"><option value="">Auswahl</option>';
	
	while($row = mysqli_fetch_object($res)) {
		$echo .= '<option value="'.$row->kid.'"'. ($row->kid == $kat ? ' selected' : '') .'>'.$row->kategorie.'</option>';
	}
	
	$echo .= '</select>'.$hidden.'</form>';

	return $echo;
}

include("cms_include.inc");

# print_r($_REQUEST);

$arr 	= array("company"=>"Firma", "name"=>"Name", "usr"=>"Login Name", "pwd"=>"Passwort", "intern"=>"Mitarbeiter Peakom E-Mail");
// $arr 	= array("company"=>"Firma", "usr"=>"Login Name", "pwd"=>"Passwort");
$arr 	= array_flip($arr);

$neu 	= $_REQUEST["neu"];
$edit 	= $_REQUEST["edit"];
$save 	= $_REQUEST["save"];
$kat 	= $_REQUEST["filter"];
$del	= $_REQUEST["del"];
$delete	= $_REQUEST["delete"];
$deldatei 		= $_GET["deldatei"];
$deldateisure 	= $_GET["deldateisure"];
$savefreigabe	= $_REQUEST["savefreigabe"];
$freigabe 		= $_REQUEST["freigabe"];


$tab	= "morp_customer";
$spid	= "id";
$pfad 	= "../secure/dfiles/xRtewuZtgB/";

$col 	= array("FFFFFF","cccccc", "cde6fa");

echo '
<div id=content_big class=text>
<p><strong>Kunden Verwaltung</strong></p>
';

if ($del) {
	die("<p><font color=#ff0000><b>Wollen Sie den Kunden wirklich löschen?</b></font></p>
<p>&nbsp; &nbsp; &nbsp; <a href=\"?delete=$del\">" .ilink() ." ja</a> &nbsp; &nbsp; &nbsp; <a href=\"?\">" .backlink() ." nein</a></p></body></html>");
}

elseif ($delete) {
	$query = "delete from $tab where $spid=$delete";
	safe_query($query);
}

elseif ($deldatei) {
	$ord = $_GET["ord"];
	die("<p><font color=#ff0000><b>Wollen Sie die Datei &nbsp; <em><u>$deldatei</u></em> &nbsp; wirklich löschen?</b></font></p>
<p>&nbsp; &nbsp; &nbsp; <a href=\"?deldateisure=$deldatei&ord=$ord&edit=$edit\">" .ilink() ." ja</a> &nbsp; &nbsp; &nbsp; <a href=\"?edit=$edit\">" .backlink() ." nein</a></p></body></html>");
}

elseif ($deldateisure) {
	$ord = $_GET["ord"];
	$data = $pfad.$ord.'/'.str_replace(" ", "+", $deldateisure);
	unlink($data);
}

elseif($savefreigabe) {
# morp_download
# ay ( [filter] => [freigabe] => Greentowers_Mood.zip [savefreigabe] => 1 [edit] => 3 [ord] => schulze [dl] => Array ( [0] => 3 [1] => 4 ) [zuordnen] => zuordnen [PHPSESSID] => 
	$dl  = $_REQUEST["dl"];
	$dat = $_REQUEST["freigabe"];
	$pfd = $_REQUEST["ord"];

	foreach ($dl as $val) {	
		$sql = "SELECT id FROM morp_download WHERE benutzer=". $val." AND datei='$dat'";
		$res = safe_query($sql);			
		$dok[] = $val;
		
		if (mysqli_num_rows($res) > 0) {}
		else {
			$sql = "INSERT morp_download set benutzer=$val, pid=0, datei='". $dat."', pfad='". $pfd."', onceagain=1";	
			$res = safe_query($sql);			
		}
	}

	// ----- pruefen, ob zuviele doks zugewiesen sind	
	$sql = "SELECT benutzer FROM morp_download WHERE datei='$dat'";	
	$res = safe_query($sql);			
	$arr_chk = array();
	while($row = mysqli_fetch_object($res)) {
		$arr_chk[] = $row->benutzer;
	}
	$dok = array_diff($arr_chk, $dok);

	if (count($dok) > 0) {
		foreach($dok as $val) {
			$sql = "DELETE FROM morp_download WHERE datei='$dat' AND benutzer='". $val."'";	
			safe_query($sql);			
		}
	}	
	
	unset($freigabe);
}

elseif ($save) {
	foreach ($arr as $key=>$val) {
		$set .= "$val='". $_REQUEST[$val]."', ";	
	}
	
	if ($neu) 	$sql = "INSERT ";
	else 		$sql = "UPDATE ";
	
	$sql .= $tab." set ".substr($set, 0, -2);

	if ($edit) $sql .= " WHERE $spid=$edit";

	$save = 0;	
	if ($neu) {
		$save = mkdir($pfad.eliminiere($_REQUEST["usr"]));
	}
	else $save = 1;
	
	if ($save) $res = safe_query($sql);			
	else echo '<h2><u>Es ist ein fehler entstanden!!!!!</u></h2>';
	
	// -------------- dok zuweisung erstellen
	
/*
	if (!$neu) {
		$dok = array();
			
		foreach ($_POST as $key=>$val) {
			if (ereg("^dl_", $key)) {
				$sql = "SELECT id FROM morp_download WHERE benutzer=$edit AND datei='". $val."'";	
				$res = safe_query($sql);			
				$dok[] = $val;
				
				if (mysqli_num_rows($res) > 0) {}
				else {
					$sql = "INSERT morp_download set benutzer=$edit, datei='". $val."', onceagain=1";	
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
*/	
	unset($edit);
	unset($neu);
}


#  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  
#  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  
#  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  
#  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  
#  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  #  


$b = $freigabe ? $edit : 0;

echo '
'. ($edit ? '<p><a href="?edit='.$b.'&filter='.$kat.'">&laquo; zur&uuml;ck</a></p>' : filter($kat)) .'
<p>&nbsp;</p>
';



if ($freigabe) {
	///// NEW 2009-12-02
	////  KUNDEN Zuordnung

	// ?freigabe='.$file.'&edit='.$edit.'&ord='.eliminiere($row->usr).'
	
	$hidden = '<input type="hidden" name="freigabe" value="'.$freigabe.'"> <input type="hidden" name="savefreigabe" value="1"> <input type="hidden" name="edit" value="'.$edit.'"> <input type="hidden" name="ord" value="'.$_REQUEST["ord"].'">';

	echo "<h2 style=\"border-bottom: solid 1px #000; width:700px;\">Firmen</h2>".filter($kat, "filter", "katfilter", $hidden)."<p>&nbsp;</p>".
		"<form method=post name=freigabe>".$hidden;
	
	$col 	= array("FFFFFF","cccccc", "cde6fa");
	if ($kat) 	$sql  = "SELECT * FROM morp_customer c, morp_customer_zuord z WHERE c.id=z.custid AND z.kid=$kat GROUP BY company ORDER BY company, name";
	else 		$sql  = "SELECT * FROM morp_customer ORDER BY company, name";
	$res  	= safe_query($sql);
	$ct   	= 0;
	
	while($row = mysqli_fetch_object($res)) {
		$sql 	= "SELECT * FROM morp_download WHERE datei='$freigabe' AND benutzer=".$row->id;
		$rs 	= safe_query($sql);
		$c 		= mysqli_num_rows($rs);
		echo '<p><input type="checkbox" value="'. ($row->id) .'" name="dl[]"'. ($c > 0 ? ' checked' : '') .'> &nbsp; <u>'.$row->usr.'</u>, '.$row->company.'</p>';
	}

	echo '<input type="submit" name="zuordnen" value="zuordnen"></form>';
}

elseif ($edit || $neu) {
	echo '<form method="post"><table>';

	if ($edit) {
		$sql = "SELECT * FROM $tab WHERE $spid=$edit";
		$res = safe_query($sql);			
		$row = mysqli_fetch_object($res);
	}

	foreach ($arr as $key=>$val) {
		echo '<tr><td>'.$key.'</td><td>';
		if (preg_match("/^Text/", $key)) 		echo '<textarea cols="" rows="8" name="'.$val.'" style="width: 400px;">'.$row->$val.'</textarea>';
		elseif (preg_match("/^Land/", $key)) 	echo '<select name="'.$val.'">'.pulldown ($row->$val, "morp_haendler_land", "landde", "lid").'</select>';
		elseif ($val == "pwd")			echo '<input type="text" name="'.$val.'" value="'. ($neu ? setpw() : $row->$val) .'" style="width: 400px;">';
		elseif ($val == "usr" && $edit)	echo '<strong>'.$row->$val.'</strong>';
		elseif ($val == "usr")			echo 'Login Name kann nur einmalig gesetzt werden. Nicht veränderbar! <input type="text" name="'.$val.'" value="'.$row->$val.'" style="width: 400px;">';
		else 							echo '<input type="text" name="'.$val.'" value="'.$row->$val.'" style="width: 400px;">';
		echo '</td></tr>';	
	}
		
	echo '<tr><td>&nbsp;<br>
		<input type="hidden" name="neu" value="'.$neu.'"><input type="hidden" name="filter" value="'.$kat.'"><input type="hidden" name="edit" value="'.$edit.'">
		<input type="submit" class="button" value="speichern" name="save"></td></tr></table>
	<p>&nbsp;</p>
	'. ($neu ? '' : '<p><a href="customer_kat_zuord.php?custid='.$edit.'">&raquo; Firma zuweisen</a></p>') .'
	';
	
	if ($edit) echo '<p>E-Mail mit Zugangsdaten an den Kunden <a href="mailto:'.$row->usr.'?subject=Ihre Zugangsdaten zum Peakom Kundenbereich&body=%0D%0A%0D%0AIhre%20Zugangsdaten:%0D%0ABenutzername: '.$row->usr.'%0D%0APasswort: '.$row->pwd.'%0D%0A%0D%0AIhr direkter Link:%0D%0Ahttp://www.peakom.com/de/log+in+kundenservice/"><strong>&raquo; versenden</strong></a></p>
				<p>&nbsp;</p>'. show_doks($edit);
	
	echo '
	</form>';
	
	if (!$neu) {
		echo '<table width="500">';
		
		$ordner = $pfad.eliminiere($row->usr);
		$handle = opendir($ordner);
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				$size  = filesize($ordner.'/'.$file);
				$size  = round(($size/1024)/1000, 3);
				echo '<tr><td width="300"><a href="'.$ordner.'/'.$file.'" target="_blank">'.$file.', '.$size.' M</a></td>
					<td><a href="?deldatei='.$file.'&edit='.$edit.'&ord='.eliminiere($row->usr).'"><img src="images/delete.gif" alt="" width="9" height="10" border="0"></a></td>
					<td width="120"><a href="?freigabe='.$file.'&edit='.$edit.'&ord='.eliminiere($row->usr).'">Kunden Freigaben</a></td>
				</tr>';
		    }
		}
		closedir($handle);
		
		echo '</table>';
	}
}

else {
	#############################################################################################
	if ($kat) $sql  = "SELECT * FROM $tab c, morp_customer_zuord z WHERE c.id=z.custid AND z.kid=$kat ORDER BY company, name";
	else $sql  = "SELECT * FROM $tab ORDER BY company, name";
	$res  = safe_query($sql);
	$ct   = 0;

	/*
	echo '<form method="get">Kunden Nr.: <input type="text" name="knr" value="'.$knr.'" style="width: 50px;"> &nbsp; Name: <input type="text" name="name" value="'.$name.'" style="width: 50px;"> &nbsp; Land: <input type="text" name="land" value="'.$land.'" style="width: 50px;"> &nbsp; Distributor <input type="Checkbox" name="distr" value="1"'.($distr ? ' checked' : '').'> &nbsp;  &nbsp; Premium-H&auml;ndler <input type="Checkbox" name="prem" value="1"'.($prem ? ' checked' : '').'> &nbsp; <input type="submit" class="button" name="suchen" value="suchen"></form>';
	*/
	echo '<table width="600">
	<tr>';
	
	foreach ($arr as $key=>$val) {
		echo '<td><strong>'.$key.'</strong></td>';
	}

	echo '</tr>';
	
	while($row = mysqli_fetch_object($res)) {
		echo '
	<tr bgcolor='.$col[$ct].'>
	';
		foreach ($arr as $key=>$val) {
			$data = $row->$val;
			echo '<td>'.substr($data, 0, 30).'</td>';
		}
		echo '<td> &nbsp; <a href="?edit='.$row->id.'&filter='.$kat.'"><img src="images/stift.gif" alt="" width="9" height="9" border="0" hspace="10"></a></td>';
		echo $admin ? '<td> &nbsp; <a href="?del='.$row->id.'&filter='.$kat.'"><img src="images/delete.gif" alt="" width="9" height="9" border="0" hspace="10"></a></td>' : '';
		echo '</tr>';

		if ($ct == 0) $ct = 1;		//farbendefenition
		else $ct = 0;
	}
	
	echo '</table>';
	
	if ($admin) echo "&nbsp;<br>
		<a href=\"?neu=1\">".ilink()." Neuen Kunden hinzuf&uuml;gen</a><p>&nbsp;</p>
		";
	#############################################################################################
}
?>

<?
include("footer.php");
?>