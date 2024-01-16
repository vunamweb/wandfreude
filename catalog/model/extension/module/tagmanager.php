<?php
/******************************************************
 * @package GTM Enhanced Ecommerce
 * @version 1.0
 * @author Ahsan Shakeel
 * @link https://www.flux.pk
 * @copyright Copyright (C) 2018. Flux (Pvt) Ltd. All rights reserved.
 * @email:info@flux.pk 
 * $date: 19 Dec 2018
*******************************************************/

class ModelExtensionModuleTagmanager extends Model {
	
	public function getTagmanger() {
		
		$tagmanager = array();
		
		$cid = (isset($_COOKIE['_ga']) ? $_COOKIE['_ga'] : '');
		
		$tagmanager = array (
			'code' 	           => $this->config->get('module_tagmanager_code'),
			'gid' 	           => $this->config->get('module_tagmanager_gid'),
			'mp' 	           => $this->config->get('module_tagmanager_mp'),
			'status'           => $this->config->get('module_tagmanager_status'),
			'admin'            => $this->config->get('module_tagmanager_admin'),
			'adword'           => $this->config->get('module_tagmanager_adword'),
			'userid_status'    => $this->config->get('module_tagmanager_userid_status'),
			'userid'		   => (isset($this->session->data['userid']) ? $this->session->data['userid'] : ''),
			'conversion_id'    => $this->config->get('module_tagmanager_conversion_id'),
			'conversion_label' => $this->config->get('module_tagmanager_conversion_label'),
			'remarketing'      => $this->config->get('module_tagmanager_remarketing'),
			'pmap'			   => $this->config->get('module_tagmanager_product'),
			'ptitle'		   => $this->config->get('tagmanager_ptitle'),
			'cid'			   => preg_replace('/GA[0-9]+\.[0-9]+\./', '', $cid),
			'language'		   => (isset($_COOKIE['language']) ? $_COOKIE['language'] : ''),
			'vs'			   => base64_encode($this->config->get('module_tagmanager_code').$this->config->get('module_tagmanager_gid').$cid),
			'host'				=> $_SERVER['SERVER_NAME'],
			'currency'         => $this->session->data['currency']
			);
		return $tagmanager;
		
	}
	
	 public function tagmangerPmap($model='',$sku='',$product_id='') {
		$pmap = $this->config->get('module_tagmanager_product');
			$curr = $this->config->get('config_currency');
			
			$supported_currencies = array('GBP', 'USD', 'EUR', 'AUD', 'BRL', 'CZK', 'JPY', 'CHF', 'CAD', 'DKK', 'INR', 'MXN', 'NOK', 'PLN', 'RUB', 'SEK', 'TRY');
				
			if (!in_array($curr, $supported_currencies)) {
					$curr = 'GBP';
			}
			
			if($curr == 'GBP'){
					$currency = 'gb';
			}elseif($curr == 'USD'){
					$currency = 'us';
			}elseif($curr == 'AUD'){
					$currency = 'au';
			}elseif($curr == 'CAD'){
					$currency = 'ca';
			}elseif($curr == 'CHF'){
					$currency = 'ch';
			}elseif($curr == 'MXN'){
					$currency = 'mx';
			}elseif($curr == 'INR'){
					$currency = 'in';
			}
					
	   
		if ($pmap == 'product_id') {
		  $pid = $product_id;      
		} elseif ($pmap == 'model') {
		  $pid = $model;
		} elseif ($pmap == 'sku') {
		  $pid = $sku;
		} elseif ($pmap == 'model_product_id') {
		  $pid = $model . '_' . $product_id;
		} elseif ($pmap == 'product_id_currency') {
		  $pid = $product_id . '_' . $currency;
		} elseif ($pmap == 'product_id_language') {
		  $pid = $product_id . '_' . $this->config->get('config_language');      
		} else {
		  $pid = $product_id;
		}
		return $pid;
	}
	  
	
	public function tagmangerPtitle($name='', $brand='',$model='',$product_id='') {
		$ptitle = $this->config->get('module_tagmanager_ptitle');
			   
		if ($ptitle == 'name') {
		  $ptitle = $name;      
		} elseif ($ptitle == 'brand_model') {
		  $ptitle = $brand . ' ' . $model;
		} else {
		  $ptitle = $name;     
		}
		$ptitle = $this->cleanStr($ptitle);
		return htmlspecialchars($ptitle, ENT_QUOTES);
	}
	
	
	public function getProductCatNameEXT($product_id) {
		$query = $this->db->query("SELECT (SELECT DISTINCT GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' > ') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE c1.category_id = pc.category_id) AS category FROM " . DB_PREFIX . "category_description cd INNER JOIN " . DB_PREFIX . "product_to_category pc ON pc.category_id = cd.category_id INNER JOIN " . DB_PREFIX . "product p ON pc.product_id = p.product_id  WHERE 1 AND p.product_id = '".$product_id."' GROUP BY p.product_id");
		return (isset($query->row['category']) && $query->row['category']) ? $query->row['category'] : '';
	}

