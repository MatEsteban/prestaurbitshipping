<?php
/**
 * Licence of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class LicenceParameter
{

    /**
     * Domain name of website install module
     * @var string
     */
    public $domain;

    /**
     * Module installed in website customer.
     * @var string
     */
    public $module_name;

    /**
     * current version install in client server
     * @var string
     */
    public $version;

    /**
     * Datetime install module
     * @var string (DD-MM-YYYY)
     */
    public $install_date;

    /**
     * Datetime last update
     * @var string (DD-MM-YYYY)
     */
    public $last_update;

    /**
     * Define status test or live
     * @var int
     */
    public $demo;

    /**
     * defined action to server
     * @var string
     */
    public $action;

    /**
     * key access to module licence in server
     * @var string
     */
    public $api_key;

    /**
     * convert LicenceParameter to array
     * @return array
     * array(
     *       'domain' => string
     *       'module_name' => string
     *       'version' => string
     *       'install_date' => string (DD-MM-YYYY)
     *       'last_update' => string (DD-MM-YYYY)
     *       'demo' => int
     *       'action' => string
     *       'api_key' => string
     *       )
     */
    public function toArray()
    {
        $result = array();
        foreach ($this as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }
}
