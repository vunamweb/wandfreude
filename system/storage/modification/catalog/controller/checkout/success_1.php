<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');
           
           if (isset($this->session->data['order_id'])) {
			//echo $this->session->data['order_id']; die();
            $this->cart->clear();

			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');

				if ($this->customer->isLogged()) {
					$activity_data = array(
						'customer_id' => $this->customer->getId(),
						'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
						'order_id'    => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_account', $activity_data);
				} else {
					$activity_data = array(
						'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
						'order_id' => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_guest', $activity_data);
				}
			}

			/*unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);*/
		}

		$collectorders = array();				
				
			$collectorders['order_id'] = $this->session->data['order_id'];
				
			if ($this->customer->isLogged()) {
				$this->load->model('account/customer');
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
				$collectorders['email'] = $customer_info['email'];
			} else {
				$collectorders['email'] = $this->session->data['guest']['email'];
			}
			
				
			$collectorders['total'] = $this->session->data['total_new'];
			//$collectorders['total'] =  $this->session->data['order_total'];
			//$collectorders['currency_code'] = $this->currency->getCode();
			$collectorders['currency_code'] = $this->session->data['currency'];
			$collectorders['payment_method'] = $this->session->data['payment_method']['code'];
			

			
			$collectorders['order_products'] = array();
			$this->load->model('tool/image');
			$this->load->model('catalog/product');
			foreach ($this->cart->getProducts() as $product) {
			
				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				}
				
				

				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				$collectorders['order_products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'image'      => $image,
					'model'      => $product['model'],
					'sku'      	 => $product_info['sku'],
					'ean'      	 => $product_info['ean'],
					'mpn'      	 => $product_info['mpn'],
					'brand'      => $product_info['manufacturer'],
					'quantity'   => $product['quantity'],
					'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				);
			}
			//echo '<pre>'.print_r($collectorders, true).'</pre>';
			$this->session->data['trustedshop_collectorders'] = $collectorders;

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}