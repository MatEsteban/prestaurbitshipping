<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license  Urb-it
 */

require_once(dirname(__FILE__) . '/../../classes/UrbitStoreApi.php');
require_once(dirname(__FILE__) . '/../../models/UrbitCart.php');

class UrbitUpdateDeliveryInfoModuleFrontController extends FrontController
{
    public function initContent()
    {
        parent::initContent();

        $cronParam = Tools::getValue('cron');

        if ($cronParam) {
            $this->checkOrdersForUpdateCheckouts();
        }
    }

    protected function checkOrdersForUpdateCheckouts()
    {
        $unsentCarts = UrbitCart::getUnsentCarts();

        $nowTime = new DateTime(null, new DateTimeZone("UTC"));
        $nowTimestamp = strtotime($nowTime->format('Y-m-d H:i:s'));

        foreach ($unsentCarts as $cart) {
            $preparationEndTime = new DateTime($cart['preparation_end_time'], new DateTimeZone("UTC"));
            $preparationEndTimestamp = strtotime($preparationEndTime->format('Y-m-d H:i:s'));

            if ($preparationEndTimestamp <= $nowTimestamp) {
                $this->sendUpdateCheckout($cart['id_urbit_order_cart']);
            }
        }
    }

    /**
     * Send delivery information to Urb-it by PUT request
     * @param $urbitCartId
     */
    protected function sendUpdateCheckout($urbitCartId)
    {
        $cart = UrbitCart::getUrbitCart($urbitCartId);

        if (!empty($cart)) {
            $checkoutId = $cart[0]['checkout_id'];

            if ($checkoutId == "") {
                return;
            }

            $deliveryDate = new DateTime($cart[0]['delivery_time'], new DateTimeZone("UTC"));
            $formattedDeliveryDate = $deliveryDate->format('Y-m-d\TH:i:sP');

            $requestArray = array(
                'delivery_time' => $formattedDeliveryDate,
                'message'       => $cart[0]['delivery_advise_message'],
                'recipient'     => array(
                    'first_name'   => $cart[0]['delivery_first_name'],
                    'last_name'    => $cart[0]['delivery_last_name'],
                    'address_1'    => $cart[0]['delivery_street'],
                    'address_2'    => "",
                    'city'         => $cart[0]['delivery_city'],
                    'postcode'     => $cart[0]['delivery_zip_code'],
                    'phone_number' => $cart[0]['delivery_is_gift'] ? $cart[0]['delivery_gift_receiver_phone'] :
                        $cart[0]['delivery_contact_phone'],
                    'email'        => $cart[0]['delivery_contact_mail']
                )
            );

            //send order information to API to set delivery information
            $ret_create_order = UrbitStoreApi::updateCheckout($checkoutId, $requestArray);

            UrbitCart::updateSentFlag('true', $cart[0]['id_urbit_order_cart']);

            if (isset($ret_create_order->httpCode)) {
                UrbitCart::updateResponseCode($ret_create_order->httpCode, $cart[0]['id_urbit_order_cart']);
            }
        }
    }
}
