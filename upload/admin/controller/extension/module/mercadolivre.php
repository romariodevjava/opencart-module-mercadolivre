<?php
include 'mercadolivre_modules/Meli.php';

class ControllerExtensionModuleMercadolivre extends Controller
{
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

        $data = array_merge($data, $this->error);

        $data['warning'] = $this->error['warning'] ?? '';

        $data['breadcrumbs'] = $this->createBreadcrumbs();

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

        $configs = $this->model_setting_setting->getSetting($this->key_prefix);
        $this->addDataToViewInput($data, 'module_mercadolivre_categories', $configs);

        $data['categories'] = $this->getCategories($data['module_mercadolivre_categories']);
        $this->addDataToViewInput($data, 'module_mercadolivre_app_id', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_app_secret', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_country', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_status', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_listing_type', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_currency', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_buying_mode', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_condition', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_auto_detect_category', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_price_adjustment', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_consider_special_price', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_feedback_enabled', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_feedback_message', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_shipping_type', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_shipping_with_local_pick_up', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_shipping_free', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_template_title', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_template_description', $configs);
        $this->addDataToViewInput($data, 'module_mercadolivre_template_image_aditional', $configs);

        $this->load->model('tool/image');

        $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        if (isset($data['module_mercadolivre_template_image_aditional']) && is_file(DIR_IMAGE . $data['module_mercadolivre_template_image_aditional'])) {
            $data['thumb'] = $this->model_tool_image->resize($data['module_mercadolivre_template_image_aditional'], 100, 100);
        }

        $this->loadTemplate($data);
        $this->response->setOutput($this->load->view($this->route, $data));
    }

