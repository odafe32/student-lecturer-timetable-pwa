<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;

class Timetable extends Model
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
        'lecturer_id',
        'course_id',
        'faculty_id',
        'department_id',
        'level',
        'day_of_week',
        'start_time',
        'end_time',
        'venue',
        'notes',
        'status',
        'effective_date',
        'end_date',
        'is_recurring',
        'total_sessions',
        'completed_sessions',
        'session_dates',
        'completed_dates',
        'completion_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'session_dates' => 'array',
        'completed_dates' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'time_range',
        'formatted_day',
        'course_code',
        'course_title',
    ];

    /**
     * Get the lecturer that owns the timetable.
     */
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * Get the course for this timetable.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the faculty for this timetable.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the department for this timetable.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Generate session dates based on the timetable schedule.
     *
     * @return array
     */
    public function generateSessionDates()
    {
        if (!$this->is_recurring || !$this->effective_date) {
            // For non-recurring classes, return the single effective date
            return $this->effective_date ? [Carbon::parse($this->effective_date)->format('Y-m-d')] : [];
        }

        $sessions = [];
        $startDate = Carbon::parse($this->effective_date);
        $endDate = $this->end_date ? Carbon::parse($this->end_date) : $startDate->copy()->addMonths(4);

        // Get the day of week number (1 = Monday, 7 = Sunday)
        $dayOfWeekMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0,
        ];

        $targetDayOfWeek = $dayOfWeekMap[strtolower($this->day_of_week)] ?? 1;

        // Find the first occurrence of the target day on or after the start date
        $currentDate = $startDate->copy();

        // If start date is not the target day, find the next occurrence
        while ($currentDate->dayOfWeek !== $targetDayOfWeek && $currentDate->lte($endDate)) {
            $currentDate->addDay();
        }

        // Generate all session dates
        while ($currentDate->lte($endDate)) {
            $sessions[] = $currentDate->format('Y-m-d');
            $currentDate->addWeek(); // Move to next week

            // Safety check to prevent infinite loop
            if (count($sessions) > 100) { // Max 100 sessions
                break;
            }
        }

        return $sessions;
    }

    /**
     * Update session dates and save to database.
     *
     * @return array
     */
    public function updateSessionDates()
    {
        $sessionDates = $this->generateSessionDates();

        // Determine completion status
        $completionStatus = 'pending';
        if (count($sessionDates) > 0) {
            $today = Carbon::today()->format('Y-m-d');
            $hasStarted = false;
            $hasEnded = true;

            foreach ($sessionDates as $sessionDate) {
                if ($sessionDate <= $today) {
                    $hasStarted = true;
                }
                if ($sessionDate >= $today) {
                    $hasEnded = false;
                }
            }

            if ($hasStarted && !$hasEnded) {
                $completionStatus = 'ongoing';
            } elseif ($hasStarted && $hasEnded) {
                $completionStatus = 'completed';
            }
        }

        $this->update([
            'session_dates' => $sessionDates,
            'total_sessions' => count($sessionDates),
            'completion_status' => $completionStatus
        ]);

        return $sessionDates;
    }

    /**
     * Mark a session as completed.
     *
     * @param string $date
     * @return $this
     */
    public function markSessionCompleted($date)
    {
        $completedDates = $this->completed_dates ?? [];

        if (!in_array($date, $completedDates)) {
            $completedDates[] = $date;
            $this->update([
                'completed_dates' => $completedDates,
                'completed_sessions' => count($completedDates),
                'completion_status' => $this->getCompletionStatus($completedDates)
            ]);
        }

        return $this;
    }

    /**
     * Get completion status based on completed sessions.
     *
     * @param array $completedDates
     * @return string
     */
    private function getCompletionStatus($completedDates)
    {
        $totalSessions = $this->total_sessions ?? 0;
        $completedCount = count($completedDates);

        if ($totalSessions === 0) {
            return 'pending';
        }

        if ($completedCount === 0) {
            return 'ongoing';
        }

        if ($completedCount >= $totalSessions) {
            return 'completed';
        }

        return 'ongoing';
    }

    /**
     * Get upcoming sessions.
     *
     * @return array
     */
    public function getUpcomingSessions()
    {
        $sessionDates = $this->session_dates ?? [];

        // If no session dates, generate them
        if (empty($sessionDates)) {
            $sessionDates = $this->generateSessionDates();
        }

        $completedDates = $this->completed_dates ?? [];
        $today = Carbon::today()->format('Y-m-d');

        return collect($sessionDates)
            ->filter(function ($date) use ($completedDates, $today) {
                return !in_array($date, $completedDates) && $date >= $today;
            })
            ->values()
            ->toArray();
    }

    /**
     * Get past sessions.
     *
     * @return array
     */
    public function getPastSessions()
    {
        $sessionDates = $this->session_dates ?? [];

        // If no session dates, generate them
        if (empty($sessionDates)) {
            $sessionDates = $this->generateSessionDates();
        }

        $today = Carbon::today()->format('Y-m-d');

        return collect($sessionDates)
            ->filter(function ($date) use ($today) {
                return $date < $today;
            })
            ->map(function ($date) {
                return [
                    'date' => $date,
                    'completed' => in_array($date, $this->completed_dates ?? []),
                    'formatted_date' => Carbon::parse($date)->format('M d, Y'),
                    'day_name' => Carbon::parse($date)->format('l')
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get completion percentage.
     *
     * @return float|int
     */
    public function getCompletionPercentage()
    {
        $totalSessions = $this->total_sessions ?? 0;
        $completedSessions = $this->completed_sessions ?? 0;

        if ($totalSessions === 0) {
            return 0;
        }

        return round(($completedSessions / $totalSessions) * 100, 2);
    }


    /**
     * Get formatted time range.
     *
     * @return string
     */
    public function getTimeRangeAttribute()
    {
        return Carbon::parse($this->start_time)->format('H:i') . ' - ' . Carbon::parse($this->end_time)->format('H:i');
    }

    /**
     * Get formatted day.
     *
     * @return string
     */
    public function getFormattedDayAttribute()
    {
        return ucfirst($this->day_of_week);
    }

    /**
     * Get course code (either from relationship or direct attribute).
     *
     * @return string
     */
    public function getCourseCodeAttribute()
    {
        return $this->course ? $this->course->course_code : null;
    }

    /**
     * Get course title (either from relationship or direct attribute).
     *
     * @return string
     */
    public function getCourseTitleAttribute()
    {
        return $this->course ? $this->course->course_title : null;
    }

    /**
     * Scope for active timetables.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for current week.
     */
    public function scopeCurrentWeek($query)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $query->where(function ($q) use ($startOfWeek, $endOfWeek) {
            // For non-recurring classes
            $q->where(function ($subQ) use ($startOfWeek, $endOfWeek) {
                $subQ->where('is_recurring', false)
                     ->whereBetween('effective_date', [$startOfWeek, $endOfWeek]);
            })
            // For recurring classes
            ->orWhere(function ($subQ) use ($startOfWeek, $endOfWeek) {
                $subQ->where('is_recurring', true)
                     ->where('effective_date', '<=', $endOfWeek)
                     ->where(function ($subSubQ) use ($startOfWeek) {
                         $subSubQ->whereNull('end_date')
                                 ->orWhere('end_date', '>=', $startOfWeek);
                     });
            });
        });
    }

    /**
     * Check if this timetable has a session in the current week.
     *
     * @return bool
     */
    public function hasSessionThisWeek()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $this->hasSessionInWeek($startOfWeek, $endOfWeek);
    }

    /**
     * Check if this timetable has a session in a specific week.
     *
     * @param Carbon $startOfWeek
     * @param Carbon $endOfWeek
     * @return bool
     */
    public function hasSessionInWeek($startOfWeek, $endOfWeek)
    {
        // Check if timetable is active
        if ($this->status !== 'active') {
            return false;
        }

        // Check if effective date is set and not in the future beyond this week
        if ($this->effective_date) {
            $effectiveDate = Carbon::parse($this->effective_date);
            if ($effectiveDate->gt($endOfWeek)) {
                return false; // Starts after this week
            }
        }

        // Check if end date is set and already passed before this week
        if ($this->end_date) {
            $endDate = Carbon::parse($this->end_date);
            if ($endDate->lt($startOfWeek)) {
                return false; // Ended before this week
            }
        }

        if (!$this->is_recurring) {
            // For non-recurring, check if the single date falls in the week
            if ($this->effective_date) {
                $effectiveDate = Carbon::parse($this->effective_date);
                return $effectiveDate->between($startOfWeek, $endOfWeek);
            }
            return false;
        }

        // For recurring classes, check if there's a session this week
        $sessionDates = $this->session_dates ?? [];

        // If no session dates generated, generate them
        if (empty($sessionDates)) {
            $sessionDates = $this->generateSessionDates();
        }

        $startOfWeekStr = $startOfWeek->format('Y-m-d');
        $endOfWeekStr = $endOfWeek->format('Y-m-d');

        foreach ($sessionDates as $sessionDate) {
            if ($sessionDate >= $startOfWeekStr && $sessionDate <= $endOfWeekStr) {
                return true;
            }
        }

        // Fallback: check by day of week if session dates are not properly generated
        $dayOfWeekMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0,
        ];

        $targetDayOfWeek = $dayOfWeekMap[strtolower($this->day_of_week)] ?? 1;

        // Check if the target day falls within this week and within the effective period
        $currentDate = $startOfWeek->copy();
        while ($currentDate->lte($endOfWeek)) {
            if ($currentDate->dayOfWeek === $targetDayOfWeek) {
                // Check if this date is within the effective period
                $effectiveDate = $this->effective_date ? Carbon::parse($this->effective_date) : null;
                $endDate = $this->end_date ? Carbon::parse($this->end_date) : null;

                if ($effectiveDate && $currentDate->lt($effectiveDate)) {
                    return false;
                }

                if ($endDate && $currentDate->gt($endDate)) {
                    return false;
                }

                return true;
            }
            $currentDate->addDay();
        }

        return false;
    }

    /**
     * Scope for completed timetables.
     *
     * @param $query
     * @return mixed
     */
    public function scopeCompleted($query)
    {
        return $query->where('completion_status', 'completed');
    }

    /**
     * Scope for ongoing timetables.
     *
     * @param $query
     * @return mixed
     */
    public function scopeOngoing($query)
    {
        return $query->where('completion_status', 'ongoing');
    }

    /**
     * Check if timetable is active for a specific date.
     *
     * @param string $date
     * @return bool
     */
    public function isActiveForDate($date)
    {
        $checkDate = Carbon::parse($date);
        $effectiveDate = Carbon::parse($this->effective_date);
        $endDate = $this->end_date ? Carbon::parse($this->end_date) : null;

        // Check if date is within the effective period
        if ($checkDate->lt($effectiveDate)) {
            return false;
        }

        if ($endDate && $checkDate->gt($endDate)) {
            return false;
        }

        // For recurring classes, check if it's the right day of week
        if ($this->is_recurring) {
            $dayOfWeekMap = [
                'monday' => 1,
                'tuesday' => 2,
                'wednesday' => 3,
                'thursday' => 4,
                'friday' => 5,
                'saturday' => 6,
                'sunday' => 0,
            ];

            $targetDayOfWeek = $dayOfWeekMap[strtolower($this->day_of_week)] ?? 1;
            return $checkDate->dayOfWeek === $targetDayOfWeek;
        }

        // For non-recurring, check if it's the exact date
        return $checkDate->isSameDay($effectiveDate);
    }

    /**
     * Check if there's a scheduling conflict.
     *
     * @param string $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param string|null $venue
     * @param string|null $effectiveDate
     * @param string|null $excludeId
     * @return bool
     */
    public static function hasConflict($dayOfWeek, $startTime, $endTime, $venue = null, $effectiveDate = null, $excludeId = null)
    {
        $query = self::where('day_of_week', $dayOfWeek)
            ->where('status', 'active')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($subQ) use ($startTime, $endTime) {
                    // New class starts during existing class
                    $subQ->where('start_time', '<=', $startTime)
                        ->where('end_time', '>', $startTime);
                })->orWhere(function ($subQ) use ($startTime, $endTime) {
                    // New class ends during existing class
                    $subQ->where('start_time', '<', $endTime)
                        ->where('end_time', '>=', $endTime);
                })->orWhere(function ($subQ) use ($startTime, $endTime) {
                    // New class completely overlaps existing class
                    $subQ->where('start_time', '>=', $startTime)
                        ->where('end_time', '<=', $endTime);
                });
            });

        if ($venue) {
            $query->where('venue', $venue);
        }

        if ($effectiveDate) {
            $query->where(function ($q) use ($effectiveDate) {
                $q->whereNull('effective_date')
                    ->orWhere('effective_date', '<=', $effectiveDate)
                    ->where(function ($subQ) use ($effectiveDate) {
                        $subQ->whereNull('end_date')
                            ->orWhere('end_date', '>=', $effectiveDate);
                    });
            });
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
