<?php
include 'mercadolivre_modules/Meli.php';

class ControllerExtensionModuleMercadolivre extends Controller {

    const TOPICS_ACCEPTS = ['questions', 'orders_v2'];
    private $log;

    /**
     * ControllerExtensionModuleMercadolivre constructor.
     * @param $registry
     */
    public function __construct($registry) {
        $this->registry = $registry;
        $this->log = new Log('mercadolivre.log');
    }



	public function index() {
		if (isset($this->request->get['code'])) {
            $this->load->model('extension/module/mercadolivre');
            $this->model_extension_module_mercadolivre->updateMLCode($this->request->get['code']);
		}

        $this->response->redirect($this->url->link('common/home', '', true));
	}

	public function notifications() {
        $body = json_decode(file_get_contents('php://input'));

        $this->load->model('extension/module/mercadolivre');
        $this->model_extension_module_mercadolivre->treatNotifications($body->resource, $body->topic, $body->application_id);

        $this->response->setOutput('Ok');
    }
}
