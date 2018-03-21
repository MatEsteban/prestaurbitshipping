<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

require_once(dirname(__FILE__) . '/../../classes/Node.php');
require_once(dirname(__FILE__) . '/../../models/UrbitRateServiceCode.php');
require_once(dirname(__FILE__) . '/../../models/UrbitValidate.php');
require_once(dirname(__FILE__) . '/../../models/UrbitRateConfig.php');

/**
 * Class AdminUrbitAbstract
 *
 * @property UrbitAbstract $module
 */
class AdminUrbitAbstract extends ModuleAdminController
{

    /**
     * array tree category
     * @var type
     */
    public $option_list = array();
    /**
     * All jQuery UI components which should be loaded
     * @var array
     */
    protected $jquery_ui_components = array(
        'ui.tabs',
        'ui.autocomplete'
    );
    /**
     * All jQuery plugins which should be loaded
     * @var array
     */
    protected $jquery_plugins = array(
        'cookie-plugin'
    );
    /**
     * All javascript files which should be loaded
     * @var array
     */
    protected $module_media_js = array(
        'settings.js',
        'jquery.validate.js',
        'package.js'
    );
    /**
     * All css files which should be loaded
     * @var array
     */
    protected $module_media_css = array(
        'settings.css'
    );
    protected $json_data = array(
        'success' => false,
        'message' => null,
        'data' => array()
    );
    /**
     * Array configuration key using setting urbit
     * @type key => type data
     */
    protected $configuration_keys = array(
        'PS_WEIGHT_UNIT' => 'isString',
        'URBIT_CARRIER_POSTAL_CODE' => 'isString',
        'URBIT_GST' => 'isInt',
        'DEBUG_MODE' => 'isInt',
        'URBIT_FLENGTH' => 'isInt',
        'URBIT_FHEIGHT' => 'isInt',
        'URBIT_FWIDTH' => 'isInt'
    );
    // product carrier filter
    protected $_category;
    protected $id_current_category;

    /**
     * Set Media file include when controller called
     */
    public function setMedia()
    {
        parent::setMedia();
        // fix error version 1.5.5 not call file ui.menu.js
        if (!array_key_exists('ui.menu', Media::$jquery_ui_dependencies['ui.autocomplete']['dependencies'])) {
            Media::$jquery_ui_dependencies['ui.autocomplete']['dependencies'][] = 'ui.menu';
            Media::$jquery_ui_dependencies['ui.menu'] = array(
                'fileName' => 'jquery.ui.menu.min.js',
                'dependencies' => array(),
                'theme' => false
            );
        }
        // Add plugins and scripts here, to make sure they are loaded after jQuery core.
        if (!empty($this->jquery_plugins)) {
            $this->addJqueryPlugin($this->jquery_plugins);
        }
        if (!empty($this->jquery_ui_components)) {
            $this->addJqueryUI($this->jquery_ui_components);
        }
        if (!empty($this->module_media_js) && is_array($this->module_media_js)) {
            $js_files = array();
            foreach ($this->module_media_js as $js_file) {
                $js_file = $this->module->getJsPath() . $js_file;
                if (version_compare(_PS_VERSION_, '1.6') === -1 || version_compare(_PS_VERSION_, '1.7') === 1) {
                    $js_file = $js_file . '?urbit=' . $this->module->version;
                }
                $js_files[] = $js_file;
            }
            $this->addJS($js_files);
        }
        if (!empty($this->module_media_css) && is_array($this->module_media_css)) {
            $css_files = array();
            foreach ($this->module_media_css as $css_file) {
                $css_file = $this->module->getCssPath() . $css_file;
                if (version_compare(_PS_VERSION_, '1.6') === -1 || version_compare(_PS_VERSION_, '1.7') === 1) {
                    $css_file = $css_file . '?urbit=' . $this->module->version;
                }
                $css_files[] = $css_file;
            }
            $this->addCSS($css_files);
        }
    }

    /**
     * Init header for Controller
     * Call function build menu (tabs)
     */
    public function initHeader()
    {
        parent::initHeader();
        $this->context->smarty->assign(array(
            'menu_items' => $this->getMenuTree()->getChildren(), // menu tree rendering
        ));
    }

    /**
     * Recursively menu tree of Shelf Tickets screen
     * @return Node
     */
    protected function getMenuTree()
    {
        $root = new Node('Root');
        // Tab general setting
        $link = $this->module->getTargetUrl($this->module->class_controller_admin, 'General');
        $root->addChild(new Node('General', $link));

        // Categories setting
        $link = $this->module->getTargetUrl($this->module->class_controller_admin, 'Categories');
        $root->addChild(new Node('Categories', $link));

        // Products setting
        $link = $this->module->getTargetUrl($this->module->class_controller_admin, 'ProductsSetting');
        $root->addChild(new Node('Products', $link));

        // Tab help
        $link = $this->module->getTargetUrl($this->module->class_controller_admin, 'Help');
        $root->addChild(new Node('Help', $link));

        // return
        return $root;
    }

