<?php

$loader = require "vendor/autoload.php";
$loader->addPsr4('App\\', __DIR__ . '/src');

use App\Factory\RandomEstimatedDeliveryDataFactory;
use App\Utils;

ini_set('max_execution_time', '300');
Utils::initEnvData();
$numberOfLines = filter_input(INPUT_GET, 'lines', FILTER_VALIDATE_INT);
if ($numberOfLines > Utils::MAX_DELIVERY_DATA_GENERATOR_LINES) {
    echo "Please use a value equal or lower than " . Utils::MAX_DELIVERY_DATA_GENERATOR_LINES;
    die();
}

$numberOfZipCodes = filter_input(INPUT_GET, 'zip-codes', FILTER_VALIDATE_INT);

try {
    (new RandomEstimatedDeliveryDataFactory($numberOfLines, $numberOfZipCodes))->generate();
    echo 'DONE!';
} catch (\Exception $ex) {
    http_response_code(500);
}