	public function getProductBrandName($product_id) {
		$query = $this->db->query("SELECT m.name from " . DB_PREFIX . "manufacturer m left join " . DB_PREFIX . "product p on m.manufacturer_id = p.manufacturer_id  WHERE 1 AND p.product_id = ".$product_id);
		if (isset($query->row['name'])) {
			$brand = $query->row['name'];
		} else {
			$brand = '';
		}
		$brand = $this->cleanStr($brand);
		return $brand;
	}

	public function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}

	public function getProductCatName($product_id) {
		
		$return_data = array();
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' ORDER BY category_id DESC LIMIT 1 ");
			
			if($query->num_rows == 1){
				$return_data = $this->getparent($query->row['category_id']);
				$return_data = array_reverse($return_data);
			}
		  $cat = '';
		  $i=1;
			foreach ($return_data as $result) {
				if ($i>1) {
					$cat .= ' > ';
				}
				$cat .= $result['name'] ;
				$i++;
			} 
			$cat = $this->cleanStr($cat);
			return $cat;
		
	}

	public function getparent($cid) {
			$data = array();
			$temp  = $this->db->query("SELECT c.category_id, cd1.name AS name, c.parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (c.category_id = cd1.category_id)  WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.category_id = '".(int)$cid."'");
			
			if($temp->num_rows == 1) {
				$data[] = $temp->row;
				
				if($temp->row['parent_id'] != 0) {
					$data = array_merge($data,  $this->getparent($temp->row['parent_id']));
				}
			} else {
				return $data;
			}
			return $data;
	}
	
	public function cleanStr($data) {
		$data = str_replace('"', "", $data);
		$data = str_replace("'", "", $data);
		$data = str_replace("&#039;", "", $data);
		return $data;
	}

	public function getCartProducts() {
		$products = $this->cart->getProducts();
		$this->load->model('catalog/product');
		$data = array();

		$data['ec_shipping_total'] = isset($this->session->data['shipping_method']['cost']) ? $this->session->data['shipping_method']['cost'] : 0;
		$data['ec_coupon'] = isset($this->session->data['coupon']) ? $this->session->data['coupon'] : false;

		$data['ecom_prodid'] = array();
		$data['ecom_pagetype']='checkout';
		$data['ecom_totalvalue'] =0;

		foreach ($products as $product) {
		
			$option_data = array();
			
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') { $value = $option['value']; } else { $value = ''; }
				$option_data[] = array( 'name'  => $option['name'], 'value' => (utf8_strlen($value) > 50 ? utf8_substr($value, 0, 50) . '..' : $value) );
			} 
					
			$pid = $this->tagmangerPmap($product['model'],$product['sku'],$product['product_id']);
			$brand = $this->getProductBrandName($product['product_id']);
			$cat = $this->getProductCatName($product['product_id']);
			$title = $this->tagmangerPtitle($product['name'], $brand, $product['model'],$product['product_id']);
			$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
			$total_price = $unit_price * $product['quantity'];
			$total_price = $this->currency->format($total_price, $this->session->data['currency'],'',false);
						
			$data['ecom_prodid'][] = $product['product_id'];
			$data['ecom_totalvalue'] += number_format((float)$total_price, 2, '.', '') ;

			$data['ec_cartproducts'][] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'pid'        => $pid,
				'title'      => $title,
				'brand' 	   => $brand,
				'category'   => $cat,
				'option'     => $option_data,
				'quantity'   => $product['quantity'],
				'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'],'',false)
			);

		}
		return $data;
	}
	
	public function getOrder($order_id) {

		$this->load->model('checkout/order');
		
		$data['ec_language'] = $this->config->get('config_language');
		$data['ec_orderCoupon'] =  $this->getOrderCoupon($order_id);
		$data['ec_orderDetails'] = $this->model_checkout_order->getOrder($order_id);
		$data['ec_orderProducts'] = $this->getOrderProducts($order_id, $data['ec_orderDetails']);
		$data['ec_orderDetails']['coupon'] =  $this->getOrderCoupon($order_id);
    	$data['ec_currency'] = $this->session->data['currency']; 
		
		if ($data['ec_currency'] != $this->config->get('config_currency')) {
			$data['ec_orderShipping'] = $this->getOrderShipping($order_id) * $data['orderDetails']['currency_value'];
            $data['ec_orderValue'] = $data['ec_orderDetails']['total'] * $data['ec_orderDetails']['currency_value'];
            $data['ec_orderValue'] = number_format((float)$data['ec_orderValue'], 2, '.', '');
            $data['ec_orderTax'] = $this->getOrderTax($order_id) * $data['ec_orderDetails']['currency_value'];

		} else {
				
			$data['ec_orderShipping'] = $this->getOrderShipping($order_id);
            $data['ec_orderValue'] = number_format((float)$data['ec_orderDetails']['total'], 2, '.', '');
            $data['ec_orderTax'] = $this->getOrderTax($order_id);
		}

		$data['ec_orderTax'] = number_format($data['ec_orderTax'], 2, '.', '');
		$data['ec_orderShipping'] = number_format((float)$data['ec_orderShipping'], 2, '.', '');
		$data['ec_affiliate_code'] = '';

		if ($data['ec_orderDetails']['affiliate_id']) {
			$this->load->model('affiliate/affiliate');
			$affiliate_id = $data['ec_orderDetails']['affiliate_id'];
			$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($affiliate_id);
			$data['ec_affiliate_code'] = $affiliate_info['code'];
		}

		 $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "analytics_tracking WHERE order_id = '" . (int)$order_id . "'" );

		 $data['hit'] = 0;

		 if ($query->num_rows) {
			$data['hit'] = $query->row['hit'];
		 } else {
		 	$data['hit'] = 0;
		 }

		return $data;

	}

	public function prepareOrder($order_id) {

		$data = $this->getOrder($order_id);

		$fbpixel_prodid = array();
		$result = array();
		$orderProducts = array();

		foreach ($data['ec_orderProducts'] as $product) {
			$optext = '';
			foreach ($product['option'] as $op) {
				$optext .= $op['name'];
			}
			$optext = utf8_substr($optext, 0, 499);

			$orderProducts[] = array(
						'id'	   => $product['pid'],
						'name'     => $product['title'],
						'category' => $product['category'],
						'brand'    => $product['brand'],
						'variant'  => $optext,
						'quantity' => $product['quantity'],
						'price'    => $product['price'],
						'currency' => $data['ec_currency']
			);
		}
		$actionField = array(
			'id'			=> $data['ec_orderDetails']['order_id'],
			'affiliation'	=> (isset($data['ec_affiliate_code'])? $data['ec_affiliate_code'] : ''),
			'revenue'		=> $data['ec_orderValue'], 
			'tax'			=> $data['ec_orderTax'],
			'shipping'		=> $data['ec_orderShipping'],
			'coupon'		=> (isset($data['ec_orderCoupon'])? $data['ec_orderCoupon'] : ''),
			'currency'		=> $data['ec_currency']
		);
		
		$purchase = array (
			'actionField'	=> $actionField,
			'products'		=> $orderProducts
		);

		$ecommerce = array (
			'purchase' =>	$purchase
		);


		$result = array(
			'event'		=>		'ecommerceComplete',
			'ecommerce'	=>		$ecommerce
		);

            	
		if(isset($data['ec_orderProducts'])) {
			foreach ($data['ec_orderProducts'] as $product) {
				$fbpixel_prodid[] = array(
						  'id' 	   => $product['pid'],
						  'quantity' => $product['quantity'],
						  'item_price' => number_format($product['price'], 2, '.', '')
				);
			}
		}

		
		$ecdata = array(
			'fbpixel'	=> $fbpixel_prodid,
			'gadata'	=> $result,
			'currency'	=> $data['ec_currency'],
			'revenue'	=> $data['ec_orderValue'],
			'tax'		=> $data['ec_orderTax'],
			'shipping'	=> $data['ec_orderShipping'],
			'order_id'		=> $data['ec_orderDetails']['order_id'],
			'coupon'	=> (isset($data['ec_orderCoupon'])? $data['ec_orderCoupon'] : ''),
			'affiliation'	=> (isset($data['ec_affiliate_code'])? $data['ec_affiliate_code'] : ''),
			'hit'		=> $data['hit']
		);

		return $ecdata;
	}
	
	 public function getOrderProducts($order_id,$order_info) {
				$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		
				$products = array();
	
				foreach ($order_product_query->rows as $product) {
					$product_id = $product['product_id'];
					$option_data = array();
					$options = $this->getOrderOptions($order_id, $product['order_product_id']); 
					foreach ($options as $option) {
						$option_data[] = array(
							'name'  => $option['name'] . " " . (utf8_strlen($option['value']) > 100 ? utf8_substr($option['value'], 0, 100) . '..' : $option['value'])
							);
					}
	
					$brand = $this->getProductBrandName($product['product_id']);
					$cat = $this->getProductCatName($product['product_id']);
					
					// conversion to selected currency

     				$price = $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value'],false);
					$total = $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'],false);

					// NO Conversion stick with default currency

					//$price = $product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0);
					//$total = $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0);

					$pid = $this->tagmangerPmap($product['model'],$product['model'],$product['product_id']);
					
					$title = $this->tagmangerPtitle($product['name'], $brand, $product['model'],$product['product_id']);
					
					
					$products[] = array(
						'name'     => $product['name'],
						'title'    => $title,
						'model'    => $product['model'],
						'pid'      => $pid,
						'category' => (isset($cat) ? $cat : ''),
						'brand'    => (isset($brand) ? $brand : ''),
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => number_format((float)$price, 2, '.', ''),
						'total'    => number_format((float)$total, 2, '.', '')
					);
				}
		
		return $products;
		
	}    
  
  	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
	
	public function getOrderTax($order_id) {
				$tax_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'tax'");
				$order_tax = '0.00';
				if ($tax_query->num_rows) {
					$order_tax = $tax_query->row['value'];
				} 
				return $order_tax;

	}
	
	public function getOrderShipping($order_id) {
				$shipping_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'shipping'");
				$order_shipping = '0.00';
				if ($shipping_query->num_rows) {
					$order_shipping = $shipping_query->row['value'];
				} 
				return $order_shipping;

	}

	public function getOrderCoupon($order_id) {
				$coupon_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'coupon'");
				$order_coupon = '';
				if ($coupon_query->num_rows) {
					$order_coupon = $coupon_query->row['title'];
				} 
				return $order_coupon;

	}
  
	
	
	public function GAorderAdd($order_id, $data) {
		$cid = '';
		$tagmanager = $this->getTagmanger();
            $this->db->query("INSERT INTO `" . DB_PREFIX . "analytics_tracking` SET 
              order_id = '" . (int)$order_id . "',
              cid = '" . $this->db->escape($tagmanager['cid']) . "',
			  currency_code = '" . $this->db->escape($data['currency_code']) . "',
			  currency_id = '" . $this->db->escape($data['currency_id']) . "',
			  uid = '" . $this->db->escape($tagmanager['userid']) . "',
			  ul = '" . $this->db->escape($tagmanager['language']) . "',
			  ip = '" . $this->db->escape($data['ip']) . "',
			  user_agent = '" . $this->db->escape($data['user_agent']) . "',
			  tid = '" . $this->db->escape($tagmanager['gid']) . "'"
			  );
	}

 
	public function GAorder($order_id) {
		  
		  $this->load->model('checkout/order');
		  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "analytics_tracking WHERE order_id = '" . (int)$order_id . "' AND hit = '0'" );
		  $data = array();
		
		  $tagmanager = $this->getTagmanger();

		  if (!isset($tagmanager['gid']) || $tagmanager['mp'] == '0') {
			return 'error analytics id or mp not set';
		  }
		
		  if ($query->num_rows) {
			$data['cid'] = $query->row['cid'];
			$data['currency_code'] = $query->row['currency_code'];
			$data['ip'] = $query->row['ip'];
			$data['user_agent'] = $query->row['user_agent'];
		  } else {
		   return 'error order not found or already hit';
		 }

		 $result = 	$this->getOrder($order_id);

		 $data = array_merge($data, $result);
		 
		 echo '<pre>';		print_r($data);

			$para  = '';
			$para .= "v=1";
			$para .= "&tid=" . $tagmanager['gid'] ;
			$para .= "&cid=" . $data['cid'];
			$para .= "&t=event&ec=Purchase&ea=sale";
			$para .= "&dh=" . $tagmanager['host'];
			$para .= "&dp=checkout/success";
			$para .= "&dt=Order%20Complete"; 
			$para .= "&ti=" . $order_id;
			$para .= "&ta=";
			$para .= "&tr=" . $data['ec_orderValue'];
			$para .= "&tt=" . $data['ec_orderTax'];
			$para .= "&ts=" . $data['ec_orderShipping'] ;
			$para .= (!empty($data['ec_orderCoupon']) ? "&tc=" . $data['ec_orderCoupon'] :'') ;
			$para .= "&aip=1&ds=web&uip=" . $data['ip'];
			$para .= "&pa=purchase";
		
		 $i = 1;
		
		 foreach ($data['ec_orderProducts'] as $product) {
		   $product['category'] = str_replace(">", "/", $product['category']);
		   $product['category'] = str_replace("&", "and", $product['category']);
		   $product['category'] = str_replace("amp;", "", $product['category']);

		   $para .= "&pr" . $i . "id=" . $product['pid'] . "&pr" . $i . "nm=" . $product['title'] . "&pr" . $i . "ca=" . $product['category'] . "&pr" . $i . "br=" . $product['brand'];
		   $para .= "&pr" . $i . "qt=" . $product['quantity'] . "&pr" . $i . "pr=" . $product['price'] ; 
		   if (isset($product['option'])) {
				$optext = '';
				foreach ($product['option'] as $op) {
					$optext .= $op['name'];
				}
				$para .= "&pr" . $i . "va=" . utf8_substr($optext, 0, 499);
		   }
			
		   $i++;
		 }

		 parse_str($para, $orderdata);
		
		 $response = $this->GApost($orderdata);

		 $this->GAupdateorder($order_id);
		 
		 return 'success';

	  }

	  public function GArefund($order_id) {

			$tagmanager = $this->getTagmanger();

			$para  = '';
			$para .= "v=1";
			$para .= "&tid=" . $tagmanager['gid'] ;
			$para .= "&cid=" . $tagmanager['cid'];
			$para .= "&t=event";
			$para .= "&ec=ecommerce";
			$para .= "&ea=refund";
			$para .= "&ni=1";
			$para .= "&dp=admin/sale/refund";
			$para .= "&dt=Refund"; 
			$para .= "&pa=refund"; 
			$para .= "&ti=" . $order_id;
			
			parse_str($para, $orderdata);
			
			$response = $this->GApost($orderdata);

			return $response;

	  }


	  public function GApost($data, $debug=false) {

		if (!isset($data)) {
			return;
		}

	  if (!$debug) {
		$curl = curl_init('https://www.google-analytics.com/collect');  
	  } else {
		$curl = curl_init('https://www.google-analytics.com/debug/collect');
	  }
		
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl);

		curl_close($curl);
		
		$json = json_decode($response,true);

		if ($debug) {
			echo '<pre>';
			print_r($response);
			print_r($data);
			echo '</pre>';
		}

		return $json;

	  }

	  public function GAupdateorder($order_id) {

		  if (isset($order_id) && !empty($order_id)) { 
			 $this->db->query("UPDATE `" . DB_PREFIX . "analytics_tracking` SET hit = '1' WHERE order_id = '" . (int)$order_id . "'");
		  }
		 return;

	  }
}

?>