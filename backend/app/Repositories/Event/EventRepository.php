<?php

namespace App\Repositories\Event;

use App\Models\Anomaly;
use App\Models\Event;
use App\Repositories\Anomaly\AnomalyRepository;
use App\Repositories\Shared\SharedRepositoryEloquent;


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
        $params    = json_decode($data, true);
        $anomaliesList = json_decode($params['metadata'], true);

        $event = Event::create([
            'date_at' => $params['date_at'],
        ]);
       
        foreach ($anomaliesList as $anomalies) {
            foreach($anomalies as $label => $coordinates) {
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
}
