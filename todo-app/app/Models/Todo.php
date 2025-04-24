<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model for a single todo item
class Todo extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'title',
        'completed',
        'reminder_at',
        'completed_at', // allow mass assignment for completed_at
    ];
    // No extra logic here for now, could add custom methods later
}
