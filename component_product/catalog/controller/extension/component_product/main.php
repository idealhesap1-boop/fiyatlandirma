<?php
namespace Opencart\Catalog\Controller\Extension\ComponentProduct;

class Main extends \Opencart\System\Engine\Controller {
    
    public function injectPanel(&$route, &$args, &$output): void {
        if (!$this->config->get('module_component_product_status')) {
            return;
        }

        $this->load->model('extension/component_product/main');
        
        $product_id = isset($args['product_id']) ? (int)$args['product_id'] : 0;
        
        if (!$product_id) {
            return;
        }

        $panel_data = $this->model_extension_component_product_main->getProductComponentPanel($product_id);
        
        if (!$panel_data || empty($panel_data['component_options'])) {
            return;
        }

        $args['component_panel_data'] = $panel_data;
    }

    public function onCartAdd(&$route, &$args, &$output): void {
        if (!$this->config->get('module_component_product_status')) {
            return;
        }

        if (!isset($this->request->post['component_data'])) {
            return;
        }

        $component_data = json_decode($this->request->post['component_data'], true);
        if (!is_array($component_data) || empty($component_data)) {
            return;
        }

        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        if (!$product_id) {
            return;
        }

        $this->load->model('extension/component_product/main');
        
        foreach ($component_data as $option_value_id => $meta) {
            if (!isset($meta['qty']) || $meta['qty'] < 1) {
                unset($component_data[$option_value_id]);
                continue;
            }
        }

        $this->session->data['component_product_cart_data'] = $component_data;
    }

    public function bindImmutableOrderData(&$route, &$args, &$output): void {
        if (!$this->config->get('module_component_product_status')) {
            return;
        }

        if (isset($this->session->data['component_product_cart_data'])) {
            $component_data = $this->session->data['component_product_cart_data'];
            $this->session->data['component_product_order_snapshot'] = json_encode($component_data);
        }
    }
}
