<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\DeadlineReminderMail;
use App\Models\Maintask;
use App\Models\Subtask;
use App\Models\Mainbranch;
use App\Models\Basic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDeadlineReminder extends Command
{
    protected $signature = 'send:deadline-reminder';
    protected $description = 'Send deadline reminder email for tasks with deadlines near.';

    public function handle()
    {
        Log::info('Deadline Reminder Process Started.');

        $this->sendSubtaskDeadlineReminders();
        $this->sendMainbranchDeadlineReminders();

        Log::info('Deadline Reminder Process Completed.');
        $this->info('Deadline reminders sent successfully.');
    }

    /**
     * Send deadline reminders for subtasks.
     */
    protected function sendSubtaskDeadlineReminders()
    {
        $tasks = Subtask::where('status', '!=', 3)
            ->WhereNull('completed_date')
            ->get();

        foreach ($tasks as $task) {
            $maintask = Maintask::find($task->task_id);

            if (!$maintask) {
                Log::warning('No maintask found for subtask_id:', ['task_id' => $task->task_id]);
                continue;
            }

            $deadline = Carbon::parse($task->deadline);
            $currentDate = Carbon::now();

            // Check if the deadline is near (within 2 days)
            if ($currentDate->diffInDays($deadline, false) == 2) {
                $this->sendEmail($task, $maintask, 'Subtask');
            }
        }
    }

    /**
     * Send deadline reminders for mainbranch tasks.
     */
    protected function sendMainbranchDeadlineReminders()
    {
        $mainbranchTasks = Mainbranch::whereNull('completed_date')->get();

        foreach ($mainbranchTasks as $task) {
            $maintask = Maintask::find($task->task_id);

            if (!$maintask) {
                Log::warning('No maintask found for mainbranch_id:', ['task_id' => $task->task_id]);
                continue;
            }

            $deadline = Carbon::parse($maintask->deadline);
            $currentDate = Carbon::now();

            // Check if the deadline is near (within 2 days)
            if ($currentDate->diffInDays($deadline, false) == 2 ) {
                $this->sendEmail($task, $maintask, 'Mainbranch');
            }
        }
    }

    /**
     * Send an email to the assigned user.
     *
     * @param $task
     * @param $maintask
     * @param string $type
     */
    protected function sendEmail($task, $maintask, $type)
    {
        $email = $this->getEmailForAssignedTo($task->assigned_to_id);

        if (!$email) {
            Log::warning("Email not found for assigned_to_id: {$task->assigned_to_id}");
            return;
        }

        $details = [
            'task_id' => $task->task_id,
            'title' => $maintask->title ?? 'N/A',
            'description' => $maintask->description ?? 'N/A',
            'assigned_date' => $task->assigned_date ?? 'N/A',
            'deadline' => $task->deadline ?? 'N/A',
            'assigned_by_id' => $maintask->assigned_by_id ?? 'N/A',
            'type' => $type,
        ];

        // Send email
        Mail::to($email)->send(new DeadlineReminderMail($details));

        Log::info("Deadline reminder email sent for {$type}:", [
            'task_id' => $details['task_id'],
            'email' => $email,
        ]);
    }

    /**
     * Get email address for the assigned_to_id from the basic table.
     *
     * @param int $assignedToId
     * @return string|null
     */
    protected function getEmailForAssignedTo($assignedToId)
    {
        $basic = Basic::where('id', $assignedToId)->first();
        return $basic ? $basic->email : null;
    }
}
