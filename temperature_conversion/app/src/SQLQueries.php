<?php

namespace TempConv;

class SQLQueries {

    public function __construct() {}

    public function __destruct() {}

    public static function getStoreDetailsQuery() {
       $query_string = "INSERT INTO temperature ";
       $query_string .= "SET calculation_type = :calculation_type, ";
       $query_string .= "temperature_input = :temperature_input, ";
       $query_string .= "windspeed = :windspeed, ";
       $query_string .= "temperature_result = :temperature_result";
       return $query_string;
    }

//    public static function displayDetails() {
//        $array_result = [];
//        $query_string = "SELECT * FROM temperature";
//
//        return $query_string;
//    }

    public static function getCalculationType() {
        $query_string = "SELECT calculation_type ";
        $query_string .= "FROM temperature";
        return $query_string;
    }

    public static function getTemperatureInput() {
        $query_string = "SELECT temperature_input ";
        $query_string .= "FROM temperature";
        return $query_string;
    }

    public static function getWindspeed() {
        $query_string = " SELECT windspeed ";
        $query_string .= "FROM temperature";
        return $query_string;
    }

    public static function getTemperatureResult() {
        $query_string = "SELECT temperature_result ";
        $query_string .= "FROM temperature";
        return $query_string;
    }
}