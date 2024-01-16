<?php
class ControllerCommonHeader extends Controller
{
    public function index()
    {

        $data['facebook_pixel_id_FAE'] =
        $this->fbevents['facebook_pixel_id_FAE'];
        $data['facebook_pixel_pii_FAE'] =
        $this->fbevents['facebook_pixel_pii_FAE'];
        $data['facebook_pixel_params_FAE'] =
        $this->fbevents['facebook_pixel_params_FAE'];
        $data['facebook_pixel_params_FAE'] =
        $this->fbevents['facebook_pixel_params_FAE'];
        $data['facebook_pixel_event_params_FAE'] =
        $this->fbevents['facebook_pixel_event_params_FAE'];
        $data['facebook_enable_cookie_bar'] =
        $this->fbevents['facebook_enable_cookie_bar'];

        // remove away the facebook_pixel_event_params_FAE in session data
        // to avoid duplicate firing after the 1st fire
        unset($this->session->data['facebook_pixel_event_params_FAE']);

// Analytics
        $this->load->model('extension/extension');

        $data['analytics'] = array();

        $analytics = $this->model_extension_extension->getExtensions('analytics');

        foreach ($analytics as $analytic) {
            if ($this->config->get($analytic['code'] . '_status')) {
                $data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
            }
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

        $data['title'] = $this->document->getTitle();

        $data['base'] = $server;
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();

        foreach ($data['links'] as $item) {
            if($item['rel'] != 'canonical')
              $data['links_1'][] = $item;
        }

        $data['links'] = $data['links_1'];

        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts();
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }

        $this->load->language('common/header');

        $data['text_home'] = $this->language->get('text_home');

        // Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        }

        $data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

        $data['text_account'] = $this->language->get('text_account');
        $data['text_register'] = $this->language->get('text_register');
        $data['text_login'] = $this->language->get('text_login');
        $data['text_order'] = $this->language->get('text_order');
        $data['text_transaction'] = $this->language->get('text_transaction');
        $data['text_download'] = $this->language->get('text_download');
        $data['text_logout'] = $this->language->get('text_logout');
        $data['text_checkout'] = $this->language->get('text_checkout');
        $data['text_category'] = $this->language->get('text_category');
        $data['text_all'] = $this->language->get('text_all');

        $data['home'] = $this->url->link('common/home');
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['logged'] = $this->customer->isLogged();
        $data['account'] = $this->url->link('account/account', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['login'] = $this->url->link('account/login', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);
        $data['shopping_cart'] = $this->url->link('checkout/cart');
        $data['checkout'] = $this->url->link('checkout/checkout', '', true);
        $data['contact'] = $this->url->link('information/contact');
        $data['telephone'] = $this->config->get('config_telephone');

        // Menu
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        if($_GET['route'] == 'product/product') {
          //print_r($_GET); die();
          $product_id = ($_GET['product_id'] != '') ? $_GET['product_id'] : 'null';
          $product = $this->model_catalog_product->getProduct($product_id);
          $product_name = $this->convertNameUrl($product['name']);

          $category_path = $_GET['category'];

          $category_id = $this->model_catalog_product->getFirstCategoryOfProduct($product_id);
          $category = $this->model_catalog_category->getCategory($category_id);
          $category_name = $this->convertNameUrl($category['name']);

          $number_plate = $_GET['number_plate'];

          if($category_path != $category_id) {
            $data['canonical_product'] = true;
            $data['canonical_product_link'] = '<link rel="canonical" href="https://www.wandfreude.de/duschrueckwand/'.$category_name.'/'.$product_name.'/'.$product_id.'/'.$category_id.'/'.$number_plate.'" />';
          } else {
            $data['canonical_product'] = false;
          }
        }

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id' => $child['category_id'],
                        'filter_sub_category' => true,
                    );

                    $children_data[] = array(
                        'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name' => $category['name'],
                    'children' => $children_data,
                    'column' => $category['column'] ? $category['column'] : 1,
                    'href' => $this->url->link('product/category', 'path=' . $category['category_id']),
                );
            }
        }

        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');

        // For page specific css
        if (isset($this->request->get['route'])) {
            if (isset($this->request->get['product_id'])) {
                $class = '-' . $this->request->get['product_id'];
            } elseif (isset($this->request->get['path'])) {
                $class = '-' . $this->request->get['path'];
            } elseif (isset($this->request->get['manufacturer_id'])) {
                $class = '-' . $this->request->get['manufacturer_id'];
            } elseif (isset($this->request->get['information_id'])) {
                $class = '-' . $this->request->get['information_id'];
            } else {
                $class = '';
            }

            $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
        } else {
            $data['class'] = 'common-home';
        }

        if ($_GET['route'] == 'checkout/success') {
            $this->load->model('extension/gaenhprochksuccess');
            //$this->model_extension_gaenhprochksuccess->getcode(193);
            $data['script_code'] = $this->model_extension_gaenhprochksuccess->getcode(!empty($this->session->data['order_id']) ? $this->session->data['order_id'] : 0);
        } else if ($_GET['route'] == 'checkout/checkout') {
            //$this->load->model('extension/gaenhprochkbeg');
            //$data['script_code'] = $this->model_extension_gaenhprochkbeg->getcode();
            $data['script_code'] = '';
        } else {
            $data['script_code'] = '';
        }

        $uri = $_SERVER['REQUEST_URI'];

        if (str_replace('?', '', $uri) != $uri) {
            $data['noindex'] = true;
        } else {
            $data['noindex'] = false;
        }

        $data['block1'] = $this->load->controller('common/block1');
        $data['block2'] = $this->load->controller('common/block2');

        return $this->load->view('common/header', $data);
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
}
