<?php
/**
 * Urbit rate service code of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

class UrbitRateServiceCode extends ObjectModel
{
    /** @var array definition */
    public static $definition = array(
        'table' => 'urbit_rate_service_code',
        'primary' => 'id_urbit_rate_service_code',
        'multilang' => false,
        'fields' => array(
            'id_carrier' => array('type' => self::TYPE_INT, 'required' => true, 'size' => 10),
            'id_carrier_history' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 1000),
            'code' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 100),
            'service' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'active' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true, 'size' => 1),
            'delay' => array('type' => self::TYPE_STRING, 'size' => 128),
        )
    );
    /** @var  int(10) id_carrier */
    public $id_carrier;
    /** @var text id_carrier_history */
    public $id_carrier_history;
    /** @var varchar(64) code */
    public $code;
    /** @var varchar(255) service */
    public $service;
    /** @var tinyint type */
    public $active;
    /** @var varchar(255) delay */
    public $delay;

    /**
     * function get selected service
     * @param int $id_carrier
     * @return array
     * */
    public static function getSelectedService($id_carrier)
    {
        $sql = 'SELECT *
		    FROM `' . _DB_PREFIX_ . 'urbit_rate_service_code`
		    WHERE `id_carrier` = ' . (int)$id_carrier;
        return Db::getInstance()->getRow($sql);
    }

    /* update service code and carrier
     * @parma
     * 	-array $arr_service,
     * 	-$active 0|1
     * @return boolean
     */

    /**
     * Get service code from ddatabase.
     * @return Array service_code
     */
    public static function getServiceCode($id_carrier)
    {
        $str_service_code = self::getServiceCodeByCarrier($id_carrier);
        $service_code = explode('+', $str_service_code);
        return $service_code;
    }

    public static function getServiceCodeByCarrier($id_carrier)
    {
        $sql = 'SELECT code FROM `' . _DB_PREFIX_ . 'urbit_rate_service_code` WHERE id_carrier = ' . (int)$id_carrier;
        return Db::getInstance()->getValue($sql);
    }

    /**
     * get all service codes from database.
     * @return array
     */
    public function getAllServiceCodes()
    {
        $sql = 'SELECT * FROM  `' . _DB_PREFIX_ . 'urbit_rate_service_code`';
        return Db::getInstance()->executeS($sql);
    }

    public function updateActiveServiceCodeAndCarrier($service_carries, $active)
    {
        if (!empty($service_carries)) {
            $services = array();
            $carries = array();
            foreach ($service_carries as $value) {
                $value_arr = explode('-', $value);
                $services[] = $value_arr[0];
                $carries[] = $value_arr[1];
            }
            // process save service
            $services_id = implode(',', $services);
            $sql_serice = 'UPDATE `' . _DB_PREFIX_ .
              'urbit_rate_service_code` SET active=' . (($active) ? '1' : '0') .
              ' WHERE id_urbit_rate_service_code IN (' . $services_id . ')';
            // process save carrie
            $carrier_id = implode(',', $carries);
            $sql_carrier = 'UPDATE `' . _DB_PREFIX_ .
              'carrier` SET active=' . (($active) ? '1' : '0') .
              ' WHERE id_carrier IN (' . $carrier_id . ')';
            if (!Db::getInstance()->query($sql_serice) || !Db::getInstance()->query($sql_carrier)) {
                return false;
            }
            return true;
        }
        return false;
    }
}
