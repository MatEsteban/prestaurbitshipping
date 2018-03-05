<?php
/**
 * Urbit api of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UrbitApi
{

    protected $api_uri;
    protected $api_key;
    protected $format;

    public function __construct()
    {
        $this->format = UrbitOutput::XML_FORMAT;
    }

    /**
     * function set output return is xml | json (default xml)
     * @param string(xml|json) $format
     * @return UrbitApi
     */
    public function setOutputFormat($format = UrbitOutput::XML_FORMAT)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * function request define: output, uri, request data from Api.
     * @param string $uri
     * @param array $params
     * @return array data shipping cost or array error
     * if error
     * Array ( [error] => Array ( [errorMessage] => ...... ) )
     */
    protected function request($uri, $params)
    {
        switch ($this->format) {
            case UrbitOutput::JSON_FORMAT:
                $uri = $uri . '.' . $this->format;
                break;
            default:
                $uri = $uri . '.' . UrbitOutput::XML_FORMAT;
                break;
        }

        $url = $uri . '?' . $this->encode($params);
        return $this->getValueRequest($url);
    }

    /**
     * Encode shiping detail for urbit_post_shipping. Exclude registered and insured.
     * @param type $data
     * @return string
     */
    protected function encode($data = array())
    {
        $query = '';
        foreach ($data as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            if (is_array($values)) {
                foreach ($values as $value) {
                    if (Tools::strlen($query) > 0) {
                        $query .= '&';
                    }
                    $query .= $key;
                    $query .= '=' . urlencode($value);
                }
            }
        }
        return $query;
    }

    /**
     * detect method which server support
     * @param string $url
     * @return Array
     */
    protected function getValueRequest($url)
    {
        $result = array();
        $options = array(
            'http' =>
                array(
                    'method' => 'GET',
                    'header' => array(
                        'Content-type: application/x-www-form-urlencoded' . "\r\n"
                        . 'AUTH-KEY:' . $this->api_key . "\r\n"
                    )
                )
        );
        $stream_context = stream_context_create($options);
        $content = Tools::file_get_contents($url, true, $stream_context);
        if ($content) {
            // define data return
            if ($this->format == UrbitOutput::XML_FORMAT) {
                $result = $this->xml2array($content);
            } else {
                $result = $this->json2array($content);
            }
        }
        return $result;
    }

    /**
     *
     * @param xml $contents
     * @return array
     */
    protected function xml2array($contents)
    {
        $xml = simplexml_load_string($contents);
        return (Tools::jsonDecode(Tools::jsonEncode($xml), true));
    }

    /**
     * function json2array() will convert the given json text to an array.
     * @param json $json
     * @return array
     */
    public function json2array($json)
    {
        if (get_magic_quotes_gpc()) {
            $json = Tools::stripslashes($json);
        }
        $json_array = $this->objectToArray(Tools::jsonDecode($json));
        return $json_array;
    }

    /**
     * function objectToArray() will convert object to an array
     * @param $data is array or object
     * @return array
     */
    protected function objectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->objectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    /**
     * function check isset and !empty of param
     * @param string $field is name of param
     * @param string $value is value of param
     * @param array $params is array params old
     * @return array params new
     */
    protected function checkEmptyInput($field, $value, array $params = array())
    {
        if (isset($value) && !empty($value)) {
            $data_input = array($field => $value);
            $params = array_merge($params, $data_input);
        }
        return $params;
    }
}
