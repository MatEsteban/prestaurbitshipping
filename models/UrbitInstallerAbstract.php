<?php
/**
 * Urbit install of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

abstract class UrbitInstallerAbstract
{
    /**
     * Array contain queries sql create tables and insert data
     */
    public $install_queries = array();

    /**
     * Array contain queries sql drop tables of module urbit
     */
    protected $uninstall_queries = array();
    protected $module_name;

    /**
     * construct
     */
    public function __construct($module_name)
    {
        $this->module_name = $module_name;
        // declare $install_queries[];
        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_rate_service_code` (
                      `id_urbit_rate_service_code` int(10) NOT null AUTO_INCREMENT,
                      `id_carrier` int(10) NOT null,
                      `id_carrier_history` text NOT null,
                      `code` varchar(100) NOT null,
                      `service` varchar(255) NOT null,
                      `active` tinyint(1) NOT null DEFAULT 1,
		      `delay` varchar(128),
                      PRIMARY KEY  (`id_urbit_rate_service_code`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Insert Service in database
        $this->install_queries[] = 'REPLACE INTO `' . _DB_PREFIX_ . 'urbit_rate_service_code` (`id_carrier`, `id_carrier_history`, `code`, `service`, `active`, `delay`) VALUES
                       (0, \'\', \'URB_REGULAR\', \'urb-it delivery\', 1, \'Now or at the time of your choice\')
                    ;';

        // Create Config Table in Database
        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_rate_config` (
                        `id_urbit_rate_config` int(10) NOT null AUTO_INCREMENT,
                        `id_product` int(10) DEFAULT 0,
                        `id_category` int(10) DEFAULT 0,
                        `id_currency` int(10) NOT null,
                        `pickup_type_code` varchar(64) NOT null,
                        `type` tinyint(1) NOT null,
                        `additional_charges` double(6,2) NOT null,
                        `date_add` datetime NOT null,
                        `date_upd` datetime NOT null,
                        PRIMARY KEY  (`id_urbit_rate_config`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Create Config (Service) Table in Database
        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_rate_config_service` (
                        `id_urbit_rate_config_service` int(10) NOT null AUTO_INCREMENT,
                        `id_urbit_rate_service_code` int(10) NOT null,
                        `id_urbit_rate_config` int(10) NOT null,
                        `date_add` datetime NOT null,
                        `date_upd` datetime NOT null,
                        PRIMARY KEY  (`id_urbit_rate_config_service`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Create Cache Table in Database
        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_cache` (
                          `id_urbit_cache` int(10) NOT null AUTO_INCREMENT,
                          `carrier_name` varchar(255) null,
                          `hash` varchar(32) NOT null,
                          `total_charges` double(10,2) NOT null,
                          `params` text (255) null,
                          `response` text,
                          `delay` varchar (255),
                          `partly_cost` text,
                          `date_add` datetime NOT null,
                          `date_upd` datetime NOT null,
                          PRIMARY KEY  (`id_urbit_cache`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Create Test Cache Table in Database
        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_cache_test` (
                        `id_urbit_cache_test` int(10) NOT null AUTO_INCREMENT,
                        `hash` varchar(32) NOT null,
                        `result` text NOT null,
                        `date_add` datetime NOT null,
                        `date_upd` datetime NOT null,
                        PRIMARY KEY  (`id_urbit_cache_test`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Create Tempory cart order Table in Database
        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_order_cart` (
                        `id_urbit_order_cart` int(10) NOT null AUTO_INCREMENT,
                        `id_cart` int(10) NOT null,
                        `id_order` int(10) NOT null,
                        `id_carrier` int(10) NOT null,
                        `id_customer` int(10) NOT null,
                        `id_address_delivery` int(10) NOT null,
                        `id_address_invoice` int(10) NOT null,
                        `flag_order_created` int(5) NOT null, 
                        `delivery_name` varchar(255) null,
                        `delivery_street` varchar(255) null,
                        `delivery_time` datetime NOT null,
                        `delivery_zip_code` varchar(255) null,
                        `delivery_contact_mail` varchar(255) null,
                        `delivery_contact_phone` varchar(20) NOT null,
                        `delivery_advise_message` varchar(255) null,
                        `delivery_is_gift` int(2) NOT null,
                        `delivery_gift_receiver_phone` varchar(20) NOT null,
                        `date_add` datetime NOT null,
                        `date_upd` datetime NOT null,
                        `delivery_type` VARCHAR(20) NOT null,
                        PRIMARY KEY  (`id_urbit_order_cart`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_configuration_data`(
                            `id` int(10) NOT null AUTO_INCREMENT,
                            `urb_it_status` VARCHAR(30) NOT NULL,
                            `times_lap` VARCHAR(30) NOT NULL,
                            `urb_carrier_id` int(11),
                            PRIMARY KEY (`id`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


        /*API log table*/
        $this->install_queries[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'urbit_api_log` (
                        `id` INT(11) NOT NULL AUTO_INCREMENT ,
                        `cart_id` INT(11) NULL ,
                        `type` VARCHAR(15) NULL ,
                        `payload` TEXT NULL ,
                        `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
                        PRIMARY KEY (`id`))
                        ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


        $this->uninstall_queries[] = 'UPDATE `' . _DB_PREFIX_ . 'carrier` SET `deleted` = 1 WHERE external_module_name = \'' . $this->module_name . '\' OR `id_carrier` IN (SELECT DISTINCT(`id_carrier`) FROM `' . _DB_PREFIX_ . 'urbit_rate_service_code`)';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_rate_service_code`;';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_cache`;';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_cache_test`;';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_rate_config`;';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_rate_config_service`;';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_order_cart`;';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_configuration_data`;';
        $this->uninstall_queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'urbit_api_log`;';

        $this->initContext();
    }

    private function initContext()
    {
        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
//            global $smarty, $cookie;
            $this->context = new StdClass();
            /*$this->context->smarty = $smarty;
            $this->context->cookie = $cookie;*/
        }
    }

    /**
     * function install all tables of module
     * @return boolean
     */
    public function installTables()
    {
        foreach ($this->install_queries as $install_query) {
            if (!Db::getInstance()->execute($install_query)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Uninstall tables
     * @return boolean
     */
    public function uninstallTables()
    {
        foreach ($this->uninstall_queries as $uninstall_query) {
            if (!Db::getInstance()->execute($uninstall_query)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Install carriers
     * @return boolean
     */
    public function installCarriers()
    {
        // Get all services availables
        $rate_services = $this->getServiceCodes();
        $languages = Language::getLanguages(true);
        foreach ($rate_services as $rate_service) {
            if (!$rate_service['id_carrier']) {
                // add a new Carrier
                $carrier = new Carrier();
                $carrier->name = $rate_service['service'];
                $carrier->is_module = true;
                $carrier->active = true;
                $carrier->deleted = 0;
                $carrier->shipping_handling = true;
                $carrier->range_behavior = 0;
                $carrier->shipping_external = false; // display urbit carriers price - shipping_external = false
                $carrier->external_module_name = $this->module_name;
                $carrier->need_range = true;
                $carrier->id_zone = 1;
                $carrier->id_tax_rules_group = 0;
                $carrier->delay = array('fr' => $rate_service['delay'], 'en' => $rate_service['delay']);
                foreach ($languages as $language) {
                    $carrier->delay[(int)$language['id_lang']] = $rate_service['delay'];
                }
                if (!($carrier->add() && $this->insertCarrierGroup($carrier) && $this->addToZones($carrier) && $this->addCarrierToUrbitService(
                    $rate_service['id_urbit_rate_service_code'],
                    $carrier
                )
                    )
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get service codes from table urbit_rate_service_code
     * @return array
     */
    protected function getServiceCodes()
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'urbit_rate_service_code`');
    }

    /**
     * Insert id_carrier in table carrier group
     * @param Carrier $carrier
     * @return boolean
     */
    protected function insertCarrierGroup(Carrier $carrier)
    {
        $groups = Group::getGroups(true);
        foreach ($groups as $group) {
            if (!Db::getInstance()->autoExecute(
                _DB_PREFIX_ . 'carrier_group',
                array(
                    'id_carrier' => (int)$carrier->id,
                    'id_group' => (int)$group['id_group']
                ),
                'INSERT'
            )
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add carrier to all existing zones
     * @param Carrier $carrier
     * @return boolean
     */
    protected function addToZones(Carrier $carrier)
    {

        $rangeWeight = new RangeWeight();
        $rangeWeight->id_carrier = $carrier->id;
        $rangeWeight->delimiter1 = '0';
        $rangeWeight->delimiter2 = '10000';
        $rangeWeight->add();

        $range_price = new RangePrice();
        $range_price->id_carrier = $carrier->id;
        $range_price->delimiter1 = '0';
        $range_price->delimiter2 = '10000';
        $range_price->add();

        $zones = Zone::getZones();

        copy(
            _PS_ROOT_DIR_ . '/modules/' . $this->module_name . '/views/img/carrier.gif',
            _PS_SHIP_IMG_DIR_ . '/' . (int)$carrier->id . '.jpg'
        );

        $currency = $this->context->currency->iso_code;
        if ($currency == 'SEK') {
            $price = '129';
        } elseif ($currency == 'EUR') {
            $price = '13';
        } elseif ($currency == 'GBP') {
            $price = '10';
        } else {
            $price = '129';
        }


        foreach ($zones as $zone) {
            if (!Db::getInstance()->autoExecute(
                _DB_PREFIX_ . 'carrier_zone',
                array(
                'id_carrier' => (int)$carrier->id,
                'id_zone' => (int)$zone['id_zone']
                ),
                'INSERT'
            ) || !Db::getInstance()->autoExecuteWithNullValues(
                _DB_PREFIX_ . 'delivery',
                array(
                    'id_carrier' => (int)$carrier->id,
                    'id_range_price' => (int)$range_price->id,
                    'id_range_weight' => null,
                    'id_zone' => (int)$zone['id_zone'],
                    'price' => $price
                    ),
                'INSERT'
            ) || !Db::getInstance()->autoExecuteWithNullValues(
                _DB_PREFIX_ . 'delivery',
                array(
                'id_carrier' => (int)$carrier->id,
                'id_range_price' => null,
                'id_range_weight' => (int)$rangeWeight->id,
                'id_zone' => (int)$zone['id_zone'],
                'price' => $price
                ),
                'INSERT'
            )
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add carrier to Urbit service
     * @param int $id_urbit_rate_service_code
     * @param Carrier $carrier
     * @return boolean
     */
    protected function addCarrierToUrbitService($id_urbit_rate_service_code, Carrier $carrier)
    {
        if (Validate::isLoadedObject($carrier)) {
            $query = '
                UPDATE ' . _DB_PREFIX_ . 'urbit_rate_service_code
                SET
                    `id_carrier` = ' . (int)$carrier->id . ',
                    `id_carrier_history` = ' . (int)$carrier->id . '
                WHERE
                    `id_urbit_rate_service_code` = ' . (int)$id_urbit_rate_service_code;
            return Db::getInstance()->execute($query);
        }
        return false;
    }

    /**
     * Set carriers to warehouse
     * @return boolean
     */
    public function installWarehouseCarriers()
    {
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            // Get all services availables
            $rate_services = $this->getServiceCodes();

            $ids_carries = array();
            foreach ($rate_services as $rate_service) {
                if ($rate_service['id_carrier']) {
                    $ids_carries[] = $rate_service['id_carrier'];
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
            return true;
        }
        return true;
    }
}
