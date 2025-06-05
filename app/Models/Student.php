<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Student extends Model
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
        'user_id',
        'department_id',
        'matric_number',
        'level',
        'status',
        'address',
        'profile_image',
        'phone_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Get the user that owns the student profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the student belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the faculty through the department.
     */
    public function faculty()
    {
        return $this->hasOneThrough(Faculty::class, Department::class, 'id', 'id', 'department_id', 'faculty_id');
    }

    /**
     * Get the messages that this student has read.
     */
    public function readMessages()
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Check if this student has read a specific message.
     *
     * @param string $messageId
     * @return bool
     */
    public function hasReadMessage($messageId)
    {
        return $this->readMessages()->where('message_id', $messageId)->exists();
    }

    /**
     * Mark a message as read by this student.
     *
     * @param string $messageId
     * @return \App\Models\MessageRead
     */
    public function markMessageAsRead($messageId)
    {
        if (!$this->hasReadMessage($messageId)) {
            return MessageRead::create([
                'message_id' => $messageId,
                'student_id' => $this->id
            ]);
        }
        
        return $this->readMessages()->where('message_id', $messageId)->first();
    }
}