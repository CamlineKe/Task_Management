<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Task Model
 * Represents a task in the task management system
 * Includes status transition logic and business rules
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     * Status is fillable but typically set via default or status update endpoint
     */
    protected $fillable = [
        'title',
        'due_date',
        'priority',
        'status',
    ];

    /**
     * Default attribute values
     * Status defaults to 'pending' on creation
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Cast attributes to native types
     */
    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status transition mapping
     * Defines allowed status movements:
     * - pending can only move to in_progress
     * - in_progress can only move to done
     * - done cannot move anywhere (terminal state)
     */
    protected $statusTransitions = [
        'pending' => ['in_progress'],
        'in_progress' => ['done'],
        'done' => [], # No transitions allowed from done
    ];

    /**
     * Check if status can transition to new status
     * Used in PATCH /api/tasks/{id}/status endpoint
     *
     * @param string $newStatus The requested new status
     * @return bool True if transition is allowed, false otherwise
     */
    public function canTransitionTo(string $newStatus): bool
    {
        # Get allowed transitions for current status, default to empty array if not defined
        $allowedTransitions = $this->statusTransitions[$this->status] ?? [];

        # Check if new status is in allowed transitions list
        return in_array($newStatus, $allowedTransitions);
    }

    /**
     * Get allowed next statuses for current status
     * Useful for UI showing available status options
     *
     * @return array List of allowed next statuses
     */
    public function getAllowedTransitions(): array
    {
        return $this->statusTransitions[$this->status] ?? [];
    }
}