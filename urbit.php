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
require_once(dirname(__FILE__) . '/models/UrbitInstaller.php');
require_once(dirname(__FILE__) . '/includes/urbitabstract.php');

class Urbit extends UrbitAbstract
{
    /**
     * Construct class urbit.
     */
    public function __construct()
    {
        $this->name = 'urbit';
        $this->version = '1.0.0';
        $this->author = 'urb-it';
        $this->tab = 'shipping_logistics';
        $this->class_controller_admin = 'AdminUrbit';
        $this->displayName = $this->l('urb-it');
        //$this->bootsrap = true;
        // call parent construct
        parent::__construct();
        $this->module_key = '7a9dca0785f107e7921c2449cc1462c3';
        $this->description = $this->l('urb-it deliveres orders exactly where you want it, when you want it.');
        $this->carrier_code = 'URB_REGULAR';
    }

    /**
     * Install module
     * @return boolean
     */
    public function install()
    {
        $this->installer = new UrbitInstaller($this->name);
        return ( parent::install() && $this->installer->installCarriers() &&
        $this->installer->installWarehouseCarriers() && $this->registerHook('extraCarrier') && $this->installer->installConfigs());
    }

    /**
     * Uninstall module
     * @return boolean
     */
    public function uninstall()
    {
        $this->uninstaller = new UrbitInstaller($this->name);
        return parent::uninstall();
    }

    /**
     * Implement hook Extra Carrier
     * - Update delay time from API
     * - Implement feature "extra_cover"
     *
     * @param type $params = Array(
      [address] => Address Object
      )
     */
    public function hookextraCarrier()
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
            'urbit_extra_form' => Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM') ? Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM') : 0,
            'urbit_partly_costs' => Tools::jsonEncode($this->processOutputCosts($this->partly_costs)),
            'urbit_show_partly_cost' => Configuration::get('URBIT_SHOW_PARTLY_COST'),
            'urbit_place_extra_cover_form' => Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM', false) ? Configuration::get('URBIT_PLACE_EXTRA_COVER_FORM') : 'popup_center',
            'this_path' => $this->_path,
            'total_product_gst' => (int) $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
            'extra_cover_carriers' => isset($this->extra_cover) ? Tools::jsonEncode(array_values($this->extra_cover)) : Tools::jsonEncode(array()),
            'id_carrier_selected' => isset($this->context->cookie->aus_id_carrier) ? $this->context->cookie->aus_id_carrier : '',
            'ajax_extra_cover_url' => $this->context->link->getModuleLink($this->name, 'ShippingCost', array(), true),
            'ajax_extra_cover_action' => 'ExtraCoverForm'
        ));
        return $this->display($this->name . '.php', 'extracarrier.tpl');
    }
}
