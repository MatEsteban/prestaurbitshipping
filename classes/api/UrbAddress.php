<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UrbAddress
{

    public $street;
    public $postal_code;
    public $city;
    public $country;
    public $care_of;
    public $street2;
    public $company_name;

    public function __construct(
        $street = null,
        $postal_code = null,
        $city = null,
        $country = null,
        $care_of = null,
        $street2 = null,
        $company_name = null
    ) {
        $this->street = $street;
        $this->postal_code = $postal_code;
        $this->city = $city;
        $this->country = $country;
        $this->care_of = $care_of;
        $this->street2 = $street2;
        $this->company_name = $company_name;
    }
}
