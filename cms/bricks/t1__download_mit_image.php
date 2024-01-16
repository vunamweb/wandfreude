<?php
global $pdf, $dir;

$query  = "SELECT * FROM `morp_cms_pdf` where pid=$text";
$result = safe_query($query);
$row = mysqli_fetch_object($result);

$de = $row->pdesc;
$nm = $row->pname;
$si = $row->psize;
$da = $row->pdate;
$pi = $row->pimage;
$da = euro_dat($da);

$typ = explode(".", $nm);
$c	 = (count($typ)-1);

$imgnm = '';
for($ii=0; $ii<$c; $ii++) {
	$imgnm .= $typ[$ii].'.';
}

$output .= '
				<div class="col-3 pdfImg"><a href="'.$dir.'pdf/'.$nm.'" target="_blank" title="'.$nm.' zum Download"><img src="'.$dir.'mthumb.php?w=200&amp;src=images/userfiles/image/'.$imgnm.'jpg" alt="'.$de.'" /></a></div>
';

$morp = '<b>Download:</b> '.$de.'<br/>';
?>