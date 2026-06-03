<?php
namespace Opencart\Catalog\Controller\Extension\ComponentProduct;

class Main extends \Opencart\System\Engine\Controller {
    
    /**
     * Event: catalog/view/product/product/before
     * Injects component panel into product page
     */
    public function injectPanel(&$route, &$args, &$output): void {
        // Check if module is enabled
        if (!$this->config->get('module_component_product_status')) {
            return;
        }

        // Load model
        $this->load->model('extension/component_product/main');
        
        // Get product_id from route args
        $product_id = isset($args['product_id']) ? (int)$args['product_id'] : 0;
        
        if (!$product_id) {
            return;
        }

        // Get component panel data
        $panel_data = $this->model_extension_component_product_main->getProductComponentPanel($product_id);
        
        if (!$panel_data || empty($panel_data['component_options'])) {
            return;
        }

        // Store panel data for view
        $args['component_panel_data'] = $panel_data;
    }

    /**
     * Event: catalog/controller/checkout/cart/add/before
     * Validates and processes component data when adding to cart
     */
    public function onCartAdd(&$route, &$args, &$output): void {
        // Check if module is enabled
        if (!$this->config->get('module_component_product_status')) {
            return;
        }

        // Check if component data is in request
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

        // Validate component quantities
        $this->load->model('extension/component_product/main');
        
        foreach ($component_data as $option_value_id => $meta) {
            if (!isset($meta['qty']) || $meta['qty'] < 1) {
                unset($component_data[$option_value_id]);
                continue;
            }

            // Stock check if available
            $stock_check = $this->model_extension_component_product_main->getOptionValueStock((int)$option_value_id);
            if ($stock_check !== null && $stock_check < $meta['qty']) {
                $component_data[$option_value_id]['stock_warning'] = true;
            }
        }

        // Store component data in session
        $this->session->data['component_product_cart_data'] = $component_data;
    }

    /**
     * Event: catalog/model/checkout/order/addOrder/before
     * Captures component data before order is finalized
     */
    public function bindImmutableOrderData(&$route, &$args, &$output): void {
        // Check if module is enabled
        if (!$this->config->get('module_component_product_status')) {
            return;
        }

        // If component data exists in session, prepare it for order storage
        if (isset($this->session->data['component_product_cart_data'])) {
            $component_data = $this->session->data['component_product_cart_data'];
            
            // Store as JSON for order options expansion
            $this->session->data['component_product_order_snapshot'] = json_encode($component_data);
        }
    }
}
