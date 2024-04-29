<?php

namespace App\Repositories\Event;

use App\Models\Anomaly;
use App\Models\Event;
use App\Repositories\Anomaly\AnomalyRepository;
use App\Repositories\Shared\SharedRepositoryEloquent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventRepository extends SharedRepositoryEloquent
{
    private Event $entity;
    private AnomalyRepository $anomalyRepository;
    public function __construct(
        Event $entity,
        AnomalyRepository $anomalyRepository,
    ) {
        parent::__construct($entity);
        $this->entity            = $entity;
        $this->anomalyRepository = $anomalyRepository;
    }

    public function getAll($sortBy, $sortDir, $perPage, $page, $relationships = null, $filters = [])
    {
        $query = Event::select(
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

    public function store($data, $file)
    {
        $params        = json_decode($data, true);
        $anomaliesList = json_decode($params['metadata'], true) ?? [];
        $event = Event::create([
            'date_at' => $params['date_at'],
            'ext_id'  => $params['ext_id'],
        ]);

        foreach ($anomaliesList as $anomalies) {
            foreach ($anomalies as $label => $coordinates) {
                Anomaly::create([
                    'class_label' => $label,
                    'coordinates' => json_encode($coordinates),
                    'event_id'    => $event->id,
                ]);
            }
        }

        $image = $event->addMedia($file)->toMediaCollection('images');
        $event->update(['image_path' => $image->original_url]);
        return $event;
    }

    public function update($id, $data, $file = null)
    {
        $params        = json_decode($data, true);
        $anomaliesList = json_decode($params['metadata'], true) ?? [];

        $event = $this->find($id);
        $event->update([
            'date_at' => $params['date_at'],
            'ext_id'  => $params['ext_id'],
        ]);
        $event->anomalies()->delete();
        foreach ($anomaliesList as $anomalies) {
            foreach ($anomalies as $label => $coordinates) {
                Anomaly::create([
                    'class_label' => $label,
                    'coordinates' => json_encode($coordinates),
                    'event_id'    => $event->id,
                ]);
            }
        }

        $event->clearMediaCollection('images');
        $image = $event->addMedia($file)->toMediaCollection('images');
        $event->update(['image_path' => $image->original_url]);

        return $event;
    }

    public function getEventsLastImages($last, $filters)
    {
        $query = $this->entity->select('events.image_path');

        // Filters
        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('events.created_at', $filters['date']);
        }

        if (!empty($filters['class_label'])) {
            $query->join('anomalies', 'events.id', '=', 'anomalies.event_id');
            $query->where('anomalies.class_label', $filters['class_label']);
        }

        return $query->orderBy('events.id', 'desc')->limit(10)->get()->pluck('image_path')->toArray();
    }

    public function getEventsTotals($filters)
    {
        $query = $this->entity->select(
            DB::raw('DAYNAME(events.date_at) AS week_day'),
            DB::raw('WEEKDAY(events.date_at) as day'),
            DB::raw("COUNT(events.id) as count"),
        );

        // Filters
        if (!empty($filters['date'])) {
            $filters['date'] = explode(',', $filters['date']);
            $query->whereBetween('events.created_at', $filters['date']);
        } else {
            $query->whereBetween(
                'date_at',
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            );
        }

        if (!empty($filters['class_label'])) {
            $query->join('anomalies', 'events.id', '=', 'anomalies.event_id');
            $query->where('anomalies.class_label', $filters['class_label']);
        }


        return $query->orderBy('day')
            ->groupBy(DB::raw('events.date_at'))
            ->get();
    }
}
