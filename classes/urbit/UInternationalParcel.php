<?php
/**
 * Urbit api of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UInternationalParcel extends UrbitPac
{

    /**
     * Required. Country code (Code retrieved from Country data oriented method)
     * @var string
     */
    public $country_code;
    /**
     * Required. The weight of the parcel in kg (max weight is 20kg).
     * @var float
     */
    public $weight;
    /**
     * Required. The service code of the letter.
     * @var string
     */
    public $service_code;
    /**
     * Optional. The option code of the parcel, multi-valued.
     * @var string
     */
    public $option_code;
    /**
     * Optional. The monetary value of the extra cover, represented in dollar value only.
     * @var type
     */
    public $extra_cover;
    /**
     * uri connet to api international parcel
     * @var string
     */
    protected $uri = 'postage/parcel/international/calculate';

    /**
     * function construct object.
     * @return object.
     */
    public function __construct($country_code, $weight, $service_code, $option_code = null, $extra_cover = null)
    {
        parent::__construct();
        $this->api_entry = $this->api_uri . $this->uri;
        $this->country_code = $country_code;
        $this->weight = $weight;
        $this->service_code = $service_code;
        $this->option_code = $option_code;
        $this->extra_cover = $extra_cover;
    }

    /**
     * function
     * @return array shipping cost | array error
     * <pre>
     */
    public function getShippingCost()
    {
        $params = array(
            'country_code' => $this->country_code,
            'weight' => $this->weight,
            'service_code' => $this->service_code,
        );
        $params = $this->checkEmptyInput('option_code', $this->option_code, $params);
        $params = $this->checkEmptyInput('extra_cover', $this->extra_cover, $params);
        $response = $this->request($this->api_entry, $params);

        return $response;
    }
}