    /**
     * @see AdminControllerCore::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $new_ajax = html_entity_decode($this->context->link->getModuleLink('Urbit', 'ShippingOptions'));

        $this->context->smarty->assign(array(
            'module_logo' => $this->module->getPathUri() . 'logo.gif',
            'module_name' => $this->module->name,
            'version_name' => $this->module->displayName . '&nbsp' . $this->module->version,
            'base_url' => $this->module->getPathUri(),
            'ajax_url' => Tools::getHttpHost(true) . __PS_BASE_URI__,
            'new_ajax' => $new_ajax,

        ));
    }

    /**
     * Default action or any ajax action which is not defined displayAjaxActionName()
     * @see AdminControllerCore::displayAjax()
     */
    public function displayAjax()
    {
        echo $this->content = $this->getHelper()->generate();
        exit;
    }

    /**
     * A common helper to generate view, typically for ajax view
     * @param string $tpl (in format of template_name.tpl)
     * @return Helper
     */
    protected function getHelper($tpl = null)
    {
        $helper = new Helper();

        $helper->override_folder = $this->tpl_folder;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;

        if (empty($tpl)) {
            $action = Tools::getValue('action');
            if ($action) {
                $tpl = 'ajax-' . Tools::strtolower($action) . '.tpl';
            } else {
                $tpl = 'ajax.tpl';
            }
        }
        $helper->setTpl($tpl);
        return $helper;
    }

    /**
     * action: screen of general setting tab
     */
    public function ajaxProcessGeneral()
    {
        // get list weight unit
        $weight_unit = $this->getWeightUnit();

        //get all service code
        $service_obj = new UrbitRateServiceCode();

        $service_lists = $service_obj->getAllServiceCodes();
        // assign to template
        $this->context->smarty->assign(array(
            'weight_unit' => $weight_unit,
            'service_lists' => $service_lists,
            'img_path' => $this->module->getImagePath(),
            'config' => Configuration::getMultiple(array_keys($this->configuration_keys))
        ));
        // process save to database
        if (Tools::getValue('PS_WEIGHT_UNIT')) {
            // save configuation to datbase
            foreach ($this->configuration_keys as $config_name => $config_validate) {
                if ($config_name == 'URBIT_CARRIER_POSTAL_CODE') {
                    if (UrbitValidate::validateZipCode(Tools::getValue($config_name), $this->module->country_iso)) {
                        Configuration::updateValue($config_name, Tools::getValue($config_name));
                    }
                } else {
                    if (UrbitValidate::$config_validate(Tools::getValue($config_name))) {
                        Configuration::updateValue($config_name, Tools::getValue($config_name));
                    }
                }
            }
            // save services to databse
            if (Tools::getValue('service')) {
                $service = Tools::getValue('service');
                $services_all = Tools::getValue('service_all');
                $array_diff = array_diff($services_all, $service);
                $service_codes = new UrbitRateServiceCode();
                $service_codes->updateActiveServiceCodeAndCarrier($service, true);
                $service_codes->updateActiveServiceCodeAndCarrier($array_diff, false);
            }
            $this->json_data['success'] = true;
            exit();
        }
    }

    /**
     * get list all weight unit
     * @return array(
     *            'kg'    => 'KGS',
     *            'gr'    => 'Grams',
     *            'ton'   => 'Tons'
     * )
     */
    public function getWeightUnit()
    {
        $weight_unit = array(
            'kg' => 'Kgs',
            'gr' => 'Grams',
            'ton' => 'Tons'
        );
        return $weight_unit;
    }

    /*
     * get list category
     */

    public function ajaxProcessCategories()
    {
        $rate_config_obj = new UrbitRateConfig();
        // get list delivery service
        $delivery_service = $rate_config_obj->getAllService();
        // get a list option of categories
        $categories_select = $this->getChildCategories(Category::getCategories($this->context->language->id), 0);
        // get list  change
        //list all categories
        $list_category = $rate_config_obj->getListCategory();
        //assgin template
        $this->context->smarty->assign(array(
            'categories_select' => $categories_select,
            'delivery_service' => $delivery_service,
            'list_category' => $list_category
        ));
    }

