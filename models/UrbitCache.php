<?php
/**
 * Urbit cache of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urb-it
 */

class UrbitCache extends ObjectModel
{

    /**
     * Contain shipping cost
     * @var array
     */
    public static $cache = array();
    /** @var array definition */
    public static $definition = array(
        'table' => 'urbit_cache',
        'primary' => 'id_urbit_cache',
        'multilang' => false,
        'fields' => array(
            'carrier_name'          => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
                'size' => 255
            ),
            'hash'                  => array('type' => self::TYPE_STRING,
              'validate' => 'isString',
              'required' => true,
              'size' => 32
            ),
            'total_charges'         => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true,
                'size' => 10
            ),
            'params'                => array('type' => self::TYPE_NOTHING),
            'UrbitShippingResponse' => array('type' => self::TYPE_NOTHING),
            'delay'                 => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'date_add'              => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd'              => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'partly_cost'           => array('type' => self::TYPE_NOTHING),
        )
    );
    /**
     * @var  int(10) id_urbit_cache
     */
    public $id_urbit_cache;
    /**
     * @var carrier_name name of current carrier
     */
    public $carrier_name;
    /**
     * @var params params of a request
     */
    public $params;
    /**
     * @var varchar(32)  hash
     */
    public $hash;
    /**
     * @var float total_charges
     */
    public $total_charges;
    /**
     * @var text response response from API
     */
    public $response;
    /** @var varchar delay delay time */
    public $delay;
    /**
     * @var datetime date_add
     */
    public $date_add;
    /**
     * @var datetime date_upd
     */
    public $date_upd;
    /**
     * $var text partly_code
     */
    public $partly_cost;

    /**
     * Save information in cache
     * @return boolean
     */
    public static function saveCache(
        $hash,
        $total_charges,
        $params = null,
        $response = null,
        $delay = null,
        $carrier_name = null,
        $partly_cost = null
    ) {
        if (empty($hash)) {
            return false;
        }
        self::$cache[$hash]['total_charges'] = ($total_charges == null || $total_charges == false) ?
          -1 : $total_charges;
        self::$cache[$hash]['delay'] = $delay;
        self::$cache[$hash]['partly_cost'] = Tools::jsonEncode($partly_cost);
        $cache = new UrbitCache();
        $cache->hash = $hash;
        $cache->total_charges = ($total_charges == null || $total_charges == false) ? -1 : $total_charges;
        if (!empty($params)) {
            $cache->params = Tools::jsonEncode($params);
        }
        if (!empty($response)) {
            $cache->response = Tools::jsonEncode($response);
        }
        if (!empty($delay)) {
            $cache->delay = $delay;
        }
        if (!empty($carrier_name)) {
            $cache->carrier_name = $carrier_name;
        }
        if (!empty($partly_cost)) {
            $cache->partly_cost = Tools::jsonEncode($partly_cost);
        }
        return $cache->save();
    }

    public static function saveCacheByHash($hash, $total_charges)
    {
        return Db::getInstance()->insert(
            self::$definition['table'],
            array(
                'hash' => $hash,
                'total_charges' => $total_charges
            )
        );
    }

    /**
     * implement caching for Urbitpost response, by Cart level
     * @param string $hash
     * @return Array cache or boolean
     */
    public static function getCache($hash)
    {
        if (empty($hash)) {
            return false;
        }
        // query from internal class, else query from db
        if (!isset(self::$cache[$hash])) {
            $db_cache = self::getCacheByHash($hash);
            if ($db_cache !== false) {
                self::$cache[$hash] = $db_cache;
            }
        }
        return isset(self::$cache[$hash]) ? self::$cache[$hash] : false;
    }

    /**
     * get available cache
     * @param unknown_type $hash
     * @return Ambigous <mixed, boolean>
     */
    public static function getCacheByHash($hash)
    {
        $sql = 'SELECT total_charges, delay, partly_cost FROM `' . _DB_PREFIX_ .
          self::$definition['table'] . '` WHERE hash = "' . $hash . '"';
        return Db::getInstance()->getRow($sql);
    }

    /**
     * get param cache by cart and encoding param
     * @param Cart $cart
     * @param float $initial_shipping_cost
     * @param int $id_carrier
     * @param string $country_code
     * @param string $post_code
     * @param array $service_code
     * @return string encoding
     */
    public static function getParamCacheByCart(
        Cart $cart,
        $initial_shipping_cost,
        $id_carrier,
        $country_code,
        $post_code,
        array $service_code
    ) {
        // validate input
        if (Cart::getNbProducts($cart->id) <= 0) {
            return null;
        }
        if (empty($id_carrier)) {
            return null;
        }
        // collect params
        // by cart
        $params = array();
        $params[] = $cart->id;
        $params[] = $initial_shipping_cost;
        foreach ($cart->getProducts() as $product) {
            $params[] = $product['id_product'] . ':'
              . $product['id_product_attribute'] . ':' . $product['cart_quantity'];
        }

        // by carrier
        $params[] = $id_carrier;

        // by address
        if (!empty($country_code)) {
            $params[] = $country_code;
        } // to_country_code, useful when international shipping
        if (!empty($post_code)) {
            $params[] = $post_code;
        }
        $params[] = Configuration::get('URBIT_CARRIER_POSTAL_CODE'); // from_postcode
        if (UrbitExtraCover::isExtraCoverService($service_code)) {
            $params['extra_cover'] = UrbitExtraCover::getExtraCover($id_carrier);
        }
        // encoding
        return md5(implode('_', $params));
    }

    /**
     * get order shipping cost from cache
     * @param array $cache
     * @return float shipping cost
     */
    public static function getOrderShippingCostFromCache(array $cache)
    {
        return $cache['total_charges'] >= 0.0 ? (float)$cache['total_charges'] : false;
    }
}
