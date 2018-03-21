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

/**
 * Class UrbitInstallerTables
 */
class UrbitInstallerTables extends UrbitInstallerEntity
{
    /**
     * @var array Contain tables sql description
     */
    protected $tables = array(
        'urbit_rate_service_code'   => "
            `id_urbit_rate_service_code` INT(10) NOT NULL AUTO_INCREMENT,
            `id_carrier` INT(10) NOT NULL,
            `id_carrier_history` TEXT NOT NULL,
            `code` VARCHAR(100) NOT NULL,
            `service` VARCHAR(255) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `delay` VARCHAR(128),
            PRIMARY KEY  (`id_urbit_rate_service_code`)
        ",

        // Create Config Table in Database
        "urbit_rate_config"         => "
            `id_urbit_rate_config` INT(10) NOT NULL AUTO_INCREMENT,
            `id_product` INT(10) DEFAULT 0,
            `id_category` INT(10) DEFAULT 0,
            `id_currency` INT(10) NOT NULL,
            `pickup_type_code` VARCHAR(64) NOT NULL,
            `type` TINYINT(1) NOT NULL,
            `additional_charges` DOUBLE(6,2) NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY  (`id_urbit_rate_config`)
        ",

        // Create Config (Service) Table in Database
        "urbit_rate_config_service" => "
            `id_urbit_rate_config_service` INT(10) NOT NULL AUTO_INCREMENT,
            `id_urbit_rate_service_code` INT(10) NOT NULL,
            `id_urbit_rate_config` INT(10) NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY  (`id_urbit_rate_config_service`)
        ",

        // Create Cache Table in Database
        "urbit_cache"               => "
            `id_urbit_cache` INT(10) NOT NULL AUTO_INCREMENT,
            `carrier_name` VARCHAR(255) NULL,
            `hash` VARCHAR(32) NOT NULL,
            `total_charges` DOUBLE(10,2) NOT NULL,
            `params` TEXT (255) NULL,
            `response` TEXT,
            `delay` VARCHAR (255),
            `partly_cost` TEXT,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY  (`id_urbit_cache`)
        ",

        // Create Test Cache Table in Database
        "urbit_cache_test"          => "
            `id_urbit_cache_test` INT(10) NOT NULL AUTO_INCREMENT,
            `hash` VARCHAR(32) NOT NULL,
            `result` TEXT NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY  (`id_urbit_cache_test`)
        ",

        // Create Tempory cart order Table in Database
        "urbit_order_cart"          => "
            `id_urbit_order_cart` INT(10) NOT NULL AUTO_INCREMENT,
            `id_cart` INT(10) NOT NULL,
            `id_order` INT(10) NOT NULL,
            `id_carrier` INT(10) NOT NULL,
            `id_customer` INT(10) NOT NULL,
            `id_address_delivery` INT(10) NOT NULL,
            `id_address_invoice` INT(10) NOT NULL,
            `flag_order_created` INT(5) NOT NULL,
            `delivery_first_name` VARCHAR(255) NULL,
            `delivery_last_name` VARCHAR(255) NULL,
            `checkout_id` VARCHAR(255) NULL,
            `is_send` VARCHAR(255) NULL,
            `response_code` VARCHAR(255) NULL,
            `delivery_street` VARCHAR(255) NULL,
            `delivery_time` DATETIME NOT NULL,
            `preparation_end_time` DATETIME NOT NULL,
            `delivery_zip_code` VARCHAR(255) NULL,
            `delivery_city` VARCHAR(255) NULL,
            `delivery_contact_mail` VARCHAR(255) NULL,
            `delivery_contact_phone` VARCHAR(20) NOT NULL,
            `delivery_advise_message` VARCHAR(255) NULL,
            `delivery_is_gift` INT(2) NOT NULL,
            `delivery_gift_receiver_phone` VARCHAR(20) NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            `delivery_type` VARCHAR(20) NOT NULL,
            PRIMARY KEY  (`id_urbit_order_cart`)
        ",

        "urbit_configuration_data" => "
            `id` INT(10) NOT NULL AUTO_INCREMENT,
            `urb_it_status` VARCHAR(30) NOT NULL,
            `times_lap` VARCHAR(30) NOT NULL,
            `urb_carrier_id` INT(11),
            PRIMARY KEY (`id`)
        ",

        // API log table
        "urbit_api_log"            => "
            `id` INT(11) NOT NULL AUTO_INCREMENT ,
            `cart_id` INT(11) NULL ,
            `type` VARCHAR(15) NULL ,
            `payload` TEXT NULL ,
            `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id`)
        ",
    );

    /**
     * @return bool
     */
    public function install()
    {
        $PREFIX = _DB_PREFIX_;
        $ENGINE = _MYSQL_ENGINE_;

        $db = Db::getInstance();

        $code = Urbit::CARRIER_CODE_REGULAR;
        $queries = array();

        foreach ($this->tables as $tableName => $tableDescription) {
            $queries[] = "CREATE TABLE IF NOT EXISTS `{$PREFIX}{$tableName}`
            ({$tableDescription}) ENGINE={$ENGINE} DEFAULT CHARSET=utf8;";
        }

        $queries[] = "REPLACE INTO `{$PREFIX}urbit_rate_service_code`
            (`id_carrier`, `id_carrier_history`, `code`, `service`, `active`, `delay`)
            VALUES
            (0, '', '{$code}', 'urb-it delivery', 1, 'Now or at the time of your choice')
        ;";

        foreach ($queries as $query) {
            if (!$db->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $PREFIX = _DB_PREFIX_;

        $db = Db::getInstance();

        $queries = array(
            "UPDATE `{$PREFIX}carrier`
                SET `deleted` = 1
                WHERE external_module_name = '{$this->module->name}' OR `id_carrier` IN (
                    SELECT DISTINCT(`id_carrier`)
                    FROM `{$PREFIX}urbit_rate_service_code`
                )
            ;",
        );

        foreach ($this->tables as $tableName => $tableDescription) {
            $queries[] = "DROP TABLE IF EXISTS `{$PREFIX}{$tableName}`;";
        }

        foreach ($queries as $query) {
            if (!$db->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
