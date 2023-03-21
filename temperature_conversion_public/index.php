<?php
/** index.php
 * PHP program to demonstrate the usage of a soap server
 *
 * @package stockquotes
 */

ini_set('xdebug.trace_output_name', 'temperatures');
ini_set('display_errors', 'On');
ini_set('html_errors', 'On');
ini_set('xdebug.trace_format', 1);
//
//if (function_exists(xdebug_start_trace()))
//{
//    xdebug_start_trace();
//}

include 'temperature_conversion/bootstrap.php';

//if (function_exists(xdebug_stop_trace()))
//{
//    xdebug_stop_trace();
//}
