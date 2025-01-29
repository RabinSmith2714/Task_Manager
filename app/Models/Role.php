<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * 
 * @property int $Rid
 * @property string|null $type
 * @property string|null $Rolename
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'role';
	protected $primaryKey = 'Rid';
	public $timestamps = false;

	protected $fillable = [
		'type',
		'Rolename'
	];
}
