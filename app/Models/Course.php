<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_code',
        'course_title',
        'credit_units',
        'department_id',
        'level',
        'semester',
        'description',
        'status',
    ];

    /**
     * Get the department that owns the course.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the timetables for the course.
     */
    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * Get the faculty through the department.
     */
    public function faculty()
    {
        return $this->hasOneThrough(Faculty::class, Department::class, 'id', 'id', 'department_id', 'faculty_id');
    }

    /**
     * Scope for active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for courses by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for courses by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Get formatted course display name.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->course_code . ' - ' . $this->course_title;
    }
}