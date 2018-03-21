<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license   Urb-it
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/load.php');

/**
 * Class Urbit
 */
class Urbit extends UrbitAbstract
{
    const CARRIER_CODE_REGULAR = 'URB_REGULAR';

    /**
     * Construct class urbit.
     */
    public function __construct()
    {
        $this->name = 'urbit';
        $this->version = '1.1.5';

        $this->author = 'urb-it';
        $this->tab = 'shipping_logistics';
        $this->class_controller_admin = 'AdminUrbit';
        $this->displayName = $this->l('urb-it');

        // call parent construct
        parent::__construct();

        $this->module_key = 'a28ee08818efc46aecb78bc6ef2c9b3c';
        $this->description = $this->l('urb-it deliveres orders exactly where you want it, when you want it.');
        $this->carrier_code = self::CARRIER_CODE_REGULAR;
    }

    /**
     * Install module
     * @return boolean
     * @throws PrestaShopDatabaseException
     */
    public function install()
    {
        $installer = new UrbitInstaller($this);

        return parent::install() && $installer->install();
    }

    /**
     * Uninstall module
     * @return boolean
     * @throws PrestaShopDatabaseException
     */
    public function uninstall()
    {
        $installer = new UrbitInstaller($this);
        return parent::uninstall() && $installer->uninstall();
    }

    /**
     * Implement hook Extra Carrier
     * - Update delay time from API
     * - Implement feature "extra_cover"
     *
     * @return string
     */
    public function hookExtraCarrier()
    {
        if (version_compare(_PS_VERSION_, '1.6') === -1 || version_compare(_PS_VERSION_, '1.7') === 1) {
            $this->context->controller->addJS(array($this->_path . 'views/js/extracarrier.js'));
        } else {
            $this->context->controller->addJS(array($this->_path . 'views/js/extracarrier16.js'));
        }


        $this->context->controller->addJS(array(
            $this->_path . 'views/js/extracover.js',
            $this->_path . 'views/js/extracover-form.js',
            $this->_path . 'views/js/jquery.tools.min.js'
        ));

        $this->context->controller->addCSS(array(
            $this->_path . 'abstract/views/css/extracarrier.css'
        ));

        $this->context->smarty->assign(array(
            'urbit_delays' => Tools::jsonEncode($this->getDelays()),
            'urbit_show_delay' => Configuration::get('URBIT_SHOW_DELAY') ? Configuration::get('URBIT_SHOW_DELAY') : 0,
            'urbit_extra_form' => Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM') ?
              Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM') : 0,
            'urbit_partly_costs' => Tools::jsonEncode($this->processOutputCosts($this->partly_costs)),
            'urbit_show_partly_cost' => Configuration::get('URBIT_SHOW_PARTLY_COST'),
            'urbit_place_extra_cover_form' => Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM', false) ?
              Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM') : 'popup_center',
            'this_path' => $this->_path,
            'total_product_gst' => (int) $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
            'extra_cover_carriers' => isset($this->extra_cover) ?
              Tools::jsonEncode(array_values($this->extra_cover)) : Tools::jsonEncode(array()),
            'id_carrier_selected' => isset($this->context->cookie->aus_id_carrier) ?
              $this->context->cookie->aus_id_carrier : '',
            'ajax_extra_cover_url' => $this->context->link->getModuleLink($this->name, 'ShippingCost', array(), true),
            'ajax_extra_cover_action' => 'ExtraCoverForm'
        ));
        return $this->display($this->name . '.php', 'extracarrier.tpl');
    }
}
