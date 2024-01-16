<?php 
class ModelExtensiongaenhprochksuccess extends Controller { 
   	private $langid = 0;
	private $storeid = 0;
	private $storename = '';
	private $custgrpid = 0;
	public function __construct($registry) {
		parent::__construct($registry);
		$this->langid = (int)$this->config->get('config_language_id');
		$this->storeid = (int)$this->config->get('config_store_id');
		$this->storename = $this->config->get('config_meta_title');
		$this->custgrpid = (int)$this->config->get('config_customer_group_id');
		ini_set("serialize_precision", -1);
 	}
	public function getcode($order_id = 0) {
		$this->load->model('extension/gaenhpro');
		$this->load->model('checkout/order');

		$rsdata = $this->model_extension_gaenhpro->getdata();
		//print_r($rsdata); die();
		if(!$order_id && isset($this->session->data['gaenhpro_order_id'])) {
			$order_id = $this->session->data['gaenhpro_order_id'];
			//echo $order_id . '  dd'; die();
		}
		if($rsdata && $order_id) {
			$storequery = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . $this->storeid . "'");
 			$this->storename = isset($storequery->row['name']) ? $storequery->row['name'] : $this->config->get('config_name');
			//$langdata = $this->model_extension_gaenhpro->getangdata($rsdata);
			
			$status = $rsdata['status'] ? $rsdata['status'] : false;
			$gaid = $rsdata['gaid'] ? $rsdata['gaid'] : false;
			$gafid = $rsdata['gafid'] ? $rsdata['gafid'] : false;
			$adwstatus = $rsdata['adwstatus'] ? $rsdata['adwstatus'] : false;
			$adwid = $rsdata['adwid'] ? $rsdata['adwid'] : false;
			$adwlbl = $rsdata['adwlbl'] ? $rsdata['adwlbl'] : false;
			 
 			$this->load->model('checkout/order');
			
			$orderdata = $this->model_checkout_order->getOrder($order_id);
			$order_products = $this->model_extension_gaenhpro->getorderproduct($order_id); 		
 			$order_tax = $this->model_extension_gaenhpro->getordertax($order_id);
			$order_shipping = $this->model_extension_gaenhpro->getordershipping($order_id);
			
			$items_data = array();
			$purchase = array();
 			$counter = 0; 			
			foreach ($order_products as $pinfo) {
				$counter += 1;
				$pricetx = $pinfo['price'] + $pinfo['tax'];
				$items = array(
					"id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					"name" => $pinfo['name'],
					"price" => $this->model_extension_gaenhpro->getcurval($pricetx),
					"quantity" => $pinfo['quantity'],
					"category" => $this->model_extension_gaenhpro->getcatname($pinfo['product_id']),
					"brand" => $this->model_extension_gaenhpro->getbrandname($pinfo['product_id'])
				);
				if (isset($this->session->data['coupon'])) {
					$items['coupon'] = $this->session->data['coupon'];
				} else {
					$items['coupon'] = '';
				}
				$items_data[] = $items;
			}
		
			$purchase = array(
				"products" => $items_data,
			);
			if (isset($this->session->data['coupon'])) {
				$purchase['coupon'] = $this->session->data['coupon'];
			} 			
			
$adw_currency = $this->session->data['currency'];
$adw_order_id = $orderdata['order_id'];
$adw_total = round($orderdata['total'],2);
$json_purchase = json_encode($purchase);
$coupon = ($this->session->data['coupon'] != "") ? $this->session->data['coupon'] : '""';

$tax = $this->model_checkout_order->getTax()/100 + 1;
$valueTax = $adw_total - ($adw_total/$tax);
$valueTax = number_format($valueTax, 2, '.', ' ');

$items_data = json_encode($items_data);

$code1 = '';
if(true) {
//if($status) {
$code1 = <<<EOF
<script type="text/javascript">
window.dataLayer = window.dataLayer || [];

dataLayer.push({ ecommerce: null })

dataLayer.push({
	'event': 'eec.purchase',
	'ecommerce': {
		'purchase': {
			'actionField': {
				'id': "$adw_order_id" ,                        // Transaction ID. Required for purchases and refunds.
				'affiliation': "$this->storename",
				'revenue': $adw_total ,              // Total transaction value (incl. tax and shipping)
				'tax': $valueTax,
				'shipping': $order_shipping,
				'coupon': $coupon
			  },
			  "products": $items_data
		}
	}
})
</script>
EOF;
}

/*$code2 = '';
if($adwstatus) {
$code2 = <<<EOF
<script type="text/javascript">
gtag('event', 'conversion', {'send_to': '$adwid/$adwlbl', 'transaction_id': '$adw_order_id', 'value': $adw_total, 'currency': '$adw_currency' });
</script>
EOF;
}*/

return $code1;
//return $code1 . $code2;
		
 		} 
	} 
}