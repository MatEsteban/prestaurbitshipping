<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

/**
 * Creates a response object.
 *
 * @param response send from Urbit API.
 * @return a custom php std object with attributes error status,
 */
class ResponseObj
{

    public function createObject($args)
    {

        if (is_array($args)) {
            $data = new stdClass;
            $data->error = "0";
            $data->data = $args;
            return $data;
        } else {
            if (property_exists($args, "exception") && $args->status != "200") {
                $data = new stdClass;
                $data->error = "1";
                $data->error_message = $args->invalid_properties[0]->message;
                $data->error_code = $args->invalid_properties[0]->code;
                return $data;
            } else {
                if (property_exists($args, "status") && $args->status != "200") {
                    $data = new stdClass;
                    $data->error = "1";
                    $data->error_message = $args->message;
                    $data->error_code = $args->code;
                    return $data;
                } else {
                    if (property_exists($args, "message") && $args->message == "An error has occurred.") {
                        $data = new stdClass;
                        $data->error = "1";
                        $data->error_message = $args->message;
                        $data->error_code = $args->code;

                        return $data;
                    } else {
                        $args->error = "0";
                        return $args;
                    }
                }
            }
        }
    }
}
