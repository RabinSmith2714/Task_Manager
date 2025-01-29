<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Maintask
 * 
 * @property int $task_id
 * @property string|null $title
 * @property string|null $description
 * @property int|null $assigned_by_id
 * @property string|null $assigned_by_name
 * @property Carbon|null $assigned_date
 * @property Carbon|null $completed_date
 * @property int|null $status
 * @property int|null $forward_count
 * @property Carbon|null $deadline
 * 
 * @property Collection|Mainbranch[] $mainbranches
 * @property Collection|Point[] $points
 * @property Collection|Subtask[] $subtasks
 *
 * @package App\Models
 */
class Maintask extends Model
{
	protected $table = 'maintask';
	protected $primaryKey = 'task_id';
	public $timestamps = false;

	protected $casts = [
		'assigned_by_id' => 'int',
		'assigned_date' => 'datetime',
		'completed_date' => 'datetime',
		'status' => 'int',
		'forward_count' => 'int',
		'deadline' => 'datetime'
	];

	protected $fillable = [
		'title',
		'description',
		'assigned_by_id',
		'assigned_by_name',
		'assigned_date',
		'completed_date',
		'status',
		'forward_count',
		'deadline'
	];

	public function mainbranches()
	{
		return $this->hasMany(Mainbranch::class, 'task_id');
	}

	public function points()
	{
		return $this->hasMany(Point::class, 'task_id');
	}

	public function subtasks()
	{
		return $this->hasMany(Subtask::class, 'task_id');
	}
}
