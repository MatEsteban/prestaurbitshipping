<?php
/**
 * Urbit extra cover of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

class UrbitExtraCover
{
    /* @var int() min */

    protected static $min = 100;
    /* @var int() max */
    protected static $max = 5000;
    /* @var string() option_code */
    protected static $option_code = 'AUS_SERVICE_OPTION_EXTRA_COVER';
    protected static $separator = '::';
    /**
     * extra corver of international services
     * @var string
     */
    protected static $option_code_international = 'INTL_SERVICE_OPTION_EXTRA_COVER';
    /* @var string() extra_cover */
    protected $extra_cover;

    /**
     * Set extra cover based on id_carrier
     * @param int $id_carrier
     * @param int $extra_cover
     * @return boolean
     */
    public static function setExtraCover($id_carrier, $extra_cover)
    {
        $extra_covers = self::getAllExtraCovers();
        $extra_covers[$id_carrier] = $extra_cover;
        Context::getContext()->cookie->extra_cover = self::encode($extra_covers);
        return true;
    }

    /**
     * function get all extra covers in cookie
     * @return string extra cover haver form id_carrier1::extra_cover1,id_carrier2::extra_cover2
     */
    protected static function getAllExtraCovers()
    {
        $context = Context::getContext();
        $extra_cover_cookie = !empty($context->cookie->extra_cover) ? $context->cookie->extra_cover : '';
        return self::decode($extra_cover_cookie);
    }

    /**
     * Convert a string into an array. String should be in format of "key::value,key::value"
     * @param string $input
     * @return array decoded array
     * array(
     * key => value,
     * key => value
     * ...
     * )
     */
    protected static function decode($input)
    {
        $tmp = explode(',', $input);
        $output = array();
        foreach ($tmp as $record) {
            $key_value = explode(self::$separator, $record);
            if (!empty($key_value[0]) && !empty($key_value[1])) {
                $output[$key_value[0]] = $key_value[1];
            }
        }
        return $output;
    }

    /**
     * Convert an array into a form of "key::value,key::value"
     * @param array $input
     * array(
     * key => value,
     * key => value
     * ...
     * )
     * return string in format of "key::value,key::value"
     */
    protected static function encode(array $input)
    {
        $output = '';
        if (!empty($input)) {
            $tmp = array();
            foreach ($input as $key => $value) {
                $tmp[] = ($key . self::$separator . $value);
            }
            $output = implode(',', $tmp);
        }
        return $output;
    }

    /**
     * get extra cover by id_carrier
     * @return int extra cover
     */
    public static function getExtraCover($id_carrier)
    {
        $extra_covers = self::getAllExtraCovers();
        return !empty($extra_covers[$id_carrier]) ? $extra_covers[$id_carrier] : self::getDefaultValue();
    }

    /**
     * Get a default value of extra cover
     * @return int default value of extra cover
     */
    protected static function getDefaultValue()
    {
        $context = Context::getContext();
        $total_order = self::$min;
        if (Validate::isLoadedObject($context->cart)) {
            $total_order = round($context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING));
        }

        if ($total_order <= self::getMinValue()) {
            $total_order = self::getMinValue();
        } elseif ($total_order >= self::getMaxValue()) {
            $total_order = self::getMaxValue();
        }
        return $total_order;
    }

    /**
     * Get minimum value of an extra cover
     * @return int miniumum value of extra cover
     */
    public static function getMinValue()
    {
        return self::$min;
    }

    /**
     * Get maximum value of an extra cover
     * @return int maximum value of extra cover
     */
    public static function getMaxValue()
    {
        return self::$max;
    }

    /**
     * Check if a Urbittralia Post service contains "suboption" extra cover
     * @param array $service_code
     * @return boolean
     */
    public static function isExtraCoverService(array $urbit_service_code)
    {
        if (Tools::strtolower(Tools::substr($urbit_service_code[0], 0, 4)) == 'intl') {
            $is_extra_cover = !empty($urbit_service_code[1]) &&
              $urbit_service_code[1] == self::$option_code_international;
        } else {
            $is_extra_cover = !empty($urbit_service_code[2]) && $urbit_service_code[2] == self::$option_code;
        }
        return $is_extra_cover;
    }

    /**
     * Validate a value of extra cover, to make sure it's in a valid range
     * @param string $extra_cover
     * @return type
     */
    public static function isValidated($extra_cover)
    {
        return ($extra_cover >= self::getMinValue() || $extra_cover <= self::getMaxValue());
    }
}
