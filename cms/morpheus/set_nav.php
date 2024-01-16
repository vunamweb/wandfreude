<?php

/// ------------------ start der regulaeren sitemap.create


if(file_exists("../lokal.dat")) $xmlpfad = $morpheus["local"];
else $xmlpfad = $morpheus["url"];

$lang_arr = $morpheus["lan_arr"];

$multilang = $morpheus["multilang"];


foreach ($lang_arr as $lang_id=>$lan) {
	include("../nogo/navID_".$lan.".inc");


	// hauptnav, ohne meta menu, ohne footer menu
	$que  	= "SELECT * FROM `morp_cms_nav` WHERE (sichtbar=1 AND `lock` < 1 AND lang=$lang_id AND bereich < 2 AND ebene=1) ORDER BY `sort` ASC";
	$res 	= safe_query($que);
	$menge	= mysqli_num_rows($res);

	$arr_H=array();

	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//Struktur richtige Reihenfolge
	while ($row = mysqli_fetch_object($res)) {
		$startEbene1 = 1;

		$arr_H[] = $row->navid;

		//start ebene 2
		$res_2 = getNav($lang_id, $row->navid);
		while ($row2 = mysqli_fetch_object($res_2)) {
			$arr_H[] = $row2->navid;

			//start ebene 3
			$res_3 = getNav($lang_id, $row2->navid);
			while ($row3 = mysqli_fetch_object($res_3)) {
				$arr_H[] = $row3->navid;

			}
		}
	}

	// print_r($arr_H);
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


	$homeID = $morpheus["home_ID"][$lan];

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

	foreach ($arr_H as $val) {
		$y++;
		if ($lastid != $val) {
			$que  	= "SELECT * FROM `morp_cms_nav` WHERE navid=$val";
			$res 	= safe_query($que);
			$rw 	= mysqli_fetch_object($res);

			$nm 	= $rw->name;
			$par	= $rw->parent;
			$ebene	= $rw->ebene;
			$nid	= $rw->navid;
			$manuellerLink 	= $rw->lnk;
			$accesskey = $rw->accesskey;
			$anker = 0;

			// echo "$nm - $nid - $ebene - $par - \n";

			if($manuellerLink) {
				if(isin("http", $manuellerLink)) {
					$extern=1;
				}
				elseif(isin("#", $manuellerLink)) {
					$anker=1;
					$manuellerLink = explode('#', $manuellerLink);
					$manuellerLink = $xmlpfad.$navID[$manuellerLink[0]].'#'.$manuellerLink[1];
				}
				else $manuellerLink = $xmlpfad.$navID[$manuellerLink];
			}

			if ($ebene < 2) {
				// wenn keine subnavigation gesetzt - platzhalter loeschen
				// ? $footer_set = str_replace(array('xx'.$par.'xx', 'dd'.$par3, 'SPAN', 'DPD'), array(''), $footer_set);
				$footer_set = str_replace(array('xx'.$par.'xx', 'SPAN', 'DPD'), array(''), $footer_set);

				if($nid == $homeID && $lan == "de")		$url = $xmlpfad;
				elseif ($nid == $homeID)				$url = $xmlpfad.$lan.'/';
				else 									$url = $xmlpfad.($multilang ? $lan.'/' : '').$navID[$val];

				$lnk = '<a'.($accesskey != '' ? ' accesskey="'.$accesskey.'"' : '').' href="'.$url.'" class="nav-link DPD">'.($nm).'SPAN</a>';

				// subnavigation abschliessen
				if($sub_on3) $footer_set .= '</li>
							</ul>
						</li>
					</ul>
				</li>
';
				else if($sub_on2) $footer_set .= '</li>
					</ul>
				</li>
';
				$footer_set .= $start || $sub_on2 ? '' : '</li>';

				$start = 0;

				// hauptnav abschliessen
				$footer_set .= ($sub_on ? '
' : '') .'				<li class="nav-item '.($nid == $homeID ? 'tabletOff ' : '').'n'.($nid).'n xx'.$nid.'xx">'.$lnk.'';

				// parameter, die z.T. ueberpruefen, ob eine subnavi aufgerufen wird
				$lasturl = $navID[$val];
				$sub_on = 1;
				$sub_on2 = 0;
				$sub_on3 = 0;
				$subsub_ende = 0;

				$gesamtArray[] = $nid;
			}

			elseif ($ebene == 2 && $par > 1) {
				// platzhalter aus hauptnav beim ersten durchlauf loeschen
				if (!$sub_on2) $footer_set = str_replace(array('xx'.$par.'xx', 'SPAN', 'DPD'), array('dropdown', '', 'nav-link dropdown-toggle n'.$par.'" id="l'.$nid.'" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"'), $footer_set);
				$lasturl = 0;

				if($sub_on3) $footer_set .= '</li>
							</ul>
						</li>
';

				$footer_set .= (!$sub_on2 ? '
					<ul class="dropdown-menu" aria-labelledby="l'.$nid.'">' : '');

				// parameter werden gesetzt. subnav vorhanden !!!!
				$sub_on2 = 1;
				$sub_on3 = 0;
				$subsub_ende = 1;

				$footer_set .= '
						<li class="dd'.$nid.' nav-item"><a'.($accesskey != '' ? ' accesskey="'.$accesskey.'"' : '').' href="'.($manuellerLink ? $manuellerLink : $xmlpfad.($multilang ? $lan.'/' : '').$navID[$val]).'"'.($extern ? ' target="_blank"' : '').'" class="nav-link dropdown-item s'.($nid).' dd'.($nid).'">'.($nm).'</a></li>';

				$gesamtArray[] = $nid;
				$par3 = $nid;
			}

			elseif ($ebene == 3 && $par > 1) {
				// platzhalter aus hauptnav beim ersten durchlauf loeschen
				// echo $par."<br>";
				if (!$sub_on3) $footer_set = str_replace(array('dd'.$par3, 'SPAN', 'DPD'), array('dropdown', '', ' id="l'.$nid.'"  class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"'), $footer_set);
				$lasturl = 0;

				$footer_set .= (!$sub_on3 ? '
							<ul class="dropdown-menu dropdown-menu-right" data-animations="right" aria-labelledby="l'.$nid.'">' : '</li>');

				// parameter werden gesetzt. subnav vorhanden !!!!
				$sub_on3 = 1;

				$footer_set .= '
								<li class="nav-item"><a'.($accesskey != '' ? ' accesskey="'.$accesskey.'"' : '').' href="'.$xmlpfad.($multilang ? $lan.'/' : '').$navID[$val].'" class="dropdown-item nav-link s'.($nid).'">'.($nm).'</a></li>';

				$gesamtArray[] = $nid;
			}
		}
	}

	if($sub_on3) $footer_set .= '</li>
							</ul>
						</li>
					</ul>
				</li>
';
	else if($sub_on2) $footer_set .= '</li>
					</ul>
				</li>
';
	else if($sub_on) $footer_set .= '</li>
';


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
	 $footer_set = $footer_set.'
\';
?>
';

	# print_r($gesamtArray);

	save_data("../nogo/nav_".$lan.".inc", $footer_set, "w");
	unset($footer_set);

	// echo $lan_id;
	$arr = readCompleteNavOrdered($lang_id);
	save_data("../nogo/orderedList_".$lan.".inc", $arr, "w");

}
 // die();
?>