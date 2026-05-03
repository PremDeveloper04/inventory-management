<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Worker;
use Carbon\Carbon;

class UpdateTimestamps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:timestamps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting update...');

        $total = Worker::count();

        $start = now()->subYears(3);
        $end = now();
        $totalSeconds = $start->diffInSeconds($end);

        $index = 0;

        $this->output->progressStart($total);

        Worker::withoutEvents(function () use ($total, $start, $totalSeconds, &$index) {

            Worker::chunkById(1000, function ($rows) use ($total, $start, $totalSeconds, &$index) {

                foreach ($rows as $row) {

                    $index++;

                    $offset = ($index / $total) * $totalSeconds;

                    $date = (clone $start)->addSeconds($offset);

                    DB::table('workers')
                        ->where('id', $row->id)
                        ->update([
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);
                }
            });
        });

        $this->output->progressFinish();

        $this->info('Update completed successfully!');
    }
}
