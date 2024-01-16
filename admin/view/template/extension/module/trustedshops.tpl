<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-featured" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-featured" class="form-horizontal">
		 
		  <div class="form-group">
			<div class="col-sm-10" id="box-trustedshops-info-intro">
				<span><img src="<?php echo $trustedshops_info_intro; ?>" /></span>
				<button style="display:block; margin: 20px auto;" onclick="window.open('<?php echo $get_your_account_url; ?>'); return false;"><span><span><span><?php echo $button_get_your_account; ?></span></span></span></button>
			</div>
		  </div>
		  
		   <div class="tab-pane">
            <ul class="nav nav-tabs" id="language">
              <?php foreach ($languages as $language) { ?>
              <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
              <?php } ?>
            </ul>
            <div class="tab-content">
              <?php foreach ($languages as $language) { ?>
              <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
			   <fieldset>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-trustbadge-shop-id-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_tsid; ?>"><?php echo $entry_trustedshops_info_tsid; ?></span></label>
                  <div class="col-sm-10">
                    <input type="text" name="trustedshops_info[<?php echo $language['language_id']; ?>][tsid]" placeholder="<?php echo $entry_trustedshops_info_tsid; ?>" id="input-heading<?php echo $language['language_id']; ?>" value="<?php echo isset($trustedshops_info[$language['language_id']]['tsid']) ? $trustedshops_info[$language['language_id']]['tsid'] : ''; ?>" class="form-control" />
					<?php if (isset($error_tsid[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_tsid[$language['language_id']]; ?></div>
                      <?php } ?>
                  </div>
                </div>
				
				<div class="form-group required">
					<label class="col-sm-2 control-label" for="input-trustbadge-mode-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_info_mode; ?></label>
					<div class="col-sm-10">
					  <select name="trustedshops_info[<?php echo $language['language_id']; ?>][mode]" id="input-trustbadge-mode-<?php echo $language['language_id']; ?>" class="form-control">
						
						<option value="standard" <?php if (isset($trustedshops_info[$language['language_id']]['mode']) && $trustedshops_info[$language['language_id']]['mode'] == "standard") { ?>selected="selected"<?php } ?>><?php echo $text_standard; ?></option>
						<option value="expert"<?php if (isset($trustedshops_info[$language['language_id']]['mode']) && $trustedshops_info[$language['language_id']]['mode'] == "expert") { ?> selected="selected"<?php } ?>><?php echo $text_expert; ?></option>
					  </select>
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-sm-10" id="box-expert-info-<?php echo $language['language_id']; ?>"><span><?php echo $help_code; ?></span></div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-trust-badge-status-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_info_status; ?></label>
					<div class="col-sm-10">
					  <select name="trustedshops_info[<?php echo $language['language_id']; ?>][status]" id="input-trust-badge-status-<?php echo $language['language_id']; ?>" class="form-control">
						<?php if (isset($trustedshops_info[$language['language_id']]['status']) && $trustedshops_info[$language['language_id']]['status'] == 1) { ?>
						<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
						<option value="0"><?php echo $text_disabled; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_enabled; ?></option>
						<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
						<?php } ?>
					  </select>
					</div>
				</div>
			   </fieldset>
			   
			   <fieldset>
				<legend><?php echo $text_trustbadge_code; ?></legend>
				<div id="box-standard-<?php echo $language['language_id']; ?>">
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-trustbadge-variant-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_trustbadge_variant; ?></label>
					<div class="col-sm-10">			
					   <select name="trustedshops_trustbadge[<?php echo $language['language_id']; ?>][variant]" id="input-trustbadge-variant-<?php echo $language['language_id']; ?>" class="form-control">
							<?php foreach($variant_list as $idx=>$variant) { ?>					
							<option value="<?php echo $idx; ?>" <?php if (isset($trustedshops_trustbadge[$language['language_id']]['variant']) && $trustedshops_trustbadge[$language['language_id']]['variant'] == $idx) { ?>selected="selected"<?php } ?>><?php echo $variant; ?></option>
							<?php } ?>
					  </select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-trustbadge-offset"><span data-toggle="tooltip" title="<?php echo $help_y_offset; ?>"><?php echo $entry_trustedshops_trustbadge_offset; ?></span></label>
					<div class="col-sm-10">
					  <input type="text" name="trustedshops_trustbadge[<?php echo $language['language_id']; ?>][offset]" value="<?php echo ($trustedshops_trustbadge[$language['language_id']]['offset'])?$trustedshops_trustbadge[$language['language_id']]['offset']:'0'; ?>" placeholder="<?php echo $entry_trustedshops_trustbadge_offset; ?>" id="input-trustbadge-offset-<?php echo $language['language_id']; ?>" class="form-control" />
					</div>
				  </div>
				</div>
				<div id="box-expert-<?php echo $language['language_id']; ?>">
					<div class="form-group required">
						<label class="col-sm-2 control-label" for="input-code"><?php echo $entry_trustedshops_trustbadge_code; ?></label>
						<div class="col-sm-10">
						<?php if($trustedshops_trustbadge[$language['language_id']]['code'] != '') { ?>
							<textarea name="trustedshops_trustbadge[<?php echo $language['language_id']; ?>][code]" rows="5" id="input-trustedshops-trustbadgecode-<?php echo $language['language_id']; ?>" placeholder="<?php echo $entry_trustedshops_trustbadge_code; ?>" class="form-control"><?php echo $trustedshops_trustbadge[$language['language_id']]['code']; ?></textarea>
						<?php } else { ?>
							<textarea name="trustedshops_trustbadge[<?php echo $language['language_id']; ?>][code]" rows="5" id="input-trustedshops-trustbadgecode-<?php echo $language['language_id']; ?>" placeholder="<?php echo $entry_trustedshops_trustbadge_code; ?>" class="form-control"><script type="text/javascript">
	(function ()  {
	var _tsid = '%tsid%';
	_tsConfig = {
	'yOffset': '0',
	'variant': 'reviews',
	'customElementId': '',
	'trustcardDirection': '',
	'customBadgeWidth': '',
	'customBadgeHeight': '',
	'disableResponsive': 'false',
	'disableTrustbadge': 'false',
	'trustCardTrigger': 'mouseenter',
	'customCheckoutElementId': ''
	};
	var _ts = document.createElement('script');
	_ts.type = 'text/javascript';
	_ts.charset = 'utf-8';
	_ts.async = true;
	_ts.src = '//widgets.trustedshops.com/js/' + _tsid + '.js';
	var __ts = document.getElementsByTagName('script')[0];
	__ts.parentNode.insertBefore(_ts, __ts);
	})();
	</script>
					</textarea>
						<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-collect-orders-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_trustbadge_collect_orders; ?></label>
						<div class="col-sm-10">
						  <select name="trustedshops_trustbadge[<?php echo $language['language_id']; ?>][collect_orders]" id="input-trustbadge-collect-orders-<?php echo $language['language_id']; ?>" class="form-control">
							<?php if ($trustedshops_trustbadge[$language['language_id']]['collect_orders']) { ?>
							<option value="1" selected="selected"><?php echo $text_yes; ?></option>
							<option value="0"><?php echo $text_no; ?></option>
							<?php } else { ?>
							<option value="1"><?php echo $text_yes; ?></option>
							<option value="0" selected="selected"><?php echo $text_no; ?></option>
							<?php } ?>
						  </select>
						</div>
					</div>
				</div>			
			  </fieldset>
			  
			  <fieldset>
				<legend><?php echo $text_product_reviews; ?></legend>
				<div class="form-group" id="box-trustedshops-product-collect-reviews-<?php echo $language['language_id']; ?>">
					<label class="col-sm-2 control-label" for="input-trustbadge-product-collect-reviews-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_collect_reviews; ?></label>
					<div class="col-sm-10">
					  <select name="trustedshops_product[<?php echo $language['language_id']; ?>][collect_reviews]" id="input-trustbadge-collect-product-reviews-<?php echo $language['language_id']; ?>" class="form-control">
						<?php if ($trustedshops_product[$language['language_id']]['collect_reviews']) { ?>
						<option value="1" selected="selected"><?php echo $text_yes; ?></option>
						<option value="0"><?php echo $text_no; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_yes; ?></option>
						<option value="0" selected="selected"><?php echo $text_no; ?></option>
						<?php } ?>
					  </select>
					</div>
				</div>
				<div class="form-group" id="box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>">
					<label class="col-sm-2 control-label" for="input-trustbadge-collect-product-reviews-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_collect_reviews; ?></label>
					<div class="col-sm-10">
					  <select name="trustedshops_product_expert[<?php echo $language['language_id']; ?>][collect_reviews]" id="input-trustbadge-collect-product-reviews-<?php echo $language['language_id']; ?>" class="form-control">
						<?php if ($trustedshops_product_expert[$language['language_id']]['collect_reviews']) { ?>
						<option value="1" selected="selected"><?php echo $text_yes; ?></option>
						<option value="0"><?php echo $text_no; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_yes; ?></option>
						<option value="0" selected="selected"><?php echo $text_no; ?></option>
						<?php } ?>
					  </select>
					</div>
					
				</div>
				 <div class="form-group">
						<div class="col-sm-10" id="box-tips-product-reviews-<?php echo $language['language_id']; ?>"><span><?php echo $tips_product_reviews; ?></span></div>
					  </div>
				
				<div id="box-product-reviews-<?php echo $language['language_id']; ?>">
					
				  <div id="box-reviews-standard-<?php echo $language['language_id']; ?>">
					<legend><?php echo $text_product_review_sticker; ?></legend>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-product-reviews-sticker-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_review_sticker; ?>"><?php echo $entry_trustedshops_product_review_active; ?></span></label>
						<div class="col-sm-10">
						  <select name="trustedshops_product[<?php echo $language['language_id']; ?>][review_active]" id="input-trustbadge-product-reviews-sticker-<?php echo $language['language_id']; ?>" class="form-control">
							<?php if ($trustedshops_product[$language['language_id']]['review_active']) { ?>
							<option value="1" selected="selected"><?php echo $text_yes; ?></option>
							<option value="0"><?php echo $text_no; ?></option>
							<?php } else { ?>
							<option value="1"><?php echo $text_yes; ?></option>
							<option value="0" selected="selected"><?php echo $text_no; ?></option>
							<?php } ?>
						  </select>
						</div>
					</div>
					<div id="box-product-review-<?php echo $language['language_id']; ?>">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-offset-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $product_review_tab_name_tips; ?>"><?php echo $entry_trustedshops_product_review_tab_name; ?></span></label>
						<div class="col-sm-10">
						  <input type="text" name="trustedshops_product[<?php echo $language['language_id']; ?>][review_tab_name]" value="<?php echo ($trustedshops_product[$language['language_id']]['review_tab_name'])?$trustedshops_product[$language['language_id']]['review_tab_name']:'Trustedshop reviews'; ?>" placeholder="<?php echo $entry_trustedshops_product_review_tab_name; ?>" id="input-trustbadge-review-tab-name-<?php echo $language['language_id']; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-offset-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_review_border_color; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="trustedshops_product[<?php echo $language['language_id']; ?>][review_border_color]" value="<?php echo ($trustedshops_product[$language['language_id']]['review_border_color'])?$trustedshops_product[$language['language_id']]['review_border_color']:'#FFDC0F'; ?>" placeholder="<?php echo $entry_trustedshops_product_review_border_color; ?>" id="input-trustbadge-shop-id-<?php echo $language['language_id']; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-offset-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_review_star_color; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="trustedshops_product[<?php echo $language['language_id']; ?>][review_star_color]" value="<?php echo ($trustedshops_product[$language['language_id']]['review_star_color'])?$trustedshops_product[$language['language_id']]['review_star_color']:'#C0C0C0'; ?>" placeholder="<?php echo $entry_trustedshops_product_review_star_color; ?>" id="input-trustbadge-shop-id-<?php echo $language['language_id']; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-hide-empty-review"><span data-toggle="tooltip" title="<?php echo $product_review_hide_empty_tips; ?>"><?php echo $entry_trustedshops_product_review_hide_empty; ?></span></label>
						<div class="col-sm-10">
							<div class="checkbox">
								<label>
								  <?php if (isset($trustedshops_product[$language['language_id']]['review_hide_empty']) && $trustedshops_product[$language['language_id']]['review_hide_empty'] == 1) { ?>
								  <input type="checkbox" name="trustedshops_product[<?php echo $language['language_id']; ?>][review_hide_empty]" value="1" checked="checked" id="input-top" />
								  <?php } else { ?>
								  <input type="checkbox" name="trustedshops_product[<?php echo $language['language_id']; ?>][review_hide_empty]" value="1" id="input-top" />
								  <?php } ?>
								  &nbsp; </label>
							 </div>
						</div>
					</div>
					</div>
					<legend><?php echo $text_product_review_stars; ?></legend>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-product-review-stars-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_rating; ?>"><?php echo $entry_trustedshops_product_rating_active; ?></span></label>
						<div class="col-sm-10">
						  <select name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_active]" id="input-trustbadge-product-review-stars-<?php echo $language['language_id']; ?>" class="form-control">
							<?php if ($trustedshops_product[$language['language_id']]['rating_active']) { ?>
							<option value="1" selected="selected"><?php echo $text_yes; ?></option>
							<option value="0"><?php echo $text_no; ?></option>
							<?php } else { ?>
							<option value="1"><?php echo $text_yes; ?></option>
							<option value="0" selected="selected"><?php echo $text_no; ?></option>
							<?php } ?>
						  </select>
						</div>
					</div>
					<div id="box-product-rating-<?php echo $language['language_id']; ?>">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-offset-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_rating_star_color; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_star_color]" value="<?php echo ($trustedshops_product[$language['language_id']]['rating_star_color'])?$trustedshops_product[$language['language_id']]['rating_star_color']:'#FFDC0F'; ?>" placeholder="<?php echo $entry_trustedshops_product_rating_star_color; ?>" id="input-trustbadge-shop-id-<?php echo $language['language_id']; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-offset-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_rating_star_size; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_star_size]" value="<?php echo ($trustedshops_product[$language['language_id']]['rating_star_size'])?$trustedshops_product[$language['language_id']]['rating_star_size']:'15px'; ?>" placeholder="<?php echo $entry_trustedshops_product_rating_star_size; ?>" id="input-trustbadge-shop-id-<?php echo $language['language_id']; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-offset-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_rating_font_size; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_font_size]" value="<?php echo ($trustedshops_product[$language['language_id']]['rating_font_size'])?$trustedshops_product[$language['language_id']]['rating_font_size']:'12px'; ?>" placeholder="<?php echo $entry_trustedshops_product_rating_font_size; ?>" id="input-trustbadge-shop-id-<?php echo $language['language_id']; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-hide-empty-start-ratings-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $product_rating_hide_empty_tips; ?>"><?php echo $entry_trustedshops_product_rating_hide_empty; ?></span></label>
						<div class="col-sm-10">
							<div class="checkbox">
								<label>
								  <?php if (isset($trustedshops_product[$language['language_id']]['rating_hide_empty']) && $trustedshops_product[$language['language_id']]['rating_hide_empty'] == 1) { ?>
								  <input type="checkbox" name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_hide_empty]" value="1" checked="checked" id="input-top" />
								  <?php } else { ?>
								  <input type="checkbox" name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_hide_empty]" value="1" id="input-top" />
								  <?php } ?>
								  &nbsp; </label>
							 </div>
						</div>
					</div>
					</div>
				  </div>
				  
				  <div id="box-review-expert-<?php echo $language['language_id']; ?>">
					<legend><?php echo $text_product_review_sticker; ?></legend>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-product-reviews-sticker-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_review_sticker; ?>"><?php echo $entry_trustedshops_product_expert_review_active; ?></span></label>
						<div class="col-sm-10">
						  <select name="trustedshops_product_expert[<?php echo $language['language_id']; ?>][review_active]" id="input-trustbadge-product-reviews-sticker-<?php echo $language['language_id']; ?>" class="form-control">
							<?php if ($trustedshops_product_expert[$language['language_id']]['review_active']) { ?>
							<option value="1" selected="selected"><?php echo $text_yes; ?></option>
							<option value="0"><?php echo $text_no; ?></option>
							<?php } else { ?>
							<option value="1"><?php echo $text_yes; ?></option>
							<option value="0" selected="selected"><?php echo $text_no; ?></option>
							<?php } ?>
						  </select>
						</div>
					</div>
					<div id="box-review-sticker-<?php echo $language['language_id']; ?>">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-trustbadge-offset-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $product_review_tab_name_tips; ?>"><?php echo $entry_trustedshops_product_review_tab_name; ?></span></label>
							<div class="col-sm-10">
							  <input type="text" name="trustedshops_product_expert[<?php echo $language['language_id']; ?>][review_tab_name]" value="<?php echo ($trustedshops_product_expert[$language['language_id']]['review_tab_name'])?$trustedshops_product_expert[$language['language_id']]['review_tab_name']:'Trustedshop Reviews'; ?>" placeholder="<?php echo $entry_trustedshops_product_review_tab_name; ?>" id="input-trustbadge-review-tab-name-<?php echo $language['language_id']; ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-trustbadge-product-review-code-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_review_code; ?></label>
							<div class="col-sm-10">
							<?php if($trustedshops_product[$language['language_id']]['review_code'] != '') { ?>
							  <textarea name="trustedshops_product[<?php echo $language['language_id']; ?>][review_code]" rows="5" id="input-trustbadge-product-review-code-<?php echo $language['language_id']; ?>" class="form-control"><?php echo $trustedshops_product[$language['language_id']]['review_code']; ?></textarea>
							<?php } else { ?>
							  <textarea name="trustedshops_product[<?php echo $language['language_id']; ?>][review_code]" rows="5" id="input-trustbadge-product-review-code-<?php echo $language['language_id']; ?>" class="form-control"><script type="text/javascript">
	_tsProductReviewsConfig = {
	tsid: '%tsid%',
	sku: ['%sku%'],
	variant: 'productreviews',
	borderColor: '#0DBEDC',
	locale: '%locale%',
	backgroundColor: ' #ffffff',
	starColor: '#FFDC0F',
	starSize: '15px',
	ratingSummary: 'false',
	maxHeight: '1200px',
	'element': '#tab-trustedshop-reviews',
	hideEmptySticker: 'false',
	introtext: '' /* optional */
	};
	var scripts = document.getElementsByTagName('SCRIPT'),
	me = scripts[scripts.length - 1];
	var _ts = document.createElement('SCRIPT');
	_ts.type = 'text/javascript';
	_ts.async = true;
	_ts.charset = 'utf-8';
	_ts.src
	='//widgets.trustedshops.com/reviews/tsSticker/tsProductSticker.js';
	me.parentNode.insertBefore(_ts, me);
	_tsProductReviewsConfig.script = _ts;
	</script>
					</textarea>
							<?php } ?>
							</div>
						</div>
					</div>
					<legend><?php echo $text_product_review_stars; ?></legend>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-trustbadge-product-review-stars-<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_rating; ?>"><?php echo $entry_trustedshops_product_expert_rating_active; ?></span></label>
						<div class="col-sm-10">
						  <select name="trustedshops_product_expert[<?php echo $language['language_id']; ?>][rating_active]" id="input-trustbadge-product-review-stars-<?php echo $language['language_id']; ?>" class="form-control">
							<?php if ($trustedshops_product_expert[$language['language_id']]['rating_active']) { ?>
							<option value="1" selected="selected"><?php echo $text_yes; ?></option>
							<option value="0"><?php echo $text_no; ?></option>
							<?php } else { ?>
							<option value="1"><?php echo $text_yes; ?></option>
							<option value="0" selected="selected"><?php echo $text_no; ?></option>
							<?php } ?>
						  </select>
						</div>
					</div>
					<div id="box-review-start-<?php echo $language['language_id']; ?>">
						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-trustbadge-star-rating-code-<?php echo $language['language_id']; ?>"><?php echo $entry_trustedshops_product_rating_code; ?></label>
							<div class="col-sm-10">
							<?php if($trustedshops_product[$language['language_id']]['rating_code'] != '') { ?>
							  <textarea name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_code]" rows="5" id="input-trustbadge-star-rating-code-<?php echo $language['language_id']; ?>" class="form-control"><?php echo $trustedshops_product[$language['language_id']]['rating_code']; ?></textarea>
							<?php } else { ?>
							  <textarea name="trustedshops_product[<?php echo $language['language_id']; ?>][rating_code]" rows="5" id="input-trustbadge-star-rating-code-<?php echo $language['language_id']; ?>" class="form-control"><script type="text/javascript"
	src="//widgets.trustedshops.com/reviews/tsSticker/tsProductStickerSummary.
	js"></script>
	<script>
	var summaryBadge = new productStickerSummary();
	summaryBadge.showSummary(
	{
	'tsId': '%tsid%',
	'sku': ['%sku%'],
	'element': '#ts_product_widget',
	'starColor' : '#FFDC0F',
	'starSize' : '14px',
	'fontSize' : '12px',
	'showRating' : true,
	'scrollToReviews' : false,
	'enablePlaceholder': true
	}
	);
	</script>
					</textarea>
							<?php } ?>
							</div>
						</div>
					</div>
				  </div>
				</div>
				<div class="col-sm-10" id="product-reviews-info-<?php echo $language['language_id']; ?>"><?php echo $product_reviews_info; ?></div>
			  </fieldset>
				
              </div><!-- tab-pane end -->
              <?php } ?>
            </div>
          </div>
		  
		  
		  
		  <div>
		</form>
	  </div>
	</div>
  </div>
