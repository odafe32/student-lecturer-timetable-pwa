<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Message extends Model
{
    use HasFactory, HasUuids;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'faculty_id',
        'department_id',
        'level',
        'title',
        'content',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
        'sender_id' => 'integer', // Cast sender_id as integer since it references users table
    ];

    /**
     * Get the sender (lecturer) of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the faculty associated with the message.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the department associated with the message.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the students who have read this message.
     */
    public function readBy()
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Check if a specific student has read this message.
     *
     * @param string $studentId
     * @return bool
     */
    public function isReadBy($studentId)
    {
        return $this->readBy()->where('student_id', $studentId)->exists();
    }

    /**
     * Scope a query to only include active messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the recipients of this message (students matching criteria).
     */
    public function recipients()
    {
        $query = Student::query()
            ->where('department_id', $this->department_id)
            ->where('level', $this->level)
            ->where('status', 'active');
            
        return $query;
    }
}