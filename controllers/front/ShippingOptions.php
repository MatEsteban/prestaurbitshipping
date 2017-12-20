<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license  Urb-it
 */

require_once(dirname(__FILE__) . '/../../classes/UrbitStoreApi.php');
require_once(dirname(__FILE__) . '/../../classes/UrbitConfigurations.php');
require_once(dirname(__FILE__) . '/../../models/UrbitCart.php');

class UrbitShippingOptionsModuleFrontController extends FrontController
{
    public function displayAjax()
    {
        $db = Db::getInstance();
        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT * FROM " . _DB_PREFIX_ . "urbit_rate_service_code WHERE  code='URB_REGULAR'");
        $form_data = array();

        if (!empty((Tools::getValue('validate_delivery')))) {
            // ******** Validate Delivery ***********

            $cart = new Cart($this->context->cart->id);

            $DATETIME =  'Y-m-d H:i:s';
            $date = DateTime::createFromFormat($DATETIME, Tools::getValue('del_time'));
            //format order expected date to ISO standard
            $delivery_expected_at = $date->format('Y-m-d\TH:i:sP');
            // get product information of the order
            $ret_order_product = UrbitCart::getOrderProducts($cart->id);
            $delivery_options = array(
                'del_name' => Tools::getValue('del_name'),
                'del_street' => Tools::getValue('del_street'),
                'del_time' => $delivery_expected_at,
                'del_zip_code' => Tools::getValue('del_zip_code'),
                'del_contact_mail' => Tools::getValue('del_contact_mail'),
                'del_contact_phone' => Tools::getValue('del_contact_phone'),
                'del_advise_message' => Tools::getValue('del_advise_message'),
                'del_is_gift' => Tools::getValue('del_is_gift'),
                'del_gift_receiver_phone' => Tools::getValue('del_gift_receiver_phone'),
                'order_products' => $ret_order_product,
                'del_type' => Tools::getValue('del_type')
            );

            $validate_delivery = UrbitStoreApi::ajaxCheckValidateDelivery($delivery_options);

            $now = time();
            $start_date = date('Y-m-d', strtotime('0 day', $now));
            $end_date = date('Y-m-d', strtotime('0 day', $now));
            $ret_openning_hours = UrbitStoreApi::getOpenningHours($start_date, $end_date);

            $open_time = $ret_openning_hours->data;
            $open_time_array = $open_time[0];
            $DATETIME = 'Y-m-d\TH:i:sP';
            if ($open_time_array->closed == false) {
                $date = DateTime::createFromFormat($DATETIME, $open_time_array->from);
                $delivery_start_at = $date->format('Y-m-d H:i:s');
                $date2 = DateTime::createFromFormat($DATETIME, $open_time_array->to);
                $delivery_end_at = $date2->format('Y-m-d H:i:s');

                $dateStr = strtotime($delivery_start_at);
                $StartTime = date("Y-m-d H:i:s", strtotime("-1 hours", $dateStr));

                $dateEnd = strtotime($delivery_end_at);
                $endTime = date("Y-m-d H:i:s", strtotime("-1 hours", $dateEnd));

                $validate_delivery['shopOpen'] = $StartTime;
                $validate_delivery['shopEnd'] = $endTime;
            } else {
                $validate_delivery['shopOpen'] = 'CLOSE';
            }

            if (!empty($validate_delivery['error_code'])) {
                $validate_delivery['error_message'] = UrbitConfigurations::getErrorMessage($validate_delivery['error_code']);
            }

            $ret = Tools::jsonEncode($validate_delivery);

            die($ret);
        } elseif (!empty((Tools::getValue('id_data')))) {
            $delivery_options = array(
                'del_name' => Tools::getValue('del_name'),
                'del_street' => Tools::getValue('del_street'),
                'del_time' => Tools::getValue('del_time'),
                'del_zip_code' => Tools::getValue('del_zip_code'),
                'del_contact_mail' => Tools::getValue('del_contact_mail'),
                'del_contact_phone' => Tools::getValue('del_contact_phone'),
                'del_advise_message' => Tools::getValue('del_advise_message'),
                'del_is_gift' => Tools::getValue('del_is_gift'),
                'del_gift_receiver_phone' => Tools::getValue('del_gift_receiver_phone'),
                'del_type' => Tools::getValue('del_type')
            );
            $cart = new Cart($this->context->cart->id);
            $order_values = array('id_cart' => $cart->id,
                'id_order' => 0,
                'id_carrier' => $cart->id_carrier,
                'id_customer' => $cart->id_customer,
                'id_address_delivery' => $cart->id_address_delivery,
                'id_address_invoice' => $cart->id_address_invoice,
                'flag_order_created' => 0,
                'delivery_options' => $delivery_options,
                'date_add' => date("Y-m-d H:i:s"),
                'date_upd' => date("Y-m-d H:i:s")
            );

            // save order delivery options
            $ret = UrbitCart::setOrderCart($order_values);
            $ret = Tools::jsonEncode(array('store_available_now' => $ret, 'sp_time' => 'true'));
            die($ret);
        } elseif (!empty((Tools::getValue('process_carrier')))) {
            // ******** process_carrier Click Validate Delivery ***********

            $delivery_options = array(
                'del_name' => Tools::getValue('del_name'),
                'del_street' => Tools::getValue('del_street'),
                'del_time' => Tools::getValue('del_time'),
                'del_zip_code' => Tools::getValue('del_zip_code'),
                'del_contact_mail' => Tools::getValue('del_contact_mail'),
                'del_contact_phone' => Tools::getValue('del_contact_phone'),
                'del_advise_message' => Tools::getValue('del_advise_message'),
                'del_is_gift' => Tools::getValue('del_is_gift'),
                'del_gift_receiver_phone' => Tools::getValue('del_gift_receiver_phone'),
                'del_type' => Tools::getValue('del_type')
            );
            $cart = new Cart($this->context->cart->id);

            $order_values = array('id_cart' => $cart->id,
                'id_order' => 0,
                'id_carrier' => $cart->id_carrier,
                'id_customer' => $cart->id_customer,
                'id_address_delivery' => $cart->id_address_delivery,
                'id_address_invoice' => $cart->id_address_invoice,
                'flag_order_created' => 0,
                'delivery_options' => $delivery_options,
                'date_add' => date("Y-m-d H:i:s"),
                'date_upd' => date("Y-m-d H:i:s")
            );

            // save order delivery options
            $saveOrder = UrbitCart::setOrderCart($order_values);

            die($saveOrder);
        } elseif (!empty((Tools::getValue('selectDate')))) {
            /*get opn hours according to selected dated*/
            $start_date = $end_date = Tools::getValue('selectDate');
            $ret_openning_hours = UrbitStoreApi::getOpenningHours($start_date, $end_date);
            $DATETIME = 'Y-m-d\TH:i:sP';
            $hours = $from_dates = $hours = $to_dates = array();
            $openning_hours_obj = $ret_openning_hours->data;
            foreach ($openning_hours_obj as $val) {
                $date = DateTime::createFromFormat($DATETIME, $val->from);
                $delivery_start_at = $date->format('Y-m-d H:i:s');
                $date2 = DateTime::createFromFormat($DATETIME, $val->to);
                $delivery_end_at = $date2->format('Y-m-d H:i:s');
                array_push($from_dates, $delivery_start_at);
                array_push($to_dates, $delivery_end_at);
            }

            $fromTime = $from_dates[0];
            $endTime = $to_dates[0];

            $date = new DateTime($fromTime);
            $start = $date->format('H');
            $date = new DateTime($endTime);
            $end = $date->format('H');

            for ($start; $start < $end; $start++) {
                $sTime = (int)$start;
                array_push($hours, $sTime);
            }
            $ret = Tools::jsonEncode($hours);
            die($ret);
        } elseif (!empty((Tools::getValue('selectOffTime')))) {
            //hide the today if time is pass
            $nowTime = Tools::getValue('nowTime');
            $now = time();
            $back_office_day_count = Configuration::get('URBIT_MODULE_TIME_SPECIFIED')-1;
            $start_date = date('Y-m-d', strtotime('0 day', $now));
            $end_date = date('Y-m-d', strtotime('+' . $back_office_day_count . ' day', $now));
            $DATETIME = 'Y-m-d\TH:i:sP';
            $ret_openning_hours = UrbitStoreApi::getOpenningHours($start_date, $end_date);
            $openning_hours_obj = $ret_openning_hours->data;
            $days = $minutes = $from_dates = $to_dates = array();

            foreach ($openning_hours_obj as $val) {
                if ($val->closed == false) {
                    $date = DateTime::createFromFormat($DATETIME, $val->from);
                    $delivery_start_at = $date->format('Y-m-d H:i:s');
                    $date2 = DateTime::createFromFormat($DATETIME, $val->to);
                    $delivery_end_at = $date2->format('Y-m-d H:i:s');
                    array_push($from_dates, $delivery_start_at);
                    array_push($to_dates, $delivery_end_at);
                }
            }

            foreach ($from_dates as $val) {
                $date = new DateTime($val);
                $result = $date->format('Y-m-d');
                array_push($days, $result);
            }

            $dateT = new DateTime($to_dates[0]);
            $ubtTimestamp = $dateT->getTimestamp();
            $dateN = new DateTime($nowTime);

            $nowHour = $dateN->format('H');
            if ($nowHour > 22) {
                $newTimeStamp = $dateN->getTimestamp();
            } else {
                $dateN->modify('+2 hours');
                $newTimeStamp = $dateN->getTimestamp();
            }

            if (($ubtTimestamp < $newTimeStamp) || ($nowHour > 22)) {
                $lastDay = end($days);
                $datetime = new DateTime($lastDay);
                $datetime->modify('+1 day');
                $tomorrow =  $datetime->format('Y-m-d');
                array_push($days, $tomorrow);
                array_shift($days);
            }
            $ret = Tools::jsonEncode($days);
            die($ret);
        }
    }
}
