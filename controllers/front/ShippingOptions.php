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
        switch (true) {
            case $this->isNotEmpty('validate_delivery'):
                $this->validateDelivery();
                break;
            case $this->isNotEmpty('id_data'):
                $this->validateIdData();
                break;
            case $this->isNotEmpty('process_carrier'):
                $this->validateProcessCarrier();
                break;
            case $this->isNotEmpty('selectDate'):
                $this->validateSelectDate();
                break;
            case $this->isNotEmpty('selectOffTime'):
                $this->validateSelectOffTime();
                break;
            case $this->isNotEmpty('nearest_possible'):
                $this->nearestPossibleNowTime();
            //Next time possible is calculating according to opening hours
            default:
                $ret = Tools::jsonEncode(array(
                    "status" => false,
                ));
                die($ret);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function isNotEmpty($name)
    {
        $data = Tools::getValue($name);
        return !empty($data);
    }

    /**
     * Gets delivery date timestamp
     * @param $deliveryDate string delivery date from API [format: Y-m-d\TH:i:sP]
     * @return int
     */
    protected function getDeliveryDateTimestamp($deliveryDate)
    {
        $date = DateTime::createFromFormat('Y-m-d\TH:i:sP', $deliveryDate, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('CET'));

        return (int)$date->getTimestamp();
    }

    protected function validateDelivery()
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', Tools::getValue('del_time'), new DateTimeZone('CET'));

        //format order expected date to ISO standard
        $delivery_expected_at = $date->format('Y-m-d\TH:i:sP');

        $delivery_options = array(
            'del_name' => Tools::getValue('del_name'),
            'del_first_name' => Tools::getValue('del_first_name'),
            'del_last_name' => Tools::getValue('del_last_name'),
            'del_street' => Tools::getValue('del_street'),
            'del_time' => $delivery_expected_at,
            'del_zip_code' => Tools::getValue('del_zip_code'),
            'del_city' => Tools::getValue('del_city'),
            'del_contact_mail' => Tools::getValue('del_contact_mail'),
            'del_contact_phone' => Tools::getValue('del_contact_phone'),
            'del_advise_message' => Tools::getValue('del_advise_message'),
            'del_is_gift' => Tools::getValue('del_is_gift'),
            'del_gift_receiver_phone' => Tools::getValue('del_gift_receiver_phone'),
            'del_type' => Tools::getValue('del_type')
        );

        $urbitStoreApi = new UrbitStoreApi();

        //check delivery address
        $validate_delivery = $urbitStoreApi->ajaxCheckValidateDelivery($delivery_options);

        //chosen time for delivery
        $chosenDeliveryDateTimestamp =  $date->getTimestamp();
        //check delivery hours
        $ret_possible_delivery_hours = UrbitStoreApi::getDeliveryHours();


        $delivery_hours_items = $ret_possible_delivery_hours->hasError() ?
          array() : $ret_possible_delivery_hours->args->items;

        $isPossibleHours = false;

        foreach ($delivery_hours_items as $delivery_item) {
            if ($delivery_item->closed == 1) {
                continue;
            }

            $firstDeliveryTimestamp = strtotime(
                '+5 minutes',
                $this->getDeliveryDateTimestamp($delivery_item->first_delivery)
            );
            $lastDeliveryTimestamp = strtotime(
                '-5 minutes',
                $this->getDeliveryDateTimestamp($delivery_item->last_delivery)
            );

            if ($chosenDeliveryDateTimestamp >= $firstDeliveryTimestamp &&
                $lastDeliveryTimestamp >= $chosenDeliveryDateTimestamp) {
                $isPossibleHours = true;
            }
        }

        $validate_delivery['possible_hours'] = $isPossibleHours;

        if (!empty($validate_delivery['error_code'])) {
            $validate_delivery['error_message'] = UrbitConfigurations::getErrorMessage(
                $validate_delivery['error_code']
            );
        }

        $ret = Tools::jsonEncode($validate_delivery);

        die($ret);
    }

    protected function validateIdData()
    {
        $delivery_options = array(
            'id_checkout' => $this->context->cookie->checkoutIdFromApi,
            'del_name' => Tools::getValue('del_name'),
            'del_first_name' => Tools::getValue('del_first_name'),
            'del_last_name' => Tools::getValue('del_last_name'),
            'del_street' => Tools::getValue('del_street'),
            'del_time' => Tools::getValue('del_time'),
            'del_zip_code' => Tools::getValue('del_zip_code'),
            'del_city' => Tools::getValue('del_city'),
            'del_contact_mail' => Tools::getValue('del_contact_mail'),
            'del_contact_phone' => Tools::getValue('del_contact_phone'),
            'del_advise_message' => Tools::getValue('del_advise_message'),
            'del_is_gift' => Tools::getValue('del_is_gift'),
            'del_gift_receiver_phone' => Tools::getValue('del_gift_receiver_phone'),
            'del_type' => Tools::getValue('del_type')
        );

        $cart = new Cart($this->context->cart->id);

        $order_values = array(
            'id_cart' => $cart->id,
            'id_order' => 0,
            'checkout_id' => "",
            'id_carrier' => $cart->id_carrier,
            'id_customer' => $cart->id_customer,
            'id_address_delivery' => $cart->id_address_delivery,
            'id_address_invoice' => $cart->id_address_invoice,
            'flag_order_created' => 0,
            'delivery_options' => $delivery_options,
            'date_add' => date("Y-m-d H:i:s"),
            'date_upd' => date("Y-m-d H:i:s"),
            'preparation_end_time' => date("Y-m-d H:i:s")
        );

        // save order delivery options
        $ret = UrbitCart::setOrderCart($order_values);
        $ret = Tools::jsonEncode(array('store_available_now' => $ret, 'sp_time' => 'true'));

        die($ret);
    }

    protected function validateProcessCarrier()
    {
        // ******** process_carrier Click Validate Delivery ***********

        $delivery_options = array(
            'checkout_id' => "",
            'del_name' => Tools::getValue('del_name'),
            'del_first_name' => Tools::getValue('del_first_name'),
            'del_last_name' => Tools::getValue('del_last_name'),
            'del_street' => Tools::getValue('del_street'),
            'del_time' => Tools::getValue('del_time'),
            'del_zip_code' => Tools::getValue('del_zip_code'),
            'del_contact_mail' => Tools::getValue('del_contact_mail'),
            'del_contact_phone' => Tools::getValue('del_contact_phone'),
            'del_city' => Tools::getValue('del_city'),
            'del_advise_message' => Tools::getValue('del_advise_message'),
            'del_is_gift' => Tools::getValue('del_is_gift'),
            'del_gift_receiver_phone' => Tools::getValue('del_gift_receiver_phone'),
            'del_type' => Tools::getValue('del_type')
        );
        $cart = new Cart($this->context->cart->id);

        $order_values = array('id_cart' => $cart->id,
            'id_order' => 0,
            'checkout_id' => "",
            'id_carrier' => $cart->id_carrier,
            'id_customer' => $cart->id_customer,
            'id_address_delivery' => $cart->id_address_delivery,
            'id_address_invoice' => $cart->id_address_invoice,
            'flag_order_created' => 0,
            'delivery_options' => $delivery_options,
            'date_add' => date("Y-m-d H:i:s"),
            'date_upd' => date("Y-m-d H:i:s"),
            'preparation_end_time' => date("Y-m-d H:i:s")

        );

        // save order delivery options
        $saveOrder = UrbitCart::setOrderCart($order_values);

        die($saveOrder);
    }

    protected function validateSelectDate()
    {
        /*get opn hours according to selected dated*/
        $start_date = Tools::getValue('selectDate');

        $ret_possible_delivery_hours = UrbitStoreApi::getDeliveryHours();
        $delivery_hours_items = $ret_possible_delivery_hours->hasError() ?
          array() : $ret_possible_delivery_hours->args->items;

        $DATETIME = 'Y-m-d\TH:i:sP';
        $utcTimeZone = new DateTimeZone('UTC');
        $cetTimeZone = new DateTimeZone('CET');
        $hours = array();
        $from_dates = array();
        $to_dates = array();

        foreach ($delivery_hours_items as $item) {
            $date = DateTime::createFromFormat($DATETIME, $item->first_delivery, $utcTimeZone);
            $date->setTimezone($cetTimeZone);
            $date->setTimestamp(strtotime('+5 minutes', $date->getTimestamp()));

            if ($start_date == $date->format('Y-m-d')) {
                $date2 = DateTime::createFromFormat($DATETIME, $item->last_delivery, $utcTimeZone);
                $date2->setTimezone($cetTimeZone);
                $date2->setTimestamp(strtotime('-5 minutes', $date2->getTimestamp()));

                $from_dates[] = $date->format('Y-m-d H:i:s');
                $to_dates[] = $date2->format('Y-m-d H:i:s');
            }
        }

        $fromTime = $from_dates[0];
        $endTime = $to_dates[0];

        $date = new DateTime($fromTime, $cetTimeZone);
        $startHour = $date->format('H');
        $startMinutes = $date->format('i');

        $nearestPossibleTimeString = $this->getNearestPossibleSpecificTime();
        $nearestPossibleTimeObj = new DateTime($nearestPossibleTimeString, $utcTimeZone);

        //First Specific time delivery possible = First delivery Time (API) + (15 min)
        $firstPossibleDeliveryTimestamp = strtotime('+10 minutes', $date->getTimestamp());

        if ($nearestPossibleTimeObj->getTimestamp() < $firstPossibleDeliveryTimestamp) {
            $nearestPossibleTimeObj->setTimestamp($firstPossibleDeliveryTimestamp);
        }

        $date = new DateTime($endTime, $utcTimeZone);
        $endHour = $date->format('H');
        $endMinutes = $date->format('i');

        //array with first delivery and last delivery minutes
        $minutes = array(
            'start' => $startMinutes,
            'end' => $endMinutes
        );

        //add to array hours between first delivery hour and last delivery hour
        for (; $startHour <= $endHour; $startHour++) {
            $hours[] = (int) $startHour;
        }

        $availableTime = array(
            'hours' => $hours,
            'minutes' => $minutes,
            'nearest' => $nearestPossibleTimeString,
            'nearestHour' => $nearestPossibleTimeObj->format('H'),
            'nearestMinute' => $nearestPossibleTimeObj->format('i'),
            'endHour' => $endHour,
            'endMinute' => $endMinutes
        );

        $ret = Tools::jsonEncode($availableTime);

        die($ret);
    }

    /**
     * Return nearest possible delivery time
     * result = now time + order preparation time (from module config) + Urb-it standard process time (1h 30m)
     * @return string
     */
    protected function getNearestPossibleNowTimestamp()
    {
        $nowTime = new DateTime(null, new DateTimeZone('CET'));

        $deliveryTime = strtotime('+1 hour 30 minutes', $nowTime->getTimestamp());
        $preparationTime = Configuration::get('URBIT_ADMIN_AUTO_VALIDATION_TIME');

        if ($preparationTime) {
            $deliveryTime += (int)$preparationTime * 60;
        }

        return $deliveryTime;
    }

    protected function getNearestPossibleSpecificTime()
    {
        $differentBetweenSpecificAndNow = 15;
        $nearestPossibleNowTime = $this->getNearestPossibleNowTimestamp();
        $nearestPossibleNowTime += $differentBetweenSpecificAndNow * 60;

        $nextPossible = new DateTime(null, new DateTimeZone('CET'));
        $nextPossible->setTimestamp($nearestPossibleNowTime);

        return $nextPossible->format("Y-m-d H:i:s");
    }

    /**
     * return nearest possible delivery time
     * for AJAX
     */
    protected function nearestPossibleNowTime()
    {
        $nowDeliveryTimestamp = $this->getNearestPossibleNowTimestamp();

        $possibleNow = new DateTime(null, new DateTimeZone('CET'));
        $possibleNow->setTimestamp($nowDeliveryTimestamp);

        die($possibleNow->format("Y-m-d H:i:s"));
    }


    protected function validateSelectOffTime()
    {
        $utcTimeZone = new DateTimeZone('UTC');
        $cetTimeZone = new DateTimeZone('CET');
        //hide the today if time is pass
        $nowTime = new DateTime(null, $cetTimeZone);

        $nearestPossibleTimeString = $this->getNearestPossibleSpecificTime();
        $nearestPossibleTimeObj = new DateTime($nearestPossibleTimeString, $cetTimeZone);

        $back_office_day_count = Configuration::get('URBIT_MODULE_TIME_SPECIFIED');
        $end_date = $back_office_day_count ?
        date(
            'Y-m-d',
            strtotime('+' . $back_office_day_count . ' day', $nowTime->getTimestamp())
        ) :
        date(
            'Y-m-d',
            strtotime('+4 days', $nowTime->getTimestamp())
        );

        $endDateTimeStamp =  strtotime($end_date);
        $DATETIME = 'Y-m-d\TH:i:sP';

        $ret_possible_delivery_hours = UrbitStoreApi::getDeliveryHours();
        $delivery_hours_items = $ret_possible_delivery_hours->hasError()?
          array() : $ret_possible_delivery_hours->args->items;

        $days = array();
        $from_dates = array();
        $to_dates = array();

        foreach ($delivery_hours_items as $item) {
            if ($item->closed == false) {
                $date = DateTime::createFromFormat($DATETIME, $item->first_delivery, $utcTimeZone);
                $date->setTimezone($cetTimeZone);
                $date->setTimestamp(strtotime('+5 minutes', $date->getTimestamp()));
                $dateTimestamp = $date->getTimestamp();

                //filter by config days count
                if ($dateTimestamp > $endDateTimeStamp) {
                    continue;
                }

                $date2 = DateTime::createFromFormat($DATETIME, $item->last_delivery, $utcTimeZone);
                $date2->setTimezone($cetTimeZone);
                $date2->setTimestamp(strtotime('-5 minutes', $date2->getTimestamp()));

                if ($nearestPossibleTimeObj->getTimestamp() > $date2->getTimestamp()) {
                    continue;
                }

                $from_dates[] = $date->format('Y-m-d H:i:s');
                $days[] = $date->format('Y-m-d');

                $to_dates[] = $date2->format('Y-m-d H:i:s');
            }
        }

        $dateT = new DateTime($to_dates[0], $cetTimeZone);
        $dateN = new DateTime($nowTime->format('Y-m-d H:i:s'), $cetTimeZone);

        $ubtTimestamp = $dateT->getTimestamp();

        $nowHour = $dateN->format('H');

        if ($nowHour > 22) {
            $newTimeStamp = $dateN->getTimestamp();
        } else {
            $dateN->modify('+2 hours');
            $newTimeStamp = $dateN->getTimestamp();
        }

        if (($ubtTimestamp < $newTimeStamp) || ($nowHour > 22)) {
            array_shift($days);
        }

        $ret = Tools::jsonEncode($days);

        die($ret);
    }
}
