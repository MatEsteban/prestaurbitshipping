<?php
/**
 * Licence of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class HsModuleInfo
{

    /**
     * Name of module
     * @var string
     */
    protected $module_name;

    /**
     * Datetime of install module
     * @var int
     */
    protected $install_date;

    /**
     * Datetime of last update
     * @var int
     */
    protected $last_update;

    /**
     * Current version of module.
     * @var string
     */
    protected $version;

    /**
     * Name of key configuration
     * @var string
     */
    protected $key_configuration;

    /**
     * an instance of module
     * @var Module
     */
    protected $module;

    /**
     * Name domain install module
     * @var string
     */
    protected $domain;

    /**
     * Construct
     * @param Module $module an instance of module
     */
    public function __construct(Module $module)
    {
        if (Validate::isLoadedObject($module)) {
            $this->module = $module;
            $this->key_configuration = $this->getKey(get_class($module));
            $array_values = Tools::jsonDecode(Configuration::get($this->key_configuration), true);
            if (!empty($array_values)) {
                $this->populate($array_values);
            }
        }
    }

    /**
     * Convert class module name to key configuration
     * @param string $class_module_name
     * @return string
     */
    protected function getKey($class_module_name)
    {
        $key_configuration = array();
        $array_key = preg_split('/(?=[A-Z])/', $class_module_name, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($array_key as $value) {
            $key_configuration[] = Tools::strtoupper(mb_substr($value, 0, 1, 'utf-8'));
        }
        return implode('', $key_configuration);
    }

    /**
     * Assign values to object
     * @param array $array_values
     */
    protected function populate($array_values)
    {
        if (!empty($array_values)) {
            foreach ($array_values as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get value from key class
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        $result = '';
        // check isset key, set value for key
        if (property_exists($this, $key)) {
            $result = $this->$key;
        }
        return $result;
    }

    /**
     * Set value for object
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        // check isset key, set value for key
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

    /**
     * Delete value config
     * @return boolean
     */
    public function delete()
    {
        $flag = false;
        if (!empty($this->key_configuration)) {
            $flag = Configuration::deleteByName($this->key_configuration);
        }
        return $flag;
    }

    /**
     * Update value to configuration
     * @return boolean
     */
    public function update()
    {
        $this->setDefaultValues();
        $array_values = $this->toArray();
        unset($array_values['module']);
        unset($array_values['key_configuration']);
        return Configuration::updateValue($this->key_configuration, Tools::jsonEncode($array_values));
    }

    /**
     * Assign default value if attribute of object empty
     */
    protected function setDefaultValues()
    {
        $shop = Context::getContext()->shop;
        if (Validate::isLoadedObject($this->module)) {
            $this->module_name = $this->module_name ? $this->module_name : $this->module->name;
            $this->version = $this->version ? $this->version : $this->module->version;
            $this->domain = $this->domain ? $this->domain : $shop->domain . $shop->physical_uri;
            $this->install_date = $this->install_date ? $this->install_date : strtotime('now');
            $this->last_update = $this->last_update ? $this->last_update : strtotime('now');
        }
    }

    /**
     * Convert HsModuleInfo to array
     * @return array
     * array(
     *       'module_name' => string
     *       'version' => string
     *         'domain' => string
     *       'install_date' => string (DD-MM-YYYY)
     *       'last_update' => string (DD-MM-YYYY)
     *       'key_configuration' => string
     *       'object' => object
     *       )
     */
    protected function toArray()
    {
        $result = array();
        foreach ($this as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }
}
