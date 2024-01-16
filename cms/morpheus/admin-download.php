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
	
function reorganize($bereich) {
	$query = "SELECT * FROM ec_dokumente where bereich=$bereich order by art, prior";
	$result = safe_query($query);
	# echo mysqli_num_rows($result);
	$sort_arr = array();
	$art = "";
	
	while ($row = mysqli_fetch_object($result)) {
	 	if ($row->art) $sort_arr[$row->id] = $row->art;
	}
	# print_r($sort_arr);
	
	$x = 0;
	foreach ($sort_arr as $id => $art_tmp) {
		if ($art != $art_tmp) {
			$art = $art_tmp;
			$x = 1;
		}
		else $x++;
		
		$query = "update ec_dokumente set prior=$x where id=$id";
		# echo "<br>";
		$result = safe_query($query);
	}
}

$bereich = $_GET["bereich"];
if (empty($bereich)) $bereich = "1";

if ($_GET["reorg"]) { reorganize($bereich); }

echo '<form action="admin-download.php" method="get" class="text" name="auswahl">
		<div id="vorschau" class="text">
		<img src="images/leer.gif" width="400" height="6" alt="">';

$sql = "SELECT * FROM ec_rechnungnr where rStelle=1";
$ergebnis = safe_query($sql);

echo "<table cellpadding=6><tr><td><select name='bereich' onChange=\"document.auswahl.submit()\">";

if ($ergebnis) {
	while ($row = mysqli_fetch_array($ergebnis))	{
		$val = $row["art"];	
		$rWert = $row["rWert"];	
		if ($bereich == $rWert) {
			$bereich_desc = $val;
			echo "<option value='$rWert' selected>$val</option>\n";
		}
		else echo "<option value='$rWert'>$val</option>\n";
	}
}
echo "</select></td>
		<td><!-- " .ilink() .' <a href="upload.php?bereich=' .$bereich .'">neuen Datensatz per UPLOAD erstellen</a><br> -->'
.ilink() .' <a href="download-edit.php?new=1&bereich=' .$bereich .'">neuen Datensatz erstellen</a>' ."</td>
	</tr>
	<tr>
		<td colspan=2><b>Dokumente für den Download bearbeiten</b></td>
	</tr>
	</table>"; //<input type=Submit value=go><p>";
?>
</form>
</div><div id="content">
		
<?

echo "<p><a href=\"admin-download.php?reorg=1&bereich=$bereich\">" .ilink() ." dokumenten <b>reihenfolge</b> reorganiseren</a></p>
<table cellpadding=5 cellspacing=1 border=0 class=text>";

$col = array("#FFFFFF","#EFECEC");
$ct 			= 0;
$prior			= 0;
$tmp_art		= "";

$sql = "SELECT * FROM ec_dokumente where bereich = $bereich order by art,prior";
$ergebnis = mysql_query($sql) or die(mysql_error());

if ($ergebnis) {

	while ($row = mysqli_fetch_array($ergebnis))	{	
		$id 		= $row["id"];
		$art 		= $row["art"];
		if ($tmp_art != $art) {
			echo "<tr><td colspan=5><p style=\"margin: 10px 0px 0px -4px; background-color: #7B1B1B; height: 16px; color: #fff; font-weight:bold;\">&nbsp; $art</p></td></tr>";
			$prior	= 0;			
		}
		$prior		= $prior + 1;

		echo "<tr bgcolor=$col[$ct]>
			<td valign=top>" .$row["prior"] ."</td>
			<td valign=top><a href=\"download-edit.php?bearbeiten=$id&bereich=$bereich\">" .$row["headline"] ."</a><br>
			<i><font size=-3>" .substr($row["name"],0,28) ."</font></i></td>	
			<!-- <td valign=top>" .$row["bereich"]  ."</td> -->	
			<td valign=top>" .substr($row["text"],0,56)     ."...</td>	
			<td valign=top valign='middle' width=40>";
		
		if ($prior > 1 && $art) echo "<a href=\"prior.php?up=$prior&art=$art&bereich=$bereich&id=$id\"><img src='images/up.gif' width=9 height=9 border=0 alt='up' hspace='2'></a>";
		
		if ($art) echo "<a href=\"prior.php?down=$prior&art=$art&bereich=$bereich&id=$id\"><img src='images/down.gif' width=9 height=9 border=0 alt='down' hspace='2'></a>";
		
		echo "</td>
			<td valign=top width=20><a href=\"download-save.php?del=$id&bereich=$bereich\"><img src=\"images/delete.gif\" width=\"9\" height=\"10\" alt=\"\" border=0></a> &nbsp; </td>";

		if ($ct == 0) $ct = 1;		//farbendefenition
		else $ct = 0;
		
		$tmp_art = $art;
	}
}
 
echo '</table>


<p>&nbsp;</p>
<p>&nbsp;</p>
<p><a href="upload.php?bereich=' .$bereich .'">'.ilink().' neuen Datensatz per UPLOAD erstellen</a></p>
<p><a href="download-edit.php?new=1&bereich=' .$bereich .'">'.ilink().' neuen Datensatz erstellen</a></p>
<p>&nbsp;</p>';

include("footer.php");
?>