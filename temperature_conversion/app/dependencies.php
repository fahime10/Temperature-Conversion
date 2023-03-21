<?php

// Register component on container

use TempConv\DatabaseWrapper;
use TempConv\SQLQueries;
use TempConv\Validator;
use TempConv\TemperatureConversionModel;

$container['view'] = function ($container) {
  $view = new \Slim\Views\Twig(
    $container['settings']['view']['template_path'],
    $container['settings']['view']['twig'],
    [
      'debug' => true // This line should enable debug mode
    ]
  );

  // Instantiate and add Slim specific extension
  $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

  return $view;
};

$container['validator'] = function () {
  $validator = new Validator();
  return $validator;
};

$container['tempConvModel'] = function () {
  $model = new TemperatureConversionModel();
  return $model;
};

$container['databaseWrapper'] = function () {
    $database_wrapper_handle = new DatabaseWrapper();
    return $database_wrapper_handle;
};

$container['sqlQueries'] = function () {
    $sql_queries = new SQLQueries();
    return $sql_queries;
};
