<?php
/**
 * Urbit api of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UDomesticParcel extends UrbitPac
{

    /**
     * Required. From post code.
     * @var int
     */
    public $from_postcode;
    /**
     * Required. To post code.
     * @var int
     */
    public $to_postcode;
    /**
     * Required. The length of the parcel in cm.
     * @var float
     */
    public $length;
    /**
     * Required. The width of the parcel in cm.
     * @var float
     */
    public $width;
    /**
     * Required. The height of the parcel in cm.
     * @var float
     */
    public $height;
    /**
     * Required. The weight of the parcel in kg.
     * @var float
     */
    public $weight;
    /**
     * Required. The service code of the parcel
     * @var string
     */
    public $service_code;
    /**
     * Optional. The option code of the parcel.
     * @var string
     */
    public $option_code;
    /**
     * Optional. The sub-option code of the parcel, multi-valued.
     * @var string
     */
    public $suboption_code;
    /**
     * Optional. The monetary value of the extra cover, represented in dollar value only.
     * @var float
     */
    public $extra_cover;
    /**
     * uri connect domestic parcel
     * @var string
     */
    protected $uri = 'postage/parcel/domestic/calculate';

    /**
     * function construct object
     * @return object
     */
    public function __construct(
        $from_postcode,
        $to_postcode,
        $length,
        $width,
        $height,
        $weight,
        $service_code,
        $option_code = null,
        $suboption_code = null,
        $extra_cover = null
    ) {
        parent::__construct();
        $this->from_postcode = $from_postcode;
        $this->to_postcode = $to_postcode;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->weight = $weight;
        $this->service_code = $service_code;
        $this->option_code = $option_code;
        $this->suboption_code = $suboption_code;
        $this->extra_cover = $extra_cover;
        $this->api_entry = $this->api_uri . $this->uri;
    }

    /**
     * function get shipping cost with params:
     * @return array shipping cost | array error
     * <pre>
     */
    public function getShippingCost()
    {
        $params = array(
            'from_postcode' => $this->from_postcode,
            'to_postcode' => $this->to_postcode,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'weight' => $this->weight,
            'service_code' => $this->service_code,
        );
        // check fiels optional add in array params
        $params = $this->checkEmptyInput('option_code', $this->option_code, $params);
        $params = $this->checkEmptyInput('suboption_code', $this->suboption_code, $params);
        $params = $this->checkEmptyInput('extra_cover', $this->extra_cover, $params);
        $response = $this->request($this->api_entry, $params);
        return $response;
    }
}
