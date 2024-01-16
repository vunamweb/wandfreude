<?php
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
# start 12/2003                                     #
# edit 27.11.2006                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # #

/*
CREATE TABLE  `morp_faq` (
`faqid` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`faq_hl` VARCHAR( 255 ) NOT NULL ,
`faq_text` TEXT NOT NULL ,
`sichtbar` INT( 1 ) NOT NULL ,
`reihenfolge` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;
*/

session_start();
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

function pfad ($feld, $tab, $wname, $id) {
	$sql = "SELECT * FROM $tab WHERE $feld=$id";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);
	$nm  = $row->$wname;

	return $nm;
}

///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////


# print_r($_REQUEST);

global $arr_form;

// NICHT VERAENDERN ///////////////////////////////////////////////////////////////////
$edit 	= $_REQUEST["edit"];
$delimg = $_REQUEST["delimg"];
$neu	= $_REQUEST["neu"];
$save	= $_REQUEST["save"];
$del	= $_REQUEST["del"];
$delimg	= $_REQUEST["delimg"];
$delete	= $_REQUEST["delete"];
$id		= $_REQUEST["id"];

///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
$qs		= $_GET["qs"];
$col	= $_GET["col"];
$setval	= $_GET["val"];
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////


//// EDIT_SKRIPT
$um_wen_gehts 	= "FAQ";
$titel			= "FAQ";
///////////////////////////////////////////////////////////////////////////////////////


$new = '<p><a href="?neu=1" class="fa fa-plus"> NEU</a></p>';

echo '<div id=vorschau>
	<h2>'.$titel.'</h2>

	'. ($edit || $neu ? '<p><a href="?pid='.$pid.'">&laquo; zur&uuml;ck</a></p>' : '') .'
	<form action="" onsubmit="" name="verwaltung" method="post">
'.($edit || $neu ? '' : $new);



