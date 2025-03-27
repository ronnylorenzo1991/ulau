<?php

namespace App\Repositories\Turn;

use App\Models\Turn;
use App\Repositories\Shared\SharedRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TurnRepository extends SharedRepositoryEloquent
{
    private Turn $entity;
    public function __construct(
        Turn $entity,
    ) {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function getAll($sortBy, $sortDir, $perPage, $page, $relationships = null, $filters = [])
    {
        $query = Turn::select('*');

        $query->with($relationships);

        // Filters
        if (!empty($filters['date_range'])) {
            $dateRange = explode(',', $filters['date_range']);
            $startDate  = !empty($dateRange[0]) ? Carbon::parse($dateRange[0]) : Carbon::now();
            $endDate    = !empty($dateRange[1]) ? Carbon::parse($dateRange[1]) : Carbon::now();
            $query->whereBetween('turns.created_at', [$startDate, $endDate]);
        }


        return $query->paginate($perPage, ['*'], 'page', $page)->toArray();
    }

    public function getTurnList($filters)
    {
        $query = Turn::select(
            'turns.id as turn_id',
            'turns.date_at',
            'turns.time_at',
            'turns.payment',
            'turns.client_id',
            'turns.status_id',
            'turns.observations',
            'turn_statuses.name',
            DB::raw('CONCAT(turns.date_at) as date'),
            DB::raw('CONCAT(users.name, " ", TIME_FORMAT(turns.time_at, "%H:%S"), ", $", turns.payment) as title'),
            DB::raw('(CASE WHEN turns.status_id = 1 THEN "#ffa6a6"
             WHEN turns.status_id = 2 THEN "#ff309e"
             WHEN turns.status_id = 3 THEN "#ff5ce4"
             ELSE ""
         END) AS backgroundColor'),
            DB::raw('(CASE WHEN turns.status_id = 1 THEN "#ffa6a6"
             WHEN turns.status_id = 2 THEN "#ff309e"
             WHEN turns.status_id = 3 THEN "#ff5ce4"
             ELSE ""
         END) AS borderColor'),
        );
        $query->whereBetween('turns.date_at', $filters['date']);
        $query->join('users', 'users.id', '=', 'turns.client_id');
        $query->join('turn_statuses', 'turn_statuses.id', '=', 'turns.status_id');
        return $query->get()->toArray();
    }

    public function cancel($id)
    {
        $turn = Turn::findOrFail($id);

        return $turn->update([
            'status_id' => 3,
            'payment'   => 0,
        ]);
    }

    public function complete($id, $request)
    {
        $turn = Turn::findOrFail($id);

        return $turn->update([
            'status_id' => 2,
            'payment'   => $request->get('payment'),
        ]);
    }

    public function getTotalProfit($filters)
    {
        $query = $this->entity->select(
            DB::raw("SUM(turns.payment) as total"),
        );
        
        if (!empty($filters['date_range'])) {
            $query->whereBetween('turns.date_at', $filters['date_range']);
        } else {
            $query->whereBetween(
                'date_at',
                [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            );
        }
        if (!empty($filters['turn'])) {
            $query->where('turns.time_at', $filters['turn']);
        }
        if (!empty($filters['clients'])) {
            $query->whereIn('turns.client_id', explode(',', $filters['clients']));
        }

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

        $dateQuery = $start_at->isSameDay($end_at) ? "HOUR(turns.created_at) as date" : "DATE(turns.date_at) as date";

        $query = $this->entity->select(DB::raw($dateQuery), DB::raw('SUM(turns.payment) as count'));

        $query->whereBetween('turns.date_at', [$start_at, $end_at]);

        if (!empty($filters['turn'])) {
            $query->where('turns.time_at', $filters['turn']);
        }
        
        if (!empty($filters['clients'])) {
            $query->whereIn('turns.client_id', explode(',', $filters['clients']));
        }

       return $query->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function findByDate($date)
    {
       return $this->entity->where('date_at', $date)->get();
    }
}
