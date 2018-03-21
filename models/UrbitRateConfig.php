<?php
/**
 * Urbit rate config of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

class UrbitRateConfig extends ObjectModel
{
    /**
     *
     * @var array definition
     */
    public static $definition = array(
        'table' => 'urbit_rate_config',
        'primary' => 'id_urbit_rate_config',
        'multilang' => false,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false, 'size' => 10),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false, 'size' => 10),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true, 'size' => 10),
            'pickup_type_code' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false,
                'size' => 64
            ),
            'type' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true, 'size' => 1),
            'additional_charges' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => false,
                'size' => 10
            ),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );
    /**
     * Cache carrier
     * @var array
     */
    protected static $enable_carriers = array();
    /**
     * cache product
     * @var array
     */
    protected static $products = array();
    /**
     * @var int id_product
     */
    public $id_product;
    /**
     *
     * @var int id_category
     */
    public $id_category;
    /**
     *
     * @var int id_currency
     */
    public $id_currency;
    /**
     *
     * @var int pickup_type_code
     */
    public $pickup_type_code;
    /**
     *
     * @var tinyint type
     */
    public $type;
    /**
     *
     * @var double(6,2) additional_charges
     */
    public $additional_charges;
    /**
     *
     * @var datetime date_add
     */
    public $date_add;
    /**
     *
     * @var datetime date_upd
     */
    public $date_upd;

    /*
     * get list all category id_category >0 in table ps_urbit_rate_config
     * @return array
     * $list_category = array(
     * 'id_urbit_rate_config',
     * 'id_category',
     * 'id_currency',
     * 'type',
     * 'services',
     * )
     */

    public function getListCategory()
    {
        // array contains list category
        $list_category = array();
        //list category have id_category >0
        $categories = $this->getCategorySetting();
        if (!$categories) {
            return;
        } else {
            foreach ($categories as $index => $category) {
                // assign id_urbit_rate_config to array
                $list_category[$index]['id_urbit_rate_config'] = $category['id_urbit_rate_config'];
                // assign chilrend category to array
                $list_category[$index]['category'] = $this->getPathInTab($category['id_category']);
                //assgin additional_charges + Currency to array
                $list_category[$index]['additional_charges'] = Tools::displayPrice($category['additional_charges']);
                //assgin type to array
                $list_category[$index]['type'] = $category['type'];
                //assgin services to array
                $services = $this->getDetailService($category['id_urbit_rate_config']);
                $str_service = '';
                foreach ($services as $service) {
                    $str_service .= $service['service'] . '<br />';
                }
                $list_category[$index]['services'] = $str_service;
            }
        }
        return $list_category;
    }

    /*
     * get all service is checked
     * @param INT $id_urbit_rate_config
     * return array $services
     */

    protected function getCategorySetting()
    {
        $sql = 'SELECT DISTINCT urc.id_urbit_rate_config,urc.*
                FROM `' . _DB_PREFIX_ . 'urbit_rate_config` urc
                INNER JOIN `' . _DB_PREFIX_ . 'urbit_rate_config_service` urs
                        ON (urc.id_urbit_rate_config = urs.id_urbit_rate_config)
                INNER JOIN `' . _DB_PREFIX_ . 'urbit_rate_service_code` ursc
                        ON (urs.id_urbit_rate_service_code = ursc.id_urbit_rate_service_code)
                WHERE urc.id_category> 0 AND ursc.active =1';
        return Db::getInstance()->ExecuteS($sql);
    }

    /*
     * get detail category of exist id_category
     * @param int $id_category
     * @return string Home->latop
     */

    private function getPathInTab($id_category)
    {
        $category = Db::getInstance()->getRow('
        SELECT id_category, level_depth, nleft, nright
        FROM ' . _DB_PREFIX_ . 'category
        WHERE id_category = ' . (int)$id_category);

        if (isset($category['id_category'])) {
            $categories = Db::getInstance()->ExecuteS('
            SELECT c.id_category, cl.name, cl.link_rewrite
            FROM ' . _DB_PREFIX_ . 'category c
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category)
            WHERE c.nleft <= ' . (int)$category['nleft'] .
              ' AND c.nright >= ' .
              (int)$category['nright'] .
              ' AND cl.id_lang = ' .
               (int)Context::getContext()->language->id . '
            ORDER BY c.level_depth ASC
            LIMIT ' . (int)($category['level_depth'] + 1));
            $path_tab = array();
            foreach ($categories as $category) {
                if ($category['name'] != 'Root') {
                    $path_tab[] = htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8');
                    $path = '';
                }
            }
            foreach ($path_tab as $p) {
                if (!empty($path)) {
                    $path .= ' > ';
                }
                $path .= $p;
            }
            return $path;
        }
    }

    /*
     * list category all category has id_category > 0
     * @return  array
     */

    public function getDetailService($id_urbit_rate_config)
    {
        $services = Db::getInstance()->ExecuteS('
                SELECT ursc.`service`
                FROM `' . _DB_PREFIX_ . 'urbit_rate_config_service` urcs
                LEFT JOIN `' . _DB_PREFIX_ . 'urbit_rate_service_code` ursc
                        ON (ursc.`id_urbit_rate_service_code` = urcs.`id_urbit_rate_service_code`)
                WHERE urcs.`id_urbit_rate_config` = ' . (int)$id_urbit_rate_config);

        return $services;
    }

    /**
     * Save category settings (service list, extra charges...)
     * @param int $id_category is category selected
     * @param array $services is delivery Service checked box
     * @param int $type 0 Add To 1 Replace
     * @param int $additional_charge
     * @param int $id_currency
     * @return boolean
     */
    public function saveCategorySettings(
        $id_category,
        $id_currency,
        $services = array(),
        $type = 0,
        $additional_charge = 0
    ) {
        if (empty($services) || !is_array($services)) {
            return false;
        }
        // load existing/selected service of id_category
        $sql = 'SELECT GROUP_CONCAT(DISTINCT arcs.id_urbit_rate_service_code) as ids
                FROM `' . _DB_PREFIX_ . 'urbit_rate_config` arc
                JOIN `' . _DB_PREFIX_ . 'urbit_rate_config_service` arcs
                        ON arc.`id_urbit_rate_config` = arcs.`id_urbit_rate_config`
                JOIN `' . _DB_PREFIX_ . 'urbit_rate_service_code` arsc
                        ON arsc.`id_urbit_rate_service_code` = arcs.`id_urbit_rate_service_code`
                WHERE arc.`id_category` = ' . (int)$id_category . '
                        AND arcs.id_urbit_rate_service_code IN(' . implode(',', $services) . ')';
        $exist_services = Db::getInstance()->getValue($sql);
        // convert string to array old_service
        $exist_services = explode(',', $exist_services);
        //get only service new
        $new_services = array_diff($services, $exist_services);
        if (empty($this->id) && empty($new_services)) {
            return false;
        }
        // assign to object
        $this->type = (int)$type;
        $this->additional_charges = (float)$additional_charge;
        $this->id_currency = (int)$id_currency;
        // save primary table
        // add
        if (empty($this->id)) {
            $this->id_category = (int)$id_category;
        } else {
            // delete old service
            $sql = 'DELETE FROM `' . _DB_PREFIX_ .
              'urbit_rate_config_service` WHERE id_urbit_rate_config = ' . (int)$this->id;
            if (!Db::getInstance()->query($sql)) {
                return false;
            }
            // add new service
            $new_services = $services;
        }
        // if there is no new services to update
        if (empty($new_services)) {
            return false;
        }

        if (!$this->save()) {
            return false;
        }

        //save new choiced services
        foreach ($new_services as $service) {
            $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'urbit_rate_config_service`
                                    (`id_urbit_rate_service_code`, `id_urbit_rate_config`)
                            VALUES(' . $service . ', ' . $this->id . ')';
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }
        return true;
    }

    /**
     * get detail of category
     * @param int $id_urbit_rate_config
     * @return array(
     * 'rate_config'=>obj,
     * 'services'=>array,
     * )
     * */
    public function getDetailCategory()
    {
        $category_detail = array();
        $category_detail['rate_config'] = $this;
        $services = $this->getDetailIdRateServiceCode();
        $category_detail['services'] = $services;

        return $category_detail;
    }

    /**
     * get urbit_rate_config's id_urbit_rate_servicces code are selected
     * @param int $id_urbit_rate_config
     * @return array $services
     */
    public function getDetailIdRateServiceCode()
    {
        $services = Db::getInstance()->getValue('
                SELECT GROUP_CONCAT(ursc.`id_urbit_rate_service_code` ORDER BY ursc.
                `id_urbit_rate_service_code`)
                FROM `' . _DB_PREFIX_ .
                'urbit_rate_config_service` urcs
                LEFT JOIN `' . _DB_PREFIX_ .
                'urbit_rate_service_code` ursc
                ON (ursc.`id_urbit_rate_service_code` = urcs.`id_urbit_rate_service_code`)
                WHERE urcs.`id_urbit_rate_config` = ' . (int)$this->id);

        return explode(',', $services);
    }

    /**
     * function deleteUrbitRateConfig object model
     * - delete urbit_rate_config_service have id_urbit_rate_config
     * - Delete all data have id_urbit_rate_config = this->id of table urbit_rate_config_service
     * @return boolen
     */
    public function deleteUrbitRateConfig()
    {
        $result = parent::delete();
        if ($result) {
            $result = Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ .
              'urbit_rate_config_service` WHERE `id_urbit_rate_config` = ' . (int)$this->id);
        }

        return $result;
    }

    /*
     * get all service exist
     * @return array $services
     */

    public function getAllCarrierIds()
    {
        $services = $this->getAllService();
        if ($services) {
            self::$enable_carriers = array();
            foreach ($services as $service) {
                if (!empty($service['id_carrier']) && Validate::isInt($service['id_carrier'])) {
                    self::$enable_carriers[] = $service['id_carrier'];
                }
            }
        }
        return self::$enable_carriers;
    }

    public function getAllService()
    {
        $services = Db::getInstance()->ExecuteS('
                SELECT *
                FROM `' . _DB_PREFIX_ . 'urbit_rate_service_code`
                WHERE active =1
                ');
        return $services;
    }

    /**
     * get list all product id_product >0 in table ps_urbit_rate_config
     * @return array
     * $arr_list_category = array(
     *    'id_urbit_rate_config',
     *    'product_name',
     *    'id_currency',
     *    'type',
     *    'services',
     * )
     */
    public function getListProduct()
    {
        // array contains list Product
        $list_product = array();
        // get list product from
        $products = $this->getConfigProduct();
        // return  array list product
        if (!$products) {
            return;
        } else {
            foreach ($products as $index => $product) {
                // assign id_urbit_rate_config to array
                $list_product[$index]['id_urbit_rate_config'] = $product['id_urbit_rate_config'];
                $obj_product = new Product($product['id_product'], false, Context::getContext()->language->id);
                // assign chilrend category to array
                $list_product[$index]['product_name'] = $obj_product->name;
                //assgin additional_charges to array
                $list_product[$index]['additional_charges'] = Tools::displayPrice($product['additional_charges']);
                //assgin type to array
                $list_product[$index]['type'] = $product['type'];
                //assgin services to array
                $str_service = '';
                $services = $this->getDetailService($product['id_urbit_rate_config']);
                foreach ($services as $service) {
                    $str_service .= $service['service'] . '<br />';
                }
                $list_product[$index]['services'] = $str_service;
            }
        }
        return $list_product;
    }

    protected function getConfigProduct()
    {
        $sql = 'SELECT DISTINCT urc.id_urbit_rate_config,urc.*
	      FROM `' . _DB_PREFIX_ . 'urbit_rate_config` urc
	      INNER JOIN `' . _DB_PREFIX_ . 'urbit_rate_config_service` urs
		      ON (urc.id_urbit_rate_config = urs.id_urbit_rate_config)
	      INNER JOIN `' . _DB_PREFIX_ . 'urbit_rate_service_code` ursc
		      ON (urs.id_urbit_rate_service_code = ursc.id_urbit_rate_service_code)
	      WHERE urc.id_product> 0 AND ursc.active =1';
        // return array list product from
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * Product Form Config Methods
     * @param int $id_urbit_rate_config
     * @return array(
     *    'id_urbit_rate_config',
     *    'product_name',
     *    'product_id',
     *    'pickup_type_code',
     *    'type',
     *    'additional_charges',
     *    'services',
     * )
     * */
    public function getDetailProduct()
    {
        // array assgin product detail
        $product_detail = array();
        // new object product
        $obj_product = new Product($this->id_product, false, Context::getContext()->language->id);
        // assign id_urbit_rate_config to array
        $product_detail['id_urbit_rate_config'] = $this->id;
        // assgin product name to array
        $product_detail['product_name'] = $obj_product->name;
        // assgin id product to array
        $product_detail['product_id'] = $this->id_product;
        // assgin pickup_type_code to array
        $product_detail['pickup_type_code'] = $this->pickup_type_code;
        // assgin type to array
        $product_detail['type'] = $this->type;
        // assgin additional_charges to array
        $product_detail['additional_charges'] = $this->additional_charges;
        //get severice of product
        $services = $this->getDetailIdRateServiceCode();
        // assgin severice to array
        $product_detail['services'] = $services;
        // return product detail
        return $product_detail;
    }

    /**
     * Save product settings (service list, extra charges...)
     * @param int $id_product
     * @param int $services
     * @param int $type
     * @param int $additional_charge
     * @param int $id_currency
     * @return boolean
     */
    public function saveProductSettings(
        $id_product,
        $id_currency,
        $services = array(),
        $type = 0,
        $additional_charge = 0
    ) {
        if (empty($services) || !is_array($services)) {
            return false;
        }

        // load existing/selected service of id_category
        $sql = 'SELECT GROUP_CONCAT(DISTINCT arcs.id_urbit_rate_service_code) as ids
                        FROM `' . _DB_PREFIX_ . 'urbit_rate_config` arc
                        JOIN `' . _DB_PREFIX_ . 'urbit_rate_config_service` arcs
                                ON arc.`id_urbit_rate_config` = arcs.`id_urbit_rate_config`
                        JOIN `' . _DB_PREFIX_ . 'urbit_rate_service_code` arsc
                                ON arsc.`id_urbit_rate_service_code` = arcs.`id_urbit_rate_service_code`
                        WHERE arc.`id_product` = ' . (int)$id_product . '
                                AND arcs.id_urbit_rate_service_code IN(' . implode(',', $services) . ')';
        $exist_services = Db::getInstance()->getValue($sql);
        $exist_services = explode(',', $exist_services);

        $new_services = array_diff($services, $exist_services);

        if (empty($this->id) && empty($new_services)) {
            return false;
        }

        $this->type = (int)$type;
        $this->additional_charges = (float)$additional_charge;
        $this->id_currency = (int)$id_currency;

        // save primary table
        if (empty($this->id)) {
            $this->id_product = (int)$id_product;
        } else {
            // delete old service
            $sql = 'DELETE FROM `' . _DB_PREFIX_ .
              'urbit_rate_config_service` WHERE id_urbit_rate_config = ' . (int)$this->id;
            if (!Db::getInstance()->query($sql)) {
                return false;
            }
            // add new service
            $new_services = $services;
        }
        // remove existing records {id_product/id_config_config_rate_service_code}
        $sql = 'SELECT GROUP_CONCAT(id_urbit_rate_service_code)
                FROM `' . _DB_PREFIX_ . 'urbit_rate_config` as arc
                JOIN `' . _DB_PREFIX_ . 'urbit_rate_config_service` as rcs
                ON arc.id_urbit_rate_config = rcs.id_urbit_rate_config
                WHERE id_product = ' . (int)$id_product . '
                    AND id_urbit_rate_service_code IN (' . implode(',', $new_services) . ')';

        $existing_records = Db::getInstance()->getValue($sql);
        $existing_records = explode(',', $existing_records);
        $new_services = array_diff($new_services, $existing_records);

        // if there is no new services to update
        if (empty($new_services)) {
            return false;
        }

        if (!$this->save()) {
            return false;
        }

        //save new choiced services
        foreach ($new_services as $service) {
            $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'urbit_rate_config_service`
                                    (`id_urbit_rate_service_code`, `id_urbit_rate_config`)
                            VALUES(' . $service . ', ' . $this->id . ')';
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Apply shipping rule of each product
     * @param Cart $cart
     * @param int $id_carrier
     * @return array list of products, same format as Cart::getProducts(), add one more index [rule]
     * <pre>
     * array(
     * 0 => array(
     *    'id_product' => int,
     *     ...,
     *   ...,
     *   'rule' => array( // @see UrbitRateConfig->getRule()
     *     'id_urbit_rate_config' => int,
     *    'id_product' => int,
     *        'id_category' => int,
     *        'id_currency' => int,
     *        'pickup_type_code' => string,
     *        'type' => tinyint,
     *        'additional_charges' => double,
     *        'date_add' => datetime,
     *        'date_upd' => datetime
     *   )
     * )
     * )
     */
    public function applyShippingRules(Cart &$cart, $id_carrier)
    {
        if (empty(self::$products[$id_carrier])) {
            $products = $cart->getProducts();
            foreach ($products as $index => $product) {
                $products[$index]['rule'] = $this->getRule($id_carrier, $product['id_product']);
            }
            self::$products[$id_carrier] = $products;
        }
        return self::$products[$id_carrier];
    }

    /**
     * get rule(s) of a product
     * Fistly, it looks up for product rule.
     * If product rule is not found, then look up for default category rule
     * If defaultf category rule is not found, look up for first parent category's rule
     *
     * @param $id_carrier int id of carrier
     * @param $id_product int id of product
     * $param $id_category int id of category
     *
     * @return array of urbit_rate_config's fields
     * array(
     * 'id_urbit_rate_config' => int,
     * 'id_product' => int,
     * 'id_category' => int,
     * 'id_currency' => int,
     * 'pickup_type_code' => string,
     * 'type' => tinyint,
     * 'additional_charges' => double,
     * 'date_add' => datetime,
     * 'date_upd' => datetime
     * )
     */
    public function getRule($id_carrier, $id_product)
    {
        $rule = $this->getProductRule($id_product, $id_carrier);
        if (!$rule) {
            $product = new Product($id_product);
            if (Validate::isLoadedObject($product)) {
                if (!$rule = $this->getCategoryRule(
                    $product->id_category_default,
                    $id_carrier
                )
                ) {
                    $default_category = new Category((int)$product->id_category_default);
                    if (Validate::isLoadedObject($default_category)) {
                        // get rules of parent categories (first one only)
                        $parent_categories = $default_category->getParentsCategories();
                        foreach ($parent_categories as $category) {
                            $rule = $this->getCategoryRule($category['id_category'], $id_carrier);
                            if ($rule) {
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $rule;
    }

    /*
     * list product have id_product > 0
     * @return array
     */

    /**
     * Get rule(s) of a product by product and carrier
     *
     * @param: $id_product int id of product
     * @param: $id_carrier int id of carrier
     *
     * @return array of urbit_rate_config's fields
     * array(
     * 'id_urbit_rate_config' => int,
     * 'id_product' => int,
     * 'id_category' => int,
     * 'id_currency' => int,
     * 'pickup_type_code' => string,
     * 'type' => tinyint,
     * 'additional_charges' => double,
     * 'date_add' => datetime,
     * 'date_upd' => datetime
     * )
     */
    public function getProductRule($id_product, $id_carrier)
    {
        $sql = 'SELECT r.*
                FROM `' . _DB_PREFIX_ . 'urbit_rate_config` r
                LEFT JOIN ' . _DB_PREFIX_ . 'urbit_rate_config_service s
                    ON (r.id_urbit_rate_config = s.id_urbit_rate_config)
                LEFT JOIN ' . _DB_PREFIX_ . 'urbit_rate_service_code rc
                    ON (rc.id_urbit_rate_service_code = s.id_urbit_rate_service_code )
                WHERE `id_product` =' . (int)$id_product . '
                    AND rc.id_carrier="' . (int)$id_carrier . '"';

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Get rule(s) of a category by category and carrier
     *
     * @param: $id_category int id of category
     * @param: $id_carrier int id of carrier
     *
     * @return array of urbit_rate_config's fields
     * array(
     * 'id_urbit_rate_config' => int,
     * 'id_product' => int,
     * 'id_category' => int,
     * 'id_currency' => int,
     * 'pickup_type_code' => string,
     * 'type' => tinyint,
     * 'additional_charges' => double,
     * 'date_add' => datetime,
     * 'date_upd' => datetime
     * )
     */
    public function getCategoryRule($id_category, $id_carrier)
    {
        $sql = 'SELECT r.*
                FROM `' . _DB_PREFIX_ . 'urbit_rate_config` r
                LEFT JOIN ' . _DB_PREFIX_ . 'urbit_rate_config_service s
                    ON (r.id_urbit_rate_config = s.id_urbit_rate_config)
                LEFT JOIN ' . _DB_PREFIX_ . 'urbit_rate_service_code rc
                    ON (rc.id_urbit_rate_service_code = s.id_urbit_rate_service_code )
                WHERE
                    `id_category` =' . (int)$id_category . '
                    AND rc.id_carrier="' . (int)$id_carrier . '"';

        return Db::getInstance()->getRow($sql);
    }
}
