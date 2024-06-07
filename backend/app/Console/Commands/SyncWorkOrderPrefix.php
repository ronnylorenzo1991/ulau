<?php

namespace App\Console\Commands;

use App\Models\Center;
use App\Models\WorkOrder;
use Illuminate\Console\Command;

class SyncWorkOrderPrefix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:prefix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the work orders prefix by center';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        WorkOrder::withTrashed()->get()->map(function ($workOrder) {
            $prefix = Center::find($workOrder->center_id)->prefix;
            if($prefix) {
                $oldNumber = $workOrder->number;
                if (!str_contains($oldNumber, $prefix . '-')) {
                    $workOrder->update([
                        'number' => $prefix . '-' . $oldNumber
                    ]);
                }
            }
        });

        return Command::SUCCESS;
    }
}
