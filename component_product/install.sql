-- Component Product Module v1.2 - Manual Install SQL

-- 1. Register module extension
INSERT INTO `oc_extension` (`extension`, `type`, `code`) VALUES ('opencart', 'module', 'component_product')
ON DUPLICATE KEY UPDATE `extension` = 'opencart';

-- 2. Create module settings
INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'module_component_product', 'module_component_product_status', '1', 0)
ON DUPLICATE KEY UPDATE `value` = '1';

-- 3. Register total extension
INSERT INTO `oc_extension` (`extension`, `type`, `code`) VALUES ('opencart', 'total', 'component_total')
ON DUPLICATE KEY UPDATE `extension` = 'opencart';

INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'total_component_total', 'total_component_total_status', '1', 0)
ON DUPLICATE KEY UPDATE `value` = '1';

INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES (0, 'total_component_total', 'total_component_total_sort_order', '8', 0)
ON DUPLICATE KEY UPDATE `value` = '8';

-- 4. Add database columns (run separately if needed)
-- ALTER TABLE `oc_option_value` ADD COLUMN `note_text` TEXT NULL;
-- ALTER TABLE `oc_cart` ADD COLUMN `component_data` TEXT NULL;
-- ALTER TABLE `oc_order_option` ADD COLUMN `component_qty` INT(11) NOT NULL DEFAULT '1';
-- ALTER TABLE `oc_order_option` ADD COLUMN `note_text` TEXT NULL;

-- 5. Register events
INSERT INTO `oc_event` (`code`, `description`, `trigger`, `action`, `status`, `sort_order`) 
SELECT 'cse_ui_binding', 'CSE Component Product - UI Binding', 'catalog/view/product/product/before', 'extension/component_product/main|injectPanel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `oc_event` WHERE `code` = 'cse_ui_binding');

INSERT INTO `oc_event` (`code`, `description`, `trigger`, `action`, `status`, `sort_order`) 
SELECT 'cse_cart_intercept', 'CSE Component Product - Cart Intercept', 'catalog/controller/checkout/cart/add/before', 'extension/component_product/main|onCartAdd', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `oc_event` WHERE `code` = 'cse_cart_intercept');

INSERT INTO `oc_event` (`code`, `description`, `trigger`, `action`, `status`, `sort_order`) 
SELECT 'cse_order_snapshot', 'CSE Component Product - Order Snapshot', 'catalog/model/checkout/order/addOrder/before', 'extension/component_product/main|bindImmutableOrderData', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `oc_event` WHERE `code` = 'cse_order_snapshot');
