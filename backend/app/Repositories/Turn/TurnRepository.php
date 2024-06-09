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
        $query = Turn::select(
            'events.id as id',
            'events.image_path',
            'events.date_at',
        );

        $query->with($relationships);

        // Filters
        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('events.created_at', $filters['date']);
        }

        if (!empty($filters['class_label'])) {
            $query->join('anomalies', 'events.id', '=', 'anomalies.event_id');
            $query->where('anomalies.class_label', $filters['class_label']);
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
        $query->whereBetween('turns.created_at', $filters['date']);
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

        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('turns.created_at', $filters['date']);
        } else {
            $query->whereBetween(
                'date_at',
                [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            );
        }

        return $query->get()->toArray();
    }
    public function getTotals($filters)
    {
        $query = $this->entity->select(
            DB::raw('DAYNAME(turns.date_at) AS week_day'),
            DB::raw('WEEKDAY(turns.date_at) as day'),
            DB::raw("SUM(turns.payment) as count"),
        );

        // Filters
        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('turns.created_at', $filters['date']);
        } else {
            $query->whereBetween(
                'date_at',
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            );
        }

        return $query->orderBy('day')
            ->groupBy(DB::raw('turns.date_at'))
            ->get();
    }
}
