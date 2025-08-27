<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use VdubDev\CsvPreviewConvert\Controller\CsvPreviewConvertController;

return function (RoutingConfigurator $routes) {
    $routes->add('_csv_preview_convert_popup', '/_csv_preview_convert_popup')
        ->controller([CsvPreviewConvertController::class, 'popup'])
        ->methods(['GET'])
    ;

    $routes->add('_csv_preview_convert_preview', '/_csv_preview_convert_preview')
        ->controller([CsvPreviewConvertController::class, 'preview'])
        ->methods(['GET'])
    ;
};
