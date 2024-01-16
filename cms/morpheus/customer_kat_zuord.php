<?
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
include("cms_include.inc");

# print_r($_REQUEST);

global $arr_form;

$edit 	= $_REQUEST["edit"];
$neu	= $_REQUEST["neu"];
$save	= $_REQUEST["save"];
$del	= $_REQUEST["del"];
$id		= $_REQUEST["custid"];

echo '<div id=vorschau>
	<h2>Kategorien / Branchen</h2>

	'. ($edit || $neu ? '<p><a href="?pid='.$pid.'">&laquo; zur&uuml;ck</a></p>' : '') .'	
	<form action="" onsubmit="" name="verwaltung" method="post">
';

$new = '<p><a href="?neu=1">&raquo; NEU</a></p>';

$arr_form = array(
	array("kategorie", "Kategorie / Branche",'<input type="Text" value="#v#" name="#n#" style="#s#">'),
);


/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function liste($custid) {
	$db = "morp_customer_kat";
	$id = "kid";
	$ord = "kid";
	$anz = "kategorie";
	
	$echo .= '<form method="post">
<p>&nbsp;</p>
<input type="hidden" name="custid" value="'.$custid.'">
<input type="hidden" name="save" value="1">
';

	$sql = "SELECT * FROM $db WHERE 1 ORDER BY ".$ord."";
	$res = safe_query($sql); 
		
	while ($row = mysqli_fetch_object($res)) {	
		$edit = $row->$id;
		$echo .= '
			<span style="width:200px; float:left; display:block;"><input type="checkbox" name="cust[]" value="'.$edit.'"'. (check($edit, $custid) ? ' checked' : '') .'> &nbsp;'.$row->$anz.'</span>';
	}
	
	$echo .= '
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<input type="submit">
		</form>
		<p>&nbsp;</p>
		<p>&nbsp;</p>';

	return $echo;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function check($kid, $custid) {
	$sql = "SELECT * FROM morp_customer_zuord WHERE custid=$custid AND kid=$kid";
	$res = safe_query($sql); 
	$x   = mysqli_num_rows($res);
	return $x;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

if ($save) {
	$db = "morp_customer_zuord";

	$sql  = "DELETE FROM $db WHERE custid=$id";
	$res  = safe_query($sql);

	$arr = $_POST["cust"];
	
	foreach($arr as $val) {	
		$sql  = "INSERT $db set custid=$id, kid=$val";
		$res  = safe_query($sql);
	}
	

	echo "<script language='javascript'>
			document.location = 'customer.php?edit=$id';
		</script>";	
}
elseif ($del) {
	$db = "morp_customer_kat";
	$id = "kid";

	$sql = "DELETE FROM $db WHERE $id=$del";
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
