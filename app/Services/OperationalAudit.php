<?php

namespace App\Services;

use App\Models\OperationalEvent;
use Illuminate\Database\Eloquent\Model;

class OperationalAudit
{
    public function record(string $event, ?Model $subject = null, array $properties = []): void
    {
        OperationalEvent::query()->create([
            'user_id' => auth()->id(),
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'event' => $event,
            'properties' => $properties ?: null,
        ]);
    }
}
