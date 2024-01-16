<div class="ma-nav-mobile-container">
			<div class="hozmenu">
				<div class="navbar">
					<div id="navbar-inner" class="navbar-inner navbar-inactive">
							<div class="menu-mobile">
								<a class="btn btn-navbar navbar-toggle">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
								</a>
								<span class="brand navbar-brand">categories</span>
							</div>
						
							<ul id="ma-mobilemenu" class="mobilemenu nav-collapse collapse"><li><span class=" button-view1 no-close"><a href="http://localhost/cutout-image-shop/index.php?route=product/category&amp;path=65">Category1</a><span class="ttclose"><a href="javascript:void(0)"></a></span></span><ul class="level2" style="display: none;"></ul></li><li><span class=" button-view1 no-close"><a href="http://localhost/cutout-image-shop/index.php?route=product/category&amp;path=66">Category2</a><span class="ttclose"><a href="javascript:void(0)"></a></span></span><ul class="level2" style="display: none;"></ul></li></ul>						
					</div>
				</div>
			</div>
</div>

<div class="nav-container visible-lg visible-md">
			<div class="nav1">
				<div class="nav2">
					<div id="pt_custommenu" class="pt_custommenu">
					<div id="pt_menu_home" class="pt_menu act"><div class="parentMenu"><a href="http://localhost/cutout-image-shop/index.php?route=common/home"><span>Home</span></a></div></div>
                    <div  class="pt_menu">
                        <div class="parentMenu">
                            <a href="./galerie">
                            <span>Galerie</span>
                            </a>
                        </div>
                    </div>
                    <div  class="pt_menu">
                        <div class="parentMenu">
                            <a href="/shop">
                            <span>Shop</span>
                            </a>
                        </div>
                    </div>
                    <div  class="pt_menu">
                        <div class="parentMenu">
                            <a href="#">
                            <span>faq</span>
                            </a>
                        </div>
                    </div>
                    <div  class="pt_menu">
                        <div class="parentMenu">
                            <a href="#">
                            <span>kontact</span>
                            </a>
                        </div>
                    </div>
                    <div  class="pt_menu">
                        <div class="parentMenu">
                            <a href="#">
                            <span>login</span>
                            </a>
                        </div>
                    </div>	
				  </div>
				</div>
			</div>
</div>
<script type="text/javascript">
//<![CDATA[
	var body_class = $('body').attr('class'); 
	if(body_class.search('common-home') != -1) {
		$('#pt_menu_home').addClass('act');
	}
	
var CUSTOMMENU_POPUP_EFFECT = <?php echo $effect; ?>;
var CUSTOMMENU_POPUP_TOP_OFFSET = <?php echo $top_offset ; ?>

//]]>
</script>