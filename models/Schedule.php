<?php
/**
 * Schedule Model using Eloquent ORM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'subject',
        'start_time',
        'end_time',
        'is_completed'
    ];
    
    protected $casts = [
        'is_completed' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];
    
    public $timestamps = false;
    
    const CREATED_AT = 'created_at';
    
    /**
     * Get the user that owns this schedule
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
