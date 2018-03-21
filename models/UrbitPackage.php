<?php
/**
 * Urbit Package Dimensions of Urbit module
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

class UrbitPackage
{

    const DOMESTIC = 'domestic';
    const INTERNATIONAL = 'international';
    /**
     * Cubic of package (V = l*w*h)
     * @var float
     */
    protected $volume;
    /**
     * Weight of package
     * @var float
     */
    protected $weight;
    /**
     * Thickness of the cover of package. It looks like the concept "margin" in CSS.
     * @var int
     */
    protected $package_margin;

    /**
     * Flexible package allows to reduce the package size to the miminum if possible, instead of using the fixed size
     * It's helpful for users. Smaller package means cheaper cost.
     * @var boolean
     */
    protected $flexible_package;

    /**
     * Construct
     * @param int $package_margin Thickness of the cover of package. It looks like the concept "margin" in CSS.
     * @param boolean $flexible_package Flexible package allows to reduce the package size to the miminum if possible, instead of using the fixed size
     */
    public function __construct($package_margin = null, $flexible_package = null)
    {
        $this->package_margin = (int)$package_margin;
        $this->flexible_package = (boolean)$flexible_package;
    }

    /**
     * function get package depend on default dimension
     * @param Cart $cart
     * @return array
     * Array
     *   (
     *       [0] => Array
     *           (
     *               [additional_charge_type] => ...
     *               [weight] => ...
     *               [additional_charges] => ..
     *           )
     *   )
     */
    public static function getPackage(Cart $cart, $service_area, $id_carrier)
    {
        // declare aus_package with array dimension and weight depend on service area (international or domestic)
        $aus_package = self::getAuPackage($service_area);
        // applying rule(s) to a cart - product
        $urbit_rate_config = new UrbitRateConfig();
        $products = $urbit_rate_config->applyShippingRules(
            $cart,
            $id_carrier
        );
        // get boxes of current cart;
        //cart's products are splited into boxes, following box rule (width, height, length, weight)
        $urbit_package = new UrbitPackage(
            Configuration::get('URBIT_PACKAGE_MARGIN'),
            Configuration::get('URBIT_FLEXIBLE_PACKAGE')
        );
        $temp_package_dimensions = new UrbitPackageDimensions(); // in order to get weight conversion factor
        $package_dimensions = new UrbitPackageDimensions(
            Configuration::get('URBIT_FWIDTH'),
            Configuration::get('URBIT_FHEIGHT'),
            Configuration::get('URBIT_FLENGTH'),
            $aus_package['max']['weight'] / $temp_package_dimensions->getWeightConversionFactor()
            // fixed value from urbit api
        );
        $default_dimensions = new UrbitPackageDimensions(
            Configuration::get('URBIT_DEFAULT_PRODUCT_WIDTH'),
            Configuration::get('URBIT_DEFAULT_PRODUCT_LENGTH'),
            Configuration::get('URBIT_DEFAULT_PRODUCT_HEIGHT'),
            Configuration::get('URBIT_DEFAULT_PRODUCT_WEIGHT')
        );
        //p($default_dimensions);exit;

        return $urbit_package->packing(
            $products,
            $package_dimensions,
            $default_dimensions
        );
    }

    /**
     * function get weight and dimension urbit Package by name service (international, domestic)
     * @param string $name_service
     * @return array(
     *      'max' => array(
     *               'weight' => weight,
     *               'height' => height,
     *               'width' => width,
     *               'length' => length
     *           )
     * )
     */
    public static function getAuPackage($name_service)
    {
        if ($name_service === UrbitPackage::DOMESTIC) {
            $result_package = array(
                'max' => array(
                    'weight' => 22,
                    'height' => null,
                    'width' => null,
                    'length' => null
                )
            );
        } else {
            $result_package = array(
                'max' => array(
                    'weight' => 20,
                    'height' => null,
                    'width' => null,
                    'length' => null
                )
            );
        }
        return $result_package;
    }

    public function packing(
        $products,
        UrbitPackageDimensions $box_dimensions,
        UrbitPackageDimensions $default_dimensions
    ) {
        if (empty($products) || !is_array($products)) {
            return array();
        }
        $packages = array();
        $volume = 0;
        $acc_weight = 0;
        $package = 0;
        $additional_charges = 0;
        $package_replace = array(
            'additional_charge_type' => 0,
            'weight' => 0,
            'additional_charges' => 0
        );
        foreach ($products as $product) {
            $item_dimensions = $this->getItemDimensions($product, $default_dimensions);
            if ($item_dimensions->volume <= 0 || $item_dimensions->weight <= 0) {
                continue;
            }
            if (isset($product['rule']['type']) && (int)$product['rule']['type'] == 1) {
                $package_replace['additional_charge_type'] = 1;
                $package_replace['weight'] += $item_dimensions->getWeight();
                $package_replace['additional_charges'] += $item_dimensions->getAdditionalCharge();
            } else {
                if (!$this->isFit($item_dimensions, $box_dimensions)) {
                    return false;
                }
                // if a product is downloadable
                if (!empty($product['is_virtual']) && $product['is_virtual']) {
                    continue;
                }
                // if quantity of cart is not a positive number
                if (empty($product['cart_quantity']) || $product['cart_quantity'] <= 0) {
                    continue;
                }
                for ($i = 0; $i < $product['cart_quantity']; $i++) {
                    // This is a hidden key
                    // With this setting, we only count 1 item even customers
                    //add more than 1 quantity of the same of different item
                    // To enable: INSERT INTO ps_configuration (`name`, `value`)
                    //VALUES ('URBIT_COUNT_AS_SINGLE_ITEM', 1);
                    if ((bool)Configuration::get('URBIT_COUNT_AS_SINGLE_ITEM')) {
                        if ($i >= 1) {
                            continue;
                        }
                    }
                    $item_volume = $this->calculateVolume($item_dimensions);
                    if ($volume + $item_volume <= $box_dimensions->getVolume() &&
                      ($acc_weight + $item_dimensions->getWeight()) <= $box_dimensions->getWeight()) {
                        $volume += $item_volume;
                        $acc_weight += $item_dimensions->getWeight();
                        $additional_charges += $item_dimensions->getAdditionalCharge();
                    } else {
                        $packages[$package] = array(
                            'additional_charge_type' => 0,
                            'additional_charges' => $additional_charges,
                            'weight' => $acc_weight,
                            'volume' => $volume
                        ); // fill up and close the current package
                        $package++; // initialize a new package
                        $acc_weight = $item_dimensions->getWeight(); // initialize a new package
                        $additional_charges = $item_dimensions->getAdditionalCharge();
                        $volume = $item_volume; // initialize a new package
                    }
                }
            }
            if ((bool)Configuration::get('URBIT_COUNT_AS_SINGLE_ITEM')) {
                break;
            }
        }

        $packages[$package] = array(
            'additional_charge_type' => 0,
            'additional_charges' => $additional_charges,
            'weight' => $acc_weight,
            'volume' => $volume
        ); // prepare for the last package
        if (!empty($package_replace['additional_charges'])) {
            $packages[] = $package_replace;
        }
        return $this->applyRealPackageDimensions($packages, $box_dimensions);
    }

    /**
     * get dimension of a product (including: width, height, lenght, weigth, additional_charges
     * @param unknown_type $product
     * @return UrbitPackageDimensions
     */
    protected function getItemDimensions($product, UrbitPackageDimensions $default_dimensions)
    {
        $temp_package_dimensions = new UrbitPackageDimensions(); // in order to get weight conversion factor
        $width = (float)$product['width'];
        $height = (float)$product['height'];
        $length = (float)$product['depth'];
        $weight = (float)$product['weight'];

        $width = !empty($width) && $width > 0.0 ?
          $width : $default_dimensions->getWidth();
        $height = !empty($height) && $height > 0.0 ?
          $height : $default_dimensions->getHeight();
        $length = !empty($length) && $length > 0.0 ? $length : $default_dimensions->getLength();
        $weight = !empty($weight) && $weight > 0.0 ?
          $weight : $default_dimensions->getWeight() / $temp_package_dimensions->getWeightConversionFactor();
        $additional_charges = !empty($product['rule']['additional_charges']) ?
          $product['rule']['additional_charges'] : 0;
        // if product is choose Add or nothing choose -> $additional_charge_type=0;
        //else => $additional_charge_type=1;//replace
        $additional_charge_type = !empty($product['rule']['type']) ?
          $product['rule']['type'] : 0;

        return new UrbitPackageDimensions(
            $width,
            $height,
            $length,
            $weight,
            $additional_charges,
            $additional_charge_type
        );
    }

    /**
     * Check if every product can fit into custom box
     */
    public function isFit(UrbitPackageDimensions $item, UrbitPackageDimensions $box)
    {
        $item_dimensions = array($item->getWidth(), $item->getHeight(), $item->getLength());
        $box_dimensions = array($box->getWidth(), $box->getHeight(), $box->getLength());
        sort($item_dimensions);
        sort($box_dimensions);
        foreach ($item_dimensions as $index => $dimension) {
            if ($dimension > $box_dimensions[$index]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Calculate volume of package
     * @param float $width
     * @param float $height
     * @param float $length
     * @return float
     */
    public function calculateVolume(UrbitPackageDimensions $item)
    {
        return $item->getWidth() * $item->getHeight() * $item->getLength();
    }

    /**
     * Use real size of package instead of the fixed size of package (box)
     * @param array $packages
     * @param UrbitPackageDimensions $box_dimensions
     */
    protected function applyRealPackageDimensions(array $packages, UrbitPackageDimensions $box_dimensions)
    {
        foreach ($packages as $index => $package) {
            $package['length'] = $box_dimensions->getLength();
            $package['width'] = $box_dimensions->getWidth();
            $package['height'] = $box_dimensions->getHeight();

            if ($package['additional_charge_type'] != 1) {
                if ($this->flexible_package) {
                    if ($package['volume'] <= $box_dimensions->getVolume()) {
                        $dimension_ratio = pow($box_dimensions->getVolume() / $package['volume'], 1 / 3); // cubic root
                        $package['length'] = $box_dimensions->getLength() / $dimension_ratio;
                        $package['width'] = $box_dimensions->getWidth() / $dimension_ratio;
                        $package['height'] = $box_dimensions->getHeight() / $dimension_ratio;
                    }
                }
                // apply margin or not
                if (!empty($this->package_margin)) {
                    $margin_ratio = (100 + $this->package_margin) / 100;
                    $package['length'] *= $margin_ratio;
                    $package['width'] *= $margin_ratio;
                    $package['height'] *= $margin_ratio;
                }
                $packages[$index] = $package;
            }
        }
        return $packages;
    }
}
