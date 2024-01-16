<?php
class ControllerExtensionModuleOchozmegamenu extends Controller {
	public function index($setting) { 
		$this->language->load('extension/module/ochozmegamenu');

		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		$this->load->model('hozmegamenu/menu');

		$data = array();

      	$data['heading_title'] = $this->language->get('heading_title');

		$menus = $this->model_hozmegamenu_menu->getblockCategTree();
		
		//print_r($menus); die();
        for($i = 0; $i < count($menus); $i++) {
          $position = $i;
          
          for($j = $i+1; $j< count($menus); $j++) {
            $current = explode(":",$menus[$position]->custom_route);
            $current = $current[1];
            
            $currentCheck = explode(":",$menus[$j]->custom_route);
            $currentCheck = $currentCheck[1];
            
            if($current > $currentCheck)
             $position = $j;
         }
         
         $temp = $menus[$i];
         $menus[$i] = $menus[$position];
         $menus[$position] = $temp;
         
         $menus[$i]->custom_route = explode(":", $menus[$i]->custom_route);
         $menus[$i]->custom_route = $menus[$i]->custom_route[0];
        }
        
        $data['menus'] = $menus;

        $categories =  $this->model_catalog_category->getCategoriesPlate();

        $dropdown = '<li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="./duschrueckwaende-kuechenrueckwaende-individuell-gestalten/2" id="navbardrop" data-toggle="dropdown">
        Motive
        </a>
        <div class="dropdown-menu">';

        foreach($categories as $item) {
          $idCategory = $item['category_id'];
          $nameCategory = $item['name_url'];

          $idProduct = $item['product_id'];
          $nameProduct = $item['name_product_id'];

          $link = 'duschrueckwand' . '/' . $nameCategory . '/' . $nameProduct . '/' . $idProduct . '/' . $idCategory . '/2';  
          $dropdown .= '<a class="dropdown-item" href="'.$link.'">'.$item["name"].'</a>';
        }
        
        $dropdown .= '</div>
        </li>';

        $data['dropdown'] = $dropdown; //$this->model_hozmegamenu_menu->getMenuDropdown();
        //print_r($menus); die();
         
        $this->document->addScript('catalog/view/javascript/opentheme/hozmegamenu/custommenu.js');
		$this->document->addScript('catalog/view/javascript/opentheme/hozmegamenu/mobile_menu.js');
		if (file_exists(DIR_TEMPLATE . $this->config->get($this->config->get('config_theme') . '_directory') . '/stylesheet/opentheme/hozmegamenu/css/custommenu.css')) {
			$this->document->addStyle('catalog/view/theme/'.$this->config->get($this->config->get('config_theme') . '_directory').'/stylesheet/opentheme/hozmegamenu/css/custommenu.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/opentheme/hozmegamenu/css/custommenu.css');
		}

		return $this->load->view('extension/module/ochozmegamenu', $data);
		
	}
}
?>