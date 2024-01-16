<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-gaenhpro" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a> <?php echo $text_extdoc; ?> </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> </h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-gaenhpro" class="form-horizontal">
          <ul class="nav nav-tabs" id="storetab">
            <?php foreach ($stores as $store) { ?>
            <li><a href="#tab-<?php echo $store['store_id'];?>" data-toggle="tab"><?php echo $store['name']; ?></a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <?php foreach ($stores as $store) { ?>
            <div class="tab-pane" id="tab-<?php echo $store['store_id'];?>">
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <input type="radio" name="status[<?php echo $store['store_id'];?>]" value="1" <?php if ($status[$store['store_id']] == 1) { ?>checked="checked"<?php } ?> />
                    <?php echo $text_enabled; ?></label>
                  <label class="radio-inline">
                    <input type="radio" name="status[<?php echo $store['store_id'];?>]" value="0" <?php if ($status[$store['store_id']] == 0) { ?>checked="checked"<?php } ?> />
                    <?php echo $text_disabled; ?></label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_gaid; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="gaid[<?php echo $store['store_id'];?>]" value="<?php echo $gaid[$store['store_id']]; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_gafid; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="gafid[<?php echo $store['store_id'];?>]" value="<?php echo $gafid[$store['store_id']]; ?>" class="form-control" />
                  <?php echo $txtleaveblank; ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_adwstatus; ?></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <input type="radio" name="adwstatus[<?php echo $store['store_id'];?>]" value="1" <?php if ($adwstatus[$store['store_id']] == 1) { ?>checked="checked"<?php } ?> />
                    <?php echo $text_enabled; ?></label>
                  <label class="radio-inline">
                    <input type="radio" name="adwstatus[<?php echo $store['store_id'];?>]" value="0" <?php if ($adwstatus[$store['store_id']] == 0) { ?>checked="checked"<?php } ?> />
                    <?php echo $text_disabled; ?></label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_adwid; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="adwid[<?php echo $store['store_id'];?>]" value="<?php echo $adwid[$store['store_id']]; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_adwlbl; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="adwlbl[<?php echo $store['store_id'];?>]" value="<?php echo $adwlbl[$store['store_id']]; ?>" class="form-control" />
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_atctxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="atctxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $atctxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_atctxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_atwtxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="atwtxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $atwtxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_atwtxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_atcmtxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="atcmtxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $atcmtxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_atcmtxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_rmctxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="rmctxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $rmctxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_rmctxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_logntxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="logntxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $logntxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_logntxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_regtxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="regtxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $regtxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_regtxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_chkonetxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="chkonetxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $chkonetxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_chkonetxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_chktwotxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="chktwotxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $chktwotxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_chktwotxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_chkthreetxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="chkthreetxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $chkthreetxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_chkthreetxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_chkfourtxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="chkfourtxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $chkfourtxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_chkfourtxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_chkfivetxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="chkfivetxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $chkfivetxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_chkfivetxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_chksixtxt; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="<?php echo $language['imgsrc']; ?>"/> </span>
                    <input type="text" name="chksixtxt[<?php echo $store['store_id'];?>][<?php echo $language['language_id']; ?>]" value="<?php echo $chksixtxt[$store['store_id']][$language['language_id']]; ?>" placeholder="<?php echo $entry_chksixtxt; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script language="javascript">$('#storetab a:first').tab('show');</script> 
<?php echo $footer; ?>