<?php


class ModelExtensionModuleLaquila extends Model
{
    const URL_LAQUILA = 'http://drop.laquila.com.br:8189/api_acom/acom/TTerceiros/Integracao/';
    const METHOD_INSERT_ORDER = '00002';

    private $key_prefix = 'module_laquila';

    public function addOrderLaquila($order_id, $order_status_id) {
        $log = new Log('laquila.log');

        $this->load->model('account/order');
        $order_info = $this->getOrder($order_id);

        if (!$this->hasOrderCreated($order_id) && in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
            $log->write('Iniciando criação de pedido ' . $order_id . 'na laquila.');

            $products = $this->model_account_order->getOrderProducts($order_id);

            $products_laquila = array();

            foreach ($products as $product) {
                $options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);
                $product_option_value_id = null;

                if (!empty($options)) {
                    $option = reset($options);
                    $product_option_value_id = $option['product_option_value_id'];
                }

                $product_laquila = $this->getProducts($product['product_id'], $product_option_value_id);

                if (!empty($product_laquila)) {
                    $products_laquila[] = array(
                        'cd_item' => $product_laquila['cd_item'],
                        'qt_pedida' => $product['quantity'],
                        'qt_disponivel' => $product['quantity'],
                        'vl_unitario' => number_format($product['price'], 2, ',', ''),
                        'vl_total' => number_format($product['total'], 2, ',', '')
                    );
                }
            }

            if (!empty($products_laquila)) {
                $order_laquila_id = $this->sendOrder($products_laquila, $order_info);
                if ($order_laquila_id != null) {
                    $this->saveOrderLaquila($order_laquila_id, $order_id);
                }
            }
        }
    }

    private function saveOrderLaquila($order_laquila_id, $order_id) {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "laquila_order` SET `order_id` = '" . (int)$order_id . "', `id_pedido_laquila` = '" . (int) $order_laquila_id . "', `status` = 'ATIVO', `created_at` = NOW()");
    }

    private function hasOrderCreated($order_id) {
        $result = $this->db->query('SELECT count(laquila_order_id) as total FROM `' . DB_PREFIX . "laquila_order` WHERE `order_id` = '" . (int) $order_id . "'");

        return $result->row['total'] > 0;
    }

    private function getOrder($order_id) {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND order_status_id > '0'");

        return array(
            'order_id'                => $order_query->row['order_id'],
            'invoice_no'              => $order_query->row['invoice_no'],
            'invoice_prefix'          => $order_query->row['invoice_prefix'],
            'customer_id'             => $order_query->row['customer_id'],
            'firstname'               => $order_query->row['firstname'],
            'lastname'                => $order_query->row['lastname'],
            'email'                   => $order_query->row['email'],
            'telephone'               => $order_query->row['telephone'],
            'custom_field'            => json_decode($order_query->row['custom_field'], true),
            'comment'                 => $order_query->row['comment'],
            'total'                   => $order_query->row['total'],
            'order_status_id'         => $order_query->row['order_status_id'],
            'date_added'              => $order_query->row['date_added'],
        );;
    }

    private function getProducts($product_id, $product_option_value_id)
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "laquila_products` lp
        INNER JOIN `" . DB_PREFIX . "laquila_products_association` lpa ON lp.laquila_product_id = lpa.laquila_product_id ";

        $this->getProductsCriteria($sql, $product_id, $product_option_value_id);
        $result = $this->db->query($sql);

        return $result->row;
    }


    private function getProductsCriteria(&$sql, $product_id, $product_option_value_id)
    {
        $criteria = array();

        $criteria[] = "lpa.product_id = '" . (int) $product_id . "'";

        if ($product_option_value_id != null) {
            $criteria[] = "lpa.product_option_value_id = '" . (int) $product_option_value_id . "'";
        }

        if (!empty($criteria)) {
            $sql .= ' WHERE ' . implode(" AND ", $criteria);
        }
    }

    private function sendOrder($products_laquila, $order_info)
    {
        $token = $this->config->get($this->key_prefix . '_token');
        $token_user = $this->config->get($this->key_prefix . '_token_user');
        $cnpj = $this->config->get($this->key_prefix . '_cnpj');

        $url = self::URL_LAQUILA . $token . '/' . self::METHOD_INSERT_ORDER;
        $customer_cpf = $order_info['custom_field'][$this->config->get('module_laquila_custom_id_cpf')];

        $request = array(
            'cnpj_empresa' => $cnpj,
            'token' => $token_user,
            'dt_pedido' => date('Y-m-d'),
            'cpf_cnpj' =>  preg_replace('/[^0-9]/', '', $customer_cpf),
            'cd_cadastro' => '0',
            'nm_cliente' => $order_info['firstname'] . ' ' . $order_info['lastname'],
            'email' => $order_info['email'],
            'nr_fone' => $order_info['telephone'],
            'nr_celular' => $order_info['telephone'],
            'cd_situacao' => 'B',
            'cd_estagio' => '499',
            'nr_pedido' => '',
            'cd_usuario' => '24999',
            'itens' => $products_laquila
        );


        $payload = json_encode(array("pedido" => $request));
        $result = $this->executeRequest($url, $payload);

        $order_id = null;
        if ($result != false && isset($result['resultado'])) {
            $order_id = $result['resultado']['id_pedido'];
        }

        return $order_id;
    }

    private function executeRequest($url, $payload)
    {
        $log = new Log('laquila.log');
        $log->write('URL: ' . $url . ' - REQUEST: ' . $payload);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $log->write('RESPONSE: ' . $result);

        if (!$result) {
            $error_msg = curl_error($ch);
            $log->write("Erro ao fazer o pedido na Laquila. Erro: " . $error_msg);
        }

        curl_close($ch);

        return json_decode($result, true);
    }
}