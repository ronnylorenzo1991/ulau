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
        Bill $entity
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
        $query->whereBetween('bills.created_at', $filters['date']);
     
        return $query->get()->toArray();
    }

    public function getTotals($filters)
    {
        $query = $this->entity->select(
            DB::raw('DAYNAME(bills.date_at) AS week_day'),
            DB::raw('WEEKDAY(bills.date_at) as day'),
            DB::raw("SUM(bills.payment) as count"),
        );

        // Filters
        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('bills.created_at', $filters['date']);
        } else {
            $query->whereBetween(
                'date_at',
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            );
        }

        return $query->orderBy('day')
            ->groupBy(DB::raw('bills.date_at'))
            ->get();
    }

    public function getTotalExpenses($filters)
    {
        $query = $this->entity->select(
            DB::raw("SUM(bills.payment) as total"),
        );

        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('bills.created_at', $filters['date']);
        } else {
            $query->whereBetween(
                'date_at',
                [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            );
        }

        return $query->get()->toArray();
    }
}
