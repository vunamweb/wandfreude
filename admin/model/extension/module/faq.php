<?php

class ModelExtensionModuleFaq extends Model {

	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "letscms_faq` (
			  `faq_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `image` VARCHAR(255),
			  `status` INT(11),
			  `date_added` DATETIME NOT NULL,
			  `date_modified` DATETIME NOT NULL,
			  PRIMARY KEY (`faq_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
    
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "letscms_faq_description` (
			  `faq_id` INT(11),
			  `language_id` INT(11),
			  `question` VARCHAR(255),
			  `answer` text
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

		
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "letscms_faq;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "letscms_faq_description;");
	}
}
