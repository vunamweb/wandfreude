<div class="newletter-subscribe">
	<div class="container">
		<div id="boxes-normal" class="newletter-container col-md-offset-6">
			<div style="" id="dialog-normal" class="window">
				<div class="box">
					<div class="module-title"><h2><?php echo $heading_title; ?></h2></div>
						<div class="box-content newleter-content">
							<div class="des-testimonial">
								<?php echo $newletter_des; ?>
							</div>
							<label><?php echo $newletter_lable; ?></label>
							<div id="frm_subscribe-normal">
								<form name="subscribe" id="subscribe-normal">
									<span class="required">*</span><span><?php echo $entry_email; ?></span><input type="text" value="" name="subscribe_email" id="subscribe_email-normal">
									<input type="hidden" value="" name="subscribe_name" id="subscribe_name-normal" />
									<a class="btn" onclick="email_subscribe()"><?php echo $entry_button; ?></a>
									<?php if($option_unsubscribe) { ?>
									<a class="btn" onclick="email_unsubscribe()"><?php echo $entry_unbutton; ?></a>
									<?php } ?>    
								</form>
							</div><!-- /#frm_subscribe -->
							<div id="notification-normal"></div>
						</div><!-- /.box-content -->
				</div>
		</div>	
		</div>
	</div>
</div>
<script type="text/javascript">
function email_subscribe(){
	$.ajax({
			type: 'post',
			url: 'index.php?route=extension/module/newslettersubscribe/subscribe',
			dataType: 'html',
            data:$("#subscribe-normal").serialize(),
			success: function (html) {
				eval(html);
			}}); 
	
	
}
function email_unsubscribe(){
	$.ajax({
			type: 'post',
			url: 'index.php?route=extension/module/newslettersubscribe/unsubscribe',
			dataType: 'html',
            data:$("#subscribe-normal").serialize(),
			success: function (html) {
				eval(html);
			}}); 
	$('html, body').delay( 1500 ).animate({ scrollTop: 0 }, 'slow'); 
	
}
</script>
<script type="text/javascript">
    $(document).ready(function() {
		$('#subscribe_email-normal').keypress(function(e) {
            if(e.which == 13) {
                e.preventDefault();
                email_subscribe();
            }
			var name= $(this).val();
		  	$('#subscribe_name-normal').val(name);
        });
		$('#subscribe_email-normal').change(function() {
		 var name= $(this).val();
		  		$('#subscribe_name-normal').val(name);
		});
	
    });
</script>

