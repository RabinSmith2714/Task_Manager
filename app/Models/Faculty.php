<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faculty
 * 
 * @property int $uid
 * @property string|null $id
 * @property string|null $name
 * @property string|null $dept
 * @property string|null $design
 * @property string|null $role
 * @property Carbon|null $doj
 * @property string|null $pass
 * @property string|null $cert
 * @property int|null $bc
 * @property int|null $ac
 * @property int|null $status
 *
 * @package App\Models
 */
class Faculty extends Model
{
	protected $table = 'faculty';
	protected $primaryKey = 'uid';
	public $timestamps = false;

	protected $casts = [
		'doj' => 'datetime',
		'bc' => 'int',
		'ac' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'id',
		'name',
		'dept',
		'design',
		'role',
		'doj',
		'pass',
		'cert',
		'bc',
		'ac',
		'status'
	];
}
