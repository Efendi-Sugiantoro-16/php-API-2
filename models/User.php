<?php
/**
 * User Model using Eloquent ORM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'username',
        'email',
        'password',
        'name',
        'npm',
        'photo'
    ];
    
    protected $hidden = [
        'password'
    ];
    
    public $timestamps = true;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    /**
     * Get schedules for this user
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'user_id');
    }
}
