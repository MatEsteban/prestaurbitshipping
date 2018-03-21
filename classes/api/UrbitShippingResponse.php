<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

/**
 * Class UrbitShippingResponse
 *
 * @property string $status
 * @property string $method
 * @property string $httpCode
 * @property string $error
 * @property string $error_message
 * @property string $error_code
 * @property string $error_data
 * @property mixed $data
 * @property mixed $args
 */
class UrbitShippingResponse
{
    const NO_ERROR = "0";
    const HAS_ERROR = "1";

    const HTTP_STATUS_GET = "get";
    const HTTP_STATUS_POST = "post";
    const HTTP_STATUS_PUT = "put";

    const HTTP_STATUS_SUCCESS_GET = "200";
    const HTTP_STATUS_SUCCESS_POST = "201";
    const HTTP_STATUS_SUCCESS_PUT = "204";

    const HTTP_STATUS_ERROR_BAD_REQUEST = "400";
    const HTTP_STATUS_ERROR_UNAUTHORISED = "404";
    const HTTP_STATUS_ERROR_NOT_FOUND = "404";
    const HTTP_STATUS_ERROR_CONFLICT = "409";
    const HTTP_STATUS_ERROR_UNPROCESSABLE_ENTITY = "422";
    const HTTP_STATUS_ERROR_TOO_MANY_REQUESTS = "429";

    const HTTP_STATUS_SERVER_ERROR = "500";
    const HTTP_STATUS_SERVER_ERROR_SERVICE_UNAVAILABLE = "503";
    const HTTP_STATUS_SERVER_ERROR_GATEWAY_TIMEOUT = "504";

    /**
     * @var string
     */
    protected $status = "";

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $httpCode;

    /**
     * @var string
     */
    protected $error = "";

    /**
     * @var string
     */
    protected $error_message = "";

    /**
     * @var string
     */
    protected $error_code = "";

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @var mixed
     */
    protected $args = null;

    /**
     * Custom object with attributes error status, error message and data.
     * The developer may check whether the response is an error by checking the 'error' attribute.
     * If the error value is 1 then it has an error. The developer can then get the error message by reading
     * the value of error_message attribute. If the error value is 0 the developer can retrieve response
     * data by reading the 'data' attribute.
     *
     * @param array|object $args
     * @param string $method
     * @param string $httpCode
     */
    public function __construct($args, $method, $httpCode)
    {
        $this->args = $args;

        $this->method = Tools::strtolower($method);
        $this->httpCode = $httpCode;

        $this->processResponseData();
    }

    /**
     * Process API response on errors
     */
    protected function processResponseData()
    {
        $args = &$this->args;

        $this->processHttpStatus();
        $hasError = $this->hasError();

        switch (true) {
            case isset($args->message) && $args->message == "An error has occurred.":
            case $hasError:
                $this->error_message = isset($args->message) ? $args->message: "An error has occurred.";
                $this->error_code = isset($args->code) ? $args->code : $this->httpCode;
                break;
            default:
                $this->data = $args;
                break;
        }
    }

    /**
     * Check HTTP code of answer
     */
    protected function processHttpStatus()
    {
        $statuses = array(
            self::HTTP_STATUS_GET => self::HTTP_STATUS_SUCCESS_GET,
            self::HTTP_STATUS_POST => self::HTTP_STATUS_SUCCESS_POST,
            self::HTTP_STATUS_PUT => self::HTTP_STATUS_SUCCESS_PUT,
        );

        $code = isset($statuses[$this->method]) ? $statuses[$this->method] : "";

        $this->error = $code == $this->httpCode ? self::NO_ERROR : self::HAS_ERROR;
    }

    public function hasError()
    {
        return $this->error !== self::NO_ERROR;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        $cls = get_class($this);

        throw new Exception("Try yo get unknown property {$cls}::\${$name}");
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return property_exists($this, $name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->args, JSON_PRETTY_PRINT);
    }
}
