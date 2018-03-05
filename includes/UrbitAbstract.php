<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class UrbitAbstract
 */
abstract class UrbitAbstract extends CarrierModule
{
    const PATH_JS = 'views/js/';
    const PATH_CSS = 'views/css/';
    const PATH_IMG = 'views/img/';
    const CLASS_PARENT_TAB = 'AdminParentShipping';

    /**
     * Remove tab of old version
     */
    const CLASS_CONTROLLER_SETTINGS = 'AdminUrbitSettings';

    /**
     * @var array
     */
    protected static $cache = array();

    /**
     * Containt all message lang include to js
     * @var array
     */
    public $i18n = array();

    /**
     * Containt url for js
     * @var array
     */
    public $url = array();

    /**
     * Define domestic australia
     * @var string
     */
    public $country_iso = 'au';

    /**
     * this will be assigned automatically by Cart::getPackageShippingCost()
     * @var int
     */
    public $id_carrier;

    /**
     * @var array
     */
    public $delays = array();

    /**
     * @var
     */
    public $partly_costs;

    /**
     * @var
     */
    public $extra_cover;

    /**
     * @var UrbitInstallerAbstract
     */
    protected $installer;

    /**
     * @var UrbitInstallerAbstract ?
     */
    protected $uninstaller;

    /**
     * an instance of the current product
     * @var Product
     */
    protected $product;

    /**
     * list carrier extra cover
     * @var array
     */
    protected $extra_cover_carriers = array();

    /**
     * caching this value
     * @var string
     */
    protected $_country_code = '';

    /**
     *  service code (combination of service_code + option_code + suboption_code)
     * @var string
     */
    protected $_service_code;

    /**
     * caching this value
     * @var string
     */
    protected $_postcode = '';

    /**
     * caching this value
     * @var string
     */
    protected $_service_area;

    /**
     * @var string
     */
    public $class_controller_admin;

    /**
     * construct
     */
    public function __construct()
    {
        $this->tab = 'shipping_logistics'; // assign tab
        $this->author = 'urb-it'; // assign author

        // call parent construct
        parent::__construct();

        //   $this->initTranslations();
        if (defined('_PS_ADMIN_DIR_')) {
            $this->assignAdminUrls();
        }
    }

    /**
     * Assign all possible urls to access from javascript, backend only
     */
    public function assignAdminUrls()
    {
        $this->urls = array(
            'saveGeneral'         => $this->getTargetUrl($this->class_controller_admin, 'General'),
            'postCodeSearch'      => $this->getTargetUrl($this->class_controller_admin, 'PostCodeSearch'),
            'editCategory'        => $this->getTargetUrl($this->class_controller_admin, 'EditCategory'),
            'saveCategorySetting' => $this->getTargetUrl($this->class_controller_admin, 'SaveCategorySetting'),
            'deleteCategory'      => $this->getTargetUrl($this->class_controller_admin, 'DeleteCategory'),
            'updateModuleStatus'  => $this->getTargetUrl($this->class_controller_admin, 'ModuleStatus'),
            'searchProduct'       => $this->getTargetUrl($this->class_controller_admin, 'SearchProduct'),
            'saveProduct'         => $this->getTargetUrl($this->class_controller_admin, 'SaveProductSetting'),
            'editProduct'         => $this->getTargetUrl($this->class_controller_admin, 'EditProductSetting'),
            'deleteProduct'       => $this->getTargetUrl($this->class_controller_admin, 'DeleteProductSetting'),
        );
    }

    /**
     * combine an Ajax URL for the default controller of module
     * @param string $controller
     * @param string $action
     * @param bool $ajax
     * @return string full Ajax Url
     */
    public function getTargetUrl($controller = '', $action = '', $ajax = true)
    {
        $params = array();
        $params['ajax'] = $ajax;
        $params['controller'] = $controller;
        $action = trim($action);
        if (!empty($action)) {
            $params['action'] = $action;
        }

        $query = array();

        return '?' . implode('&', $query);
    }


    /**
     * Get relative path to js files of module
     * @return string
     */
    public function getJsPath()
    {
        return $this->_path . self::PATH_JS;
    }

    /**
     * Get relative path to css files of module
     * @return string
     */
    public function getCssPath()
    {
        return $this->_path . self::PATH_CSS;
    }

    /**
     * Get relative path to image files of module
     * @return string
     */
    public function getImagePath()
    {
        return $this->_path . self::PATH_IMG;
    }

    /**
     * hook into Back Office header position
     * @return assign template
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (version_compare(_PS_VERSION_, '1.6') === 1) {
        }
        $st = array(
            'url' => $this->urls,
        );
        $this->context->smarty->assign('st', Tools::jsonEncode($st));
        $this->context->smarty->assign(
            array('urbit_img_path'  => $this->_path.'views/img/',)
        );

        return $this->display($this->name . '.php', 'backofficeheader.tpl');
    }

    /**
     * update status service code if change status in list carrier (shipping->carrier)
     * @param Array $params
     * Array
     * (
     *     [object] => Carier
     *     [cookie] => Cookie
     *     [cart] => Cart
     * )
     * @return boolean
     */
    public function hookActionObjectCarrierUpdateAfter($params)
    {
        $carrier = $params['object'];
        $flag = false;
        if (validate::isLoadedObject($carrier)) {
            // get list services from model
            $selected_service = UrbitRateServiceCode::getSelectedService($carrier->id);

            // update service code
            if (!empty($selected_service)) {
                $object_service_code = new UrbitRateServiceCode($selected_service['id_urbit_rate_service_code']);
                if (Validate::isLoadedObject($object_service_code)) {
                    $object_service_code->active = (int)$carrier->active;
                    if ($object_service_code->update()) {
                        $flag = true;
                    }
                }
            }
        }

        return $flag;
    }

    /**
     * function get delay of carrier
     * @param type $id_carrier
     * @return string delays or array delays depend on id carrier
     */
    public function getDelays($id_carrier = null)
    {
        if (empty($id_carrier)) {
            return $this->delays;
        } else {
            return !empty($this->delays[$id_carrier]) ? $this->delays[$id_carrier] : null;
        }
    }

    /**
     * function set partly costs by id carrier
     * @param string $delay
     */
    public function setDelays($delay)
    {
        if (version_compare(_PS_VERSION_, '1.6') === -1 || version_compare(_PS_VERSION_, '1.7') === 1) {
            $this->delays[$this->id_carrier] = $delay;
        } else {
            $carrier = new Carrier($this->id_carrier, $this->context->language->id);
            if (Validate::isLoadedObject($carrier)) {
                $this->delays[$this->id_carrier] = $carrier->name . '<br/>' . $delay;
            }
        }
    }

