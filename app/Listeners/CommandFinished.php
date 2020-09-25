<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished as Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CommandFinished
{
    /**
     * @var array
     */
    public $report = [
        'schedule:run',
    ];

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Console\Events\CommandFinished  $event
     * @return void
     */
    public function handle(Event $event)
    {
        if (empty($event->command) || ! $this->shouldReport($event)) {
            return false;
        }

        Log::info(class_basename($this) . ' - ' . $event->command, $this->summary($event));
    }

    /**
     * @param \Illuminate\Console\Events\CommandFinished  $event
     *
     * @return bool
     */
    public function shouldReport(Event $event)
    {
        foreach ($this->report as $report) {
            if (strpos($event->command, $report) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Illuminate\Console\Events\CommandFinished  $event
     *
     * @return array
     */
    public function summary(Event $event)
    {
        $info = [
            'command'     => $event->command,
            'exitCode'    => $event->exitCode,
            'runtime'     => round(microtime(true) - LARAVEL_START, 2),
            'memory'      => round(memory_get_usage() / 1024 / 1024, 2),
            'memory_peak' => round(memory_get_peak_usage() / 1024 / 1024, 2),
        ];
        $input = array_filter([
            'arguments' =>  Arr::except($event->input->getArguments(), [
                'command',
            ]),
            'options'   => Arr::except($event->input->getOptions(), [
                'raw',
                'format',
                'help',
                'quiet',
                'verbose',
                'version',
                'ansi',
                'no-ansi',
                'no-interaction',
                'env',
            ]),
        ]);

        if (!empty($input)) {
            $info['input'] = $input;
        }

        return $info;
    }
}
