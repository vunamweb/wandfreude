<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a> 
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-faq').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-faq">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[question*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'question') { ?>
                    <a href="<?php echo $sort_question; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_question; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_question; ?>"><?php echo $column_question; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($answer == 'answer') { ?>
                    <a href="<?php echo $sort_answer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_answer; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_answer; ?>"><?php echo $column_answer; ?></a>
                    <?php } ?></td>
                <td class="text-left"><?php echo $column_image; ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($faqs) { ?>
                <?php foreach ($faqs as $faq) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($faq['faq_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $faq['faq_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $faq['faq_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $faq['question']; ?></td>
                  <td class="text-left"><?php echo $faq['answer']; ?></td>
                  <td class="text-left"><img src="<?php echo $faq['thumb']; ?>"></td>
                  <td class="text-right"><a href="<?php echo $faq['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>