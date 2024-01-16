<?php
/* pixel-dusche.de */
global $hl, $design, $itext, $startDIV, $anker, $h1, $bgIMG;
global $fileID, $lastUsedTemplateID, $tabstand, $anker, $anzahlOffenerDIV, $templateIsClosed, $parallaxText;
global $video;


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

$video_special = $video ? ' style="margin:0;"' : '';

$farbe = '';

if($tref == 1 || !$tref) $template = '
<section'.($anker ? ' id="'.$anker.'"' : '').' class="mb2 '.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').'"'.($farbe ? ' style="background:#'.$farbe.'"' : $video_special).'>
    <div class="container-fluid content center'.($tabstand ? ' mt6 ' : '').'"'.($bgIMG ? ' style="background:url('.$bgIMG.') no-repeat fixed; background-size: cover; padding-top:2em; padding-bottom:2em;"' : '').'>
        <div class="row">
            <div class="col-12">
#cont#
            </div>
        </div>
    </div>
</section>

';
elseif($tref == 2) $template = '
<section'.($anker ? ' id="'.$anker.'"' : '').' class="'.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').'"'.($farbe ? ' style="background:#'.$farbe.'"' : '').'>
    <div class="container content text-center '.($tabstand ? ' mt6 ' : '').'"'.($bgIMG ? ' style="background:url('.$bgIMG.') no-repeat fixed; background-size: cover; padding-top:2em; padding-bottom:2em;"' : '').'>
        <div class="row">
            <div class="col-12">
#cont#
            </div>
        </div>
    </div>
</section>
';
elseif($tref == 3) $template = '
<section'.($anker ? ' id="'.$anker.'"' : '').' class="'.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').'"'.($farbe ? ' style="background:#'.$farbe.'"' : '').'>
    <div class="container-fluid '.($parallax ? 'pad0' : '').' center bgIMG'.($tabstand ? ' mt6 ' : '').'"'.($bgIMG ? ' style="background:url('.$bgIMG.') no-repeat fixed; background-size: cover; padding-top:2em; padding-bottom:2em;"' : '').'>
		'.($parallax ? '<div class="parallax" style="background: url('.$parallax.') no-repeat top center fixed; -webkit-background-size: cover; background-size: cover;">
        '.($parallaxText ? '

        <div class="container">

			<div class="row h-100">
			   <div class="col-sm-12 col-lg-9 col-xl-6 my-auto">
			     <div class="card card-block silverton-card text-left">

						#cont#

			     </div>
			   </div>
			</div>

        </div>' : '

        ').'
' : '

		#cont#
        <div class="row">
            <div class="col-12">
            </div>
        </div>
        ').'
    </div>
</section>

';
elseif($tref == 4) $template = '
<section'.($anker ? ' id="'.$anker.'"' : '').' class="'.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').'"'.($farbe ? ' style="background:#'.$farbe.'"' : '').'>
    <div class="container content '.($tabstand ? ' mt6 ' : '').'"'.($bgIMG ? ' style="background:url('.$bgIMG.') no-repeat fixed; background-size: cover; padding-top:2em; padding-bottom:2em;"' : '').'>
        <div class="row">
            <div class="col-12">
#cont#
            </div>
        </div>
    </div>
</section>
';
elseif($tref == 5) $template = '
<section'.($anker ? ' id="'.$anker.'"' : '').' class="'.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').'"'.($farbe ? ' style="background:#'.$farbe.'"' : '').'>
    <div class="container content center '.($tabstand ? ' mt6 ' : '').'"'.($bgIMG ? ' style="background:url('.$bgIMG.') no-repeat fixed; background-size: cover; padding-top:2em; padding-bottom:2em;"' : '').'>
        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
#cont#
            </div>
        </div>
    </div>
</section>
';
elseif($tref == 6) $template = '
<section'.($anker ? ' id="'.$anker.'"' : '').' class="'.($tabstand ? ' mt6 ' : '').($class ? $class.' bg-color' : '').'"'.($farbe ? ' style="background:#'.$farbe.'"' : '').'>

    <div class="container-fluid '.($parallax ? 'pad0' : '').' center bgIMG'.($tabstand ? ' mt6 ' : '').'"'.($bgIMG ? ' style="background:url('.$bgIMG.') no-repeat fixed; background-size: cover; padding-top:2em; padding-bottom:2em;"' : '').'>
		'.($parallax ? '<div class="parallax parallax2" style="background: url('.$parallax.') no-repeat top center fixed; -webkit-background-size: cover; background-size: cover;">' : '
 #cont#
        <div class="row">
            <div class="col-12">
            </div>
        </div>
        ').'
    </div>
</section>

';


$anzahlOffenerDIV=0;

$hl = '';
$farbe = '';
$class = '';
// $bgIMG = '';
$itext = '';
$parallaxText = '';

?>