<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class Article
{
    public $identifier;
    public $quantity;
    public $description;

    public function __construct($identifier = null, $quantity = null, $description = null)
    {
        $this->identifier = $identifier;
        $this->quantity = $quantity;
        $this->description = $description;
    }
}
