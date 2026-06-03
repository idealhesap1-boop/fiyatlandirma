<?php
namespace Opencart\Catalog\Model\Extension\ComponentProduct;

class Main extends \Opencart\System\Engine\Model {
    
    public function getProductComponentPanel(int $product_id): array {
        $query = $this->db->query("
            SELECT p.`product_id`, p.`sku`, pd.`name` 
            FROM `" . DB_PREFIX . "product` p
            LEFT JOIN `" . DB_PREFIX . "product_description` pd 
                ON p.`product_id` = pd.`product_id`
            WHERE p.`product_id` = '" . (int)$product_id . "'
            AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
        ");
        
        if (!$query->num_rows) {
            return [];
        }
        
        $product = $query->row;
        
        $option_values = $this->db->query("
            SELECT ov.`option_value_id`, ov.`option_id`, ov.`name`, ov.`note_text`,
                   po.`required`, od.`name` as option_name
            FROM `" . DB_PREFIX . "option_value` ov
            LEFT JOIN `" . DB_PREFIX . "product_option` po 
                ON ov.`option_id` = po.`option_id`
            LEFT JOIN `" . DB_PREFIX . "option_description` od 
                ON ov.`option_id` = od.`option_id`
            WHERE po.`product_id` = '" . (int)$product_id . "'
            AND ov.`note_text` IS NOT NULL
            AND ov.`note_text` != ''
            AND od.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY od.`name` ASC, ov.`name` ASC
        ");
        
        return [
            'product_id' => $product_id,
            'product_name' => $product['name'],
            'product_sku' => $product['sku'],
            'component_options' => $option_values->rows ?: []
        ];
    }
    
    public function getOptionValueStock(int $option_value_id): ?int {
        return null;
    }
}