<script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
	$('select[name=\'trustedshops_info[<?php echo $language['language_id']; ?>][mode]\']').on('change', function() {
		if(this.value == "expert") {
			$('#box-standard-<?php echo $language['language_id']; ?>').hide();
			$('#box-expert-<?php echo $language['language_id']; ?>').show();
			$('#box-expert-info-<?php echo $language['language_id']; ?>').show();
			$('#box-trustedshops-product-collect-reviews-<?php echo $language['language_id']; ?>').hide();	
			
			if($('select[name=\'trustedshops_product_expert[<?php echo $language['language_id']; ?>][review_active]\']').val() == 1) {			
				$('#box-review-sticker-<?php echo $language['language_id']; ?>').show();	
			} else {
				$('#box-review-sticker-<?php echo $language['language_id']; ?>').hide();	
			}
			
			if($('select[name=\'trustedshops_product_expert[<?php echo $language['language_id']; ?>][rating_active]\']').val() == 1) {			
				$('#box-review-start-<?php echo $language['language_id']; ?>').show();	
			} else {
				$('#box-review-start-<?php echo $language['language_id']; ?>').hide();	
			}
			
			if($('select[name=\'trustedshops_product_expert[<?php echo $language['language_id']; ?>][collect_reviews]\']').val() == 1) {			
				$('#box-review-expert-<?php echo $language['language_id']; ?>').show();	
			} else {
				$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();	
			}				
			
			if($('select[name=\'trustedshops_trustbadge[<?php echo $language['language_id']; ?>][collect_orders]\']').val() == 1) {
				$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').show();
			} else {
				$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').hide();
				$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();	
				$('#product-reviews-info-<?php echo $language['language_id']; ?>').show();			
			}

			$('#box-reviews-standard-<?php echo $language['language_id']; ?>').hide();	
			
		} else {
			$('#box-standard-<?php echo $language['language_id']; ?>').show();
			$('#box-expert-<?php echo $language['language_id']; ?>').hide();
			$('#box-expert-info-<?php echo $language['language_id']; ?>').hide();
			$('#box-trustedshops-product-collect-reviews-<?php echo $language['language_id']; ?>').show();
			$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').hide();
			$('#product-reviews-info-<?php echo $language['language_id']; ?>').hide();	
			
			if($('select[name=\'trustedshops_product[<?php echo $language['language_id']; ?>][review_active]\']').val() == 1) {
				$('#box-product-review-<?php echo $language['language_id']; ?>').show();
			} else {
				$('#box-product-review-<?php echo $language['language_id']; ?>').hide();
			}
			
			if($('select[name=\'trustedshops_product[<?php echo $language['language_id']; ?>][rating_active]\']').val() == 1) {
				$('#box-product-rating-<?php echo $language['language_id']; ?>').show();
			} else {
				$('#box-product-rating-<?php echo $language['language_id']; ?>').hide();
			}
				
				
			if($('select[name=\'trustedshops_product[<?php echo $language['language_id']; ?>][collect_reviews]\']').val() == 1) {
				$('#box-reviews-standard-<?php echo $language['language_id']; ?>').show();
			} else {
				$('#box-reviews-standard-<?php echo $language['language_id']; ?>').hide();
			}	
			$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();
			
		}
	});



	<?php if($trustedshops_info[$language['language_id']]['mode'] == "expert") { ?>

		$('#box-standard-<?php echo $language['language_id']; ?>').hide();
		$('#box-trustedshops-product-collect-reviews-<?php echo $language['language_id']; ?>').hide();
		$('#box-reviews-standard-<?php echo $language['language_id']; ?>').hide();
		$('#box-expert-<?php echo $language['language_id']; ?>').show();	
		$('#box-expert-info-<?php echo $language['language_id']; ?>').show();
		
		<?php if($trustedshops_product_expert[$language['language_id']]['collect_reviews'] == 1) { ?> 
			$('#box-review-expert-<?php echo $language['language_id']; ?>').show();
		<?php } else { ?>	
			$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();
		<?php } ?>
		
		<?php if($trustedshops_product_expert[$language['language_id']]['review_active'] == 1) { ?>
			$('#box-review-sticker-<?php echo $language['language_id']; ?>').show();
		<?php } else { ?>
			$('#box-review-sticker-<?php echo $language['language_id']; ?>').hide();
		<?php } ?>
		
		<?php if($trustedshops_product_expert[$language['language_id']]['rating_active'] == 1) { ?>
			$('#box-review-start-<?php echo $language['language_id']; ?>').show();
		<?php } else { ?>
			$('#box-review-start-<?php echo $language['language_id']; ?>').hide();
		<?php } ?>
		<?php if($trustedshops_trustbadge[$language['language_id']]['collect_orders'] == 1) { ?>	
			$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').show();
			
			<?php if($trustedshops_product_expert[$language['language_id']]['collect_reviews'] == 1) { ?> 
				$('#box-review-expert-<?php echo $language['language_id']; ?>').show();
			<?php } else { ?>
				$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();
			<?php } ?>
			
			$('#product-reviews-info-<?php echo $language['language_id']; ?>').hide();
		<?php } else { ?>	
			$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').hide();
			$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();
			$('#product-reviews-info-<?php echo $language['language_id']; ?>').show();
		<?php } ?>
	<?php } else { ?>

		$('#box-standard-<?php echo $language['language_id']; ?>').show();
		$('#box-expert-<?php echo $language['language_id']; ?>').hide();	
		$('#box-trustedshops-product-collect-reviews-<?php echo $language['language_id']; ?>').show();
		$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').hide();
		$('#box-expert-info-<?php echo $language['language_id']; ?>').hide();
		
		<?php if($trustedshops_product[$language['language_id']]['collect_reviews'] == 1) { ?>	
			$('#box-reviews-standard-<?php echo $language['language_id']; ?>').show();
		<?php } else { ?>	
			$('#box-reviews-standard-<?php echo $language['language_id']; ?>').hide();
		<?php } ?>
		
		<?php if($trustedshops_product[$language['language_id']]['review_active'] == 1) { ?>
			$('#box-product-review-<?php echo $language['language_id']; ?>').show();
		<?php } else { ?>
			$('#box-product-review-<?php echo $language['language_id']; ?>').hide();
		<?php } ?>
		
		<?php if($trustedshops_product[$language['language_id']]['rating_active'] == 1) { ?>
			$('#box-product-rating-<?php echo $language['language_id']; ?>').show();
		<?php } else { ?>
			$('#box-product-rating-<?php echo $language['language_id']; ?>').hide();
		<?php } ?>
		
		$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();
		$('#product-reviews-info-<?php echo $language['language_id']; ?>').hide();
	<?php } ?>


	$('select[name=\'trustedshops_product[<?php echo $language['language_id']; ?>][collect_reviews]\']').on('change', function() {
		if(this.value == 1){
			$('#box-reviews-standard-<?php echo $language['language_id']; ?>').show();
		} else {
			$('#box-reviews-standard-<?php echo $language['language_id']; ?>').hide();
		}
	});


	$('select[name=\'trustedshops_product[<?php echo $language['language_id']; ?>][review_active]\']').on('change', function() {
		if(this.value == 1){
			$('#box-product-review-<?php echo $language['language_id']; ?>').show();
		} else {
			$('#box-product-review-<?php echo $language['language_id']; ?>').hide();
		}
	});


	$('select[name=\'trustedshops_product[<?php echo $language['language_id']; ?>][rating_active]\']').on('change', function() {
		if(this.value == 1){
			$('#box-product-rating-<?php echo $language['language_id']; ?>').show();
		} else {
			$('#box-product-rating-<?php echo $language['language_id']; ?>').hide();
		}
	});


	$('select[name=\'trustedshops_trustbadge[<?php echo $language['language_id']; ?>][collect_orders]\']').on('change', function() {
		if(this.value == 1){
			$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').show();
			
			if($('select[name=\'trustedshops_product_expert[<?php echo $language['language_id']; ?>][collect_reviews]\']').val() == 1) {
				$('#box-review-expert-<?php echo $language['language_id']; ?>').show();
			}		
			
			$('#product-reviews-info-<?php echo $language['language_id']; ?>').hide();
		} else {
			$('#box-product-expert-collect-reviews-<?php echo $language['language_id']; ?>').hide();
			$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();
			$('#product-reviews-info-<?php echo $language['language_id']; ?>').show();
		}
	});


	$('select[name=\'trustedshops_product_expert[<?php echo $language['language_id']; ?>][collect_reviews]\']').on('change', function() {
		if(this.value == 1){
			$('#box-review-expert-<?php echo $language['language_id']; ?>').show();
		} else {
			$('#box-review-expert-<?php echo $language['language_id']; ?>').hide();
		}
	});

	$('select[name=\'trustedshops_product_expert[<?php echo $language['language_id']; ?>][review_active]\']').on('change', function() {
		if(this.value == 1){
			$('#box-review-sticker-<?php echo $language['language_id']; ?>').show();
		} else {
			$('#box-review-sticker-<?php echo $language['language_id']; ?>').hide();
		}
	});

	$('select[name=\'trustedshops_product_expert[<?php echo $language['language_id']; ?>][rating_active]\']').on('change', function() {
		if(this.value == 1){
			$('#box-review-start-<?php echo $language['language_id']; ?>').show();
		} else {
			$('#box-review-start-<?php echo $language['language_id']; ?>').hide();
		}
	});
<?php } ?>

//--></script>
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
//--></script>
</div>
<?php echo $footer; ?>