//// EDIT_SKRIPT
// 0 => Feldbezeichnung, 1 => Bezeichnung für Kunden, 2 => Art des Formularfeldes
$arr_form = array(
	array("reihenfolge", "Position", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
//	array("sichtbar", "Sichtbar", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("faq_hl", "Headline", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
	array("faq_text", "Text", '<textarea cols="" rows="18" name="#n#" style="#s#">#v#</textarea>'),
//	array("block1", "Text lang / Beschreibung", '<textarea cols="" rows="10" name="#n#" style="#s#">#v#</textarea>'),
//	array("block2", "Text Details", '<textarea cols="" rows="5" name="#n#" style="#s#">#v#</textarea>'),
	///// DROPDOWNMEMU :))  HINWEIS: FELDNAME DER ZIELTABELLEN ID MUSS IDENTISCH SEIN
//	array("sid", "Art / Region", 'sel', 'morp_immo_stadt', 'stadt'),
//	array("gnid", "Bildergalerie", 'sel', 'morp_cms_galerie_name', 'gnname', 1, 'ggid'),
//	array("img1", "Foto klein / Übersicht<br>68px x 68px", 'foto', 'image', 'imgname', 6, 'gid'),


);
///////////////////////////////////////////////////////////////////////////////////////


#	array("mberechtigung", "Berechtigung (ID: 1 = Zugang)", '<input type="Text" value="#v#" name="#n#" style="#s#">'),
# 	array("ausbildungen", "<strong>Ausbildung EN</strong>", '<textarea cols="80" rows="5" name="#n#">#v#</textarea>'),
# 	array("imgid", "Berechtigung (ID: 1 = Zugang)", 'sel', 'image', 'imgname'),

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function liste() {
	//// EDIT_SKRIPT
	$db = "morp_faq";
	$id = "faqid";
	$ord = "reihenfolge";
	$anz = "faq_hl";
	$anz2 = "reihenfolge";
	$anz3 = "sichtbar";

	////////////////////

	$echo .= '<p>&nbsp;</p><table class="autocol p20">';

	$sql = "SELECT * FROM $db WHERE 1 ORDER BY ".$ord."";
	$res = safe_query($sql);

	while ($row = mysqli_fetch_object($res)) {
		$edit = $row->$id;
		$echo .= '			<tr>
			<td valign="top" width="50" align="center"><a href="?qs='.$edit.'&amp;col=sichtbar&amp;val='.($row->$anz3 ? '0' : '1').'"><i class="fa '.($row->$anz3 ? ' fa-eye' : ' fa-eye-slash').'"></i></a></td>
			<td valign="top" width="50" align="center"><a href="?edit='.$edit.'"><i class="fa fa-pencil-square-o"></i></a></td>
			<td width="600"><p><a href="?edit='.$edit.'">'.$row->$anz2.' &nbsp; | &nbsp; <strong>'.$row->$anz.'</strong></a></p></td>
			<td valign="top" width="50" align="center"><a href="?del='.$edit.'"><i class="fa fa-trash-o"></i></a></td>
		</tr>
';
	}

	$echo .= '</table><p>&nbsp;</p>';

	return $echo;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function edit($edit) {
	global $arr_form;

	//// EDIT_SKRIPT
	$db = "morp_faq";
	$id = "faqid";
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
		$get = $arr[0];
		if ($arr[2] == "sellan") {
			$echo .= '<tr><td>'.$arr[1].'</td><td><select name="lan">';
			$echo .= '<option value="1" '. ($row->lan == 1 ? ' selected' : '') .'>Deutsch</option>';
			$echo .= '<option value="2" '. ($row->lan == 2 ? ' selected' : '') .'>English</option>';
			$echo .= '<option value="3" '. ($row->lan == 3 ? ' selected' : '') .'>Francais</option>';
			$echo .= '</select></td></tr>';
		}
		elseif ($arr[2] == "foto") {
			$echo .= '<tr><td width="160">'.$arr[1].'</td><td><input type=hidden name='.$arr[0].' value="' .$row->$get.'" style="width:500px"><a href="image_folder_upload.php?faqid='.$edit.'&tn='.$morpheus["img_size_news_tn"].'&imgid='.$arr[0].'&objekt=1">';

			if ($row->$get) $echo .=  '<img src="../images/objekte/'.$row->$get.'"></a> &nbsp; &nbsp; <a href="?delimg='.$arr[0].'&edit='.$edit.'"><img src="images/delete.gif" width="9" height="10" alt="Bild löschen" border="0" hspace="6"></a>';
			else $echo .=  '<b>Foto</b>: bitte wählen</a>';

			$echo .= '</td></tr>';

		}
		elseif ($arr[2] == "sel") {
			$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'">'.pulldown ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6]).'</select></td></tr>';
			if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
		}
		elseif ($arr[2] == "text") {
			$echo .= '<tr><td>'.$arr[1].'</td><td>'.str_replace("#e#", $edit, $arr[3]).'</td></tr>';
		}
		else $echo .= '<tr>
		<td>'.$arr[1].':</td>
		<td>'. str_replace(
					array("#v#", "#n#", "#s#", "#db#", '#e#', '#id#', '#s1#', '#s0#'),
					array(stripslashes($row->$get), $arr[0], 'width:500px;', $db2, $edit, $id2, $sel1, $sel2),
			$arr[2]).'</td>
	</tr>';
	}

	if ($image) $echo .= '<tr><td></td><td><img src="../images/userfiles/image/' .$image .'" /></td></tr>';

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
		$get = $arr[0];
		if ($x <= 5) $echo .= '<tr>
			<td>'.$arr[1].':</td>
			<td>'. str_replace(array("#v#", "#n#", "#s#"), array($row->$get, $arr[0], 'width:400px;'), $arr[2]).'</td>
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
	$db = "morp_faq";
	$id = "faqid";
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

}
elseif ($del) {
	die('<p>M&ouml;chten Sie den '.$um_wen_gehts.' wirklich l&ouml;schen?</p>
	<p><a href="?delete='.$del.'">Ja</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="?">Nein</a></p>
	');
}
elseif ($delete) {
	$sql = "DELETE FROM morp_faq WHERE did=$delete";
	$res = safe_query($sql);
}
elseif ($delimg) {
	$sql = "UPDATE morp_faq set $delimg='' WHERE faqid=$edit";
	$res = safe_query($sql);
}

if ($qs) {
	$sql = "UPDATE morp_faq set $col='$setval' WHERE faqid=$qs";
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
