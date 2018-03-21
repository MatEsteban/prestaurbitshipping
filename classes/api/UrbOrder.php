<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UrbOrder
{

    const DELIVERY_TYPE_ONE_HOUR = 'OneHour';
    const DELIVERY_TYPE_SPECIFIC = 'Specific';
    const ORDER_DIRECTION_STORE_TO_CONSUMER = 'StoreToConsumer';
    const ORDER_DIRECTION_CONSUMER_TO_STORE = 'ConsumerToStore';
    public $retailer_reference_id;
    public $delivery_type;
    public $postal_code;
    public $delivery_expected_at;
    public $order_direction;
    public $store_location;
    public $articles;
    public $consumer;
    public $total_amount_excl_vat;

    public function __construct(
        $retailer_reference_id = null,
        $delivery_type = null,
        $postal_code = null,
        $delivery_expected_at = null,
        $order_direction = null,
        $total_amount_excl_vat = null
    ) {
        $this->retailer_reference_id = $retailer_reference_id;
        $this->delivery_type = $delivery_type;
        $this->postal_code = $postal_code;
        $this->delivery_expected_at = $delivery_expected_at;
        $this->order_direction = $order_direction;
        $this->total_amount_excl_vat = $total_amount_excl_vat;
    }
}
