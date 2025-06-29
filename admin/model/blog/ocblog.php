<?php
class ModelBlogOcblog extends Model 
{
    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "article` (
			    `article_id` INT(11) NOT NULL AUTO_INCREMENT,
	            `sort_order` INT(11) NOT NULL DEFAULT '0',
	            `status` TINYINT(1) NOT NULL DEFAULT '0',
	            `date_added` DATETIME NOT NULL,
	            `date_modified` DATETIME NOT NULL,
	        PRIMARY KEY (`article_id`)
		) DEFAULT COLLATE=utf8_general_ci;");
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "article_description` (
			    `article_id` INT(11) NOT NULL,
                `language_id` INT(11) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `intro_text` TEXT NOT NULL,
                `meta_title` VARCHAR(255) NOT NULL,
                `meta_description` VARCHAR(255) NOT NULL,
                `meta_keyword` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`article_id`, `language_id`),
	        INDEX `name` (`name`)
		) DEFAULT COLLATE=utf8_general_ci;");
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "article_list` (
			    `article_list_id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `status` TINYINT(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`article_list_id`),
	        INDEX `name` (`name`)
		) DEFAULT COLLATE=utf8_general_ci;");
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "article_to_list` (
			    `article_list_id` INT(11) NOT NULL,
                `article_id` INT(11) NOT NULL
		) DEFAULT COLLATE=utf8_general_ci;");
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "article_to_store` (
			    `article_id` INT(11) NOT NULL,
                `store_id` INT(11) NOT NULL
		) DEFAULT COLLATE=utf8_general_ci;");
        $this->updateImageToTable();
        $this->updateAuthorToTable();
    }
	public function updateImageToTable() {
		$check_sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "article` LIKE 'image'";
        $query = $this->db->query($check_sql);
        if($query->rows) {
            return;
        } else {
            $sql = "ALTER TABLE `" . DB_PREFIX . "article` ADD `image` varchar(255) DEFAULT NULL";
            $this->db->query($sql);
            return;
        }
	}
	
	public function updateAuthorToTable() {
		$check_sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "article` LIKE 'author'";
        $query = $this->db->query($check_sql);
        if($query->rows) {
            return;
        } else {
            $sql = "ALTER TABLE `" . DB_PREFIX . "article` ADD `author` varchar(100) DEFAULT NULL";
            $this->db->query($sql);
            return;
        }
	}
	
    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "article`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "article_description`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "article_list`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "article_to_list`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "article_to_store`");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'ocblog'");
    }
}