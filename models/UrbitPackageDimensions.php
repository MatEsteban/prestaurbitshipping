<?php
/**
 * Urbit Package Dimensions of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

class UrbitPackageDimensions
{

    public $weight;
    public $volume;
    protected $width;
    protected $height;
    protected $length;
    protected $additional_charges;
    /**
     * 0: add,1: replace
     * @var boolean
     */
    protected $additional_charge_type;
    protected $weight_conversion_factor = 1;

    public function __construct(
        $width = 0,
        $height = 0,
        $length = 0,
        $weight = 0,
        $additional_charges = 0,
        $additional_charge_type = 0
    ) {
        $this->width = (float)$width;
        $this->height = (float)$height;
        $this->length = (float)$length;
        $this->weight = (float)$weight;
        $this->volume = $this->width * $this->height * $this->length;
        $this->additional_charges = $additional_charges;
        $this->additional_charge_type = $additional_charge_type;
        $this->setWeightUnitFactor();
    }

    /**
     * Set rate to convert between weight unit, in order to output "kg" which is required by Urbit Api
     */
    protected function setWeightUnitFactor()
    {
        switch (Configuration::get('PS_WEIGHT_UNIT')) {
            case 'kg':
                $this->weight_conversion_factor = 1;
                break;

            case 'gr':
                $this->weight_conversion_factor = 0.001;
                break;
            default:
                $this->weight_conversion_factor = 1;
        }
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function getWeight()
    {
        return $this->weight * $this->weight_conversion_factor;
    }

    public function getAdditionalCharge()
    {
        return $this->additional_charges;
    }

    public function getExtraType()
    {
        return $this->additional_charge_type;
    }

    public function getWeightConversionFactor()
    {
        return $this->weight_conversion_factor;
    }
}
