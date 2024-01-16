<?php
	global $H1_count, $accordionText;

	if(!$H1_count) $H1_count = 1;
	else $H1_count++;

	$accordionText = '
				<div class="panel-heading">
			    	<a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$H1_count.'" class="collapsed" aria-expanded="false"><h2 class="panel-title">'.nl2br($text).'</h2></a>
			    </div>
';
	$morp = $text;

	$parallaxText = 1;
?>