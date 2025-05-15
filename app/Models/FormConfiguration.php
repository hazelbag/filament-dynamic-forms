<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormConfiguration extends Model
{
    protected $fillable = [
        'name',
        'title',
        'description',
        'fields',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'fields' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the submissions for this form configuration.
     */
    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * Check if this form has any submissions.
     *
     * @return bool
     */
    public function hasSubmissions()
    {
        return $this->submissions()->exists();
    }
}
