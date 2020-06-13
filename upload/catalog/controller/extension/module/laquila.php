<?php
require_once 'laquila_modules/SimpleXLSX.php';

class ControllerExtensionModuleLaquila extends Controller
{
    private $route = 'extension/module/laquila';

    public function eventAddOrder($route, $data) {

        if (isset($data[0]) && !empty($data[0]) && isset($data[1]) && !empty($data[1])) {
            $this->load->model($this->route);
            $order_id = $data[0];
            $order_status_id = $data[1];

            $this->model_extension_module_laquila->addOrderLaquila($order_id, $order_status_id);
        }
    }
}