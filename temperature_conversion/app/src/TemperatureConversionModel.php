<?php

namespace TempConv;

class TemperatureConversionModel
{
    private string $temperature;
    private $calculation_type;
    private $windspeed;
    private $result_attribute;
    private $soap_call_parameters;
    private $result;
    private $temp_database_wrapper;
    private $database_wrapper;
    private $sql_queries;
    private $db_connection_settings;

    public function __construct(){}

    public function __destruct(){}


    /**
     * Sets the conversion parameters
     * By default, the app will prefer the temperature, even if you type in both inputs
     *
     * @param $conversion_parameters
     * @return void
     */
    public function setConversionParameters($conversion_parameters)
    {
        $this->calculation_type = $conversion_parameters['cleaned_calculation'];
        $this->temperature = $conversion_parameters['cleaned_temperature'];
        $this->windspeed = $conversion_parameters['cleaned_windspeed'];
    }

    /**
     * A SOAP client is created and then temperature conversion is processed
     * using an auxiliary function convertTemperature($soap_handle, $soap_function)
     *
     * @return void
     */
    public function performTemperatureConversion()
    {
        $result = null;
        $soap_client_handle = null;
        $soap_function = $this->selectCalculation();

        $soap_client_handle = $this->createSoapClient();

        if ($soap_client_handle !== false && $soap_function != 'null')
        {
            $result = $this->convertTemperature($soap_client_handle, $soap_function);
        }

        $this->result = $result;
    }

    /**
     * Each type of calculation has a separate function call with differently named parameters
     *
     * @return string
     */
    private function selectCalculation()
    {
        $soap_function = '';
        $soap_call_parameters = [];
        $result_attribute = '';

        $conversion_required = $this->calculation_type;
        switch($conversion_required)
        {
            case 'ctof':
                $soap_function = 'CelsiusToFahrenheit';
                $soap_call_parameters = [
                    'nCelsius' => $this->temperature
                ];
                $result_attribute = 'CelsiusToFahrenheitResult';
                break;
            case 'ftoc':
                $soap_function = 'FahrenheitToCelsius';
                $soap_call_parameters = [
                    'nFahrenheit' => $this->temperature
                ];
                $result_attribute = 'FahrenheitToCelsiusResult';
                break;
            case 'cchill':
                $soap_function = 'WindChillInCelsius';
                $soap_call_parameters = [
                    'nCelsius' => $this->temperature,
                    'nWindSpeed' => $this->windspeed
                ];
                $result_attribute = 'WindChillInCelsiusResult';
                break;
            case 'fchill':
                $soap_function = 'WindChillInFahrenheit';
                $soap_call_parameters = [
                    'nFahrenheit' => $this->temperature,
                    'nWindSpeed' => $this->windspeed
                    ];
                $result_attribute = 'WindChillInFahrenheitResult';
                break;
        }
        $this->result_attribute = $result_attribute;
        $this->soap_call_parameters = $soap_call_parameters;

        return $soap_function;
    }

    /**
     * Creates a SOAP client
     *
     * @return false|\SoapClient
     */
    private function createSoapClient()
    {
        $soap_client_handle = false;

        $soapclient_attributes = ['trace' => true, 'exceptions' => true];
        $wsdl = WSDL;

        try
        {
            $soap_client_handle = new \SoapClient($wsdl, $soapclient_attributes);
//            var_dump($soap_client_handle->__getFunctions());
//            var_dump($soap_client_handle->__getTypes());
        }
        catch (\SoapFault $exception)
        {
            trigger_error($exception);
        }

        return $soap_client_handle;
    }

    /**
     * Note the use of the variable variable to extract the appropriate returned attribute
     *
     * @param $soap_client_handle
     * @param $soap_function
     * @return bool|null
     */
    private function convertTemperature($soap_client_handle, $soap_function)
    {
        $result = null;

        try
        {
            $conversion_result = $soap_client_handle->__soapCall($soap_function, [$this->soap_call_parameters]);
            $result_attribute = $this->result_attribute;
            $result = $conversion_result->$result_attribute;
//      var_dump($obj_soap_client_handle->__getLastRequest());
//      var_dump($obj_soap_client_handle->__getLastResponse());
//      var_dump($obj_soap_client_handle->__getLastRequestHeaders());
//      var_dump($obj_soap_client_handle->__getLastResponseHeaders());
        }
        catch (\SoapFault $exception)
        {
            trigger_error($exception);
        }

        return $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setDatabaseWrapper($database_wrapper) {
        $this->database_wrapper = $database_wrapper;
    }

    public function setDatabaseConnectionSettings($database_connection_settings) {
        $this->db_connection_settings = $database_connection_settings;
    }

    public function setSqlQueries($sql_queries) {
        $this->sql_queries = $sql_queries;
    }

    public function storeData(array $cleaned_parameters) {
        $store_result = false;

        $sql_query_string = $this->sql_queries->getStoreDetailsQuery();

        $query_parameters = [
            ':calculation_type' => $cleaned_parameters['cleaned_calculation'],
            ':temperature_input' => $cleaned_parameters['cleaned_temperature'],
            ':windspeed' => $cleaned_parameters['cleaned_windspeed'],
            ':temperature_result' => $cleaned_parameters['cleaned_result']
        ];

        $this->database_wrapper->setDatabaseConnectionSettings($this->db_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();

        $this->database_wrapper->safeQuery($sql_query_string, $query_parameters);

        $store_result = true;

        return $store_result;
    }

    public function retrieveStoredValues() {
        $retrieved_values = [];

        $this->temp_database_wrapper->setDatabaseConnectionSettings($this->db_connection_settings);
        $this->temp_database_wrapper->makeDatabaseConnection();

        $retrieved_values['calculation_type'] = $this->temp_database_wrapper->getCalculationType('calculation_type');
        $retrieved_values['windspeed'] = $this->temp_database_wrapper->getWindspeed('windspeed');
        $retrieved_values['temperature_calculate'] = $this->temp_database_wrapper->getTemperatureInput('temperature_calculate');
        $retrieved_values['temperature_result'] = $this->temp_database_wrapper->getTemperatureResult('temperature_result');

        return $retrieved_values;
    }

}