<?php
/**
 * Ajax of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

//include(dirname(__FILE__) . '/../../config/config.inc.php');
// assuming your script is in the root folder of your site
include(dirname(__FILE__) . '/classes/api/UbitAPIWrapper.php');
// you can then access to everything
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');

$db = Db::getInstance();
// ps_urbit_rate_service_code
$data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT * FROM " . _DB_PREFIX_ .
  "urbit_rate_service_code WHERE  code='URB_REGULAR'");

$form_data = array();
$mod = Tools::getValue('mod');
$test_api_call = Tools::getValue('test_api_call');
$module_status = Tools::getValue('module_status');
$validate = Tools::getValue('validate');

if ($test_api_call) {
    $form_data['URBIT_API_CUSTOMER_KEY'] = Configuration::get('URBIT_API_CUSTOMER_KEY');
    $form_data['URBIT_API_TEST_CUSTOMER_KEY'] = Configuration::get('URBIT_API_TEST_CUSTOMER_KEY');
    $form_data['URBIT_API_TEST_BEARER_JWT_TOKEN'] = Configuration::get('URBIT_API_TEST_BEARER_JWT_TOKEN');
    $form_data['URBIT_API_URL'] = Configuration::get('URBIT_API_URL');
    $form_data['URBIT_API_BEARER_JWT_TOKEN'] = Configuration::get('URBIT_API_BEARER_JWT_TOKEN');
    $form_data['URBIT_API_TEST_URL'] = Configuration::get('URBIT_API_TEST_URL');
    $form_data['URBIT_ENABLE_TEST_MOD'] = Configuration::get('URBIT_ENABLE_TEST_MOD');
    $form_data['URBIT_ENABLE_TEST'] = Configuration::get('URBIT_ENABLE_TEST');
    $form_data['URBIT_SEND_FAILIOR_REPORT'] = Configuration::get('URBIT_SEND_FAILIOR_REPORT');
    $form_data['URBIT_MODULE_STATUS'] = Configuration::get('URBIT_MODULE_STATUS');
    $form_data['URBIT_MODULE_TIME_SPECIFIED'] = Configuration::get('URBIT_MODULE_TIME_SPECIFIED');

   /* Configuration::updateValue('URBIT_API_CUSTOMER_KEY', $_POST['URBIT_API_CUSTOMER_KEY']);
    Configuration::updateValue('URBIT_API_TEST_CUSTOMER_KEY', $_POST['URBIT_API_TEST_CUSTOMER_KEY']);
    Configuration::updateValue('URBIT_API_TEST_BEARER_JWT_TOKEN', $_POST['URBIT_API_TEST_BEARER_JWT_TOKEN']);
    Configuration::updateValue('URBIT_API_URL', $_POST['URBIT_API_URL']);
    Configuration::updateValue('URBIT_API_BEARER_JWT_TOKEN', $_POST['URBIT_API_BEARER_JWT_TOKEN']);
    Configuration::updateValue('URBIT_API_TEST_URL', $_POST['URBIT_API_TEST_URL']);
    Configuration::updateValue('URBIT_ENABLE_TEST_MOD', $_POST['URBIT_ENABLE_TEST_MOD']);
    Configuration::updateValue('URBIT_ENABLE_TEST', $_POST['URBIT_ENABLE_TEST']);
    Configuration::updateValue('URBIT_SEND_FAILIOR_REPORT', $_POST['URBIT_SEND_FAILIOR_REPORT']);
   */

    Configuration::updateValue('URBIT_API_CUSTOMER_KEY', Tools::getValue('URBIT_API_CUSTOMER_KEY'));
    Configuration::updateValue('URBIT_API_TEST_CUSTOMER_KEY', Tools::getValue('URBIT_API_TEST_CUSTOMER_KEY'));
    Configuration::updateValue('URBIT_API_TEST_BEARER_JWT_TOKEN', Tools::getValue('URBIT_API_TEST_BEARER_JWT_TOKEN'));
    //BEARER
    Configuration::updateValue('URBIT_API_URL', Tools::getValue('URBIT_API_URL'));
    Configuration::updateValue('URBIT_API_BEARER_JWT_TOKEN', Tools::getValue('URBIT_API_BEARER_JWT_TOKEN'));
    Configuration::updateValue('URBIT_API_TEST_URL', Tools::getValue('URBIT_API_TEST_URL'));
    Configuration::updateValue('URBIT_ENABLE_TEST_MOD', Tools::getValue('URBIT_ENABLE_TEST_MOD'));
    Configuration::updateValue('URBIT_ENABLE_TEST', Tools::getValue('URBIT_ENABLE_TEST'));
    Configuration::updateValue('URBIT_SEND_FAILIOR_REPORT', Tools::getValue('URBIT_SEND_FAILIOR_REPORT'));

    $urbApi = new UbitAPIWrapper();
    $start_date = date("Y-m-d");
    $startDate = time();
    $end_date = date('Y-m-d', strtotime('+1 day', $startDate));

    $return = $urbApi->getDeliveryHours();
    $delivery_hours_items = $return->hasError() ? array() : $return->args->items;

    $DATETIME = 'Y-m-d\TH:i:sP';

    $possibleDates = false;

    if (!empty($delivery_hours_items)) {
        foreach ($delivery_hours_items as $item) {
            $date = DateTime::createFromFormat($DATETIME, $item->first_delivery)->format('Y-m-d');

            if ($start_date == $date || $end_date == $date) {
                $possibleDates = true;
            }
        }
    }

    //if (Tools::getIsset($return->Message) && !Tools::getIsset($return->data)) {
    if (!$possibleDates) {
        Configuration::updateValue('URBIT_API_CUSTOMER_KEY', $form_data['URBIT_API_CUSTOMER_KEY']);
        Configuration::updateValue('URBIT_API_TEST_CUSTOMER_KEY', $form_data['URBIT_API_TEST_CUSTOMER_KEY']);
        Configuration::updateValue('URBIT_API_TEST_BEARER_JWT_TOKEN', $form_data['URBIT_API_TEST_BEARER_JWT_TOKEN']);
        //BEARER
        Configuration::updateValue('URBIT_API_URL', $form_data['URBIT_API_URL']);
        Configuration::updateValue('URBIT_API_BEARER_JWT_TOKEN', $form_data['URBIT_API_BEARER_JWT_TOKEN']);
        Configuration::updateValue('URBIT_API_TEST_URL', $form_data['URBIT_API_TEST_URL']);
        Configuration::updateValue('URBIT_ENABLE_TEST_MOD', $form_data['URBIT_ENABLE_TEST_MOD']);
        Configuration::updateValue('URBIT_ENABLE_TEST', $form_data['URBIT_ENABLE_TEST']);
        Configuration::updateValue('URBIT_SEND_FAILIOR_REPORT', $form_data['URBIT_SEND_FAILIOR_REPORT']);

        echo Tools::jsonEncode("fail");
        die;
    } else {
        echo Tools::jsonEncode("success");
        die;
    }
} elseif ($module_status) {
//http://stackoverflow.com/questions/16606300/prestashop-inserting-values-to-the-database-showing-unexpected-t-string
    /*    $insertData = array(

      'urb_it_status'  => $_POST['module_status'],
      'times_lap'  => $_POST['module_period'],
      'updated_dt'  => '2013-2-20'

      );
      //URB_REGULAR
      Db::getInstance()->insert("urb_it_status", $insertData); */
   // $module_status = Tools::getValue('module_status');
    $module_period = Tools::getValue('module_period');

    Configuration::updateValue('URBIT_MODULE_STATUS', Tools::getValue('module_status'));
    Configuration::updateValue('URBIT_MODULE_TIME_SPECIFIED', Tools::getValue('module_period'));
    Configuration::updateValue('URBIT_ADMIN_EMAIL', Tools::getValue('URBIT_ADMIN_EMAIL'));

    Configuration::updateValue('URBIT_ADMIN_AUTO_VALIDATION_TIME', Tools::getValue('URBIT_ADMIN_AUTO_VALIDATION_TIME'));
    Configuration::updateValue('URBIT_ADMIN_STATUS_TRIGGER', Tools::getValue('URBIT_ADMIN_STATUS_TRIGGER'));

    //update delivery cost
    Configuration::updateValue('URBIT_ADMIN_FLAT_FEE_EUR', Tools::getValue('URBIT_ADMIN_FLAT_FEE_EUR'));
    Configuration::updateValue('URBIT_ADMIN_FLAT_FEE_SEK', Tools::getValue('URBIT_ADMIN_FLAT_FEE_SEK'));
    Configuration::updateValue('URBIT_ADMIN_FLAT_FEE_GBP', Tools::getValue('URBIT_ADMIN_FLAT_FEE_GBP'));

    $currency = Context::getContext()->currency->iso_code;

    switch ($currency) {
        case 'SEK':
            updateUrbitDeliveryPrice(Configuration::get('URBIT_ADMIN_FLAT_FEE_SEK'), '129');
            break;
        case 'EUR':
            updateUrbitDeliveryPrice(Configuration::get('URBIT_ADMIN_FLAT_FEE_EUR'), '13');
            break;
        case 'GBP':
            updateUrbitDeliveryPrice(Configuration::get('URBIT_ADMIN_FLAT_FEE_EUR'), '10');
            break;
    }

    if ($module_status == "enabled") {
        $status = 1;
    } elseif ($module_status == "disabled") {
        $status = 0;
    }
    DB::getInstance()->Execute("UPDATE `" . _DB_PREFIX_ .
      "carrier` SET `active`='" . $status . "' WHERE `id_carrier`='" . $data[0]['id_carrier'] . "'");

    $data_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT * FROM `" . _DB_PREFIX_ .
      "urbit_configuration_data`  WHERE `urb_carrier_id`='" .
      $data[0]['id_carrier'] . "'");
    if (sizeof($data_array) > 0) {
        DB::getInstance()->Execute("UPDATE `" . _DB_PREFIX_ .
          "urbit_configuration_data` SET `urb_it_status`='" . $module_status . "', `times_lap`='" . $module_period .
          "'  WHERE `urb_carrier_id`='" . $data[0]['id_carrier'] . "'");
    } else {
        DB::getInstance()->Execute("INSERT INTO `" . _DB_PREFIX_ .
          "urbit_configuration_data` (`urb_it_status`, `times_lap`, `urb_carrier_id`) VALUES ('" .
          $module_status . "','" . $module_period . "','" . $data[0]['id_carrier'] . "')");
    }

    echo Tools::jsonEncode("success");
} elseif ($validate) {
    //$testUrl = $_POST['URBIT_API_TEST_URL'];
    $lastChar = Tools::substr(Tools::getValue('URBIT_API_TEST_URL'), -1);

    if ($lastChar == "/") {
        $_POST['URBIT_API_TEST_URL'] =  rtrim(Tools::getValue('URBIT_API_TEST_URL'), "/");
    }

    $lastLive = Tools::substr(Tools::getValue('URBIT_API_URL'), -1);

    if ($lastLive == "/") {
        $_POST['URBIT_API_URL'] =  rtrim(Tools::getValue('URBIT_API_URL'), "/");
    }

    $form_data['URBIT_API_CUSTOMER_KEY'] = Configuration::get('URBIT_API_CUSTOMER_KEY');
    $form_data['URBIT_API_TEST_CUSTOMER_KEY'] = Configuration::get('URBIT_API_TEST_CUSTOMER_KEY');
    $form_data['URBIT_API_TEST_BEARER_JWT_TOKEN'] = Configuration::get('URBIT_API_TEST_BEARER_JWT_TOKEN');
    $form_data['URBIT_API_URL'] = Configuration::get('URBIT_API_URL');
    $form_data['URBIT_API_BEARER_JWT_TOKEN'] = Configuration::get('URBIT_API_BEARER_JWT_TOKEN');
    $form_data['URBIT_API_TEST_URL'] = Configuration::get('URBIT_API_TEST_URL');
    $form_data['URBIT_ENABLE_TEST_MOD'] = Configuration::get('URBIT_ENABLE_TEST_MOD');
    $form_data['URBIT_ENABLE_TEST'] = Configuration::get('URBIT_ENABLE_TEST');
    $form_data['URBIT_SEND_FAILIOR_REPORT'] = Configuration::get('URBIT_SEND_FAILIOR_REPORT');
    $form_data['URBIT_MODULE_STATUS'] = Configuration::get('URBIT_MODULE_STATUS');
    $form_data['URBIT_MODULE_TIME_SPECIFIED'] = Configuration::get('URBIT_MODULE_TIME_SPECIFIED');

    Configuration::updateValue('URBIT_API_CUSTOMER_KEY', Tools::getValue('URBIT_API_CUSTOMER_KEY'));
    Configuration::updateValue('URBIT_API_TEST_CUSTOMER_KEY', Tools::getValue('URBIT_API_TEST_CUSTOMER_KEY'));
    Configuration::updateValue('URBIT_API_TEST_BEARER_JWT_TOKEN', Tools::getValue('URBIT_API_TEST_BEARER_JWT_TOKEN'));
    Configuration::updateValue('URBIT_API_URL', Tools::getValue('URBIT_API_URL'));
    Configuration::updateValue('URBIT_API_BEARER_JWT_TOKEN', Tools::getValue('URBIT_API_BEARER_JWT_TOKEN'));
    Configuration::updateValue('URBIT_API_TEST_URL', Tools::getValue('URBIT_API_TEST_URL'));
    Configuration::updateValue('URBIT_ENABLE_TEST_MOD', Tools::getValue('URBIT_ENABLE_TEST_MOD'));
    Configuration::updateValue('URBIT_ENABLE_TEST', Tools::getValue('URBIT_ENABLE_TEST'));
    Configuration::updateValue('URBIT_SEND_FAILIOR_REPORT', Tools::getValue('URBIT_SEND_FAILIOR_REPORT'));

    $urbApi = new UbitAPIWrapper();

    $start_date = date("Y-m-d");
    $startDate = time();
    $end_date = date('Y-m-d', strtotime('+1 day', $startDate));

    $return = $urbApi->getDeliveryHours();
    $delivery_hours_items = $return->hasError() ? array() : $return->args->items;

    $DATETIME = 'Y-m-d\TH:i:sP';

    $possibleDates = false;

    if (!empty($delivery_hours_items)) {
        foreach ($delivery_hours_items as $item) {
            $date = DateTime::createFromFormat($DATETIME, $item->first_delivery)->format('Y-m-d');

            if ($start_date == $date || $end_date == $date) {
                $possibleDates = true;
            }
        }
    }

    if (!$possibleDates) {
        if (Tools::getValue('URBIT_ENABLE_TEST_MOD')) {
            Configuration::updateValue('URBIT_API_TEST_CUSTOMER_KEY', "");
            Configuration::updateValue('URBIT_API_TEST_BEARER_JWT_TOKEN', "");
            Configuration::updateValue('URBIT_API_TEST_URL', "");
            Configuration::updateValue('URBIT_SEND_FAILIOR_REPORT', "");

            $form_data['URBIT_API_TEST_CUSTOMER_KEY'] = "";
            $form_data['URBIT_API_TEST_BEARER_JWT_TOKEN'] = "";
            $form_data['URBIT_API_TEST_URL'] = "";
            $form_data['URBIT_ENABLE_TEST_MOD'] = "";
            $form_data['URBIT_ENABLE_TEST'] = "";
        } else {
            Configuration::updateValue('URBIT_API_CUSTOMER_KEY', "");
            Configuration::updateValue('URBIT_API_BEARER_JWT_TOKEN', "");
            Configuration::updateValue('URBIT_API_URL', "");
            Configuration::updateValue('URBIT_ENABLE_TEST_MOD', "");

            $form_data['URBIT_API_URL'] = "";
            $form_data['URBIT_API_BEARER_JWT_TOKEN'] = "";
            $form_data['URBIT_API_CUSTOMER_KEY'] = "";
        }

        $form_data['status']='fail';
        echo Tools::jsonEncode($form_data);
        die;
    } else {
        $_POST['status']='success';
        echo Tools::jsonEncode($_POST);
        die;
    }
} elseif ($mod && $mod == "get_default_data") {
    $form_data = array();
    $data_carr = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT * FROM " . _DB_PREFIX_ .
      "carrier  WHERE `id_carrier`='" . $data[0]['id_carrier'] . "'");

    if (isset($data_carr['max_width'])) {
            DB::getInstance()->Execute("UPDATE `"._DB_PREFIX_ .
              "carrier` SET `max_width` = '142', `max_height` = '142', `max_depth` = '142', `max_weight` = '10' WHERE `"
              ._DB_PREFIX_ . "carrier`.`id_carrier`='" . $data[0]['id_carrier'] . "'");
    }

    $data_zone = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT * FROM " . _DB_PREFIX_ .
      "carrier_zone  WHERE `id_carrier`='" . $data[0]['id_carrier'] . "'");

    if (sizeof($data_zone) > 1) {
        DB::getInstance()->Execute("DELETE FROM `" . _DB_PREFIX_ .
          "carrier_zone` WHERE `" . _DB_PREFIX_ . "carrier_zone`.`id_carrier` = '" . $data[0]['id_carrier'] . "'");

        DB::getInstance()->Execute("INSERT INTO `" . _DB_PREFIX_ .
          "carrier_zone` (`id_carrier`, `id_zone`) VALUES ('" . $data[0]['id_carrier'] . "', '1')");
    }

    $form_data['URBIT_API_CUSTOMER_KEY'] = Configuration::get('URBIT_API_CUSTOMER_KEY');
    $form_data['URBIT_API_TEST_CUSTOMER_KEY'] = Configuration::get('URBIT_API_TEST_CUSTOMER_KEY');
    $form_data['URBIT_API_TEST_BEARER_JWT_TOKEN'] = Configuration::get('URBIT_API_TEST_BEARER_JWT_TOKEN');
    $form_data['URBIT_API_URL'] = Configuration::get('URBIT_API_URL');
    $form_data['URBIT_API_BEARER_JWT_TOKEN'] = Configuration::get('URBIT_API_BEARER_JWT_TOKEN');
    $form_data['URBIT_API_TEST_URL'] = Configuration::get('URBIT_API_TEST_URL');
    $form_data['URBIT_ENABLE_TEST_MOD'] = Configuration::get('URBIT_ENABLE_TEST_MOD');
    $form_data['URBIT_ENABLE_TEST'] = Configuration::get('URBIT_ENABLE_TEST');
    $form_data['URBIT_SEND_FAILIOR_REPORT'] = Configuration::get('URBIT_SEND_FAILIOR_REPORT');
    $form_data['URBIT_MODULE_STATUS'] = Configuration::get('URBIT_MODULE_STATUS');
    $form_data['URBIT_MODULE_TIME_SPECIFIED'] = Configuration::get('URBIT_MODULE_TIME_SPECIFIED');
    $form_data['URBIT_ADMIN_EMAIL'] = Configuration::get('URBIT_ADMIN_EMAIL');
    $form_data['URBIT_ADMIN_AUTO_VALIDATION_TIME'] = Configuration::get('URBIT_ADMIN_AUTO_VALIDATION_TIME');
    $form_data['URBIT_ADMIN_STATUS_TRIGGER'] = Configuration::get('URBIT_ADMIN_STATUS_TRIGGER');
    $form_data['URBIT_ADMIN_FLAT_FEE_EUR'] = Configuration::get('URBIT_ADMIN_FLAT_FEE_EUR');
    $form_data['URBIT_ADMIN_FLAT_FEE_SEK'] = Configuration::get('URBIT_ADMIN_FLAT_FEE_SEK');
    $form_data['URBIT_ADMIN_FLAT_FEE_GBP'] = Configuration::get('URBIT_ADMIN_FLAT_FEE_GBP');

    $form_data['URBIT_ADMIN_STATUS_TRIGGER_OPTIONS'] = OrderState::getOrderStates(
        (int)Context::getContext()->language->id
    );

    echo Tools::jsonEncode($form_data);
}

/**
 * Update urbit carrier'd delivery cost
 * @param $priceFromConfig string
 * @param $defaultPrice string If Config price field is empty => set this value
 * @return bool
 */
function updateUrbitDeliveryPrice($priceFromConfig, $defaultPrice)
{
    $price = $priceFromConfig ? : $defaultPrice;

    $urbit_carrier = Db::getInstance()->executeS('SELECT `id_carrier` FROM `' . _DB_PREFIX_ .
      'carrier` WHERE `external_module_name` = "urbit" ORDER BY `id_carrier` DESC LIMIT 1');

    if (isset($urbit_carrier[0]['id_carrier'])) {
        $sql = 'UPDATE `'._DB_PREFIX_.
          'delivery` SET price=' . $price . ' WHERE id_carrier=' . $urbit_carrier[0]['id_carrier'];

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    }
}
