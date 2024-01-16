<?php
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
# start 12/2003                                     #
#                                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # #
// error_reporting(E_ALL);

global $ausrichtungArray, $fileTypes, $imageFolder;

$imageFolder = 'images/userfiles/image/';

$ausrichtungArray = array(
	1=>'',
	2=>' la',
	3=>' center',
	4=>' ra',
);

$fileTypes = array('jpg', 'jpeg', 'png', 'pdf', 'swf', 'ai', 'eps', 'tif', 'tiff', 'psd', 'svg'); // Allowed file extensions

global $imgWidth;
$imgWidth = array(1=>450, 2=>700, 3=>580, 4=>1100, 5=>1100, 7=>1500);


function getImageAltText($alt) {
	return str_replace(array("-", "_", ".png", ".jpg", ".jpeg"), array(" ", " ", "", ""), $alt);
}


function getSubNavigation($lang, $parentID, $linkDesign='<li><a href="#lnk#">#txt#</a></li>') {
	global $navID, $dir, $multilang, $lan;

	$que  	= "SELECT navid, name FROM `morp_cms_nav` WHERE (sichtbar=1 AND lang=$lang AND parent=$parentID AND ebene=2) ORDER BY `sort` ASC, navid ASC";
	$res 	= safe_query($que);
	$x = mysqli_num_rows($res);

	$ret = '';

	if($x < 1) {
		$que  	= "SELECT navid, name FROM `morp_cms_nav` WHERE sichtbar=1 AND lang=$lang AND navid=$parentID";
		$res 	= safe_query($que);
	}

	while($rw = mysqli_fetch_object($res)) {
		$lnk = $dir.($multilang ? $lan.'/' : '').$navID[$rw->navid];
		$txt = $rw->name;

		$subsub = getSubSubNavigation($lang, $rw->navid, '<li><a href="#lnk#" class="einzug">#txt#</a></li>');

		if($subsub)	$ret .= str_replace(array("#lnk#", "#txt#"), array('#', $txt), $linkDesign).$subsub;
		else 		$ret .= str_replace(array("#lnk#", "#txt#"), array($lnk, $txt), $linkDesign);

	}

	return $ret;
}

function getSubSubNavigation($lang, $parentID, $linkDesign='<li><a href="#lnk#">#txt#</a></li>') {
	global $navID, $dir, $multilang, $lan;

	$que  	= "SELECT navid, name FROM `morp_cms_nav` WHERE (sichtbar=1 AND lang=$lang AND parent=$parentID AND ebene=3) ORDER BY `sort` ASC, navid ASC";
	$res 	= safe_query($que);
	$x = mysqli_num_rows($res);

	$ret = '';

	if($x < 1) { }
	else {
		while($rw = mysqli_fetch_object($res)) {
			$lnk = $dir.($multilang ? $lan.'/' : '').$navID[$rw->navid];
			$txt = $rw->name;
			$ret .= str_replace(array("#lnk#", "#txt#"), array($lnk, $txt), $linkDesign);
		}
	}

	return $ret;
}

// LOGIN *******************************************************************************
//**************************************************************************************

function getEventConfirmed($eventid, $mid) {
	$sql = "SELECT * FROM morp_event_zusage WHERE eventid=$eventid AND mid=$mid";
	$res = safe_query($sql);
	return $x = mysqli_num_rows($res);
}

function getEventDetails($event) {
	$event_arr = array();

	$sql = "SELECT * FROM morp_events WHERE eventid=$event";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);

	$event_arr["name"] = $row->eventName;
	$event_arr["datum"] = $row->eventDatum;
//	$event_arr["start"] = $row->eventStartZeit;
	$event_arr["ort"] = $row->eventOrt;
	$event_arr["text"] = $row->eventText;
	$event_arr["begleitung"] = $row->eventBegleitung;
	$event_arr["anzahl"] = $row->eventAnzahlTeilnehmer;

	return $event_arr;
}

function getform($uname, $em) {
	return '
	<p>Um Ihr Passwort zurückzusetzen, benötigen wir folgende Informationen:<br><br></p>
	<form name="getlog" method="post">

		<input type="hidden" name="newpass" value="1" />

		<div class="form-group">
			<label>Ihre Login Name</label>
			<input type="text" class="form-control" name="uname" id="uname" value="'.$uname.'" placeholder="Ihre Login Name" />
		</div>
		<div class="form-group">
			<label>Ihre E-Mail Adresse</label>
			<input type="text" class="form-control" name="mail" id="mail" value="'.$em.'" placeholder="Ihre E-Mail Adresse" />
		</div>
		<button type="submit" class="btn btn-info" />absenden</button>
	</form>
';
}



function newPW($min, $sec) {
	global $dir, $navID;

	return '
			<h2 itemprop="headline">Bitte geben Sie ein <u>neues</u> Passwort ein.</h2>
			<p>Das Passwort sollte mind. eine Zahl enthalten und insgesamt aus mind. 6 Zeichen bestehen.<br/>&nbsp;</p>

			<form name="golog" method="post" class="form">
				<div class="form-group">
					<label>Neues Passwort</label>
					<input type="password" name="pw1" id="pw1" value="" placeholder="Neues Passwort" class="form-control" />
				</div>
				<div class="form-group">
					<label>Neues Passwort wiederholen</label>
					<input type="password" name="pw2" id="pw2" value="" placeholder="Neues Passwort wiederholen" class="form-control" />
				</div>
				<div class="form-group">
					<input type="hidden" name="sec" id="sec" value="'.erstelleID($sec).'" class="form-control" />
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-success">Passwort setzen</button>
				</div>
				<div class="form-group">
					<a href="'.$dir.$navID[6].'" class="btn btn-info"><i class="fa fa-remove"></i> Abbrechen</a>
				</div>
			</form>

';
}

function isLike($mid, $table, $feld, $id) {
	$sql = "SELECT likesID FROM `$table` WHERE mid=$mid AND $feld=$id";
	$rs = safe_query($sql);
	return mysqli_num_rows($rs);
}

function countLikes($table, $feld, $id) {
	$sql = "SELECT likesID FROM `$table` WHERE $feld=$id";
	$rs = safe_query($sql);
	return mysqli_num_rows($rs);
}

function hasComment($table, $feld, $id) {
	$sql = "SELECT commentsID FROM `$table` WHERE $feld=$id";
	$rs = safe_query($sql);
	return mysqli_num_rows($rs);
}

function isMyComment($mid, $table, $feld, $id) {
	$sql = "SELECT commentsID FROM `$table` WHERE mid=$mid AND $feld=$id";
	$rs = safe_query($sql);
	return mysqli_num_rows($rs);
}

function getCommentsTopics($active, $zielSeite) {
	global $navID, $dir;

	$sql = "SELECT * FROM `morp_cms_meinungen_topics` WHERE sichtbar=1";
	$rs = safe_query($sql);
	$topicList = '';

	while ($rw = mysqli_fetch_object($rs)) {
		$topicList .= '<a href="'.$dir.'?hn='.$zielSeite.'&cont='.$zielSeite.'&topic='.$rw->topicID.'"'.($active == $rw->topicID ? ' class="active"' : '').'><i class="fa fa-chevron-right"></i> '. $rw->topicTitle.'</a>';
	}

	return $topicList;
}


function getComments($table, $thisIdFeld, $feld, $id, $mid, $last=0) {
	if($last) $sql = "SELECT * FROM `$table` WHERE $feld=$id ORDER BY $thisIdFeld DESC LIMIT 0,".$last;
	else $sql = "SELECT * FROM `$table` WHERE $feld=$id ORDER BY $thisIdFeld ASC";

	$rs = safe_query($sql);
	$comments = '';

	while ($rw = mysqli_fetch_object($rs)) {
		$besitzer = $rw->mid;
		if($besitzer) $profile = getProfile($besitzer);

		$comments .= '
			<div class="comment">
				<p class="commentUser">'.$profile["vname"].' '.$profile["nname"].' schrieb am '.ts2dat($rw->dat).'</p>
				<p class="commentText" id="t'.$rw->$thisIdFeld.'">'.nl2br($rw->comment).'</p>
				'.( $besitzer==$mid ? '<textarea class="form-control mt1 commentOff" id="c'.$rw->$thisIdFeld.'">'.($rw->comment).'</textarea>
						<button class="btn btn-info upd commentOff" id="b'.$rw->$thisIdFeld.'" ref="'.$rw->$thisIdFeld.'"><i class="fa fa-save"></i></button> &nbsp;
						<button type="button" class="btn btn-danger commentOff" id="d'.$rw->$thisIdFeld.'" data-toggle="modal" data-target="#deleteModal" data-todel="'.$rw->$thisIdFeld.'"><i class="fa fa-trash-o"></i></button>  &nbsp;
							<span class="v1"></span>
						<button class="btn btn-primary abbruch commentOff" id="a'.$rw->$thisIdFeld.'" ref="'.$rw->$thisIdFeld.'"><i class="fa fa-remove"></i></button>

					<p class="commentEdit"><a href="#" class="editComment" ref="'.$rw->$thisIdFeld.'"><i class="fa fa-edit"></i></a></p>
				' : '').'
			</div>
		';
	}

	return $comments;

}

function getComments2($table, $thisIdFeld, $feld, $id, $mid, $last=0) {
	if($last) $sql = "SELECT * FROM `$table` WHERE $feld=$id ORDER BY $thisIdFeld DESC LIMIT 0,".$last;
	else $sql = "SELECT * FROM `$table` WHERE $feld=$id ORDER BY $thisIdFeld ASC";

	$rs = safe_query($sql);
	$comments = '';

	while ($rw = mysqli_fetch_object($rs)) {
		$besitzer = $rw->mid;
		if($besitzer) $profile = getProfile($besitzer);

		$comments .= '
			<div class="comment">
				<p class="commentUser">'.$profile["vname"].' '.$profile["nname"].' schrieb am '.ts2dat($rw->dat).'</p>
				<p class="commentText" id="t'.$rw->$thisIdFeld.'">'.($besitzer==$mid ? '<a href="#" class="editComment" ref="'.$rw->$thisIdFeld.'"><i class="fa fa-edit"></i></a> ' : '').nl2br($rw->comment).'</p>
				'.( $besitzer==$mid ? '<textarea class="form-control mt1 commentOff" id="c'.$rw->$thisIdFeld.'">'.($rw->comment).'</textarea>
						<button class="btn btn-info upd commentOff" id="b'.$rw->$thisIdFeld.'" ref="'.$rw->$thisIdFeld.'"><i class="fa fa-save"></i></button> &nbsp;
						<button type="button" class="btn btn-danger commentOff" id="d'.$rw->$thisIdFeld.'" data-toggle="modal" data-target="#deleteModal" data-todel="'.$rw->$thisIdFeld.'"><i class="fa fa-trash-o"></i></button>  &nbsp;
							<span class="v1"></span>
						<button class="btn btn-primary abbruch commentOff" id="a'.$rw->$thisIdFeld.'" ref="'.$rw->$thisIdFeld.'"><i class="fa fa-remove"></i></button>
				' : '').'
			</div>
		';
	}

	return $comments;

}

function getProfile($besitzer) {
	if($besitzer) {
		$sql = "SELECT * FROM `morp_intranet_user` WHERE mid=$besitzer";
		$rs = safe_query($sql);
		$rw = mysqli_fetch_object($rs);

		$profile = array("nname"=>$rw->nname, "vname"=>$rw->vname, "fon"=>$rw->fon, "mail"=>$rw->email, "img"=>$rw->img, "setmefree"=>$rw->setMeFree, "abteilung"=>$rw->abteilung, "wantmail"=>$rw->wantEmail);

		return $profile;
	}
}

