<?php
require_once 'mercadolivre_modules/Meli.php.php';

class ControllerExtensionModuleMercadolivre extends Controller
{
    const MERCADO_LIVRE_COUNTRIES = ['MLA', 'MLB', 'MCO', 'MCR', 'MEC', 'MLC', 'MLM', 'MLU', 'MLV', 'MPA', 'MPE', 'MPT', 'MRD'];

    private $route = 'extension/module/mercadolivre';
    private $key_prefix = 'module_mercadolivre';
    private $error = array();

    public function index()
    {
        $data = $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model($this->route);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting($this->key_prefix, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['warning'] = $this->error['warning'] ?? '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['countries'] = array();

        foreach (self::MERCADO_LIVRE_COUNTRIES as $country) {
            $data['countries'][] = ['key' => $country, 'value' => $this->language->get($country)];
        };

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->route, $data));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission_message');
        }

        return !$this->error;
    }

    public function install()
    {
        $this->load->model('setting/setting');
        $this->load->model($this->route);
        $this->load->model('user/user_group');
        $this->load->model('setting/event');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $this->route);
        $this->model_extension_module_mercadolivre->criarTabelas();
        $this->model_extension_module_mercadolivre->createCron();
        $this->model_setting_event->addEvent('mercadolivre_edit_product', 'admin/model/catalog/product/editProduct/after', $this->route . '/eventEditProduct');
    }

    public function uninstall()
    {
        $this->load->model('setting/setting');
        $this->load->model('user/user_group');
        $this->load->model($this->route);

        $this->model_setting_setting->deleteSetting($this->route);
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', $this->route);
        $this->model_extension_module_laquila->removerTabelas();
        $this->model_extension_module_laquila->removeCron();
        $this->model_setting_event->deleteEventByCode('laquila_add_order');
    }

    public function cron()
    {
        $this->load->model($this->route);
        $this->model_extension_module_laquila->updateProductsSynced();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => true]));
    }

    public function eventEditProduct($route, $data)
    {

    }

    private function mountUrl(&$url, $name, $isText = false)
    {
        if (isset($this->request->get[$name])) {
            $url .= "&{$name}=" . ($isText ? urlencode(html_entity_decode($this->request->get[$name], ENT_QUOTES, 'UTF-8')) : $this->request->get[$name]);
        }
    }
}