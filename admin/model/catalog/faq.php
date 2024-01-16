<?php
class ModelCatalogFaq extends Model {
	public function addFaq($data) {
	    
	    $this->db->query("INSERT INTO " . DB_PREFIX . "letscms_faq SET image = '" . $this->db->escape($data['image']) . "', `status` = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

		$faq_id = $this->db->getLastId();
	    
	    foreach($data['faq_description'] as $language_id => $value){
		    $this->db->query("INSERT INTO " . DB_PREFIX . "letscms_faq_description SET faq_id='".(int)$faq_id."',language_id='".$language_id."', question = '" . $this->db->escape($value['question']) . "', answer = '" . $this->db->escape($value['answer']). "'");
	    }
	
		return true;
	}

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
    			);
    		}
    
    		return $faq_description_data;
    }
    
   
    
	public function editFaq($faq_id, $data) {
	    
		$this->db->query("UPDATE " . DB_PREFIX . "letscms_faq SET image = '" . $this->db->escape($data['image']) . "',status='".(int)$data['status']."', date_modified = NOW() WHERE faq_id = '" . (int)$faq_id . "'");
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "letscms_faq_description WHERE faq_id = '" . (int)$faq_id . "'");

		foreach ($data['faq_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "letscms_faq_description SET faq_id = '" . (int)$faq_id . "', language_id = '" . (int)$language_id . "', question = '" . $this->db->escape($value['question']) . "', answer = '" . $this->db->escape($value['answer']) . "'");
		}
		
	}


	
	public function getTotalFaqs() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "letscms_faq");

		return $query->row['total'];
	}

    public function getFaqs() {
        $data_faqs=array();
        $sql="SELECT * FROM " . DB_PREFIX . "letscms_faq f LEFT JOIN ".DB_PREFIX."letscms_faq_description fd on f.faq_id=fd.faq_id where fd.language_id='".(int)$this->config->get('config_language_id')."'";
        
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

    public function deleteFaq($faq_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "letscms_faq WHERE faq_id = '" . (int)$faq_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "letscms_faq_description WHERE faq_id = '" . (int)$faq_id . "'");
	}

}

