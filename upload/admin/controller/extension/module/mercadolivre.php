<?php
include 'mercadolivre_modules/Meli.php';

class ControllerExtensionModuleMercadolivre extends Controller
{
    private $route = 'extension/module/mercadolivre';
    private $key_prefix = 'module_mercadolivre';
    private $key_prefix_oauth = 'module_mercadolivre_oauth';
    private $error = array();

    public function index()
    {
        $data = $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model($this->route);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting($this->key_prefix, $this->request->post);

            foreach ($this->request->post['categories'] as $key => $item) {
                $this->model_extension_module_mercadolivre->editCategory($key, $item);
            }

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
        $this->addDataToViewInput($data, 'categories', $configs, []);

        if (empty($data['categories'])) {
            $data['categories'] = $this->model_extension_module_mercadolivre->getCategoriesML();
        }

        $data['categories'] = $this->getCategories($data['categories']);
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
            preg_match('/[^0-9+><%;:=]/i', html_entity_decode($this->request->post['module_mercadolivre_price_adjustment']))) {
            $this->error['error_price_adjustment'] = $this->language->get('message_error_price_adjustment');
        }

        return !$this->error;
    }

    private function createBreadcrumbs() {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        return $breadcrumbs;
    }

    private function addDataToViewInput(&$data, $inputName, $result, $default = '')
    {
        if (isset($this->request->post[$inputName])) {
            $data[$inputName] = $this->request->post[$inputName];
        } else {
            $data[$inputName] = $result[$inputName] ?? $default;
        }
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

    private function loadTemplate(&$data)
    {
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
    }

    public function revoke() {
        $this->load->language($this->route);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model($this->route);
            $this->model_extension_module_mercadolivre->revokeAuthentication();
        }

        $this->response->redirect($this->url->link('extension/module/mercadolivre/authentication', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function authentication()
    {
        $data = $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title_authentication'));

        $this->load->model('setting/setting');

        $data['breadcrumbs'] = $this->createBreadcrumbs();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_authentication'),
            'href' => $this->url->link($this->route . '/authentication', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        $data['revoke'] = $this->url->link('extension/module/mercadolivre/revoke', 'user_token=' . $this->session->data['user_token'], true);
        $data['warning_about_application_uri_redirect'] = sprintf($this->language->get('warning_about_application_uri_redirect'), HTTPS_CATALOG);
        $data['authentication_url'] = sprintf($this->language->get('authentication_url'), HTTPS_CATALOG);

        $configs = $this->model_setting_setting->getSetting($this->key_prefix);
        $configs_oauth = $this->model_setting_setting->getSetting($this->key_prefix_oauth);
        $data['without_code'] = false;
        $data['module_mercadolivre_app_id'] = '';


        if (empty($configs['module_mercadolivre_app_id'])) {
            $data['warning'] = $this->language->get('message_error_extesion_not_configured');
        } else if (empty($configs_oauth['module_mercadolivre_oauth_authentication_code'])) {
            $data['without_code'] = true;
            $data['module_mercadolivre_app_id'] = $configs['module_mercadolivre_app_id'];
        }

        $this->loadTemplate($data);
        $this->response->setOutput($this->load->view($this->route . '/authentication', $data));
    }

    public function products()
    {
        $data = $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title_products'));
        $data['heading_title'] = $this->language->get('heading_title_products');

        $this->load->model($this->route);
        $this->load->model('setting/setting');
        $configs = $this->model_setting_setting->getSetting($this->key_prefix);

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->session->data['warning'])) {
            $data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        if (!$this->validateConfig($configs)) {
            $data['warning'] = $this->language->get('message_error_configs_not_done');

            $this->response->redirect($this->url->link('extension/module/mercadolivre', 'user_token=' . $this->session->data['user_token'], true));
            return;
        }

        $data['consider_special_price'] = $configs['module_mercadolivre_consider_special_price'];
        $data['price_adjustment'] = $configs['module_mercadolivre_price_adjustment'];
        $data['auto_detect_category'] = $configs['module_mercadolivre_auto_detect_category'];
        $data['listing_type'] = $configs['module_mercadolivre_listing_type'];
        $data['with_local_pick_up'] = $configs['module_mercadolivre_shipping_with_local_pick_up'];
        $data['shipping_free'] = $configs['module_mercadolivre_shipping_free'];
        $data['ml_country'] = $configs['module_mercadolivre_country'];
        $data['shipping_free'] = $configs['module_mercadolivre_shipping_free'];

        $this->getList($data);
    }

    private function validateConfig($configs)
    {
        $configs_oauth = $this->model_setting_setting->getSetting($this->key_prefix_oauth);
        return !empty($configs['module_mercadolivre_app_id']) && !empty($configs['module_mercadolivre_app_secret']) &&
            !empty($configs_oauth['module_mercadolivre_oauth_authentication_code']) && !empty($configs['module_mercadolivre_shipping_type']) &&
            !empty($configs['module_mercadolivre_condition']) && !empty($configs['module_mercadolivre_buying_mode']) &&
            !empty($configs['module_mercadolivre_country']);
    }

    private function getList(&$data)
    {
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
        $data['synchronizeStockPrice'] = $this->url->link($this->route . '/synchronizeStockPrice', 'user_token=' . $this->session->data['user_token'], true);

        $data['products'] = array();
        $filter_data = array(
            'filter_name' => $filter_name,
            'filter_status' => $filter_status,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $this->load->model('catalog/product');
        $product_total = $this->model_catalog_product->getTotalProducts($filter_data);
        $results = $this->model_catalog_product->getProducts($filter_data);
        $this->load->model('tool/image');

        foreach ($results as $result) {
            $image_path = is_file(DIR_IMAGE . $result['image']) ? $result['image'] : 'no_image.png';
            $products_in_ml = $this->model_extension_module_mercadolivre->getProductML($result['product_id']);

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
                'image' => $this->model_tool_image->resize($image_path, 40, 40),
                'name' => $result['name'],
                'model' => $result['model'],
                'ml_id' => $ml_ids,
                'ml_quantity' => $ml_quantity,
                'ml_categories' => $ml_categories,
                'status_ml' => $status_ml,
                'quantity' => $result['quantity'],
                'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit' => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
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

    private function mountUrl(&$url, $name, $isText = false)
    {
        if (isset($this->request->get[$name])) {
            $url .= "&{$name}=" . ($isText ? urlencode(html_entity_decode($this->request->get[$name], ENT_QUOTES, 'UTF-8')) : $this->request->get[$name]);
        }
    }

    public function activeProduct()
    {
        $this->load->language($this->route);
        $this->load->model($this->route);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $products = $this->request->post['selected'];
            $count = 0;

            try {
                foreach ($products as $product_id) {
                    if ($this->model_extension_module_mercadolivre->activeProduct($product_id)) {
                        $count++;
                    }
                }
            } catch (Exception $ex) {
                $this->session->data['warning'] = $ex->getMessage();
            }

            if ($count > 0) {
                $this->session->data['success'] = $this->language->get('text_activated_with_success');
            } else {
                $this->session->data['warning'] = $this->language->get('text_no_items_activated');
            }
        }

        $this->response->redirect($this->url->link('extension/module/mercadolivre/products', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function pauseProduct()
    {
        $this->load->language($this->route);
        $this->load->model($this->route);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $products = $this->request->post['selected'];
            $count = 0;
            try {
                foreach ($products as $product_id) {
                    if ($this->model_extension_module_mercadolivre->pauseProduct($product_id)) {
                        $count++;
                    }
                }
            } catch (Exception $ex) {
                $this->session->data['warning'] = $ex->getMessage();
            }

            if ($count > 0) {
                $this->session->data['success'] = $this->language->get('text_paused_with_success');
            } else {
                $this->session->data['warning'] = $this->language->get('text_no_items_paused');
            }
        }

        $this->response->redirect($this->url->link('extension/module/mercadolivre/products', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function disconnectProduct()
    {
        $this->load->language($this->route);
        $this->load->model($this->route);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $products = $this->request->post['selected'];
            $count = 0;

            try {
                foreach ($products as $product_id) {
                    if ($this->model_extension_module_mercadolivre->removeProduct($product_id)) {
                        $count++;
                    }
                }
            } catch (Exception $ex) {
                $this->session->data['warning'] = $ex->getMessage();
            }

            if ($count > 0) {
                $this->session->data['success'] = $this->language->get('text_deleted_with_success');
            } else {
                $this->session->data['warning'] = $this->language->get('text_no_items_deleted');
            }
        }

        $this->response->redirect($this->url->link('extension/module/mercadolivre/products', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function addProducts()
    {
        $this->load->language($this->route);
        $this->load->model($this->route);
        $json = array();

        $subtract_stock = $this->request->post['subtract_product'] ?? '';
        $price_adjustment = html_entity_decode($this->request->post['ml_price_adjustment']);
        $products = explode(',', $this->request->post['product_ids']);
        $category_id = $this->request->post['ml_category'] ?? null;

        if (!empty($price_adjustment) && preg_match('/[^0-9+><%;:=]/i', $price_adjustment)) {
            $json['error'] = $this->language->get('message_error_price_adjustment');
        }

        try {
            if (empty($json)) {
                $this->load->model('catalog/product');

                foreach ($products as $product_id) {
                    if ($product = $this->model_extension_module_mercadolivre->getProduct($product_id)) {
                        $product['product_warranty_type'] = $this->request->post['ml_product_warranty_type'];
                        $product['warranty_unit'] = $this->request->post['ml_product_warranty_unit'];
                        $product['warranty'] = $this->request->post['ml_product_warranty'];
                        $product['price_adjustment'] = $price_adjustment;
                        $product['listing_type'] = $this->request->post['ml_listing_type'];
                        $product['subtract_product'] = $subtract_stock;
                        $product['category_id'] = $category_id;
                        $product['shipping_free'] = $this->request->post['shipping_free'];
                        $product['variations'] = array();
                        $product['price'] = (float)$this->config->get('module_mercadolivre_consider_special_price') && $product['special'] ? $product['special'] : $product['price'];

                        $product['images'] = $this->model_catalog_product->getProductImages($product_id);
                        $options = $this->model_catalog_product->getProductOptions($product_id);

                        foreach ($options as $option) {
                            foreach ($option['product_option_value'] as $product_option_value) {
                                $optionDescription = $this->model_catalog_product->getProductOptionValue($product_id, $product_option_value['product_option_value_id']);

                                $priceWithTax = $this->tax->calculate($product_option_value['price'], $product['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);
                                $price = $product['price'] + ($product_option_value['price_prefix'] == '+' ? $priceWithTax : -$priceWithTax);

                                $product['variations'][] = [
                                    'name' => $option['name'],
                                    'value_name' => $optionDescription['name'],
                                    'price' => $price,
                                    'quantity' => $product_option_value['quantity'],
                                    'sku' => $product_option_value['product_option_value_id'],
                                    'image' => $product_option_value['image']
                                ];
                            }
                            //support only one Variation
                            break;
                        }

                        $this->setCategoryByConfiguration($category_id, $product_id, $product);

                        $this->model_extension_module_mercadolivre->createProductInMl($product);
                    }
                }
                $json['success'] = $this->language->get('text_products_add_success');
            }
        } catch (Exception $ex) {
            $json['error'] = $ex->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * @param $category_id
     * @param $product_id
     * @param $product
     */
    private function setCategoryByConfiguration($category_id, $product_id, &$product)
    {
        if (empty($category_id)) {
            $categories = $this->model_extension_module_mercadolivre->getCategoriesByProductId($product_id);

            foreach ($categories as $category) {
                $result = $this->model_extension_module_mercadolivre->getCategoryMl($category['category_id']);

                if (!empty($result)) {
                    $product['category_id'] = $result['mercadolivre_category_code'];
                    break;
                }
            }
        }
    }

    public function synchronizeStockPrice()
    {
        $this->load->language($this->route);
        $this->load->model($this->route);
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            try {
                $products = $this->request->post['products'];

                foreach ($products as $product_id) {
                    $this->model_extension_module_mercadolivre->updatePriceAndStock($product_id);
                }
            } catch (Exception $ex) {
                $json['error'] = $ex->getMessage();
            }
        }

        if (empty($json)) {
            $json['success'] = $this->language->get('text_stock_and_price_updated');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
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
        $this->model_setting_event->addEvent('mercadolivre_delete_product', 'admin/model/catalog/product/deleteProduct/after', $this->route . '/eventDeleteProduct');
    }

    public function uninstall()
    {
        $this->load->model('setting/setting');
        $this->load->model('user/user_group');
        $this->load->model($this->route);
        $this->load->model('setting/event');

        $this->model_setting_setting->deleteSetting($this->key_prefix);
        $this->model_setting_setting->deleteSetting($this->key_prefix_oauth);
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', $this->route);
        $this->model_extension_module_mercadolivre->removerTabelas();
        $this->model_extension_module_mercadolivre->removeCron();
        $this->model_setting_event->deleteEventByCode('mercadolivre_edit_product');
    }

    public function cron()
    {
        $this->load->model($this->route);
        try {
            $this->model_extension_module_mercadolivre->updateAllStockAndPrices();
        } catch (Exception $ex) {
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => true]));
    }

    public function eventDeleteProduct($route, $data)
    {
        if (!empty($data[0])) {
            try {
                $this->load->model($this->route);
                $this->model_extension_module_mercadolivre->removeProduct($data[0]);
            } catch (Exception $ex) { }
        }
    }

    public function orders() {
        $data = $this->load->language($this->route);
        $filter_expiration_date = $this->request->get['filter_expiration_date'] ?? null;
        $filter_creation_date = $this->request->get['filter_creation_date'] ?? null;
        $page = $this->request->get['page'] ?? 1;

        $this->document->setTitle($this->language->get('heading_title_orders'));
        $data['heading_title'] = $this->language->get('heading_title_orders');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->session->data['warning'])) {
            $data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        $this->load->model($this->route);
        $this->load->model('setting/setting');

        $configs = $this->model_setting_setting->getSetting($this->key_prefix);

        if (!$this->validateConfig($configs)) {
            $data['warning'] = $this->language->get('message_error_configs_not_done');

            $this->response->redirect($this->url->link('extension/module/mercadolivre', 'user_token=' . $this->session->data['user_token'], true));
            return;
        }

        $urlMain = '';
        $this->mountUrl($urlMain, 'filter_expiration_date');
        $this->mountUrl($urlMain, 'filter_creation_date');

        $url = $urlMain;
        $this->mountUrl($url, 'page');

        $data['breadcrumbs'] = $this->createBreadcrumbs();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_orders'),
            'href' => $this->url->link($this->route . '/orders', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['delete'] = $this->url->link($this->route . '/deleteOrder', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['orders'] = array();
        $data['orders_products'] = array();
        $filter_data = array(
            'filter_expiration_date' => $filter_expiration_date,
            'filter_creation_date' => $filter_creation_date,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $question_total = $this->model_extension_module_mercadolivre->getTotalOrders($filter_data);
        $results = $this->model_extension_module_mercadolivre->getOrders($filter_data);

        foreach ($results as $order) {
            $products = $this->model_extension_module_mercadolivre->getOrdersProducts($order['mercadolivre_order_id']);
            $data['orders_products'][$order['mercadolivre_order_id']] = $products;

            $data['orders'][] = [
                'mercadolivre_order_id' => $order['mercadolivre_order_id'],
                'ml_id' => $order['ml_id'],
                'date_created' => date('d/m/Y H:i', strtotime($order['date_created'])),
                'expiration_date' => date('d/m/Y H:i', strtotime($order['expiration_date'])),
                'total' => $this->currency->format($order['total'], $this->session->data['currency']),
                'buyer' => $order['buyer'],
                'buyer_document' => $order['buyer_document_type'] . ': ' . $order['buyer_document_number'],
                'status' => $this->language->get('order_status_' . $order['status']),
                'products' => $products
            ];
        }

        $pagination = new Pagination();
        $pagination->total = $question_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->route . '/orders', 'user_token=' . $this->session->data['user_token'] . $urlMain . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['user_token'] = $this->session->data['user_token'];
        $data['filter_expiration_date'] = $filter_expiration_date;
        $data['filter_creation_date'] = $filter_creation_date;

        $this->loadTemplate($data);
        $this->response->setOutput($this->load->view($this->route . '/orders', $data));

    }

    public function deleteOrder() {
        $this->load->language($this->route);
        $this->load->model($this->route);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $orders = $this->request->post['selected'];
            $count = 0;

            try {
                foreach ($orders as $order_id) {
                    if ($this->model_extension_module_mercadolivre->removeOrder($order_id)) {
                        $count++;
                    }
                }

                if ($count > 0) {
                    $this->session->data['success'] = $this->language->get('text_order_deleted_with_success');
                } else {
                    $this->session->data['warning'] = $this->language->get('text_no_order_deleted');
                }
            } catch (Exception $ex) {
                $this->session->data['warning'] = $ex->getMessage();
            }
        }
        $this->response->redirect($this->url->link('extension/module/mercadolivre/orders', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function questions() {
        $data = $this->load->language($this->route);
        $filter_question = empty($this->request->get['filter_question']) ? null :  $this->request->get['filter_question'];
        $page = $this->request->get['page'] ?? 1;

        $this->document->setTitle($this->language->get('heading_title_questions'));
        $data['heading_title'] = $this->language->get('heading_title_questions');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->session->data['warning'])) {
            $data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        $this->load->model($this->route);
        $this->load->model('setting/setting');

        $configs = $this->model_setting_setting->getSetting($this->key_prefix);

        if (!$this->validateConfig($configs)) {
            $data['warning'] = $this->language->get('message_error_configs_not_done');

            $this->response->redirect($this->url->link('extension/module/mercadolivre', 'user_token=' . $this->session->data['user_token'], true));
            return;
        }

        $urlMain = '';
        $this->mountUrl($urlMain, 'filter_question');

        $url = $urlMain;
        $this->mountUrl($url, 'page');

        $data['breadcrumbs'] = $this->createBreadcrumbs();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_questions'),
            'href' => $this->url->link($this->route . '/questions', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['delete'] = $this->url->link($this->route . '/deleteQuestion', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['questions'] = array();
        $filter_data = array(
            'filter_question' => $filter_question,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $question_total = $this->model_extension_module_mercadolivre->getTotalQuestions($filter_data);
        $results = $this->model_extension_module_mercadolivre->getQuestions($filter_data);

        foreach ($results as $question) {
            $product_ml = $this->model_extension_module_mercadolivre->getProductMLBy($question['mercadolivre_products_id']);
            $product_name = $this->language->get('entry_without_product_related');

            if (!empty($product_ml)) {
                $product = $this->model_extension_module_mercadolivre->getProduct($product_ml['product_id']);
                $product_name = $product['name'];
            }


            $data['questions'][] = [
                'mercadolivre_question_id' => $question['mercadolivre_question_id'],
                'question' => $question['question'],
                'product_name' => $product_name,
                'answered' => !empty($question['answer']),
                'answer' => empty($question['answer']) ? $this->language->get('text_question_no_answered') : $question['answer']
            ];
        }

        $pagination = new Pagination();
        $pagination->total = $question_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->route . '/questions', 'user_token=' . $this->session->data['user_token'] . $urlMain . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['user_token'] = $this->session->data['user_token'];
        $data['filter_question'] = $filter_question;

        $this->loadTemplate($data);
        $this->response->setOutput($this->load->view($this->route . '/questions', $data));
    }

    public function deleteQuestion() {
        $this->load->language($this->route);
        $this->load->model($this->route);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $questions = $this->request->post['selected'];
            $count = 0;

            try {
                foreach ($questions as $question_id) {
                    if ($this->model_extension_module_mercadolivre->removeQuestion($question_id)) {
                        $count++;
                    }
                }

                if ($count > 0) {
                    $this->session->data['success'] = $this->language->get('text_question_deleted_with_success');
                } else {
                    $this->session->data['warning'] = $this->language->get('text_no_question_deleted');
                }
            } catch (Exception $ex) {
                $this->session->data['warning'] = $ex->getMessage();
            }
        }
        $this->response->redirect($this->url->link('extension/module/mercadolivre/questions', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function addAnswer() {
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $question_id = $this->request->post['ml_question_id'];
            $answer = $this->request->post['ml_answer'];
            $this->load->language($this->route);
            $this->load->model($this->route);

            try {
                if ($this->model_extension_module_mercadolivre->sendAsnwer($question_id, $answer)) {
                    $json['success'] =  $this->language->get('text_question_answered');
                } else {
                    $json['error'] =  $this->language->get('text_question_no_answered');
                }
            } catch (Exception $ex) {
                $json['error'] = $ex->getMessage();
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function eventEditProduct($route, $data)
    {


    }

    public function logs()
    {
        $data = $this->load->language($this->route);

        $this->document->setTitle($this->language->get('heading_title_logs'));
        $data['heading_title'] = $this->language->get('heading_title_logs');
        $data['text_list'] = $this->language->get('heading_title_logs');

        $data['success'] = '';
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        }

        $data['error_warning'] = '';
        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        }

        $data['breadcrumbs'] = $this->createBreadcrumbs();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_logs'),
            'href' => $this->url->link('extension/module/mercadolivre/logs', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['download'] = $this->url->link('extension/module/mercadolivre/downloadLog', 'user_token=' . $this->session->data['user_token'], true);
        $data['clear'] = $this->url->link('extension/module/mercadolivre/clearLog', 'user_token=' . $this->session->data['user_token'], true);

        $file = DIR_LOGS . 'mercadolivre.log';
        $data['log'] = '';
        if (is_file($file)) {
            $data['log'] = file_get_contents(DIR_LOGS . 'mercadolivre.log', FILE_USE_INCLUDE_PATH, null);
        }

        $this->loadTemplate($data);
        $this->response->setOutput($this->load->view('tool/log', $data));
    }

    public function clearLog()
    {
        $this->load->language($this->route);

        $file = DIR_LOGS . 'mercadolivre.log';
        if (file_exists($file)) {
            $handle = fopen($file, 'w+');
            fclose($handle);
        }

        $this->session->data['success'] = $this->language->get('text_success');

        $this->response->redirect($this->url->link('extension/module/mercadolivre/logs', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function downloadLog()
    {
        $this->load->language($this->route);
        $file = DIR_LOGS . 'mercadolivre.log';

        if (file_exists($file) && filesize($file) > 0) {
            $this->response->addheader('Pragma: public');
            $this->response->addheader('Expires: 0');
            $this->response->addheader('Content-Description: File Transfer');
            $this->response->addheader('Content-Type: application/octet-stream');
            $this->response->addheader('Content-Disposition: attachment; filename="mercadolivre_' . date('Y-m-d_H-i-s', time()) . '_error.log"');
            $this->response->addheader('Content-Transfer-Encoding: binary');

            $this->response->setOutput(file_get_contents($file, FILE_USE_INCLUDE_PATH, null));
        } else {
            $this->session->data['error'] = sprintf($this->language->get('message_log_error_warning'), basename($file), '0B');

            $this->response->redirect($this->url->link('extension/module/mercadolivre/logs', 'user_token=' . $this->session->data['user_token'], true));
        }
    }
}