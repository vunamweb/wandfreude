<?php
session_start();

global $form_desc, $cid, $navID, $lan, $nosend, $morpheus, $ssl_arr, $ssl, $lokal_pfad, $js, $cid, $formMailAntwort, $plichtArray, $multilang;


// print_r($_REQUEST);


// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// DESIGN DES FORMULARES
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .

	$design = '
			<div class="form-group">
				#cont#
			</div>
	';

	$designCheckbox = '
		<div class="form-group">
			<div class="checkbox">
				<label>#cont# &nbsp; #desc#</label>
			</div>
		</div>
	';
	$designschmal = '
		<div class="form-group">
			<label>#desc#</label> &nbsp; &nbsp;<br/>
			#cont#
		</div>
	';


	$designTEXT = '
		<div class="form-group">
			<p>#desc#</p>
		</div>
	';
	$designFETT = '
		<div class="form-group">
			<h3>#desc#</h3>
		</div>
	';

// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// ENDE DESIGN
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .
// .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .    .

	$fid 	= $text;

	$query  	= "SELECT * FROM morp_cms_form WHERE fid=".$fid;
	$result 	= safe_query($query);
	$row 		= mysqli_fetch_object($result);
	$formMailAntwort = $row->antwort;
	$formMailBetreff = $row->betreff;

	$query  = "SELECT * FROM morp_cms_form_field WHERE fid=$fid ORDER BY reihenfolge";
	$result = safe_query($query);

	$x = 0;

	$plichtArray = array();

	while ($row    = mysqli_fetch_object($result)) {
		$nm 	= $row->fname;
		$text 	= $row->fform;

		$art 	= $row->art;
		$feld 	= $row->feld;
		$desc 	= ($row->desc);
		$hilfe 	= $row->hilfe;
		$email 	= $row->email;
		$size	= $row->size;
		$parent	= $row->parent;
		$fehler	= $row->fehler;
		$style  = $row->klasse;
		$cont	= $row->cont;
		$auswahl = ($row->auswahl);

		# if ($style) $style = ' style="'.$style.'"';
		if ($style) $style = ' '.$style;

		$star = ' *';
		$pflicht = '';

		if($row->pflicht) $plichtArray[]=$row->feld;

		if ($cont == "email" && $row->pflicht) 	{ $pflicht = ' required'; }
		elseif ($cont == "number" && $row->pflicht) 	{ $pflicht = ' required'; $rules .= $feld.': { required:true, number: true },
	'; }
		elseif ($cont == "number") 	{ $star = ''; $rules .= $feld.': { number: true },
	'; }
		elseif ($cont == "email") 	{ $pflicht = ' class="email"'; $star = ''; }
		elseif ($row->pflicht) 	{ $pflicht = ' required'; }
		else					{ $pflicht = ''; $star = ''; }

		$desc .= $star;

		if ($fehler) 	$messages .= $feld.': "'.$fehler.'"'.",\n";

		$data = "";

		// FELD IST ABHAENGIG DAVON; DASS EINE CHECKBOX AKTIVIERT WURDE
		if ($art == "Fieldset Start") $form .= '</table><fieldset id="'.$feld.'" style="">'.$table;

		elseif ($art == "Fieldset Ende") $form .= '</table></fieldset>'.$table;

		elseif (isin("^Ende", $art)) $form .= '<br style="clear:both;" />';

		elseif ($art == "Eingabefeld") {
			// $size = $size > 220 ? 220 : 0;
			$size = 220;
			$data = '<input id="'.$feld.'" name="'.$feld.'"'.$pflicht. ' placeholder="'.$desc.'" type="text" class="form-control" />';
			if ($style == " schmal") $form .= str_replace(array('#cont#', '#desc#', '#style#'), array($data, $desc, $style), $designschmal);
			else $form .= str_replace(array('#cont#', '#desc#', '#style#'), array($data, $desc, $style), $design);
		}

		elseif ($art == "Checkbox") {
			$x++;

			unset($value);
			if (isin("\|", $feld)) {
				$t	 = explode("|", $feld);
				$feld 	= $t[0];
				$value  = $t[1];
			}

			# $data = '<input type="checkbox"'. ($feld=="de"?' checked':'') .' class="checkbox" id="'.$feld.'" '. ($value ? ' value="'.$value.'"' : '') .' name="'.$feld.'"'.$pflicht.' /> ';
			# $data = '<input type="checkbox"'. ($feld=="de"?' checked':'') .' class="checkbox" id="'.$feld.'" '. ($value ? ' value="'.$value.'"' : ' value="ja"') .' name="'.$feld.'"'.$pflicht.' /> ';
			$data = '<input type="checkbox"'. ($feld=="de"?' checked':'') .' class="checkbox" id="'.$feld.'" '. ($value ? ' value="'.$value.'"' : ' value="'.$feld.'"') .' name="'.$feld.'"'.$pflicht.' /> ';

			$form .= str_replace(array('#cont#', '#desc#', '#style#', '#anz#'), array($data, $desc, $style, $x), $designCheckbox);

			// CHECKBOX SCHALTET FIELDSET FREI
			if ($parent) $optional .= '	var '.$feld.' = $("#'.$feld.'");
		var inital'.$feld.' = '.$feld.'.is(":checked");
		var topics'.$feld.' = $("#'.$parent.'")[inital'.$feld.' ? "removeClass" : "addClass"]("gray");
		var topicInputs'.$feld.' = topics'.$feld.'.find("input").attr("disabled", !inital'.$feld.');
		var topicText'.$feld.' = topics'.$feld.'.find("textarea").attr("disabled", !inital'.$feld.');
		'.$feld.'.click(function() {
			topics'.$feld.'[this.checked ? "removeClass" : "addClass"]("gray");
			topicInputs'.$feld.'.attr("disabled", !this.checked);
			topicText'.$feld.'.attr("disabled",  !this.checked);
		});
	';
		}

		elseif ($art == "Radiobutton") {
			$data .= fpdForm($feld, $auswahl, "radio", $pflicht);
			if ($pflicht) {
				// $data .= '<br style="clear:left;" /><label for="'.$feld.'" class="error" style="clear:left;">Bitte w&auml;hlen Sie eine Option</label>';
				// $data .= '<br style="clear:left;" /><label for="'.$feld.'" class="error" style="clear:left;">Bitte w&auml;hlen Sie eine Option</label>';
				$rules .= $feld.': "required",
	';
			}
			$form .= str_replace(array('#cont#', '#desc#', '#style#'), array($data, $desc, $style), $design);
		}

		elseif ($art == "Dropdown") {
			if ($style == " StSp1" || $style == " StSp2" || $style == " StSp3") $breite = 100;
			else $breite = 180;

			if (!isin("print.php", $uri)) 	$data .= fpdForm($feld, $auswahl, "sel", $pflicht, $breite).'</select>';
			else 							$data .= fpdForm($feld, $auswahl, "radio", $pflicht);

			if ($pflicht) {
				// $data .= '<label for="'.$feld.'" class="error">Bitte w&auml;hlen Sie eine Option</label>';
				$rules .= $feld.': "required",
	';
			}
			if ($style == " schmal") $form .= str_replace(array('#cont#', '#desc#', '#style#'), array($data, $desc, $style), $designschmal);
			else $form .= str_replace(array('#cont#', '#desc#', '#style#'), array($data, $desc, $style), $designschmal);
		}

		elseif ($art == "Mitteilungsfeld") {
			$data .= '<textarea id="'.$feld.'" name="'.$feld.'"'.$pflicht.' placeholder="'.$desc.'" class="form-control" style="height:100px;"></textarea>';
			$form .= str_replace(array('#cont#', '#desc#', '#style#'), array($data, $desc, $style), $design);
		}

		elseif ($art == "Freitext Fett") {
			$form .= str_replace(array('#desc#', '#anz#'), array(nl2br($hilfe), $x), $designFETT);
		}

		elseif ($art == "Freitext") {
			$form .= str_replace(array('#desc#', '#anz#'), array(nl2br($hilfe), $x), $designTEXT);
		}

	}

	$mail = 'info@your-plate.de';
	if($lan == "de") $datenschutzID = 5;


	$dsText = '';
	if($lan == "de") $dsText = '<p><input id="datenschutz" type="checkbox" name="datenschutz" required > &nbsp; Ich stimme zu, dass meine Angaben aus dem Kontaktformular zur Beantwortung meiner Anfrage erhoben und verarbeitet werden. Die Daten werden nach abgeschlossener Bearbeitung Ihrer Anfrage gelöscht. Hinweis: Sie können Ihre Einwilligung jederzeit für die Zukunft per E-Mail an <a href="mailto:'.$mail.'"><u>'.$mail.'</u></a> widerrufen. Detaillierte Informationen zum Umgang mit Nutzerdaten finden Sie in unserer <a href="/datenschutz" target="_blank"><u>Datenschutzerklärung</u></a></p>';

	$senden = '';
	if($lan == "de") $senden = 'absenden';
	else if($lan == "en") $senden = 'send';
	else if($lan == "fr") $senden = 'envoyer';


	$js = str_replace(array('<!-- rules -->', '<!-- optional -->', '<!-- messages -->'), array($rules, $optional, $messages), $js);


	$output .= '

					<div id="kontaktformular">
						<form class="frmContact leftalgn" id="kontaktf" method="post">
							<div class="row">
								<div class="col-12 col-md-6">
									<input type="Hidden" name="betreff" value="'.$formMailBetreff.'">

									'.$form .'<br style="clear:left;" />
								</div>
								<div class="col-12 col-md-6">

									'.$dsText.'

									<button class="btn btn-info sendform" type="submit">'.$senden.'</button>
								</div>
						</form>
					</div>




	';


	$morp = '<b>FORMULAR</b>';

?>