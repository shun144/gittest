<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomizeFormatter
{
    public function __invoke($logger)
    {
        $format = "%datetime% [%level_name%] - %message%-%extra.class%(L.%extra.line%)"  . PHP_EOL;
        $dateFormat = "Y-m-d H:i:s.v";
        $lineFormatter = new LineFormatter($format, $dateFormat, true, true);

        $ip = new IntrospectionProcessor(Logger::DEBUG, ['Illuminate\\']);
        $wp = new WebProcessor();

        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor($ip);
            $handler->pushProcessor($wp);
            $handler->setFormatter($lineFormatter);
        }
    }
}