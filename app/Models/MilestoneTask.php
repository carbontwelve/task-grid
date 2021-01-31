<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MilestoneTask extends Pivot
{

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

}
