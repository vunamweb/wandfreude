<?php
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
# start 11/2007                                     #
#                                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # #

global $morpheus;

$morpheus["layout"]			= array("","Dreispaltig");

/* DESIGNS BENENNUNG */
$morpheus["design"]			= array(
									1=>"Standard",
);

/* TEMPLATES BENENNUNG */
$morpheus["template"]		= array(

									1=>"Content<br>",
#									2=>"Container, farbiger Hintergrund",
									2 => "Mehr Spalten Container<br>",

									6=> "1 Spalte Bild / 1 Spalte Text",
#									5=>"Header Bild<br>",

									3=>"Vorher / Nachher Slider",
#									7=> "Header Bild",
#									5=> "Text plus Galerie<br>",
									4=> "Accordion<br>",
#									12=>"Vcard / Team<br>",
#									10=> "News Seite",
#									8=> "Zitat<br>",
#									9=> "Footer",
#									15=>"Zeitstrahl",
#									13=> "Circle / Kreis Animation",
#									14=> "rote Bubbles Leistungen<br>",
#									10=> "Leer<br>",
#									5=> "Naviagtion",

);

$morpheus["navpos"]			= array(
									1=>"Hauptnavigation",
#									2=>"Meta-Navigation",
#									3=>"Footer-Naviagtion"
									);

#$morpheus["news_formate"]	= array(1=>"Abstrakt und Detail", 4=>"Presse", );

# einstellungen, welche elemente in der seitenverwaltung aktiv sein sollen
$morpheus["foto"]			= 0;
$morpheus["standard_tid"]	= array(1);
$morpheus["standard_des"]	= 1;
#$morpheus["news_formate"]	= array(1=>"News", 3=>"Interview", 4=>"Settings");
$morpheus["news_formate"]	= array(1=>"News");

$morpheus["templ_conf1"]		= array(1=>"Container Full", 2=>"Container zentrierter Text" );
$morpheus["templ_conf2"]		= array(1=>"1/3 Spalten", 2=>"1/4 Spalten", 3=>"1/2 Spalten", 4=>"2/3 Spalten", );
//$morpheus["templ_conf3"]		= array(1=>"Logo // w=200 h=200", 2=>"Screens w=500");
$morpheus["templ_conf6"]		= array(1=>"1/3 Bild / 2/3 Text", 2=>"2/3 Text / 1/3 Bild", 3=>"1/2 Bild / 1/2 Text", 4=>"1/2 Text / 1/2 Bild", );
// $morpheus["templ_conf7"]		= array(1=>"Header Slider", 2=>"Hintergrund Image ohne Slider");


$morpheus["template_ende"]	= array(1,2,6,10,12);
$morpheus["template_abstand"]	= array(1,2,7,6);


$auths_arr = array(
	10=>"Seitenverwaltung, Bildarchiv, Text Vorlagen, Formulare",
	14=>"News",
	21=>"Mitarbeiter",
	30=>"Dokumente Downloads",
	99=>"Settings",
);

?>