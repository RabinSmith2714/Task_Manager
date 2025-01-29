<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mainbranch
 * 
 * @property int $id
 * @property int|null $task_id
 * @property int|null $assigned_to_id
 * @property string|null $assigned_to_name
 * @property Carbon|null $completed_date
 * @property Carbon|null $deadline
 * @property string|null $reason
 * @property string|null $feedback
 * @property int|null $status
 * 
 * @property Maintask|null $maintask
 *
 * @package App\Models
 */
class Mainbranch extends Model
{
	protected $table = 'mainbranch';
	public $timestamps = false;

	protected $casts = [
		'task_id' => 'int',
		'assigned_to_id' => 'int',
		'completed_date' => 'datetime',
		'deadline' => 'datetime',
		'status' => 'int'
	];

	protected $fillable = [
		'task_id',
		'assigned_to_id',
		'assigned_to_name',
		'completed_date',
		'deadline',
		'reason',
		'feedback',
		'status'
	];

	public function maintask()
	{
		return $this->belongsTo(Maintask::class, 'task_id');
	}
}
