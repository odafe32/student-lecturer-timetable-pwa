<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lecturer_id');
            $table->uuid('faculty_id'); // Changed to uuid to match faculties table
            
            // Check department and course key types dynamically
            $departmentKeyType = $this->getTableKeyType('departments');
            $courseKeyType = $this->getTableKeyType('courses');
            
            if ($departmentKeyType === 'uuid') {
                $table->uuid('department_id');
            } else {
                $table->foreignId('department_id');
            }
            
            if ($courseKeyType === 'uuid') {
                $table->uuid('course_id');
            } else {
                $table->foreignId('course_id');
            }
            
            $table->string('level'); // 100, 200, 300, 400, etc.
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'cancelled', 'rescheduled'])->default('active');
            $table->boolean('is_recurring')->default(true);
            $table->integer('total_sessions')->nullable();
            $table->integer('completed_sessions')->default(0);
            $table->json('session_dates')->nullable();
            $table->json('completed_dates')->nullable();
            $table->enum('completion_status', ['pending', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->date('effective_date')->nullable(); // When this schedule starts
            $table->date('end_date')->nullable(); // When this schedule ends
            $table->timestamps();

            // Add foreign key constraints manually with correct types
            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            
            if ($departmentKeyType === 'uuid') {
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            } else {
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            }
            
            if ($courseKeyType === 'uuid') {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            } else {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            }
            
            // Ensure no conflicts for same time, day, and venue
            $table->unique(['day_of_week', 'start_time', 'venue', 'effective_date'], 'unique_schedule_slot');
            
            // Index for better performance
            $table->index(['lecturer_id', 'day_of_week']);
            $table->index(['faculty_id', 'department_id', 'level']);
            $table->index(['lecturer_id', 'completion_status']);
            $table->index(['effective_date', 'end_date']);
        });

        // Set default values for JSON columns after table creation
        // This is more compatible with different database systems
        DB::statement('ALTER TABLE timetables ALTER COLUMN session_dates SET DEFAULT (JSON_ARRAY())');
        DB::statement('ALTER TABLE timetables ALTER COLUMN completed_dates SET DEFAULT (JSON_ARRAY())');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
    
    /**
     * Get the primary key type of a table.
     *
     * @param string $tableName
     * @return string
     */
    private function getTableKeyType(string $tableName): string
    {
        try {
            // Check if the table exists
            if (!Schema::hasTable($tableName)) {
                return 'bigint'; // Default to bigint if table doesn't exist
            }
            
            // Get the column type of the id column
            $columnType = DB::select("SHOW COLUMNS FROM {$tableName} WHERE Field = 'id'");
            
            if (!empty($columnType) && isset($columnType[0]->Type)) {
                if (strpos(strtolower($columnType[0]->Type), 'char') !== false) {
                    return 'uuid';
                }
            }
            
            return 'bigint'; // Default to bigint
        } catch (\Exception $e) {
            return 'bigint'; // Default to bigint on error
        }
    }
};
