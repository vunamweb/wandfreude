<style>
  .collapse .panel-body span img{float:left;margin:0 10px 10px 0;}
</style>

<h3><?php echo $heading_title; ?></h3>
<div class="panel-group" id="accordion">
   <?php if ($faqs < $faq_limit) { ?> 
    <?php foreach($faqs as $key=>$faq) { ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $faq['faq_id'];?>"><h4 class="panel-title">
        <?php echo $faq['question'];?></h4></a>
    </div>
    <div id="collapse<?php echo $faq['faq_id'];?>" class="panel-collapse collapse">
      <div class="panel-body"><span><img src="<?php echo $faq['image'];?>"></span><?php echo $faq['answer'];?></div>
    </div>
  </div>
  
  <?php } ?>
  <?php } else { ?>
  <?php foreach($faqs as $key=>$faq) { ?>
<div class="panel panel-default">
    <div class="panel-heading">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $faq['faq_id'];?>"><h4 class="panel-title">
        <?php echo $faq['question'];?></h4></a>
    </div>
    <div id="collapse<?php echo $faq['faq_id'];?>" class="panel-collapse collapse">
      
      <div class="panel-body"><span><img src="<?php echo $faq['image'];?>"></span><?php echo $faq['answer'];?></div>
    </div>
  </div>
  <?php } ?>
  <div class="" style="text-align: center;margin-top: 5px;">
    <a href="<?php echo $faq_link; ?>"><button class="btn btn-primary"> <?php echo $faq_page; ?></button></a>
  </div>
  
<?php } ?>
  
</div>
