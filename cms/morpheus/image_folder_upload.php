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

$cust 	 = $_REQUEST["cust"];
$mid 	 = $_REQUEST["mid"];
$nid 	 = $_REQUEST["nid"];
$ngid 	 = $_REQUEST["ngid"];
$imgid 	 = $_REQUEST["imgid"];
$news	 = $_REQUEST["news"];

$cedit	= $_REQUEST["cedit"];
$navid	= $_REQUEST["navid"];

$folder	 = $_REQUEST["folder"];
$id 	 = $_REQUEST["id"];
$from 	 = $_REQUEST["from"];
$setid 	 = $_REQUEST["setid"];
$tbl 	 = $_REQUEST["tbl"];

$download 	 = $_REQUEST["download"];
$tab 	 = "foto";
$mformid = $_REQUEST["mformid"];

// id='.$edit.'&imgid='.$arr[0].'&folder='.$imgfolder.'&from='.$scriptname.'&setid='.$id.'

# ziel-ordner und db bestimmen
if ($folder) {
	$ordner = "images/".$folder;
	$db		= $tbl;
	$cedit	= $id;
	$jumpback = $from.'.php';
	$tab = $imgid;
}
elseif ($navid) {
	$ordner = "images/backg";
	$db		= "morp_cms_content";
	$setid  = "cid";
	$tab 	= "timage";
}
elseif ($download) {
	$ordner = "images/download";
	$db		= "morp_cms_pdf_group";
	$setid  = "pgid";
	$cedit	= $download;
	$jumpback = 'pdf_group.php';
	$tab = $imgid;
}
elseif ($news) 	{
	$ordner = "images/news";
	$db		= "morp_cms_news";
	$setid  = "nid";
	$cedit	= $nid;
	$tab = $imgid;
}
elseif ($cust) 	{
	$ordner = "secure/dfiles/HgtFGDkjg/";
	$db		= "morp_download";
	$setid  = "benutzer";
	$imgid 	= "datei";
	$jumpback = "customer_kat.php";
}
elseif ($mid) 	{
	$ordner = "images/team";
	$db		= "morp_mitarbeiter";
	$setid  = "mid";
	$cedit	= $mid;
	$jumpback = "morp_mitarbeiter.php";
}
else 	{
	$ordner = "images/news";
	$db		= "news";
}

echo "<div>\n\n";
// echo $tab.' / '.$ordner;
#die();


if($_FILES) {
	$tmp  = $_FILES['image']['tmp_name'];
	$img  = strtolower(($_FILES['image']['name']));

	$img = explode(".", $img);
	$position = count($img)-1;
	$fileType = $img[$position];
	$fileName = eliminiere($img[0]).'.'.$fileType;

	if (!move_uploaded_file($tmp, "../$ordner/".$fileName)) die("upload fehlgeschlagen!");
	chmod("../$ordner/".$fileName, 0777);

	//echo "../$ordner/".$img;

	if ($cust)		$query = "INSERT $db set $imgid='$fileName', benutzer='$cust'";
	elseif ($db)	$query = "UPDATE $db SET $tab='$fileName' WHERE $setid='$cedit'";

	#echo $query;
	#die();

	if ($query) {
		//$result = safe_query($query);
		safe_query($query);
		#unlink($tmp);
	}

	# rueckspruenge zu den ausgangs-tools
	if ($news) 				die("<script language=\"JavaScript\">document.location='news.php?edit=$nid&ngid=$ngid';</script>");
	elseif ($navid)			die("<script language=\"JavaScript\">document.location='content_template.php?edit=$cedit&navid=$navid';</script>");
	else					die("<script language=\"JavaScript\">document.location='".$jumpback."?edit=$cedit';</script>");
}

else {
	echo "<h2>Bild Upload</h2><br>
		<form method=post enctype=\"multipart/form-data\">\n\n";

	echo '	<input name="image" type="file" style="width:500px"><br>
			<input name=ngid type=hidden value='.$ngid.'>
			<input name=cedit type=hidden value='.$cedit.'>
			<input name=cust type=hidden value='.$cust.'>
			<input name=nid type=hidden value='.$nid.'>
			<input name=navid type=hidden value='.$navid.'>
			<input name=news type=hidden value='.$news.'>
			<input name=tn type=hidden value='.$tn.'>
			<input name=full type=hidden value='.$full.'>
			<input name=imgid type=hidden value='.$imgid.'>
			<p><input type="submit" value="upload starten"></p>
	</form>
	';
}

#echo '<p><a href="javascript:history.back();">' .backlink() .' zur&uuml;ck</a></p>';
?>

<?php
include("footer.php");
?>
