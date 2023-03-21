<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->post(
    '/displaytemperaturedetails',
    function(Request $request, Response $response) use ($app) {

        $display_text = 'Sorry, we encountered a problem while attempting to display the data...';

        $retrieved_values = retrieveStoredValues($app);

        if ($retrieved_values !== false) {
            $storage_text = "The values retrieved were: ";
        }

        $html_output = $this->view->render($response,
        'display_result.html.twig',
        [
            'landing_page' => $_SERVER["SCRIPT_NAME"],
            'css_path' => CSS_PATH,
            'page_heading_2' => 'Temperature results',
            'calculation_type' => $retrieved_values['calculation_type'],
            'temperature_input' => $retrieved_values['temperature_input'],
            ''
        ]);
        return $html_output;
    }
);

function retrieveStoredValues($app) {
    $retrieved_values = false;
    $db_conf = $app->getContainer()->get('settings');
    $db_connection_settings = $db_conf['pdo_settings'];

    $db_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('sqlQueries');
    $tempConvModel = $app->getContainer()->get('tempConvModel');

    $tempConvModel->setSqlQueries($sql_queries);
    $tempConvModel->setDatabaseConnectionSettings($db_connection_settings);

    $retrieved_values = $tempConvModel->retrieveStoredValues();

    return $retrieved_values;
}