<?php

/// ------------------ start der regulaeren sitemap.create


if(file_exists("../lokal.dat")) $xmlpfad = $morpheus["local"];
else $xmlpfad = $morpheus["url"];

$lang_arr = $morpheus["lan_arr"];

$multilang = $morpheus["multilang"];

foreach ($lang_arr as $lan_id=>$lan) {
	include("../nogo/navID_".$lan.".inc");

	$menu_r1 = array();
	$menu_r2 = array();
	$menu_r3 = array();

	$que  	= "SELECT * FROM `morp_cms_nav` WHERE (sichtbar=1 AND `lock` < 1 AND lang=$lan_id AND bereich < 2) ORDER BY navid DESC";
	$res 	= safe_query($que);
	$menge	= mysqli_num_rows($res);
	$rw 	= mysqli_fetch_object($res);

	$arr_H = array();
	$arr_S = array();
	$gesamtArray = array();

	for ($c=0; $c <= 400; $c++) {
		$arr = "arr_".$c;
		unset ($$arr);
	}

	$homeID = $morpheus["home_ID"][$lan];

	# echo "wwww".print_r($arr_15, 1)."xxx<br>";

	//
	for($c=3; $c>0; $c--) {		// komplette `morp_cms_nav` auslesen und navid in die richtige reihenfolgen bringen
		$que  	= "SELECT * FROM `morp_cms_nav` WHERE (ebene=$c AND sichtbar=1 AND `lock` < 1 AND lang=$lan_id AND bereich < 2) ORDER BY parent, sort";
		$res 	= safe_query($que);
		$num	= mysqli_num_rows($res);

		while ($rw = mysqli_fetch_object($res)) {
			// echo $rw->name.'<br>';
			$par	 = $rw->parent;
			$nid	 = $rw->navid;

			echo $arr	 = "arr_".$par;						// fuer jedes parent eine eigene globale mit dem parentwert schreiben
			echo "\n";
			$$arr .= $nid.'<!-- split:'.$nid.' -->,';	// das split wird mit dem navid wert belegt
			if ($par > $x) $x = $par;

			if($rw->bereich < 2 && $rw->parent < 1) $menu_r1[]=$rw->navid;
			else if($rw->ebene == 2) $menu_r2[]=$rw->navid;
			else if($rw->ebene == 3) $menu_r3[]=$rw->navid;
		}
	}

	$tmp = '';

	for ($c=0; $c <= $x; $c++) {						// gehe alle navid durch. suche fuer jeden wert, ob ein split vorhanden ist
		$val 	= "arr_".$c;							// falls vorhanden wird am split gesplittet und der globale wert mittendrin eingesetzt
		$spl 	= explode("<!-- split:$c -->", $tmp);	// nicht die eleganteste methode, aber effektiv ;-) und scheint zu funken :-))
		$tmp	= $spl[0].",".$$val."," .$spl[1];
	}

	 echo $tmp;
	$new = explode(",", $tmp);							// temporaeren datensatz in array wandeln
	# print_r($new);
	# print_r($menu_r1);

	$arr_H = array_filter($new, function($val) {
        return ($val!==null && $val!==false && $val!=='');
    });


/*
	foreach ($new as $val) {							// alle leeren elemente loeschen und neues nav-array schreiben
		if ($val) $arr_H[] = "$val";
	}
*/
	 print_r($arr_H);
	# echo '<br><br><br>';

	// jetzt gehts los. jeden wert in en oder de auslesen und richtigen link erzeugen!

	$lnk_arr = array();
	$nm_arr  = array();

	$footer_set = '<?php  $nav = \'
';
	$sub_on	= 0;
	$sub_on2	= 0;
	$sub_on3	= 0;
	$subsub_ende = 0;
	$ausnahme = array(999);		// das sind hauptnavigatiosIDs, die direkt ein zusätzliches modul bedienen
	$start = 1;

	$par3	= 0;

	// $xmlpfad .= $lan.'/';

	foreach ($arr_H as $val) {
		$y++;
		if ($lastid != $val) {
			settype($val, "integer");	# warum auch immer der typ festgesetzt werden muss. bei einigen abfragen gab es fehler :-((
			$que  	= "SELECT * FROM `morp_cms_nav` where navid=$val";
			$res 	= safe_query($que);
			$rw 	= mysqli_fetch_object($res);

			echo $nm 	= $rw->name;
			echo "\n";
			$par	= $rw->parent;
			$ebene	= $rw->ebene;
			$nid	= $rw->navid;
			$manuellerLink 	= $rw->lnk;

			if($manuellerLink) {
				if(isin("http", $manuellerLink)) {
					$extern=1;
				}
				else $manuellerLink = $xmlpfad.$navID[$manuellerLink];
			}


			// link id's einsetzen
			$lnk_arr[$ebene] = $nid;
			$linkX			 = "";

			// `morp_cms_nav` namen einsetzen
			$nm_arr[$ebene]  = $nm;
			$nnm			 = "";
			$accesskey = $rw->accesskey;

			if ($ebene < 2) {
				// wenn keine subnavigation gesetzt - platzhalter loeschen
				// ? $footer_set = str_replace(array('xx'.$par.'xx', 'dd'.$par3, 'SPAN', 'DPD'), array(''), $footer_set);
				$footer_set = str_replace(array('xx'.$par.'xx', 'SPAN', 'DPD'), array(''), $footer_set);

/*
	<a href="seite24b.php" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Aktuelles<span class="caret"></span></a>
	*/
				if($nid == $homeID && $lan == "de")		$url = $xmlpfad;
				elseif ($nid == $homeID)				$url = $xmlpfad.$lan.'/';
				else 									$url = $xmlpfad.($multilang ? $lan.'/' : '').$navID[$val];

				$lnk = '<a'.($accesskey != '' ? ' accesskey="'.$accesskey.'"' : '').' href="'.$url.'"DPD>'.($nm).'SPAN</a>';
				// else $lnk = '<em class="x'.($nid).'x">'.$nm.'</em>';

				// subnavigation abschliessen
				$footer_set .=
($sub_on3 ? '</li>
							</ul>
						</li>
' : '').
($sub_on2 ? '</li>
					</ul>
			</li>' : '');

				$footer_set .= $start || $sub_on2 ? '' : '</li>';


				$start = 0;
				// hauptnav abschliessen
				$footer_set .= ($sub_on ? '
' : '') .'			<li class="nav-item '.($nid == $homeID ? 'tabletOff ' : '').'n'.($nid).' xx'.$nid.'xx">'.$lnk.'';

				// parameter, die z.T. ueberpruefen, ob eine subnavi aufgerufen wird
				$lasturl = $navID[$val];
				$sub_on = 1;
				$sub_on2 = 0;
				$sub_on3 = 0;
				$subsub_ende = 0;

				$gesamtArray[] = $nid;
			}

			elseif ($ebene == 2 && $par > 1 && in_array($par, $menu_r1)) {
				// platzhalter aus hauptnav beim ersten durchlauf loeschen
				if (!$sub_on2) $footer_set = str_replace(array('xx'.$par.'xx', 'SPAN', 'DPD'), array('dropdown', '', ' id="l'.$nid.'"  class="nav-link dropdown-toggle n'.$par.'" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"'), $footer_set);
				$lasturl = 0;

				$footer_set .=
($sub_on3 ? '					</ul>
			</li>
' : '');

				$footer_set .= (!$sub_on2 ? '
					<ul class="dropdown-menu" aria-labelledby="l'.$nid.'">' : '</li>');

				// parameter werden gesetzt. subnav vorhanden !!!!
				$sub_on2 = 1;
				$sub_on3 = 0;
				$subsub_ende = 1;

				$footer_set .= '
						<li class="dd'.$nid.'"><a'.($accesskey != '' ? ' accesskey="'.$accesskey.'"' : '').' href="'.($manuellerLink ? $manuellerLink : $xmlpfad.($multilang ? $lan.'/' : '').$navID[$val]).'"'.($extern ? ' target="_blank"' : '').'" class="dropdown-item s'.($nid).' dd'.($nid).'">'.($nm).'</a>';

				$gesamtArray[] = $nid;
				$par3 = $nid;
			}

			elseif ($ebene == 3 && $par > 1  && in_array($par, $menu_r2)) {
				// platzhalter aus hauptnav beim ersten durchlauf loeschen
				// echo $par."<br>";
				if (!$sub_on3) $footer_set = str_replace(array('dd'.$par3, 'SPAN', 'DPD'), array('dropdown III ', '', ' id="l'.$nid.'"  class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"'), $footer_set);
				$lasturl = 0;

				$footer_set .= (!$sub_on3 ? '
							<ul class="dropdown-menu dropdown-menu-right" data-animations="right" aria-labelledby="l'.$nid.'">' : '</li>');

				// parameter werden gesetzt. subnav vorhanden !!!!
				$sub_on3 = 1;

				$footer_set .= '
								<li><a'.($accesskey != '' ? ' accesskey="'.$accesskey.'"' : '').' href="'.$xmlpfad.($multilang ? $lan.'/' : '').$navID[$val].'" class="dropdown-item s'.($nid).'">'.($nm).'</a>';

				$gesamtArray[] = $nid;
			}
		}
	}

				$footer_set .=
($sub_on3 ? '</li>
							</ul>
						</li>
' : '').
($sub_on2 ? '</li>
					</ul>
			</li>
' : '').
($sub_on && !$sub_on2 ? 't</li>
' : '');


/*
	      <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" data-toggle="dropdown" href="#">Something else here</a>
            <ul class="dropdown-menu">
              <a class="dropdown-item" href="#">A</a>
              <a class="dropdown-item" href="#">b</a>
            </ul>
          </li>
*/

	if ($lasturl) $footer_set = str_replace(array('SPAN', 'DPD'), '', $footer_set);

/*
	$que  	= "SELECT * FROM `morp_cms_nav` WHERE ebene=1 AND sichtbar=1 AND lang=$lan_id AND bereich = 2 ORDER BY `sort`";
	$res 	= safe_query($que);
	$nav_meta = '';

	while ($rw = mysqli_fetch_object($res)) {
		$par	 = $rw->parent;
		$nid	 = $rw->navid;
		$nm	 	 = $rw->name;
		$name	 = eliminiere($nm);

		$nav_meta .= '		<li><a href="'.$xmlpfad.($multilang ? $lan.'/' : '').$navID[$nid].'" title="'.$name.'"><strong>'.$nm.'</strong></a></li>'."\n\t";
	}
*/

#	$xmlpfad = "/peakom/";
echo	 $footer_set = $footer_set.'
\';
?>
';

	# print_r($gesamtArray);

	save_data("../nogo/nav_".$lan.".inc", $footer_set, "w");
	unset($footer_set);

	// echo $lan_id;
	$arr = readCompleteNavOrdered($lan_id);
	save_data("../nogo/orderedList_".$lan.".inc", $arr, "w");

}
  die();
?>