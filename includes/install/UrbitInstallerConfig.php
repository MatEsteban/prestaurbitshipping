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
 * Class UrbitInstallerConfig
 */
class UrbitInstallerConfig extends UrbitInstallerEntity
{
    /**
     * @return bool
     */
    public function install()
    {
        return Configuration::updateValue('URBIT_SHOW_DELAY', 1)
            && Configuration::updateValue('URBIT_SHOW_PARTLY_COST', 1)
            && Configuration::updateValue('URBIT_FLEXIBLE_PACKAGE', 1)
            && Configuration::updateValue('URBIT_PACKAGE_MARGIN', 0)
            && Configuration::updateValue('URBIT_PLACE_EXTRA_COVER_FORM', 'popup_center');
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
}
