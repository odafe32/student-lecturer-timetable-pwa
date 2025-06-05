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
        // First, check the primary key type of the departments table
        $departmentKeyType = $this->getDepartmentKeyType();
        
        Schema::create('courses', function (Blueprint $table) use ($departmentKeyType) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('course_title');
            $table->integer('credit_units')->default(3);
            
            // Use the correct foreign key type based on the departments table
            if ($departmentKeyType === 'uuid') {
                $table->uuid('department_id');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            } else {
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            }
            $table->string('level'); // 100, 200, 300, 400
            $table->enum('semester', ['first', 'second', 'both'])->default('both');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['department_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
    
    /**
     * Get the primary key type of the departments table.
     *
     * @return string
     */
    private function getDepartmentKeyType(): string
    {
        try {
            // Check if the departments table exists
            if (!Schema::hasTable('departments')) {
                return 'bigint'; // Default to bigint if table doesn't exist
            }
            
            // Get the column type of the id column
            $columnType = DB::select("SHOW COLUMNS FROM departments WHERE Field = 'id'");
            
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
