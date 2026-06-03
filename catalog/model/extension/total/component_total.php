<?php
namespace Opencart\Catalog\Model\Extension\Total;

class ComponentTotal extends \Opencart\System\Engine\Model {
    public function getTotal(array &$totals, array &$taxes, float &$total): void {
        // Component Total is informational for now
        // Actual component pricing is handled through option values
    }
}
