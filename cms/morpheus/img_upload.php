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

$box=1;
$myauth = 10;
include("cms_include.inc");

?>

<link rel="stylesheet" type="text/css" href="uploadifive/uploadifive.css">
<script src="uploadifive/jquery.min.js" type="text/javascript"></script>
<script src="uploadifive/jquery.uploadifive.min.js" type="text/javascript"></script>

<style type="text/css">
body {
	font: 13px Arial, Helvetica, Sans-serif;
}
.uploadifive-button {
	float: left;
	margin-right: 10px;
}
#queue {
	border: 1px solid #E5E5E5;
	height: 377px;
	overflow: auto;
	margin-bottom: 10px;
	padding: 0 3px 3px;
	width: 500px;
}
</style>

<?php

echo "<div id=content_big class=text>";

# wenn bild in content eingesetzt wird
$stelle = isset($_REQUEST["stelle"])? $_REQUEST["stelle"] : '';
$imglnk = isset($_REQUEST["imglnk"])? $_REQUEST["imglnk"] : '';
$navid  = isset($_REQUEST["navid"]) ? $_REQUEST["navid"] : '';
$edit   = isset($_REQUEST["edit"]) 	? $_REQUEST["edit"] : '';
$cedit  = isset($_REQUEST["cedit"]) ? $_REQUEST["cedit"] : '';
$db		= isset($_REQUEST["db"]) 	? $_REQUEST["db"] : '';
$art	= isset($_REQUEST["art"]) 	? $_REQUEST["art"] : '';
$vorlage= isset($_REQUEST["vorlage"])? $_REQUEST["vorlage"] : '';


/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Set the uplaod directory
$gid 	 = isset($_REQUEST["gid"]) ? $_REQUEST["gid"] : 1;
#$neu 	 = $_REQUEST["neu"];
#$reload	 = $_REQUEST["reload"];

?>

	<p>&nbsp;</p>
<?php if($navid) { ?>
	<p><a href="image_liste.php?stelle=<?php echo $stelle; ?>&navid=<?php echo $navid; ?>&edit=<?php echo $edit; ?>&cedit=<?php echo $cedit; ?>&vorlage=<?php echo $vorlage; ?>&gid=<?php echo $gid; ?>&back=<?php echo $back; ?>&db=<?php echo $db; ?>&imglnk=<?php echo $imglnk; ?>&art=<?php echo $art; ?>"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i> Fertig und Reload Seite</a></p>
<?php } else { ?>
	<p><a href="image_liste.php?gid=<?php echo $gid; ?>"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i> Fertig und Reload Seite</a></p>
<?php } ?>

	<p>&nbsp;</p>
	<form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file" multiple="true">
		<a style="position: relative; top: 8px; border:solid 1px #4595ce; color:#4595ce; font-weight:bold; height:27px; display:block; float:left; margin-top:-6px; padding:0 8px; text-transform:uppercase; line-height:28px; background:#f1f1f1;" href="javascript:$('#file_upload').uploadifive('upload')" class="upload">Upload Files</a>
	</form>

	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadifive({
				'auto'             : true,
				'checkScript'      : 'uploadifive/check-exists_img.php',
				'formData'         : {
									   'timestamp' : '<?php echo $timestamp;?>',
									   'token'     : '<?php echo md5('pixeld' . $timestamp);?>',
									   'gid'	   : '<?php echo $gid; ?>'
				                     },
				'queueID'          : 'queue',
				'uploadScript'     : 'uploadifive/uploadifive_img.php',
				'onUploadComplete' : function(file, data) { console.log(data); }
			});
		});
	</script>

