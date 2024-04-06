<?php

namespace App\Console\Commands;

use App\Models\WorkOrder;
use Illuminate\Console\Command;

class SyncWorkOrdersJobsStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the work orders statuses with jobs statuses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        WorkOrder::withTrashed()->get()->map(function ($workOrder) {
            if ($workOrder->work_order_status_id === 2) {
                foreach($workOrder->jobs as $job) {
                    $job->update(['job_status_id' => 3]);
                }
            }
        });

        return Command::SUCCESS;
    }
}
