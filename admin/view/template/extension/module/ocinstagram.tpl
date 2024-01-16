<?php echo $header;?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-ocmostproduct" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ocmostproduct" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                            <?php if ($error_name) { ?>
                            <div class="text-danger" style="margin-top: 5px;"><?php echo $error_name; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title; ?></label>
                        <div class="col-sm-10">
                            <?php foreach ($languages as $language) { ?>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $language['name']; ?></span>
                                <input type="text" name="title[<?php echo $language['code']; ?>][title]" value="<?php echo isset($title[$language['code']]['title']) ? $title[$language['code']]['title'] : ''; ?>" placeholder="" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="status" id="input-status" class="form-control">
                                <?php if ($status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-userid"><?php echo $entry_userid; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="userid" value="<?php echo $userid; ?>" placeholder="<?php echo $entry_userid; ?>" id="input-userid" class="form-control" />
                            <div class="text-info" style="margin-top: 5px;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $help_userid; ?></div>
                            <?php if ($error_userid) { ?>
                            <div class="text-danger" style="margin-top: 5px;"><?php echo $error_userid; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-access-token"><?php echo $entry_access_token; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="access_token" value="<?php echo $access_token; ?>" placeholder="<?php echo $entry_access_token; ?>" id="input-access-token" class="form-control" />
                            <div class="text-info" style="margin-top: 5px;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $help_access_token; ?></div>
                            <?php if ($error_access_token) { ?>
                            <div class="text-danger" style="margin-top: 5px;"><?php echo $error_access_token; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-limit"><?php echo $entry_limit; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="limit" value="<?php echo $limit; ?>" placeholder="<?php echo $entry_limit; ?>" id="input-limit" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-rows"><?php echo $entry_rows; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="rows" value="<?php echo $rows; ?>" placeholder="<?php echo $entry_rows; ?>" id="input-rows" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_view_mode; ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" id="input-mode" name="view_mode">
                                <option value="gallery" <?php if($view_mode == 'gallery'): ?> selected="selected" <?php endif; ?> ><?php echo $text_gallery_mode; ?></option>
                                <option value="slider"  <?php if($view_mode == 'slider'): ?>  selected="selected" <?php endif; ?> ><?php echo $text_slider_mode; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mode-slider">
                        <label class="col-sm-2 control-label" for="input-item"><?php echo $entry_item; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="item" value="<?php echo $item; ?>" placeholder="<?php echo $entry_item; ?>" id="input-item" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group mode-slider">
                        <label class="col-sm-2 control-label" for="input-speed"><?php echo $entry_speed; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="speed" value="<?php echo $speed; ?>" placeholder="<?php echo $entry_speed; ?>" id="input-speed" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group mode-slider">
                        <label class="col-sm-2 control-label" for="input-autoplay"><?php echo $entry_autoplay; ?></label>
                        <div class="col-sm-10">
                            <select name="autoplay" id="input-autoplay" class="form-control">
                                <?php if ($autoplay) { ?>
                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="0"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mode-slider">
                        <label class="col-sm-2 control-label" for="input-shownextback"><?php echo $entry_shownextback ?></label>
                        <div class="col-sm-10">
                            <select id="input-shownextback" name="shownextback" class="form-control">
                                <option value="0" <?php if($shownextback==0) { ?> selected="selected" <?php } ?> >No</option>
                                <option value="1" <?php if($shownextback==1) { ?> selected="selected" <?php } ?> >Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mode-slider">
                        <label class="col-sm-2 control-label" for="input-shownav"><?php echo $entry_shownav ?></label>
                        <div class="col-sm-10">
                            <select id="input-shownav" name="shownav" class="form-control">
                                <option value="0" <?php if($shownav==0) { ?> selected="selected" <?php } ?> >No</option>
                                <option value="1" <?php if($shownav==1) { ?> selected="selected" <?php } ?> >Yes</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.mode-slider').hide();
        var view_mode = $('#input-mode').val();
        if(view_mode == 'slider') {
            $('.mode-slider').show();
        }

        $('#input-mode').change(function () {
            view_mode = $('#input-mode').val();
            if(view_mode == 'slider') {
                $('.mode-slider').show();
            } else {
                $('.mode-slider').hide();
            }
        })
    })
</script>
<?php echo $footer; ?>