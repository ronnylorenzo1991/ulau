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

    public function store($data, $file)
    {
        $params        = json_decode($data, true);
        $anomaliesList = json_decode($params['metadata'], true);

        $event = Event::create([
            'date_at' => $params['date_at'],
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

    public function getEventsLastImages($last) {
        return $this->entity->select('image_path')->orderBy('id', 'desc')->limit(10)->get()->pluck('image_path')->toArray();
    }

    public function getEventsTotals($getBy)
    {
        $query = $this->entity->select(
            DB::raw('DAYNAME(events.date_at) AS week_day'),
            DB::raw('WEEKDAY(events.date_at) as day'),
            DB::raw("COUNT(events.id) as count"),
        )->whereBetween(
                'date_at',
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            );

        return $query->orderBy('day')
            ->groupBy(DB::raw('events.date_at'))
            ->get();
    }
}
