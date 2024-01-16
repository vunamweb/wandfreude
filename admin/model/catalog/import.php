<?php
class ModelCatalogImport extends Model {
	public function getMaterial(){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "material where language_id = 2");
        foreach ($query->rows as $result) {
	       $results[] = $result;
        }
        //print_r($results);
        return $results; 
    }
    
    public function addPriceMaterial($id_material, $qm, $price ) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "price_material SET id_material = " . $id_material . ", qm = '" . $qm. "', price = ".$price."");
   }
   
   public function deletePriceMaterial($id_material) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "price_material WHERE id_material = " . $id_material . "");
   }
}

