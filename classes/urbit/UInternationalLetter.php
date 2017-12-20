<?php
/**
 * Urbit api of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UInternationalLetter extends UrbitPac
{

    /**
     * Required. Country code (Code retrieved from Country data oriented method).
     * @var string
     */
    public $country_code;
    /**
     * Optional. The letter weight in grams (max weight is 500g , if not specified, assume it is a postcard).
     * @var float
     */
    public $weight;
    /**
     * Required. The service code of the letter.
     * @var string
     */
    public $service_code;
    /**
     * Optional. The option code of the letter, multi-valued.
     * @var string
     */
    public $option_code;
    /**
     * Optional. The sub-option code of the letter.
     * @var string
     */
    public $suboption_code;
    /**
     * Optional. The monetary value of the extra cover, represented in dollar value only.
     * @var float
     */
    public $extra_cover;
    /**
     * uri connect to api international letter
     * @var string
     */
    protected $uri = 'postage/letter/international/calculate';

    /**
     * function construct object
     * @return object.
     */
    public function __construct(
        $country_code,
        $service_code,
        $weight = null,
        $option_code = null,
        $suboption_code = null,
        $extra_cover = null
    ) {
        parent::__construct();
        $this->api_entry = $this->api_uri . $this->uri;
        $this->country_code = $country_code;
        $this->weight = $weight;
        $this->service_code = $service_code;
        $this->option_code = $option_code;
        $this->suboption_code = $suboption_code;
        $this->extra_cover = $extra_cover;
    }

    /**
     * function get shipping cost with params:
     * @return array shipping cost | array error
     * <pre>
     */
    public function getShippingCost()
    {
        $params = array(
            'country_code' => $this->country_code,
            'service_code' => $this->service_code,
        );
        $params = $this->checkEmptyInput('weight', $this->weight, $params);
        $params = $this->checkEmptyInput('option_code', $this->option_code, $params);
        $params = $this->checkEmptyInput('suboption_code', $this->suboption_code, $params);
        $params = $this->checkEmptyInput('extra_cover', $this->extra_cover, $params);
        $response = $this->request($this->api_entry, $params);
        return $response;
    }
}
