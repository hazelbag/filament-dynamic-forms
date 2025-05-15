<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_configuration_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the form configuration that this submission belongs to.
     */
    public function formConfiguration(): BelongsTo
    {
        return $this->belongsTo(FormConfiguration::class);
    }
}
