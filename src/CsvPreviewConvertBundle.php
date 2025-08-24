<?php

namespace VdubDev\CsvPreviewConvert;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CsvPreviewConvertBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
