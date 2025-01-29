<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Specialrole
 * 
 * @property int $S_no
 * @property int $id
 * @property string|null $type
 * @property string $Role
 * @property string $Status
 *
 * @package App\Models
 */
class Specialrole extends Model
{
	protected $table = 'specialrole';
	protected $primaryKey = 'S_no';
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'id',
		'type',
		'Role',
		'Status'
	];
}
