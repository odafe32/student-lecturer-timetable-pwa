<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Faculty extends Model
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
        'description',
    ];

    /**
     * Get the departments for the faculty.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the students for the faculty through departments.
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, Department::class);
    }
}