function getTags($id, $art='image') {
	$sql = "SELECT * FROM `morp_tags_list` t1, `morp_tags` t2 WHERE t1.tagID=t2.tagID AND art='$art' AND targetID=$id";
	$rs = safe_query($sql);
	$tagList = array();
	$arr = array();

	while ($rw = mysqli_fetch_object($rs)) {
		$tagList[]=array($rw->tagID, $rw->tag, $rw->tag_long);
	}

	return $tagList;
}

function getAllTags($id, $art='image') {
	$sql = "SELECT * FROM `morp_tags_list` t1, `morp_tags` t2 WHERE t1.tagID=t2.tagID AND art='$art'";
	$rs = safe_query($sql);
	$tagList = array();
	$arr = array();

	while ($rw = mysqli_fetch_object($rs)) {
		$tagList[]=array($rw->tagID, $rw->tag, $rw->tag_long);
	}

	return $tagList;
}


function holeID($key) {
	$key = substr($key,15);
	$len = strlen($key);
	$len = $len-10;
	return $key = substr($key,0,$len);
}

function erstelleID($id) {
	return substr(md5($row->mail),0,15).$id.substr(md5($row->mail),5,10);
}

//**************************************************************************************
//**************************************************************************************
//**************************************************************************************


function makeImage($f, $imgFolder='/intranet/images/icons/', $size='400x400', $allpage=0) {
	global $fileTypes;

// http://www.imagemagick.org/Usage/resize/

// echo "\n".$f." -- $imgFolder ##<br>";

	$fileParts = pathinfo($f);
//	print_r($fileParts);

	$fileName = trim($fileParts["filename"]);
	$imgFolder = trim($imgFolder);

	$thisFileTyp = strtolower($fileParts['extension']);

	if (in_array($thisFileTyp, $fileTypes)) {
//		echo "<hr>".$f.'<br>';
	    $im = new Imagick($f);
	    $imageprops = $im->getImageProperties();
//	    print_r($imageprops);

	    $width = $im->getImageWidth();
		$height = $im->getImageHeight();

		$noOfPagesInPDF = $im->getNumberImages();
		$max = $allpage ? $noOfPagesInPDF : 1;

//		echo 'number of pages: '.$noOfPagesInPDF.'<br>';

		if($thisFileTyp == "eps")	{
			exec("/usr/bin/convert convert -density 300 $f -resize $size\> ".$imgFolder.$fileName.".jpg");
//			echo '<img src="eps/eps-'.$f.'.jpg" ><br>';
		}
		elseif($thisFileTyp == "ai")	{
			exec("/usr/bin/convert convert -density 300 $f -resize $size\> ".$imgFolder.$fileName.".jpg");
//			echo '<img src="eps/ai-'.$f.'.png" ><br>';
			// for($i=1; $i<$noOfPagesInPDF; $i++) echo '<img src="eps/ai-'.$f.'-'.$i.'.jpg" ><br>';
		}
		elseif($thisFileTyp == "pdf")	{
//			exec("/usr/bin/convert convert -colorspace sRGB -density 300 $f -resize 240x240 eps/$f.jpg");
			exec("/usr/bin/convert convert  -limit thread 1 -colorspace RGB $f -background white -alpha deactivate -resize $size\> -colorspace sRGB -scene 1 ".$imgFolder.$fileName.".jpg");
//			for($i=0; $i<$noOfPagesInPDF; $i++) echo '<img src="eps/'.$f.'-'.$i.'.jpg" ><br>';
//			for($i=1; $i<=$max; $i++) echo '<img src="eps/pdf-0'.$x.'-'.$i.'.jpg" ><br>';
//			echo '<img src="'.$imgFolder.$fileName.'.jpg" ><br>';
		}
		elseif($thisFileTyp == "psd")	{
			exec("/usr/bin/convert convert -colorspace sRGB -density 300 $f -resize $size\> ".$imgFolder.$fileName.".jpg");
//			if($noOfPagesInPDF > 1) for($i=0; $i<$max; $i++) echo '<img src="eps/psd-'.$f.'-'.$i.'.jpg" ><br>';
//			else echo '<img src="eps/psd-'.$f.'.jpg" ><br>';
		}
		else {
			// echo '########## file: '.$fileName;
			exec("/usr/bin/convert convert -colorspace sRGB -density 300 $f -resize $size\> ".$imgFolder.$fileName.".jpg");
			// echo 'xx<img src="'.$imgFolder.$fileName.'.jpg" ><br>';
		}
		// else echo '<img src="'.$f.'" ><br>'.$f .' - '. $width. ' - ' . $height;

// echo "next<hr><br>";
	 #   $auxIMG->readImage($mytifspath."/".$files[$i]);

  #  $multiTIFF->addImage($auxIMG);
	}
}






global $tage;

$tage 		= array(1=>"Montag", 2=>"Dienstag", 3=>"Mittwoch", 4=>"Donnerstag", 5=>"Freitag", 6=>"Samstag", 7=>"Sonntag");

function seDay($begin,$end,$format,$sep){
	$pos1 = strpos($format, 'd');
	$pos2 = strpos($format, 'm');
	$pos3 = strpos($format, 'Y');

#$jd = GregorianToJD(10, 11, 1970);
#echo "$jd\n";
#$gregorian = JDToGregorian($jd);
#echo "$gregorian\n";

	$begin = explode($sep,$begin);
	$end = explode($sep,$end);

	$first = GregorianToJD($end[$pos2],$end[$pos1],$end[$pos3]);
	$second = GregorianToJD($begin[$pos2],$begin[$pos1],$begin[$pos3]);

	if($first > $second)
	   return $first - $second;
	else
	   return $second - $first;
}

function noCookie($id=19) {
	echo "here".textvorlage($id);
	return '<div class="hinweisNoCookie">'.textvorlage($id).'</div>';
}

//**************************************************************************************
//**************************************************************************************


function get_time_difference($start_time_o, $end_time_o, $art="m"){
    $start_time = explode(":", $start_time_o);
    $end_time = explode(":", $end_time_o);

    $start_time_stamp = mktime($start_time[0], $start_time[1], 0, 0, 0, 0);
    $end_time_stamp = mktime($end_time[0], $end_time[1], 0, 0, 0, 0);

    $time_difference = $end_time_stamp - $start_time_stamp;

	if ($art == "m") return $time_difference/60;
	elseif ($art == "s") return $time_difference;
    elseif ($art == "t") return gmdate("H:i", $time_difference);
}


function setLink($input, $icon=' <i class="fa fa-external-link textLink"></i> ', $class='') {
	global $dir, $navID, $lan, $multilang;

	$array = explode("<a:", $input);
	$anzahl = count($array);

	$ret = '';

	if($anzahl > 1) {
		// echo "LINK GEFUNDEN";

		for($i=0; $i<$anzahl; $i++) {
			if($i>0) {
				$array_inner = explode(":>", $array[$i]);

				$link = trim($array_inner[0]);
				$add_text = $array_inner[1];

				if(isin('^http', $link)) $set_link = '<a href="'.$link.'" target="_blank" '.$class.'>'.$icon;
				elseif(isin('@', $link)) $set_link = '<a href="mailto:'.$link.'" class="mail">';
				else {
					$set_link = '<a href="'.$dir.($multilang ? $lan.'/' : '').$navID[$link].'" class="'.$class.'">'.$icon;
				}
				$ret .= $set_link.$add_text;
			}
			else $ret .= $array[$i];

		}
	}
	else $ret = $input;

	return $ret;
}


//**************************************************************************************
//**************************************************************************************


function set_nav($string, $id=0, $ebene=1, $setLang=0) {
	global $cid, $hn_id, $lang, $navID, $lan, $dir;

	if($ebene > 1) 	$sql = "SELECT name, navid, lnk FROM `morp_cms_nav` WHERE ebene=$ebene AND `parent`=$id AND sichtbar=1 AND lang=$lang ORDER BY `sort`";
	else	 		$sql = "SELECT name, navid, lnk FROM `morp_cms_nav` WHERE navid=$id AND sichtbar=1 AND lang=$lang ORDER BY `sort`";

	// echo $sql;
	$res = safe_query($sql);

	$nav = '';

	while ($row = mysqli_fetch_object($res)) {
		$name = $row->name;
		$nid = $row->navid;
		echo $lnk = $row->lnk;

		if($lnk) {
			if(isin("http", $lnk)) {
				$getUrl = $lnk;
			}
			else {
				$getUrl = getUrl($lnk, $lan, $setLang);
			}
		}
		else $getUrl = getUrl($nid, $lan, $setLang);

		$nav .= str_replace(array("name", "url"), array($name, $getUrl), $string).'
';
		// '       <li><a href="'.$dir.$lan.'/'.$sprungUrl.eliminiere($name).'-'.$aid.'/">'.($row->art2 == 1 ? $link.' ' : '').''.$name.'</a></li>';

	}

	return $nav;
}

function get_db_field($id, $field, $table, $tid) {
	$sql = "SELECT $field FROM $table WHERE $tid=".$id;
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);

	return $row->$field;
}

function get_2_db_field($id, $field, $field2, $table, $tid) {
	$sql = "SELECT $field, $field2 FROM $table WHERE $tid=".$id;
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);

	return $row->$field.' '.$row->$field2;
}

function repl($raus, $rein, $val) {
	//$raus = "/".$raus."/";
	//$rein = $rein;
	//$tmp = preg_replace($raus, $rein, $val);
	$tmp = str_replace($raus, $rein, $val);
	return $tmp;
}
function isin($suche, $val) {
	$suche = "/".$suche."/";
	return preg_match($suche, $val);
}

function getUrl($id, $lan="de", $setLang=0) {
	global $morpheus, $dir, $navID;

	if($setLang) {
		if($morpheus["home_ID"] == $id && $lan == "de") 	$ziel = $dir;
		elseif ($morpheus["home_ID"] == $id) 				$ziel = $dir.$lan.'/';
		else 												$ziel = $dir.$lan.'/'.$navID[$id];

	}
	else {
		if($morpheus["home_ID"] == $id) 	$ziel = $dir;
		else 								$ziel = $dir.$navID[$id];
	}

	return $ziel;
}


function deleteImage($delimg, $edit, $imgFolder) {
	global $table, $tid, $mylink;

	$sql = "UPDATE $table SET $delimg='' WHERE $tid=$edit";
	$res = safe_query($sql);

	$foto = $imgFolder . $_GET["foto"];

	// DELETE `morp_cms_image` HERE !!!!
	unlink($foto);

}


function saveMorpheusForm($edit, $neu) {
	global $arr_form, $table, $tid, $mylink;

	$sql = '';

	foreach($arr_form as $arr) {
		$tmp = $arr[0];
		$val = $_POST[$tmp];

		if ($arr[3] == "date" || $arr[3] == "date2") 	$sql .= "`".$tmp. "`='" .($val == '00.00.0000' || !$val ? '0000-00-00' : us_dat($val)). "', ";
		elseif ($arr[3] == "float") 	$sql .= "`".$tmp. "`='" .us_p($val). "', ";
		elseif ($arr[2] == "chk") 	$sql .= "`".$tmp. "`='" .($val == 'on' ? 1 : 0). "', ";
		elseif ($arr[2] == "multisel")  $sql .= "`".$tmp. "`='" .implode(',',$val). "', ";
		elseif ($arr[1] == "CONFIG" || $arr[2] == "img") {}
		elseif ($tmp != "region") $sql .= "`".$tmp. "`='" .addslashes($val). "', ";
	}

	$sql = substr($sql, 0, -2);

	if ($neu) {
		$sql  = "INSERT $table set $sql";
		$res  = safe_query($sql);
		$edit = mysqli_insert_id($mylink);
		unset($neu);
	}
	else {
		$sql = "UPDATE $table SET $sql WHERE $tid=$edit";
		$res = safe_query($sql);
	}

	// echo $sql;

	return $edit;
}


