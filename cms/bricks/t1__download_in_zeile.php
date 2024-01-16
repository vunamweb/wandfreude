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
$img = $typ[$c]."_s.gif";

$output .= '
				<p class="pdf_lnk"><a href="'.$dir.'pdf/'.$nm.'" target="_blank" title="'.$nm.' zum Download"><i class="fa fa-download"></i> &nbsp; '.$de.'</a></p>
';

$morp = '<b>Download:</b> '.$de.'<br/>';
?>