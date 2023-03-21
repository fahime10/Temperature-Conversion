<?php
/**
 * Created by PhpStorm.
 * User: cfi
 * Date: 20/11/15
 * Time: 14:01
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$app->post(
    '/processtemperatureconversion',
    function(Request $request, Response $response) use ($app)
    {
        $logs_file_path = '/p3t/phpappfolder/logs/';
        $logs_file_name = 'temperatures.log';
        $logs_file = $logs_file_path . $logs_file_name;

        $log = new Logger('logger');
        $log->pushHandler(new StreamHandler($logs_file, Logger::INFO));

        $tainted_parameters = $request->getParsedBody();

        $validator = $this->validator;

        $cleaned_parameters = cleanupParameters($validator, $tainted_parameters);
        $calculation_result = performCalculation($app, $cleaned_parameters);
        $cleaned_parameters['cleaned_result'] = $calculation_result;
        $conversion_calculation_text = CONV_CALC[$cleaned_parameters['cleaned_calculation']];

        $store_result = storeResult($app, $cleaned_parameters);

        if($cleaned_parameters['cleaned_temperature'] != null) {
            $log->info('Calculation type: '. $cleaned_parameters['cleaned_calculation'] .
                ', Temperature input: ' . $cleaned_parameters['cleaned_temperature'] .
                ', Temperature result is: ' . $calculation_result);
        } else if($cleaned_parameters['cleaned_windspeed'] != null) {
            $log->info('Calculation type: ' . $cleaned_parameters['cleaned_calculation'] .
            ', Windchill input: ' . $cleaned_parameters['cleaned_windspeed'] .
            ', Windchill result is: ' . $calculation_result);
        }

        return $this->view->render($response,
            'display_result.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE,
                'initial_input_box_value' => null,
                'page_title' => APP_NAME,
                'page_heading_1' => APP_NAME,
                'page_heading_2' => 'Result',

                'temperature' => $cleaned_parameters['cleaned_temperature'],
                'calculation' => $cleaned_parameters['cleaned_calculation'],
                'windspeed' => $cleaned_parameters['cleaned_windspeed'],
                'conversion_type_text' => $conversion_calculation_text,
                'result' => $cleaned_parameters['cleaned_result'],
            ]);
    });

function cleanupParameters($validator, array $tainted_parameters): array
{
    $cleaned_parameters = [];

    $tainted_calculation_type = $tainted_parameters['conversion'];
    $tainted_temperature = $tainted_parameters['temperature'];
    $tainted_windspeed = $tainted_parameters['windspeed'];

    $cleaned_parameters['cleaned_calculation'] = $validator->validateCalculationType($tainted_calculation_type);
    $cleaned_parameters['cleaned_temperature'] = $validator->validateTemperature($tainted_temperature, $cleaned_parameters['cleaned_calculation']);
    $cleaned_parameters['cleaned_windspeed'] = $validator->validateWindspeed($tainted_windspeed);
    return $cleaned_parameters;
}


function performCalculation($app, $cleaned_parameters)
{
    $tempconv_model = $app->getContainer()->get('tempConvModel');

    $calculation_type = $cleaned_parameters['cleaned_calculation'];

    $tempconv_model->setConversionParameters($cleaned_parameters);

    $tempconv_model->performTemperatureConversion();

    $temperature_conversion_result = $tempconv_model->getResult();

    if ($temperature_conversion_result === false)
    {
        $temperature_conversion_result = 'not available';
    }

    return $temperature_conversion_result;
}

function storeResult($app, $cleaned_parameters) {
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('sqlQueries');
    $db_conf = $app->getContainer()->get('settings');
    $temp_model = $app->getContainer()->get('tempConvModel');
    $database_connection_settings = $db_conf['pdo_settings'];

    $store_result = false;
    $temp_model->setDatabaseWrapper($database_wrapper);
    $temp_model->setSqlQueries($sql_queries);
    $temp_model->setDatabaseConnectionSettings($database_connection_settings);
    $temp_model->storeData($cleaned_parameters);
    $store_result = true;
    return $store_result;
}
