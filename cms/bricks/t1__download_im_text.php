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

$output .= 'ilink<a href="'.$dir.'pdf/'.$nm.'" target="_blank" title="'.$nm.' zum Download" class="underl">'.$de.'</a>ilink';

$morp = '<b>Download:</b>: '.$de.'<br/>';
?>