<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-page-builder" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_edit; ?></h3>
            </div>
        </div>
        <div class="panel-body">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-page-builder" class="form-horizontal">
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                        <?php if ($error_name) { ?>
                        <div class="text-danger"><?php echo $error_name; ?></div>
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

                <div class="form-group">
                    <div class="widget-container col-sm-12">
                        <?php if($widgets): ?>
                            <?php $widgets_row_count = 0; ?>
                            <?php foreach($widgets as $widget_row): ?>
                                <?php $cols_format = ""; $col_count = 0;?>
                                <?php foreach($widget_row['cols'] as $row_info): ?>
                                    <?php $col_count++; ?>
                                    <?php if($row_info == end($widget_row['cols'])): ?>
                                        <?php $cols_format .= $row_info['format']; ?>
                                    <?php else: ?>
                                        <?php $cols_format .= $row_info['format'] . ' + '; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <div class="widget-row col-sm-12">
                                    <div class="row-action">
                                        <div class="action-group">
                                            <input type="text" value="<?php echo $widget_row['class'] ?>" name="widget[<?php echo $widgets_row_count ?>][class]" class="form-control input-class-name" placeholder="<?php echo $text_custom_classname; ?>" />
                                            <span class="row-identify"><?php echo $text_columns; ?></span>
                                            <div class="col-count">
                                                <a href="javascript:void(0);" onclick="builder.plusColumn($(this));" rel="1" class="col-plus"></a>
                                                <span class="count"><?php echo $col_count; ?></span>
                                                <a href="javascript:void(0);" onclick="builder.minusColumn($(this));" rel="1" class="col-minus"></a>
                                            </div>
                                            <div class="a-group">
                                                <a class="a-column-custom" title="<?php echo $text_custom_columns; ?>" onclick="builder.customColumns($(this));" href="javascript:void(0);"></a>
                                                <a class="a-row-delete" onclick="builder.removeRow($(this));" href="javascript:void(0);"></a>
                                            </div>
                                        </div>
                                        <input type="hidden" class="cols-format" value="<?php echo $cols_format; ?>" />
                                    </div>
                                    <div class="row-content row-<?php echo $widgets_row_count ?>">
                                    <?php $col_count = 0; ?>
                                    <?php foreach($widget_row['cols'] as $row_info): ?>
                                        <div class="col-sm-<?php echo $row_info['format']; ?> column-area">
                                            <div class="module-area droparea ui-droppable ui-sortable col-<?php echo $col_count; ?>">
                                                <div class="text-insert-module"><span class=""><?php echo $text_insert_module; ?></span></div>
                                                <?php if(isset($row_info['info'])): ?>
                                                    <?php foreach($row_info['info'] as $modules): ?>
                                                        <?php $position = 0; ?>
                                                        <?php foreach($modules as $module): ?>
                                                            <?php foreach ($extensions as $extension) { ?>
                                                                <?php if (!$extension['module'] && $extension['code'] == $module['code']) { ?>
                                                                    <?php $module_url = $extension['url']; ?>
                                                                    <?php $module_name = $extension['name']; ?> 
                                                                <?php } else { ?>
                                                                    <?php foreach ($extension['module'] as $m) { ?>
                                                                        <?php if($m['code'] == $module['code']): ?>
                                                                            <?php $module_url = $m['url']; ?>
                                                                            <?php $module_name = $m['name']; ?>
                                                                        <?php endif; ?>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            <div class="layout-module-info moveable">
                                                                <div class="top">
                                                                    <div class="module-info">
                                                                        <p><?php echo $module_name; ?></p>
                                                                        <a class="btn-edit" href="javascript:void(0);" onclick="loadModule('<?php echo $module_url; ?>')"></a>
                                                                        <a class="btn-remove" href="javascript:void(0);" onclick="builder.removeModule($(this))"></a>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" class="module-in-row" value="<?php echo $widgets_row_count ?>" />
                                                                <input type="hidden" class="module-in-col" value="<?php echo $col_count; ?>" />
                                                                <input type="hidden" class="module-code" name="widget[<?php echo $widgets_row_count ?>][cols][<?php echo $col_count; ?>][info][module][<?php echo $position ?>][code]" value="<?php echo $module['code']; ?>" />
                                                                <input type="hidden" class="module-name" name="widget[<?php echo $widgets_row_count ?>][cols][<?php echo $col_count; ?>][info][module][<?php echo $position ?>][name]" value="<?php echo $module['name']; ?>" />
                                                                <input type="hidden" class="module-url" name="widget[<?php echo $widgets_row_count ?>][cols][<?php echo $col_count; ?>][info][module][<?php echo $position ?>][url]" value="<?php echo $module['url']; ?>" />
                                                            </div>
                                                        <?php $position++; ?>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-action">
                                                <div class="action-group">
                                                    <a class="a-module-add" onclick="builder.showAllModules($(this))" href="javascript:void(0);"><i class="fa fa-plus"></i> <?php echo $text_add_module; ?></a>
                                                </div>
                                            </div>
                                            <input type="hidden" class="col-pos" value="<?php echo $col_count; ?>" />
                                            <input type="hidden" class="col-format" name="widget[<?php echo $widgets_row_count ?>][cols][<?php echo $col_count; ?>][format]" value="<?php echo $row_info['format']; ?>" />
                                        </div>
                                        <?php $col_count++; ?>
                                    <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" class="row-pos" value="<?php echo $widgets_row_count; ?>" />
                                </div>
                                <?php $widgets_row_count++; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php $widget_row = 0; ?>
                            <div class="widget-row col-sm-12">
                                <div class="row-action">
                                    <div class="action-group">
                                        <input type="text" class="form-control input-class-name" name="widget[0][class]" value="" placeholder="<?php echo $text_custom_classname; ?>" />
                                        <span class="row-identify"><?php echo $text_columns; ?></span>
                                        <div class="col-count">
                                            <a href="javascript:void(0);" onclick="builder.plusColumn($(this));" rel="1" class="col-plus"></a>
                                            <span class="count">1</span>
                                            <a href="javascript:void(0);" onclick="builder.minusColumn($(this));" rel="1" class="col-minus"></a>
                                        </div>
                                        <div class="a-group">
                                            <a class="a-column-custom" title="<?php echo $text_custom_columns; ?>" onclick="builder.customColumns($(this));" href="javascript:void(0);"></a>
                                            <a class="a-row-delete" onclick="builder.removeRow($(this));" href="javascript:void(0);"></a>
                                        </div>
                                    </div>
                                    <input type="hidden" class="cols-format" value="12" />
                                </div>
                                <div class="row-content row-<?php echo $widget_row ?>">
                                    <?php $col_count = 0; ?>
                                    <div class="col-sm-12 column-area">
                                        <div class="module-area droparea ui-droppable ui-sortable col-<?php echo $col_count; ?>">
                                            <div class="text-insert-module"><span class=""><?php echo $text_insert_module; ?></span></div>
                                        </div>
                                        <div class="col-action">
                                            <div class="action-group">
                                                <a class="a-module-add" onclick="builder.showAllModules($(this))" href="javascript:void(0);"><i class="fa fa-plus"></i> <?php echo $text_add_module; ?></a>
                                            </div>
                                        </div>
                                        <input type="hidden" class="col-pos" value="<?php echo $col_count; ?>" />
                                        <input type="hidden" class="col-format" name="widget[0][cols][0][format]" value="12" />
                                    </div>
                                </div>
                                <input type="hidden" class="row-pos" value="<?php echo $widget_row ?>" />
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="widget-info-group col-sm-12">
                        <button type="button" class="btn-insert btn">
                            <i class="fa fa-plus"></i>
                            <span><?php echo $text_add_row; ?></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="all-modules-container row">
            <div class="modules-container">
                <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                        <?php $extension_url = $extension['url']; ?>
                        <?php $extension_code = $extension['code']; ?>
                        <?php $extension_name = $extension['name']; ?>
                        <div class="col-sm-4">
                            <button type="button" onclick="builder.addModule('<?php echo $extension_name; ?>', '<?php echo $extension_code; ?>', '<?php echo $extension_url; ?>')" class="btn-choose-module btn btn-default" value="<?php echo $extension_code; ?>">
                                <span>
                                    <?php echo $extension_name; ?>
                                </span>
                            </button>
                        </div>
                    <?php } else { ?>
                        <?php foreach ($extension['module'] as $module) { ?>
                            <?php $module_url = $module['url']; ?>
                            <?php $module_code = $module['code']; ?>
                            <?php $module_name = $module['name']; ?>
                            <div class="col-sm-4">
                                <button type="button" onclick="builder.addModule('<?php echo $module_name; ?>', '<?php echo $module_code; ?>', '<?php echo $module_url; ?>')" class="btn-choose-module btn btn-default" value="<?php echo $module_code; ?>">
                                    <span>
                                        <?php echo $module_name; ?>
                                    </span>
                                </button>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
            <input type="hidden" id="module-row" value="<?php echo $widget_row ?>" />
            <input type="hidden" id="module-col" value="<?php echo $col_count; ?>" />
            <div class="modules-btn-group">
                <button type="button" class="btn-close btn-default pull-right" onclick="builder.closeAllModules();">Close</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="text-columns" value="<?php echo $text_columns; ?>" />
<input type="hidden" id="text-insert-module" value="<?php echo $text_insert_module; ?>" />
<input type="hidden" id="text-add-module" value="<?php echo $text_add_module; ?>" />
<input type="hidden" id="text-custom-columns" value="<?php echo $text_custom_columns; ?>" />
<input type="hidden" id="text-custom-classname" value="<?php echo $text_custom_classname; ?>" />
<input type="hidden" id="text-number-min-over" value="<?php echo $text_number_min_over; ?>" />
<input type="hidden" id="text-number-max-over" value="<?php echo $text_number_max_over; ?>" />
<input type="hidden" id="text-columns-error-format" value="<?php echo $text_columns_error_format; ?>" />

<div class="popup-background"></div>
    <div class="popup-loader-img">
    </div>
    <div class="popup-container">
        <div class="popup-content">
            <iframe src="" id="module-frame" scrolling="yes"></iframe>
        </div>
        <div class="popup-btn-group">
            <button type="button" class="btn-close btn-default pull-right" onclick="closePopup();">Close</button>
        </div>
    </div>
<?php echo $footer; ?>

<script type="text/javascript">
    $('.btn-insert').click(function () {
        var row_number = $('.widget-row:last .row-pos').val();
        builder.drawRow(row_number);
    });

    var module_edit_frame = $('#module-frame');

    function closePopup() {
        $('.popup-background').hide();
        $('.popup-loader-img').hide();
        $('.popup-container').hide();
        module_edit_frame.attr('src', "");
    }

    function loadModule(url) {
        module_edit_frame.attr('src', url);
        $('.popup-background').show();
        $('.popup-loader-img').show();
    }

    module_edit_frame.on('load', function(event) {
        var current_url = document.getElementById("module-frame").contentWindow.location.href;

        if(module_edit_frame.attr('src') != "" && current_url.search('route=extension/module') > -1) {
            module_edit_frame.contents().find('#header,#content .page-header .breadcrumb,#column-left,#footer').hide();
            module_edit_frame.contents().find('#column-left + #content').css('margin', '0px');
            module_edit_frame.contents().find('#content').css('padding', '20px 0 0');
            $('.popup-container').show();
        } else {
            $('.popup-background').hide();
            $('.popup-loader-img').hide();
            $('.popup-container').hide();
        }
    });

    $('.popup-background').on('click', function(){
        closePopup();
        builder.closeAllModules();
    });
</script>