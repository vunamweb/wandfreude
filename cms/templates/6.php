<?php
/* pixel-dusche.de */

global $fileID, $lastUsedTemplateID, $anker, $class, $farbe, $tabstand, $anzahlOffenerDIV, $templateIsClosed, $grIMG;

$fileID = basename(__FILE__, '.php');
$lastUsedTemplateID = $fileID;

if($lastUsedTemplateID && $lastUsedTemplateID != $fileID && !$templateIsClosed) {
	for($i=1; $i<=$anzahlOffenerDIV; $i++) $template .= '					</div>
';

	$template .= '
				</section>
';
	$templateIsClosed=1;
}


$template = '
<section'.($anker ? ' id="'.$anker.'"' : '').' class="'.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').'"'.($farbe ? ' style="background:#'.$farbe.'"' : '').'>
    <div class="container content">
        <div class="row">
';


// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +
// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +
// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +
// TEMPLATE


if($tref == 1 || !$tref) $template .= '
            <div class="col-md-4 order-1 order-md-1">
'.$grIMG.'
            </div>
            <div class="col-md-8 order-2 order-md-2 imgPaddingL">
#cont#
            </div>
';
else if($tref == 2) $template .= '
            <div class="col-md-8 order-2 order-md-1 imgPaddingR">
#cont#
            </div>
            <div class="col-md-4 order-1 order-md-2">
'.$grIMG.'
            </div>
';
else if($tref == 3) $template .= '
            <div class="col-md-6 order-1 order-md-1">
'.$grIMG.'
            </div>
            <div class="col-md-6 order-2 order-md-2">
#cont#
            </div>
';
else if($tref == 4) $template .= '
            <div class="col-md-6 order-2 order-md-1">
#cont#
            </div>
            <div class="col-md-6 order-1 order-md-2">
'.$grIMG.'
            </div>
';


// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +
// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +
// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +
// END TEMPLATE

$template .= '
        </div>
    </div>
</section>
';

$anzahlOffenerDIV = 0;

$class = '';
$farbe = '';
$grIMG = '';

?>