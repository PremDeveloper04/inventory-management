<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use App\Events\WorkerExportStarted;
use App\Mail\WorkerExportStartedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWorkerExportStartedEmail implements ShouldQueue
{
    public $queue = 'listeners';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WorkerExportStarted $event): void
    {
        Mail::to('admin@test.com')
            ->queue(
                (new WorkerExportStartedMail($event->exportData))
                    ->onQueue('email')
            );
    }
}
