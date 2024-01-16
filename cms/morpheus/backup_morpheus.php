<?php
session_start();
# # # # # # # # # # # # # # # # # # # # # # # # # # #
# www.pixel-dusche.de                               #
# björn t. knetter                                  #
# start 12/2003                                     #
#                                                   #
# post@pixel-dusche.de                              #
# frankfurt am main, germany                        #
# # # # # # # # # # # # # # # # # # # # # # # # # # #

include("cms_include.inc");

$f = fopen($morpheus["dfile"], "w");
// $res 		= mysql_list_tables($morpheus["dbname"]);
$res = "SHOW TABLES FROM ".$morpheus["dbname"];
$res = safe_query($res);

$ausschluss = array(
"morp_newsletter",
"morp_newsletter_cont",
"morp_newsletter_track",
"morp_newsletter_versand",
"morp_newsletter_vt",
"morp_newsletter_vt_csv",
"morp_newsletter_vt_test",
"morp_newsletter_vt_test-nurich",
"morp_register",
);

while ($row = mysqli_fetch_row($res)) {
	#print_r($row);
    if(in_array($row[0], $ausschluss)) {}
    else $tables[] = $row[0];
}

# print_r($tables);

$n = 0;

foreach ($tables as $table) {
	# echo $table."<br>";
	fwrite($f,"DROP TABLE `$table`;\n");
	$sql = "SHOW CREATE TABLE `$table`";
	$res = safe_query($sql);

	if ($res) {
		$create = mysqli_fetch_array($res);
		$create[1] .= ";";
		$line = str_replace("\n", "", $create[1]);

		fwrite($f, $line."\n");
		$que 	= "SELECT * FROM `$table`";
		$result = safe_query($que);
		$num 	= mysqli_num_fields($result);

		while ($row = mysqli_fetch_array($result)){
			$n++;
		    $line = "INSERT INTO `$table` VALUES(";
		    for ($i=1;$i<=$num;$i++) {
		     	$line .= "'".mysqli_real_escape_string(stripslashes($row[$i-1]))."', ";
		    }
	    	$line = substr($line,0,-2);
	    	fwrite($f, $line.");\n");
	 	}
	}
}
fclose($f);

$link = "download.php?dfile=morpheus_db.sql";

#echo "<script language='javascript'>
#	parent.location = '$link';
#</script>";

echo '
<div id=content_big class=text>
';

echo "<p><strong>$n SQL Befehle geschrieben</strong></p>";

echo '<p>Bitte die Datei runterladen und archivieren.</p>
<p><a href="download.php?dfile=morpheus_db.sql"><strong>&raquo; Klicken Sie bitte hier</strong></a></p>
';
?>

</div>

</body>
</html>