function setMorpheusForm($row, $arr, $folder="", $edit, $from, $setid, $imgSize='800x800', $placeholder='') {
	global $morpheus, $table, $dir, $navID, $cid, $js;

	$echo = '';

	$get = $arr[0];

	if ($arr[1] == "CONFIG") {
		$echo .= $arr[2];
	}


	elseif ($arr[2] == "sellan") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label>
				<select class="form-control" id="'.$arr[0].'" name="'.$arr[0].'">
		';

		foreach($morpheus["lan_arr"] as $key=>$val) {
			$echo .= '<option value="'.$key.'" '. ($row->lan == $key ? ' selected' : '') .'>'.$val.'</option>';

		}
		$echo .= '</select>
			</div>';
	}


	elseif ($arr[2] == "dropdown_array") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label>
				<select class="form-control" id="'.$arr[0].'" name="'.$arr[0].'">
		';

		foreach($arr[3] as $key=>$val) {
			$echo .= '<option value="'.$key.'" '. ($row->$get == $key ? ' selected' : '') .'>'.$val.'</option>';

		}
		$echo .= '</select>
			</div>';
	}



	elseif ($arr[2] == "fotoG") {
		$pos = substr($arr[0],3,strlen($arr[0]));
		$col = substr($arr[0],0, (strlen($arr[0])-1) );

		$navUp = '';
		$navDown = '';

		if($pos) {
			if($pos > 1) $navUp = '<a href="?edit='.$edit.'&up='.$pos.'&col='.$col.'"><i class="fa fa-toggle-up"></i></a>';
			else $navUp = '<i class="fa fa-toggle-up inactive gray"></i>';
			$navDown = '<a href="?edit='.$edit.'&down='.$pos.'&col='.$col.'"><i class="fa fa-toggle-down"></i></a>';
		}

		$echo .= '
			<div class="fotoG">
				<input type=hidden name='.$arr[0].' id='.$arr[0].' value="' .$row->$get.'">

				<a href="image_folder_upload.php?id='.$edit.'&tbl='.$table.'&from='.$from.'&tn='.$morpheus["img_size_news_tn"].'&imgid='.$arr[0].'&folder='.$folder.'&setid='.$setid.'">';

		if ($row->$get) $echo .=  '
					<img src="../mthumb.php?w=200&amp;src=images/'.$folder.$row->$get.'"></a>

					<div class="inner">
						'.$arr[1].' &nbsp; &nbsp; &nbsp;
						'.$navUp.'
						'.$navDown.' &nbsp; &nbsp; &nbsp;
						<a href="?delimg='.$arr[0].'&edit='.$edit.'&foto='.$row->$get.'" style="float:right; margin: 4px 0 0;"><i class="fa fa-trash-o"></i></a>
					</div>

		';
		else $echo .=  '<b>Foto</b>: bitte wählen<br><i class="fa fa-camera-retro"></i></a>';

		$echo .= '</div>';

	}


	elseif ($arr[2] == "foto") {
		$pos = substr($arr[0],3,strlen($arr[0]));
		$col = substr($arr[0],0, (strlen($arr[0])-1) );

		$navUp = '';
		$navDown = '';

		if($pos > 0 && $pos < 20) {
			if($pos > 1) $navUp = '<a href="?edit='.$edit.'&up='.$pos.'&col='.$col.'"><i class="fa fa-toggle-up"></i></a>';
			else $navUp = '<i class="fa fa-toggle-up inactive gray"></i>';
			$navDown = '<a href="?edit='.$edit.'&down='.$pos.'&col='.$col.'"><i class="fa fa-toggle-down"></i></a>';
		}

		$echo .= ''.$arr[1].'<div class="foto"><input type=hidden name='.$arr[0].' id='.$arr[0].' value="' .$row->$get.'">'.$navUp.''.$navDown.'<a href="image_folder_upload.php?id='.$edit.'&tbl='.$table.'&from='.$from.'&tn='.$morpheus["img_size_news_tn"].'&imgid='.$arr[0].'&folder='.$folder.'&setid='.$setid.'">';

		if ($row->$get) $echo .=  '<img src="../mthumb.php?w=200&amp;src=images/'.$folder.$row->$get.'"></a> &nbsp; &nbsp; <a href="?delimg='.$arr[0].'&edit='.$edit.'&foto='.$row->$get.'"><img src="images/delete.gif" width="9" height="10" alt="Bild löschen" border="0" hspace="6"></a>';
		else $echo .=  '<b>Foto</b>: bitte wählen<br><i class="fa fa-camera-retro"></i></a>';

		$echo .= '</div>';

	}



	elseif ($arr[2] == "img") {
		$echo .= '<div class="imgCol"><input type="hidden" name='.$arr[0].' id='.$arr[0].' value="' .$row->$get.'">
			<a href="'.$dir.'page/img_upload.php?id='.$edit.'&tbl='.$table.'&imgid='.$arr[0].'&folder='.$folder.'&setid='.$setid.'&imgSize='.$imgSize.'" data-title="" data-width="500" data-toggle="lightbox" data-gallery="remoteload" class="btn btn-primary"><i class="fa fa-image"></i> &nbsp; '.$arr[1].' hochladen &nbsp; <i class="fa fa-upload"></i></a>'.($row->$get ? ' <!--<a href="'.$dir.$navID[$cid].'delimg-'.$arr[0].'+'.$edit.'/" class="btn btn-danger"><i class="fa fa-trash-o"></i>&nbsp;</a>-->' : '' ) . '</div>';

	}


	elseif ($arr[2] == "chk") {
		$echo .= '
        <div class="form-group">
            <input type="checkbox" name="'.$arr[0].'"  id="'.$arr[0].'"'.($row->$get ? ' checked' : '').'  />
            <div class="btn-group">
                <label for="'.$arr[0].'" class="btn btn-success">
                    <span class="fa fa-check" style="padding:3px 0"></span>
                    <span> </span>
                </label>
                <label for="'.$arr[0].'" class="btn btn-default active">
                    '.$arr[1].'
                </label>
            </div>
        </div>
';

	}


	elseif ($arr[2] == "sel") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label>
				<select  class="form-control" name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6]).'</select>
			</div>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel2") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label><select class="form-control" name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown2 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5]).'</select>
			</div>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel2m") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label><select class="form-control" name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown2multi ($row->$get, $arr[3], $arr[4], $arr[5], $arr[0], $arr[6]).'</select>
			</div>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel3") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label><select class="form-control" name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown3 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6], $arr[7], $arr[8]).'</select>
			</div>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel4") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label><select class="form-control" name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown4 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6], $arr[7], $arr[8]).'</select>
			</div>';
	}

	elseif ($arr[2] == "sel5") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label><select class="form-control" name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown5 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6], $arr[7], $arr[8], $arr[9]).'</select>
			</div>';
	}

	elseif ($arr[2] == "multisel") {
		$vals = explode(",", $row->$get);
		$sel = '';
		foreach($vals as $val) {
			$sel .= '				$("#'.$arr[0].'").multiList("select", "'.$val.'");
';
		}

		$echo .= '
			<div class="form-group multi">
				<label for="'.$arr[0].'">'.$arr[1].'</label>
				<ul name="'.$arr[0].'" id="'.$arr[0].'">
					'.multiselect ($row->$get, "morp_blog_kat", "fBlogKatID", "fKat", "fLanguage=1").'
				</ul>
				<button onclick="$(\'#'.$arr[0].'\').multiList(\'selectAll\'); return false;" >Select all</button>
				<button onclick="$(\'#'.$arr[0].'\').multiList(\'unselectAll\'); return false;" >Deselect all</button>
			</div>

			<script>
				$("#'.$arr[0].'").multiList();
'.$sel.'
			</script>

			';
	}


	elseif ($arr[3] == "date") {
		$dat = $row->$get;
		if($dat) $dat = euro_dat($dat);
		//else $dat = date("d.m.Y");
		else $dat = ("00.00.0000");

		$echo .= '
			<div class="form-group'.$arr[4].'">
				<label for="'.$arr[0].'">'.$arr[1].'</label>
				'.str_replace(array("#v#", "#n#"), array($dat, $arr[0]), $arr[2]).'
			</div>';
	}


	elseif ($arr[3] == "date2") {
		$dat = $row->$get;
		if($dat) $dat = euro_dat($dat);
		//else $dat = date("d.m.Y");
		else $dat = ("00.00.0000");

		// http://www.daterangepicker.com/

		$echo .= '
			<div class="form-group'.$arr[4].'">
				<label for="'.$arr[0].'">'.$arr[1].'</label>

<input data-date-format="dd.mm.yyyy" id="'.$arr[0].'" name="'.$arr[0].'">
<script>
$("#'.$arr[0].'").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
      "locale": {
      "format": "DD.MM.YYYY",
      "separator": " . ",
    }
}, function(start, end, label) {
});
</script>

			</div>';
	}

	elseif ($arr[3] == "float") {
		$dat = ger_p($row->$get);
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label>
 				'.str_replace(array("#v#", "#n#"), array($dat, $arr[0]), $arr[2]).'
 			</div>';
	}

	elseif ($arr[2] == "text") {
		$echo .= '
			<div class="form-group col">
				<label for="'.$arr[0].'">'.$arr[1].'</label>'.str_replace("#e#", $edit, $arr[3]).'
			</div>';
	}

	else {
		$placeholder = $placeholder ? $placeholder : $arr[4];
		$data = $row->$get ? $row->$get : $placeholder;
		$echo .= '
		<div class="form-group'.$arr[3].'">
			<label for="'.$arr[0].'">'.$arr[1].'</label>
			'. str_replace(
				array("#v#", "#n#", "#s#", "#db#", '#e#', '#id#', '#s1#', '#s0#', '#ph#'),
				array(stripslashes($data), $arr[0], '', $table2, $edit, $tid2, $sel1, $sel2, $arr[1]), $arr[2]).'
		</div>';
	}

	return $echo;

}

