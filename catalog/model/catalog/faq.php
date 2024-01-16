<?php
class ModelCatalogFaq extends Model {
	
    public function getfaq($faq_id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "letscms_faq where faq_id='".(int)$faq_id."'");
        return  $query->row;
    }

    public function getfaqDescription($faq_id){
        $faq_description_data = array();
    
    		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "letscms_faq_description WHERE faq_id = '" . (int)$faq_id . "'");
    
    		foreach ($query->rows as $result) {
    			$faq_description_data[$result['language_id']] = array(
    				'question'             => $result['question'],
    				'answer'       => $result['answer'],
    				'image'       => $result['image'],
    			);
    		}
    
    		return $faq_description_data;
    }
    
   
    
	
	public function getTotalFaqs() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "letscms_faq");

		return $query->row['total'];
	}

    public function getFaqs($data) {
        $data_faqs=array();
        $sql="SELECT * FROM " . DB_PREFIX . "letscms_faq f LEFT JOIN ".DB_PREFIX."letscms_faq_description fd on f.faq_id=fd.faq_id where fd.language_id='".(int)$this->config->get('config_language_id')."' LIMIT ". $data['start'] .' , '. $data['limit'];
        
		$query = $this->db->query($sql);
		
		foreach($query->rows as $faq){
		    $data_faqs[]=array(
		        'faq_id'=> $faq['faq_id'],
		        'image'=> $faq['image'],
		        'question'=> $faq['question'],
		        'answer'=> $faq['answer'],
		        );
		}
		
		return $data_faqs;
	}
}