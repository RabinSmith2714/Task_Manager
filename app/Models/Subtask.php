<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subtask
 * 
 * @property int $sid
 * @property int|null $task_id
 * @property int|null $assigned_by_id
 * @property string|null $assigned_by_name
 * @property int|null $assigned_to_id
 * @property string|null $assigned_to_name
 * @property Carbon|null $forwarded_date
 * @property Carbon|null $deadline
 * @property Carbon|null $completed_date
 * @property string|null $reason
 * @property string|null $feedback
 * @property int|null $status
 * 
 * @property Maintask|null $maintask
 *
 * @package App\Models
 */
class Subtask extends Model
{
	protected $table = 'subtask';
	protected $primaryKey = 'sid';
	public $timestamps = false;

	protected $casts = [
		'task_id' => 'int',
		'assigned_by_id' => 'int',
		'assigned_to_id' => 'int',
		'forwarded_date' => 'datetime',
		'deadline' => 'datetime',
		'completed_date' => 'datetime',
		'status' => 'int'
	];

	protected $fillable = [
		'task_id',
		'assigned_by_id',
		'assigned_by_name',
		'assigned_to_id',
		'assigned_to_name',
		'forwarded_date',
		'deadline',
		'completed_date',
		'reason',
		'feedback',
		'status'
	];

	public function maintask()
	{
		return $this->belongsTo(Maintask::class, 'task_id');
	}
}
