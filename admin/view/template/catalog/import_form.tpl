<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-faq" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
      </div>
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <?php if(isset($_GET[success])){
            echo '<b style="color: blue">import success</b>';
        } ?>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-review" class="form-horizontal">
            <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-status">Material</label>
                    <div class="col-sm-10">
                        <select name="material" id="material" class="form-control">
                          <?php foreach($materials as $item) { ?>  
                            <option value="<?php echo $item[material_id] ?>"><?php echo $item[question] ?></option>
                          <?php } ?>  
                        </select>
                    </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">File import</label>
                <div class="col-sm-10">
                  <input type="file" name="file" id="file" />
                </div>
              </div>
             </div>
                
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
  <link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
  <script type="text/javascript" src="view/javascript/summernote/opencart.js"></script> 
  <script type="text/javascript"><!--
$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
//--></script>
  </div>
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
//--></script>
<?php echo $footer; ?>