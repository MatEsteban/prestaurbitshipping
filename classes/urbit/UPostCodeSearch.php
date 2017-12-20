<?php
/**
 * Urbit api of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UPostCodeSearch extends UrbitApi
{

    /**
     * Required. The search criterion used to drive the search query.
     * @var string
     */
    public $q;
    /**
     * Optional. Used to filter possible search results.
     * @var int
     */
    public $state;
    /**
     * Optional. Set value to true, if post boxes should be excluded from search results.
     * @var boolean
     */
    public $exclude_post_box_flag;
    /**
     * uri connet to api post code search
     * @var string
     */
    protected $uri = 'postcode/search';

    /**
     * function construct object.
     * @return object
     */
    public function __construct($q, $state = null, $exclude_post_box_flag = true)
    {
        parent::__construct();
        $this->q = $q;
        $this->state = $state;
        $this->exclude_post_box_flag = $exclude_post_box_flag;
        $this->api_entry = $this->api_uri . $this->uri;
    }

    /**
     * @param type $params is array with 3 parameters:
     * format: json || xml (default)
     * @return array post code | array error
     * <pre>
     */
    public function searchPostCode()
    {
        $params = array(
            'q' => $this->q,
        );
        $params = $this->checkEmptyInput('state', $this->state, $params);
        $params = $this->checkEmptyInput('excludePostBoxFlag', $this->exclude_post_box_flag, $params);
        $response = $this->request($this->api_entry, $params);

        return Tools::jsonEncode($response, true);
    }
}