    /**
     * get all child categories of a category
     * @param array $categories
     * @param integer $id_cate
     * @return array tree category
     * $this->option_list = array(
     * 'home',
     * 'home->laptop'
     * 'home->accsession'
     * 'home->mac'
     * )
     * */
    private function getChildCategories(array $categories, $id_cate)
    {
        if (isset($categories[$id_cate])) {
            foreach ($categories[$id_cate] as $id_category => $category) {
                if ($category['infos']['name'] != 'Root') {
                    $this->option_list[$id_category]['id_category'] = $category['infos']['id_category'];

                    $cat_obj = new Category($id_category);
                    //get parent of category
                    $parent_categories = $cat_obj->getParentsCategories($this->context->language->id);
                    //reverse order of array parent categories
                    $parent_categories = array_reverse($parent_categories);
                    // assign parent name category to array
                    $category = array();
                    foreach ($parent_categories as $parent_category) {
                        if (isset($parent_category['name'])) {
                            $category[] = $parent_category['name'];
                        }
                    }
                    $this->option_list[$id_category]['name'] = implode('>', $category);
                    unset($category);
                }
                $this->getChildCategories($categories, $id_category);
            }
        }

        return $this->option_list;
    }


    /**
     * Save settings of a category (add or edit)
     */
    public function ajaxProcessSaveCategorySetting()
    {
        //get id_category when submit form
        $id_category = Tools::getValue('id_category', 0);
        //get additional_charges when submit form
        $additional_charges = Tools::getValue('additional_charges', 0);
        // get add_to_or_replace when submit form
        $add_to_or_replace = Tools::getValue('type', 0);
        //get delivery_service when submit form
        $delivery_service = Tools::getValue('service', array());
        //get id_urbit_rate_config when submit form
        $id_urbit_rate_config = Tools::getValue('id', 0);
        if (empty($additional_charges) || empty($id_category) || empty($delivery_service)) {
            return;
        }

        // edit
        if ($id_urbit_rate_config) {
            $urbit_rate_config = new UrbitRateConfig($id_urbit_rate_config);
            if (!Validate::isLoadedObject($urbit_rate_config)) {
                return;
            }
        } else {
            $urbit_rate_config = new UrbitRateConfig();
        }
        $this->json_data['success'] = $urbit_rate_config->saveCategorySettings(
            $id_category,
            Configuration::get('PS_CURRENCY_DEFAULT'),
            $delivery_service,
            $add_to_or_replace,
            $additional_charges
        );
    }

    /**
     * Display ajax search result
     * @return json_data
     */
    public function displayAjaxSaveCategorySetting()
    {
        if (!empty($this->json_data) && is_array($this->json_data)) {
            echo Tools::jsonEncode($this->json_data);
        }
    }

    /**
     * display form Edit category
     * @return view html
     */
    public function displayAjaxEditCategory()
    {
        $id_urbit_rate_config = Tools::getValue('id');
        //list all category
        $rate_config_obj = new UrbitRateConfig($id_urbit_rate_config);
        // get list category setting
        $list_category = $rate_config_obj->getListCategory();
        // get list delivery service
        $delivery_service = $rate_config_obj->getAllService();
        // call to getChildCategories to get a list option of categories
        $categories_select = $this->getChildCategories(Category::getCategories($this->context->language->id), 0);
        // get array rate config
        $detail_rate_config = $rate_config_obj->getDetailCategory();
        //get Add To OR Replace
        $this->context->smarty->assign(array(
            'categories_select' => $categories_select,
            'delivery_service' => $delivery_service,
            'list_category' => $list_category,
            'service' => $detail_rate_config['services'],
            'obj_rate_config' => $detail_rate_config['rate_config']
        ));
        // render view
        echo $this->getHelper()->generate();
    }

    /**
     * Process delete object UrbitRateConfig exist id_urbit_rate_config
     * @return json data
     * */
    public function ajaxProcessDeleteCategory()
    {
        // get $id_urbit_rate_config
        $id_urbit_rate_config = Tools::getValue('id', 0);
        // process save category
        $rate_config_obj = new UrbitRateConfig($id_urbit_rate_config);
        if (Validate::isLoadedObject($rate_config_obj)) {
            if ($rate_config_obj->deleteUrbitRateConfig()) {
                $this->json_data['success'] = true;
            }
        } else {
            $this->json_data['success'] = false;
        }
    }

    /**
     * show message after delete category
     * */
    public function displayAjaxDeleteCategory()
    {
        if (!empty($this->json_data) && $this->json_data) {
            echo Tools::jsonEncode($this->json_data);
        }
    }

    /*
     * Get list product exist configuration from urbit
     *
     */

