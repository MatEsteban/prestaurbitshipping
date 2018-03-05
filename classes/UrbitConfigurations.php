<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class UrbitConfigurations
{

    public static function getErrorMessage($error_code)
    {
        $message = "error";

        switch ($error_code) {
            case "404":
                $message = "The zip code not available.";
                break;
            case "RET-005":
                $message = "The expected delivery date and time not available.";
                break;
            case "222":
                $message = "ccc";
                break;
        }

        return $message;
    }
}
