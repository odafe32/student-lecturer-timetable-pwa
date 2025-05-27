<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Department extends Model
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
        'name',
        'code',
        'faculty_id',
        'description',
    ];

    /**
     * Get the faculty that owns the department.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the students for the department.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}