    public function ajaxProcessProductsSetting()
    {
        $urbit_rate_config_obj = new UrbitRateConfig();
        // get list product
        $product = $urbit_rate_config_obj->getListProduct();
        // get list delivery service
        $deliveries = $urbit_rate_config_obj->getAllService();
        // get list change
        $urbit_change = $this->getUrbitCharge();
        // render view
    }

    /**
     * Save settings of a product (add or edit)
     * @params additional_charges float addtional charge of a category
     * @params add_to_or_replace int 0 = add to  charge,  1 = replace  charge
     * @params id_product int id of list search product
     * @params delivery_service array list of carriers
     * @params id_urbit_rate_config int (optional) id of current category (edit/update only)
     * @return json data
     */
    public function ajaxProcessSaveProductSetting()
    {
        $id_urbit_rate_config = (int)Tools::getValue('id_urbit_rate_config', 0);
        // get id_product
        $id_product = Tools::getValue('product_id');
        // get additional charges
        $additional_charges = Tools::getValue('additional_charges');
        // get list delivery service
        $services = Tools::getValue('service', array());
        // check required fields
        if (empty($additional_charges) || empty($id_product) || empty($services)) {
            return;
        }
        // process add or edit  // edit
        if ($id_urbit_rate_config) {
            $urbit_rate_config = new UrbitRateConfig($id_urbit_rate_config);
            if (!Validate::isLoadedObject($urbit_rate_config)) {
                return;
            }
        } else {
            $urbit_rate_config = new UrbitRateConfig();
        }
        $this->json_data['success'] = $urbit_rate_config->saveProductSettings(
            $id_product,
            $services,
            Configuration::get('PS_CURRENCY_DEFAULT'),
            $additional_charges
        );
        echo Tools::jsonEncode($this->json_data);
        exit();
    }

    /**
     * display information of product to form Edit product
     *
     */
    public function ajaxProcessEditProductSetting()
    {
        // get $id_urbit_rate_config from form
        $id_urbit_rate_config = (int)Tools::getValue('id_rate_config', 0);
        if (!$id_urbit_rate_config) {
            exit('error');
        }
        $urbit_rate_config = new UrbitRateConfig($id_urbit_rate_config);
        // get detail product have $id_urbit_rate_config
        $product = $urbit_rate_config->getDetailProduct();
        // get list delivery service
        $severices = $urbit_rate_config->getAllService();
        // get  change
        $urbit_charge = $this->getUrbitCharge();
        // render view
        $this->context->smarty->assign(array(
            'product' => $product
        ));
    }

    /**
     * Delete object UrbitRateConfig exist id_urbit_rate_config from
     * @return json data
     */
    public function ajaxProcessDeleteProductSetting()
    {
        // get $id_urbit_rate_config from form
        $id_urbit_rate_config = (int)Tools::getValue('id_rate_config', 0);
        // process delete product
        if ($id_urbit_rate_config) {
            $urbit_rate_config = new UrbitRateConfig($id_urbit_rate_config);
            if (Validate::isLoadedObject($urbit_rate_config)) {
                $this->json_data['success'] = $urbit_rate_config->deleteUrbitRateConfig();
            }
        }
        echo Tools::jsonEncode($this->json_data);
        exit();
    }

    /**
     * Search ajax product
     * @params string keyword
     * @return json data
     */
    public function ajaxProcessSearchProduct()
    {
        // get query  name product
        $keyword = Tools::getValue('keyword');
        if ($keyword && empty($keyword['term'])) {
            $this->json_data = array(); // overide json_data, force to an empty array
            return;
        }
        // find product
        $query = Tools::replaceAccentedChars($keyword['term']);
        $products = Search::find($this->context->language->id, $query, 1, 1, 'position', 'desc', true, false);

        $product_search = array();
        // assign to array
        if ($products) {
            foreach ($products as $product) {
                $product_search[] = array(
                    'value' => $product['id_product'],
                    'label' => $product['pname']
                );
            }
        }
        // return to data json
        $this->json_data = $product_search;
        echo Tools::jsonEncode($this->json_data);
        exit();
    }

    /**
     * update block module status
     */
    public function ajaxProcessModuleStatus()
    {
        $this->context->smarty->assign(array(
            'configuration_status' => $this->module->getConfigurationStatus()
        ));
    }

    /** Search post code from API urbit
     * @params string keyword
     * @return json data
     */
    public function ajaxProcessPostCodeSearch()
    {
        $this->json_data = array(); // by default, returned data should be an empty array
        $keyword = Tools::getValue('term', null);
        if (!empty($keyword)) {
        }
        echo $this->json_data;
        exit();
    }
    // product carrier filter
    
}
