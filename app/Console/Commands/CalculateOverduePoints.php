<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subtask;
use App\Models\Mainbranch;
use App\Models\Maintask;
use App\Models\Point;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalculateOverduePoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:overdue-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate overdue points for subtasks and mainbranch tasks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('CalculateOverduePoints command started.');

        $this->processSubtaskPoints();
        $this->processMainbranchPoints();

        Log::info('CalculateOverduePoints command completed successfully.');
        $this->info('Demerit points calculated for subtasks and mainbranch tasks successfully.');

        return 0;
    }

    /**
     * Calculate demerit points for subtasks.
     */
    protected function processSubtaskPoints()
{
    $tasks = Subtask::where('status', '!=', 3)
        ->orWhereNull('completed_date')
        ->get();

    foreach ($tasks as $task) {
        $maintask = Maintask::find($task->task_id);

        if (!$maintask) {
            Log::warning('No maintask found for subtask:', ['task_id' => $task->task_id]);
            continue;
        }

        $deadline = Carbon::parse($task->deadline);
        $currentDate = Carbon::now();

        // Skip storing demerit points if the deadline is today
        if ($currentDate->isSameDay($deadline)) {
            continue;
        }

        if ($currentDate->greaterThan($deadline)) {
            $overdueDays = $currentDate->diffInDays($deadline);
            
            // Determine multiplier based on complexity_level
            $multiplier = match ($maintask->complexity_level) {
                'Easy' => 3,
                'Medium' => 2,
                'Hard' => 1,
                default => 1
            };

            $demeritPoints = $overdueDays * $multiplier;

            $existingPoint = Point::where('task_id', $task->task_id)
                ->where('assigned_to_id', $task->assigned_to_id)
                ->whereDate('date', $currentDate->toDateString())
                ->first();

            if (!$existingPoint || $existingPoint->demerit_points != $demeritPoints) {
                Point::updateOrCreate(
                    [
                        'task_id' => $task->task_id,
                        'assigned_to_id' => $task->assigned_to_id,
                        'date' => $currentDate->toDateString(),
                    ],
                    [
                        'demerit_points' => $demeritPoints,
                    ]
                );

                Log::info('Demerit points calculated for subtask:', [
                    'task_id' => $task->task_id,
                    'assigned_to_id' => $task->assigned_to_id,
                    'complexity_level' => $maintask->complexity_level,
                    'demerit_points' => $demeritPoints
                ]);
            }
        }
    }
}

/**
 * Calculate demerit points for mainbranch tasks.
 */
protected function processMainbranchPoints()
{
    $mainbranchTasks = Mainbranch::whereNull('completed_date')->get();

    foreach ($mainbranchTasks as $task) {
        $deadline = Carbon::parse($task->deadline); // Now using Mainbranch deadline
        $currentDate = Carbon::now();

        // Skip storing demerit points if the deadline is today
        if ($currentDate->isSameDay($deadline)) {
            continue;
        }

        if ($currentDate->greaterThan($deadline)) {
            $maintask = Maintask::find($task->task_id);

            if (!$maintask) {
                Log::warning('No maintask found for mainbranch task:', ['task_id' => $task->task_id]);
                continue;
            }

            $overdueDays = $currentDate->diffInDays($deadline);

            // Determine multiplier based on complexity_level
            $multiplier = match ($maintask->complexity_level) {
                'Easy' => 3,
                'Medium' => 2,
                'Hard' => 1,
                default => 1
            };

            $demeritPoints = $overdueDays * $multiplier;

            $existingPoint = Point::where('task_id', $task->task_id)
                ->where('assigned_to_id', $task->assigned_to_id)
                ->whereDate('date', $currentDate->toDateString())
                ->first();

            if (!$existingPoint || $existingPoint->demerit_points != $demeritPoints) {
                Point::updateOrCreate(
                    [
                        'task_id' => $task->task_id,
                        'assigned_to_id' => $task->assigned_to_id,
                        'date' => $currentDate->toDateString(),
                    ],
                    [
                        'demerit_points' => $demeritPoints,
                    ]
                );

                Log::info('Demerit points calculated for mainbranch task:', [
                    'task_id' => $task->task_id,
                    'assigned_to_id' => $task->assigned_to_id,
                    'complexity_level' => $maintask->complexity_level,
                    'demerit_points' => $demeritPoints
                ]);
            }
        }
    }
}

    
}
