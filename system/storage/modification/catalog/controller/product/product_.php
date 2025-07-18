<?php
class ControllerProductProduct extends Controller
{
    private $error = array();

    public function index()
    {
        if ($_SERVER['REQUEST_URI'] == '/konfigurator/2') {
            $this->response->redirect('./duschrueckwaende-kuechenrueckwaende-individuell-gestalten/2');
        } else if (!$_GET['number_plate']) {
            $this->response->redirect('./');
        }

        $this->load->language('product/product');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
        );

        $this->load->model('catalog/category');

        $sizeStandard = array();

        if (!isset($_GET['category']) && isset($_GET['category_type']) && !isset($_GET['product_id'])) {
            //echo "1"; die();
            $this->request->get['category'] = $this->model_catalog_category->getFirstOrderCategory();
            $this->request->get['product_id'] = $this->model_catalog_category->getFirstProductOfCategory($this->request->get['category']);
            $sizeStandard = $this->model_catalog_category->getSizeStandardOfCategory($this->request->get['category']);
        } else if (isset($_GET['type_product'])) {
            //echo "2"; die();
            $sizeStandard = $this->model_catalog_category->getSizeStandardOfCategory($_GET['category']);
        }

        //echo $sizeStandard['width'] . 'dd'; die();

        if (isset($this->request->get['path'])) {
            $path = '';

            $parts = explode('_', (string) $this->request->get['path']);

            $category_id = (int) array_pop($parts);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = $path_id;
                } else {
                    $path .= '_' . $path_id;
                }

                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    $data['breadcrumbs'][] = array(
                        'text' => $category_info['name'],
                        'href' => $this->url->link('product/category', 'path=' . $path),
                    );
                }
            }

            // Set the last category breadcrumb
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $url = '';

                if (isset($this->request->get['sort'])) {
                    $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                    $url .= '&order=' . $this->request->get['order'];
                }

                if (isset($this->request->get['page'])) {
                    $url .= '&page=' . $this->request->get['page'];
                }

                if (isset($this->request->get['limit'])) {
                    $url .= '&limit=' . $this->request->get['limit'];
                }

                $data['breadcrumbs'][] = array(
                    'text' => $category_info['name'],
                    'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url),
                );
            }
        }

        $this->load->model('catalog/manufacturer');

        if (isset($this->request->get['manufacturer_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_brand'),
                'href' => $this->url->link('product/manufacturer'),
            );

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

            if ($manufacturer_info) {
                $data['breadcrumbs'][] = array(
                    'text' => $manufacturer_info['name'],
                    'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url),
                );
            }
        }

        if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
            $url = '';

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_search'),
                'href' => $this->url->link('product/search', $url),
            );
        }

        if (isset($this->request->get['product_id'])) {
            $product_id = (int) $this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);
        //print_r($product_info); die();
        $material = $this->model_catalog_product->getMaterial();
        //print_r($material);die();

        if ($product_info && ($product_info['type_product'] == 0 || isset($_GET['type_product']))) {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'][] = array(
                'text' => $product_info['name'],
                'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']),
            );

            if ($_SERVER['REQUEST_URI'] == '/konfigurator/2') {
                $addLink = 'https://' . $_SERVER['HTTP_HOST'] . '/duschrueckwaende-kuechenrueckwaende-individuell-gestalten/2';
            } else {
                $addLink = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }

            $uri = $_SERVER['REQUEST_URI'];

            if (str_replace('?', '', $uri) != $uri) {
                $this->document->addLink($addLink, 'noindex');
            } else {
                $this->document->addLink($addLink, 'canonical');
            }

            $this->document->setTitle($product_info['meta_title']);
            $this->document->setDescription($product_info['meta_description']);
            $this->document->setKeywords($product_info['meta_keyword']);
            //$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
            $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

            $data['heading_title'] = $product_info['name'];

            $data['text_select'] = $this->language->get('text_select');
            $data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $data['text_model'] = $this->language->get('text_model');
            $data['text_reward'] = $this->language->get('text_reward');
            $data['text_points'] = $this->language->get('text_points');
            $data['text_stock'] = $this->language->get('text_stock');
            $data['text_discount'] = $this->language->get('text_discount');
            $data['text_tax'] = $this->language->get('text_tax');
            $data['text_option'] = $this->language->get('text_option');
            $data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            $data['text_write'] = $this->language->get('text_write');
            $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
            $data['text_note'] = $this->language->get('text_note');
            $data['text_tags'] = $this->language->get('text_tags');
            $data['text_related'] = $this->language->get('text_related');
            $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
            $data['text_loading'] = $this->language->get('text_loading');

            $data['entry_qty'] = $this->language->get('entry_qty');
            $data['entry_name'] = $this->language->get('entry_name');
            $data['entry_review'] = $this->language->get('entry_review');
            $data['entry_rating'] = $this->language->get('entry_rating');
            $data['entry_good'] = $this->language->get('entry_good');
            $data['entry_bad'] = $this->language->get('entry_bad');

            $data['button_cart'] = $this->language->get('button_cart');
            $data['button_wishlist'] = $this->language->get('button_wishlist');
            $data['button_compare'] = $this->language->get('button_compare');
            $data['button_upload'] = $this->language->get('button_upload');
            $data['button_continue'] = $this->language->get('button_continue');

            $this->load->model('catalog/review');

            $data['tab_description'] = $this->language->get('tab_description');
            $data['tab_attribute'] = $this->language->get('tab_attribute');
            $data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

            $data['product_id'] = (int) $this->request->get['product_id'];
            $data['manufacturer'] = $product_info['manufacturer'];
            $data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
            $data['model'] = $product_info['model'];
            $data['reward'] = $product_info['reward'];
            $data['points'] = $product_info['points'];
            $data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
            $data['mydescription'] = $product_info['description'];
            $data['material'] = $material;
            $data['name'] = $product_info['name'];
            $data['width_standard'] = $sizeStandard['width'];
            $data['height_standard'] = $sizeStandard['height'];
            if (true) {
                //if ($product_info['quantity'] <= 0) {
                $data['stock'] = $product_info['stock_status'];
            } elseif ($this->config->get('config_stock_display')) {
                $data['stock'] = $product_info['quantity'];
            } else {
                $data['stock'] = $this->language->get('text_instock');
            }

            $this->load->model('tool/image');

            if ($product_info['image']) {
                $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
            } else {
                $data['popup'] = '';
            }

            if ($product_info['image']) {
                //print_r($product_info);
                $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
                $data['img_url'] = $product_info['image'];
            } else {
                $data['thumb'] = '';
            }

            $data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

            foreach ($results as $result) {
                $data['images'][] = array(
                    'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
                    'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height')),
                );
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['price'] = false;
            }

            if ((float) $product_info['special']) {
                $data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['special'] = false;
            }

            if ($this->config->get('config_tax')) {
                $data['tax'] = $this->currency->format((float) $product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
            } else {
                $data['tax'] = false;
            }

            $discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

            $data['discounts'] = array();

            foreach ($discounts as $discount) {
                $data['discounts'][] = array(
                    'quantity' => $discount['quantity'],
                    'price' => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                );
            }

            $data['options'] = array();

            foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
                $product_option_value_data = array();

                foreach ($option['product_option_value'] as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                        if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float) $option_value['price']) {
                            $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                        } else {
                            $price = false;
                        }

                        $product_option_value_data[] = array(
                            'product_option_value_id' => $option_value['product_option_value_id'],
                            'option_value_id' => $option_value['option_value_id'],
                            'name' => $option_value['name'],
                            'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                            'price' => $price,
                            'price_prefix' => $option_value['price_prefix'],
                        );
                    }
                }

                $data['options'][] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value' => $product_option_value_data,
                    'option_id' => $option['option_id'],
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'value' => $option['value'],
                    'required' => $option['required'],
                );
            }

            if ($product_info['minimum']) {
                $data['minimum'] = $product_info['minimum'];
            } else {
                $data['minimum'] = 1;
            }

            $data['review_status'] = $this->config->get('config_review_status');

            if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
                $data['review_guest'] = true;
            } else {
                $data['review_guest'] = false;
            }

            if ($this->customer->isLogged()) {
                $data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
            } else {
                $data['customer_name'] = '';
            }

            $data['reviews'] = sprintf($this->language->get('text_reviews'), (int) $product_info['reviews']);
            $data['rating'] = (int) $product_info['rating'];

            // Captcha
            if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array) $this->config->get('config_captcha_page'))) {
                $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
            } else {
                $data['captcha'] = '';
            }

            $data['share'] = $this->url->link('product/product', 'product_id=' . (int) $this->request->get['product_id']);

            $data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

            $data['products'] = array();

            $results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_related_width'), $this->config->get($this->config->get('config_theme') . '_image_related_height'));
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', $this->config->get($this->config->get('config_theme') . '_image_related_width'), $this->config->get($this->config->get('config_theme') . '_image_related_height'));
                }

                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $price = false;
                }

                if ((float) $result['special']) {
                    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $special = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = (int) $result['rating'];
                } else {
                    $rating = false;
                }

                $data['products'][] = array(
                    'product_id' => $result['product_id'],
                    'thumb' => $image,
                    'name' => $result['name'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
                    'price' => $price,
                    'special' => $special,
                    'tax' => $tax,
                    'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
                    'rating' => $rating,
                    'href' => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                );
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

            $data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

            $this->model_catalog_product->updateViewed($this->request->get['product_id']);

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            if (file_exists('catalog/model/extension/gaenhproprodpage.php')) {
                $this->load->model('extension/gaenhproprodpage');
                $data['footer'] .= $this->model_extension_gaenhproprodpage->getcode(isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0);
            }
            $data['header'] = $this->load->controller('common/header');

            $trustedshops_info = $this->config->get('trustedshops_info');
				$trustedshops_trustbadge = $this->config->get('trustedshops_trustbadge');
				$trustedshops_product = $this->config->get('trustedshops_product');
				$trustedshops_product_expert = $this->config->get('trustedshops_product_expert');
				
				if($trustedshops_info[$this->config->get('config_language_id')]['status'] == 1){
					
						if($trustedshops_info[$this->config->get('config_language_id')]['mode'] == "expert"){
						
							if($trustedshops_trustbadge[$this->config->get('config_language_id')]['collect_orders'] == 1) {
								if($trustedshops_product_expert[$this->config->get('config_language_id')]['collect_reviews'] == 1) {
								
									if($trustedshops_product_expert[$this->config->get('config_language_id')]['review_active'] == 1) {
										$data['trustedshops_display_review'] = true;
									} else {
										$data['trustedshops_display_review'] = false;
									}
									
									if($trustedshops_product_expert[$this->config->get('config_language_id')]['rating_active'] == 1) {
										$data['trustedshops_display_rating'] = true;
									} else {
										$data['trustedshops_display_rating'] = false;
									}
								} else {
									$data['trustedshops_display_review'] = false;
									$data['trustedshops_display_rating'] = false;
								}
							} else {
								$data['trustedshops_display_review'] = false;
								$data['trustedshops_display_rating'] = false;
							}
							
							if($trustedshops_product_expert[$this->config->get('config_language_id')]['review_tab_name'] !='') {
								$data['tab_trusted_shops_reviews'] = $trustedshops_product_expert[$this->config->get('config_language_id')]['review_tab_name'];
							} else {
								$data['tab_trusted_shops_reviews'] = $this->language->get('tab_trusted_shops_reviews');
							}

						} else {
							if($trustedshops_product[$this->config->get('config_language_id')]['collect_reviews'] == 1){
								if($trustedshops_product[$this->config->get('config_language_id')]['review_active'] == 1) {
									$data['trustedshops_display_review'] = true;
								} else {
									$data['trustedshops_display_review'] = false;
								}
								
								if($trustedshops_product[$this->config->get('config_language_id')]['rating_active'] == 1) {
									$data['trustedshops_display_rating'] = true;
								} else {
									$data['trustedshops_display_rating'] = false;
								}
							} else {
								$data['trustedshops_display_review'] = false;
								$data['trustedshops_display_rating'] = false;
							}
							
							if($trustedshops_product[$this->config->get('config_language_id')]['review_tab_name'] !='') {
								$data['tab_trusted_shops_reviews'] = $trustedshops_product[$this->config->get('config_language_id')]['review_tab_name'];
							} else {
								$data['tab_trusted_shops_reviews'] = $this->language->get('tab_trusted_shops_reviews');
							}
								
						}
					} else {
						$data['trustedshops_display_review'] = false;
						$data['trustedshops_display_rating'] = false;						
						$data['tab_trusted_shops_reviews'] = $this->language->get('tab_trusted_shops_reviews');						
					}
					
					/*if($trustedshops_product[$this->config->get('config_language_id')]['review_tab_name'] !='') {
						$data['tab_trusted_shops_reviews'] = $trustedshops_product[$this->config->get('config_language_id')]['review_tab_name'];
					} else {
						$data['tab_trusted_shops_reviews'] = $this->language->get('tab_trusted_shops_reviews');
					}*/
					//echo $data['tab_trusted_shops_reviews'].'-<br />';
					//echo $data['trustedshops_display_review'].'-<br />';
					//echo $data['trustedshops_display_rating'].'-<br />';

            if (isset($_GET[type_product]) && $_GET[type_product] != '') {
                $this->response->setOutput($this->load->view('product/product_plate', $data));
            } else {
                $this->response->setOutput($this->load->view('product/product', $data));
            }

        } else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id),
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['button_continue'] = $this->language->get('button_continue');

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            if (file_exists('catalog/model/extension/gaenhproprodpage.php')) {
                $this->load->model('extension/gaenhproprodpage');
                $data['footer'] .= $this->model_extension_gaenhproprodpage->getcode(isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0);
            }
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function exportXmlForProductConfiguration()
    {
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('extension/module/randomproduct');

        $products = $this->model_catalog_product->getProductConfiguration();
        $url = 'https://' . $_SERVER['HTTP_HOST'];

        //print_r($products); die();

        header('Content-Type:text/xml');

        $domtree = new DOMDocument('1.0', 'UTF-8');

        $xmlRoot = $domtree->createElement("urlset");

        $xmlRoot->setAttribute('xmlns', 'http://www.google.com/schemas/sitemap/0.84');
        $xmlRoot->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xmlRoot->setAttribute('xsi:schemaLocation', 'http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd');

        /* create the root element of the xml tree */
        //$xmlRoot = $domtree->createElement("xml");
        /* append it to the document created */
        $xmlRoot = $domtree->appendChild($xmlRoot);

        /* you should enclose the following lines in a cicle */
        foreach ($products as $product) {
            //echo $product['product_id'] . '<br>';
            //$product_info = $this->model_catalog_product->getProduct($product['product_id']);

            if($product['product_id'] == 5189) {
                //echo 'dd'; 
                //die();
            }
              
            $product_id = $product['product_id'];

            $date = $product['date_modified'];
            
            if($date == '0000-00-00 00:00:00')
              $date = $product['date_added'];

            $given = new DateTime($date, new DateTimeZone("Pacific/Auckland"));
            $given->setTimezone(new DateTimeZone("T"));

            $date = $given->format("Y-m-dTH:i:s+00:00");

            $categories = $this->model_catalog_category->getCategoriesOfProduct($product_id);

            foreach ($categories as $item) {
                $category = $item;

                $currentTrack = $domtree->createElement("url");
                $currentTrack = $xmlRoot->appendChild($currentTrack);
                $currentTrack->appendChild($domtree->createElement('loc', $url . '/product/' . $product_id . '/' . $category . '/2'));
                $currentTrack->appendChild($domtree->createElement('lastmod', $date));
                $currentTrack->appendChild($domtree->createElement('changefreq', 'weekly'));
                $currentTrack->appendChild($domtree->createElement('priority', '0.9'));
            }
        }

        /* get the xml printed */
        $domtree->formatOutput = true;

        echo $domtree->saveXML();
    }

    public function add()
    {
        //print_r($_POST['product_description']);die();
        //$a = json_encode($_POST['product_description']);
        //$a = json_decode($a);
        //print_r($a); die();
        //save image
        $imagedata = base64_decode($_POST['imgdata']);
        $filename = $_POST['filename'];

        $price = $this->getPrice();
        //$_POST['price'] = $price;
        //print_r($_POST); die();

        //path where you want to upload image
        $file = $_SERVER['DOCUMENT_ROOT'] . '/image/uploads/' . $filename;
        //echo $file;
        //$imageurl  = 'http://example.com/uploads/'.$filename.'.png';
        file_put_contents($file, $imagedata);

        $this->load->model('catalog/product');

        $this->request->post['price'] = $price;
        //print_r($this->request->post); die();
        echo $this->model_catalog_product->addProduct($this->request->post);
    }

    public function drawCanvasMirror()
    {
        //print_r($_SERVER);die();
        //save image
        $imagedata = base64_decode($_POST['imgdata']);
        $filename = $_POST['filename'];
        //path where you want to upload image
        $file = $_SERVER['DOCUMENT_ROOT'] . '/image/uploads/' . $filename;
        //echo $file;
        //$imageurl  = 'http://example.com/uploads/'.$filename.'.png';
        file_put_contents($file, $imagedata);
    }

    public function getPriceMaterial()
    {
        $material_id = $_POST['material_id'];
        $qm = $_POST['qm'];

        if ($qm > 0.1) {
            $round_qm = $this->floor_dec($qm, 1, '.');
        } else {
            $round_qm = $this->floor_dec($qm, 2, '.');
        }

        // $round_qm = round($qm,1);
        $this->load->model('catalog/product');

        $chk = $this->model_catalog_product->getPriceMaterial($material_id, $round_qm);
        $price = round($this->model_catalog_product->getPriceMaterial($material_id, $round_qm) * $qm, 2);
        $price = number_format($price, 2, '.', '');

        $result->qqm = $qm;
        $result->priceMaterial = $this->model_catalog_product->getPriceMaterial($material_id, $round_qm);
        $result->price = $price;

        echo json_encode($result);
    }

    public function getPrice()
    {
        $material_id = $_POST['material_id'];
        $qm = $_POST['qm'];

        if ($qm > 0.1) {
            $round_qm = $this->floor_dec($qm, 1, '.');
        } else {
            $round_qm = $this->floor_dec($qm, 2, '.');
        }

        // $round_qm = round($qm,1);
        $this->load->model('catalog/product');

        $chk = $this->model_catalog_product->getPriceMaterial($material_id, $round_qm);
        $price = round($this->model_catalog_product->getPriceMaterial($material_id, $round_qm) * $qm, 2);
        $price = number_format($price, 2, '.', '');

        return $price;
    }

    public function floor_dec($number, $precision = 2, $separator = '.')
    {
        $numberpart = explode($separator, $number);
        $numberpart[1] = substr_replace($numberpart[1], $separator, $precision, 0);
        if ($numberpart[0] >= 0) {
            $numberpart[1] = substr(floor('1' . $numberpart[1]), 1);
        } else {
            $numberpart[1] = substr(ceil('1' . $numberpart[1]), 1);
        }
        // $ceil_number= array($numberpart[0],$numberpart[1]);
        // return implode($separator,$ceil_number);
        if ($numberpart[1] == 0) {
            return $numberpart[0];
        } else {
            $ceil_number = array($numberpart[0], $numberpart[1]);
            return implode($separator, $ceil_number);
        }
    }

    public function review()
    {
        $this->load->language('product/product');

        $this->load->model('catalog/review');

        $data['text_no_reviews'] = $this->language->get('text_no_reviews');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['reviews'] = array();

        $review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

        $results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

        foreach ($results as $result) {
            $data['reviews'][] = array(
                'author' => $result['author'],
                'text' => nl2br($result['text']),
                'rating' => (int) $result['rating'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
            );
        }

        $pagination = new Pagination();
        $pagination->total = $review_total;
        $pagination->page = $page;
        $pagination->limit = 5;
        $pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

        $this->response->setOutput($this->load->view('product/review', $data));
    }

    public function write()
    {
        $this->load->language('product/product');

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
                $json['error'] = $this->language->get('error_name');
            }

            if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
                $json['error'] = $this->language->get('error_text');
            }

            if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
                $json['error'] = $this->language->get('error_rating');
            }

            // Captcha
            if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array) $this->config->get('config_captcha_page'))) {
                $captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

                if ($captcha) {
                    $json['error'] = $captcha;
                }
            }

            if (!isset($json['error'])) {
                $this->load->model('catalog/review');

                $this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

                $json['success'] = $this->language->get('text_success');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getRecurringDescription()
    {
        $this->load->language('product/product');
        $this->load->model('catalog/product');

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        } else {
            $product_id = 0;
        }

        if (isset($this->request->post['recurring_id'])) {
            $recurring_id = $this->request->post['recurring_id'];
        } else {
            $recurring_id = 0;
        }

        if (isset($this->request->post['quantity'])) {
            $quantity = $this->request->post['quantity'];
        } else {
            $quantity = 1;
        }

        $product_info = $this->model_catalog_product->getProduct($product_id);
        $recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

        $json = array();

        if ($product_info && $recurring_info) {
            if (!$json) {
                $frequencies = array(
                    'day' => $this->language->get('text_day'),
                    'week' => $this->language->get('text_week'),
                    'semi_month' => $this->language->get('text_semi_month'),
                    'month' => $this->language->get('text_month'),
                    'year' => $this->language->get('text_year'),
                );

                if ($recurring_info['trial_status'] == 1) {
                    $price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
                } else {
                    $trial_text = '';
                }

                $price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

                if ($recurring_info['duration']) {
                    $text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
                } else {
                    $text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
                }

                $json['success'] = $text;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
