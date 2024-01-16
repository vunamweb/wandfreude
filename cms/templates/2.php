<?php
/* pixel-dusche.de */

global $cid, $template2count, $templateTotal;
global $lasttref, $linkbox;

global $containerLink, $containerLinkText, $template2count, $templateTotal, $lastUsedTemplateID, $templateIsClosed, $templateCloseNow;
global $design, $cid, $tref, $farbe, $class, $tende, $tabstand, $tpos, $DoNotCloseTemplate, $anzahlOffenerDIV, $anker;
global $class_inner, $farbe_inner, $kontaktCount;

$template = '';

$fileID = basename(__FILE__, '.php');


if(!$template2count || $template2count < 1) {
	$sql = "SELECT cid FROM morp_cms_content WHERE tid=$fileID AND navid=$cid AND ton=1 ORDER by tpos";
	$res = safe_query($sql);
	$templateTotal = mysqli_num_rows($res);

	$template2count = 1;
}
else $template2count++;



if($lastUsedTemplateID && $lastUsedTemplateID != $fileID && !$templateIsClosed) {
	for($i=1; $i<=$anzahlOffenerDIV; $i++) $template .= '					</div>
';

	$template .= '
				</section>
';
	$templateIsClosed=1;
}


if($template2count == 1 || $templateIsClosed) { $template .= '
    <div class="container-full '.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').' content">
	    <div class="container">
        <div class="row">';
	$templateIsClosed=0;
}


if($tref == 1 || !$tref) $template .= '
            <div class="col-12 col-md-4 "'.($linkbox ? ' ref="'.$linkbox.'"' : '').($anker ? ' id="'.$anker.'"' : '').'>
            <div class="box_color">
			'.($class_inner ? '<div class="inner '.$class_inner .'">' : '').'
#cont#
            '.($class_inner ? '</div>' : '').'
            </div>
			</div>
';
elseif($tref == 2) $template .= '
            <div class="col-md-3 "'.($linkbox ? ' ref="'.$linkbox.'"' : '').($anker ? ' id="'.$anker.'"' : '').'>
            '.($class_inner ? '<div class="inner '.$class_inner .'">' : '').'
#cont#
            '.($class_inner ? '</div>' : '').'
            </div>

';
elseif($tref == 3) $template .= '
            <div class="col-md-6 "'.($linkbox ? ' ref="'.$linkbox.'"' : '').($anker ? ' id="'.$anker.'"' : '').'>
            '.($class_inner ? '<div class="inner '.$class_inner .'">' : '').'
#cont#
            '.($class_inner ? '</div>' : '').'
            </div>

';
elseif($tref == 4) $template .= '
            <div class="col-md-8 "'.($linkbox ? ' ref="'.$linkbox.'"' : '').($anker ? ' id="'.$anker.'"' : '').'>
            '.($class_inner ? '<div class="inner '.$class_inner .'">' : '').'
#cont#
            '.($class_inner ? '</div>' : '').'
            </div>

';



if(($template2count == $templateTotal || $tende) && !$templateIsClosed) {
	for($i=1; $i<=$anzahlOffenerDIV; $i++) $template .= '					</div>
';

	$template .= '
';
	$template2count = 0;
	$templateTotal = 0;
	$templateIsClosed = 1;
	$tende = 0;
}

$lastUsedTemplateID = $fileID;
$anzahlOffenerDIV = 3;

$farbe='';
$class='';
$tabstand = '';
$anker = '';
//$class_inner = '';

?>