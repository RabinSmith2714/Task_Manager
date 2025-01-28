<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 * 
 * @property int $did
 * @property string $dname
 *
 * @package App\Models
 */
class Department extends Model
{
	protected $table = 'department';
	protected $primaryKey = 'did';
	public $timestamps = false;

	protected $fillable = [
		'dname'
	];
}
