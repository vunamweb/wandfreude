<?php
class ModelExtensiongaenhprochkbeg extends Controller
{
    private $langid = 0;
    private $storeid = 0;
    private $storename = '';
    private $custgrpid = 0;
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->langid = (int) $this->config->get('config_language_id');
        $this->storeid = (int) $this->config->get('config_store_id');
        $this->storename = $this->config->get('config_meta_title');
        $this->custgrpid = (int) $this->config->get('config_customer_group_id');
        ini_set("serialize_precision", -1);
    }
    public function getcode()
    {
		$this->load->model('extension/gaenhpro');
		$this->load->model('checkout/order');
        $rsdata = $this->model_extension_gaenhpro->getdata();
        //print_r($this->cart); die();

        if ($rsdata && $rsdata['status'] == 1 && $rsdata['gaid'] && $this->cart->hasProducts()) {
            $langdata = $this->model_extension_gaenhpro->getlangdata($rsdata);

            $items_data = array();
            $counter = 0;
            foreach ($this->cart->getProducts() as $pinfo) {
                $counter += 1;
                $pricetx = $this->tax->calculate($pinfo['total'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
                $items = array(
                    "id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
                    "name" => $pinfo['name'],
                    "price" => $this->model_extension_gaenhpro->getcurval($pricetx),
                    "quantity" => $pinfo['minimum'],
                    "brand" => $this->model_extension_gaenhpro->getbrandname($pinfo['product_id']),
                    "category" => $this->model_extension_gaenhpro->getcatname($pinfo['product_id']),
                    "brand" => $this->model_extension_gaenhpro->getbrandname($pinfo['product_id']),
                    "coupon" => '',
                );
                if (isset($this->session->data['coupon'])) {
                    $items['coupon'] = $this->session->data['coupon'];
                }
                $items_data[] = $items;
            }
            $begin_checkout = array(
                //"currency" => $this->session->data['currency'],
                //"value" => $this->cart->getTotal(),
                "products" => $items_data,
            );
            $begin_checkout['event_category'] = 'ecommerce';
            $begin_checkout['event_label'] = 'begin_checkout';

			$total = $this->cart->getTotal();
			
            $tax = $this->model_checkout_order->getTax() / 100 + 1;
            $valueTax = $total- ($total / $tax);
            $valueTax = number_format($valueTax, 2, ',', ' ');

            if (isset($this->session->data['coupon'])) {
                $begin_checkout['coupon'] = $this->session->data['coupon'];
            }

            $json_begin_checkout = json_encode($begin_checkout);
/*$code = <<<EOF
<script type="text/javascript">
gtag('event', 'begin_checkout', $json_begin_checkout);
</script>
EOF;*/

            $code = <<<EOF
<script type="text/javascript">
window.dataLayer = window.dataLayer || [];

dataLayer.push({ ecommerce: null })

dataLayer.push({
	'event': 'eec.checkout',
	'ecommerce': {
		'purchase': {
			'actionField': {
				'id': '',                        // Transaction ID. Required for purchases and refunds.
				'affiliation': $this->storename,
				'revenue': $total,                 // Total transaction value (incl. tax and shipping)
				'tax': $valueTax, 
				'shipping': '0',
				'coupon': ''
			  },
			  $json_begin_checkout
		}
	}
})
</script>
EOF;

            return $code;

        }
    }
}
