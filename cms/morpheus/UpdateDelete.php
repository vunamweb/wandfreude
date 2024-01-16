<?php
session_start();

global $mylink;

// echo "here";

include("../nogo/config.php");
include("../nogo/config_morpheus.inc");
include("../nogo/db.php");
dbconnect();
include("login.php");
include("../nogo/funktion.inc");

$todel = $_POST["todel"];
$table = $_POST["table"];
$tid = $_POST["tid"];

$sql  = "DELETE FROM $table WHERE $tid=$todel";

safe_query($sql);


?>