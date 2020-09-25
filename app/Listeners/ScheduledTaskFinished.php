<?php

namespace App\Listeners;

use Illuminate\Console\Events\ScheduledTaskFinished as Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Console\Application;
use Illuminate\Support\Facades\Log;

class ScheduledTaskFinished
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Console\Events\ScheduledTaskFinished  $event
     * @return void
     */
    public function handle(Event $event)
    {
        Log::info(class_basename($this) . ' - ' . $this->getCommand($event), $this->summary($event));
    }

    /**
     * @param \Illuminate\Console\Events\ScheduledTaskFinished  $event
     *
     * @return string
     */
    public function getCommand(Event $event)
    {
        return trim(str_replace(
            [Application::phpBinary(), "'artisan'"],
            ['', ''],
            $event->task->command
        ));
    }

    /**
     * @param \Illuminate\Console\Events\ScheduledTaskFinished  $event
     *
     * @return array
     */
    public function summary(Event $event)
    {
        return [
            'command'     => $this->getCommand($event),
            'exitCode'    => $event->task->exitCode,
            'runtime'     => $event->runtime,
            'memory'      => round(memory_get_usage() / 1024 / 1024, 2),
            'memory_peak' => round(memory_get_peak_usage() / 1024 / 1024, 2),
        ];
    }
}
