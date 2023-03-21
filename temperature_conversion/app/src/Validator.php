<?php

namespace TempConv;

class Validator
{
    public function __construct() { }

    public function __destruct() { }


    /**
     * Validates the calculation type to be used for a specific conversion type
     *
     * @param $type_to_check
     * @return false|mixed
     */
    public function validateCalculationType($type_to_check)
    {
        $checked_unit_type = false;
        $unit_type = CONV_CALC;
        $result = array_key_exists($type_to_check, $unit_type);

        if ($result === true)
        {
            $checked_unit_type = $type_to_check;
        }

        return $checked_unit_type;
    }

    /**
     * Validates the input for the temperature
     *
     * If no temperature is set, then it should return the minimum temperature
     *
     * @param $temperature_to_check
     * @param $cleaned_calculation_type
     * @return false|mixed
     */
    public function validateTemperature($temperature_to_check, $cleaned_calculation_type)
    {
        $checked_temperature = false;
        $minimum_temperature_value = $this->selectMinimumTemperature($cleaned_calculation_type);

        if (isset($temperature_to_check))
        {
            $sanitised_temperature = filter_var($temperature_to_check, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $validated_temperature = filter_var($sanitised_temperature, FILTER_VALIDATE_FLOAT);
            if ($validated_temperature >= $minimum_temperature_value)
            {
                $checked_temperature = $validated_temperature;
            } else {
                $checked_temperature = 0;
            }
        }
        return $checked_temperature;
    }


    /**
     * Validates the input for the windspeed
     *
     * @param $windspeed_to_check
     * @return false|mixed
     */
    public function validateWindspeed($windspeed_to_check)
    {
        $checked_windspeed = false;

        if (isset($windspeed_to_check))
        {
            $minimum_windspeed_value = 0;
            $sanitised_windspeed = filter_var($windspeed_to_check, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $validated_windspeed = filter_var($sanitised_windspeed, FILTER_VALIDATE_FLOAT);
            if ($validated_windspeed >= $minimum_windspeed_value)
            {
                $checked_windspeed = $validated_windspeed;
            }
        }
        return $checked_windspeed;
    }

    /**
     * Select the correct value for absolute zero
     *
     * @param $cleaned_calculation_type
     * @return int
     */
    private function selectMinimumTemperature($cleaned_calculation_type)
    {
        $absolute_zero = 0;

        switch ($cleaned_calculation_type)
        {
            case 'ctof':
                $absolute_zero = LOWEST_CENTIGRADE_TEMPERATURE;
                break;
            case 'ftoc':
                $absolute_zero = LOWEST_FAHRENHEIT_TEMPERATURE;
                break;
            default:
                $absolute_zero = 0;
        }
        return $absolute_zero;
    }

    public function sanitiseString($string_to_sanitise) {
        $sanitised_string = false;

        if (!empty($string_to_sanitise)) {
            $sanitised_string = filter_var($string_to_sanitise, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        }
        return $sanitised_string;
    }
}