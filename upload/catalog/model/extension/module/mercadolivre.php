<?php

class ModelExtensionModuleMercadolivre extends Model
{
    /**
     * @var Meli
     */
    private static $mlSdk;

    private $route = 'extension/module/mercadolivre';
    private $key_prefix = 'module_mercadolivre';
    private $key_prefix_oauth = 'module_mercadolivre_oauth';

    public function updateMLCode($code)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix_oauth . "' AND `key` = 'module_mercadolivre_oauth_authentication_code'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix_oauth . "', `key` = 'module_mercadolivre_oauth_authentication_code', `value` = '" . $this->db->escape($code) . "'");
    }

    public function treatNotifications($resource, $topic, $application_id)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mercadolivre_notifications SET `resource` = '" . $this->db->escape($resource) . "', `topic` = '" . $this->db->escape($topic) . "', `application_id` = '" . $this->db->escape($application_id) . "', `created_at` = NOW()");
        if ($topic === 'questions') {
            $this->treatNotificationForQuestions($resource);
        } else if ($topic === 'orders_v2') {
            $this->treatNotificationForOrders($resource);
        }
    }

    private function treatNotificationForQuestions($resource)
    {
        $this->prepareForRequest();
        $response = self::$mlSdk->get($resource);
        $this->treatResponse($response, $resource);

        $product = $this->getProductML($response['body']->item_id);
        $result = $this->db->query('SELECT COUNT(*) AS total FROM `' . DB_PREFIX . "mercadolivre_questions` WHERE `question_id` = '" . (int)$response['body']->id . "' AND `mercadolivre_products_id` = '" . $product['mercadolivre_products_id'] . "'");

        if ($result->row['total'] > 0) {
            $this->db->query('UPDATE `' . DB_PREFIX . "mercadolivre_questions` SET `answer` = '" . $this->db->escape($response['body']->answer->text) . "', `answered_at` = NOW() WHERE `question_id` = '" . (int)$response['body']->id . "' AND `mercadolivre_products_id` = '" . $product['mercadolivre_products_id'] . "'");
        } else if (!empty($product)) {
            $query = "INSERT INTO `" . DB_PREFIX . "mercadolivre_questions` SET `mercadolivre_products_id` = '" . $product['mercadolivre_products_id'] . "', `question_id` = '" . (int)$response['body']->id . "', `question` = '" . $this->db->escape($response['body']->text) . "', `created_at` = NOW()";
            if (!empty($response['body']->answer)) {
                $query .= ", `answer` = '" . $this->db->escape($response['body']->answer->text) . "', `answered_at` = NOW()";
            }

            $this->db->query($query);
        }
    }

    private function prepareForRequest()
    {
        $this->load->model('setting/setting');
        $configs = $this->model_setting_setting->getSetting($this->key_prefix_oauth);

        $accessToken = $configs[$this->key_prefix_oauth . '_access_token'] ?? null;
        $refreshToken = $configs[$this->key_prefix_oauth . '_refresh_token'] ?? null;
        $expire_at = $configs[$this->key_prefix_oauth . '_expire_at'] ?? null;

        if (self::$mlSdk == null) {
            self::$mlSdk = new Meli($this->config->get('module_mercadolivre_app_id'), $this->config->get('module_mercadolivre_app_secret'), $accessToken, $refreshToken);
        }

        if ($refreshToken == null) {
            $redirect_uri = sprintf($this->language->get('authentication_url'), HTTPS_SERVER);
            $response = self::$mlSdk->authorize($this->config->get('module_mercadolivre_oauth_authentication_code'), $redirect_uri);
            $this->treatResponse($response, '/oauth/token');
            $expire_at = time() + ($response["body"]->expires_in - 10);

            $this->registerOauthAttributes($response["body"]->access_token, $response["body"]->refresh_token, $expire_at);
        }

        if ($expire_at <= time()) {
            $response = self::$mlSdk->refreshAccessToken();
            $this->treatResponse($response, '/oauth/token');
            $expire_at = time() + ($response["body"]->expires_in - 10);

            $this->registerOauthAttributes($response["body"]->access_token, $response["body"]->refresh_token, $expire_at);
        }
    }

    private function treatResponse($response, $path, $request = array())
    {
        if ($response['httpCode'] >= 400 || (isset($response['body']->status) && $response['body']->status >= 400)) {
            $this->log->write('PATH: ' . $path);
            $this->log->write('REQUEST: ' . json_encode($request));
            $this->log->write('RESPONSE: ' . json_encode($response));
        }
    }

    private function registerOauthAttributes($accessToken, $refreshToken, $expireAt)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix_oauth . "' AND `key` = 'module_mercadolivre_oauth_access_token'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix_oauth . "' AND `key` = 'module_mercadolivre_oauth_refresh_token'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix_oauth . "' AND `key` = 'module_mercadolivre_oauth_expire_at'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix_oauth . "', `key` = 'module_mercadolivre_oauth_access_token', `value` = '" . $this->db->escape($accessToken) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix_oauth . "', `key` = 'module_mercadolivre_oauth_refresh_token', `value` = '" . $this->db->escape($refreshToken) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix_oauth . "', `key` = 'module_mercadolivre_oauth_expire_at', `value` = '" . $this->db->escape($expireAt) . "'");
    }

    public function getProductML($product_ml)
    {
        $result = $this->db->query('SELECT * FROM `' . DB_PREFIX . "mercadolivre_products` WHERE `ml_product_code` = '" . $this->db->escape($product_ml) . "'");

        return $result->row;
    }

    private function treatNotificationForOrders($resource)
    {
        $this->prepareForRequest();
        $response = self::$mlSdk->get($resource);
        $this->treatResponse($response, $resource);

        $result = $this->db->query('SELECT COUNT(*) AS total FROM `' . DB_PREFIX . "mercadolivre_orders` WHERE `ml_id` = '" . (int)$response['body']->id . "'");

        if ($result->row['total'] > 0) {
            $this->updateOrder($response['body']);
        } else {
            $this->sendMessageForBuyer($response['body']);
            $this->createOrder($response['body']);
            $this->subtractStock($response['body']);
        }
    }

    private function updateOrder($order_ml)
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "mercadolivre_orders` SET `status` = '" . $this->db->escape($order_ml->status) . "' WHERE `ml_id` = '" . (int)$order_ml->id . "'");
        if ($order_ml->status == 'cancelled') {
            $this->sumStock($order_ml);
        }
    }

    private function sendMessageForBuyer($order_ml)
    {
        $messageToBuyerEnabled = $this->config->get('module_mercadolivre_feedback_enabled');

        if (!empty($messageToBuyerEnabled) && $messageToBuyerEnabled == 'y') {
            $this->prepareForRequest();
            $pack_id = empty($order_ml->pack_id) ? $order_ml->id : $order_ml->pack_id;
            $uri = "messages/packs/$pack_id/sellers/{$order_ml->seller->id}";
            $request = [
                'from' => [
                    'user_id' => $order_ml->seller->id,
                    'email' => $order_ml->seller->email
                ],
                'to' => [
                    'user_id' => $order_ml->buyer->id
                ],
                'text' => $this->config->get('module_mercadolivre_feedback_message')
            ];

            $response = self::$mlSdk->post($uri, $request);
            $this->treatResponse($response, $uri, $request);
        }
    }

    private function createOrder($order_ml)
    {
        $creationDate = date('Y-m-d H:i:s', strtotime($order_ml->date_created));
        $expirationDate = date('Y-m-d H:i:s', strtotime($order_ml->expiration_date));

        $this->db->query('INSERT INTO `' . DB_PREFIX . "mercadolivre_orders` SET `ml_id` = '" . (int)$order_ml->id . "', `date_created` = '$creationDate', `expiration_date` = '$expirationDate', 
            `total` = '" . (float)$order_ml->total_amount . "', `buyer` = '" . $this->db->escape($order_ml->buyer->first_name . ' ' . $order_ml->buyer->last_name) . "', 
            `buyer_document_type` = '" . $this->db->escape($order_ml->buyer->billing_info->doc_type) . "', `buyer_document_number` = '" . $this->db->escape($order_ml->buyer->billing_info->doc_number) . "', 
            `status` = '" . $this->db->escape($order_ml->status) . "', `created_at` = NOW()");

        $order_id = $this->db->getLastId();

        if (is_array($order_ml->order_items)) {
            foreach ($order_ml->order_items as $item) {
                $variation = '';
                if (is_array($item->item->variation_attributes)) {
                    foreach ($item->item->variation_attributes as $key => $variation) {
                        $variation = ($key + 1) . ' - ' . $variation->name . ': ' . $variation->value_name . '<br />';
                    }
                }

                $this->db->query('INSERT INTO `' . DB_PREFIX . "mercadolivre_orders_products` SET `mercadolivre_order_id` = '$order_id', `name` = '" . $this->db->escape($item->item->title) . "', `variation` = '" . $this->db->escape($variation) . "'");
            }
        }
    }

    private function subtractStock($order_ml)
    {
        if (is_array($order_ml->order_items)) {
            foreach ($order_ml->order_items as $item) {
                $result = $this->db->query('SELECT * FROM `' . DB_PREFIX . "mercadolivre_products` WHERE `ml_product_code` = '" . $this->db->escape($item->item->id) . "'");
                $product_ml = $result->row;

                if (!empty($product_ml) && $product_ml['subtract_product']) {
                    $this->load->model('catalog/product');

                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity - " . (int)$item->quantity . ") WHERE product_id = '" . (int)$product_ml['product_id'] . "' AND subtract = '1'");

                    if (is_array($item->item->variation_attributes)) {
                        foreach ($item->item->variation_attributes as $variation) {
                            $this->db->query('UPDATE `' . DB_PREFIX . 'product_option_value` pov 
                                              INNER JOIN `' . DB_PREFIX . 'option_value_description` ovd ON pov.option_value_id = ovd.option_value_id 
                                              INNER JOIN `' . DB_PREFIX . 'option_description` od ON pov.option_id = od.option_id 
                                              SET pov.quantity = (pov.quantity - ' . (int)$item->quantity . ") WHERE ovd.name = '" . $this->db->escape($variation->value_name) . "' 
                                              AND od.name = '" . $this->db->escape($variation->name) . "' AND pov.product_id = '" . (int)$product_ml['product_id'] . "' AND pov.subtract = '1'");
                        }
                    }
                }
            }
        }
    }

    private function sumStock($order_ml)
    {
        if (is_array($order_ml->order_items)) {
            foreach ($order_ml->order_items as $item) {
                $result = $this->db->query('SELECT * FROM `' . DB_PREFIX . "mercadolivre_products` WHERE `ml_product_code` = '" . $this->db->escape($item->item->id) . "'");
                $product_ml = $result->row;

                if (!empty($product_ml) && $product_ml['subtract_product']) {
                    $this->load->model('catalog/product');

                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$item->quantity . ") WHERE product_id = '" . (int)$product_ml['product_id'] . "' AND subtract = '1'");

                    if (is_array($item->item->variation_attributes)) {
                        foreach ($item->item->variation_attributes as $variation) {
                            $this->db->query('UPDATE `' . DB_PREFIX . 'product_option_value` pov 
                                              INNER JOIN `' . DB_PREFIX . 'option_value_description` ovd ON pov.option_value_id = ovd.option_value_id 
                                              INNER JOIN `' . DB_PREFIX . 'option_description` od ON pov.option_id = od.option_id 
                                              SET pov.quantity = (pov.quantity + ' . (int)$item->quantity . ") WHERE ovd.name = '" . $this->db->escape($variation->value_name) . "' 
                                              AND od.name = '" . $this->db->escape($variation->name) . "' AND pov.product_id = '" . (int)$product_ml['product_id'] . "' AND pov.subtract = '1'");
                        }
                    }
                }
            }
        }
    }
}