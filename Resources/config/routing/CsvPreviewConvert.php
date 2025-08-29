<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use VdubDev\CsvPreviewConvert\Controller\CsvPreviewConvertController;

return function (RoutingConfigurator $routes) {
    $routes->add('csv_preview_popup', '/csv-preview-popup')
        ->controller([CsvPreviewConvertController::class, 'popup'])
        ->methods(['GET'])
    ;

    $routes->add('csv_preview_content', '/csv-preview-content')
        ->controller([CsvPreviewConvertController::class, 'content'])
        ->methods(['GET'])
    ;

    $routes->add('csv_preview_submit', '/csv-preview-submit')
        ->controller([CsvPreviewConvertController::class, 'submit'])
        ->methods(['POST'])
    ;

    $routes->add('csv_preview_convert_and_download', '/csv-preview-convert-download')
        ->controller([CsvPreviewConvertController::class, 'download'])
        ->methods(['POST'])
    ;
};
