<?php


class ModelExtensionModuleMercadolivre extends Model
{
    const CATEGORY_DEFAULT = 'MLB3530';

    /**
     * @var Meli
     */
    private static $mlSdk;
    private $key_prefix = 'module_mercadolivre';
    private $log;

    /**
     * ModelExtensionModuleMercadolivre constructor.
     * @param $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->log = new Log('mercadolivre.log');
    }

    public function getCategoriesByProductId($product_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = '" . (int)$product_id . "'");

        return $query->rows;
    }

    public function getProduct($product_id)
    {
        $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, 
                (SELECT price FROM `" . DB_PREFIX . "product_discount` pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, 
                (SELECT price FROM `" . DB_PREFIX . "product_special` ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special,  
                (SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "review` r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, 
                p.sort_order FROM `" . DB_PREFIX . "product` p 
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) 
                LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p.product_id = p2s.product_id) 
                LEFT JOIN `" . DB_PREFIX . "manufacturer` m ON (p.manufacturer_id = m.manufacturer_id) 
                WHERE p.product_id = '" . (int)$product_id . "' 
                AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                AND p.status = '1' AND p.date_available <= NOW() 
                AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return array(
                'product_id' => $query->row['product_id'],
                'name' => $query->row['name'],
                'description' => $query->row['description'],
                'tag' => $query->row['tag'],
                'model' => $query->row['model'],
                'sku' => $query->row['sku'],
                'ncm' => $query->row['ncm'],
                'cest' => $query->row['cest'],
                'upc' => $query->row['upc'],
                'ean' => $query->row['ean'],
                'jan' => $query->row['jan'],
                'isbn' => $query->row['isbn'],
                'mpn' => $query->row['mpn'],
                'quantity' => $query->row['quantity'],
                'shipping' => $query->row['shipping'],
                'image' => $query->row['image'],
                'manufacturer_id' => $query->row['manufacturer_id'],
                'manufacturer' => $query->row['manufacturer'],
                'price' => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
                'special' => $query->row['special'],
                'date_available' => $query->row['date_available'],
                'weight' => $query->row['weight'],
                'length' => $query->row['length'],
                'width' => $query->row['width'],
                'height' => $query->row['height'],
                'status' => $query->row['status']
            );
        } else {
            return false;
        }
    }

    public function criarTabelas()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mercadolivre_products` (
			  `mercadolivre_products_id` int(11) NOT NULL AUTO_INCREMENT,
			  `ml_product_code` varchar(15) NOT NULL,
			  `condition` varchar(15) NOT NULL,
			  `product_id` int(11) NOT NULL,
			  `listing_type_id` varchar(15) NOT NULL,
			  `subtract_product` tinyint(1) NULL,
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
			  `mercadolivre_products_id` int(11) NOT NULL,
			  `created_at` datetime NOT NULL,
			  PRIMARY KEY (`mercadolivre_variation_id`),
			  FOREIGN KEY (option_value_id) REFERENCES " . DB_PREFIX . "option_value (option_value_id),
			  FOREIGN KEY (mercadolivre_products_id) REFERENCES " . DB_PREFIX . "mercadolivre_products (mercadolivre_products_id)
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

    public function revokeAuthentication() {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `store_id` = '0' AND `key` = 'module_mercadolivre_authentication_code'");
    }

    public function getCategories($parent_id = 0)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN `" . DB_PREFIX . "category_to_store` c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
        return $query->rows;
    }

    public function getProductML($product_id)
    {
        $result = $this->db->query('SELECT * FROM `' . DB_PREFIX . "mercadolivre_products` WHERE `product_id` = '" . (int)$product_id . "'");

        return $result->rows;
    }

    public function createProductInMl($product)
    {
        $request = $this->createRequestForCreateAds($product);
        $this->prepareForRequest();
        $response = self::$mlSdk->post('items', $request);
        $this->treatResponse($response, 'items', $request);

        $this->db->query('INSERT INTO `' . DB_PREFIX . "mercadolivre_products` SET `ml_product_code` = '" . $this->db->escape($response['body']->id) ."', `condition` = '" .
            $this->db->escape($response['body']->condition) ."', `product_id` = '" . $product['product_id'] . "', `listing_type_id` = '" . $this->db->escape($response['body']->listing_type_id) .
            "', `mercadolivre_category_id` = '" . $this->db->escape($response['body']->category_id) . "', `status` = '" . $response['body']->status . "', `subtract_product` = '" . (int) $product['subtract_product'] . "', `created_at` = NOW()");

        $product_ml_id = $this->db->getLastId();
        foreach ($response['body']->variations as $variation) {
            foreach ($variation['attributes'] as $attribute) {
                if ($attribute['id'] == 'SELLER_SKU') {
                    $key = array_search($attribute['value_name'], array_column($product['variations'], 'sku'));
                    $this->db->query('INSERT INTO `' . DB_PREFIX . "mercadolivre_variations` SET `mercadolivre_products_id` = '" . $product_ml_id . "' `ml_variation_code` = '" . (int) $variation['id'] . "', `option_value_id` = '" . $product['variations'][$key]['sku'] . "', `created_at` = NOW()");
                }
            }
        }
    }

    private function createRequestForCreateAds($product)
    {
        $request = [
            'listing_type_id' => $product['listing_type'],
            'title' => $this->getTitle($product),
            'currency_id' => $this->config->get('module_mercadolivre_currency'),
            'available_quantity' => $product['quantity'],
            'buying_mode' => $this->config->get('module_mercadolivre_buying_mode'),
            'condition' => $this->config->get('module_mercadolivre_condition'),
            'shipping' => [
                'mode' => $this->config->get('module_mercadolivre_shipping_type') ?? 'me2',
                'local_pick_up' => $this->config->get('module_mercadolivre_shipping_with_local_pick_up') == 1,
            ],
            'description' => [
                'plain_text' => $this->getDescription($product)
            ],
            'attributes' => [
                [
                    'id' => 'BRAND',
                    'value_name' => $product['manufacturer']
                ],
                [
                    'id' => 'EAN',
                    'value_name' => $product['ean']
                ],
                [
                    'id' => 'PACKAGE_HEIGHT',
                    'value_name' => intval($product['height']) . ' cm'
                ],
                [
                    'id' => 'PACKAGE_WIDTH',
                    'value_name' => intval($product['width']) . ' cm'
                ],
                [
                    'id' => 'PACKAGE_LENGTH',
                    'value_name' => intval($product['length']) . ' cm'
                ],
                [
                    'id' => 'PACKAGE_WEIGHT',
                    'value_name' => floatval($product['weight']) . ' kg'
                ]
            ]
        ];

        if ($this->config->get('module_mercadolivre_shipping_free')) {
            $request['shipping'][] = [
                'free_methods' => [
                    [
                        'id' => 100009,
                        'rule' => ['free_mode' => 'country']
                    ]
                ]
            ];
        }

        $request['price'] = $this->getPrice($product['price'], $product['special'], $product['price_adjustment']);
        $this->treatWarranty($product['product_warranty_type'], $product['warranty_unit'], $product['warranty'], $request);
        $this->treatCategory($product['category_id'], $request);
        $this->mapVariations($product['variations'], $request);
        $this->mapImagens($product['image'], $product['images'], $request);

        return $request;
    }

    private function getTitle($product)
    {
        $title = $product['name'];
        if (!empty($this->config->get('module_mercadolivre_template_title'))) {
            $find = array('__TITLE__', '__MODEL__', '__SKU__', '__BRAND__', '__ISBN__', '__MPN__');
            $replace = array($product['name'], $product['model'], $product['sku'], $product['manufacturer'], $product['isbn'], $product['mpn']);

            $title = str_replace($find, $replace, $this->config->get('module_mercadolivre_template_title'));
        }

        return substr($title, 0, 59);
    }

    private function getDescription($product)
    {
        $description = $product['description'];
        if (!empty($this->config->get('module_mercadolivre_template_description'))) {
            $find = array('__TITLE__', '__DESCRIPTION__', '__MODEL__', '__SKU__', '__BRAND__', '__ISBN__', '__MPN__');
            $replace = array($product['name'], $product['description'], $product['model'], $product['sku'], $product['manufacturer'], $product['isbn'], $product['mpn']);

            $description = str_replace($find, $replace, $this->config->get('module_mercadolivre_template_description'));
        }
        $description = strip_tags(html_entity_decode($description), '<br><p><li>');
        $description = preg_replace("/<p\s(.+?)>(.+?)<\/p>/is", "$2\n\n", $description);
        $description = preg_replace("/<p>(.+?)<\/p>/is", "$1\n\n", $description);
        $description = preg_replace("/<br>/is", "\n\n", $description);
        $description = preg_replace("/<li\s(.+?)>(.+?)<\/li>/is", "$2\n", $description);

        return $description;
    }

    private function getPrice($normal, $special, $price_adjustment)
    {
        $price = (float)$this->config->get('module_mercadolivre_consider_special_price') && $special ? $special : $normal;
        $rules = explode(';', $price_adjustment);

        foreach ($rules as $rule) {
            $steps = explode(':', $rule);
            $newPrice = $price;

            if (count($steps) > 1) {
                $newPrice = $this->calculePriceWithCondition($price, $steps);
            } else {
                $newPrice = $this->calculePrice($price, $steps[0]);
            }

            if ($newPrice != $price) return number_format($newPrice, 2, '.', '');
        }

        return number_format($price, 2, '.', '');
    }

    private function calculePriceWithCondition($price, $steps)
    {
        $value = (float)$steps[0];

        if ((strpos($steps[0], '<') && $price < $value) || (strpos($steps[0], '>') && $price > $value) ||
            (strpos($steps[0], '<=') && $price <= $value) || (strpos($steps[0], '>=') && $price >= $value)) {
            return $this->calculePrice($price, $steps[1]);
        }

        return $price;
    }

    private function calculePrice($price, $operationWithValue)
    {
        preg_match('/(\d%|\d)(?:\s*)(?:\+)(?:\s*)(\d%+|\d+)/i', $operationWithValue, $output_array);
        if (empty($output_array)) {
            $output_array = [1 => $operationWithValue];
        }

        $newPrice = $price;

        foreach ($output_array as $key => $item) {
            if ($key == 0) continue;

            if (strpos($item, '%') !== false) {
                $percent = (float)$item;
                $newPrice *= (1 + ($percent / 100));
            } else {
                $newPrice += (float)$item;
            }
        }

        return $newPrice;
    }

    private function treatWarranty($warranty_type, $warranty_unit, $warranty, &$request)
    {
        if (!empty($warranty_type)) {
            $request['sale_terms'] = [
                ['id' => 'WARRANTY_TYPE', 'value_name' => $warranty_type],
                ['id' => 'WARRANTY_TIME', 'value_name' => $warranty . ' ' . $warranty_unit]
            ];
        }
    }

    private function treatCategory($category_id, &$request)
    {
        if (empty($category_id)) {
            if ($this->config->get('module_mercadolivre_auto_detect_category')) {
                $country = $this->config->get('module_mercadolivre_country');
                $ch = curl_init('https://api.mercadolibre.com/sites/' . $country . '/domain_discovery/search?limit=1&q=' . urlencode($request['title']));
                curl_setopt_array($ch, Meli::$CURL_OPTS);

                $response = json_decode(curl_exec($ch));
                if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 && !empty($response)) {
                    $category_id = $response[0]->category_id;
                }

                curl_close($ch);
            } else {
                $category_id = self::CATEGORY_DEFAULT;
            }
        }

        $request['category_id'] = $category_id;
    }

    private function mapVariations($variations, &$request)
    {
        $request['variations'] = array();

        foreach ($variations as $variation) {
            $request['variations'][] = [
                'attribute_combinations' => [
                    'name' => $variation['name'],
                    'value_name' => $variation['value_name']
                ],
                'price' => $variation['price'],
                'available_quantity' => $variation['quantity'],
                'attributes' => [
                    [
                        'id' => 'SELLER_SKU',
                        'name' => 'SKU',
                        'value_name' => $variation['sku']
                    ]
                ]
            ];
        }
    }

    private function mapImagens($image, $images, &$request)
    {
        $urlMain = HTTPS_CATALOG . 'image/';
        $request['pictures'] = array();
        if ($image) {
            $request['pictures'][] = ['source' => $urlMain . $image];
        }

        foreach ($images as $image_1) {
            $request['pictures'][] = ['source' => $urlMain . $image_1['image']];
        }

        if (!empty($this->config->get('module_mercadolivre_template_image_aditional'))) {
            $request['pictures'][] = ['source' => $urlMain . $this->config->get('module_mercadolivre_template_image_aditional')];
        }
    }

    private function prepareForRequest()
    {
        $this->load->model('setting/setting');
        $configs = $this->model_setting_setting->getSetting($this->key_prefix);

        $accessToken = $configs['module_mercadolivre_access_token'] ?? null;
        $refreshToken = $configs['module_mercadolivre_refresh_token'] ?? null;
        $expire_at = $configs['module_mercadolivre_expire_at'] ?? null;

        if (self::$mlSdk == null) {
            self::$mlSdk = new Meli($this->config->get('module_mercadolivre_app_id'), $this->config->get('module_mercadolivre_app_secret'), $accessToken, $refreshToken);
        }

        if ($refreshToken == null) {
            $redirect_uri = sprintf($this->language->get('authentication_url'), HTTPS_CATALOG);
            $response = self::$mlSdk->authorize($this->config->get('module_mercadolivre_authentication_code'), $redirect_uri);
            $this->treatResponse($response, '/oauth/token');
            $expire_at = time() + ($response["body"]->expires_in - 10);

            $this->registerOauthAttributes($response["body"]->access_token, $response["body"]->refresh_token, $expire_at);
        }

        if ($expire_at <= time()) {
            $response = self::$mlSdk->refreshAccessToken();
            $this->treatResponse($response, '/oauth/token');
            $this->registerOauthAttributes($response["body"]->access_token, $response["body"]->refresh_token, $expire_at);
        }
    }

    private function registerOauthAttributes($accessToken, $refreshToken, $expireAt)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix . "' AND `key` = 'module_mercadolivre_access_token'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix . "' AND `key` = 'module_mercadolivre_refresh_token'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix . "' AND `key` = 'module_mercadolivre_expire_at'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix . "', `key` = 'module_mercadolivre_access_token', `value` = '" . $this->db->escape($accessToken) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix . "', `key` = 'module_mercadolivre_refresh_token', `value` = '" . $this->db->escape($refreshToken) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix . "', `key` = 'module_mercadolivre_expire_at', `value` = '" . $this->db->escape($expireAt) . "'");
    }

    public function getCategorieMl($category_id)
    {
        $result = $this->db->query('SELECT * FROM `' . DB_PREFIX . "mercadolivre_categories` WHERE `category_id` = '" . ($category_id) . "'");

        return $result->rows;
    }

    public function editCategory($category_id, $category_code_ml)
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "mercadolivre_categories`");
        $this->db->query('INSERT INTO `' . DB_PREFIX . "mercadolivre_categories` SET `category_id` = '" . ($category_id) . "', `mercadolivre_category_code` = '" . $category_code_ml . "', `created_at`= NOW()");
    }

    public function treatResponse($response, $path, $request = array()) {
        if ($response['httpCode'] >= 400 || (isset($response['body']->status) && $response['body']->status >= 400)) {
            $this->log->write('PATH: ' . $path);
            $this->log->write('REQUEST: ' . json_encode($request));
            $this->log->write('RESPONSE: ' . json_encode($response));

            throw new Exception($response['body']->message);
        }
    }
}