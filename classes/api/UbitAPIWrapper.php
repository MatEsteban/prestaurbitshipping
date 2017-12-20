<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

include("Config.php");
include("ResponseObj.php");

class UbitAPIWrapper
{

    public $conf;
    public $object;
    public $path;
    public $method = 'GET';
    public $params = array();
    public $request = array();
    public $request_body = '';
    public $test = true;
    public $dev = false;
    public $result;

    public function __construct()
    {
        $config = new Config();
        $this->conf = $config->getConfig();

        $this->object = new ResponseObj();
        $this->initContext();
    }

    private function initContext()
    {
        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
           // global $smarty, $cookie;
            $this->context = new StdClass();
           /* $this->context->smarty = $smarty;
            $this->context->cookie = $cookie;*/
        }
    }

    /**
     * Creates a new Order.
     *
     * @return a response object with error status, data and error message attributs.
     */
    public function createOrder($args)
    {
        $this->getPath('order');
        $this->method = 'POST';
        if (!isset($this->params)) {
            $this->params = array();
        }

        $this->params = $args;
        return $this->send();
    }

    /**
     * Get path for a given API function.
     * @return a complete API endpoint path.
     */
    public function getPath($path = '')
    {
        if ($path) {
            $path = '/' . trim($path, '/');
        }

        if ($this->test) {
            $this->path = ($this->dev ? $this->conf["base_path_dev"] : $this->conf["base_path_test"]) . $path;
        } else {
            $this->path = $this->conf["base_path"] . $path;
        }
        return $this->path;
    }

    /**
     * Send API request.
     *
     * @return a response json object from Urbit API.
     */
    public function send()
    {

        $endpoint = $this->path;

        $json = $this->params ? Tools::jsonEncode($this->params) : '';
        $this->request_body = $json;

        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        if ($this->method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->conf["connecttimeout"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->conf["timeout"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $headers = array(
            'Accept-Encoding: gzip,deflate',
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'Content-Length: ' . Tools::strlen($json),
            'Authorization: ' . $this->getAuthorizationHeader(
                $this->conf["store_key"],
                $this->conf["shared_secret"],
                $this->method,
                $endpoint,
                $json
            )
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $apiRequest = array();
        $apiRequest['headers'] = $headers;
        $apiRequest['url'] = $endpoint;
        $apiRequest['method'] = $this->method;
        $apiRequest['params'] = $json;
        $this->getApiLogs($apiRequest, 'REQUEST');

        $this->result = Tools::jsonDecode(curl_exec($ch));

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->request = curl_getinfo($ch);

        curl_close($ch);

        $this->getApiLogs($this->object->createObject($this->result), 'RESPONSE');

        return $this->object->createObject($this->result);
    }

    /**
     * Create an authorization header to access the Urbit API.
     *
     * @param string $store_key
     * @param string $shared_secret
     * @param string $method
     * @param string $url
     * @param string $json
     * (required set of parameters for an API functionality.
     * @return string
     */
    public function getAuthorizationHeader($store_key = '', $shared_secret = '', $method = '', $url = '', $json = '')
    {
        // Ensure JSON content is encoded a UTF-8
        $json = utf8_encode($json);

        // Create MD5 digest ($raw_output = true)
        $md5_digest = md5($json, true);

        // Create Base64 digest
        $base64_digest = base64_encode($md5_digest);

        // Get current Unix timestamp
        $timestamp = time();

        // Create a unique nonce
        $nonce = md5(microtime(true) . $_SERVER['REMOTE_ADDR'] . rand(0, 999999));

        // Concatenate data
        $msg = implode('', array(
            $store_key,
            Tools::strtoupper($method),
            Tools::strtolower($url),
            $timestamp,
            $nonce,
            $json ? $base64_digest : ''
        ));
        #var_dump($msg);
        // Decode shared secret (used as a byte array)
        $byte_array = base64_decode($shared_secret);

        // Create signature
        $signature = base64_encode(hash_hmac('sha256', utf8_encode($msg), $byte_array, true));

        // Return header
        return 'UWA ' . implode(':', array($store_key, $signature, $nonce, $timestamp));
        //return ($json);
    }

    private function getApiLogs($request, $type)
    {
        $apiCall = Tools::jsonEncode($request);
        $id_cart = $this->context->cart->id;
        if (!$id_cart) {
            $id_cart = 000;
        }

        $sql = "INSERT INTO `" . _DB_PREFIX_ . "urbit_api_log`
                                    (`cart_id`,`type`, `payload`)
                            VALUES($id_cart, '$type', '" . $apiCall . "')";

        Db::getInstance()->execute($sql);
    }

    /**
     * Validates a delivery.
     *
     * @return a response object with error status, data and error message attributs.
     */
    public function validateDelivery($args)
    {
        $this->getPath('delivery/validate');
        $this->method = 'POST';
        if (!isset($this->params)) {
            $this->params = array();
        }

        $this->params = $args;

        return $this->send();
    }

    /**
     * Validates postal code.
     *
     * @param string postal code.
     * @return  response object with error status, data and error message attributs.
     */
    public function validatePostalCode($args)
    {
        $this->getPath('postalcode/validate');
        $this->method = 'POST';
        if (!isset($this->params)) {
            $this->params = array();
        }

        $this->params = array('postal_code' => $args);
        return $this->send();
    }

    /*get cart ID from $smarty*/

    /**
     * Get opening hours.
     *
     * @param string $from
     * @param string $to
     * @return a response object with error status, data and error message attributs.
     * @internal param from $string date and string to date.
     */
    public function getOpeningHours($from = '', $to = '')
    {
        $this->getPath('openinghours/?from=' . $from . '&to=' . $to);
        $this->method = 'GET';

        return $this->send();
    }
}
