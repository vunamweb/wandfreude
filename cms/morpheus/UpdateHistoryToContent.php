<?php
session_start();

global $mylink;



 echo "here";

include("../nogo/config.php");
include("../nogo/config_morpheus.inc");
include("../nogo/db.php");
dbconnect();
include("login.php");
include("../nogo/funktion.inc");

$get = $_POST["get"];

$sql  = "SELECT content, cid FROM `morp_cms_content_history` WHERE id=$get";
$res = safe_query($sql);
$row = mysqli_fetch_object($res);

$toID = $row->cid;
$cont = $row->content;


echo $sql  = "UPDATE `morp_cms_content` SET content='$cont' WHERE cid=$toID";
safe_query($sql);

?>