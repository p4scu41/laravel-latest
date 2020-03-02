<?php

namespace App\Exceptions;

use Monolog\Formatter\LineFormatter;

class CustomizeFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param \Illuminate\Log\Logger $logger
     *
     * @return void
     */
    public function __invoke($logger)
    {
        $formatter = new LineFormatter();

        $formatter->includeStacktraces(false);
        $formatter->allowInlineLineBreaks(true); // Should call after includeStacktraces
        $formatter->setJsonPrettyPrint(true);

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
        }
    }
}
