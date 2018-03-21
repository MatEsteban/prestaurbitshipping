<?php
/**
 * Urbit carrier of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urb-it
 */

class UrbitCarrier extends Carrier
{

    /**
     * get list available with cart + shipping cost
     * @param array $carriers
     * @param Cart $cart
     * @param int $id_zone
     * @return array
     */
    public static function getAvailableCarriers(array $carriers, Cart $cart, $id_zone)
    {
        $results_array = array();
        if (empty($carriers) || !Validate::isLoadedObject($cart)) {
            return $results_array;
        }

        foreach ($carriers as $k => $row) {
            $carrier = new Carrier((int)$row['id_carrier']);
            $shipping_method = $carrier->getShippingMethod();
            if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
                // Get only carriers that are compliant with shipping method
                // Probably we don't need this check. Disabling since 2.9.7
                // If out-of-range behavior carrier is set on "Desactivate carrier"
                if ($row['range_behavior']) {
                    // Get id zone
                    if (!$id_zone) {
                        $id_zone = Country::getIdZone(Country::getDefaultCountryId());
                    }

                    // Get only carriers that have a range compatible with cart
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && (!Carrier::checkDeliveryPriceByWeight(
                        $row['id_carrier'],
                        $cart->getTotalWeight(),
                        $id_zone
                    )
                        )
                        ) ||
                          ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && (!Carrier::checkDeliveryPriceByPrice(
                              $row['id_carrier'],
                              $cart->getOrderTotal(
                                  true,
                                  Cart::BOTH_WITHOUT_SHIPPING
                              ),
                              $id_zone
                          )
                            )
                        )
                    ) {
                        unset($carriers[$k]);
                        continue;
                    }
                }
            }

            $row['name'] = ((string)$row['name'] != '0' ? $row['name'] : Configuration::get('PS_SHOP_NAME'));
            $row['price'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ? 0 : $cart->getPackageShippingCost(
                (int)$row['id_carrier'],
                true,
                null,
                null,
                $id_zone
            )
            );
            $row['price_tax_exc'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ?
              0 : $cart->getPackageShippingCost(
                  (int)$row['id_carrier'],
                  false,
                  null,
                  null,
                  $id_zone
              )
            );
            $row['img'] = file_exists(_PS_SHIP_IMG_DIR_ . (int)$row['id_carrier']) . '.jpg' ?
              _THEME_SHIP_DIR_ . (int)$row['id_carrier'] . '.jpg' : '';

            // If price is false, then the carrier is unavailable (carrier module)
            if ($row['price'] === false) {
                unset($carriers[$k]);
                continue;
            }
            $results_array[] = $row;
        }

        // if we have to sort carriers by price
        $prices = array();
        if (Configuration::get('PS_CARRIER_DEFAULT_SORT') == Carrier::SORT_BY_PRICE) {
            foreach ($results_array as $r) {
                $prices[] = $r['price'];
            }
            if (Configuration::get('PS_CARRIER_DEFAULT_ORDER') == Carrier::SORT_BY_ASC) {
                array_multisort($prices, SORT_ASC, SORT_NUMERIC, $results_array);
            } else {
                array_multisort($prices, SORT_DESC, SORT_NUMERIC, $results_array);
            }
        }

        return $results_array;
    }

    /**
     * Get warehouse by id carrier
     * @param int $id_carrier
     * @return int
     */
    public static function getWarehouseByIdCarrier($id_carrier)
    {
        return (int)Db::getInstance()->getValue('SELECT `id_warehouse` FROM '
          . _DB_PREFIX_ .
           'warehouse_carrier WHERE id_carrier = ' .
           (int)$id_carrier);
    }

    /**
     * Update carrier to warehouse
     * @param int $id_warehouse
     * @param int $id_carrier
     * @param int $old_id_carrier
     * @return boolean
     */
    public function updateWarehouseCarrier($id_warehouse, $id_carrier, $old_id_carrier)
    {
        return Db::getInstance()->execute('UPDATE `'
          . _DB_PREFIX_ .
          'warehouse_carrier` SET `id_carrier` = ' .
           (int)$id_carrier .
           ' WHERE `id_warehouse` = ' .
           $id_warehouse .
           ' AND `id_carrier` = ' .
            $old_id_carrier);
    }

    public function getActiveCarriers($module_name)
    {
        return Db::getInstance()->executeS(' SELECT * FROM '
          . _DB_PREFIX_ . 'carrier c WHERE c.external_module_name = "' .
           $module_name .
           '"  AND c.deleted = 0  AND c.active = 1 ');
    }

    public function getUserAddress($address_id)
    {
        $user_delivery_address = Db::getInstance()->executeS('
        SELECT a.*, c.*,c.name as country FROM ' . _DB_PREFIX_ . 'address a
            INNER JOIN ' . _DB_PREFIX_ . 'country_lang c ON a.id_country = c.id_country
            WHERE  a.id_address = ' . $address_id . ' AND a.deleted = 0 AND a.active = 1
        ');

        $user = array();
        foreach ($user_delivery_address as $val) {
            $user['id'] = $val['id_address'];
            $user['firstname'] = $val['firstname'];
            $user['lastname'] = $val['lastname'];
            $user['company'] = $val['company'];
            $user['address1'] = $val['address1'];
            $user['address2'] = $val['address2'];
            $user['city'] = $val['city'];
            $user['phone'] = $val['phone'];
            $user['phone_mobile'] = $val['phone_mobile'];
            $user['country'] = $val['country'];
            $user['postcode'] = $val['postcode'];
        }

        return $user;
    }

    private $carrier_code = 'URB_REGULAR';
    public function getUserUrbCarrier()
    {
        $defalult_urb_carrier = Db::getInstance()->executeS('SELECT `id_carrier`
            FROM `' . _DB_PREFIX_ . 'urbit_rate_service_code` WHERE `code`="' . $this->carrier_code . '"');

        $defalult_urb_carrier_id = "";
        foreach ($defalult_urb_carrier as $val) {
            $defalult_urb_carrier_id = $val['id_carrier'];
        }

        return $defalult_urb_carrier_id;
    }
}
