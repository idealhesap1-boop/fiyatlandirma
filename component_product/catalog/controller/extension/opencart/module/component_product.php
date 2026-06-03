<?php
namespace Opencart\Catalog\Controller\Extension\Opencart\Module;

class ComponentProduct extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/module/component_product');
        
        $data['component_product_status'] = $this->config->get('module_component_product_status');
        
        if ($data['component_product_status']) {
            $this->load->model('extension/component_product/main');
            
            $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
            $data['component_panel_data'] = $this->model_extension_component_product_main->getProductComponentPanel($product_id);
        } else {
            $data['component_panel_data'] = [];
        }
        
        $this->response->setOutput($this->load->view('extension/module/component_product', $data));
    }
}
