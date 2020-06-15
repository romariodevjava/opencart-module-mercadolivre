<?php
class ControllerExtensionModuleMercadolivre extends Controller {

    private $key_prefix = 'module_mercadolivre';

	public function index() {
		if (isset($this->request->get['code'])) {
            $this->load->model('extension/module/mercadolivre');
            $this->model_extension_module_mercadolivre->updateMLCode($this->request->get['code']);
		}

        $this->response->redirect($this->url->link('common/home', '', true));
	}
}
