<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license    Urbit
 */

if (!class_exists('UrbitInstallerAbstract')) {
    require_once('UrbitInstallerAbstract.php');
}

class UrbitInstaller extends UrbitInstallerAbstract
{
    public function __construct($module_name)
    {
        $this->module_name = $module_name;
        parent::__construct($module_name);
    }

    public function installConfigs()
    {
        return( Configuration::updateValue('URBIT_SHOW_DELAY', 1) && Configuration::updateValue('URBIT_SHOW_PARTLY_COST', 1) && Configuration::updateValue('URBIT_FLEXIBLE_PACKAGE', 1) && Configuration::updateValue('URBIT_PACKAGE_MARGIN', 0) && Configuration::updateValue('URBIT_PLACE_EXTRA_COVER_FORM', 'popup_center'));
    }
}
