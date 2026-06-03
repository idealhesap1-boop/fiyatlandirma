<?php
/**
 * OpenCart 4 Module Installation Script
 * Component Product Module v1.2
 */

namespace Opencart\System\Engine;

class ExtensionInstaller {
    public function install(\Opencart\System\Engine\Controller $controller, array $args = []): void {
        $this->db = $controller->db;
        $this->load = $controller->load;
        
        // 1. Register module extension
        $this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'component_product' AND `type` = 'module'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET `extension` = 'component_product', `type` = 'module', `code` = 'component_product'");

        // 2. Create default module settings
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'module_component_product'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'module_component_product', 'module_component_product_status', '1', 0)");

        // 3. Register total extension
        $this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'component_total' AND `type` = 'total'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET `extension` = 'component_total', `type` = 'total', `code` = 'component_total'");
        
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'total_component_total'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'total_component_total', 'total_component_total_status', '1', 0)");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'total_component_total', 'total_component_total_sort_order', '8', 0)");

        // 4. Add database columns
        $this->addColumnIfNotExists(DB_PREFIX . 'option_value', 'note_text', "TEXT NULL");
        $this->addColumnIfNotExists(DB_PREFIX . 'cart', 'component_data', "TEXT NULL");
        $this->addColumnIfNotExists(DB_PREFIX . 'order_option', 'component_qty', "INT(11) NOT NULL DEFAULT '1'");
        $this->addColumnIfNotExists(DB_PREFIX . 'order_option', 'note_text', "TEXT NULL");

        // 5. Register events - load event model first
        $controller->load->model('setting/event');
        
        $events = [
            [
                'code' => 'cse_ui_binding',
                'description' => 'CSE Component Product - UI Binding',
                'trigger' => 'catalog/view/product/product/before',
                'action' => 'extension/component_product/main|injectPanel',
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'code' => 'cse_cart_intercept',
                'description' => 'CSE Component Product - Cart Intercept',
                'trigger' => 'catalog/controller/checkout/cart/add/before',
                'action' => 'extension/component_product/main|onCartAdd',
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'code' => 'cse_order_snapshot',
                'description' => 'CSE Component Product - Order Snapshot',
                'trigger' => 'catalog/model/checkout/order/addOrder/before',
                'action' => 'extension/component_product/main|bindImmutableOrderData',
                'status' => 1,
                'sort_order' => 1
            ]
        ];

        foreach ($events as $event) {
            $controller->model_setting_event->addEvent($event);
        }

        // 6. Clear modification cache
        $this->clearModificationCache();
    }

    public function uninstall(\Opencart\System\Engine\Controller $controller, array $args = []): void {
        $this->db = $controller->db;
        
        // 1. Remove module extension
        $this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'component_product' AND `type` = 'module'");
        
        // 2. Remove module settings
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'module_component_product'");

        // 3. Remove total extension
        $this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `code` = 'component_total' AND `type` = 'total'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'total_component_total'");

        // 4. Remove events
        $controller->load->model('setting/event');
        $controller->model_setting_event->deleteEventByCode('cse_ui_binding');
        $controller->model_setting_event->deleteEventByCode('cse_cart_intercept');
        $controller->model_setting_event->deleteEventByCode('cse_order_snapshot');

        // 5. Clear modification cache
        $this->clearModificationCache();
    }

    private function addColumnIfNotExists(string $table, string $column, string $type): void {
        $check = $this->db->query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $column . "'");
        if (!$check->num_rows) {
            $this->db->query("ALTER TABLE `" . $table . "` ADD COLUMN `" . $column . "` " . $type);
        }
    }

    private function clearModificationCache(): void {
        $files = glob(DIR_CACHE . 'modification*.php');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }
}
