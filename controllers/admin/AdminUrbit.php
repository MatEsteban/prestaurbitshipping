<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

require_once(dirname(__FILE__) . '/../../controllers/admin/AdminUrbitAbstract.php');

class AdminUrbitController extends AdminUrbitAbstract
{
    public function __construct()
    {
        parent::__construct();

        $this->configuration_keys['URBIT_SHOW_DELAY'] = 'isInt';
        $this->configuration_keys['URBIT_SHOW_PARTLY_COST'] = 'isInt';
        $this->configuration_keys['URBIT_DEFAULT_PRODUCT_LENGTH'] = 'isInt';
        $this->configuration_keys['URBIT_DEFAULT_PRODUCT_HEIGHT'] = 'isInt';
        $this->configuration_keys['URBIT_DEFAULT_PRODUCT_WIDTH'] = 'isInt';
        $this->configuration_keys['URBIT_DEFAULT_PRODUCT_WEIGHT'] = 'isInt';
        $this->configuration_keys['URBIT_FLEXIBLE_PACKAGE'] = 'isBool';
        $this->configuration_keys['URBIT_PACKAGE_MARGIN'] = 'isInt';
        $this->configuration_keys['URBIT_PLACE_EXTRA_COVER_FORM'] = 'isString';
    }

}
