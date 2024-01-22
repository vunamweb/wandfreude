<?php

//global $access_token;

class ControllerApiOrder extends Controller
{
	
    public function add()
    {
		echo "add "; exit();
        $this->load->language('api/order');

        $json = array();

        if (!isset($this->session->data['api_id']))
        {
            $json['error'] = $this->language->get('error_permission');
        } else
        {
            // Customer
            if (!isset($this->session->data['customer']))
            {
                $json['error'] = $this->language->get('error_customer');
            }

            // Payment Address
            if (!isset($this->session->data['payment_address']))
            {
                $json['error'] = $this->language->get('error_payment_address');
            }

            // Payment Method
            if (!$json && !empty($this->request->post['payment_method']))
            {
                if (empty($this->session->data['payment_methods']))
                {
                    $json['error'] = $this->language->get('error_no_payment');
                } elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']]))
                {
                    $json['error'] = $this->language->get('error_payment_method');
                }

                if (!$json)
                {
                    $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->
                        request->post['payment_method']];
                }
            }

            if (!isset($this->session->data['payment_method']))
            {
                $json['error'] = $this->language->get('error_payment_method');
            }

            // Shipping
            if ($this->cart->hasShipping())
            {
                // Shipping Address
                if (!isset($this->session->data['shipping_address']))
                {
                    $json['error'] = $this->language->get('error_shipping_address');
                }

                // Shipping Method
                if (!$json && !empty($this->request->post['shipping_method']))
                {
                    if (empty($this->session->data['shipping_methods']))
                    {
                        $json['error'] = $this->language->get('error_no_shipping');
                    } else
                    {
                        $shipping = explode('.', $this->request->post['shipping_method']);

                        if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]))
                        {
                            $json['error'] = $this->language->get('error_shipping_method');
                        }
                    }

