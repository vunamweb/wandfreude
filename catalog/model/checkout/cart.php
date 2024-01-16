<?php
class ModelCheckoutCart extends Model {
   public function deleteCart($cart_id) {
		$sql = "SELECT product_id FROM " . DB_PREFIX . "cart where cart_id=".$cart_id."";
        
        $query = $this->db->query($sql);
        
        foreach ($query->rows as $result) {
			$product_id = $result['product_id'];
		}
        
        $sql = "SELECT type_product FROM " . DB_PREFIX . "product where product_id=".$product_id."";
        
        $query = $this->db->query($sql);
        
        foreach ($query->rows as $result) {
			$type_product = $result['type_product'];
		}
        
        if($type_product == 2){
            $this->db->query("DELETE FROM `" . DB_PREFIX . "product` WHERE product_id = " . $product_id . "");
            $this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE product_id = " . $product_id . "");
            $this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_store` WHERE product_id = " . $product_id . "");
        }
    }
}