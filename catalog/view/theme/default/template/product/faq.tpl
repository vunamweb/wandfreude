<?php echo $header; ?>
<div id="faq" class="container">
<ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
<div class="row">
<style>
  .collapse .panel-body span imgzz{float:left;margin:0 10px 10px 0;}
</style>

<div class="panel-group" id="accordion">
   <div class="bold"><?php echo $heading_title; ?></div>
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
 </div>
 </div>

<div class="row">
        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
      </div>
</div>
<?php echo $footer; ?>




