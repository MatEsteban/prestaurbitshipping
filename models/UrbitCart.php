<?php
/**
 * Urbit cart of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

class UrbitCart
{
    protected static $separator = '::';

    /**
     * function set id_product and id_cart to cookie.
     * @param int $id_product
     * @param int $id_cart
     * @return int boolean
     */
    public static function setCart($id_product, $id_cart)
    {
        $product_carts = self::getAllProductCart();
        $product_carts[(int)$id_product] = $id_cart;
        Context::getContext()->cookie->product_cart = self::encode($product_carts);
        return true;
    }

    /**
     * function get all id_product,id_cart in cookie
     * @return string product cart haver form id_product1::id_cart1,id_product2::id_cart2
     */
    protected static function getAllProductCart()
    {
        $context = Context::getContext();
        $product_cart_cookie = !empty($context->cookie->product_cart) ? $context->cookie->product_cart : '';
        return self::decode($product_cart_cookie);
    }

    /**
     * Convert a string into an array. String should be in format of "key::value,key::value"
     * @param string $input
     * @return array decoded array
     * array(
     * key => value,
     * key => value
     * ...
     * )
     */
    protected static function decode($input)
    {
        $tmp = explode(',', $input);
        $output = array();
        foreach ($tmp as $record) {
            $key_value = explode(self::$separator, $record);
            if (!empty($key_value[0]) && !empty($key_value[1])) {
                $output[$key_value[0]] = $key_value[1];
            }
        }
        return $output;
    }

    /**
     * Convert an array into a form of "key::value,key::value"
     * @param array $input
     * array(
     * key => value,
     * key => value
     * ...
     * )
     * return string in format of "key::value,key::value"
     */
    protected static function encode(array $input)
    {
        $output = '';
        if (!empty($input)) {
            $tmp = array();
            foreach ($input as $key => $value) {
                $tmp[] = ($key . self::$separator . $value);
            }
            $output = implode(',', $tmp);
        }
        return $output;
    }

    /**
     * Get id_cart from cookie with id_product
     * @param int $id_product
     * @return int id_cart
     */
    public static function getCart($id_product)
    {
        $product_carts = self::getAllProductCart();
        return !empty($product_carts[$id_product]) ? $product_carts[$id_product] : null;
    }

    /**
     * delete a product out of $this->context->cookie->product_cart
     * @param int $id_product
     * @return boolean
     * */
    public static function deleteCart($id_product)
    {
        $product_carts = self::getAllProductCart();
        if (!empty($product_carts[$id_product])) {
            unset($product_carts[$id_product]);
        }
        Context::getContext()->cookie->product_cart = self::encode($product_carts);
        return true;
    }

    public static function setOrderCart($order_values)
    {
        $order_cart = Db::getInstance()->executeS(
            'SELECT o.id_urbit_order_cart FROM ' . _DB_PREFIX_ . 'urbit_order_cart o '
            . 'WHERE  o.id_cart = ' . $order_values['id_cart']
        );

        foreach ($order_cart as $val) {
            $id_urbit_order_cart = $val['id_urbit_order_cart'];
        }

        $checkout_id = isset($order_values['delivery_options']['checkout_id']) ?
          $order_values['delivery_options']['checkout_id'] : $order_values['checkout_id'];
        $isSend = "false";
        $responseCode = "0";
        $preparationEndTime = "0";

        if (empty($id_urbit_order_cart)) {
            $ret = Db::getInstance()->execute(
                'INSERT INTO ' .
                  _DB_PREFIX_ .
                  'urbit_order_cart (`id_cart`,
                  `id_order`,
                  `id_carrier`,
                  `id_customer`,
                   `id_address_delivery`,
                    `id_address_invoice`,
                    `flag_order_created`,
                    `delivery_first_name`,
                    `delivery_last_name`,
                    `checkout_id`,
                    `is_send`,
                    `response_code`,
                    `delivery_street`,
                    `delivery_time`,
                    `preparation_end_time`,
                     `delivery_zip_code`,
                     `delivery_city`,
                     `delivery_contact_mail`,
                      `delivery_contact_phone`,
                      `delivery_advise_message`,
                      `delivery_is_gift`,
                      `delivery_gift_receiver_phone`,
                      `date_add`, `date_upd`,`delivery_type`)  VALUES (' . $order_values['id_cart']
                  . ',' . $order_values['id_order']
                  . ',' . $order_values['id_carrier']
                  . ',' . $order_values['id_customer']
                  . ',' . $order_values['id_address_delivery']
                  . ',' . $order_values['id_address_invoice']
                  . ',' . $order_values['flag_order_created']
                  . ',"' . $order_values['delivery_options']['del_first_name']
                  . '","' . $order_values['delivery_options']['del_last_name']
                  . '","' . $checkout_id
                  . '","' . $isSend
                  . '","' . $responseCode
                  . '","' . $order_values['delivery_options']['del_street']
                  . '","' . $order_values['delivery_options']['del_time']
                  . '","' . $order_values['preparation_end_time']
                  . '","' . $order_values['delivery_options']['del_zip_code']
                  . '","' . $order_values['delivery_options']['del_city']
                  . '","' . $order_values['delivery_options']['del_contact_mail']
                  . '","' . $order_values['delivery_options']['del_contact_phone']
                  . '","' . $order_values['delivery_options']['del_advise_message']
                  . '","' . $order_values['delivery_options']['del_is_gift']
                  . '","' . $order_values['delivery_options']['del_gift_receiver_phone']
                  . '","' . $order_values['date_add']
                  . '","' . $order_values['date_upd']
                  . '","' . $order_values['delivery_options']['del_type'] . '")'
            );
        } else {
            $ret = Db::getInstance()->execute(
                'UPDATE ' . _DB_PREFIX_ . 'urbit_order_cart SET `id_cart`=' . $order_values['id_cart']
                . ',`id_order`=' . $order_values['id_order']
                . ',`id_carrier`=' . $order_values['id_carrier']
                . ',`id_customer`=' . $order_values['id_customer']
                . ',`id_address_delivery`=' . $order_values['id_address_delivery']
                . ',`id_address_invoice`=' . $order_values['id_address_invoice']
                . ',`flag_order_created`="' . $order_values['flag_order_created']
                . '",`delivery_first_name`="' . $order_values['delivery_options']['del_first_name']
                . '",`delivery_last_name`="' . $order_values['delivery_options']['del_last_name']
                . '",`checkout_id`="' . $checkout_id
                . '",`is_send`="' . $isSend
                . '",`response_code`="' . $responseCode
                . '",`delivery_street`="' . $order_values['delivery_options']['del_street']
                . '",`delivery_time`="' . $order_values['delivery_options']['del_time']
                . '",`preparation_end_time`="' . $order_values['preparation_end_time']
                . '",`delivery_zip_code`="' . $order_values['delivery_options']['del_zip_code']
                . '",`delivery_city`="' . $order_values['delivery_options']['del_city']
                . '",`delivery_contact_mail`="' . $order_values['delivery_options']['del_contact_mail']
                . '",`delivery_contact_phone`="' . $order_values['delivery_options']['del_contact_phone']
                . '",`delivery_advise_message`="' . $order_values['delivery_options']['del_advise_message']
                . '",`delivery_is_gift`="' . $order_values['delivery_options']['del_is_gift']
                . '",`delivery_gift_receiver_phone`="' . $order_values['delivery_options']['del_gift_receiver_phone']
                . '",`date_add`="' . $order_values['date_add']
                . '",`date_upd`="' . $order_values['date_upd']
                . '",`delivery_type`="' . $order_values['delivery_options']['del_type']
                . '" WHERE `id_urbit_order_cart`=' . $id_urbit_order_cart
            );
        }

        return $ret;
    }

    public static function getOrderCart($id_cart, $id_carrier)
    {
        $order_cart = Db::getInstance()->executeS('SELECT
                    uc.id_cart,
                    uc.id_carrier,
                    uc.id_address_delivery,
                    uc.id_address_invoice,
                    uc.id_currency,
                    uc.id_customer,
                    uoc.id_urbit_order_cart,
                    uoc.id_order,
                    uoc.flag_order_created,
                    uoc.delivery_first_name as del_first_name,
                    uoc.delivery_last_name as del_last_name,
                    uoc.checkout_id as del_checkout_id,
                    uoc.is_send as del_is_send,
                    uoc.delivery_street as del_address1,
                    uoc.delivery_zip_code as del_zipcode,
                    uoc.delivery_city as del_city,
                    uoc.delivery_contact_mail as del_contact_mail,
                    uoc.delivery_contact_phone as del_contact_phone,
                    uoc.delivery_advise_message as del_advise_message,
                    uoc.delivery_is_gift as del_is_gift,
                    uoc.delivery_gift_receiver_phone as del_receiver_phone,
                    uoc.delivery_time as del_time,
                    uoc.delivery_type as del_type,
                    uoc.date_add,
                    uoc.date_upd,
                    cus.company,
                    cus.siret,
                    cus.ape,
                    cus.firstname,
                    cus.lastname,
                    cus.email,
                    cus.active,
                    cus.deleted,
                    car.id_reference,
                    car.id_tax_rules_group,
                    car.name,
                    car.url,
                    car.active,
                    car.deleted,
                    car.shipping_handling,
                    car.range_behavior,
                    car.is_module,
                    car.is_free,
                    car.shipping_external,
                    car.need_range,
                    car.external_module_name,
                    car.shipping_method,
                    car.position,
                    car.max_width,
                    car.max_height,
                    car.max_depth,
                    car.max_weight,
                    car.grade,
                    adr.id_address,
                    adr.id_country,
                    adr.id_state,
                    adr.id_customer,
                    adr.id_manufacturer,
                    adr.id_supplier,
                    adr.id_warehouse,
                    adr.alias,
                    adr.company,
                    adr.lastname,
                    adr.firstname,
                    adr.address1,
                    adr.address2,
                    adr.postcode,
                    adr.city,
                    adr.other,
                    adr.phone,
                    adr.phone_mobile,
                    adr.vat_number,
                    adr.dni,
                    adr.active,
                    adr.deleted,
                    cnt.id_country,
                    cnt.id_zone,
                    cnt.id_currency,
                    cnt.iso_code,
                    cnt.call_prefix,
                    cntlng.name as country_name

                    FROM ' . _DB_PREFIX_ . 'urbit_order_cart AS uoc
                    INNER JOIN ' . _DB_PREFIX_ . 'cart AS uc ON uoc.id_cart = uc.id_cart
                    INNER JOIN ' . _DB_PREFIX_ . 'carrier AS car ON uoc.id_carrier = car.id_carrier
                    INNER JOIN ' . _DB_PREFIX_ . 'customer AS cus ON uoc.id_customer = cus.id_customer
                    INNER JOIN ' . _DB_PREFIX_ . 'address AS adr ON uoc.id_address_delivery = adr.id_address
                    INNER JOIN ' . _DB_PREFIX_ . 'country AS cnt ON adr.id_country = cnt.id_country
                    INNER JOIN ' . _DB_PREFIX_ . 'country_lang  AS cntlng ON adr.id_country = cntlng.id_country
                    WHERE uoc.id_cart =' . $id_cart . ' AND uoc.id_carrier =' . $id_carrier);

        return $order_cart;
    }

    public static function getUnsentCarts()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' .
             _DB_PREFIX_ .
             'urbit_order_cart` WHERE `is_send`="false"'
        );
    }

    public static function getUrbitCart($urbitCartId)
    {
        return Db::getInstance()->executeS('SELECT * FROM `' .
           _DB_PREFIX_
           .'urbit_order_cart` WHERE `id_urbit_order_cart`="' .
           $urbitCartId .
           '"');
    }

    public static function getUrbitCartByOrderId($orderId)
    {
        return Db::getInstance()->executeS('SELECT * FROM `' .
            _DB_PREFIX_ .
            'urbit_order_cart` WHERE `id_order`=' .
             $orderId);
    }

    public function getOrderProducts($id_cart)
    {
        $context = Context::getContext();
        $lang_id = $context->language->id;
        $order_products = Db::getInstance()->executeS('SELECT
             cp.id_product, cp.quantity, p.price, p.reference, pl.name, pl.description_short
                    FROM ' . _DB_PREFIX_ . 'cart_product AS cp
                    INNER JOIN ' . _DB_PREFIX_ . 'product  AS p ON cp.id_product = p.id_product
                    INNER JOIN ' . _DB_PREFIX_ . 'product_lang  AS pl ON p.id_product = pl.id_product
                    WHERE cp.id_cart =' . $id_cart . ' AND pl.id_lang = ' . $lang_id);

        return $order_products;
    }

    public static function updateOrderId($orderId, $cartId)
    {
        $ret = Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'urbit_order_cart SET `id_order` =' . $orderId
            . ' WHERE `id_cart`=' . $cartId
        );

        return $ret;
    }

    public static function updateSentFlag($isSendValue, $urbitCartId)
    {
        $ret = Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'urbit_order_cart SET `is_send` ="' . $isSendValue
            . '" WHERE `id_urbit_order_cart`=' . $urbitCartId
        );

        return $ret;
    }

    public static function updateResponseCode($responseCode, $urbitCartId)
    {
        $ret = Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'urbit_order_cart SET `response_code` ="' . $responseCode
            . '" WHERE `id_urbit_order_cart`=' . $urbitCartId
        );

        return $ret;
    }

    public static function updateCheckoutId($checkoutId, $cartId)
    {
        $ret = Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'urbit_order_cart SET `checkout_id` ="' . $checkoutId
            . '" WHERE `id_cart`=' . $cartId
        );

        return $ret;
    }

    public static function updatePreparationEndTime($preparationEndTime, $cartId)
    {
        $ret = Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'urbit_order_cart SET `preparation_end_time` ="' . $preparationEndTime
            . '" WHERE `id_cart`=' . $cartId
        );

        return $ret;
    }
}