    /**
     * Front Methods
     * If you set need_range at true when you created your carrier (in install method)
     * The method called by the cart will be getOrderShippingCost
     * If not, the method called will be getOrderShippingCostExternal
     * $cart var contains the cart, the customer, the address
     */
    public function getOrderShippingCostExternal($cart)
    {
        return $this->getOrderShippingCost($cart, 0);
    }

    /**
     * Front Methods
     *
     * If you set need_range at true when you created your carrier (in install method)
     * The method called by the cart will be getOrderShippingCost
     * If not, the method called will be getOrderShippingCostExternal
     * @param Cart $cart
     * @param float $shipping_cost
     * @return boolean || shipping_cost
     */
    public function getOrderShippingCost($cart, $shipping_cost)
    {
        $configuratrion_status = $this->getConfigurationStatus();
        // validate all value input

        if (!$this->isValidate($cart) || !empty($configuratrion_status['fail'])) {
            return false;
        }

        // initial cost (calculated by Prestashop, in according to handling fee,
        // additional shipping fee, weight range/price range fee, etc
        $initial_shipping_cost = $shipping_cost;
        $this->_country_code = $this->getCountryCode($cart);
        $this->_service_area = $this->getServiceArea($this->_country_code);
        $this->_postcode = $this->getPostCode($cart);
        $this->_service_code = UrbitRateServiceCode::getServiceCode($this->id_carrier);
        if (UrbitExtraCover::isExtraCoverService($this->_service_code)) {
            $this->extra_cover[$this->id_carrier] = $this->id_carrier;
        }

        // validate cache by cart level - if it's availabe, use cache's data instead

        $hash = UrbitCache::getParamCacheByCart(
            $cart,
            $initial_shipping_cost,
            $this->id_carrier,
            $this->_country_code,
            $this->_postcode,
            $this->_service_code
        );

        $cache = UrbitCache::getCache($hash);
        if ($cache !== false) {
            // not type casting here. false = no record found
            if (isset($cache['delay']) && !empty($cache['delay'])) {
                $this->setDelays($cache['delay']);
            }
            if (isset($cache['partly_cost']) && !empty($cache['partly_cost'])) {
                $this->setPartlyCosts($cache['partly_cost']);
            }

            return UrbitCache::getOrderShippingCostFromCache($cache);
        }

        // get all packages
        $packages = UrbitPackage::getPackage(
            $cart,
            $this->_service_area,
            $this->id_carrier
        );
        if (empty($packages) || !is_array($packages)) {
            return false;
        }
        $request = $this->requestToAuspostAPI($packages, $cart);

        // final result
        if (empty($request['total_cost'])) {
            return false;
        } else {
            $shipping_cost = array_sum($request['total_cost']);
        }

        // include initial cost, calculated by Prestashop
        $shipping_cost += $initial_shipping_cost;
        // rounded, after apply GST, returned value may so long, fx: 14.409090909091
        $shipping_cost = Tools::ps_round($shipping_cost, 2);

        // save to cache
        if (!UrbitCache::saveCache(
            $hash,
            $shipping_cost,
            null,
            null,
            $request['delay'],
            $request['carrier_name'],
            $request['total_partly_costs']
        )
        ) {
            return false;
        }

        $this->setDelays($request['delay']);
        $this->setPartlyCosts($request['total_partly_costs']);

        return $shipping_cost;
    }

    /**
     * Check and notify status of all settings
     * @return Array (
     * [success] =>
     *    Array ( [0] => Urbit is configured and online! )
     * [fail] => Array ( )
     * )
     */
    public function getConfigurationStatus()
    {
        $check_configuration = array();
        if (!Validate::isInt(Configuration::get('URBIT_FLENGTH')) || !Configuration::get('URBIT_FLENGTH')) {
            $check_configuration['generalSettings'] = 1;
        }
        if (!Validate::isInt(Configuration::get('URBIT_FHEIGHT')) ||
            !Configuration::get('URBIT_FHEIGHT')) {
            $check_configuration['generalSettings'] = 1;
        }
        if (!Validate::isInt(Configuration::get('URBIT_FWIDTH')) || !Configuration::get('URBIT_FWIDTH')) {
            $check_configuration['generalSettings'] = 1;
        }
        if (!UrbitValidate::validateZipCode(Configuration::get('URBIT_CARRIER_POSTAL_CODE'), $this->country_iso)
          || !Configuration::get('URBIT_CARRIER_POSTAL_CODE')) {
            $check_configuration['generalSettings'] = 1;
        }
        // Validate service rate
        if (Db::getInstance()
                ->getValue(
                    'SELECT count(id_urbit_rate_service_code) FROM `' .
                     _DB_PREFIX_ .
                      'urbit_rate_service_code` WHERE `active` = 1'
                ) < 1) {
            $check_configuration['deliveryServices'] = 1;
        }

        // Validate webservice
        if (!$this->webServiceTest()) {
            $check_configuration['webserviceTest'] = 1;
        }

        $configuration_status = array('success' => array(), 'fail' => array());
        if (!count($check_configuration)) {
        } else {
        }

        return $configuration_status;
    }

    protected function webServiceTest()
    {
        return true;
    }

