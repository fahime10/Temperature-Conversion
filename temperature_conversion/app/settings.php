<?php

//ini_set('display_errors', 'On');
//ini_set('html_errors', 'On');
//ini_set('xdebug.trace_output_name', 'temp_conversion.%t');
//ini_set('xdebug.trace_format', '1');

const DIRSEP = DIRECTORY_SEPARATOR;
const APP_NAME = 'Temperature Calculations';
const LOWEST_CENTIGRADE_TEMPERATURE = -273.15;
const LOWEST_FAHRENHEIT_TEMPERATURE = -459.67;

$app_url = dirname($_SERVER['SCRIPT_NAME']);
$css_path = $app_url . '/css/standard.css';

define('CSS_PATH', $css_path);
define('LANDING_PAGE', $_SERVER['SCRIPT_NAME']);

$conversion_calculation = [
    'null' => 'Select:',
    'ctof' => 'Centigrade to Fahrenheit',
    'ftoc' => 'Fahrenheit to Centigrade',
    'cchill' => 'Calculate Windchill in Centigrade',
    'fchill' => 'Calculate Windchill in Fahrenheit',
];
define ('CONV_CALC', $conversion_calculation);

$wsdl = 'https://webservices.daehosting.com/services/TemperatureConversions.wso?WSDL';
define ('WSDL', $wsdl);

$settings = [
    "settings" => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'mode' => 'development',
        'debug' => true,
        'class_path' => __DIR__ . '/src/',
        'view' => [
            'template_path' => __DIR__ . '/templates/',
            'twig' => [
                'cache' => false,
                'auto_reload' => true,
            ]
        ],
        'pdo_settings' => [
            'rdbms' => 'mysql',
            'host' => 'localhost',
            'db_name' => 'temperature_db',
            'port' => '3306',
            'user_name' => 'temperature_user',
            'user_password' => 'temperature_user_pass',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true,
            ],
        ],
    ],
];

return $settings;
