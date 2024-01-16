<?php
class ControllerExtensionModuleOcrandomslider extends Controller
{
    public function index($setting)
    {
        //print_r($setting); die();
        $this->load->language('extension/module/ocrandomslider');

        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('extension/module/randomproduct');
        $this->load->model('catalog/ocproductrotator');
        $this->load->model('tool/image');

        $data = array();

        $data['heading_title'] = $this->language->get('heading_title');

        $lang_code = $this->session->data['language'];

        if (isset($setting['title']) && $setting['title']) {
            $data['title'] = $setting['title'][$lang_code]['title'];
        } else {
            $data['title'] = $this->language->get('heading_title');
        }

        $data['text_tax'] = $this->language->get('text_tax');
        $data['text_empty'] = $this->language->get('text_empty');
        $data['text_sale'] = $this->language->get('text_sale');
        $data['text_new'] = $this->language->get('text_new');

        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');

        $data['products'] = array();

        if (empty($setting['limit'])) {
            $setting['limit'] = 4;
        }

        $new_filter_data = array(
            'sort' => 'p.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 10,
        );

        $new_results = $this->model_catalog_product->getProducts($new_filter_data);

        $categories = $this->model_catalog_category->getCategoriesPlate();
        //print_r($categories); die();

        if (isset($_GET['category'])) {
            $data['current_category'] = $_GET['category'];
        } else {
            $data['current_category'] = $this->model_catalog_category->getFirstOrderCategory();
        }

        if (isset($setting['rotator']) && $setting['rotator']) {
            $product_rotator_status = (int) $this->config->get('ocproductrotator_status');
        } else {
            $product_rotator_status = 0;
        }

        $products = $this->model_extension_module_randomproduct->getRandomProducts($setting['limit']);
        //print_r($products); die();
		
		$uri = $_SERVER['REQUEST_URI'];

		if (str_replace('product', '', $uri) != $uri)
		  $products = $this->displayProductByOrder($products);
        //print_r($products); die();

        foreach ($products as $product) {
            //print_r($product);die();
            $product_info = $this->model_catalog_product->getProduct($product['product_id']);
            $product_info['category_id'] = $product['category_id'];
            $product_info['category_name'] = $product['category_name'];

            if ($product_info) {
                if ($product_info['image']) {
                    $image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    //$price_num = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                } else {
                    $price = false;
                    $price_num = false;
                }

                if ((float) $product_info['special']) {
                    $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $special_num = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                } else {
                    $special = false;
                    $special_num = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float) $product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = $product_info['rating'];
                } else {
                    $rating = false;
                }

                if ($product_rotator_status == 1) {
                    $product_id = $product_info['product_id'];
                    $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($product_id);

                    if ($product_rotator_image) {
                        $rotator_image = $this->model_tool_image->resize($product_rotator_image, $setting['width'], $setting['height']);
                    } else {
                        $rotator_image = false;
                    }
                } else {
                    $rotator_image = false;
                }

                $is_new = false;
                if ($new_results) {
                    foreach ($new_results as $new_r) {
                        if ($product_info['product_id'] == $new_r['product_id']) {
                            $is_new = true;
                        }
                    }
                }

                $data['tags'] = array();

                if ($product_info['tag']) {
                    $tags = explode(',', $product_info['tag']);

                    foreach ($tags as $tag) {
                        $data['tags'][] = array(
                            'tag' => trim($tag),
                            'href' => $this->url->link('product/search', 'tag=' . trim($tag)),
                        );
                    }
                }

                $data['products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'category_id' => $product_info['category_id'],
                    'category_name' => $this->convertNameUrl($product_info['category_name']),
                    'is_new' => $is_new,
                    'thumb' => $image,
                    'url_image' => $product_info['image'],
                    'rotator_image' => $rotator_image,
                    'name' => $this->convertNameUrl($product_info['name']),
                    'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
                    'price' => $price,
                    'price_num' => $price_num,
                    'special' => $special,
                    'special_num' => $special_num,
                    'tax' => $tax,
                    'rating' => $rating,
                    'tags' => $data['tags'],
                    'href' => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                    'product_id' => $product_info['product_id'],
                    //'categories' => $categories
                );

                $data['categories'] = $categories;
            }
        }

