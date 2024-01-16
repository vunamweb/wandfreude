<nav id="column-left">
  <div id="profile">
    <div>
      <?php if ($image) { ?>
      <img src="<?php echo $image; ?>" alt="<?php echo $firstname; ?> <?php echo $lastname; ?>" title="<?php echo $username; ?>" class="img-circle" />
      <?php } else { ?>
      <i class="fa fa-opencart"></i>
      <?php } ?>
    </div>
    <div>
      <h4><?php echo $firstname; ?> <?php echo $lastname; ?></h4>
      <small><?php echo $user_group; ?></small></div>
  </div>
  <ul id="menu">
    <li><a href="index.php?route=extension/module/d_admin_menu&token=<?php echo $_GET["token"]; ?>"><i class="fa fa-bars"></i> <span>Navigation / Menu</span></a></li>
<!-- 	<li><a href="index.php?route=catalog/faq&token=<?php echo $_GET["token"]; ?>"><i class="fa fa-bars"></i> <span>FAQ</span></a></li> -->

	<li id="menu-bildwelten">
		<a class="parent"><i class="fa fa-photo fw"></i> <span>Konfigurator</span></a>

		<ul>
			<li><a href="index.php?route=catalog/product&type_product=1&token=<?php echo $_GET["token"]; ?>"><span>Bilder Upload</span></a></li>
			<li><a href="index.php?route=catalog/category&category_type=1&token=<?php echo $_GET["token"]; ?>"><span>Bilder Kategorien</span></a></li>
			<li><a href="index.php?route=catalog/material&token=<?php echo $_GET["token"]; ?>"><span>Material Liste</span></a></li>
			<li><a href="index.php?route=catalog/import&token=<?php echo $_GET["token"]; ?>"><span>Preise Upload</span></a></li>
			<li>&nbsp;</li>
			<li><a href="index.php?route=extension/module/ocslideshow/update&ocslideshow_id=18&token=<?php echo $_GET["token"]; ?>"><span>Slideshow Startseite</span></a></li>
		</ul>
	</li>

	<li><a href="../cms/morpheus/navigation.php?token=<?php echo $_GET["token"]; ?>"><i class="fa fa-pencil"></i> <span>CMS Morpheus</span></a></li>

    <?php foreach ($menus as $menu) { ?>
    <li id="<?php echo $menu['id']; ?>">
      <?php if ($menu['href']) { ?>
      <a href="<?php echo $menu['href']; ?>"><i class="fa <?php echo $menu['icon']; ?> fw"></i> <span><?php echo $menu['name']; ?></span></a>
      <?php } else { ?>
      <a class="parent"><i class="fa <?php echo $menu['icon']; ?> fw"></i> <span><?php echo $menu['name']; ?></span></a>
      <?php } ?>
      <?php if ($menu['children']) { ?>
      <ul>
        <?php foreach ($menu['children'] as $children_1) { ?>
        <li>
          <?php if ($children_1['href']) { ?>
          <a href="<?php echo $children_1['href']; ?>"><?php echo $children_1['name']; ?></a>
          <?php } else { ?>
          <a class="parent"><?php echo $children_1['name']; ?></a>
          <?php } ?>
          <?php if ($children_1['children']) { ?>
          <ul>
            <?php foreach ($children_1['children'] as $children_2) { ?>
            <li>
              <?php if ($children_2['href']) { ?>
              <a href="<?php echo $children_2['href']; ?>"><?php echo $children_2['name']; ?></a>
              <?php } else { ?>
              <a class="parent"><?php echo $children_2['name']; ?></a>
              <?php } ?>
              <?php if ($children_2['children']) { ?>
              <ul>
                <?php foreach ($children_2['children'] as $children_3) { ?>
                <li><a href="<?php echo $children_3['href']; ?>"><?php echo $children_3['name']; ?></a></li>
                <?php } ?>
              </ul>
              <?php } ?>
            </li>
            <?php } ?>
          </ul>
          <?php } ?>
        </li>
        <?php } ?>
      </ul>
      <?php } ?>
    </li>
    <?php } ?>
  </ul>
  <div id="stats">
    <ul>
      <li>
        <div><?php echo $text_complete_status; ?> <span class="pull-right"><?php echo $complete_status; ?>%</span></div>
        <div class="progress">
          <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $complete_status; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $complete_status; ?>%"> <span class="sr-only"><?php echo $complete_status; ?>%</span></div>
        </div>
      </li>
      <li>
        <div><?php echo $text_processing_status; ?> <span class="pull-right"><?php echo $processing_status; ?>%</span></div>
        <div class="progress">
          <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo $processing_status; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $processing_status; ?>%"> <span class="sr-only"><?php echo $processing_status; ?>%</span></div>
        </div>
      </li>
      <li>
        <div><?php echo $text_other_status; ?> <span class="pull-right"><?php echo $other_status; ?>%</span></div>
        <div class="progress">
          <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $other_status; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $other_status; ?>%"> <span class="sr-only"><?php echo $other_status; ?>%</span></div>
        </div>
      </li>
    </ul>
  </div>
</nav>
