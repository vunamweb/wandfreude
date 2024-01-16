<?php $str = '.' . str_replace("/cutout-image-shop", "", $_SERVER['REQUEST_URI']) ?>
<nav class="navbar navbar-expand-lg navbar-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <?php echo $dropdown; ?>
      
      <?php foreach($menus as $menu) { ?>
        <li class="nav-item <?php if($str == $menu->custom_route ) echo 'active' ?>">
          <a class="nav-link" href="<?php echo $menu->custom_route ?>"><?php echo $menu->name ?></a>
        </li>
      <?php } ?>
    </ul>
    
  </div>
</nav>