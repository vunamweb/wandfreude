$(document).ready(function(){
    builder.reArrangeColumns();
    builder.reArrangeRows();
    builder.triggerDragnDrop();

    $('.widget-row').click(function () {
        $(this).css('cursor', 'grabbing');
        $(this).css('cursor', '-moz-grabbing');
        $(this).css('cursor', '-webkit-grabbing');
    })
});

var builder = {
    'triggerDragnDrop' : function () {
        $('.droparea').sortable({
            placeholder: "ui-state-highlight",
            connectWith: '.droparea',
            items: '.moveable',
            receive: function () {
                var col_pos = $(this).closest('.column-area').find('.col-pos').val();
                var row_pos = $(this).closest('.widget-row').find('.row-pos').val();

                $(this).find('.module-in-row').val(row_pos);
                $(this).find('.module-in-col').val(col_pos);
            },
            stop: function() {
                builder.reArrangeColumns();
            }
        }).droppable({
            accept: '.moveable'
        });

        $('.widget-container').sortable({
            placeholder: "ui-state-highlight",
            stop: function () {
                builder.reArrangeRows();
            }
        });
    },

    'plusColumn' : function (container) {
        var column_count = parseInt(container.closest('.col-count').find('.count').text());
        if(column_count < 12) {
            if(column_count < 4) {
                column_count++;
            } else {
                if(column_count >= 4 && column_count < 6) {
                    column_count = 6;
                } else {
                    if(column_count >= 6) column_count = 12;
                }
            }

            container.closest('.col-count').find('.count').html(column_count);
            var row_container = container.closest('.widget-row').find('.row-content');
            builder.divideColumn(column_count, row_container);
        }

        builder.triggerDragnDrop();
    },

    'minusColumn' : function (container) {
        var column_count = parseInt(container.closest('.col-count').find('.count').text());
        if(column_count > 1) {
            if(column_count <= 12 && column_count > 6) {
                column_count = 6;
            } else {
                if(column_count <= 6 && column_count > 4) {
                    column_count = 4;
                } else {
                    if(column_count <= 4) {
                        column_count--;
                    }
                }
            }
            container.closest('.col-count').find('.count').html(column_count);
            var row_container = container.closest('.widget-row').find('.row-content');
            builder.divideColumn(column_count, row_container);
        }

        builder.triggerDragnDrop();
    },

    'customColumns' : function (container) {
        var row_container = container.closest('.widget-row').find('.row-content');
        builder.setUpColumns(row_container);
        builder.triggerDragnDrop();
    },

    'divideColumn' : function (col_number, container) {
        switch (col_number) {
            case 1:
                builder.drawColumns("12", container);
                break;
            case 2:
                builder.drawColumns("6 + 6", container);
                break;
            case 3:
                builder.drawColumns("4 + 4 + 4", container);
                break;
            case 4:
                builder.drawColumns("3 + 3 + 3 + 3", container);
                break;
            case 6:
                builder.drawColumns("2 + 2 + 2 + 2 + 2 + 2", container);
                break;
            case 12:
                builder.drawColumns("1 + 1 + 1 + 1 + 1 + 1 + 1 + 1 + 1 + 1 + 1 + 1", container);
                break;
            default: break;
        }
        builder.triggerDragnDrop();
    },

    'setUpColumns' : function (container) {
        var cols = container.closest('.widget-row').find('.cols-format').val();
        var text_custom_columns = $('#text-custom-columns').val();
        var columns = prompt(text_custom_columns, cols);
        if(columns !== null) {
            builder.drawColumns(columns, container);
        }
        builder.triggerDragnDrop();
    },

    'drawColumns' : function (cols, container) {
        var html = "";
        var count = 0;
        var col_count = 0;
        var isDraw = false;
        var row_pos = container.closest('.widget-row').find('.row-pos').val();
        var text_insert_module = $('#text-insert-module').val();
        var text_add_module = $('#text-add-module').val();
        var text_columns_error_format = $('#text-columns-error-format').val();

        var columns = cols.split('+').map(function (str) {
            return str.trim();
        });

        if(columns) {
            var col_num = columns.length;

            columns.forEach(function (col) {
                if(col != "0") {
                    count += parseInt(col);
                } else {
                    count = 13;
                }
            });

            if(count == 12) {
                isDraw = true;
            }

            if(isDraw) {
                columns.forEach(function (col) {
                    if(container.has('.col-' + col_count + ' .layout-module-info').length) {
                        html += '<div class="col-sm-' + col + ' column-area">';
                        html += '   <div class="module-area droparea ui-droppable ui-sortable col-' + col_count + '">';
                        html += container.find('.col-' + col_count).html();
                        html += '   </div>';
                        html += '   <div class="col-action">';
                        html += '       <div class="action-group">';
                        html += '           <a class="a-module-add" onclick="builder.showAllModules($(this))" href="javascript:void(0);"><i class="fa fa-plus"></i> ' + text_add_module + '</a>';
                        html += '       </div>';
                        html += '   </div>';
                        html += '   <input type="hidden" class="col-pos" value="' + col_count + '" />';
                        html += '   <input type="hidden" name="widget['+ row_pos + '][cols]['+ col_count +'][format]" class="col-format" value="' + col + '" />';
                        html += '</div>';
                        col_count++;
                        container.find('.col-' + col_count).find('.text-insert-module').hide();
                    } else {
                        html += '<div class="col-sm-' + col + ' column-area">';
                        html += '   <div class="module-area droparea ui-droppable ui-sortable col-' + col_count + '">';
                        html += '       <div class="text-insert-module"><span>'+ text_insert_module +'</span></div>';
                        html += '   </div>';
                        html += '   <div class="col-action">';
                        html += '       <div class="action-group">';
                        html += '           <a class="a-module-add" onclick="builder.showAllModules($(this))" href="javascript:void(0);"><i class="fa fa-plus"></i> ' + text_add_module + '</a>';
                        html += '       </div>';
                        html += '   </div>';
                        html += '   <input type="hidden" class="col-pos" value="' + col_count + '" />';
                        html += '   <input type="hidden" name="widget['+ row_pos + '][cols]['+ col_count +'][format]" class="col-format" value="' + col + '" />';
                        html += '</div>';
                        col_count++;
                    }
                });

                container.closest('.widget-row').find('.count').html(col_num);
                container.closest('.widget-row').find('.cols-format').val(cols);
                container.html(html);
            } else {
                alert(text_columns_error_format);
            }
        } else {
            alert(text_columns_error_format);
        }
        builder.triggerDragnDrop();
    },

    'drawRow' : function (row_number) {
        row_number++;
        var text_columns = $("#text-columns").val();
        var text_insert_module = $("#text-insert-module").val();
        var text_add_module = $('#text-add-module').val();
        var text_custom_columns = $('#text-custom-columns').val();
        var text_custom_classname = $('#text-custom-classname').val();
        var html = "";
        html += '<div class="widget-row col-sm-12">';
        html += '   <div class="row-action">';
        html += '       <div class="action-group">';
        html += '           <input type="text" class="form-control input-class-name" name="widget['+ row_number + '][class]" value="" placeholder="'+ text_custom_classname +'" />';
        html += '           <span class="row-identify">'+ text_columns +'</span>';
        html += '           <div class="col-count">';
        html += '               <a href="javascript:void(0);" onclick="builder.plusColumn($(this));" rel="1" class="col-plus"></a>';
        html += '               <span class="count" >1</span>';
        html += '               <a href="javascript:void(0);" onclick="builder.minusColumn($(this));" rel="1" class="col-minus"></a>';
        html += '           </div>';
        html += '           <div class="a-group">';
        html += '               <a class="a-column-custom" onclick="builder.customColumns($(this));" href="javascript:void(0);" title="' + text_custom_columns + '"></a>';
        html += '               <a class="a-row-delete" onclick="builder.removeRow($(this));" href="javascript:void(0);"></a>';
        html += '           </div>';
        html += '       </div>';
        html += '       <input type="hidden" class="cols-format" value="12" />';
        html += '   </div>';
        html += '   <div class="row-content row-'+ row_number +'">' +
            '       <div class="col-sm-12 column-area">' +
            '           <div class="module-area droparea ui-droppable ui-sortable col-0">' +
            '               <div class="text-insert-module"><span>'+ text_insert_module +'</span></div>' +
            '           </div> ' +
            '           <div class="col-action"> ' +
            '               <div class="action-group">' +
            '                   <a class="a-module-add" onclick="builder.showAllModules($(this))" href="javascript:void(0);"><i class="fa fa-plus"></i> ' + text_add_module + '</a> ' +
            '               </div> ' +
            '           </div> ' +
            '           <input type="hidden" class="col-pos" value="0" />' +
            '           <input type="hidden" name="widget['+ row_number + '][cols][0][format]" class="col-format" value="12" />' +
            '       </div> ' +
            '   </div> ' +
            '   <input type="hidden" class="row-pos" value="'+ row_number +'" />' +
            '</div>';
        $('.widget-container').append(html);
        builder.triggerDragnDrop();
    },

    'removeRow' : function (container) {
        container.closest('.widget-row').remove();
        builder.reArrangeRows();
        builder.reArrangeColumns();
    },

    'showAllModules' : function (container) {
        var row_pos = container.closest('.widget-row').find('.row-pos').val();
        var col_pos = container.closest('.column-area').find('.col-pos').val();
        $('#module-row').val(row_pos);
        $('#module-col').val(col_pos);
        $('.popup-background').show();
        $('.popup-loader-img').show();
        $('.all-modules-container').show(600);
        builder.triggerDragnDrop();
    },

    'closeAllModules' : function() {
        $('.all-modules-container').hide(600);
        $('.popup-background').hide();
        $('.popup-loader-img').hide();
    },

    'addModule' : function(name, code, url) {
        var row_pos =  $('#module-row').val();
        var col_pos =  $('#module-col').val();

        html = '<div class="layout-module-info moveable">';
        html += '	<div class="top">';
        html += '		<div class="module-info">';
        html += '			<p>' + name + '</p>';
        html += '		    <a class="btn-edit" href="javascript:void(0);" onclick="loadModule(\'' + url + '\')"></a>';
        html += '			<a class="btn-remove" href="javascript:void(0);" onclick="builder.removeModule($(this))"></a>';
        html += '		</div>';
        html += '	</div>';
        html += '	<input type="hidden" class="module-in-row" value="' + row_pos +'" />';
        html += '	<input type="hidden" class="module-in-col" value="' + col_pos +'" />';
        html += '	<input type="hidden" class="module-code" name="widget['+ row_pos + '][cols]['+ col_pos +'][info][module][0][code]" value="' + code +'" />';
        html += '	<input type="hidden" class="module-name" name="widget['+ row_pos + '][cols]['+ col_pos +'][info][module][0][name]" value="' + name +'" />';
        html += '	<input type="hidden" class="module-url" name="widget['+ row_pos + '][cols]['+ col_pos +'][info][module][0][url]" value="' + url +'" />';
        html +=	'</div>';

        $('.row-' + row_pos + ' .col-' + col_pos + ' .text-insert-module').hide();
        $('.row-' + row_pos + ' .col-' + col_pos).append(html);
        $('.all-modules-container').hide(600);
        $('.popup-background').hide();
        $('.popup-loader-img').hide();
        builder.reArrangeColumns();
        builder.triggerDragnDrop();
    },

    'removeModule' : function (container) {
        var module_area = container.closest('.module-area');
        container.closest('.layout-module-info').remove();
        if(module_area.has('.layout-module-info').length) {
            module_area.find('.text-insert-module').hide();
        } else {
            module_area.find('.text-insert-module').show();
        }
        builder.reArrangeColumns();
        builder.triggerDragnDrop();
    },

    'reArrangeColumns' : function () {
        $('.droparea').each(function () {
            var position_code = 0;
            var position_name = 0;
            var position_url = 0;
            var col_pos = $(this).closest('.column-area').find('.col-pos').val();
            var row_pos = $(this).closest('.widget-row').find('.row-pos').val();

            if($(this).has('.layout-module-info').length) {
                $(this).find('.text-insert-module').hide();
            } else {
                $(this).find('.text-insert-module').show();
            }

            $(this).find('.module-code').each(function() {
                $(this).attr('name', 'widget['+ row_pos + '][cols]['+ col_pos +'][info][module]['+ position_code +'][code]');
                position_code++;
            });

            $(this).find('.module-name').each(function() {
                $(this).attr('name', 'widget['+ row_pos + '][cols]['+ col_pos +'][info][module]['+ position_name +'][name]');
                position_name++;
            });

            $(this).find('.module-url').each(function() {
                $(this).attr('name', 'widget['+ row_pos + '][cols]['+ col_pos +'][info][module]['+ position_url +'][url]');
                position_url++;
            });
        });
    },

    'reArrangeRows' : function () {
        var row_pos = 0;
        $('.widget-row').each(function () {
            $(this).find('.column-area').each(function () {
                var col_pos = $(this).find('.col-pos').val();
                var position_code = 0;
                var position_name = 0;
                var position_url = 0;

                $(this).closest('.widget-row').find('.row-pos').val(row_pos);
                $(this).closest('.widget-row').find('.row-content').removeClass().addClass('row-content row-' + row_pos);
                $(this).closest('.widget-row').find('.input-class-name').attr('name', 'widget['+ row_pos + '][class]');
                $(this).closest('.column-area').find('.col-format').attr('name', 'widget['+ row_pos + '][cols]['+ col_pos +'][format]');
                $(this).find('.layout-module-info').each(function () {
                    $(this).find('.module-in-row').val(row_pos);
                    $(this).find('.module-code').each(function() {
                        $(this).attr('name', 'widget['+ row_pos + '][cols]['+ col_pos +'][info][module]['+ position_code +'][code]');
                        position_code++;
                    });

                    $(this).find('.module-name').each(function() {
                        $(this).attr('name', 'widget['+ row_pos + '][cols]['+ col_pos +'][info][module]['+ position_name +'][name]');
                        position_name++;
                    });

                    $(this).find('.module-url').each(function() {
                        $(this).attr('name', 'widget['+ row_pos + '][cols]['+ col_pos +'][info][module]['+ position_url +'][url]');
                        position_url++;
                    });
                });
            });
            row_pos++;
        });
    }
};