function setMorpheusFormTable($row, $arr, $folder="", $edit, $from, $setid) {
	global $morpheus, $table;

	$echo = '';

	$get = $arr[0];

	if ($arr[2] == "sellan") {
		$echo .= '<tr><td>'.$arr[1].'</td><td>
			<select name="'.$arr[0].'" id="'.$arr[0].'">
		';

		foreach($morpheus["lan_arr"] as $key=>$val) {
			$echo .= '<option value="'.$key.'" '. ($row->lan == $key ? ' selected' : '') .'>'.$val.'</option>';

		}
		$echo .= '</select></td></tr>';
	}
	elseif ($arr[2] == "foto") {
		$echo .= '<tr><td width="160">'.$arr[1].'</td><td><input type=hidden name='.$arr[0].' value="' .$row->$get.'" style="width:500px"><a href="image_folder_upload.php?id='.$edit.'&tbl='.$table.'&from='.$from.'&tn='.$morpheus["img_size_news_tn"].'&imgid='.$arr[0].'&folder='.$folder.'&setid='.$setid.'">';

		if ($row->$get) $echo .=  '<img src="../mthumb.php?w=200&amp;src=images/'.$folder.$row->$get.'"></a> &nbsp; &nbsp; <a href="?delimg='.$arr[0].'&edit='.$edit.'&foto='.$row->$get.'"><img src="images/delete.gif" width="9" height="10" alt="Bild löschen" border="0" hspace="6"></a>';
		else $echo .=  '<b>Foto</b>: bitte wählen</a>';

		$echo .= '</td></tr>';

	}

	elseif ($arr[2] == "chk") {
		$echo .= '<tr><td>'.$arr[1].'</td><td><input type="checkbox" value="1" name="'.$arr[0].'"'.($row->$get ? ' checked' : '').'></td></tr>';
	}

	elseif ($arr[2] == "sel") {
		$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6]).'</select></td></tr>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel2") {
		$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown2 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5]).'</select></td></tr>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel2m") {
		$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'" id="'.$arr[0].'">'.pulldown2multi ($row->$get, $arr[3], $arr[4], $arr[5], $arr[0], $arr[6]).'</select></td></tr>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel3") {
		$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'" disable>'.pulldown3 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6], $arr[7], $arr[8]).'</select></td></tr>';
		if ($arr[0] == "imgid") $image = pfad ($arr[0], $arr[3], $arr[4], $row->$get);
	}

	elseif ($arr[2] == "sel4") {
		$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'" style="width:100%;">'.pulldown4 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6], $arr[7], $arr[8]).'</select></td></tr>';
	}

	elseif ($arr[2] == "sel5") {
		$echo .= '<tr><td>'.$arr[1].'</td><td><select name="'.$arr[0].'" style="width:400px;">'.pulldown5 ($row->$get, $arr[3], $arr[4], $arr[0], $arr[5], $arr[6], $arr[7], $arr[8], $arr[9]).'</select></td></tr>';
	}

	elseif ($arr[3] == "date") {
		$dat = $row->$get;
		if($dat && $dat != "0000-00-00") $dat = euro_dat($dat);
		else $dat = '';
		$echo .= '<tr><td>'.$arr[1].'</td><td>'.str_replace(array("#v#", "#n#"), array($dat, $arr[0]), $arr[2]).'</td></tr>';
	}

	elseif ($arr[3] == "float") {
		$dat = ger_p($row->$get);
		$echo .= '<tr><td>'.$arr[1].'</td><td>'.str_replace(array("#v#", "#n#"), array($dat, $arr[0]), $arr[2]).'</td></tr>';
	}

	elseif ($arr[2] == "text") {
		$echo .= '<tr><td>'.$arr[1].'</td><td>'.str_replace("#e#", $edit, $arr[3]).'</td></tr>';
	}

	else $echo .= '<tr>
	<td>'.$arr[1].':</td>
	<td>'. str_replace(
				array("#v#", "#n#", "#s#", "#db#", '#e#', '#id#', '#s1#', '#s0#'),
				array(stripslashes($row->$get), $arr[0], 'width:500px;', $table2, $edit, $tid2, $sel1, $sel2),
		$arr[2]).'</td>
</tr>';

	return $echo;

}



function pfad ($feld, $tab, $wname, $id) {
	$sql = "SELECT * FROM $tab WHERE $feld=$id";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);
	$nm  = $row->$wname;

	return $nm;
}
function pdfname($pid) {
	$que  = "SELECT * FROM `morp_cms_pdf` WHERE pid=$pid";
	$res = safe_query($que);
	$row = mysqli_fetch_object($res);
	return $row->pname;
}

///// ZIP **********************************************************************
function unzip($file, $folder){

    $zip=zip_open(realpath(".")."/".$file);
    if(!$zip) {return("Unable to proccess file '{$file}'");}

    $e='';

    while($zip_entry=zip_read($zip)) {
       $zdir=dirname(zip_entry_name($zip_entry));
       $zname=zip_entry_name($zip_entry);

       if(!zip_entry_open($zip,$zip_entry,"r")) {$e.="Unable to proccess file '{$zname}'";continue;}
       if(!is_dir($folder.$zdir)) mkdirr($folder.$zdir,0777);

       // print "{$zdir} ---- {$zname} \n<br>";

       $zip_fs=zip_entry_filesize($zip_entry);
       if(empty($zip_fs)) continue;

       $zz=zip_entry_read($zip_entry,$zip_fs);

       $z=fopen($folder.$zname,"w");
       fwrite($z,$zz);
       fclose($z);
       zip_entry_close($zip_entry);

    }
    zip_close($zip);

    return($e);
}

function textvorlage($id) {
	global $lan;
	if(!$lan) $lan = "de";
 	$sql  = "SELECT $lan FROM morp_sprachdatei WHERE id=$id";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);
    return nl2br($row->$lan);
}

function mkdirr($pn,$mode=null) {

  if(is_dir($pn)||empty($pn)) return true;
  $pn=str_replace(array('/', ''),DIRECTORY_SEPARATOR,$pn);

  if(is_file($pn)) {trigger_error('mkdirr() File exists', E_USER_WARNING);return false;}

  $next_pathname=substr($pn,0,strrpos($pn,DIRECTORY_SEPARATOR));
  if(mkdirr($next_pathname,$mode)) {if(!file_exists($pn)) {return mkdir($pn,$mode);} }
  return false;
}

///// ZIP **********************************************************************


function getDownloadDirectoy ($pgid, $dots="../") {
	$sql  = "SELECT pgart FROM morp_cms_pdf_group where pgid=$pgid";
	$res  = safe_query($sql);
	$row  = mysqli_fetch_object($res);
 	$art  = $row->pgart;

	if ($art < 2) {
		$pfad 	= $dots."pdf/";
	} else {
		$pfad 	= $dots."secure/dfiles/vxcDfgH/";
	}
	// echo $pfad;
	return $pfad;
}

function getPDFtargetDirectoy ($pgid) {
	$rootDir = dirname(__FILE__);
	$rootDir = str_replace("nogo", "", $rootDir);

	$sql  = "SELECT pgart FROM morp_cms_pdf_group WHERE pgid=$pgid";
	$res  = safe_query($sql);
	$row  = mysqli_fetch_object($res);
 	$art  = $row->pgart;

	if ($art < 2) {
		$pfad 	= "pdf/";
	} else {
		$pfad 	= "secure/dfiles/vxcDfgH/";
	}
	// echo $pfad;
	return $rootDir.$pfad;
}

function multiselect ($vals, $table, $tID, $cols, $where=1) {
	$sql = "SELECT * FROM $table WHERE $where ORDER BY $cols";
	$res = safe_query($sql);
	$ms = '';

	$vals = explode(",",$vals);

	while ($row = mysqli_fetch_object($res)) {
		$ms .= '<li value="'.$row->$tID.'">'.$row->$cols.'</li>';

	}
	return $ms;
}


function pulldown ($tp, $tab, $wname, $wid, $gruppe=0, $spalte=0) {
	if ($gruppe) 	$query = "SELECT * FROM $tab WHERE $spalte=$gruppe ORDER BY $wname";
	else 			$query = "SELECT * FROM $tab ORDER BY $wname";

	//echo $query;

	$result = safe_query($query);
	$pd = "<option value=\"0\"> </option>\n";

	while ($row = mysqli_fetch_object($result)) {
		if ($row->$wid == $tp) $sel = "selected";
		else $sel = "";

		$nm = $row->$wname;
		$pd .= "<option value=\"" .$row->$wid ."\" $sel>$nm</option>\n";
	}
	return $pd;
}

function pulldown2 ($wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID) {
	$sql = "SELECT * FROM $tabelleGet ORDER BY $tabelleGetName";
	$res = safe_query($sql);
	$pd = "<option value=\"\">bitte wählen</option>\n";

	// echo "$wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID";

	while ($row = mysqli_fetch_object($res)) {
		if ($row->$tabellenID == $wert) $sel = "selected";
		else $sel = "";

		$nm = $row->$tabelleGetName;
		$pd .= "<option value=\"" .$row->$tabellenID ."\" $sel>$nm</option>\n";
	}
	return $pd;
}

function pulldown2multi ($wert, $tabelleGet, $tabelleGetName, $tabelleGetName2, $thisTabellenID, $tabellenID) {
	$sql = "SELECT * FROM $tabelleGet ORDER BY $tabelleGetName";
	$res = safe_query($sql);
	$pd = "<option value=\"\">bitte wählen</option>\n";

	// echo "$wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID";

	while ($row = mysqli_fetch_object($res)) {
		if ($row->$tabellenID == $wert) $sel = "selected";
		else $sel = "";

		$nm = $row->$tabelleGetName;
		$nm2 = $row->$tabelleGetName2;
		$pd .= "<option value=\"" .$row->$tabellenID ."\" $sel>$nm / $nm2</option>\n";
	}
	return $pd;
}

function pulldown3 ($wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID, $parTabelle, $parTabelleID, $parID) {
	$sql = "SELECT * FROM $tabelleGet t1, $parTabelle t2 WHERE t1.$parTabelleID = t2.$parTabelleID AND t2.$parTabelleID=$parID ORDER BY $tabelleGetName";
	$res = safe_query($sql);
	$pd = "<option value=\"\">bitte wählen</option>\n";

	// echo "$wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID";

	while ($row = mysqli_fetch_object($res)) {
		if ($row->$tabellenID == $wert) $sel = "selected";
		else $sel = "";

		$nm = $row->$tabelleGetName;
		$pd .= "<option value=\"" .$row->$tabellenID ."\" $sel>$nm</option>\n";
	}
	return $pd;
}

function pulldown4 ($wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID, $parTabelle, $parTabelleNm) {
	//echo "($wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID, $parTabelle, $parTabelleNm)";
	//echo "<br>";
	$sql = "SELECT * FROM $tabelleGet t1, $parTabelle t2 WHERE t1.$tabellenID = t2.$tabellenID ORDER BY $tabelleGetName";
	$res = safe_query($sql);
	$pd = "<option value=\"\">bitte wählen</option>\n";

	// echo "$wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID";

	while ($row = mysqli_fetch_object($res)) {
		if ($row->$thisTabellenID == $wert) $sel = "selected";
		else $sel = "";

		$nm = $row->$tabelleGetName.' / '.$row->$parTabelleNm;
		$pd .= "<option value=\"" .$row->$thisTabellenID ."\" $sel>$nm</option>\n";
	}
	return $pd;
}

function pulldown5 ($wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID, $parTabelle, $parTabelleCol, $filterWert) {
	// echo "($wert, $tabelleGet, $tabelleGetName, $thisTabellenID, $tabellenID, $parTabelle, $parTabelleCol, $filterWert)";
	// echo "<br>";

	$sql = "SELECT * FROM $tabelleGet t1, $parTabelle t2 WHERE t1.$parTabelleCol = t2.$parTabelleCol AND  t2.$parTabelleCol = '$filterWert' ORDER BY $tabelleGetName";
	$res = safe_query($sql);
	$pd = "<option value=\"\">bitte wählen</option>\n";

	while ($row = mysqli_fetch_object($res)) {
		if ($row->$tabellenID == $wert) $sel = "selected";
		else $sel = "";

		$nm = $row->$tabelleGetName;
		$pd .= "<option value=\"" .$row->$tabellenID ."\" $sel>$nm</option>\n";
	}
	return $pd;
}

function wort_anz($inhalt, $max=150, $punkte=1) {
	$text = explode(" ", $inhalt);
	$sum = 0;
	$anz = count($text);
	$new = '';
	$ct  = 0;
	for($i=0; $i<= $anz; $i++) {
		if(array_key_exists($i, $text)) {
			$wort = $text[$i];
			$c = strlen($wort)+1;   // +1 wegen leerzeichen
			$sum += $c;
			if ($sum < $max) { $new .= $wort.' ';	 $ct++; }
		}
	}
	if ($punkte) $new .= $ct < $anz ? '...' : '';

	return $new;
}

function getVorlagenText($id, $lang, $dir) {
	$sql  = "SELECT content, `tid` FROM `morp_cms_content` WHERE cid=$id";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);
    $this_text  = $row->content;
	$templ_id = $row->tid;
	$alle_bricks_aus_einem_template = get_cms_text($this_text, $lang, $dir);
	include("templates/". ($templ_id ? $templ_id : 1) .".php");
	return str_replace(array("#cont#"), array($alle_bricks_aus_einem_template), $template);
}

