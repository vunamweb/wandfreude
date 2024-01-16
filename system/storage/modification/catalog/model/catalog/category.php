<?php
class ModelCatalogCategory extends Model
{
    public function getCategory($category_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int) $category_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND c.status = '1'");

        return $query->row;
    }

    public function getFirstOrderCategory()
    {
        $sql = "select category_id from " . DB_PREFIX . "category where category_type = 1 order by sort_order asc";
        $query = $this->db->query($sql);
        $query = $query->rows;
        return $query[0]['category_id'];
    }

    public function getCategoriesOfProduct($product_id)
    {
		$sql = "select pc.category_id, name from " . DB_PREFIX . "product_to_category pc, " . DB_PREFIX . "category_description cd where cd.category_id = pc.category_id and pc.product_id = " . $product_id . "";

		/*if($product_id == 5189) {
			$query = $this->db->query($sql);

			$categories = array();
	
			foreach ($query->rows as $result) {
				$categories[] = $result['category_id'];
			}

			print_r($categories); die();
		}*/
		 
		$query = $this->db->query($sql);

        $categories = array();

        foreach ($query->rows as $result) {
            $categories[] = $result['category_id'] . ',' . $result['name'];
        }

        return $categories;
    }

    public function getFirstProductOfCategory($category_id)
    {
        $sql = "select pc.product_id from " . DB_PREFIX . "product_to_category pc, " . DB_PREFIX . "product p where p.product_id = pc.product_id and pc.category_id = " . $category_id . " order by p.sort_order asc";
        $query = $this->db->query($sql);
        $query = $query->rows;
        return $query[0]['product_id'];
    }

    public function getSizeStandardOfCategory($category_id)
    {
        $sql = "select * from " . DB_PREFIX . "category where category_id = " . $category_id . "";
        $query = $this->db->query($sql);
        $query = $query->rows;

        $sizeStandard = array();
        $sizeStandard['width'] = $query[0]['width_standard'];
        $sizeStandard['height'] = $query[0]['height_standard'];
        return $sizeStandard;
    }

    public function getCategories($parent_id = 0)
    {
        $category_type = (isset($_GET[category_type]) && $_GET[category_type] != '') ? $_GET[category_type] : 0;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id and c.category_type=" . $category_type . ") LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

        return $query->rows;
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


    public function getCategoriesPlate($parent_id = 0)
    {
        $category_type = (isset($_GET[category_type]) && $_GET[category_type] != '') ? $_GET[category_type] : 1;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id and c.category_type=" . $category_type . ") LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

        //print_r($query->rows); die();
        $categories = $query->rows;

        $count = 0;

        foreach ($categories as $category) {
            $query = $this->db->query("SELECT p.product_id, pd.name FROM " . DB_PREFIX . "product_description pd, " . DB_PREFIX . "product_to_category pc, " . DB_PREFIX . "product p where p.product_id = pd.product_id and p.product_id = pc.product_id and pc.category_id = " . $category[category_id] . " order by p.sort_order");

            $productList = $query->rows;

            $categories[$count]['name_url'] = $this->convertNameUrl($categories[$count]['name']);

            if($categories[$count]['category_id'] == 83) {
                //print_r($name); die();
            }
             
            if (count($productList) > 0) {
                $categories[$count]['product_id'] = $productList[0]['product_id'];
                $categories[$count]['name_product_id'] = $this->convertNameUrl($productList[0]['name']);
            }

            $categories[$count]['count'] = count($productList);

            $count++;
        }

        return $categories;
    }

    public function getCategoryFilters($category_id)
    {
        $implode = array();

        $query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int) $category_id . "'");

        foreach ($query->rows as $result) {
            $implode[] = (int) $result['filter_id'];
        }

        $filter_group_data = array();

        if ($implode) {
            $filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int) $this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

            foreach ($filter_group_query->rows as $filter_group) {
                $filter_data = array();

                $filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int) $filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

                foreach ($filter_query->rows as $filter) {
                    $filter_data[] = array(
                        'filter_id' => $filter['filter_id'],
                        'name' => $filter['name'],
                    );
                }

                if ($filter_data) {
                    $filter_group_data[] = array(
                        'filter_group_id' => $filter_group['filter_group_id'],
                        'name' => $filter_group['name'],
                        'filter' => $filter_data,
                    );
                }
            }
        }

        return $filter_group_data;
    }

    public function getCategoryLayoutId($category_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int) $category_id . "' AND store_id = '" . (int) $this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return $query->row['layout_id'];
        } else {
            return 0;
        }
    }

    public function getTotalCategoriesByCategoryId($parent_id = 0)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND c.status = '1'");

        return $query->row['total'];
    }
}
