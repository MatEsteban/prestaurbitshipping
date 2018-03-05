<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class Config
{
    public function getConfig()
    {
        $mode = Configuration::get('URBIT_ENABLE_TEST_MOD');
        if (isset($mode)) {
            $data_array = array(
                'store_key' => Configuration::get('URBIT_API_TEST_CUSTOMER_KEY'),
                'bearer_jwt_token' => Configuration::get('URBIT_API_TEST_BEARER_JWT_TOKEN'),
                'base_path' => Configuration::get('URBIT_API_TEST_URL'),
                'base_path_test' => Configuration::get('URBIT_API_TEST_URL'),
                'base_path_dev' => Configuration::get('URBIT_API_TEST_URL'),
                'connecttimeout' => '15',
                'timeout' => '15',
            );
        } else {
            $data_array = array(
                'store_key' => Configuration::get('URBIT_API_CUSTOMER_KEY'),
                'bearer_jwt_token' => Configuration::get('URBIT_API_BEARER_JWT_TOKEN'),
                'base_path' => Configuration::get('URBIT_API_URL'),
                'base_path_test' => Configuration::get('URBIT_API_URL'),
                'base_path_dev' => Configuration::get('URBIT_API_URL'),
                'connecttimeout' => '15',
                'timeout' => '15',
            );
        }

        return $data_array;
    }
}
