<?php
/**
 * Urbit api of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UDomesticLetter extends UrbitPac
{

    /**
     * Required. The service code of the letter
     * @var string
     */
    public $service_code;
    /**
     * Required. The weight of the letter in grams (max weight is 500g)
     * @var float
     */
    public $weight;
    /**
     * Optional. The option code of the letter.
     * @var string
     */
    public $option_code;
    /**
     * Optional. The sub-option code of the letter, multi-valued.
     * @var string
     */
    public $suboption_code;
    /**
     * Optional. The monetary value of the extra cover, represented in dollar value only.
     * @var float
     */
    public $extra_cover;
    /**
     * uri api support get domestic Letter
     * @var string
     */
    protected $uri = 'postage/letter/domestic/calculate';

    /**
     * function construct object
     */
    public function __construct(
        $service_code,
        $weight,
        $option_code = null,
        $suboption_code = null,
        $extra_cover = null
    ) {
        parent::__construct();
        $this->service_code = $service_code;
        $this->weight = $weight;
        $this->option_code = $option_code;
        $this->suboption_code = $suboption_code;
        $this->extra_cover = $extra_cover;
        $this->api_entry = $this->api_uri . $this->uri;
    }

    /**
     * function get shipping cost with params:
     * @return array list shipping cost | array error
     * <pre>
     */
    public function getShippingCost()
    {
        $params = array(
            'service_code' => $this->service_code,
            'weight' => $this->weight,
        );
        $params = $this->checkEmptyInput('option_code', $this->option_code, $params);
        $params = $this->checkEmptyInput('suboption_code', $this->suboption_code, $params);
        $params = $this->checkEmptyInput('extra_cover', $this->extra_cover, $params);
        $response = $this->request($this->api_entry, $params);
        return $response;
    }
}
