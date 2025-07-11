<?php 
class ModelExtensionModuleRandomproduct extends Model
{
	public function getFirstOrderCategory() {
	   $sql = "select category_id from " . DB_PREFIX . "category where category_type = 1 order by sort_order asc";
       $query = $this->db->query($sql);
       $query = $query->rows;
       return $query[0]['category_id'];
    }
    
    public function getRandomProducts($limit) {
		$category_id = (isset($_GET['category']) && $_GET['category'] != '' ) ? $_GET['category'] : $_GET['path'] ;
        $category_id = ($category_id != '') ? $category_id : $this->getFirstOrderCategory();
        //echo $category_id ;die();
        
        $sql = "SELECT *, p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id and p.type_product = 1) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'"; 
		
        $sql .= " GROUP BY p.product_id";
		
		$sql .= " ORDER BY p.sort_order";
		
		//$sql .= " LIMIT " . (int) $limit;
		
		
        //echo $sql; die();
        $product_data = array();
        $product_data_result = array();	
		$query = $this->db->query($sql);
        
        foreach ($query->rows as $result) {
			if($category_id != 0) {
    			 if($this->getCategory($result['product_id'],$category_id) != ''){
                  $product_data_result[$result['product_id']] = $this->getProduct($result['product_id']);
                  $product_data_result[$result['product_id']]['category_id'] = $this->getCategory($result['product_id'],$category_id);
				  $product_data_result[$result['product_id']]['category_name'] = $this->getNameCategoryFromId($category_id);
                } 
			} else {
			   $product_data_result[$result['product_id']] = $this->getProduct($result['product_id']);
               $product_data_result[$result['product_id']]['category_id'] = $this->getCategoryHomePage($result['product_id']);
			   $product_data_result[$result['product_id']]['category_name'] = $this->getNameCategoryFromId($product_data_result[$result['product_id']]['category_id']);
            }
        }
		//echo count($product_data_result); die();
		return $product_data_result;
	}
    
   public function getCategory($product_id, $category_id){
	   $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category p where p.category_id = ".$category_id." AND p.product_id = ".$product_id."");
       
       $productList = $query->rows;
       
       if(count($productList) > 0)
         return $category_id;
       else
         return '';  
	}
	
	public function getNameCategoryFromId($category_id){
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description where category_id = ".$category_id."");
		
		$category = $query->rows;
		//print_r($category); die();

		return $category[0]['name'];
	}
    
    public function getCategoryHomePage($product_id){
	   $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category p where p.product_id = ".$product_id."");
       
       $productList = $query->rows;
       //print_r($productList);die();
       
       if(count($productList) > 0)
         return $productList[0]['category_id'];
       else
         return '';  
    }
    
    public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}

}