        $data['config_slide'] = array(
            'items' => $setting['item'],
            'autoplay' => $setting['autoplay'],
            'f_show_nextback' => $setting['shownextback'],
            'f_show_ctr' => $setting['shownav'],
            'f_speed' => $setting['speed'],
            'f_show_label' => $setting['showlabel'],
            'f_show_price' => $setting['showprice'],
            'f_show_des' => $setting['showdes'],
            'f_show_addtocart' => $setting['showaddtocart'],
            'f_rows' => $setting['rows'],
        );

        $data['currentCategory'] = 81;

        if ($setting['name'] != 'slider product homepage') {
            return $this->load->view('extension/module/ocrandomslider', $data);
        } else {
            return '<div id="root">' . $this->load->view('extension/module/ocrandomslider_homepage', $data) . '</div>';
        }

    }

    public function getNameUrl($name) {
        $result = '';

        for($i = 0; $i < count($name); $i++) {
            $name[$i] = str_replace([" ", "amp;", "&", ","], "", $name[$i]);

            if($name[$i] != '') {
                if($i < count($name) - 1)
                $result .= strtolower($name[$i]) . '-';
              else 
                $result .= strtolower($name[$i]);
            }
        }
          
        return $result;   
    }

    public function convertNameUrl($nameOriginal) {
        $response = '';

        $name = $nameOriginal;
        $name = explode(" ", $name);

       if(count($name) > 1)
         $response = $this->getNameUrl($name);
       else
         $response = $nameOriginal;

       $response = $this->eliminiere($response); 

       return $response;
    }

    function eliminiere ($nm) {
        $replacement = array( ("Ò") => "o", ("Ó") => "o", ("ò") => "o", ("ó") => "o", ("Ô") => "o", ("ô") => "o", ("Ú") => "U", ("ú") => "u", ("Ù") => "u", ("ù") => "u", ("Û") => "U", ("û") => "u", ("Á") => "a", ("À") => "a", ("à") => "a", ("á") => "a", ("â") => "a", ("é") => "e", ("è") => "e", ("É") => "e", ("È") => "e", ("Ê") => "e", ("ê") => "e", ("ä") => "ae", ("ä") => "ae", ("ö") => "oe", ("ü") => "ue", ("Ä") => "ae", ("Ö") => "oe", ("Ü") => "ue", ("ö") => "oe", ("ü") => "ue", ("Ä") => "ae", ("Ö") => "oe", ("Ü") => "ue", ("ß") => "ss", ("ß") => "ss", ("&") => "+", ("Í") => "I", ("Ì") => "I", ("í") => "i", ("ì") => "i", ("î") => "i", ("Î") => "I", (":") => "", (",") => "", (".") => "", ("´") => "", ("`") => "", ("'") => "", ("“") => "", ("”") => "", ('"') => "", ("„") => "", ("\"") => "", ("\|") => "", ("?") => "", ("!") => "", ("$") => "", ("=") => "", ("*") => "", ("&copy;") => "", ("&reg;") => "", ("®") => "", ("@") => "", ("€") => "", ("©") => "", ("°") => "", ("%") => "", ("(") => "", (")") => "", ("[") => "", ("]") => "", ("™") => "I", ("+") => "-", ("¾") => "", ("½") => "", ("¼") => "", ("²") => "", ("³") => "", (".") => "-", (" ") => "-", ("/") => "-", ("---") => "-", ("--") => "-", ("-") => "-");
        
        $nm = trim(strtolower($nm));
        if (preg_match("/\"/", $nm)) $nm = str_replace("\"", "", $nm);
        $nm = str_replace(array_keys($replacement), array_values($replacement), $nm);
        return $nm;
    }

    public function displayProductByOrder($products)
    {
        $product_id = $_GET['product_id'];

        $result = array();
        $finalResult = array();

        foreach ($products as $item) {
            $result[] = $item;
        }

        for ($i = 0; $i < count($result); $i++) {
            if ($result[$i]['product_id'] == $product_id) {
                for($j = $i; $j < count($result); $j++) {
                    $finalResult[] = $result[$j];
                    /*$temp = $result[$j-1];
                    $result[$j-1] = $result[$j];
                    $result[$j] = $temp;*/
                }

                for($j = 0; $j < $i; $j++) {
                    $finalResult[] = $result[$j];
                }

                return $finalResult;
            }
		}
	}

    public function load()
    {
        //echo 'ddxxc'; die();
        $this->load->language('extension/module/ocrandomslider');

        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('extension/module/randomproduct');
        $this->load->model('catalog/ocproductrotator');
        $this->load->model('tool/image');

        $data = array();

        $data['heading_title'] = $this->language->get('heading_title');

        $lang_code = $this->session->data['language'];

        if (isset($setting['title']) && $setting['title']) {
            $data['title'] = $setting['title'][$lang_code]['title'];
        } else {
            $data['title'] = $this->language->get('heading_title');
        }

        $data['text_tax'] = $this->language->get('text_tax');
        $data['text_empty'] = $this->language->get('text_empty');
        $data['text_sale'] = $this->language->get('text_sale');
        $data['text_new'] = $this->language->get('text_new');

        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');

        $data['products'] = array();

        if (empty($setting['limit'])) {
            $setting['limit'] = 4;
        }

        $new_filter_data = array(
            'sort' => 'p.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 10,
        );

        $new_results = $this->model_catalog_product->getProducts($new_filter_data);

        $categories = $this->model_catalog_category->getCategoriesPlate();

        if (isset($setting['rotator']) && $setting['rotator']) {
            $product_rotator_status = (int) $this->config->get('ocproductrotator_status');
        } else {
            $product_rotator_status = 0;
        }

        $products = $this->model_extension_module_randomproduct->getRandomProducts($setting['limit']);

        foreach ($products as $product) {
            //print_r($product);die();
            $product_info = $this->model_catalog_product->getProduct($product['product_id']);
            $product_info['category_id'] = $product['category_id'];
            $product_info['category_name'] = $product['category_name'];

            if ($product_info) {
                if ($product_info['image']) {
                    $image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    //$price_num = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                } else {
                    $price = false;
                    $price_num = false;
                }

                if ((float) $product_info['special']) {
                    $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $special_num = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                } else {
                    $special = false;
                    $special_num = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float) $product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = $product_info['rating'];
                } else {
                    $rating = false;
                }

                if ($product_rotator_status == 1) {
                    $product_id = $product_info['product_id'];
                    $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($product_id);

                    if ($product_rotator_image) {
                        $rotator_image = $this->model_tool_image->resize($product_rotator_image, $setting['width'], $setting['height']);
                    } else {
                        $rotator_image = false;
                    }
                } else {
                    $rotator_image = false;
                }

                $is_new = false;
                if ($new_results) {
                    foreach ($new_results as $new_r) {
                        if ($product_info['product_id'] == $new_r['product_id']) {
                            $is_new = true;
                        }
                    }
                }

                $data['tags'] = array();

                if ($product_info['tag']) {
                    $tags = explode(',', $product_info['tag']);

                    foreach ($tags as $tag) {
                        $data['tags'][] = array(
                            'tag' => trim($tag),
                            'href' => $this->url->link('product/search', 'tag=' . trim($tag)),
                        );
                    }
                }

                $data['products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'category_id' => $product_info['category_id'],
                    'category_name' => $this->convertNameUrl($product_info['category_name']),
                    'is_new' => $is_new,
                    'thumb' => $image,
                    'url_image' => $product_info['image'],
                    'rotator_image' => $rotator_image,
                    'name' => $this->convertNameUrl($product_info['name']),
                    'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
                    'price' => $price,
                    'price_num' => $price_num,
                    'special' => $special,
                    'special_num' => $special_num,
                    'tax' => $tax,
                    'rating' => $rating,
                    'tags' => $data['tags'],
                    'href' => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                    'product_id' => $product_info['product_id'],
                    //'categories' => $categories
                );

                $data['categories'] = $categories;
            }
        }

        $data['config_slide'] = array(
            'items' => 5,
            'autoplay' => true,
            'f_show_nextback' => $setting['shownextback'],
            'f_show_ctr' => $setting['shownav'],
            'f_speed' => $setting['speed'],
            'f_show_label' => $setting['showlabel'],
            'f_show_price' => $setting['showprice'],
            'f_show_des' => $setting['showdes'],
            'f_show_addtocart' => $setting['showaddtocart'],
            'f_rows' => $setting['rows'],
        );

        $data['currentCategory'] = $_GET['category'];
        //echo $category_id; die();

        //echo 'hahaha'; die();

        echo $this->load->view('extension/module/ocrandomslider_homepage', $data);
    }
}
