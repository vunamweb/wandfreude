<?php
session_start();
# # # # # # # # # # # # # # # # # # # # # # # # # # # 
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
# start 12/2003                                     #
#                                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # # 

include("cms_include.inc");

$bereich 	= $_REQUEST["bereich"];
$del  		= $_REQUEST["delete"];
$delete		= $_REQUEST["del"];

echo "<div id=vorschau>";

if ($del && $admin) {
	$nm  = $_REQUEST["nm"];
	echo '<p>&nbsp;</p>
		<p>Sind Sie sich sicher, dass sie den Dozenten löschen wollen?</p>
		<p>&nbsp; &nbsp; &nbsp; &nbsp; <a href="?del=' .$del .'&bereich='.$bereich.'">Ja</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="?">Nein</a></p></body></html>';
	die();
}

elseif ($delete) {
	$query = "delete from ec_dozenten WHERE dozentID=$delete";
	$result = safe_query($query);
	protokoll($uid, 'ec_dozenten', $delete, "del");
	$query = "delete from ec_doz_fb WHERE dozentID=$delete";
	$result = safe_query($query);
	protokoll($uid, 'ec_doz_fb', $delete, "del");
}


echo '<form action="admin-dozenten.php" method="post" class="text" name="auswahl">';
echo "<img src=\"images/leer.gif\" width=1 height=40> bereich: <select name='bereich' onChange=\"document.auswahl.submit()\" class=text>";

# $sql 		= "SELECT * FROM rechnungnr where rStelle=1";
$sql 		= "SELECT * FROM ec_fachbereiche f, ec_rechnungnr r WHERE f.rWert=r.rWert";
$ergebnis 	= mysql_query($sql) or die(mysql_error());

if ($ergebnis) {
	echo "<option value=''>alle</option>\n";
	
	while ($row = mysqli_fetch_array($ergebnis))	{
		# $val = $row["art"];	
		$val = $row["art"] ." | ".$row["fb"];	
		# $rWert = $row["rWert"];	
		$rWert = $row["fbid"];	
		if ($bereich == $rWert) {
			$bereich_desc = $val;
			echo "<option value='$rWert' selected>$val</option>\n";
		}
		else echo "<option value='$rWert'>$val</option>\n";
	}
}

echo "</select>\n\n";

$sorted = $_REQUEST["sorted"];
if (!$sorted) $sorted = "name";

if ($sorted == "name") $nm_sel = "selected";
else $id_sel = "selected";

echo '&nbsp; &nbsp; sortierung: <select name="sorted" class="text" onChange="document.auswahl.submit()">
			<option value="">bitte wählen</option>
			<option value="name" '.$nm_sel.'>name</option>
			<option value="dozentID" '.$id_sel.'>id</option>
		</select>&nbsp; &nbsp; <input type=Submit value=go class=text>
	</form>
';

echo "<p><a href=\"dozenten-edit.php?new=1&bereich=$bereich\">" .ilink() ." neuen Datensatz erstellen</a></div><div id=content>";
?>

<table cellpadding=5 cellspacing=0 border=0 class=text>
		
<?
$col = array("#FFFFFF","#EFECEC");
$ct  = 0;

if ($bereich) {
	# $sql 		= "SELECT * FROM dozenten where bereich like '%$bereich%' order by $sorted";
	$sql 		= "SELECT * FROM ec_dozenten d, ec_doz_fb df WHERE df.fbid=$bereich AND df.dozentID=d.dozentID AND df.aktiv=1 ORDER BY $sorted";
}
else $sql = "SELECT * FROM ec_dozenten where 1 order by $sorted";

	$ergebnis 	= mysql_query($sql) or die(mysql_error());
	
	if ($ergebnis) {
	
		while ($row = mysqli_fetch_array($ergebnis))	{	
			echo "<tr bgcolor=$col[$ct]>
				<td valign=top width=60 align=center><!-- <input type=hidden name='bereich' value=" .$row['bereich'] .">
					<input type=hidden name='dozentID' value=" .$row['dozentID'] ."> -->
					<a href=\"dozenten-edit.php?bearbeiten=" .$row["dozentID"] ."&bereich=$bereich\">" .$row['dozentID'] ."</a></td>";
			echo "\n\t<td valign=top width=350><b>" .$row['name'] ."</b>, " .$row['titel1'] ." " .substr($row['titel2'],0,20) ." " .$row['vorname'] ."</td>";
			
			$aktiv = $row['aktiv'];
			if ($aktiv == "2")	echo "<td valign=top>nicht aktiv</td>\n\t";
			else echo "<td valign=top><b>aktiv</b></td>\n\t";
			
			echo '<td valign=top width="50" align="right"><a href="?delete=' .$row["dozentID"] .'&bereich='.$bereich.'"><img src="images/delete.gif" alt="" width="9" height="10" border="0"></a></td>';

			if ($ct == 0) $ct = 1;		//farbendefenition
			else $ct = 0;
		}
	}

 
echo '</table>';

?>
 
</font>
<?
include("footer.php");
?>