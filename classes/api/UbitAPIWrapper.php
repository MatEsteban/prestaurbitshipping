<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

include("Config.php");
include("UrbitShippingResponse.php");

class UbitAPIWrapper
{
    public $conf;
    public $response;
    public $path;
    public $method = 'GET';
    public $params = array();
    public $request = array();
    public $request_body = '';
    public $test = true;
    public $dev = false;
    public $result;
    public $needAuthorization = false;

    public $context;

    public function __construct()
    {
        $config = new Config();
        $this->conf = $config->getConfig();

        $this->initContext();
    }

    private function initContext()
    {
        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
            // global $smarty, $cookie;
            $smarty = $this->context->smarty;
            $cookie = $this->context->cookie;

            $this->context = (object) array(
                "smarty" => $smarty,
                "cookie" => $cookie,
            );
        }
    }

    /**
     * Creates a new Order.
     *
     * @param order object with required sub objects such as articles, consumer, store location.
     * @param Make sure that you check  the validity of the postal code before creating an order.
     * @return UrbitShippingResponse response object with error status, data and error message attributs.
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
     * @param $args
     * @return UrbitShippingResponse
     */
    public function createCart($args)
    {
        $this->getPath('v2/carts');
        $this->method = 'POST';

        if (!isset($this->params)) {
            $this->params = array();
        }

        $this->params = $args;

        return $this->send();
    }

    /**
     * @param $args
     * @return UrbitShippingResponse
     */
    public function createCheckout($args)
    {
        $this->getPath('v2/checkouts');
        $this->method = 'POST';

        if (!isset($this->params)) {
            $this->params = array();
        }

        $this->params = $args;

        return $this->send();
    }

    /**
     * PUT request for setting information about delivery (time, message, information about recipient)
     * @param $checkoutId string
     * @param $args array
     * @return UrbitShippingResponse
     */
    public function updateCheckout($checkoutId, $args)
    {
        $this->getPath('v2/checkouts/' . $checkoutId . '/delivery');
        $this->method = 'PUT';

        if (!isset($this->params)) {
            $this->params = array();
        }

        $this->params = $args;

        return $this->send();
    }

    /**
     * Get path for a given API function.
     *
     * @param string $path name of the function which is required to add as a suffix for the API endpoint.
     * @return string complete API endpoint path.
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
     * @return UrbitShippingResponse response json object from Urbit API.
     */
    public function send()
    {
        $endpoint = $this->path;

        $json = $this->params ? Tools::jsonEncode($this->params) : '';



        $this->request_body = $json;

        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

        if ($this->method != 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->conf["connecttimeout"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->conf["timeout"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $headers = $this->method === 'GET' && $this->needAuthorization == false ?
            array(
                'Content-Type: application/json',
                'X-API-Key: ' . $this->getUrbitApiKey(),
            ) :
            array(
                'Content-Type: application/json',
                'X-API-Key: ' . $this->getUrbitApiKey(),
                'Authorization: ' . $this->getBearerJWTToken()
            );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $apiRequest = array();
        $apiRequest['headers'] = $headers;
        $apiRequest['url'] = $endpoint;
        $apiRequest['method'] = $this->method;
        $apiRequest['params'] = $json;
        $this->getApiLogs($apiRequest, 'REQUEST');

        $this->result = Tools::jsonDecode(curl_exec($ch));

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->request = curl_getinfo($ch);

        /*if ($this->method == "PUT") {
            print_r($httpStatusCode);exit;
        }*/

        curl_close($ch);
        $this->response = new UrbitShippingResponse($this->result, $this->method, $httpStatusCode);
        $this->getApiLogs($this->response, 'UrbitShippingResponse');

        return $this->response;
    }

    public function getUrbitApiKey()
    {
        return $this->conf["store_key"];
    }

    public function getBearerJWTToken()
    {
        return  $this->conf["bearer_jwt_token"];
    }

    /**
     * Create an authorization header to access the Urbit API.
     *
     * @param string $store_key
     * @param string $shared_secret
     * @param string $method
     * @param string $url
     * @param string $json
     * @return a UWA encrypted authorization security string.
     * @internal param store $string key, string shared_secret, string mwthod(POST or GET), string url(complete endpoint url), json string
     * (required set of parameters for an API functionality.
     */
    //TODO: remove old authorization function
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
     * Validates delivery address.
     *
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @return UrbitShippingResponse object with error status, data and error message attributs.
     */
    public function validateDeliveryAddress($street = '', $postcode = '', $city = '')
    {
        $this->getPath('v2/address?' . http_build_query(array(
            'street' => $street,
            'postcode' => $postcode,
            'city' => $city,
        )));

        $this->method = 'GET';
        $this->needAuthorization = false;

        return $this->send();
    }

    /*get cart ID from $smarty*/

    /**
     * Get possible delivery hours.
     *
     * @return UrbitShippingResponse response object with error status, data and error message attributs.
     * @internal param from $string date and string to date.
     */
    public function getDeliveryHours()
    {
        $this->getPath('/v2/deliveryhours');
        $this->method = 'GET';
        $this->needAuthorization = false;

        return $this->send();
    }

    public function getCheckoutInformation($checkoutId)
    {
        $this->getPath('/v2/checkouts/' . $checkoutId);
        $this->method = 'GET';
        $this->needAuthorization = true;

        return $this->send();
    }
}
