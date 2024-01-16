ALTER TABLE  `morp_cms_content` ADD  `navid` INT( 11 ) NOT NULL AFTER  `cid`;
ALTER TABLE  `morp_cms_content` ADD    `vid` int(11) NOT NULL;
ALTER TABLE  `morp_cms_content` ADD   `vorlage` int(1) NOT NULL;
ALTER TABLE  `morp_cms_content` ADD   `vorl_name` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL;
ALTER TABLE  `morp_cms_content` ADD   `pos` int(11) NOT NULL default '1';
ALTER TABLE  `morp_cms_content` ADD   `img0` int(11) default NULL;
ALTER TABLE  `morp_cms_content` ADD   `img5` int(11) NOT NULL default '0';
ALTER TABLE  `morp_cms_content` ADD    `img6` int(11) NOT NULL default '0';
ALTER TABLE  `morp_cms_content` ADD   `tid` int(11) NOT NULL default '1';
ALTER TABLE  `morp_cms_content` ADD   `ton` int(1) NOT NULL default '1';
ALTER TABLE  `morp_cms_content` ADD   `tpos` int(11) NOT NULL;
ALTER TABLE  `morp_cms_content` ADD   `tlink` varchar(100) character set utf8 collate utf8_unicode_ci default NULL;
ALTER TABLE  `morp_cms_content` ADD   `tbackground` varchar(100) character set utf8 collate utf8_unicode_ci default NULL;
ALTER TABLE  `morp_cms_content` ADD   `timage` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL;
ALTER TABLE  `morp_cms_content` ADD   `theadl` varchar(80) character set utf8 collate utf8_unicode_ci NOT NULL;
ALTER TABLE  `morp_cms_content` ADD   `theight` int(11) NOT NULL;
ALTER TABLE  `morp_cms_content` ADD   `twidth` int(11) NOT NULL;
ALTER TABLE  `morp_cms_content` ADD    `tcolor` varchar(6) character set utf8 collate utf8_unicode_ci NOT NULL;
ALTER TABLE  `morp_cms_content` ADD    `tref` int(11) NOT NULL;

ALTER TABLE  `delete` ADD  `dat` TIMESTAMP NOT NULL;

ALTER TABLE  `morp_cms_form` ADD  `extended` INT( 1 ) NOT NULL;

ALTER TABLE  `morp_cms_img_group` ADD  `art` INT( 1 ) NOT NULL DEFAULT  '1';


ALTER TABLE  `morp_cms_nav` ADD  `emotional` INT( 11 ) NOT NULL ,
ADD  `design` INT( 11 ) NOT NULL ,
ADD  `oldlnk` VARCHAR( 255 ) NOT NULL;


ALTER TABLE  `morp_cms_news` ADD  `hid` INT( 11 ) NOT NULL ,
ADD  `sichtbar` INT( 1 ) NOT NULL DEFAULT  '1';

ALTER TABLE  `morp_cms_pdf` ADD  `pimage` VARCHAR( 150 ) NOT NULL;

ALTER TABLE  `pdf_group` ADD  `parent` INT( 11 ) NOT NULL AFTER  `pgname`;


/*
$sql = "SELECT * FROM  `morp_cms_content` WHERE 1";
$res = safe_query($sql);
while ($row = mysqli_fetch_object($res)) {
	$id = $row->cid;
	$sql = "UPDATE `morp_cms_content` set navid=$id WHERE cid=$id";
	safe_query($sql);
}
*/

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `morp_cms_form_auswertung`
--

CREATE TABLE IF NOT EXISTS `morp_cms_form_auswertung` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `vorname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `wahrnehmung` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `arbeiter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `beruf` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `alter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `newsletter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `wahrnehmungX` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `wohnort` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `newsletter_topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `malsehen` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `come` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ggggg` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `morp_cms_news` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `anrede` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Nachname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `strasse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plz` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ort` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firma` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `strasse_firma` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plz_firma` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ort_firma` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fon_firma` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `teilnahme1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `teilnahme2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `morp_cms_form_field`
--

CREATE TABLE IF NOT EXISTS `morp_cms_form_field` (
  `ffid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `art` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `feld` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hilfe` text COLLATE utf8_unicode_ci NOT NULL,
  `spalte` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `reihenfolge` int(11) NOT NULL,
  `pflicht` int(1) NOT NULL,
  `email` int(1) NOT NULL,
  `auswahl` text COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `parent` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `fehler` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `klasse` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cont` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fmin` int(11) NOT NULL,
  `fmax` int(11) NOT NULL,
  PRIMARY KEY (`ffid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;