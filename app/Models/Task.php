<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    const UrgencyNotRequired = 'not-required';
    const UrgencyNiceToHave  = 'nice-to-have';
    const UrgencyRequired    = 'required';
    const UrgencyShowStopper = 'show-stopper';

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authored_by');
    }

    public function milestones():BelongsToMany
    {
        return $this->belongsToMany(Milestone::class)
            ->withPivot(['urgency']);
    }
}
