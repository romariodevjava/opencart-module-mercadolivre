<?php


class ModelExtensionModuleMercadolivre extends Model
{
    private $key_prefix = 'module_mercadolivre';

    public function criarTabelas()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_products` (
			  `mercadolivre_products_id` int(11) NOT NULL AUTO_INCREMENT,
			  `ml_product_code` varchar(15) NOT NULL,
			  `condition` varchar(15) NOT NULL,
			  `product_id` int(11) NOT NULL,
			  `listing_type_id` varchar(15) NOT NULL,
			  `mercadolivre_category_id` varchar(15) NOT NULL,
			  `status` varchar(15) NOT NULL,
			  `created_at` datetime NOT NULL,
			  `updated_at` datetime NULL,
			  PRIMARY KEY (`mercadolivre_products_id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_variations` (
			  `mercadolivre_variation_id` int(11) NOT NULL AUTO_INCREMENT,
			  `ml_variation_code` int(11) NOT NULL,
			  `option_value_id` int(11) NOT NULL,
			  `created_at` datetime NOT NULL,
			  PRIMARY KEY (`mercadolivre_variation_id`),
			  FOREIGN KEY (option_value_id) REFERENCES " . DB_PREFIX . "option_value (option_value_id)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_categories` (
			  `mercadolivre_category_id` int(11) NOT NULL AUTO_INCREMENT,
			  `category_id` int(11) NOT NULL,
			  `mercadolivre_category_code` varchar(15) NOT NULL,
			  `created_at` datetime NOT NULL,
			  PRIMARY KEY (`mercadolivre_category_id`),
			  FOREIGN KEY (category_id) REFERENCES " . DB_PREFIX . "oc_category(category_id)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_questions` (
			  `mercadolivre_question_id` int(11) NOT NULL AUTO_INCREMENT,
			  `question_id` varchar(15) NOT NULL,
			  `question` varchar(350) NOT NULL,
			  `answer` varchar(350) NULL,
			  `created_at` datetime NOT NULL,
			  `answered_at` datetime NULL,
			  PRIMARY KEY (`mercadolivre_question_id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_orders` (
			  `mercadolivre_order_id` int(11) NOT NULL AUTO_INCREMENT,
			  `ml_id` int(11) NOT NULL,
			  `date_created` datetime NOT NULL,
			  `expiration_date` datetime NOT NULL,
			  `total` decimal(16, 2) NOT NULL,
			  `buyer` varchar(100) NOT NULL,
			  `buyer_document_type` varchar(10) NOT NULL,
			  `buyer_document_number` varchar(15) NOT NULL,
			  `status` varchar(20) NOT NULL,
			  `sale_fee` decimal(16, 2) NOT NULL,
			  `listing_type_id` varchar(15) NOT NULL,
			  `created_at` datetime NOT NULL,
			  PRIMARY KEY (`mercadolivre_order_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_orders_products` (
			  `mercadolivre_orders_product_id` int(11) NOT NULL AUTO_INCREMENT,
			  `mercadolivre_order_id` int(11) NOT NULL,
			  `name` varchar(100) NOT NULL,
			  `variation` TEXT null,
			  PRIMARY KEY (`mercadolivre_orders_product_id`),
			  FOREIGN KEY (mercadolivre_order_id) REFERENCES " . DB_PREFIX . "mercadolivre_orders (mercadolivre_order_id)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_notifications` (
			  `mercadolivre_notification_id` int(11) NOT NULL AUTO_INCREMENT,
			  `resource` varchar(200) NOT NULL,
			  `topic` varchar(20) NOT NULL,
			  `application_id` bigint NOT NULL,
			  `created_at` datetime NOT NULL,
			  PRIMARY KEY (`mercadolivre_notification_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
    }

    public function createCron()
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "cron` SET `code` = 'mercadolivre', `cycle` = 'hour', `action` = 'extension/module/mercadolivre/cron', `status` = '1', `date_added` = NOW()");
    }

    public function removeCron()
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "cron` WHERE `code` = 'mercadolivre'");
    }

    public function removerTabelas()
    {
        $this->db->query("DROP TABLE `" . DB_PREFIX . "mercadolivre_notifications`");
        $this->db->query("DROP TABLE `" . DB_PREFIX . "mercadolivre_orders_products`");
        $this->db->query("DROP TABLE `" . DB_PREFIX . "mercadolivre_orders`");
        $this->db->query("DROP TABLE `" . DB_PREFIX . "mercadolivre_questions`");
        $this->db->query("DROP TABLE `" . DB_PREFIX . "mercadolivre_categories`");
        $this->db->query("DROP TABLE `" . DB_PREFIX . "mercadolivre_variations`");
        $this->db->query("DROP TABLE `" . DB_PREFIX . "mercadolivre_products`");
    }

    public function getCategories($parent_id = 0) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN `" . DB_PREFIX . "category_to_store` c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
        return $query->rows;
    }

    public function getProduct($product_id) {
        $result = $this->db->query('SELECT * FROM `' . DB_PREFIX . "mercadolivre_products` WHERE `product_id` = '" . ($product_id) ."'");

        return $result->rows;
    }
}