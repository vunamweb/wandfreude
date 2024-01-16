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

echo '<div id=vorschau class=text>&nbsp;<p><form action="admin-seminar.php" method="get" class="text" name="auswahl">';

$bereich 	= $_REQUEST["bereich"];
$del		= $_REQUEST["del"];
$akt		= $_REQUEST["akt"];
if (!$order = $_REQUEST["order"]) $order = "prior";
if (!$rf = $_REQUEST["rf"]) $rf = "DESC";

if ($del) {
	echo "<p>Wollen Sie wirklich alle Inhalte des Seminars <b>$del</b> l&ouml;schen?</p>
	<p>Die Seminare werden auch auf dem <b>LIVE-Server</b> sofort <b>gel&ouml;scht</b>.</p>
	<p>Die Daten k&ouml;nnen <b>nicht</b> wieder hergestellt werden!</p>
	<p>&nbsp; &nbsp; &nbsp; &nbsp; <a href=\"seminar-save.php?delete=$del&bereich=$bereich\">".ilink()." ja</a>&nbsp; &nbsp; &nbsp; &nbsp; <a href=\"admin-seminar.php?bereich=$bereich\"><b>".ilink()." nein</b></a></p>";
	die("</body></html>");
}


$sql 	 	= "SELECT * FROM ec_rechnungnr where rStelle=1 order by rWert";
$ergebnis 	= mysql_query($sql) or die(mysql_error());

echo "<select name='bereich' onChange=\"document.auswahl.submit()\">";

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

echo "</select>&nbsp; &nbsp; &nbsp; <a href='seminar-edit.php?new=1&lastprior=$akt_prior&bereich=$bereich'>" .ilink() ." neuen Datensatz erstellen</a><p>&nbsp;<p>"; //<input type=Submit value=go><p>";
echo "<font class=text><b>Seminare $bereich_desc</b> <!-- &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; -- <a href=\"?bereich=$bereich&order=prior&rf=DESC\">Standard Sortier-Reihenfolge</a> -- -- <a href=\"?bereich=$bereich&order=prior&akt=1\">Sortierung aktualisieren</a> -- --><p>";
?>

</form>

</div><div id=content class=text>
<table cellpadding=0 cellspacing=1 border=0 width="100%">
	<tr bgcolor="A32E1D">
		<td style="color:#FFFFFF;" class="td_abst weiss"><a href="?bereich=<?php echo $bereich; ?>&order=rNr&rf=<?php echo $rf == "DESC" ? "ASC" : "DESC"; ?>">RechNr*</a></td>
		<td style="color:#FFFFFF;" class="td_abst weiss"><a href="?bereich=<?php echo $bereich; ?>&order=pdf&rf=<?php echo $rf == "DESC" ? "ASC" : "DESC"; ?>">PDF*</a></td>
		<td style="color:#FFFFFF;" class="td_abst weiss"><a href="?bereich=<?php echo $bereich; ?>&order=pdf&rf=<?php echo $rf == "DESC" ? "ASC" : "DESC"; ?>">ICS*</a></td>
		<td style="color:#FFFFFF;" class="td_abst weiss"><a href="?bereich=<?php echo $bereich; ?>&order=start&rf=<?php echo $rf == "DESC" ? "ASC" : "DESC"; ?>">Datum von/bis*</a></td>
		<td style="color:#FFFFFF;" class="td_abst" align="center">Sichtbar</td>
		<td style="color:#FFFFFF;" class="td_abst">Flie&szlig;text</td>
		<td colspan="3">&nbsp;</td>
	</tr>
		
<?
$col 	= array("#FFFFFF","#EFECEC");
$ct 	= 0;
$prior	= 0;

if ($bereich) {
	$sql = "SELECT * FROM ec_seminar where bereich=$bereich order by ". ($order ? $order : '') ." $rf";
	$ergebnis = mysql_query($sql) or die(mysql_error());
	
	if ($ergebnis) {
		while ($row = mysqli_fetch_array($ergebnis))	{	
			// print_r($row);
			$prior		= $prior + 1;
			$rNr		= $row['rNr'];
			$akt_prior	= $row['prior'];			
			$pdf		= $row['pdf'];
			$ics		= $row['pdf2'];
			if (!$pdf) $pdf = "fehlt";
			else $pdf = "";
			if (!$ics) $ics = "fehlt";
			else $ics = "";
			
			$start = euro_dat($row['start']);
			$ende  = euro_dat($row['ende']);

			echo "<tr bgcolor=$col[$ct]>
				<td valign=top style=\"height:22px;\" class=\"td_abst\">
					<input type=hidden name='bereich' value=" 	.$row['bereich'] .">
					<input type=hidden name='rWert' value=" 	.$row['rWert'] ."><a href=\"seminar-edit.php?bereich=" 	.$row['bereich'] ."&rWert=" .$row['rWert'] ."&bearbeiten=" .$row['rNr'] ."&order=$order&rf=$ref\">" .$row['rNr'] ."</a></td>
				<td valign=top class=\"td_abst\">$pdf</td>
				<td valign=top class=\"td_abst\">$ics</td>
				<td valign=top class=\"td_abst\">$start - $ende</td>	
				<td valign=top align=center class=\"td_abst\">";
				
			if ($row['aktiv'] == "on") echo "On";
			else echo "<b>Off</b>";

			echo "</td>
				<td valign=top class=\"td_abst\">" .$row['beschreibung'] ."</td>
				<td>";

			if ($prio > 1 && $order == "prior") echo "<a href=\"priorS.php?up=$prior&bereich=$bereich&rNr=$rNr\"><img src='images/up.gif' width=9 height=9 border=0 alt='up' hspace='2'></a>";
		
			if ($order == "prio") echo "<a href=\"priorS.php?down=$prior&bereich=$bereich&rNr=$rNr\"><img src='images/down.gif' width=9 height=9 border=0 alt='down' hspace='2'></a>";
			
			echo "</td>
				<td><!-- $akt_prior --></td>
				<td class=\"td_abst\"><a href=\"admin-seminar.php?del=$rNr&bereich=$bereich&order=$order&rf=$ref\">löschen</a></td>
				</tr>";

			if ($ct == 0) $ct = 1;		//farbendefenition
			else $ct = 0;
		}
	}
	echo "</table>
			<!-- <a href='seminar-edit.php?new=1&lastprior=$akt_prior&bereich=$bereich'>neuen Datensatz erstellen</a> -->";
}
?>
 
</font>
<?
include("footer.php");
?>