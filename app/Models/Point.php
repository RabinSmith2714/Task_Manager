<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Point
 * 
 * @property int $point_id
 * @property int|null $task_id
 * @property int|null $assigned_to_id
 * @property int|null $demerit_points
 * @property Carbon|null $date
 * 
 * @property Maintask|null $maintask
 *
 * @package App\Models
 */
class Point extends Model
{
	protected $table = 'point';
	protected $primaryKey = 'point_id';
	public $timestamps = false;

	protected $casts = [
		'task_id' => 'int',
		'assigned_to_id' => 'int',
		'demerit_points' => 'int',
		'date' => 'datetime'
	];

	protected $fillable = [
		'task_id',
		'assigned_to_id',
		'demerit_points',
		'date'
	];

	public function maintask()
	{
		return $this->belongsTo(Maintask::class, 'task_id');
	}
}
