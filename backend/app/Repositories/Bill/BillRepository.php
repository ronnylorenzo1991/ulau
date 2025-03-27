<?php

namespace App\Repositories\Bill;

use App\Models\Bill;
use App\Repositories\Shared\SharedRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillRepository extends SharedRepositoryEloquent
{
    private Bill $entity;
    public function __construct(
        Bill $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function getBillsList($filters)
    {
        $query = Bill::select(
            'bills.id as bill_id',
            'bills.date_at',
            'bills.payment',
            'bills.description',
            'bills.product_name',
            DB::raw('CONCAT(bills.date_at) as date'),
            DB::raw('CONCAT(bills.product_name, " ", ", $", bills.payment) as title'),
            DB::raw('CONCAT("#f21862") as borderColor'),
            DB::raw('CONCAT("#f21862") as backgroundColor'),
        );
        $query->whereBetween('bills.date_at', $filters['date']);

        return $query->get()->toArray();
    }

    public function getTotals($filters)
    {
        if (!empty($filters['date_range'])) {
            $dateRange = explode(',', $filters['date_range']);
            $start_at  = !empty($dateRange[0]) ? Carbon::parse($dateRange[0]) : Carbon::now();
            $end_at    = !empty($dateRange[1]) ? Carbon::parse($dateRange[1]) : Carbon::now();
        } else {
            $start_at = Carbon::now()->startOfWeek();
            $end_at   = Carbon::now()->endOfWeek();
        }

        $dateQuery = $start_at->isSameDay($end_at) ? "HOUR(bills.created_at) as date" : "DATE(bills.date_at) as date";

        $query = $this->entity->select(DB::raw($dateQuery), DB::raw('SUM(bills.payment) as count'));

        $query->whereBetween('bills.date_at', [$start_at, $end_at]);

        return $query->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }


    public function getTotalExpenses($filters)
    {
        $query = $this->entity->select(
            DB::raw("SUM(bills.payment) as total"),
        );

        if (!empty($filters['date_range'])) {
            $query->whereBetween('bills.date_at', $filters['date_range']);
        } else {
            $query->whereBetween(
                'date_at',
                [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            );
        }

        return $query->get()->toArray();
    }
}
