<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

include(dirname(__FILE__) . '/api/UbitAPIWrapper.php');
include(dirname(__FILE__) . '/api/UrbOrder.php');
include(dirname(__FILE__) . '/api/Article.php');
include(dirname(__FILE__) . '/api/UrbAddress.php');
include(dirname(__FILE__) . '/api/StoreLocation.php');
include(dirname(__FILE__) . '/api/Consumer.php');
include(dirname(__FILE__) . '/api/Delivery.php');
include(dirname(__FILE__) . '/api/PickupLocation.php');


class UrbitStoreApi
{

    public function checkStoreAvailableNow()
    {
        $ret = array('store_available_now' => 'true', 'sp_time' => 'true');
        return $ret;
    }

    public function ajaxCheckZipCode($user_street, $user_postcode, $user_city)
    {
        $urbApi = new UbitAPIWrapper();
        //validate a postal code : 11143 Stockholm
        $validate_return = $urbApi->validateDeliveryAddress($user_street, $user_postcode, $user_city);

        return array(
            'ajaxCheckZipCode' => $validate_return->hasError() ? 'false' : 'true',
            'error' => $validate_return->error_message,
            'zipcode' => $user_postcode,
            'validate_return' => $validate_return->args
        );
    }

    public function ajaxCheckValidateDelivery($delivery_options)
    {
        $urbApi = new UbitAPIWrapper();

        //validate a delivery address
        $ret_delivery_validate = $urbApi->validateDeliveryAddress($delivery_options['del_street'], $delivery_options['del_zip_code'], $delivery_options['del_city']);

        return array(
            'ajaxCheckValidateDelivery' => $ret_delivery_validate->hasError() ? 'false' : 'true',
            'error_code' => $ret_delivery_validate->error_code,
            'error_message' => $ret_delivery_validate->error_message,
            'status' => ''
        );
    }

    public function checkAddressDeliverable($user_delivery_address)
    {
        $urbApi = new UbitAPIWrapper();

        $street = $user_delivery_address['address1'];
        $postcode = $user_delivery_address['postcode'];
        $city = $user_delivery_address['city'];

        $validate_return = $urbApi->validateDeliveryAddress($street, $postcode, $city);

        $return = array();
        if ($validate_return->error == 0) {
            $return['deliverable'] = true;
            $return['deliverable_msg'] = '';
        } else {
            $return['deliverable'] = false;
            $return['deliverable_msg'] = $validate_return->error_message;
        }

        return $return;
    }

    /**
     * @return UrbitShippingResponse
     */
    public static function getDeliveryHours()
    {
        $urbApi = new UbitAPIWrapper();

        return  $urbApi->getDeliveryHours();
    }

    /**
     * @return UrbitShippingResponse
     */
    public static function getCheckoutInformation($checkoutId)
    {
        $urbApi = new UbitAPIWrapper();

        return $urbApi->getCheckoutInformation($checkoutId);
    }

    /**
     * @param $items
     * @return UrbitShippingResponse
     */
    public static function createCart($items)
    {
        $urbApi = new UbitAPIWrapper();

        return $urbApi->createCart($items);
    }

    /**
     * @param $cartId
     * @return UrbitShippingResponse
     */
    public static function createCheckout($cartId)
    {
        $urbApi = new UbitAPIWrapper();

        return $urbApi->createCheckout($cartId);
    }

    public static function updateCheckout($checkoutId, $info)
    {
        $urbApi = new UbitAPIWrapper();

        return $urbApi->updateCheckout($checkoutId, $info);
    }

    public static function createOrder($order_data)
    {
        //Create main order object
        $order = new UrbOrder();
        $order->retailer_reference_id = $order_data['order_retailer_reference_id'];
        $order->delivery_type = $order_data['order_delivery_type'];
        $order->postal_code = $order_data['order_postal_code'];
        $order->delivery_expected_at = $order_data['delivery_expected_at'];
        $order->order_direction = $order_data['order_direction'];
        $order->total_amount_excl_vat = $order_data['order_total_amount_excl_vat'];
        $articles = array();

        foreach ($order_data['order_products'] as $product) {
            //Create articles and push them into the articles array.
            $article = new Article();
            $article->identifier = $product['reference'];
            $article->quantity = $product['quantity'];
            $article->description = $product['name'];

            array_push($articles, $article);
        }

        //Set articles array
        $order->articles = $articles;

        //Create store location
        $storelocation = new StoreLocation();
        $storelocation->id = $order_data['storelocation_id'];

        //Create consumer object
        $consumer = new Consumer();
        $consumer->first_name = $order_data['consumer_first_name'];
        $consumer->last_name = $order_data['consumer_last_name'];
        $consumer->email = $order_data['consumer_email'];
        $consumer->cell_phone = trim($order_data['consumer_cell_phone']);
        $consumer->consumer_comment = $order_data['consumer_consumer_comment'];

        //Create address
        $address = new UrbAddress();
        $address->street = $order_data['address_street'];
        $address->postal_code = $order_data['address_postal_code'];
        $address->city = $order_data['address_city'];
        $address->country = $order_data['address_country'];
        $address->care_of = $order_data['address_care_of'];
        $address->street2 = $order_data['address_street2'];
        $address->company_name = $order_data['address_company_name'];

        $consumer->address = $address;

        //assigning consumer and store location objects to main order object
        $order->consumer = $consumer;
        $order->store_location = $storelocation;

        //Creates delivery object
        $del = new Delivery();
        $del->delivery_type = $order_data['delivery_delivery_type'];
        $del->postal_code = $order_data['delivery_postal_code'];
        $del->delivery_expected_at = $order_data['delivery_expected_at'];

        //creates pickup location object.
        $pickuplocation = new PickupLocation();
        $pickuplocation->id = $order_data['pickuplocation_id'];

        //set articles and pickup location for the delivery
        $del->pickup_location = $pickuplocation;
        $del->articles = $articles;


        $start_date = date("Y-m-d");
        $startDate = time();
        $end_date = date('Y-m-d', strtotime('+1 day', $startDate));

        $urbApi = new UbitAPIWrapper();

        //Create an order
        $returnObj = $urbApi->createOrder($order);

        $id_land = 1;
        $template_name = 'order_error';  // this is file name here the file name is test.html
        $admin_email = Configuration::get('URBIT_ADMIN_EMAIL');
        $to = $admin_email;
        $toName = "Admin";
        $from = null;
        $fromName = "Urb-it";
        $title = "Urbit Order Fail";
        $mailDir = _PS_MODULE_DIR_ . 'urbit/mails/'; //Directory with message templates


        if ($returnObj->hasError()) {
            $templateVars = array();
            $templateVars['{order_ref}'] = $order->retailer_reference_id;
            $templateVars['{error_message}'] = $returnObj->error_message;
            $templateVars['{error_code}'] = $returnObj->error_code;
            $deliveryTime = date("Y-m-d H:i:s", strtotime(date($order->delivery_expected_at)));
            $templateVars['{delivery_expected_at}'] = $deliveryTime;

            $result = Mail::Send(
                $id_land,
                $template_name,
                $title,
                $templateVars,
                $to,
                $toName,
                $from,
                $fromName,
                null,
                null,
                $mailDir
            );
            $return = '<p class="alert alert-warning">' . $returnObj->error_message . '</p>';
        } else {
            $return = '<p class="alert alert-success">Urbit Order Successfully Created !</p>';
        }

        return $return;
    }
}