function links($text) {
  $p[] = '"(( |^)((ftp|http|https){1}://)[-a-zA-Z0-9@:%_+.~#?&//=]+)"i';
  $r[] = '<a href="1" target="_blank">\1</a>';
  $p[] = '"( |^)(www.[-a-zA-Z0-9@:%_+.~#?&//=]+)"i';
  $r[] = '\1<a href="http://\2" target="_blank">\\2</a>';
  $p[] = '"([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})"i';
  $r[] = '<a href="mailto:\1">\\1</a>';
  $text = preg_replace($p, $r, $text);
  return $text;
}
#$text = "www.php4u.net ist meine Lieblingsseite.<br/> ";
#$text.= "michi@php4u.net ist die Email des Authors.";
#echo links($text);

function redirect ($dir) {
	# echo $dir;
	Header( "HTTP/1.1 301 Moved Permanently" );
	Header( "Location: ".$dir);
	Header("Connection: close");
}

function dat_monat($dat, $jahr=0, $month=0) {
	global $lan;

	$monate = array(1=>"Januar", 2=>"Februar", 3=>"März", 4=>"April", 5=>"Mai", 6=>"Juni", 7=>"Juli", 8=>"August", 9=>"September", 10=>"Oktober", 11=>"November", 12=>"Dezember");

/*
	if($lan == "de") $monate = array(1=>"Jan", 2=>"Feb", 3=>"Mär", 4=>"Apr", 5=>"Mai", 6=>"Jun", 7=>"Jul", 8=>"Aug", 9=>"Sep", 10=>"Okt", 11=>"Nov", 12=>"Dez");
	else if($lan == "en") $monate = array(1=>"Jan", 2=>"Feb", 3=>"Mar", 4=>"Apr", 5=>"May", 6=>"Jun", 7=>"Jul", 8=>"Aug", 9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dec");
	else if($lan == "fr") $monate = array(1=>"Jan", 2=>"Fév", 3=>"Mar", 4=>"Avr", 5=>"Mai", 6=>"Jui", 7=>"Jui", 8=>"Aoû", 9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Déc");
*/

	if ($dat == "0000-00-00") return;
	$dat = explode("-", $dat);
	$month = ceil($dat[1]);
	if($month) $dat = $monate[$month] .($jahr ? " " .$dat[0] : '');
	else $dat = $dat[2] .". " .$monate[$month] .($jahr ? " " .$dat[0] : '');
	return $dat;
}

