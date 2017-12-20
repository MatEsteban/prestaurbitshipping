<?php
/**
 * Urbit validate of Urbit
 *
 * @author    Urbit
 * @copyright Urbit
 * @license Urbit
 */

class UrbitValidate extends Validate
{
    public static function validateZipCode($zip_code, $country_iso)
    {
        $flag = false;
        $country_id = Country::getByIso($country_iso);
        if ($country_id) {
            $country = new Country((int)$country_id);
            if (Validate::isLoadedObject($country)) {
                $flag = $country->checkZipCode($zip_code);
            }
        }
        return $flag;
    }
}
