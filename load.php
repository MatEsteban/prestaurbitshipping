<?php
/**
 * Ajax of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

define("URBIT_SHIPPING_ROOT", dirname(__FILE__));

if (!class_exists('UrbitInstaller')) {
    require_once(URBIT_SHIPPING_ROOT . '/includes/UrbitInstaller.php');
}

if (!class_exists('LicenceApi')) {
    require_once(URBIT_SHIPPING_ROOT . '/classes/urbit/LicenceApi.php');
}

if (!class_exists('UrbitApi')) {
    require_once(URBIT_SHIPPING_ROOT . '/classes/urbit/UrbitApi.php');
}

if (!class_exists('UrbitPackage')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitPackage.php');
}

if (!class_exists('UrbitExtraCover')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitExtraCover.php');
}

if (!class_exists('UrbitRateConfig')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitRateConfig.php');
}

if (!class_exists('UrbitRateServiceCode')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitRateServiceCode.php');
}

if (!class_exists('UrbitCache')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitCache.php');
}

if (!class_exists('UrbitCarrier')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitCarrier.php');
}

if (!class_exists('UrbitCart')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitCart.php');
}

if (!class_exists('UrbitValidate')) {
    require_once(URBIT_SHIPPING_ROOT . '/models/UrbitValidate.php');
}

if (!class_exists('UrbitStoreApi')) {
    require_once(URBIT_SHIPPING_ROOT . '/classes/UrbitStoreApi.php');
}

if (!class_exists('UrbitAbstract')) {
    require_once(URBIT_SHIPPING_ROOT . '/includes/UrbitAbstract.php');
}
