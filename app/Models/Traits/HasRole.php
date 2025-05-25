<?php

namespace App\Models\Traits;

trait HasRole
{
    /**
     * Check if the user has a specific role
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles);
    }

    /**
     * Check if the user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a lecturer
     *
     * @return bool
     */
    public function isLecturer()
    {
        return $this->role === 'lecturer';
    }

    /**
     * Check if the user is a student
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }
}