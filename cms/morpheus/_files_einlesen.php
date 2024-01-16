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
$box = 1;
include("cms_include.inc");

$edit	= $_REQUEST["edit"];
$show	= $_REQUEST["show"];
$save	= $_REQUEST["save"];
$imgid	= $_REQUEST["imgid"];

$db 	= "morp_cms_content";
$getid 	= "cid";


# wenn bild in content eingesetzt wird
$stelle = $_REQUEST["stelle"];
$imglnk = $_REQUEST["imglnk"];
$navid  = $_REQUEST["navid"];
$db		= $_REQUEST["db"];
$art	= $_REQUEST["art"];
if ($navid)  $incl_lnk = "content_edit.php?db=$db&stelle=$stelle&edit=$navid&art=$art";


$back = $_GET["back"];

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# neu gewaehltes deko-bild wird in db geschrieben
if ($imgid) {
	$inr = 1;
	$query = "UPDATE `morp_cms_content` set img" .$inr ."=$imgid, edit=1 where cid=$edit";
	$result = safe_query($query);

echo '
<script type="text/javascript">
<!--
		parent.parent.GB_hide();
 -->
</script>
';

}

// content_edit.php?db=content&stelle=4&edit=4&art=&imgid=21&back=ebene;:;1;;p_0;:;0;;n_0;:;Hauptnavigation&db=content&imglnk=1
// content_foto.php?edit=&db=content&back=ebene;:;1;;p_0;:;0;;n_0;:;Hauptnavigation

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
	$dir 	 = '../pdf/lak/';
	echo "<p>Bitte auf das gew&uuml;nschte Bild klicken,  ".$incl_lnk."</p>

	<p><a href=\"".$incl_lnk."&back=".$back."\">&laquo; zur&uuml;ck</a></p>";

	$ord = opendir($dir);
	$arr = array();

	while ($name = readdir($ord)) {
		if ($name != "." && $name != ".." && !preg_match("/.db/", $name)) $arr[] = $name;
	}
	sort ($arr);

	foreach ($arr as $val) {
		$id = explode(".", $val);
		$ft = date ("Y-m-d", fileatime($dir.$val));
		$ft2 = date ("Y-m-d", getPDFts($dir.$val));
		$cut = splitname($val, "_");
		echo "<p><a href=\"".$incl_lnk."&back=".$back."&imgnm=".$val."\">$val _____ $cut</a></p>------------------------------<p>";
	}


?>

</div>

<?
include("footer.php");

function splitname($nm, $split) {
	$nm = splitnm($nm, '.');;
	$nm = explode($split,$nm);
	$nm1 = $nm[2];

	if (preg_match("/-/", $nm1)) $nm1 = splitnm2($nm1, '-');;

	if ($nm1 > 1000) $nm1 = "jahr: ".$nm1.' #### ';
	elseif (preg_match("/j/", $nm1)) $nm1 = "monat: ".$nm1.' #### ';
	$nm2 = $nm[3];

	if (preg_match("/-/", $nm2)) $nm2 = splitnm2($nm2, '-');;
	if ($nm2 > 1000) $nm2 = "jahr: ".$nm2.' #### ';
	elseif (preg_match("/j/", $nm2)) $nm2 = "monat: ".$nm2.' #### ';

	return $nm1.' # '.$nm2;
}

function splitnm($nm, $split) {
	$nm = explode($split,$nm);
	$nm1 = $nm[0];
	return $nm1;
}

function splitnm2($nm, $split) {
	$nm = explode($split,$nm);
	$nm1 = $nm[0];
	$nm2 = $nm[1];
	return $nm1.' : '.$nm2;
}

function getPDFts($pdfDatei) {
        /* gibt den timestamp des erstellungsdatums der pdf-datei aus den meta-angaben zurück
         *
         *        $pdfDatei = absoluter pfad zur pdf-datei
         *
        */
        $datei = file_get_contents($pdfDatei);
        preg_match("/\/CreationDate\((.+)\)/si",$datei,$dummi);
        preg_match("/[0-9]{14}/si",$dummi[1],$treffer);
        $ts = mktime( substr($treffer[0],8,2),
                      substr($treffer[0],10,2),
                      substr($treffer[0],12,2),
                      substr($treffer[0],4,2),
                      substr($treffer[0],6,2),
                      substr($treffer[0],0,4)
                    );
        return $ts;
}
?>