                    if (!$json)
                    {
                        $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                    }
                }

                // Shipping Method
                if (!isset($this->session->data['shipping_method']))
                {
                    $json['error'] = $this->language->get('error_shipping_method');
                }
            } else
            {
                unset($this->session->data['shipping_address']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
            }

            // Cart
            if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) ||
                (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')))
            {
                $json['error'] = $this->language->get('error_stock');
            }

            // Validate minimum quantity requirements.
            $products = $this->cart->getProducts();

            foreach ($products as $product)
            {
                $product_total = 0;

                foreach ($products as $product_2)
                {
                    if ($product_2['product_id'] == $product['product_id'])
                    {
                        $product_total += $product_2['quantity'];
                    }
                }

                if ($product['minimum'] > $product_total)
                {
                    $json['error'] = sprintf($this->language->get('error_minimum'), $product['name'],
                        $product['minimum']);

                    break;
                }
            }

            if (!$json)
            {
                $json['success'] = $this->language->get('text_success');

                $order_data = array();

                // Store Details
                $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
                $order_data['store_id'] = $this->config->get('config_store_id');
                $order_data['store_name'] = $this->config->get('config_name');
                $order_data['store_url'] = $this->config->get('config_url');

                // Customer Details
                $order_data['customer_id'] = $this->session->data['customer']['customer_id'];
                $order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
                $order_data['firstname'] = $this->session->data['customer']['firstname'];
                $order_data['lastname'] = $this->session->data['customer']['lastname'];
                $order_data['email'] = $this->session->data['customer']['email'];
                $order_data['telephone'] = $this->session->data['customer']['telephone'];
                $order_data['fax'] = $this->session->data['customer']['fax'];
                $order_data['custom_field'] = $this->session->data['customer']['custom_field'];

                // Payment Details
                $order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
                $order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
                $order_data['payment_company'] = $this->session->data['payment_address']['company'];
                $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
                $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
                $order_data['payment_city'] = $this->session->data['payment_address']['city'];
                $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
                $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
                $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
                $order_data['payment_country'] = $this->session->data['payment_address']['country'];
                $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
                $order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
                $order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ?
                    $this->session->data['payment_address']['custom_field'] : array());

                if (isset($this->session->data['payment_method']['title']))
                {
                    $order_data['payment_method'] = $this->session->data['payment_method']['title'];
                } else
                {
                    $order_data['payment_method'] = '';
                }

                if (isset($this->session->data['payment_method']['code']))
                {
                    $order_data['payment_code'] = $this->session->data['payment_method']['code'];
                } else
                {
                    $order_data['payment_code'] = '';
                }

                // Shipping Details
                if ($this->cart->hasShipping())
                {
                    $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
                    $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
                    $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                    $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                    $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
                    $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                    $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                    $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                    $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                    $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                    $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                    $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
                    $order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ?
                        $this->session->data['shipping_address']['custom_field'] : array());

                    if (isset($this->session->data['shipping_method']['title']))
                    {
                        $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                    } else
                    {
                        $order_data['shipping_method'] = '';
                    }

                    if (isset($this->session->data['shipping_method']['code']))
                    {
                        $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                    } else
                    {
                        $order_data['shipping_code'] = '';
                    }
                } else
                {
                    $order_data['shipping_firstname'] = '';
                    $order_data['shipping_lastname'] = '';
                    $order_data['shipping_company'] = '';
                    $order_data['shipping_address_1'] = '';
                    $order_data['shipping_address_2'] = '';
                    $order_data['shipping_city'] = '';
                    $order_data['shipping_postcode'] = '';
                    $order_data['shipping_zone'] = '';
                    $order_data['shipping_zone_id'] = '';
                    $order_data['shipping_country'] = '';
                    $order_data['shipping_country_id'] = '';
                    $order_data['shipping_address_format'] = '';
                    $order_data['shipping_custom_field'] = array();
                    $order_data['shipping_method'] = '';
                    $order_data['shipping_code'] = '';
                }

                // Products
                $order_data['products'] = array();

                foreach ($this->cart->getProducts() as $product)
                {
                    $option_data = array();

                    foreach ($product['option'] as $option)
                    {
                        $option_data[] = array(
                            'product_option_id' => $option['product_option_id'],
                            'product_option_value_id' => $option['product_option_value_id'],
                            'option_id' => $option['option_id'],
                            'option_value_id' => $option['option_value_id'],
                            'name' => $option['name'],
                            'value' => $option['value'],
                            'type' => $option['type']);
                    }

                    $order_data['products'][] = array(
                        'product_id' => $product['product_id'],
                        'name' => $product['name'],
                        'model' => $product['model'],
                        'option' => $option_data,
                        'download' => $product['download'],
                        'quantity' => $product['quantity'],
                        'subtract' => $product['subtract'],
                        'price' => $product['price'],
                        'total' => $product['total'],
                        'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                        'reward' => $product['reward']);
                }

                // Gift Voucher
                $order_data['vouchers'] = array();

                if (!empty($this->session->data['vouchers']))
                {
                    foreach ($this->session->data['vouchers'] as $voucher)
                    {
                        $order_data['vouchers'][] = array(
                            'description' => $voucher['description'],
                            'code' => token(10),
                            'to_name' => $voucher['to_name'],
                            'to_email' => $voucher['to_email'],
                            'from_name' => $voucher['from_name'],
                            'from_email' => $voucher['from_email'],
                            'voucher_theme_id' => $voucher['voucher_theme_id'],
                            'message' => $voucher['message'],
                            'amount' => $voucher['amount']);
                    }
                }

                // Order Totals
                $this->load->model('extension/extension');

                $totals = array();
                $taxes = $this->cart->getTaxes();
                $total = 0;

                // Because __call can not keep var references so we put them into an array.
                $total_data = array(
                    'totals' => &$totals,
                    'taxes' => &$taxes,
                    'total' => &$total);

                $sort_order = array();

                $results = $this->model_extension_extension->getExtensions('total');

                foreach ($results as $key => $value)
                {
                    $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                }

                array_multisort($sort_order, SORT_ASC, $results);

                foreach ($results as $result)
                {
                    if ($this->config->get($result['code'] . '_status'))
                    {
                        $this->load->model('extension/total/' . $result['code']);

                        // We have to put the totals in an array so that they pass by reference.
                        $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                    }
                }

                $sort_order = array();

                foreach ($total_data['totals'] as $key => $value)
                {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $total_data['totals']);

                $order_data = array_merge($order_data, $total_data);

                if (isset($this->request->post['comment']))
                {
                    $order_data['comment'] = $this->request->post['comment'];
                } else
                {
                    $order_data['comment'] = '';
                }

                if (isset($this->request->post['affiliate_id']))
                {
                    $subtotal = $this->cart->getSubTotal();

                    // Affiliate
                    $this->load->model('affiliate/affiliate');

                    $affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->
                        post['affiliate_id']);

                    if ($affiliate_info)
                    {
                        $order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
                        $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                    } else
                    {
                        $order_data['affiliate_id'] = 0;
                        $order_data['commission'] = 0;
                    }

                    // Marketing
                    $order_data['marketing_id'] = 0;
                    $order_data['tracking'] = '';
                } else
                {
                    $order_data['affiliate_id'] = 0;
                    $order_data['commission'] = 0;
                    $order_data['marketing_id'] = 0;
                    $order_data['tracking'] = '';
                }

                $order_data['language_id'] = $this->config->get('config_language_id');
                $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
                $order_data['currency_code'] = $this->session->data['currency'];
                $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
                $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

                if (!empty($this->request->server['HTTP_X_FORWARDED_FOR']))
                {
                    $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
                } elseif (!empty($this->request->server['HTTP_CLIENT_IP']))
                {
                    $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
                } else
                {
                    $order_data['forwarded_ip'] = '';
                }

                if (isset($this->request->server['HTTP_USER_AGENT']))
                {
                    $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
                } else
                {
                    $order_data['user_agent'] = '';
                }

                if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE']))
                {
                    $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
                } else
                {
                    $order_data['accept_language'] = '';
                }

                $this->load->model('checkout/order');

                $json['order_id'] = $this->model_checkout_order->addOrder($order_data);

                // Set the order history
                if (isset($this->request->post['order_status_id']))
                {
                    $order_status_id = $this->request->post['order_status_id'];
                } else
                {
                    $order_status_id = $this->config->get('config_order_status_id');
                }

                $this->model_checkout_order->addOrderHistory($json['order_id'], $order_status_id);

        $products = $this->cart->getProducts();
        if ($products && sizeof($products)) {
          $this->facebookcommonutils = new FacebookCommonUtils();
          $this->facebookcommonutils->updateProductAvailability(
            $this->registry,
            $products);
        }
      

                // clear cart since the order has already been successfully stored.
                //$this->cart->clear();
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN']))
        {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->
                server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

    public function edit()
    {
		echo "edit "; exit();
        $this->load->language('api/order');

        $json = array();

        if (!isset($this->session->data['api_id']))
        {
            $json['error'] = $this->language->get('error_permission');
        } else
        {
            $this->load->model('checkout/order');

            if (isset($this->request->get['order_id']))
            {
                $order_id = $this->request->get['order_id'];
            } else
            {
                $order_id = 0;
            }

            $order_info = $this->model_checkout_order->getOrder($order_id);

            if ($order_info)
            {
                // Customer
                if (!isset($this->session->data['customer']))
                {
                    $json['error'] = $this->language->get('error_customer');
                }

                // Payment Address
                if (!isset($this->session->data['payment_address']))
                {
                    $json['error'] = $this->language->get('error_payment_address');
                }

                // Payment Method
                if (!$json && !empty($this->request->post['payment_method']))
                {
                    if (empty($this->session->data['payment_methods']))
                    {
                        $json['error'] = $this->language->get('error_no_payment');
                    } elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']]))
                    {
                        $json['error'] = $this->language->get('error_payment_method');
                    }

                    if (!$json)
                    {
                        $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->
                            request->post['payment_method']];
                    }
                }

                if (!isset($this->session->data['payment_method']))
                {
                    $json['error'] = $this->language->get('error_payment_method');
                }

                // Shipping
                if ($this->cart->hasShipping())
                {
                    // Shipping Address
                    if (!isset($this->session->data['shipping_address']))
                    {
                        $json['error'] = $this->language->get('error_shipping_address');
                    }

                    // Shipping Method
                    if (!$json && !empty($this->request->post['shipping_method']))
                    {
                        if (empty($this->session->data['shipping_methods']))
                        {
                            $json['error'] = $this->language->get('error_no_shipping');
                        } else
                        {
                            $shipping = explode('.', $this->request->post['shipping_method']);

                            if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]))
                            {
                                $json['error'] = $this->language->get('error_shipping_method');
                            }
                        }

                        if (!$json)
                        {
                            $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                        }
                    }

                    if (!isset($this->session->data['shipping_method']))
                    {
                        $json['error'] = $this->language->get('error_shipping_method');
                    }
                } else
                {
                    unset($this->session->data['shipping_address']);
                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['shipping_methods']);
                }

                // Cart
                if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) ||
                    (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')))
                {
                    $json['error'] = $this->language->get('error_stock');
                }

                // Validate minimum quantity requirements.
                $products = $this->cart->getProducts();

                foreach ($products as $product)
                {
                    $product_total = 0;

                    foreach ($products as $product_2)
                    {
                        if ($product_2['product_id'] == $product['product_id'])
                        {
                            $product_total += $product_2['quantity'];
                        }
                    }

                    if ($product['minimum'] > $product_total)
                    {
                        $json['error'] = sprintf($this->language->get('error_minimum'), $product['name'],
                            $product['minimum']);

                        break;
                    }
                }

                if (!$json)
                {
                    $json['success'] = $this->language->get('text_success');

                    $order_data = array();

                    // Store Details
                    $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
                    $order_data['store_id'] = $this->config->get('config_store_id');
                    $order_data['store_name'] = $this->config->get('config_name');
                    $order_data['store_url'] = $this->config->get('config_url');

                    // Customer Details
                    $order_data['customer_id'] = $this->session->data['customer']['customer_id'];
                    $order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
                    $order_data['firstname'] = $this->session->data['customer']['firstname'];
                    $order_data['lastname'] = $this->session->data['customer']['lastname'];
                    $order_data['email'] = $this->session->data['customer']['email'];
                    $order_data['telephone'] = $this->session->data['customer']['telephone'];
                    $order_data['fax'] = $this->session->data['customer']['fax'];
                    $order_data['custom_field'] = $this->session->data['customer']['custom_field'];

                    // Payment Details
                    $order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
                    $order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
                    $order_data['payment_company'] = $this->session->data['payment_address']['company'];
                    $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
                    $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
                    $order_data['payment_city'] = $this->session->data['payment_address']['city'];
                    $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
                    $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
                    $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
                    $order_data['payment_country'] = $this->session->data['payment_address']['country'];
                    $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
                    $order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
                    $order_data['payment_custom_field'] = $this->session->data['payment_address']['custom_field'];

                    if (isset($this->session->data['payment_method']['title']))
                    {
                        $order_data['payment_method'] = $this->session->data['payment_method']['title'];
                    } else
                    {
                        $order_data['payment_method'] = '';
                    }

                    if (isset($this->session->data['payment_method']['code']))
                    {
                        $order_data['payment_code'] = $this->session->data['payment_method']['code'];
                    } else
                    {
                        $order_data['payment_code'] = '';
                    }

                    // Shipping Details
                    if ($this->cart->hasShipping())
                    {
                        $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
                        $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
                        $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                        $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                        $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
                        $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                        $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                        $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                        $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                        $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                        $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                        $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
                        $order_data['shipping_custom_field'] = $this->session->data['shipping_address']['custom_field'];

                        if (isset($this->session->data['shipping_method']['title']))
                        {
                            $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                        } else
                        {
                            $order_data['shipping_method'] = '';
                        }

                        if (isset($this->session->data['shipping_method']['code']))
                        {
                            $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                        } else
                        {
                            $order_data['shipping_code'] = '';
                        }
                    } else
                    {
                        $order_data['shipping_firstname'] = '';
                        $order_data['shipping_lastname'] = '';
                        $order_data['shipping_company'] = '';
                        $order_data['shipping_address_1'] = '';
                        $order_data['shipping_address_2'] = '';
                        $order_data['shipping_city'] = '';
                        $order_data['shipping_postcode'] = '';
                        $order_data['shipping_zone'] = '';
                        $order_data['shipping_zone_id'] = '';
                        $order_data['shipping_country'] = '';
                        $order_data['shipping_country_id'] = '';
                        $order_data['shipping_address_format'] = '';
                        $order_data['shipping_custom_field'] = array();
                        $order_data['shipping_method'] = '';
                        $order_data['shipping_code'] = '';
                    }

                    // Products
                    $order_data['products'] = array();

                    foreach ($this->cart->getProducts() as $product)
                    {
                        $option_data = array();

                        foreach ($product['option'] as $option)
                        {
                            $option_data[] = array(
                                'product_option_id' => $option['product_option_id'],
                                'product_option_value_id' => $option['product_option_value_id'],
                                'option_id' => $option['option_id'],
                                'option_value_id' => $option['option_value_id'],
                                'name' => $option['name'],
                                'value' => $option['value'],
                                'type' => $option['type']);
                        }

                        $order_data['products'][] = array(
                            'product_id' => $product['product_id'],
                            'name' => $product['name'],
                            'model' => $product['model'],
                            'option' => $option_data,
                            'download' => $product['download'],
                            'quantity' => $product['quantity'],
                            'subtract' => $product['subtract'],
                            'price' => $product['price'],
                            'total' => $product['total'],
                            'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                            'reward' => $product['reward']);
                    }

                    // Gift Voucher
                    $order_data['vouchers'] = array();

                    if (!empty($this->session->data['vouchers']))
                    {
                        foreach ($this->session->data['vouchers'] as $voucher)
                        {
                            $order_data['vouchers'][] = array(
                                'description' => $voucher['description'],
                                'code' => token(10),
                                'to_name' => $voucher['to_name'],
                                'to_email' => $voucher['to_email'],
                                'from_name' => $voucher['from_name'],
                                'from_email' => $voucher['from_email'],
                                'voucher_theme_id' => $voucher['voucher_theme_id'],
                                'message' => $voucher['message'],
                                'amount' => $voucher['amount']);
                        }
                    }

                    // Order Totals
                    $this->load->model('extension/extension');

                    $totals = array();
                    $taxes = $this->cart->getTaxes();
                    $total = 0;

                    // Because __call can not keep var references so we put them into an array.
                    $total_data = array(
                        'totals' => &$totals,
                        'taxes' => &$taxes,
                        'total' => &$total);

                    $sort_order = array();

                    $results = $this->model_extension_extension->getExtensions('total');

                    foreach ($results as $key => $value)
                    {
                        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result)
                    {
                        if ($this->config->get($result['code'] . '_status'))
                        {
                            $this->load->model('extension/total/' . $result['code']);

                            // We have to put the totals in an array so that they pass by reference.
                            $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                        }
                    }

                    $sort_order = array();

                    foreach ($total_data['totals'] as $key => $value)
                    {
                        $sort_order[$key] = $value['sort_order'];
                    }

                    array_multisort($sort_order, SORT_ASC, $total_data['totals']);

                    $order_data = array_merge($order_data, $total_data);

                    if (isset($this->request->post['comment']))
                    {
                        $order_data['comment'] = $this->request->post['comment'];
                    } else
                    {
                        $order_data['comment'] = '';
                    }

                    if (isset($this->request->post['affiliate_id']))
                    {
                        $subtotal = $this->cart->getSubTotal();

                        // Affiliate
                        $this->load->model('affiliate/affiliate');

                        $affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->
                            post['affiliate_id']);

                        if ($affiliate_info)
                        {
                            $order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
                            $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                        } else
                        {
                            $order_data['affiliate_id'] = 0;
                            $order_data['commission'] = 0;
                        }
                    } else
                    {
                        $order_data['affiliate_id'] = 0;
                        $order_data['commission'] = 0;
                    }


        $products_pre_edit = $this->model_checkout_order->getOrderProductIds($order_id);
      
                    $this->model_checkout_order->editOrder($order_id, $order_data);

                    // Set the order history
                    if (isset($this->request->post['order_status_id']))
                    {
                        $order_status_id = $this->request->post['order_status_id'];
                    } else
                    {
                        $order_status_id = $this->config->get('config_order_status_id');
                    }

                    $this->model_checkout_order->addOrderHistory($order_id, $order_status_id);

        $products_post_edit = $this->cart->getProducts();
        $this->facebookcommonutils = new FacebookCommonUtils();
        $products_for_availabilty_update =
          array_merge($products_pre_edit, $products_post_edit);
        $this->facebookcommonutils->updateProductAvailability(
          $this->registry,
          $products_for_availabilty_update);
      
                }
            } else
            {
                $json['error'] = $this->language->get('error_not_found');
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN']))
        {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->
                server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function delete()
    {
		echo "delete "; exit();
        $this->load->language('api/order');

        $json = array();

        if (!isset($this->session->data['api_id']))
        {
            $json['error'] = $this->language->get('error_permission');
        } else
        {
            $this->load->model('checkout/order');

            if (isset($this->request->get['order_id']))
            {
                $order_id = $this->request->get['order_id'];
            } else
            {
                $order_id = 0;
            }

            $order_info = $this->model_checkout_order->getOrder($order_id);

            if ($order_info)
            {
                $this->model_checkout_order->deleteOrder($order_id);

                $json['success'] = $this->language->get('text_success');
            } else
            {
                $json['error'] = $this->language->get('error_not_found');
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN']))
        {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->
                server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function info()
    {
		echo "info "; exit();
        $this->load->language('api/order');

        $json = array();

        if (!isset($this->session->data['api_id']))
        {
            $json['error'] = $this->language->get('error_permission');
        } else
        {
            $this->load->model('checkout/order');

            if (isset($this->request->get['order_id']))
            {
                $order_id = $this->request->get['order_id'];
            } else
            {
                $order_id = 0;
            }

            $order_info = $this->model_checkout_order->getOrder($order_id);

            if ($order_info)
            {
                $json['order'] = $order_info;

                $json['success'] = $this->language->get('text_success');
            } else
            {
                $json['error'] = $this->language->get('error_not_found');
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN']))
        {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->
                server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getToken()
    {
        //echo __dir__ . '/settings.inc.php'; return;
		//echo "gettoken "; exit();
        $token = null;

        $this->load->model('checkout/order');

        //$this->model_checkout_order->createTableSaveToken(); //return;
        //$this->model_checkout_order->createTableSaveOrderDreamRobot(); return;

        include ('./dream_robot/settings.inc.php');

        ////////////////////�ber die Zugangsdaten kann direkt ein Accesstoken geholt werden. Dieser ist eine Stunde lang g�ltig:

        // --- 3-User --- //

        ////////////////////Getting the Accesstoken with the Client-Credentials:

        if ($this->model_checkout_order->expiredToken())
        {
            $curl_request->set_options(array('user_pwd' => $api_username . ':' . $api_password));

            $response = $curl_request->request($authorization_host . 'token.php', array('grant_type' =>
                    'client_credentials'), 'POST', $token_header);

            $token = $response['content']['access_token'];
            $create_day = date('Y-m-d h:i:sa');

            $this->model_checkout_order->insertToken($token, $create_day);
        } else
        {
            $token = $this->model_checkout_order->getToken();
        }

        return $token;
    }

    function httpPostToken($url, $headers, $data)
    {
		//echo "posttoken "; exit();
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function getTokenPrint()
    {
		$token = null;

        $this->load->model('checkout/order');

        include ('./print/settings.inc.php');

        //$this->model_checkout_order->createTableSaveTokenPrint(); //return;

        if ($this->model_checkout_order->expiredTokenPrint())
        {
            $url = 'https://oauth.cimpress.io/v2/token';

            $headers = array(
                'Content-Type: application/json',
                'Accept: application/json'
            );

            $postData = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                "grant_type"  => "client_credentials",
                "audience" => "https://api.cimpress.io/"
            );

            $response = $this->httpPostToken($url, $headers, $postData);
            $response = json_decode($response);
            
            $token = $response->access_token;
            $create_day = date('Y-m-d h:i:sa');

            $this->model_checkout_order->insertTokenPrint($token, $create_day);
        } else
        {
            $token = $this->model_checkout_order->getTokenPrint();
        }

        return $token;
    }

    public function createOrderPrint($order_id=559)
    {
        $urls = $_POST['urls'];
        $urls = json_decode($urls); 
		//print_r($urls);exit();
        $body = new \stdClass;

        $access_token = $this->getTokenPrint();
        //echo $access_token; die();

        $infor_plates = array();

        $order_info = $this->model_checkout_order->getOrder($order_id);
        //print_r($order_info); die();

        $body->deliveryAddress = new \stdClass;
        $body->deliveryAddress->country = 'DE'; //$order_info['payment_country'];
        $body->deliveryAddress->postalCode = $order_info['payment_postcode'];
        $body->deliveryAddress->city = $order_info['payment_city'];
        $body->deliveryAddress->company = $order_info['payment_company'];
        $body->deliveryAddress->firstName = $order_info['payment_firstname'];
        $body->deliveryAddress->lastName = $order_info['payment_lastname'];
        $body->deliveryAddress->phone = $order_info['telephone'];
        $body->deliveryAddress->street1 = $order_info['payment_address_1'];
        $body->deliveryAddress->email = $order_info['email'];

        $body->invoiceAddress = $body->deliveryAddress; 
        
        //print_r($order_info); die();

        $order_status = $order_info['order_status'];

        $name_customer = $order_info['firstname'] . $order_info['lastname'];

        $products = $this->model_checkout_order->getOrderProducts($order_id);
        $items = array();

        foreach ($products as $product)
        {
			$this->edit_product($product['product_id']);
            $nameUrl = 'data_' . $product["product_id"];
            $url = $urls->$nameUrl;

            $item = new \stdClass;
            $item->product = new \stdClass;

            $imgUrl = $this->request->post['upload_'.$product["product_id"].'']; //$this->model_checkout_order->getProductImage($product['product_id']);

            $order_another_information_query = $this->db->query("SELECT another_information FROM " .
                DB_PREFIX . "product WHERE product_id = '" . (int)$product['product_id'] . "'");
            $order_another_information_query = $order_another_information_query->rows;

            $another_information = json_decode($order_another_information_query[0]['another_information']);
            $numberPlates = $another_information->plates;
            $material = $another_information->material;

            $attributes = array();
            $x = 0; $y;

           foreach($numberPlates as $plate) {
                if($plate->x != 0 && $plate->y != 0) {
                    $x = $x + $plate->x;
                    $y = $plate->y;
                }
            }

            $attributes[0] = new \stdClass;

            $attributes[0]->name = 'Thickness';
            $attributes[0]->value = "3.0000";

            $attributes[1]->name = 'Format';
            $attributes[1]->value = 'Custom';

            $attributes[2]->name = 'Material';
            $attributes[2]->value = 'Aluminum Composite - White (ex: Dibond)'; //$material;

            $attributes[3]->name = 'CutShape';
            $attributes[3]->value = 'Rectangle';

            $attributes[4]->name = 'CustomWidth';
            $attributes[4]->value = ($x <= 3000) ? $x : 3000;

            $attributes[5]->name = 'CustomHeight';
            $attributes[5]->value = ($y <= 1500) ? $y : 1500;

            $attributes[6]->name = 'Crease';
            $attributes[6]->value = "0";

            $attributes[7]->name = 'PrintColor';
            $attributes[7]->value = '4/0 - CMYK';
            
            $attributes[8]->name = 'LaminationOrCoating';
            $attributes[8]->value = 'None';

            $attributes[9]->name = 'DisplaySystem';
            $attributes[9]->value = 'None';

            $attributes[10]->name = 'RigidMaterialPrintType';
            $attributes[10]->value = 'Direct To Plate';

            $attributes[11]->name = 'DrillHoles';
            $attributes[11]->value = 'No';

            $attributes[12]->name = 'DesignCount';
            $attributes[12]->value = "1";
            

            $file = new \stdClass;
            $file->url = $url;

            $item->product->productId = 'PRD-J9RKYK6LS'; //. $product['product_id'];
            //echo $item->product->productId; die();
            $item->product->quantity = $product['quantity'];
            $item->product->customName = $product['name'];
            $item->product->attributes = $attributes;

            $item->files = $file;
            
            $items[] = $item;
        }

        $body->items = $items;

        print_r(json_encode($body)); die();

        $baseUrl = 'https://staging.orders.api.erfolgreich-drucken.de';
        $url = $baseUrl . '/v1/orders';

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $access_token
        );

        $response = $this->httpPostToken($url, $headers, $body);
        $response = json_decode($response);

        //print_r($response); die();
     }

    public function generateStringFromId($product_id) {
        $number = $product_id; // Replace this with your number

        // Convert the number to a string and pad with leading zeros to make it 9 characters long
        $string = str_pad((string)$number, 9, '0', STR_PAD_LEFT);

        //echo $string; die();
        return $string; // Output the string
    }

    public function createOrderInDreamRobot($order_id)
    {
		
        $access_token = $this->getToken();

        $data['products'] = array();
        $lines = array();
        $infor_plates = array();

        $order_info = $this->model_checkout_order->getOrder($order_id);

        $order_status = $order_info['order_status'];

        $name_customer = $order_info['firstname'] . $order_info['lastname'];

        $products = $this->model_checkout_order->getOrderProducts($order_id);
		
        foreach ($products as $product)
        {
			$this->edit_product($product['product_id']);
            $order_another_information_query = $this->db->query("SELECT another_information FROM " .
                DB_PREFIX . "product WHERE product_id = '" . (int)$product['product_id'] . "'");
            $order_another_information_query = $order_another_information_query->rows;
			
            $data['products'][] = array(
                'another_information' => ($order_another_information_query[0]['another_information'] !=
                    '') ? json_decode($order_another_information_query[0]['another_information']) :
                    '',
                'name' => $product['name'],
                'model' => $product['model'],
                'quantity' => $product['quantity'],
                'price' => $product['price']);
				
				
        }

        $id_count = 0;

        foreach ($data['products'] as $item)
        {
            $another_information = $item['another_information'];
            $plates = $another_information->plates;
            $GESPIEGELT = ($another_information->mirror == 'false') ? 'GESPIEGELT: Nein' :
                'GESPIEGELT: Ja';
            $Experte = ($another_information->expert == 'false') ? 'Experte: Nein' :
                'Experte: Ja';

            $name .= ' ' . $another_information->material;

            $infor_plate = '';
            for ($i = 0; $i < count($plates); $i++)
            {
				
                if ($plates[$i]->x != 0)
                    $infor_plate .= $plates[$i]->y . '*' . $plates[$i]->x . "\n";
            }
            $infor_plates[] = $name . '\n' . "\n" . $GESPIEGELT . "\n" . $Experte . "\n" . $infor_plate;

            $lines[] = array(
                'id' => $id_count,

                'name' => $name,

                'quantity' => $item['quantity'],

                'price' => $item['price'],
                );

            $id_count++;
        }

        //print_r($lines); die();

        include ('./dream_robot/settings.inc.php');

        $order_id_dream_robot = $this->model_checkout_order->getOrderInDreamrobot($order_id);
        //echo $order_id_dream_robot; die();

        if ($order_id_dream_robot == null)
        {
            $response = $curl_request->request($rest_host . 'order/', array('portal_account_id' =>
                    $portal_account_id, 'order' => array('customer' => array('email' => '' . $order_info['email'] .
                            '', 'address' => array('name' => '' . $name_customer . '', )), 'line' => $lines)),
                'POST', $rest_header);
            //print_r($response);

            $order_id_dream_robot = $response['content']['order_id'];

            $this->model_checkout_order->insertOrderDreamrobot($order_id, $order_id_dream_robot);
        }

        $this->updateOrder($order_id, $order_status, $infor_plates, $order_id_dream_robot);
    }

    public function updateOrder($order_id_, $order_status, $infor_plates, $order_id_dream_robot)
    {
		//echo "updateOrder";exit();
        $access_token = $this->getToken();
        $order_id = $order_id_dream_robot;
        
        $order_info = $this->model_checkout_order->getOrder($order_id_);
        
        $customer_name_2 = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $customer_name = $order_info['firstname'] . ' ' . $order_info['lastname'];
        $customer_phone_1 = $order_info['telephone'];
        $customer_fax = $order_info['fax'];
        $customer_comment = $order_info['comment'];
        $customer_street_1 = $order_info['payment_address_1'];
        $customer_street_2 = $order_info['payment_address_2'];
        $customer_zip = $order_info['payment_postcode'];
        $customer_city = $order_info['payment_city'];
        $customer_province = $order_info['payment_zone'];
        
        $ship_name_2 = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $ship_name = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
        $ship_street_1 = $order_info['shipping_address_1'];
        $ship_street_2 = $order_info['shipping_address_2'];
        $ship_zip = $order_info['shipping_postcode'];
        $ship_city = $order_info['shipping_city'];
        $ship_province = $order_info['shipping_zone'];
        
        include ('./dream_robot/settings.inc.php');

        $infor = 'Order id: ' . $order_id_ . "\n";
        $infor .= 'Order status: ' . $order_status . "\n\n";

        foreach ($infor_plates as $item)
        {
            $infor .= $item . "\n";
        }


        $curl_request->request($rest_host . 'order/' . $order_id . '/', array('order' =>
                array( //Die aktuelle Notiz mit einer neuen Notiz �berschreiben.
                'infos' => $infor,
                'status' => array( //Angekommen Status setzen.
                        //Set arrived status.
                    'is_arrived' => 1),
                'shipping' => array('address' => array(
                        'name' => $ship_name,
                        'name_2' => $ship_name_2 ,
                        'street' => $ship_street_1,
                        'street_2' => $ship_street_2,
                        'zip' => $ship_zip,
                        'city' => $ship_city,
                        'province' => $ship_province,
                        )),
                'customer' => array(
                    'phone_1' => $customer_phone_1,
                    'fax' => $customer_fax,
                    'comment' => $customer_comment,
                    'address' => array(
                        'name' => $customer_name,
                        'name_2' => $customer_name_2,
                        'street' => $customer_street_1,
                        'street_2' => $customer_street_2,
                        'zip' => $customer_zip,
                        'city' => $customer_city,
                        'province' => $customer_province,
                        ))), ), 'POST', $rest_header);
    }
	
	public function edit_product($product_id=1673) {
		$urls = $_POST['urls'];
        $urls = json_decode($urls);
		//print_r($urls);
		//echo $urls->data_url_hire0_1673;
		$order_another_information_query = $this->db->query("SELECT another_information FROM " .
                DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$order_another_information_query = $order_another_information_query->rows;
		$another_information=($order_another_information_query[0]['another_information'] !=
                    '') ? json_decode($order_another_information_query[0]['another_information']) :
                    '';
		//print_r($_POST'');exit();
		$plates=$another_information->plates;
		$i =0;
		foreach($plates as & $obj) {
			$url_hire="data_url_hire".$i."_".$product_id;
			$obj->file = $urls->$url_hire;
			$i++;
		}
		
		//print_r($another_information);exit();
		
		
		//$json = array();
		

		$this->db->query("UPDATE " . DB_PREFIX . "product SET another_information = '" . json_encode($another_information) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		$json['success'] = 'success';

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

    public function history()
    {
		
        $status_order = $this->request->post['order_status_id'];
		
        $this->load->language('api/order');

        $json = array();

        if (!isset($this->session->data['api_id']))
        {
            $json['error'] = $this->language->get('error_permission');
        } else
        {
            // Add keys for missing post vars
            $keys = array(
                'order_status_id',
                'notify',
                'override',
                'comment');

            foreach ($keys as $key)
            {
                if (!isset($this->request->post[$key]))
                {
                    $this->request->post[$key] = '';
                }
            }
			
            $this->load->model('checkout/order');

            if (isset($this->request->get['order_id']))
            {
                $order_id = $this->request->get['order_id'];
            } else
            {
                $order_id = 0;
            }

            $order_info = $this->model_checkout_order->getOrder($order_id);

            if ($order_info)
            {
                $this->model_checkout_order->addOrderHistory($order_id, $this->request->post['order_status_id'],
                    $this->request->post['comment'], $this->request->post['notify'], $this->request->
                    post['override']);

                $json['success'] = $this->language->get('text_success');
            } else
            {
                $json['error'] = $this->language->get('error_not_found');
            }
        }
		
        if (isset($this->request->server['HTTP_ORIGIN']))
        {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->
                server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        //echo 'nad'; die();
        $this->createOrderInDreamRobot($order_id);

        if($status_order == 29)
          $this->createOrderPrint($order_id);
          
        //print_r($json); exit();
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
