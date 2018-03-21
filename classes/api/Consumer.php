<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class Consumer
{
    public $address;
    public $first_name;
    public $last_name;
    public $email;
    public $cell_phone;
    public $consumer_comment;

    public function __construct(
        $first_name = null,
        $last_name = null,
        $email = null,
        $cell_phone = null,
        $consumer_comment = null
    ) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->cell_phone = $cell_phone;
        $this->consumer_comment = $consumer_comment;
    }
}
