<?php
# # # # # # # # # # # # # # # # # # # # # # # # # # # 
# www.pixel-dusche.de                               #
# bj�rn t. knetter                                  #
# start 12/2003                                     #
# edit 27.11.2006                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # # 

session_start();
#$box = 1;
include("cms_include.inc");

///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function pulldown ($tp, $tab, $wname, $wid, $gruppe=0, $spalte=0) {
	if ($gruppe) 	$query = "SELECT * FROM $tab WHERE $spalte=$gruppe ORDER BY $wname";
	else 			$query = "SELECT * FROM $tab ORDER BY $wname";
	
	// echo $query;
	
	$result = safe_query($query);

	while ($row = mysqli_fetch_object($result)) {
		if ($row->$wid == $tp) $sel = "selected";
		else $sel = "";
		
		$nm = $row->$wname;		
		$pd .= "<option value=\"" .$row->$wid ."\" $sel>$nm</option>\n";
	}
	return $pd;
}

// print_r($_REQUEST);

global $arr_form;

// NICHT VERAENDERN ///////////////////////////////////////////////////////////////////
$edit 	= $_REQUEST["edit"];
$delimg = $_REQUEST["delimg"];
$neu	= $_REQUEST["neu"];
$save	= $_REQUEST["save"];
$del	= $_REQUEST["del"];
$delete	= $_REQUEST["delete"];
$id		= $_REQUEST["id"];
///////////////////////////////////////////////////////////////////////////////////////


//// EDIT_SKRIPT
$um_wen_gehts 	= "Formate Proof";
$titel			= "Formate Proof";
///////////////////////////////////////////////////////////////////////////////////////


echo '<div id=vorschau>
	<h2>'.$titel.'</h2>

	'. ($edit || $neu ? '<p><a href="?">&laquo; zur&uuml;ck</a></p>' : '') .'	
	<form action="" onsubmit="" name="verwaltung" method="post">
';


$new = '<p><a href="?neu=1">&raquo; NEU</a></p>';

