<?php
/* pixel-dusche.de */

global $cid, $template4count, $templateTotal;
global $lasttref, $linkbox;

global $containerLink, $containerLinkText, $template4count, $templateTotal, $lastUsedTemplateID, $templateIsClosed, $templateCloseNow;
global $design, $cid, $tref, $farbe, $class, $tende, $tabstand, $tpos, $DoNotCloseTemplate, $anzahlOffenerDIV, $anker;
global $class_inner, $farbe_inner, $kontaktCount;

$template = '';

$fileID = basename(__FILE__, '.php');


if(!$template4count || $template4count < 1) {
	$sql = "SELECT cid FROM morp_cms_content WHERE tid=$fileID AND navid=$cid AND ton=1 ORDER by tpos";
	$res = safe_query($sql);
	$templateTotal = mysqli_num_rows($res);

	$template4count = 1;
}
else $template4count++;



if($lastUsedTemplateID && $lastUsedTemplateID != $fileID && !$templateIsClosed) {
	for($i=1; $i<=$anzahlOffenerDIV; $i++) $template .= '					</div>
';

	$template .= '
				</section>
';
	$templateIsClosed=1;
}


global $H1_count, $accordionText;


if($template4count == 1 || $templateIsClosed) { $template .= '
    <div class="container '.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').' content"'.($anker ? ' id="'.$anker.'"' : '').'>
        <div class="container">
        	<div class="row">
				<div class="panel-group" id="accordion">
        	';
	$templateIsClosed=0;
}


if($tref == 1 || !$tref) $template .= '

			<div class="panel panel-default">
'.$accordionText.'
			    <div id="collapse'.$H1_count.'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
			    	<div class="panel-body">
#cont#
			      	</div>
			    </div>
			</div>


';



if(($template4count == $templateTotal || $tende) && !$templateIsClosed) {
	for($i=1; $i<=$anzahlOffenerDIV; $i++) $template .= '					</div>
';

	$template .= '
';
	$template4count = 0;
	$templateTotal = 0;
	$templateIsClosed = 1;
	$tende = 0;
}

$lastUsedTemplateID = $fileID;
$anzahlOffenerDIV = 4;

$farbe='';
$class='';
$tabstand = '';
$anker = '';
//$class_inner = '';

?>