    public function authentication() {
        $data = $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title_authentication'));
        $data['heading_title'] = $this->language->get('heading_title_authentication');

        $this->load->model('setting/setting');
        $this->load->model($this->route);

        $data['breadcrumbs'] = $this->createBreadcrumbs();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_authentication'),
            'href' => $this->url->link($this->route . '/authentication', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        $data['warning_about_application_uri_redirect'] = sprintf($this->language->get('warning_about_application_uri_redirect'), HTTPS_CATALOG);
        $data['authentication_url'] = sprintf($this->language->get('authentication_url'), HTTPS_CATALOG);

        $configs = $this->model_setting_setting->getSetting($this->key_prefix);
        $data['without_code'] = false;
        $data['module_mercadolivre_app_id'] = '';


        if (empty($configs['module_mercadolivre_app_id'])) {
            $data['warning'] = $this->language->get('message_error_extesion_not_configured');
        } else if (empty($configs['module_mercadolivre_authentication_code'])) {
            $data['without_code'] = true;
            $data['module_mercadolivre_app_id'] = $configs['module_mercadolivre_app_id'];
        }

        $this->loadTemplate($data);
        $this->response->setOutput($this->load->view($this->route . '/authentication', $data));
    }

    public function products() {
        $data = $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title_products'));
        $data['heading_title'] = $this->language->get('heading_title_products');

        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $configs = $this->model_setting_setting->getSetting($this->key_prefix);

        $data['consider_special_price'] = $configs['module_mercadolivre_consider_special_price'];
        $data['price_adjustment'] = $configs['module_mercadolivre_price_adjustment'];
        $data['auto_detect_category'] = $configs['module_mercadolivre_auto_detect_category'];
        $data['listing_type'] = $configs['module_mercadolivre_listing_type'];
        $data['with_local_pick_up'] = $configs['module_mercadolivre_shipping_with_local_pick_up'];
        $data['shipping_free'] = $configs['module_mercadolivre_shipping_free'];

        $this->getList($data);
    }

    private function getList(&$data) {
        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : '';
        $filter_status = isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : '';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'pd.name';
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

        $urlMain = '';
        $this->mountUrl($urlMain, 'filter_name');
        $this->mountUrl($urlMain, 'filter_status');

        $url = $urlMain;
        $this->mountUrl($url, 'page');
        $this->mountUrl($url, 'order');
        $this->mountUrl($url, 'sort');

        $data['breadcrumbs'] = $this->createBreadcrumbs();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_products'),
            'href' => $this->url->link($this->route . '/products', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        $data['disconnect'] = $this->url->link($this->route . '/disconnectProduct', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['addProducts'] = $this->url->link($this->route . '/addProducts', 'user_token=' . $this->session->data['user_token'], true);
        $data['synchronizeStockPrice'] = $this->url->link($this->route . '/synchronizeStockPrice', 'user_token=' . $this->session->data['user_token'], true);

        $data['products'] = array();
        $filter_data = array(
            'filter_name'	  => $filter_name,
            'filter_status'   => $filter_status,
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $this->load->model('catalog/product');
        $product_total = $this->model_catalog_product->getTotalProducts($filter_data);
        $results = $this->model_catalog_product->getProducts($filter_data);
        $this->load->model('tool/image');

        foreach ($results as $result) {
            $image_path = is_file(DIR_IMAGE . $result['image']) ? $result['image'] : 'no_image.png';
            $special = false;

            $product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

            foreach ($product_specials  as $product_special) {
                if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
                    $special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

                    break;
                }
            }

            $products_in_ml = $this->model_extension_module_mercadolivre->getProduct($result['product_id']);

            $ml_ids = '';
            $ml_quantity = 0;
            $ml_categories = [];
            $status_ml = '';

            foreach ($products_in_ml as $product_in_ml) {
                $ml_ids .= $product_in_ml['ml_product_code'] . '<br />';
                $ml_quantity++;
                $ml_categories[] = $product_in_ml['mercadolivre_category_id'];
                $status_ml .= ($product_in_ml['status'] ? $this->language->get('text_ml_status_' . $product_in_ml['status']) : $this->language->get('text_ml_without_status')) . '<br />';
            }

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'image'      => $this->model_tool_image->resize($image_path, 40, 40),
                'name'       => $result['name'],
                'model'      => $result['model'],
                'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
                'special'    => $special,
                'ml_id'      => $ml_ids,
                'ml_quantity'    => $ml_quantity,
                'ml_categories'  => $ml_categories,
                'status_ml'    => $status_ml,
                'quantity'   => $result['quantity'],
                'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
            );
        }

        $url = $urlMain;
        $url .= $order == 'ASC' ? '&order=DESC' : '&order=ASC';
        $this->mountUrl($url, 'page');
        $data['sort_name'] = $this->url->link($this->route . '/products', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
        $data['sort_quantity'] = $this->url->link($this->route . '/products', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
        $data['sort_status'] = $this->url->link($this->route . '/products', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);

        $url = $urlMain;
        $this->mountUrl($url, 'order');
        $this->mountUrl($url, 'sort');

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->route . '/products', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['user_token'] = $this->session->data['user_token'];
        $data['filter_name'] = $filter_name;
        $data['filter_status'] = $filter_status;
        $data['sort'] = $sort;
        $data['order'] = $order;

        $this->loadTemplate($data);
        $this->response->setOutput($this->load->view($this->route . '/products', $data));
    }

    private function createBreadcrumbs() {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        return $breadcrumbs;
    }

    public function disconnectProduct() {

    }

    public function addProducts() {

    }

    public function synchronizeStockPrice() {

    }

    private function loadTemplate(&$data) {
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
    }

    private function addDataToViewInput(&$data, $inputName, $result,  $default = '')
    {
        if (isset($this->request->post[$inputName])) {
            $data[$inputName] = $this->request->post[$inputName];
        } else {
            $data[$inputName] = $result[$inputName] ?? $default;
        }
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission_message');
        }

        if (empty($this->request->post['module_mercadolivre_app_id'])) {
            $this->error['error_app_id'] = $this->language->get('message_error_app_id');
        }

        if (empty($this->request->post['module_mercadolivre_app_secret'])) {
            $this->error['error_app_secret'] = $this->language->get('message_error_app_secret');
        }

        if (empty($this->request->post['module_mercadolivre_country'])) {
            $this->error['error_app_country'] = $this->language->get('message_error_app_country');
        }

        if (empty($this->request->post['module_mercadolivre_listing_type'])) {
            $this->error['error_listing_type'] = $this->language->get('message_error_listing_type');
        }

        if (empty($this->request->post['module_mercadolivre_currency'])) {
            $this->error['error_currency'] = $this->language->get('message_error_currency');
        }

        if (empty($this->request->post['module_mercadolivre_buying_mode'])) {
            $this->error['error_buying_mode'] = $this->language->get('message_error_buying_mode');
        }

        if (empty($this->request->post['module_mercadolivre_buying_mode'])) {
            $this->error['error_buying_mode'] = $this->language->get('message_error_buying_mode');
        }

        if (empty($this->request->post['module_mercadolivre_condition'])) {
            $this->error['error_condition'] = $this->language->get('message_error_condition');
        }

        if (!empty($this->request->post['module_mercadolivre_price_adjustment']) &&
                preg_match('/[^0-9+\-><%;]/', $this->request->post['module_mercadolivre_price_adjustment'])) {
            $this->error['error_price_adjustment'] = $this->language->get('message_error_price_adjustment');
        }

        return !$this->error;
    }

    private function getCategories($mercadolivre_categories = array())
    {
        $categories = array();

        $categories_1 = $this->model_extension_module_mercadolivre->getCategories(0);

        foreach ($categories_1 as $category_1) {
            $ml_category = $mercadolivre_categories[$category_1['category_id']] ?? '';
            $categories[] = ['category_id' => $category_1['category_id'], 'name' => $category_1['name'], 'ml_category' => $ml_category];

            $categories_2 = $this->model_extension_module_mercadolivre->getCategories($category_1['category_id']);

            foreach ($categories_2 as $category_2) {
                $ml_category = $mercadolivre_categories[$category_2['category_id']] ?? '';
                $categories[] = ['category_id' => $category_2['category_id'], 'name' => $category_2['name'], 'ml_category' => $ml_category];

                $categories_3 = $this->model_extension_module_mercadolivre->getCategories($category_2['category_id']);

                foreach ($categories_3 as $category_3) {
                    $ml_category = $mercadolivre_categories[$category_3['category_id']] ?? '';
                    $categories[] = ['category_id' => $category_3['category_id'], 'name' => $category_3['name'], 'ml_category' => $ml_category];
                }
            }
        }

        return $categories;
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
        $this->load->model('setting/event');

        $this->model_setting_setting->deleteSetting($this->key_prefix);
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', $this->route);
        $this->model_extension_module_mercadolivre->removerTabelas();
        $this->model_extension_module_mercadolivre->removeCron();
        $this->model_setting_event->deleteEventByCode('mercadolivre_edit_product');
    }

    public function cron()
    {
        $this->load->model($this->route);

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