//// EDIT_SKRIPT
// 0 => Feldbezeichnung, 1 => Bezeichnung f&uuml;r Kunden, 2 => Art des Formularfeldes
$arr_form = array(
	array("name_de", "Bezeichnung deutsch", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("name_en", "Bezeichnung english", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("price", "Preis in &auml;", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("wvon", "von Breite in mm", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("width", "bis Breite in mm", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("hvon", "von H�he in mm", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("height", "bis H�he in mm", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
#	array("img", "Foto (228 x 162)", 'foto', 'image', 'imgname', 6, 'gid'),

);
///////////////////////////////////////////////////////////////////////////////////////


#	array("mberechtigung", "Berechtigung (ID: 1 = Zugang)", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
# 	array("ausbildungen", "<strong>Ausbildung EN</strong>", '<textarea cols="80" rows="5" name="#n#">#v#</textarea>'),
# 	array("imgid", "Berechtigung (ID: 1 = Zugang)", 'sel', 'image', 'imgname'),

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function liste() {
	//// EDIT_SKRIPT
	$db = "shop_article_prop1";
	$id = "p1id";
	$ord = "p1id";
	$anz = "name_de";
	$anz2 = "name_en";
	$anz3 = "price";
	////////////////////
	
	$echo .= '<p>&nbsp;</p><table width="100%" cellspacing="0" cellpadding="0">';

	$sql = "SELECT * FROM $db WHERE 1 ORDER BY ".$ord."";
	$res = safe_query($sql); 
		
	while ($row = mysqli_fetch_object($res)) {	
		$edit = $row->$id;
		$echo .= '<tr>
			<td width="600"><p><a href="?edit='.$edit.'">'.$row->$anz.' | <i>'.$row->$anz2.'</i> | <i>&auml; '.ger_p($row->$anz3).'</i></a></p></td>
			<td valign="top"><a href="?edit='.$edit.'"><img src="images/edit.gif" alt="" width="18" height="10" border="0"></a></td>
			<td valign="top"><a href="?del='.$edit.'"><img src="images/delete.gif" alt="" width="9" height="10" border="0"></a></td>
		</tr>';
	}
	
	$echo .= '</table><p>&nbsp;</p>';

	return $echo;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function edit($edit) {
	global $arr_form;
	
	//// EDIT_SKRIPT
	$db = "shop_article_prop1";
	$id = "p1id";
	/////////////////////

	$sql = "SELECT * FROM $db WHERE $id=".$edit."";
	$res = safe_query($sql); 
	$row = mysqli_fetch_object($res);
	
	$echo .= '<input type="Hidden" name="neu" value="'.$neu.'">
		<input type="Hidden" name="edit" value="'.$edit.'">
		<input type="Hidden" name="save" value="1">
	
	<table cellspacing="6">';

	$echo .= '<tr>
		<td></td>
	</tr>
';
		
	foreach($arr_form as $arr) {	
		if ($arr[2] == "sellan") {
			$echo .= '<tr><td>'.$arr[1].'</td><td><select name="lan">';
			$echo .= '<option value="1" '. ($row->lan == 1 ? ' selected' : '') .'>Deutsch</option>';
			$echo .= '<option value="2" '. ($row->lan == 2 ? ' selected' : '') .'>English</option>';			
			$echo .= '<option value="3" '. ($row->lan == 3 ? ' selected' : '') .'>Francais</option>';			
			$echo .= '</select></td></tr>';
		}
		elseif ($arr[2] == "sel") {
			$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'">'.pulldown ($row->$arr[0], $arr[3], $arr[4], $arr[0]).'</select></td></tr>';
		}
		elseif ($arr[2] == "text") {
			$echo .= '<tr><td>'.$arr[1].'</td><td>'.str_replace("#e#", $edit, $arr[3]).'</td></tr>';
		}
		elseif ($arr[2] == "foto") {
			// $echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'">'.pulldown ($row->$arr[0], $arr[3], $arr[4], $arr[0], $arr[5], $arr[6]).'</select></td></tr>';
			// if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$arr[0]);

			$echo .= '<tr><td width="160">'.$arr[1].'</td><td><input type=hidden name='.$arr[0].' value="' .$row->$arr[0].'" style="width:500px"><a href="image_folder_upload.php?wid='.$edit.'&imgid='.$arr[0].'&prod=1">';
	
			if ($row->$arr[0] && $arr[0] != "pdf") $echo .=  '<img src="../images/produkt/'.$row->$arr[0].'"></a> &nbsp; &nbsp; <a href="?delimg='.$arr[0].'&edit='.$edit.'"><img src="images/delete.gif" width="9" height="10" alt="Bild l�schen" border="0" hspace="6"></a>';
			elseif ($row->$arr[0]) $echo .=  '<a href="../pdf/'.$row->$arr[0].'" target="_blank">PDF</a> &nbsp; &nbsp; <a href="?delimg='.$arr[0].'&edit='.$edit.'"><img src="images/delete.gif" width="9" height="10" alt="Bild l�schen" border="0" hspace="6"></a>';
			else $echo .=  '<b>'.($arr[0]=="pdf" ? 'PDF' : 'Foto').'</b>: bitte w�hlen</a>';
			
			$echo .= '</td></tr>';

		}
		else $echo .= '<tr>
		<td>'.$arr[1].':</td>
		<td>'. str_replace(
					array("#v#", "#n#", "#s#", "#db#", '#e#', '#id#', '#s1#', '#s0#'), 
					array($row->$arr[0], $arr[0], 'width:400px;', $db2, $edit, $id2, $sel1, $sel2), 
			$arr[2]).'</td>
	</tr>';
	}
	
	$echo .= '<tr><td><td><input type=hidden name="image" value="' .$image .'" style="width:500px"></td></tr>';
	
	$echo .= '
	<tr>
		<td></td>
		<td><input type="submit" name="speichern" value="speichern"></td>
	</tr>
</table>';

	return $echo;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function neu() {
	global $arr_form;

	$x = 0;
	
	$echo .= '<input type="Hidden" name="neu" value="1"><input type="Hidden" name="save" value="1">
	
	<table cellspacing="6">';
		
	foreach($arr_form as $arr) {	
		if ($x <= 4) $echo .= '<tr>
			<td>'.$arr[1].':</td>
			<td>'. str_replace(array("#v#", "#n#", "#s#"), array($row->$arr[0], $arr[0], 'width:400px;'), $arr[2]).'</td>
		</tr>';
		$x++;
	}
	
	$echo .= '<tr>
		<td></td>
		<td><input type="submit" name="speichern" value="speichern"></td>
	</tr>
</table>';

	return $echo;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

if ($save) {
	global $arr_form;

	//// EDIT_SKRIPT
	$db = "shop_article_prop1";
	$id = "p1id";
	/////////////////////

	foreach($arr_form as $arr) {	
		$tmp = $arr[0];
		$val = $_POST[$tmp];

		if ($tmp != "region") $sql .= $tmp. "='" .$val. "', ";
	}
	
	$sql = substr($sql, 0, -2);	
	
	if ($neu) {
		$sql  = "INSERT $db set $sql";
		$res  = safe_query($sql);
		$edit = mysqli_insert_id($mylink);
		unset($neu);
	}
	else {
		$sql = "update $db set $sql WHERE $id=$edit";
		$res = safe_query($sql);
	}	
	// echo $sql;
	unset($edit);
}
elseif ($del) {
	die('<p>M&ouml;chten Sie den '.$um_wen_gehts.' wirklich l&ouml;schen?</p>
	<p><a href="?delete='.$del.'">Ja</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="?">Nein</a></p>
	');
}
elseif ($delete) {
	$sql = "DELETE FROM shop_article_prop1 WHERE p1id=$delete";
	$res = safe_query($sql);	
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

if ($neu) 		echo neu("neu");
elseif ($edit) 	echo edit($edit);
else			echo liste($id).$new;

echo '
</form>
';

include("footer.php");

?>
