<?php
global $color;

$text = explode("\n", $text);

$output .= '
<div class="d-flex justify-content-center">
';

foreach($text as $val) {
	if ($val) $output .= '		<div class="p-3">'.$val.'</div>
';
}

$output .= '</div>

';

?>