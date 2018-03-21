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
 * Class UrbitInstallerCarriers
 */
class UrbitInstallerCarriers extends UrbitInstallerEntity
{
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function install()
    {
        // Get all services availables
        $rate_services = $this->getServiceCodes();
        $languages = Language::getLanguages(true);

        foreach ($rate_services as $rate_service) {
            if ($rate_service['id_carrier']) {
                continue;
            }

            $delay = $rate_service['delay'];

            // add a new Carrier
            $carrier = new Carrier();
            $carrier->name = $rate_service['service'];
            $carrier->is_module = true;
            $carrier->active = true;
            $carrier->deleted = 0;
            $carrier->shipping_handling = true;
            $carrier->range_behavior = 0;
            $carrier->shipping_external = false; // display urbit carriers price - shipping_external = false
            $carrier->external_module_name = $this->module->name;
            $carrier->need_range = true;
            $carrier->id_zone = 1;
            $carrier->id_tax_rules_group = 0;
            $carrier->delay = array(
                'fr' => $delay,
                'en' => $delay,
            );

            foreach ($languages as $language) {
                $langID = (int) $language['id_lang'];
                $carrier->delay[$langID] = $delay;
            }

            if (!$this->insertCarrier($carrier, $rate_service['id_urbit_rate_service_code'])) {
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
        return true;
    }

    /**
     * @param Carrier $carrier
     * @param $serviceCode
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    protected function insertCarrier(Carrier $carrier, $serviceCode)
    {
        return $carrier->add()
            && $this->insertCarrierGroup($carrier)
            && $this->addToZones($carrier)
            && $this->addCarrierToUrbitService(
                $serviceCode,
                $carrier
            );
    }

    /**
     * Insert id_carrier in table carrier group
     * @param Carrier $carrier
     * @return boolean
     * @throws PrestaShopDatabaseException
     */
    protected function insertCarrierGroup(Carrier $carrier)
    {
        $groups = Group::getGroups(true);
        foreach ($groups as $group) {
            if (!Db::getInstance()->autoExecute(
                _DB_PREFIX_ . 'carrier_group',
                array(
                    'id_carrier' => (int)$carrier->id,
                    'id_group'   => (int)$group['id_group'],
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
     * @throws PrestaShopDatabaseException
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
            _PS_ROOT_DIR_ . '/modules/' . $this->module->name . '/views/img/carrier.gif',
            _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg'
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
                  'id_zone'    => (int)$zone['id_zone'],
                ),
                'INSERT'
            ) || !Db::getInstance()->autoExecuteWithNullValues(
                _DB_PREFIX_ . 'delivery',
                array(
                      'id_carrier'      => (int)$carrier->id,
                      'id_range_price'  => (int)$range_price->id,
                      'id_range_weight' => null,
                      'id_zone'         => (int)$zone['id_zone'],
                      'price'           => $price,
                        ),
                'INSERT'
            ) || !Db::getInstance()->autoExecuteWithNullValues(
                _DB_PREFIX_ . 'delivery',
                array(
                                'id_carrier'      => (int)$carrier->id,
                                'id_range_price'  => null,
                                'id_range_weight' => (int)$rangeWeight->id,
                                'id_zone'         => (int)$zone['id_zone'],
                                'price'           => $price,
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
}
