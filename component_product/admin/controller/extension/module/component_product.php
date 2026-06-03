<?php
namespace Opencart\Admin\Controller\Extension\Module;

class ComponentProduct extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/module/component_product');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
            ],
            [
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/component_product', 'user_token=' . $this->session->data['user_token'])
            ]
        ];

        $data['save'] = $this->url->link('extension/module/component_product.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

        $data['module_component_product_status'] = $this->config->get('module_component_product_status');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/component_product', $data));
    }

    public function save(): void {
        $this->load->language('extension/module/component_product');
        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/module/component_product')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('module_component_product', $this->request->post);
            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
