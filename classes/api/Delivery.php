<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class Delivery
{

    public $delivery_type;
    public $postal_code;
    public $delivery_expected_at;
    public $articles;
    public $pickup_location;

    public function __construct($delivery_type = null, $postal_code = null, $delivery_expected_at = null)
    {
        $this->delivery_type = $delivery_type;
        $this->postal_code = $postal_code;
        $this->delivery_expected_at = $delivery_expected_at;
    }
}
