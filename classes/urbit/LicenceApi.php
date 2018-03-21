<?php
/**
 * Licence of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class LicenceApi
{

    /**
     * Url to other server.
     * @var string
     */
    protected $url = 'http://prestashopextensions.com/m/licence/Management';

    /**
     * Key access to module licence in server.
     * @var string
     */
    protected $api_key;

    /**
     * Entity data of module.
     * @var Object
     */
    protected $module;

    /**
     * Construct
     * @param string $api_key
     * @param Module $module
     */
    public function __construct($api_key, Module $module)
    {
        $this->api_key = !empty($api_key) ? $api_key : null;
        if (Validate::isLoadedObject($module)) {
            $this->module = $module;
        }
    }

    /**
     * Get all newer versions.
     * @param LicenceParameter $params
     * @return array
     * array (
     *        ['success']=>boolean,
     *        ['data'] =>array(
     *            [0]=>array(
     *                ['version']=>"string",
     *                ['release']=>"string",
     *                ['url']=>"string",
     *                ['features']=>array(
     *                                        [0]=>"string",
     *                                        [1]=>"string",
     *                                      ...)
     *             ),
     *            ....
     *        )
     *    )
     */
    public function getNewVersions(LicenceParameter $params)
    {
        $params->action = 'getNewVersions';
        return $this->request($params);
    }

    /**
     * Call function be long to format_reqest
     * @param LicenceParameter $params
     * @return array
     *  ['success'] => boolean
     *  ['data'] => Array (
     *              .......
     *            )
     *
     */
    protected function request(LicenceParameter $params)
    {
        if (empty($this->api_key) || empty($this->module)) {
            return array();
        }
        $result = array();
        $module_key = implode('', $this->getKey(get_class($this->module)));
        $action_key = implode('_', $this->getKey($params->action));
        $configuration_key_action = $module_key . '_' . $action_key;

        $current_time = strtotime('now');
        $next_time = $this->getCacheTime($configuration_key_action);

        if ($this->checkExistKey($configuration_key_action) && $current_time <= $next_time) {
            $result = $this->getCache($configuration_key_action);
        } else {
            $result = $this->getValueRequest($params);
            $data = Tools::jsonDecode($result, true);
            if ($data['success']) {
                $this->setCache($configuration_key_action, $data);
            }
        }
        return Tools::jsonDecode($result, true);
    }

    /**
     * Get key configuration of module.
     * @param string $string
     * @return array
     * array (
     *      0 => string 'G'
     *      1 => string 'N'
     *      2 => string 'V'
     *      )
     */
    protected function getKey($string)
    {
        $key = array();
        $resutl = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($resutl as $value) {
            $key[] = Tools::strtoupper(mb_substr($value, 0, 1, 'utf-8'));
        }
        return $key;
    }

    /**
     * Get time exist cache
     * @param string $key_configuration
     * @return float
     */
    protected function getCacheTime($key_configuration)
    {
        $next_time = 0;
        if ($this->checkExistKey($key_configuration)) {
            $data = Tools::jsonDecode(Configuration::get($key_configuration), true);
            if (is_array($data) && !empty($data)) {
                $next_time = $data['time'];
            }
        }
        return $next_time;
    }

    /**
     * Check key exist in configuration.
     * @param type $key_configuration
     * @return boolean
     */
    protected function checkExistKey($key_configuration)
    {
        return Configuration::hasKey($key_configuration);
    }

    /**
     * Set data from configuration.
     * @param string $key_configuration
     * @return string format json
     */
    protected function getCache($key_configuration)
    {
        return Configuration::get($key_configuration);
    }

    /**
     * Get value after send information.
     * @param LicenceParameter $params
     * @return string format json
     */
    protected function getValueRequest(LicenceParameter $params)
    {
        $result = array();
        if (function_exists('curl_version') && ini_get('allow_url_fopen')) {
            $result = $this->requestCurl($params);
        } elseif (Tools::file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
            $result = $this->requestFileGetContent($params);
        }
        return (!empty($result)) ? $result : Tools::jsonEncode($result);
    }

    /**
     * Use curl push and get information from other server.
     * @param LicenceParameter $params
     * @return string format json
     */
    protected function requestCurl(LicenceParameter $params)
    {
        $params->api_key = $this->api_key;
        $array_params = $params->toArray();
        $str_params = http_build_query($array_params);
        $url = $this->url . '?' . $str_params;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('AUTH-KEY: ' . $this->api_key));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * Use file_get_contents push and get information from other server.
     * @param LicenceParameter $params
     * @return string format json
     */
    protected function requestFileGetContent(LicenceParameter $params)
    {
        $params->api_key = $this->api_key;
        $array_params = $params->toArray();
        $str_params = http_build_query($array_params);
        $url = $this->url . '?' . $str_params;
        $result = Tools::file_get_contents($url);
        return $result;
    }

    /**
     * Set data to configuration
     * @param string $key_configuration
     * @param array $value
     */
    protected function setCache($key_configuration, $value)
    {
        if (is_array($value) && !empty($value)) {
            $value['time'] = strtotime('+1 week');
        }
        Configuration::updateValue($key_configuration, Tools::jsonEncode($value));
    }

    /**
     * Validate licence of current module.
     * @param LicenceParameter $params
     * @return array
     *  array (
     *        ['success']=>boolean,
     *      ['data']=> ''
     * }
     */
    public function validateLicence(LicenceParameter $params)
    {
        $params->action = 'validateLicence';
        return $this->request($params);
    }

    /**
     * Get link client want to download version.
     * @param LicenceParameter $params
     * @return array
     * array
     *        ['success']=>boolean,
     *        ['data']=>array(
     *            ['url']=>"string"
     *        )
     * }
     */
    public function downloadNewVersion(LicenceParameter $params)
    {
        $params->action = 'downloadNewVersion';
        return $this->request($params);
    }

    /**
     * Get all news feature and promotion of module
     * @param LicenceParameter $params
     * @return array
     * array(
     *         ['success']=>boolean,
     *           ['data']=>array
     *           (
     *        ['news']=>array('string','string',...),
     *        ['promotion']=>array('string','string',...)
     *           )
     * )
     */
    public function getNews(LicenceParameter $params)
    {
        $params->action = 'getNews';
        return $this->request($params);
    }
}