function email_style() {
	return '<style Type="text/css">
<!--
body { background-color: #ffffff; }
p, h1, h2, h3, td, a	{ font-family: 	Arial, Verdana; font-size:	13px; line-height: 	16px; color: #666666; margin: 0 0 1px 0; padding: 0; }
td { padding:0;	margin:0;}
h1, h2  { font-weight: 	bold; margin: 0 0 10px 0; }
h1  { font-size: 22px; font-weight: normal; line-height: 20px; margin: 0 0 12px 0; color: #bbe630; }
a:link, a:visited, a:hover	{ font-weight: normal; text-decoration: underline; }
a:hover	{ color: #bbe630; }
//-->
</style>';
}

function setpw () {
	$pool = "qwertzupasdfghkyxcvbnm";
	$pool .= "23456789";
	$pool .= "WERTZUPLKJHGFDSAYXCVBNM";

	srand ((double)microtime()*1000000);

	for($index = 0; $index < 6; $index++) {
	    $pass .= substr($pool,(rand()%(strlen ($pool))), 1);
	}
	return $pass;
}
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
function no_injection ($str, $artikel=0) {
	if ($str) {
		$str_new = rawurldecode ($str);
		$str_new = preg_replace("=<[^>]*>=siU","",$str_new);

		if ($str_new != $str) unset($str);

		$ersetz = array(
			"select"=>"",
			"insert"=>"",
			"update"=>"",
			"delete"=>"",
			"drop"=>"",
			"function"=>"",
			"script"=>"",
			"alert"=>"",
			"having"=>""
		);

		if ($artikel) {
			$tmp = explode("+", $str);
			foreach ($tmp as $wort) {
				if (in_array($wort, $ersetz)) $nogo = 1;
			}
			if ($nogo) unset($str);
		}

		else {
			# keine unterstuetzung von str_ireplace. daher vergleich der strings vorher und nachher
			$str_lower 	= strtolower($str);
			$str_new 	= str_replace(array_keys($ersetz), array_values($ersetz), $str_lower);

			if ($str_lower != $str_new) unset($str);
		}
		return $str;
	}
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

function menge($menge) {
	global $waehrung;

	if ($menge > 999) {
		$x = strlen($menge);

		if ($waehrung == "&pound;")	$menge = substr($menge, 0, ($x-3)).','.substr($menge, ($x-3), 3);
		else $menge = substr($menge, 0, ($x-3)).'.'.substr($menge, ($x-3), 3);
	}
	return $menge;
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
global $ersetzung, $ersetzungIMG;

$ersetzung = array(
("Ò") => "o",
("Ó") => "o",
("ò") => "o",
("ó") => "o",
("Ô") => "o",
("ô") => "o",
("Ú") => "U",
("ú") => "u",
("Ù") => "u",
("ù") => "u",
("Û") => "U",
("û") => "u",
("Á") => "a",
("À") => "a",
("à") => "a",
("á") => "a",
("â") => "a",
("é") => "e",
("è") => "e",
("É") => "e",
("È") => "e",
("Ê") => "e",
("ê") => "e",
("ä") => "ae",
("ä") => "ae",
("ö") => "oe",
("ü") => "ue",
("Ä") => "ae",
("Ö") => "oe",
("Ü") => "ue",
("ö") => "oe",
("ü") => "ue",
("Ä") => "ae",
("Ö") => "oe",
("Ü") => "ue",
("ß") => "ss",
("ß") => "ss",
("&") => "+",
("Í") => "I",
("Ì") => "I",
("í") => "i",
("ì") => "i",
("î") => "i",
("Î") => "I",
(":") => "",
(",") => "",
// (".") => "",
("´") => "",
("`") => "",
("'") => "",
("“") => "",
("”") => "",
('"') => "",
("„") => "",
("\"") => "",
("\|") => "",
("?") => "",
("!") => "",
("$") => "",
("=") => "",
("*") => "",
("&copy;") => "",
("&reg;") => "",
("®") => "",
("@") => "",
("€") => "",
("©") => "",
("°") => "",
("%") => "",
("(") => "",
(")") => "",
("™") => "I",
("+") => "-",
("¾") => "",
("½") => "",
("¼") => "",
("²") => "",
("³") => "",
(".") => "-",
(" ") => "-",
("/") => "-",
("---") => "-",
("--") => "-",
("-") => "-",

);

$ersetzungIMG = array(
("ß") => "ss",
("ß") => "ss",
("&") => "",
(":") => "",
(",") => "",
("?") => "",
(")") => "",
("(") => "",
("!") => "",
("$") => "",
("=") => "",
("*") => "",
(" ") => "-",
("---") => "-",
("--") => "-",
("-") => "-",

);

function eliminiere ($nm) {
	global $ersetzung;

	$nm = trim(strtolower($nm));
	if (preg_match("/\"/", $nm)) $nm = str_replace("\"", "", $nm);
	$nm = str_replace(array_keys($ersetzung), array_values($ersetzung), $nm);

	return $nm;
}

function eliminiereIMG ($nm) {
	global $ersetzungIMG;

	$nm = strtolower($nm);
	$nm = str_replace(array(chr(136),chr(204)), '', $nm);
echo "\n";

	for($i=0;$i<=strlen($nm);$i++) {
		echo $nm[$i].'-'.ord($nm[$i])."\n";
	}

	if (preg_match("/\"/", $nm)) $nm = str_replace("\"", "", $nm);
echo	$nm = str_replace(array_keys($ersetzungIMG), array_values($ersetzungIMG), $nm);
echo "\n";
	return $nm;
}

function eliminiereBack ($nm) {
	$back_str 		= array("/\+/", "/ae/", "/oe/", "/ue/", "/Ae/", "/Oe/", "/Ue/");
	$back_str_new 	= array(" ", "ä", "ö", "ü", "Ä", "Ö", "Ü");
	$search_str 	= repl("/", "", implode("|", $back_str));

	if (preg_match("/".$search_str."/", $nm)) 	$nm = preg_replace($back_str, $back_str_new, $nm);

	return $nm;
}

# # # # # # #
# # # # # # #
function email_code ($string) {
	$safe_string = "";
	# $string = strtolower($string);
	$string_length = strlen($string);

	$safe_array = array("0"=>"&#48;", "1"=>"&#49;", "2"=>"&#50;", "3"=>"&#51;", "4"=>"&#52;", "5"=>"&#53;", "6"=>"&#54;", "7"=>"&#55;", "8"=>"&#56;", "9"=>"&#57;", "a"=>"&#97;", "b"=>"&#98;", "c"=>"&#99;", "d"=>"&#100;", "e"=>"&#101;", "f"=>"&#102;", "g"=>"&#103;", "h"=>"&#104;", "i"=>"&#105;", "j"=>"&#106;", "k"=>"&#107;", "l"=>"&#108;", "m"=>"&#109;", "n"=>"&#110;", "o"=>"&#111;", "p"=>"&#112;", "q"=>"&#113;", "r"=>"&#114;", "s"=>"&#115;", "t"=>"&#116;", "u"=>"&#117;", "v"=>"&#118;", "w"=>"&#119;", "x"=>"&#120;", "y"=>"&#121;", "z"=>"&#122;", "-"=>"&#45;", "."=>"&#46;", ":"=>"&#58;", "@"=>"&#64;", "_"=>"&#95;", "A"=>"&#65;", "B"=>"&#66;", "C"=>"&#67;", "D"=>"&#68;", "E"=>"&#69;", "F"=>"&#70;", "G"=>"&#71;", "H"=>"&#72;", "I"=>"&#73;", "J"=>"&#74;", "K"=>"&#75;", "L"=>"&#76;", "M"=>"&#77;", "N"=>"&#78;", "O"=>"&#79;", "P"=>"&#80;", "Q"=>"&#81;", "R"=>"&#82;", "S"=>"&#83;", "T"=>"&#84;", "U"=>"&#85;", "V"=>"&#86;", "W"=>"&#87;", "X"=>"&#88;", "Y"=>"&#89;", "Z"=>"&#90;", " "=>" ");

	for ($i = 0; $i < $string_length; $i++) {
	       $safe_string .= $safe_array[$string["$i"]];
	}

	return $safe_string;

}


function email_check($email) {
   	$p = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';
   	$p.= '@([-a-z0-9]+\.)+([a-z]{2,3}';
   	$p.= '|info|arpa|aero|coop|name|museum)$/ix';
   	return preg_match($p, $email);
}

# # # # # # #
# # # # # # #

function show_galerie($res) {
	global $dir, $morpheus;

	$x = 0;

	while ($row = mysqli_fetch_object($res)) {
		$tit 	= $row->gtexten;
		$desc 	= $row->gtextde;
		$img 	= $row->gname;
		$tn 	= $row->tn;
		$ordner = $row->gnname;

		$output .= $x < 1 ? '<div class="col-12"><hr><h2 itemprop="headline">'.$row->gntextde.'</h2><hr></div>' : '';

		$output .= '
		<div class="col-3 myGal"><a href="'.$dir.'Galerie/'.$morpheus["GaleryPath"].'/'.$ordner.'/'.$img.'" title="'.$tit.'" data-toggle="lightbox" data-gallery="Fahrwelt Gallerie">
            <img src="'.$dir.'mthumb.php?w=400&amp;h=400&amp;zc=2&amp;src=Galerie/'.$morpheus["GaleryPath"].'/'.urlencode($ordner).'/'.urlencode($img).'" class="img-fluid" />
		</a></div>';

		$x++;
	}

	return $output;
}

function galerie_liste ($res) {
	global $te, $hl, $de, $cid, $p2, $p3, $p4, $dir, $lightbox, $navID;

	$lightbox = 1;

	while ($row = mysqli_fetch_object($res)) {
		$gnm 	= $row->$hl;
		$te 	= nl2br($row->$te);
		$img 	= $row->img;
		$gnid 	= $row->gnid;

		$output .= '<div class="galListe"><a href="'.$dir.$navID[$cid].eliminiere($gnm).'+'.$gnid.'/"><i class="fa fa-camera-retro"></i> &nbsp; '.$gnm.'</a></div>';
	}
	return $output;
}

# # # # # # #
# # # # # # #

function save_data($datei,$data,$art)  {
	$write = fopen($datei,$art);	   				//oeffne datei zum schreiben der daten
	if ($write!=0) fwrite($write, $data);			//write data
	fclose($write);
}

function read_data($name) {
	# echo $name;
	$data = fopen($name,"r");

	while (!feof($data)) {
		$val .= trim(fgets($data,4096)) ."\n";
	}

	fclose($data);

	return $val;
} // read_infobox

# # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # #

function wochentag($d, $m, $y) {
	$tage 		= array(1=>"Montag", 2=>"Dienstag", 3=>"Mittwoch", 4=>"Donnerstag", 5=>"Freitag", 6=>"Samstag", 7=>"Sonntag");
	$timestamp 	= mktime(0,0,0,$m,$d,$y);
	$w			= date("w",$timestamp);
	return $tage[$w];
}

function tag($d) {
	$tage 		= array(1=>"Montag", 2=>"Dienstag", 3=>"Mittwoch", 4=>"Donnerstag", 5=>"Freitag", 6=>"Samstag", 7=>"Sonntag");
	return $tage[$d];
}

function create_img_liste()	{
	$query 	= "SELECT itext, imgid, `longtext` FROM `morp_cms_image`";
	$result = safe_query($query);
	$x 		= mysqli_num_rows($result);
	$n		= 0;

	while ($row = mysqli_fetch_object($result)) {
		$n++;
		$itext 		= $row->itext;
		$imgid 		= $row->imgid;
		$longtext 	= $row->longtext;
		$arr1 .= $imgid.'=>"'.$itext.'"';
		$arr2 .= $imgid.'=>"'.$longtext.'"';
		if ($n < $x) {
			$arr1 .= ', ';
			$arr2 .= ', ';
		}
	}

	$out = '<?php
$img_arr_t = array('.$arr1.');

$img_arr_lt = array('.$arr2.');

?>
';
	save_data("../nogo/img.php",$out,"w");
}

function protokoll($uid, $db, $id, $art) {
	# echo "$uid, $db, $id, $art";

	$date = date(Y ."-" .m ."-" .d);

	if (!$uid) $uid = 1;
	if ($art != "neu") {
		$que = "select * from `morp_cms_protokoll` WHERE uid=$uid AND db='$db' AND id=$id AND datum='$date' AND art='$art'";
		$res = safe_query($que);
		$x = mysqli_num_rows($res);
	}
	if ($art == "neu" || $x < 1) {
		$que = "insert `morp_cms_protokoll` SET uid=$uid, db='$db', id=$id, datum='$date', art='$art'";
		safe_query($que);
	}
}

function getPdf ($pid) {
	if ($pid) {
		$query = "SELECT pname, pdesc FROM `morp_cms_pdf` where pid=$pid";
		$result = safe_query($query);
		$row = mysqli_fetch_object($result);
		return $row->pname ."##" .$row->pdesc;
	}
}

# # # # bricks einfuegen
# # # # auswertung text startet
function get_cms_text ($text, $lang="de", $dir, $video_page="") 	{

	// echo "$text, $lang, $dir";
	global $navarray, $navarrayFULL, $hn;
	global $color, $h1_2, $h1_1, $rechts;

	# zuerst check, ob template im text
	$tchk = 0;
	if (preg_match("/template/", $text)) $tchk = 1;

	$output = '';

	# # # # template auswertung, falls vorhanden
	if ($tchk) {		# falls template vorhanden, dieses durch template inhalt ersetzen
		$tx = explode("##", $text);

		for($i=0; $i <= count($tx); $i++) {
			$txt 	= $tx[$i];
			if (preg_match("/template/", $txt)) {
				$txt = explode("@@", $txt);
				$tid = $txt[1];
				$query 	= "SELECT * FROM `morp_cms_template` WHERE tid=$tid";
				$result = safe_query($query);
				$row 	= mysqli_fetch_object($result);
				$tx[$i]	= stripslashes($row->content);
			}
		}
		$text = implode("##", $tx);
	}
	# # # # _template

	$tx = explode("##", $text);

	for($i=0; $i < count($tx); $i++) {
		if(array_key_exists($i, $tx)) {
			$txt 	= $tx[$i];
			$txt 	= explode("@@", $txt);
			$text 	= array_key_exists(1, $txt) ? $txt[1] : '';

			// echo "bricks/" .$txt[0] .".php -- ".$txt[1].'-- ##'.$tx[$i].'##';

			if ($txt[0] && !preg_match("/template/",$txt[0])) {
				if(file_exists("bricks/" .$txt[0] .".php")) include("bricks/" .$txt[0] .".php");
			}
			elseif ($txt[0] && preg_match("/template/",$txt[0])) {
				$output .= "<!-- templ" .$txt[1] ." -->";
				$templ_arr[] = $txt[1];
			}
		}
	}
	# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
	# # # # # # # # # # # # # # # # # # # # # # # # # # #
	# # jetzt werden textinterne links noch ausgebessert

#ohne inneres </p>
#ilink<a href="http://xxx" target="_blank">www</a>ilink<p>Harmonie gibt. Mittlerweile sind die östlichen Philosophie

	$raus  	= array("ilink\n<p><strong>", "</strong></p>ilink", "ilink<p><strong>", "</strong></p>\nilink", "ilink<p class=\"lead\">", "ilink<p>", "</p>\nilink", "</p>ilink", "\nilink", "ilink");
	$rein  	= array("", "", "", "", "", "", "", "", "", "", "");
	$output	= str_replace($raus, $rein, stripslashes($output));

	return $output;
}
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # bricks einfuegen
function get_raw_text ($text, $lang="de") 	{
	global $no_str, $nw_str, $search_str, $startseite_l, $startseite_r;

	$tx = explode("##", strip_tags($text));
	$t = '';

	for($i=0; $i < count($tx); $i++) {
		$txt 	= $tx[$i];
		$txt 	= explode("@@", $txt);
		$chk 	= $txt[0];
#		if (isin("fliesstext", $chk)) {
			$txt 	= $txt[1];
			$txt	= repl("umbruch", " ", $txt);
			$txt	= repl("\n", " ", $txt);
			$txt	= repl("\r", " ", $txt);
			$txt	= repl("  ", " ", $txt);
			$t		.= $txt." ";
#		}
	}

	return $t;
}
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # #

function get_raw_text_BR ($tx, $lang="de", $fbimage) 	{
	global $no_str, $nw_str, $search_str, $startseite_l, $startseite_r, $socialImage, $fbAuswahl;

	$tx = explode("##", strip_tags($tx));

	for($i=0; $i <= count($tx); $i++) {
		$txt 	= $tx[$i];
		$txt 	= explode("@@", $txt);
		$chk 	= $txt[0];
		$text 	= $txt[1];
		$socialImage = '';

		if(file_exists("../bricks/" .$txt[0] .".php")) {
			// $t .= "<br>bricks/" .$brick.$txt[0] .".php<br>";
			include("../bricks/" .$txt[0] .".php");
			if($socialImage) $fbAuswahl .= '
				<label style="background:#e2e2e2;"><input type="radio" name="fbimage" id="'.$socialImage.'" value="'.$socialImage.'"'.($fbimage == $socialImage ? ' checked' : '').'> <img for="'.$socialImage.'" src="../mthumb.php?w=200&src=images/userfiles/image/'.$socialImage.'" class="fbImage" '.($fbimage == $socialImage ? ' style="border:solid 4px red;"' : '').' /></label><br/><br/>';
		}


		if (isin("bild", $chk)) { }
		else {
			$txt 	= $txt[1];
			$txt	= repl("umbruch", "<br>", $txt);
			$txt	= repl("\n", "<br>", $txt);
			$txt	= repl("\r", "<br>", $txt);
			$txt	= repl("  ", "<br>", $txt);
			$t		.= $txt."<br>";
		}
	}

	return $t;
}

function get_raw_text_history ($tx, $lang="de") 	{
	$tx = explode("##", strip_tags($tx));

	for($i=0; $i <= count($tx); $i++) {
		$txt 	= $tx[$i];
		$txt 	= explode("@@", $txt);
		$chk 	= $txt[0];
		$text 	= $txt[1];
		$socialImage = '';

		if(file_exists("../bricks/" .$txt[0] .".php")) {
			// $t .= "<br>bricks/" .$brick.$txt[0] .".php<br>";
			include("../bricks/" .$txt[0] .".php");
		}
			$brick = $txt[0];
			$txt 	= $txt[1];
			$txt	= repl("umbruch", "<br>", $txt);
			$txt	= repl("\n", "<br>", $txt);
			$txt	= repl("\r", "", $txt);
			$txt	= repl("  ", "<br>", $txt);
			if(isin("headline", $brick))	$t .= '<h2 itemprop="headline">'.$txt.'</h2>';
			else							$t .= $txt."<br>";

	}

	return $t;
}

# # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # bricks einfuegen
# # # # auswertung text startet

function get_raw_text_morp ($text, $lang="de") 	{
	global $no_str, $nw_str, $search_str, $startseite_l, $startseite_r;

	$tx = explode("##", strip_tags($text));
	# print_r($tx);

	for($i=0; $i <= count($tx); $i++) {
		$txt 	= $tx[$i];
		$txt 	= explode("@@", $txt);
		$text 	= $txt[1];
		$brick 	= $txt[0];
		$brick  = explode("__", $brick);

		# echo "bricks/" .$txt[0] .".php";

		if(file_exists("../bricks/" .$txt[0] .".php") && !preg_match("/page/", $brick[1])) { include("../bricks/" .$txt[0] .".php"); }

		if ($brick[1]) $t .= $morp.' &nbsp; ';
	}

	return $t;
}
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # #


# # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # #


function browser_detection($which_test)
{
	// initialize variables
	$browser_name = '';
	$browser_number = '';
	// get userAgent string
	$browser_user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
	//pack browser array
	// values [0]= user agent identifier, lowercase, [1] = dom browser, [2] = shorthand for browser,
	$a_browser_types[] = array('opera', true, 'op' );
	$a_browser_types[] = array('msie', true, 'ie' );
	$a_browser_types[] = array('konqueror', true, 'konq' );
	$a_browser_types[] = array('safari', true, 'saf' );
	$a_browser_types[] = array('gecko', true, 'moz' );
	$a_browser_types[] = array('mozilla/4', false, 'ns4' );

	for ($i = 0; $i < count($a_browser_types); $i++)
	{
		$s_browser = $a_browser_types[$i][0];
		$b_dom = $a_browser_types[$i][1];
		$browser_name = $a_browser_types[$i][2];
		// if the string identifier is found in the string
		if (stristr($browser_user_agent, $s_browser))
		{
			// we are in this case actually searching for the 'rv' string, not the gecko string
			// this test will fail on Galeon, since it has no rv number. You can change this to
			// searching for 'gecko' if you want, that will return the release date of the browser
			if ( $browser_name == 'moz' )
			{
				$s_browser = 'rv';
			}
			$browser_number = browser_version( $browser_user_agent, $s_browser );
			break;
		}
	}
	// which variable to return
	if ( $which_test == 'browser' )
	{
		return $browser_name;
	}
	elseif ( $which_test == 'number' )
	{
		return $browser_number;
	}

	/* this returns both values, then you only have to call the function once, and get
	 the information from the variable you have put it into when you called the function */
	elseif ( $which_test == 'full' )
	{
		$a_browser_info = array( $browser_name, $browser_number );
		return $a_browser_info;
	}
}

// function returns browser number or gecko rv number
// this function is called by above function, no need to mess with it unless you want to add more features
function browser_version( $browser_user_agent, $search_string )
{
	$string_length = 8;// this is the maximum  length to search for a version number
	//initialize browser number, will return '' if not found
	$browser_number = '';

	// which parameter is calling it determines what is returned
	$start_pos = strpos( $browser_user_agent, $search_string );

	// start the substring slice 1 space after the search string
	$start_pos += strlen( $search_string ) + 1;

	// slice out the largest piece that is numeric, going down to zero, if zero, function returns ''.
	for ( $i = $string_length; $i > 0 ; $i-- )
	{
		// is numeric makes sure that the whole substring is a number
		if ( is_numeric( substr( $browser_user_agent, $start_pos, $i ) ) )
		{
			$browser_number = substr( $browser_user_agent, $start_pos, $i );
			break;
		}
	}
	return $browser_number;
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

function setstrong ($nm) {
	global $sstr, $nstr, $strong_str;

	if (preg_match("/".$strong_str."/", $nm)) $nm = preg_replace($sstr, $nstr, $nm);

	return $nm;
}
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

function write_session($sid, $page, $ip)
{
	$date = date(Y ."-" .m ."-" .d);
	$time = date(H.":".i);

	dbconnect();
	$query = "select * FROM `morp_cms_path` WHERE sid='$sid' AND datum='$date'";
	$result = safe_query($query);
	$x = mysqli_num_rows($result);

	if ($x > 0) {
		$row   = mysqli_fetch_array($result);
		$path  = $row["path"];
		$id    = $row["id"];
		$path .= "|$page";
		$query = "update path set path='$path', ende='$time' where id=$id";
	}
	else $query = "insert path set ip='$ip', path='$page', sid='$sid', datum='$date', start='$time'";
	$result = safe_query($query);
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

function get_text($textid) {
	global $language;
	#return "[".$language[$textid]."]";
	return utf8_encode($language[$textid]);
} // get_text()

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# images für up and down
function down() {
	return "<img src=\"images/down.gif\" width=9 height=9 alt=\"eine Position nach unten\" border=0>";
}

function up() {
	return "<img src=\"images/up.gif\" width=9 height=9 alt=\"eine Position nach oben\" border=0>";
}

function ilink($text='') {
	return "<img src=\"images/link.gif\" width=5 height=10 alt=\"$text\" border=0>";
}

function backlink($text='') {
	return '<i class="fa fa-arrow-circle-left"></i> ';
}

function table($cp=0, $cs=0, $w='', $b=0) {
	return "<table width=$w border=$b cellspacing=$cs cellpadding=$cp>";
}

function select($name, $style) {
	return "\n\n<select name=$name style=\"$style\">\n";
}

function option($value, $text) {
	return "<option value=\"$value\">$text</option>\n";
}

// für darstellung europäische zahldarstellung mit komma
function ger_p($val)
{
	$endung = explode(".", $val);
	$val = strtr($val,".",",");

	if(count($endung)>1) return $val;
	else return $val.',-';
}

function ger_trenn($val)
{
	$val = strtr($val,".",",");
	return $val;
}

// für berechnung benötigt man amerikanische zahldarstellung mit punkt
function us_p($val)
{
	$val = strtr($val,",",".");
	return $val;
}

// damit die zahlen immer mit 2 nachkommastellen geschrieben werden
function check_nachkomma($val)
{
	$nk = sprintf ('%.2f', $val);
	return $nk;
}

function euro_dat($dat) {
	$dat = explode("-", $dat);
	$dat = $dat[2] ."." .$dat[1] ."." .$dat[0];
	return $dat;
}

function ts2dat($dat) {
	$dat = date("d.m.Y",strtotime($dat));
	return $dat;
}

function us_dat($dat) {
	$dat = explode(".", $dat);

	if(strlen($dat[2]) == 2) $jahr = "20".$dat[2];
	elseif(strlen($dat[2]) < 2) $jahr = date("Y");
	else $jahr = $dat[2];

	if(strlen($dat[1]) == 1) $monat = "0".$dat[1];
	else $monat = $dat[1];

	if(strlen($dat[0]) == 1) $tag = "0".$dat[0];
	else $tag = $dat[0];


	$dat = $jahr ."-" .$monat ."-" .$tag;
	return $dat;
}

# # # # # # # NEWSLETTER # # # # # # # # #
# # # # # # # NEWSLETTER # # # # # # # # #
# # # # # # # NEWSLETTER # # # # # # # # #


function create_html_dok($text, $dir="", $art="html") {
	global $newsletter_show, $nl_image, $morpheus, $nav_s;

	if (preg_match("/template/", $text)) $tchk = 1;

	# # # # template auswertung, falls vorhanden
	if ($tchk) {		# falls template vorhanden, dieses durch template inhalt ersetzen
		$tx = explode("##", $text);

		for($i=0; $i <= count($tx); $i++) {
			$txt 	= $tx[$i];
			if (preg_match("/template/", $txt)) {
				$txt = explode("@@", $txt);
				$tid = $txt[1];
				$query 	= "SELECT * FROM `morp_cms_template` WHERE tid=$tid";
				$result = safe_query($query);
				$row 	= mysqli_fetch_object($result);
				$tx[$i]	= $row->c_de;
			}
		}
		$text = implode("##", $tx);
	}

	if (!$dir) 	include ("design.php");
	else 		include ("newsletter/design.php");

	$tx = explode("##", $text);
	$newsletter_show = 1;

	for($i=0; $i <= count($tx); $i++) {
		$txt 	= $tx[$i];
		$txt 	= explode("@@", $txt);
		$text 	= $txt[1];

		# # # # # # # # # # # # #
		# der NUR text-bereich
		if (preg_match("/umbruch/", $txt[0]) || preg_match("/^umbruch/", $txt[1])) {}
		elseif (!preg_match("/bild/", $txt[0]) && !preg_match("/news/", $txt[0]) && !preg_match("/link/", $txt[0]) && !preg_match("/headline/", $txt[0]))
				$raw   .= $text;
		elseif (preg_match("/headline/", $txt[0])) $raw .= "\n\r\n\r*".$text."*\n";
		elseif (preg_match("/news/", $txt[0])) {}
		elseif (preg_match("/link/", $txt[0]) && !preg_match("/image/", $txt[0]) && !preg_match("/bild/", $txt[0])) {
				$tmp	= explode("|", $txt[1]);
				$raw   .= ' <'.$tmp[0].'>
				';
		}
		# # # # # # # # # # # # # _text
		# # # # # # # # # # # # #

		# # # # # # # # # # # # #
		# der HTML Bereich
		if (!$dir) 	{
			if(file_exists("../../bricks/" .$txt[0] .".php")) include("../../bricks/" .$txt[0] .".php");
		}
		else {
			if(file_exists("../bricks/" .$txt[0] .".php")) include("../bricks/" .$txt[0] .".php");
		}
		# # # # # # # # # # # # #
		# # # # # # # # # # # # #

		if (preg_match("/news/", $txt[0]))	{
			$news = repl("<a href=\"", "", $news);
			$news = repl("\" class=\"intern\">Zum Artikel", "", $news);
			$raw   .= strip_tags($news);
		}
	}
	# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
	# # # # # # # # # # # # # # # # # # # # # # # # # # #
	# # jetzt werden textinterne links noch ausgebessert

	# # jetzt werden textinterne links noch ausgebessert
	if ($art == "html") 	{
		$raus  = array("/<\/p>\n\nilink/", "/ilink<p style=\"margin: 16px 0px 0px 0px;\">/", "/ilink<p>/", "/ilink/", "/<a name=\"[0-9][0-9]\"><\/a>/", "/ \./");
		$rein  = array(" ", " ", " ", "", "", ".");
		$suche = repl("/", "", implode("|", $raus));

		if (preg_match("/ilink/", $output)) $output = preg_replace($raus, $rein, $output);

		# $raw   = strip_tags($output);
		# # # # # # # # # # # # # # # # # # # # # # # # # # #

		$arr 	   = explode("<!-- inhalt -->", $html);

		$html_dok  = $arr[0];
		$html_dok .= $output;
		$html_dok .= $arr[1];
		// _gefuellt

		return $html_dok;
	}
	else	{
		$raw = repl("\n\n", "\n", $raw);
		return $raw;
	}
}

function konvert_image ($file, $name, $mx, $savedir) {
	// echo $name;
	$typ = explode(".", $name);
	$typ = $typ[(count($typ)-1)];

	if ($typ == "jpg") 		$type = 11;
	elseif ($typ == "png")  $type = 10;
	else die("upload von unbekannten datenformat");
	# _type

	$src 	= ImageCreateFromJPEG($file);
	$w 		= ImageSx($src);
	$h 		= ImageSy($src);

	// echo "<li>w: ".$w."<li>h: ".$h."<li>new: ".$mx."<li>pfad: ".$savedir."<li>";

	# das konvertierte `morp_cms_image` ist quadratisch qualitaet 80%
	if ($mx) {
		if ($w > $h) {
			if ($w > $mx) $wert = $mx/$w;
			else $wert = $w/$mx;
		}
		else {
			if ($h > $mx) $wert = $mx/$h;
			else $wert = 1;
		}

		if ($wert > 1) {
			$x = $w/$wert;
			$y = $h/$wert;
		}
		else {
			$x = $w*$wert;
			$y = $h*$wert;
		}

		$posx = ($mx - $x)/2;
		$posy = ($mx - $y)/2;

		$dst 	= ImageCreateTrueColor($mx,$mx);
		$white 	= ImageColorAllocate($dst, 255, 255, 255);
		imagefill($dst, 0, 0, $white);
		ImageCopyResampled($dst, $src, $posx, $posy, 0, 0, $x, $y, $w, $h);
		@imageJPEG($dst, $savedir.$name, 80);
	}
}

function konvert_img ($file, $name, $mx, $savedir) {
	// echo $name;
	$typ = explode(".", $name);
	$typ = $typ[(count($typ)-1)];

	if ($typ == "jpg") 		$type = 11;
	elseif ($typ == "png")  $type = 10;
	else die("upload von unbekannten datenformat");
	# _type

	$src 	= ImageCreateFromJPEG($file);
	$w 		= ImageSx($src);
	$h 		= ImageSy($src);

	if ($mx) {
		$wert = $mx/$h;

		$x = $w*$wert;
		$y = $h*$wert;

#		$posx = ($mx - $x)/2;
#		$posy = ($mx - $y)/2;

		$dst 	= ImageCreateTrueColor($x,$y);
		$white 	= ImageColorAllocate($dst, 255, 255, 255);
		imagefill($dst, 0, 0, $white);
		ImageCopyResampled($dst, $src, $posx, $posy, 0, 0, $x, $y, $w, $h);
		@imageJPEG($dst, $savedir.$name, 80);
	}
}


function set_link() {
	global $navarray;

	$cid = $_GET["cid"];
	$p2  = $_GET["p2"];
	$p3  = $_GET["p3"];
	$p4  = $_GET["p4"];

	$setlink  .= $cid;
	if ($p2) {
		$setlink  .= "-".$p2;
		echo $thnav	= $navarray[$p2];
	}
	if ($p3) {
		$setlink 	.= "-".$p3;
		$thnav  .= "/".$navarray[$p3];
	}
	if ($p4) {
		$setlink 	.= "-".$p4;
		$thnav  .= "/".$navarray[$p4];
	}

	return $nlink = $thnav."/".$setlink;
}


function pull_down ($id, $db, $sp_name, $sp_id) {
	$query 	= "SELECT * FROM $db ORDER BY $sp_name";
	$result = safe_query($query);
	$pd = "<option value=\"\">Bitte w&auml;hlen</option>\n";

	while ($row = mysqli_fetch_object($result)) {
		if ($row->$sp_id == $id) $sel = "selected";
		else $sel = "";

		$nm  = $row->$sp_name;
		$pd .= "<option value=\"" .$row->$sp_id ."\" $sel>$nm</option>\n";
	}
	return $pd;
}

function get_img($imgid) {
	$que  	= "SELECT itext, imgname FROM `morp_cms_image` WHERE imgid=$imgid";
	$res 	= safe_query($que);
	$rw     = mysqli_fetch_object($res);
	$inm 	= $rw->imgname;
	return $inm;
}

function getPartner ($pid) {
	$sql 	= "SELECT * FROM morp_partner WHERE pid=$pid";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);
	return $row->name.'#'.$row->web.'#'.$row->ort;
}

function getArt ($aid) {
	$sql 	= "SELECT * FROM morp_art WHERE aid=$aid";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);
	return $row->art;
}

function getKat ($id) {
	$sql 	= "SELECT * FROM `morp_news_kategorie` WHERE mnID=$id";
	$res = safe_query($sql);
	$row = mysqli_fetch_object($res);
	return $row->mnName;
}

function mobile_device_detect($iphone=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false){

  $mobile_browser   = false; // set mobile browser as false till we can prove otherwise
  $user_agent       = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed
  $accept           = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed

  switch(true){ // using a switch against the following statements which could return true is more efficient than the previous method of using if statements

    case (isin('ipod',$user_agent)||isin('iphone',$user_agent)); // we find the words iphone or ipod in the user agent
      $mobile_browser = $iphone; // mobile browser is either true or false depending on the setting of iphone when calling the function
      $status = 'Apple';
    break; // break out and skip the rest if we've had a match on the iphone or ipod

    case (isin('iPad',$user_agent)); // we find the words iphone or ipod in the user agent
      $mobile_browser = 3; // mobile browser is either true or false depending on the setting of iphone when calling the function
      $status = 'Apple';
    break; // break out and skip the rest if we've had a match on the iphone or ipod

//    case (isin('android',$user_agent) && isin('Mobile',$user_agent));  // we find android in the user agent
    case (isin('android',$user_agent) );  // we find android in the user agent
      $mobile_browser = $android; // mobile browser is either true or false depending on the setting of android when calling the function
      $status = 'Android';
    break; // break out and skip the rest if we've had a match on android

    case (isin('opera mini',$user_agent)); // we find opera mini in the user agent
      $mobile_browser = $opera; // mobile browser is either true or false depending on the setting of opera when calling the function
      $status = 'Opera';
    break; // break out and skip the rest if we've had a match on opera

    case (isin('blackberry',$user_agent)); // we find blackberry in the user agent
      $mobile_browser = $blackberry; // mobile browser is either true or false depending on the setting of blackberry when calling the function
      $status = 'Blackberry';
    break; // break out and skip the rest if we've had a match on blackberry

    case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|fennec|plucker|xiino|blazer|elaine)/i',$user_agent)); // we find palm os in the user agent - the i at the end makes it case insensitive
      $mobile_browser = $palm; // mobile browser is either true or false depending on the setting of palm when calling the function
      $status = 'Palm';
    break; // break out and skip the rest if we've had a match on palm os

    case (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent)); // we find windows mobile in the user agent - the i at the end makes it case insensitive
      $mobile_browser = $windows; // mobile browser is either true or false depending on the setting of windows when calling the function
      $status = 'Windows Smartphone';
    break; // break out and skip the rest if we've had a match on windows

    default;
      $mobile_browser = false; // set mobile browser to false
      $status = 'Desktop / full capability browser';
    break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it

  } // ends the switch

  // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
//  header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
//  header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies

  // if redirect (either the value of the mobile or desktop redirect depending on the value of $mobile_browser) is true redirect else we return the status of $mobile_browser

    return $mobile_browser; // will return either true or false


} // ends function mobile_device_detect




function nextID($id) {
	$sql = "SELECT ebene, parent, `sort` FROM `morp_cms_nav` WHERE navid=$id";
	$res = safe_query($sql);
	$ct = mysqli_num_rows($res);
	if($ct>0) {
		$row = mysqli_fetch_object($res);
		$e = $row->ebene;
		$p = $row->parent;
		$s = $row->sort;

		if($e == 1) {
			$sql = "SELECT navid FROM `morp_cms_nav` WHERE parent=$id AND `sort`=1 AND sichtbar=1";
			$res = safe_query($sql);
			$ct = mysqli_num_rows($res);
			if($ct>0) {
				$row = mysqli_fetch_object($res);
				$NEXT = $row->navid;
			}
			else {
				$s++;

				$NEXT = getEbeneID($s);
			}
		}
		elseif($e == 2) {
			$s++;
			$sql = "SELECT navid FROM `morp_cms_nav` WHERE parent=$id AND sichtbar=1 ORDER BY `sort` LIMIT 0,1";
			$res = safe_query($sql);
			$ct = mysqli_num_rows($res);
			if($ct>0) {
				$row = mysqli_fetch_object($res);
				$NEXT = $row->navid;
			}
			else {
				$sql = "SELECT navid FROM `morp_cms_nav` WHERE parent=$p AND `sort`=$s AND sichtbar=1";
				$res = safe_query($sql);
				$ct = mysqli_num_rows($res);
				if($ct>0) {
					$row = mysqli_fetch_object($res);
					$NEXT = $row->navid;
				}
				else {
					$sql = "SELECT ebene, parent, `sort` FROM `morp_cms_nav` WHERE navid=$p AND sichtbar=1";
					$res = safe_query($sql);
					$ct = mysqli_num_rows($res);
					if($ct) {
						$row = mysqli_fetch_object($res);
						$s = $row->sort;
						$s++;

						$NEXT = getEbeneID($s);
					}
				}
			}
		}
		elseif($e == 3) {
			$s++;
			$sql = "SELECT navid FROM `morp_cms_nav` WHERE parent=$p AND `sort`=$s AND sichtbar=1";
			$res = safe_query($sql);
			$ct = mysqli_num_rows($res);
			if($ct>0) {
				$row = mysqli_fetch_object($res);
				$NEXT = $row->navid;
			}
			else {
				$sql = "SELECT ebene, parent, `sort` FROM `morp_cms_nav` WHERE navid=$p";
				$res = safe_query($sql);
				$row = mysqli_fetch_object($res);
				$s = $row->sort;
				$p = $row->parent;

				$sql = "SELECT navid FROM `morp_cms_nav` WHERE parent=$p AND sichtbar=1 AND `sort`> $s LIMIT 0,1";
				$res = safe_query($sql);
				$ct = mysqli_num_rows($res);
				if($ct) {
					$row = mysqli_fetch_object($res);
					$NEXT = $row->navid;
				}
			}
		}
	}
	return $NEXT;
}


function getEbeneID($s) {
	$sql = "SELECT navid, nocontent FROM `morp_cms_nav` WHERE ebene=1 AND `sort`=$s AND sichtbar=1";
	$res = safe_query($sql);
	$ct = mysqli_num_rows($res);
	if($ct>0) {
		$row = mysqli_fetch_object($res);
		$nocontent = $row->nocontent;
		if($nocontent) {
			$navid = $row->navid;
			$sql = "SELECT navid FROM `morp_cms_nav` WHERE ebene=2 AND parent=$navid AND sichtbar=1 ORDER BY `sort` LIMIT 0,1";
			$res = safe_query($sql);
			$ct = mysqli_num_rows($res);
			if($ct>0) {
				$row = mysqli_fetch_object($res);
				$NEXT = $row->navid;
			}
		}
		else $NEXT = $row->navid;
	}
	return $NEXT;
}

function getNav($lang, $par) {
	// $que = "SELECT * FROM `morp_cms_nav` WHERE (sichtbar=1 AND `lock` < 1 AND lang=$lang AND parent = $par) ORDER BY `sort` ASC";
	$que = "SELECT * FROM `morp_cms_nav` WHERE (sichtbar=1 AND lang=$lang AND parent = $par) ORDER BY `sort` ASC";
	return $res = safe_query($que);
}


function readCompleteNavOrdered($lang, $art=2) {
	global $morpheus;

	$homeID = $morpheus["lan_arr"][$lang];
	$homeID = $morpheus["home_ID"][$homeID];
	// hauptnav, ohne meta menu, ohne footer menu
	global $morpheus;
	$que  	= "SELECT * FROM `morp_cms_nav` WHERE (sichtbar=1 AND `lock` < 1 AND lang=$lang AND bereich < 2 AND ebene=1) OR navid=$homeID ORDER BY `sort` ASC";
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
		$res_2 = getNav($lang, $row->navid);
		while ($row2 = mysqli_fetch_object($res_2)) {
			$arr_H[] = $row2->navid;

			//start ebene 3
			$res_3 = getNav($lang, $row2->navid);
			while ($row3 = mysqli_fetch_object($res_3)) {
				$arr_H[] = $row3->navid;

			}
		}
	}

	$data = '<?php
$orderedList = array(';

	$navAll = array();
	$x=0;
	foreach($arr_H as $key=>$val) {
		$sql = "SELECT nocontent FROM `morp_cms_nav` WHERE navid=$val";
		$res = safe_query($sql);
		$rw = mysqli_fetch_object($res);
		if($rw->nocontent < 1) {
			$x++;
			$navAll[]=$val;
			$data .= ''.$x.'=>"'.$val.'", ';
		}
	}

	$data .= '); ?>';

	if($art==1) return $navAll;
	else return $data;
}


function mailBody() {

}


function mail2col($left, $right) {
	return '								<table class="row" width="552" cellpadding="0" cellspacing="0" align="center">
                                    <tbody><tr>
                                        <td class="wrapper last" valign="top">
                                            <table class="six columns" cellpadding="0" cellspacing="0" dir="ltr">
                                                <tbody><tr>
                                                    <td class="panel" valign="top">
                                                        '.$left.'<!--[if gte mso 9]><br><br><![endif]-->
                                                    </td>
                                                    <td class="expander"></td>
                                                </tr>
                                            </tbody></table>
                                        </td><!--[if !mso]><!== -->
                                        <td class="wrapper last" valign="top">
<!--<![endif]--><!--[if gte mso 9]>
                                        <td class="last" valign="top">
<![endif]-->
                                            <table class="six columns" cellpadding="0" cellspacing="0" align="left">
                                                <tbody><tr>
                                                    <td class="panel" valign="top" dir="ltr">
                                                        '.$right.'
                                                    </td>

                                                    <td class="expander"></td>
                                                </tr>
                                            </tbody></table>
                                        </td>
                                    </tr>
                                </tbody></table>
';
}

function mail1col($text) {
	return '                                <table class="row" width="552" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td class="wrapper last">
                                            <table class="twelve columns" cellpadding="0" cellspacing="0" dir="ltr">
                                                <tr>
                                                    <td>
														'.$text.'
													</td>

                                                    <td class="expander"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table><br class="gmailbr"><!-- *  *  *  *  *  *  *  *  *  -->
';
}

function mailH1($text) {
	return '	                                                    <h1 style="font-family: Arial,Helvetica,sans-serif; font-size: 16px; line-height: 20px; color: #404A59; font-weight: normal; letter-spacing: 1px; text-transform: uppercase">'.$text.'</h1>
';
}

function mailP($text) {
	return '														<p style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 16px; color: #404A59; margin-right:20px;">'.$text.'</p>
';
}

function br($anzahl=1) {
	$br = '';
	for($i=1; $i<=$anzahl; $i++) { $br .= '<br/>'; }
	return $br;
}
?>