    /**
     * Validate all value input of object Cart
     * @param Cart $cart
     * @return boolean
     */
    protected function isValidate(Cart $cart)
    {
        if (!Module::isEnabled($this->name)) {
            return false;
        }
        // validate - basket empty
        if (!($cart instanceof Cart) || Cart::getNbProducts($cart->id) <= 0) {
            return false;
        }
        $service_area = $this->getServiceArea($this->getCountryCode($cart));
        if ($this->isCarrierCompare()) {
            // compare carrier module compatible
            if ($service_area == 'domestic') {
                if (empty($this->context->cookie->postcode)) {
                    return false;
                }
            }
        } elseif (!empty($cart->id_address_delivery)) {
            $address = $this->getAddress($cart->id_address_delivery);
            if (!Validate::isLoadedObject($address)) {
                return false;
            }
        } elseif ($this->isProductEstimation()) {
            // compare carrier module compatible
            if ($service_area == 'domestic') {
                if (empty($this->context->cookie->postcode)) {
                    return false;
                }
            }
        } else {
            return false;
        }
        if (!$this->isProductEstimation()) {
            // validate - current carrier is not initialized and specified by Cart class
            if (empty($this->id_carrier) || !is_int($this->id_carrier)) {
                return false;
            }
            // validate - this carrier is disabled by Prestashop admin (through carrier tab)
            $carrier = $this->getCarrier();
            if (!$carrier->active) {
                return false;
            }
            // validate - this carrier is not enabled by Urbit module
            $urbit_rate_config = new UrbitRateConfig();
            if (!in_array($this->id_carrier, $urbit_rate_config->getAllCarrierIds())) {
                return false;
            }
            $service_code = UrbitRateServiceCode::getServiceCode($this->id_carrier);
            if (Tools::strtolower(Tools::substr($service_code[0], 0, 4)) == 'intl') {
                if ($service_area == 'domestic') {
                    return false;
                }
            } else {
                if ($service_area == 'international') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * get service area by country code
     * @param string contry code
     * @return string area: domestic || international
     */
    protected function getServiceArea($country_code)
    {
        return Tools::strtolower($country_code) == 'au' ? 'domestic' : 'international';
    }

    /**
     * get country code from cache or baseon address delivery
     * @param Cart $cart
     * @return string country_code
     */
    protected function getCountryCode(Cart $cart)
    {
        $country_code = '';
        if ($this->isCarrierCompare()) {
            if (!empty($this->context->cookie->id_country)) {
                $country_code = Country::getIsoById($this->context->cookie->id_country);
            }
        } elseif (!empty($cart->id_address_delivery)) {
            $address = $this->getAddress($cart->id_address_delivery);
            $country_code = Country::getIsoById($address->id_country);
        } elseif ($this->isProductEstimation()) {
            if (!empty($this->context->cookie->id_country)) {
                $country_code = Country::getIsoById($this->context->cookie->id_country);
            }
        }

        return $country_code;
    }

    /**
     * Check if the current request is from the module Carrier Compare (Shipping Estimate)
     * @return boolean
     */
    public function isCarrierCompare()
    {
        $carrier_compare_name = 'carriercompare';
        $script_name = $_SERVER['SCRIPT_NAME'];

        return strpos($script_name, $carrier_compare_name) !== false;
    }

    /**
     * declare object address
     * @return Address address
     */
    public function getAddress($id_address_delivery)
    {
        return new Address($id_address_delivery);
    }

    /**
     * check request from cart or product
     * true if request from product | false if request from cart
     * @return boolean
     */
    protected function isProductEstimation()
    {
        $carrier_compare_name = 'Urbit';
        $script_name = $_SERVER['REQUEST_URI'];

        return strpos($script_name, $carrier_compare_name) !== false;
    }

    /**
     * declare object carrier
     * return Carrier carrier
     */
    public function getCarrier()
    {
        return new Carrier($this->id_carrier);
    }

    /**
     * Get post code from cookie or base on address delivery
     * @param Cart $cart
     * @return string post_code
     */
    protected function getPostCode(Cart $cart)
    {
        $post_code = '';
        if ($this->isCarrierCompare()) {
            // compare carrier module compatible
            if ($this->getServiceArea($this->getCountryCode($cart)) == 'domestic') {
                if (!empty($this->context->cookie->postcode)) {
                    $post_code = $this->context->cookie->postcode;
                }
            }
        } elseif (!empty($cart->id_address_delivery)) {
            $address = $this->getAddress($cart->id_address_delivery);
            $post_code = $address->postcode;
        } elseif ($this->isProductEstimation()) {
            if (!empty($this->context->cookie->postcode)) {
                $post_code = $this->context->cookie->postcode;
            }
        }

        return $post_code;
    }

    /**
     * function request to Auspost API
     * @param array $packages
     * @param Cart $cart
     * @param int $initial_shipping_cost
     * @return array =>
     * (
     *      'total_cost' => ...,
     *      'carrier_name' => ...,
     *      'partly_cost' => ...,
     *      'total_partly_costs' => ...,
     *      'delay' => ...
     * )
     */
    protected function requestToAuspostAPI(array $packages, Cart $cart)
    {
        $total_cost = array();
        $delay = '';
        $partly_cost = '';
        $array_partly_costs = array();
        $carrier = $this->getCarrier();
        $carrier_name = $carrier->id . ' ' . $carrier->name;
        foreach ($packages as $package) {
            // process calc total for case product have extra type is "Replace"
            if ($package['additional_charge_type'] == 1) {
                $total_cost[] = $package['additional_charges'];
                continue;
            }
            // request from static cache, otherwise request from db cache, finally make an api request
            $request_params = $this->getRequestParams($cart, $package);
            if (!$request_params) {
                return false;
            }
            // from cache
            $hash2 = md5(implode('_', $request_params));
            $cache2 = UrbitCache::getCache($hash2);
            $package_cost = false;
            if ($cache2 !== false && $cache2['total_charges'] >= 0.0) {
                $total_cost[] = $cache2['total_charges'];
                if (!empty($cache2['delay'])) {
                    $delay = $cache2['delay'];
                }
                if (!empty($cache2['partly_cost'])) {
                    $array_partly_costs[] = $cache2['partly_cost'];
                }
                continue;
            }
            // set default service with parcel
            if (1) {
                $package_response = $this->getRespondApi($request_params);
            } else {
                $this->enableDebugMode($package_response, $request_params);
            }
            if (!empty($package_response['total_cost'])) {
                $package_cost = $package_response['total_cost'];

                // Fix: additional_charges is not applied.
                // From the API:
                // [additional_charges]: Optional. The monetary value of the extra cover,
                //represented in dollar value only.
                // Integer only, no dollar sign, decimal or comma accepted.
                // This parameter is only applicable when the suboption_code is AUS_SERVICE_OPTION_EXTRA_COVER
                // But service code of the carrier Registered Post with Delivery Confirmation is defined as"
                // AUS_PARCEL_REGULAR+AUS_SERVICE_OPTION_REGISTERED_POST+AUS_SERVICE_OPTION_DELIVERY_CONFIRMATION
                // It means, suboption_code = AUS_SERVICE_OPTION_DELIVERY_CONFIRMATION
                // There is no way to pass this option as an array
                if (!empty($request_params['additional_charges']) && (int)$request_params['additional_charges'] > 0) {
                    $package_cost += (int)$request_params['additional_charges'];
                }
                $delay = !empty($package_response['delivery_time']) ? $package_response['delivery_time'] : '';
                $partly_cost = $this->getPartlyCosts($package_response['costs']);
            } else {
                return false;
            }
            if (!UrbitCache::saveCache(
                $hash2,
                $package_cost,
                $request_params,
                $package_response,
                $delay,
                $carrier_name,
                $partly_cost
            )
            ) {
                return false;
            }
            if ($package_cost !== false) {
                $total_cost[] = $package_cost;
            }
            $array_partly_costs[] = $partly_cost;
        }

        return array(
            'total_cost'         => $total_cost,
            'carrier_name'       => $carrier_name,
            'delay'              => $delay,
            'partly_cost'        => $partly_cost,
            'total_partly_costs' => $this->calculateTotalCost($this->filterOutputCosts($array_partly_costs)),
        );
    }

    /**
     * Get request params
     * @param Cart $cart
     * @param array $package
     * @return boolean || array request data
     */
    protected function getRequestParams($cart, $package)
    {
        if ($this->isCarrierCompare()) {
            // carrier compare case
            $country_code = Country::getIsoById(trim($this->context->cookie->id_country));
            $postcode = empty($this->context->cookie->postcode) ? null : $this->context->cookie->postcode;
        } elseif (!empty($cart->id_address_delivery)) {
            $address = new Address($cart->id_address_delivery);
            $postcode = $address->postcode;
            $country_code = Country::getIsoById($address->id_country);
        } elseif ($this->isProductEstimation()) {
            // product shipping rate
            $country_code = Country::getIsoById(trim($this->context->cookie->id_country));
            $postcode = empty($this->context->cookie->postcode) ? null : $this->context->cookie->postcode;
        } else {
            return false;
        }

        $service_area = $this->getServiceArea($country_code);
        $service_codes = $this->_service_code;
        // by defaut, service_code/ service_option/ service_sub_option are separated by '+'
        $request_data = array();
        $request_data['service_area'] = $service_area; // for internal only
        $request_data['from_postcode'] = Configuration::get('URBIT_CARRIER_POSTAL_CODE');
        $request_data['weight'] = $package['weight'];
        $request_data['service_code'] = $service_codes[0];

        if (!empty($service_codes[1])) {
            $request_data['option_code'] = $service_codes[1];
        }
        $request_data['additional_charges'] = $package['additional_charges'];
        if (UrbitExtraCover::isExtraCoverService($this->_service_code)) {
            $request_data['extra_cover'] = UrbitExtraCover::getExtraCover($this->id_carrier);
        }

        // domestic param only
        if ($service_area == 'domestic') {
            $request_data['to_postcode'] = $postcode;
            $request_data['length'] = $package['length'];
            $request_data['width'] = $package['width'];
            $request_data['height'] = $package['height'];
            if (!empty($service_codes[2])) {
                $request_data['suboption_code'] = $service_codes[2];
            }
        } else {
            $request_data['country_code'] = $country_code;
        }

        return $request_data;
    }

    /**
     * function get respond API
     * @param array $request_params
     * @return array
     */
    protected function getRespondApi($request_params)
    {
        $option_code = isset($request_params['option_code']) ? $request_params['option_code'] : '';
        $suboption_code = isset($request_params['suboption_code']) ? $request_params['suboption_code'] : '';
        $extra_cover = isset($request_params['extra_cover']) ? $request_params['extra_cover'] : '';
        $service = '';
        $package_response = array();
        switch ($request_params['service_area']) {
            case 'domestic':
            case 'international':
                break;
            default:
                break;
        }

        if (!empty($service)) {
            $service->setOutputFormat(UrbitOutput::XML_FORMAT);
            $package_response = $service->getShippingCost();
        }

        return $package_response;
    }

    /**
     * function enable show debug mode
     * @param array or json $package_response
     * @param array $initial_shipping_cost
     */
    protected function enableDebugMode($package_response, $request_params)
    {
        if (Configuration::get('DEBUG_MODE')) {
            echo '<pre>Request:<br/>';
            print_r($request_params);
            echo 'Response:<br/>';
            print_r($package_response);
            echo '</pre>';
        }
    }

    /**
     * function get partly costs
     * return $partly_cost
     * [cost] =>
     *     Array (
     *         [0] => Array(
     *             [cost] => 6.95,
     *             [item] => Parcel Post
     *         )
     *             ......
     *     )
     */
    public function getPartlyCosts($partly_cost)
    {
        return !empty($partly_cost) ? $partly_cost : array();
    }

    /**
     * function set partly costs by id carrier
     * @param array $partly_cost
     * Array (
     *          [id_carrier] => string json}
     *          ...
     * )
     */
    public function setPartlyCosts($partly_cost)
    {
        $this->partly_costs[$this->id_carrier] = $partly_cost;
    }

    /**
     * function calculator total cost of multil package.
     * @var array $total_costs
     * @return array total costs
     */
    protected function calculateTotalCost($total_costs)
    {
        $element_cost = array();
        $i = 0;
        foreach ($total_costs as $costs) {
            if ($i == 0) {
                $element_cost = $costs['cost'];
            } else {
                $element_cost = $this->mergePartlyCost($element_cost, $costs['cost']);
            }
            $i++;
        }

        return array('cost' => $element_cost);
    }

    /**
     * function merge 2 arrays to array
     * @var array $array1
     * @var array $array2
     * @return array total partly cost
     */
    protected function mergePartlyCost($array1, $array2)
    {
        $merge_party_costs = array();
        foreach ($array1 as $key1) {
            foreach ($array2 as $key2) {
                if ($key1['item'] === $key2['item']) {
                    $merge_party_costs[] = array(
                        'cost' => $key1['cost'] + $key2['cost'],
                        'item' => $key1['item'],
                    );
                }
            }
        }

        return $merge_party_costs;
    }

    /**
     * function synchronize array multi costs and single cost
     * @var array $partly_costs
     * @return array partly_cost
     */
    public function filterOutputCosts($partly_costs)
    {
        $format_output_costs = array();
        foreach ($partly_costs as $costs) {
            if (!is_array($costs)) {
                $costs = Tools::jsonDecode($costs, true);
            }
            if (is_array($costs) && !empty($costs)) {
                foreach ($costs as $cost) {
                    $array_cost = array();
                    foreach ($cost as $key => $value) {
                        if (is_numeric($key)) {
                            $array_cost[] = array(
                                'cost' => $value['cost'],
                                'item' => $value['item'],
                            );
                        } else {
                            $array_cost[] = array(
                                'cost' => $cost['cost'],
                                'item' => $cost['item'],
                            );
                            break;
                        }
                    }
                    $format_output_costs[] = array('cost' => $array_cost);
                }
            }
        }

        return $format_output_costs;
    }

    /**
     * Update urbit rate service code when user update carrier
     * @param array $params
     * array(
     *        'id_carrier' => ...,
     *        'carrier' => Carrier object
     * )
     * @return boolean
     */
    public function hookActionCarrierUpdate($params)
    {
        if ((int)($params['id_carrier']) != (int)($params['carrier']->id)) {
            // update carrier to warehouse
            $id_warehouse = (int)UrbitCarrier::getWarehouseByIdCarrier($params['id_carrier']);
            if ($id_warehouse) {
                UrbitCarrier::updateWarehouseCarrier(
                    $id_warehouse,
                    (int)$params['carrier']->id,
                    (int)$params['id_carrier']
                );
            }

            // get list services from model
            $selected_service = UrbitRateServiceCode::getSelectedService($params['id_carrier']);
            // update service code
            if (!empty($selected_service)) {
                $object_service_code = new UrbitRateServiceCode($selected_service['id_urbit_rate_service_code']);
                if (Validate::isLoadedObject($object_service_code)) {
                    $object_service_code->id_carrier = (int)($params['carrier']->id);
                    $object_service_code->id_carrier_history = pSQL(
                        $selected_service['id_carrier_history'] . '|' . (int)($params['carrier']->id)
                    );
                    $object_service_code->active = (int)$params['carrier']->active;
                    if ($object_service_code->save()) {
                        return true;
                    }

                    return false;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Convert array multil partly costs to string partly cost
     * @var array $partly_costs
     * @return array(
     *        'id_carrier'=>'string partly cost'
     *    )
     */
    public function processOutputCosts($partly_costs)
    {
        if (empty($partly_costs)) {
            return false;
        }
        $carrier_partly_costs = array();
        foreach ($partly_costs as $id_carrier => $costs) {
            $costs = Tools::jsonDecode($costs, true);
            if (is_array($costs) && !empty($costs)) {
                foreach ($costs as $cost) {
                    $string_partly_cost = array();
                    foreach ($cost as $key => $value) {
                        if (is_numeric($key)) {
                            $string_partly_cost[] = array($value['item'] => $value['cost']);
                        } else {
                            $string_partly_cost[] = array($cost['item'] => $cost['cost']);
                        }
                    }
                    $this->context->smarty->assign(array(
                        'array_partly_costs' => $string_partly_cost,
                    ));
                    $carrier_partly_costs[$id_carrier] = $this->display($this->name . '.php', 'showpartlycost.tpl');
                }
            }
        }

        return $carrier_partly_costs;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink($this->class_controller_admin));
    }

    /**
     * add check and get button
     */
    public function hookDisplayProductButtons()
    {
    }

    public function hookdisplayCarrierList($params)
    {
        $url = $this->context->link->getAdminLink('ShippingOptions', false) . '&configure=' . $this->name;
        $base = Tools::getHttpHost(true) . __PS_BASE_URI__;
        $user_id = (int)$this->context->cookie->id_customer;
        $cart = new Cart($this->context->cart->id);
        $urbitCarrier = new UrbitCarrier();
        $urbitStoreApi = new UrbitStoreApi();
        $default_carrier_id = $urbitCarrier->getUserUrbCarrier();
        $user_delivery_address = $urbitCarrier->getUserAddress($cart->id_address_delivery);
        $user_billing_address = $urbitCarrier->getUserAddress($cart->id_address_invoice);
        $ret_zip_code_check = $urbitStoreApi->checkAddressDeliverable($user_delivery_address);
        $active_carriers = $urbitCarrier->getActiveCarriers($this->name);

        $carrier_id = '';
        foreach ($active_carriers as $val) {
            if ($default_carrier_id == $val['id_carrier']) {
                $carrier_id = $val['id_carrier'];
            }
        }

        $this->smarty->assign(array(
            'user_delivery_address'    => $user_delivery_address,
            'user_billing_address'     => $user_billing_address,
            'zip_code_deliverable'     => $ret_zip_code_check['deliverable'],
            'zip_code_deliverable_msg' => $ret_zip_code_check['deliverable_msg'],
            'logged_user_id'           => $user_id,
            'user_email'               => $this->context->cookie->email,
            'carrier_id'               => $carrier_id . ',',
            'carrier_img_id'           => $base . 'img/s/' . $carrier_id . '.jpg',
            'base_url'                 => $base,
            'ajax_url'                 => $url

        ));

        return $this->display($this->name . '.php', 'shipping_sp_time.tpl');
    }

    /**
     * Hook for create checkout (send POST request to Urb-it API)
     * @param $params
     */
    public function hookDisplayBeforeCarrier($params)
    {
        $bodyForRequest = array(
            'cart_reference' => $this->context->cookie->cartIdFromApi,
        );

        $apiResponse = UrbitStoreApi::createCheckout($bodyForRequest);

        if (property_exists($apiResponse->args, "id")) {
            $this->context->cookie->checkoutIdFromApi = $apiResponse->args->id;
        }
    }

    /**
     * Hook for create cart (send POST request to Urb-it API)
     * @param $summary
     * @return mixed
     */
    public function hookActionCartSummary($summary)
    {
        $cart = new Cart($this->context->cart->id);
        $cartItems = $cart->getProducts();

        $result = array();
        $apiItems = array();

        foreach ($cartItems as $item) {
            $apiItems[] = array(
                'sku'      => $item['id_product'],
                'name'     => $item['name'],
                'vat'      => $this->priceFormat($item['rate']),
                'price'    => $this->priceFormat($item['price_with_reduction']),
                'quantity' => (int)$item['cart_quantity'],
            );
        }

        $result['items'] = $apiItems;

        $apiResponse = UrbitStoreApi::createCart($result);


        if (property_exists($apiResponse->args, "id")) {
            $this->context->cookie->cartIdFromApi = $apiResponse->args->id;
        }

        return $summary;
    }

    /**
     * format price for Urb-it API (ex. 59.99 => 5999 integer)
     * @param $price
     * @return float|int
     */
    protected function priceFormat($price)
    {
        return number_format((float)$price, 2, '.', '') * 100;
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $objOrder = $params['objOrder']; // get order object from the orderconfirm hook

        $order_cart_carrier = Db::getInstance()
            ->executeS(
                'SELECT  id_carrier FROM ' .
                _DB_PREFIX_ .
                'carrier WHERE external_module_name = "urbit" ORDER BY id_carrier DESC LIMIT 1'
            );

        if ($order_cart_carrier[0]["id_carrier"] == $objOrder->id_carrier) {
            $upservice = Db::getInstance()
                ->execute(
                    'UPDATE ' .
                    _DB_PREFIX_ .
                    'urbit_order_cart SET `id_carrier` = ' .
                    $objOrder->id_carrier .
                    ' WHERE id_cart = ' .
                    $objOrder->id_cart
                );
        }

        // get order information saved in urbit_order_cart if the customer select urbit as a shipping option
        $ret_order_cart = UrbitCart::getOrderCart($objOrder->id_cart, $objOrder->id_carrier);

        $preparationEndTime = $this->getPreparationEndDate();

        if (!empty($ret_order_cart)) {
            UrbitCart::updateOrderId($objOrder->id, $objOrder->id_cart);
            UrbitCart::updateCheckoutId($this->context->cookie->checkoutIdFromApi, $objOrder->id_cart);
            UrbitCart::updatePreparationEndTime($preparationEndTime, $objOrder->id_cart);
        }
    }

    protected function getPreparationEndDate()
    {
        $nowTime = new DateTime(null, new DateTimeZone("UTC"));
        $nowTimestamp = strtotime($nowTime->format('Y-m-d H:i:s'));
        $preparationTime = Configuration::get('URBIT_ADMIN_AUTO_VALIDATION_TIME');

        if ($preparationTime) {
            $nowTimestamp += (int)$preparationTime * 60;
        }

        $preparationEndTime = new DateTime();
        $preparationEndTime->setTimestamp($nowTimestamp);

        return $preparationEndTime->format("Y-m-d H:i:s");
    }

    /**
     * Hook for order status change
     * @param $params
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        $orderId = $params['id_order'];
        $orderStatusId = $params['newOrderStatus']->id;

        $this->checkOrdersStatusForUpdateCheckout($orderId, $orderStatusId);
    }

    /**
     * Find orders, which should be updated by Urb-it API (PUT request)
     * function for CRON
     */
    public function checkOrdersForUpdateCheckouts()
    {
        $unsentCarts = UrbitCart::getUnsentCarts();
        $nowTimestamp = strtotime(date("Y-m-d H:i:s"));

        foreach ($unsentCarts as $cart) {
            $deliveryDate = strtotime($cart['delivery_time']);

            if ($deliveryDate <= $nowTimestamp) {
                $this->sendUpdateCheckout($cart['id_urbit_order_cart']);
            }
        }
    }

    /**
     * Check current order status
     * If order status == config trigger status => send PUT to Urb-it with delivery information
     * @param $orderId
     * @param $orderStatusId
     */
    public function checkOrdersStatusForUpdateCheckout($orderId, $orderStatusId)
    {
        $configOrderTriggerValue = Configuration::get('URBIT_ADMIN_STATUS_TRIGGER');

        if ($configOrderTriggerValue && (int)$configOrderTriggerValue == $orderStatusId) {
            $cart = UrbitCart::getUrbitCartByOrderId($orderId);

            if (!empty($cart) && $cart[0]['is_send'] == "false") {
                $this->sendUpdateCheckout($cart[0]['id_urbit_order_cart']);
            }
        }
    }

    /**
     * Send delivery information to Urb-it by PUT request
     * @param $urbitCartId
     */
    protected function sendUpdateCheckout($urbitCartId)
    {
        $cart = UrbitCart::getUrbitCart($urbitCartId);

        if (!empty($cart)) {
            $checkoutId = $cart[0]['checkout_id'];

            if ($checkoutId == "") {
                return;
            }

            //$deliveryDate = DateTime::createFromFormat('Y-m-d H:i:s', $cart[0]['delivery_time']);
            $deliveryDate = new DateTime($cart[0]['delivery_time'], new DateTimeZone("CET"));
            $deliveryDate->setTimezone(new DateTimeZone('UTC'));
            $formattedDeliveryDate = $deliveryDate->format('Y-m-d\TH:i:sP');

            $requestArray = array(
                'delivery_time' => $formattedDeliveryDate,
                'message'       => $cart[0]['delivery_advise_message'],
                'recipient'     => array(
                    'first_name'   => $cart[0]['delivery_first_name'],
                    'last_name'    => $cart[0]['delivery_last_name'],
                    'address_1'    => $cart[0]['delivery_street'],
                    'address_2'    => "",
                    'city'         => $cart[0]['delivery_city'],
                    'postcode'     => $cart[0]['delivery_zip_code'],
                    'phone_number' => $cart[0]['delivery_is_gift'] ? $cart[0]['delivery_gift_receiver_phone'] :
                        $cart[0]['delivery_contact_phone'],
                    'email'        => $cart[0]['delivery_contact_mail']
                )
            );

            //send order information to API to set delivery information
            $ret_create_order = UrbitStoreApi::updateCheckout($checkoutId, $requestArray);

            UrbitCart::updateSentFlag('true', $cart[0]['id_urbit_order_cart']);

            if (isset($ret_create_order->httpCode)) {
                UrbitCart::updateResponseCode($ret_create_order->httpCode, $cart[0]['id_urbit_order_cart']);
            }
        }
    }

    /**
     *
     * @return type
     */
    public function hookDisplayProductTab()
    {
        return $this->hookDisplayRightColumnProduct();
    }

    /**
     * hook into Back Office header position
     * @return assign template
     */
    public function hookDisplayRightColumnProduct()
    {
        if (!Module::isEnabled($this->name)) {
            return;
        }

        if ($this->isVirtualProduct()) {
            return;
        }

        if (!$this->isStockAvailable()) {
            return;
        }

        $this->context->controller->addJS(array(
            $this->_path . 'views/js/product_shipping_cost.js',
        ));

        if (isset($this->context->cookie->id_country) && $this->context->cookie->id_country > 0) {
            $id_country = (int)$this->context->cookie->id_country;
        }
        if (!isset($id_country)) {
            $id_country = (isset($this->context->customer->geoloc_id_country) ?
              (int)$this->context->customer->geoloc_id_country :
                (int)Configuration::get('PS_COUNTRY_DEFAULT')
              );
        }
        if (isset($this->context->customer->id)
            && $this->context->customer->id
            && isset($this->context->cart->id_address_delivery) &&
            $this->context->cart->id_address_delivery) {
            $address = new Address((int)($this->context->cart->id_address_delivery));
            $id_country = (int)$address->id_country;
        }

        if (isset($this->context->cookie->id_state) && $this->context->cookie->id_state > 0) {
            $id_state = (int)$this->context->cookie->id_state;
        }
        if (!isset($id_state)) {
            $id_state = (isset($this->context->customer->geoloc_id_state) ?
              (int)$this->context->customer->geoloc_id_state : 0
            );
        }
    }

    /**
     * check if the current product is virtual
     * @return boolean
     */
    protected function isVirtualProduct()
    {
        $product = $this->getProduct();

        return !empty($product) && $product->is_virtual;
    }

    /**
     * get an instance of the current product if any
     * @return Product
     */
    protected function getProduct()
    {
        if (empty($this->product)) {
            if (method_exists($this->context->controller, 'getProduct')) {
                // since PS 1.5.6.0
                $this->product = $this->context->controller->getProduct();
            } else {
                $id_product = (int)Tools::getValue('id_product', 0);
                if ($id_product) {
                    $product = new Product($id_product, true);
                    $this->product = Validate::isLoadedObject($product) ? $product : null;
                }
            }
        }

        return $this->product;
    }

    /**
     * Check if the current product is available for order
     * @return boolean
     */
    protected function isStockAvailable()
    {
        $product = $this->getProduct();

        return !empty($product) &&
          ($product->quantity > 0
            || $product->isAvailableWhenOutOfStock((int)$product->out_of_stock)
        );
    }

    /*
     * display carriers in checkout shipping
     */

    /**
     *
     * @return type
     */
    public function hookDisplayLeftColumnProduct()
    {
        return $this->hookDisplayRightColumnProduct();
    }

    /**
     *
     * @return type
     */
    public function hookDisplayProductTabContent()
    {
        return $this->hookDisplayRightColumnProduct();
    }

    /**
     * Dedicated callback to upgrading process
     * @param type $version
     * @return boolean
     */
    public function upgrade($version)
    {
        $flag = true;
        switch ($version) {
            case '2.6':
                if ($this->name !== 'urbitbasic') {
                    $flag = $this->registerHook('extraCarrier');
                }
                break;
            case '2.7':
                if ($this->name !== 'urbitbasic') {
                    // Feature: show party costs
                    $sql = 'ALTER TABLE `' .
                      _DB_PREFIX_ .
                      'urbit_cache` ADD COLUMN `partly_cost` TEXT AFTER `date_upd`';
                    $flag = (
                      Configuration::deleteByName('URBIT_AFTER_DELAY')
                      && Configuration::deleteByName('URBIT_BEFORE_DELAY')
                      && Configuration::deleteByName('URBIT_EXTRA_DELAY') && Configuration::updateValue(
                          'URBIT_SHOW_DELAY',
                          1
                      )
                           && Configuration::updateValue(
                               'URBIT_SHOW_PARTLY_COST',
                               1
                           ) && Db::getInstance()->query($sql)
                    );
                }
                break;
            case '2.7.0.5':
                if ($this->name !== 'urbitbasic') {
                    $flag = (
                        Configuration::updateValue(
                            'URBIT_FLEXIBLE_PACKAGE',
                            0
                        ) && Configuration::updateValue(
                            'URBIT_PACKAGE_MARGIN',
                            0
                        ) && Configuration::updateValue(
                            'URBIT_PLACE_EXTRA_COVER_FORM',
                            'popup_center'
                        )
                    );
                }
                break;
            case '2.8':
                if ($this->name !== 'urbitbasic') {
                    $sql_alter = 'ALTER TABLE `' .
                      _DB_PREFIX_ .
                      'urbit_rate_service_code` ADD COLUMN `delay` VARCHAR(128) AFTER `service`';
                    $sql_update = 'UPDATE `' .
                       _DB_PREFIX_ .
                       'carrier_lang` AS `cl`
                       JOIN `' .
                        _DB_PREFIX_ .
                        'urbit_rate_service_code` AS `arsc`
                        ON (`cl`.`id_carrier` = `arsc`.`id_carrier`)
                        SET `cl`.`delay` = `arsc`.`delay`';
                    $flag = (Db::getInstance()->query($sql_alter) && Db::getInstance()
                            ->query(
                                $sql_update
                            )
                                && $this->registerHook('displayRightColumnProduct')
                                && $this->registerHook('actionObjectCarrierUpdateAfter')
                    );
                }
                break;
            case '2.9':
                $flag = $this->updateModuleTab();
                if (Configuration::get('PS_STOCK_MANAGEMENT')) {
                    $urbit_services_code = new UrbitRateServiceCode();
                    $service_codes = $urbit_services_code->getAllServiceCodes();
                    $ids_carries = array();
                    foreach ($service_codes as $service_code) {
                        if ($service_code['id_carrier']) {
                            $ids_carries[] = $service_code['id_carrier'];
                        }
                    }

                    $warehouses = Warehouse::getWarehouses();
                    foreach ($warehouses as $warehouse) {
                        $wh = new Warehouse($warehouse['id_warehouse']);
                        // add more carrier of current warehouse
                        $carriers = $wh->getCarriers(true);
                        if (!empty($carriers)) {
                            foreach ($carriers as $id_carrier) {
                                $ids_carries[] = $id_carrier;
                            }
                        }

                        $ids_carries = array_unique($ids_carries);
                        if (Validate::isLoadedObject($wh) && !empty($ids_carries)) {
                            $wh->setCarriers($ids_carries);
                        }
                    }
                    $flag = true;
                }
                break;
            default:
                break;
        }

        return $flag;
    }

    /**
     * Update old tab to new tab
     */
    protected function updateModuleTab()
    {
        $flag = true;
        $id_tab = (int)Tab::getIdFromClassName(self::CLASS_CONTROLLER_SETTINGS);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                $tab->class_name = $this->class_controller_admin;
                $name = array();
                $languages = Language::getLanguages(false);
                if (!empty($languages)) {
                    foreach (Language::getLanguages(false) as $language) {
                        $name[$language['id_lang']] = $this->displayName;
                    }
                }

                $tab->name = $name;
                $flag = $tab->update();
            }
        }

        return $flag;
    }

    /**
     * return list avaible carrier + shipping cost
     * @param int $id_product
     * @param int $id_attribute_product
     * @param int $qty
     * @param int $id_country
     * @param int $id_state
     * @param string $postcode
     * @return html
     */
    public function productGetShippingCost($id_product, $id_attribute_product, $qty, $id_country, $id_state, $postcode)
    {
        // validate enable module
        if (!Module::isEnabled($this->name)) {
            return false;
        }
        // validate id_product and qty
        if ($id_product === 0 || $qty === 0) {
            return false;
        }

        $cart = $this->getCart($id_product, $id_attribute_product, $qty);
        if (!validate::isLoadedObject($cart)) {
            return false;
        }

        //  $shipping_cost[] = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
        $group = null;
        if (!empty($this->context->customer->id)) {
            $customer = new Customer($this->context->customer->id);
            $group = $customer->getGroups();
        }

        $shipping_costs = $this->getShippingCostCarriers($id_country, $group, $cart, $id_state, $postcode);
        if (empty($shipping_costs)) {
            return false;
        }
        $this->context->smarty->assign(array(
            'shipping_costs' => $shipping_costs,
        ));
        $template_path = _PS_MODULE_DIR_ . $this->name . '/views/templates/front/shipping_cost.tpl';
        exit($this->context->smarty->fetch($template_path));
    }

    /**
     * Add new or Update Cart
     * @param int $id_product
     * @param int $qty
     * @return \Cart
     */
    protected function getCart($id_product, $id_attribute_product, $qty)
    {
        require_once(dirname(__FILE__) . '/models/UrbitCart.php');

        if (empty($id_product) || (int)$id_product <= 0) {
            return;
        }

        $cart = null;
        $id_cart = UrbitCart::getCart($id_product);
        if (empty($id_cart)) {
            $cart = clone $this->context->cart;
            $cart->id = null;
            $cart->id_address_delivery = 0;
            $cart->id_address_invoice = 0;
            $cart->id_customer = 0;

            $cart->add();
            if ($cart->id) {
                UrbitCart::setCart($id_product, $cart->id);
            }
        } else {
            $cart = new Cart((int)$id_cart);
            if (Validate::isLoadedObject($cart)) {
                $products = $cart->getProducts();
                if (count($products)) {
                    foreach ($products as $product) {
                        $cart->deleteProduct($product['id_product']);
                    }
                }
            }
        }
        $cart->updateQty($qty, (int)$id_product, (int)$id_attribute_product);

        return $cart;
    }

    /**
     *
     * @param int $id_country
     * @param int $id_state
     * @param int $postcode
     * @param int $group
     * @param Cart $cart
     * @return array
     */
    protected function getShippingCostCarriers($id_country, $group, Cart $cart, $id_state = 0, $postcode = 0)
    {
        $group = $group;
        // cookie saving/updating
        $this->context->cookie->id_country = $id_country;
        if ($id_state != 0) {
            $this->context->cookie->id_state = $id_state;
        }
        if ($postcode != 0) {
            $this->context->cookie->postcode = $postcode;
        }
        $id_zone = 0;
        if ($id_state != 0) {
            $id_zone = State::getIdZone($id_state);
        }
        if (!$id_zone) {
            $id_zone = Country::getIdZone($id_country);
        }
        $iso_code = Country::getIsoById($id_country);
        // Need to set the infos for carrier module !
        $this->context->cookie->id_country = $id_country;
        $this->context->cookie->id_state = $id_state;
        $this->context->cookie->postcode = $postcode;
        $carriers = $this->getCarriersList($id_zone, $iso_code);

        $shipping_cost_carriers = UrbitCarrier::getAvailableCarriers($carriers, $cart, $id_zone);

        return (count($shipping_cost_carriers) ? $shipping_cost_carriers : array());
    }

    /**
     * Get carrier depend iso_code
     * iso_code = au -> carrier of domestic else carrier of interational
     * @param int $id_zone
     * @param string $iso_code
     * @return array
     */
    protected function getCarriersList($id_zone, $iso_code)
    {
        $carriers = UrbitCarrier::getCarriers(
            $this->context->language->id,
            true,
            false,
            (int)$id_zone,
            array(Configuration::get('PS_UNIDENTIFIED_GROUP')),
            4
        );

        foreach ($carriers as $key => $carrier) {
            $service_code = UrbitRateServiceCode::getServiceCode($carrier['id_carrier']);
            if ($iso_code === Tools::strtoupper($this->country_iso)) {
                if (Tools::strtolower(Tools::substr($service_code[0], 0, 4)) == 'intl') {
                    unset($carriers[$key]);
                }
            } else {
                if (Tools::strtolower(Tools::substr($service_code[0], 0, 4)) != 'intl') {
                    unset($carriers[$key]);
                }
            }
        }

        return $carriers;
    }

    /**
     * Get states by Country id, called by the ajax process
     * @return Json
     */
    public function getState($id_country)
    {
        $states = State::getStatesByIdCountry($id_country);
        exit(Tools::jsonEncode($states));
    }

    /**
     * save module infor to configuration
     * @param string $module_name
     * @return Boolean
     */
    protected function setModuleInfo()
    {
        $module_info = new HsModuleInfo($this);
        $current_version = $module_info->get('version');

        if ($this->version !== $current_version) {
            $module_info->set('version', $this->version);
            $module_info->set('last_update', strtotime('now'));
        }

        return $module_info->update();
    }

    /**
     * delete module infor from configuration
     * @return Boolean
     */
    protected function deleteModuleInfo()
    {
        $module_info = new HsModuleInfo($this);

        return $module_info->delete();
    }

    /**
     * Get module info to header AdminModule.
     * @return string
     */
    protected function getWarningLicenceModule()
    {
        $licence_api = new LicenceApi($this->API_KEY, $this);
        $paramater = $this->getLicenceParameter();
        $validate_licence = $licence_api->validateLicence($paramater);
        $new_versions = $licence_api->getNewVersions($paramater);
        $news = $licence_api->getNews($paramater);

        $result = '';
        if (!empty($validate_licence['success']) &&
          $validate_licence['success'] &&
          !empty($new_versions['success'])
          && $new_versions['success'] &&
          !empty($news['success']) && $news['success']
        ) {
            $this->context->smarty->assign(array(
                'new_versions'     => $new_versions,
                'validate_licence' => $validate_licence,
                'news'             => $news,
            ));
            $result = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ .
                 $this->name .
                 '/views/templates/admin/urbit/admin_module.tpl'
            );
        }

        return $result;
    }

    /**
     * assign module info to LicenceParamater
     * @return LicenceParameter
     */
    public function getLicenceParameter()
    {
        $parameter = new LicenceParameter();
        $info_module = new HsModuleInfo($this);
        $parameter->domain = $info_module->get('domain');
        $parameter->module_name = $info_module->get('module_name');
        $parameter->version = $info_module->get('version');
        $parameter->install_date = $info_module->get('install_date');
        $parameter->last_update = $info_module->get('last_update');
        $parameter->demo = 1;

        return $parameter;
    }
}
