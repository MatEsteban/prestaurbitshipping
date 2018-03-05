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
 * Class UrbitInstallerWarehouseCarriers
 */
class UrbitInstallerWarehouseCarriers extends UrbitInstallerEntity
{
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function install()
    {
        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            return true;
        }

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

    /**
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
}
