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
 * Class UrbitInstallerEntity
 */
abstract class UrbitInstallerEntity
{
    /**
     * @var UrbitAbstract
     */
    protected $module;

    /**
     * @var object
     */
    protected $context;

    /**
     * UrbitInstallerEntity constructor.
     * @param UrbitAbstract $module
     * @param object $context
     */
    public function __construct(UrbitAbstract $module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    /**
     * @return bool
     */
    abstract public function install();

    /**
     * @return bool
     */
    abstract public function uninstall();


    /**
     * Get service codes from table urbit_rate_service_code
     * @return array Array
     *   (
     *   [0] => Array
     *       (
     *           [id_urbit_rate_service_code] => ...
     *           [id_carrier] => ....
     *           [id_carrier_history] => ...
     *           [code] => ...
     *           [service] => ...
     *           [active] => ...
     *       )
     *       .....
     * @throws PrestaShopDatabaseException
     */
    protected function getServiceCodes()
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'urbit_rate_service_code`');
    }
}
