<?php


class ModelExtensionModuleMercadolivre extends Model
{
    private $key_prefix = 'module_mercadolivre';

    public function updateMLCode($code) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = '" . $this->key_prefix . "' AND `key` = 'module_mercadolivre_authentication_code'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->key_prefix . "', `key` = 'module_mercadolivre_authentication_code', `value` = '" . $this->db->escape($code) . "'");
    }
}