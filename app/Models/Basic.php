<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Basic
 * 
 * @property string $id
 * @property string $fname
 * @property string $lname
 * @property string $photo
 * @property string $gender
 * @property string $dob
 * @property string $religion
 * @property string $social
 * @property string $caste
 * @property string $ms
 * @property string $pmc
 * @property string $pim1
 * @property string $pim2
 * @property string $paddress
 * @property string $taddress
 * @property string $state
 * @property string $city
 * @property string $zip
 * @property string $country
 * @property string $mobile
 * @property string $email
 * @property string $blood
 * @property string $aadhar
 * @property string $pan
 * @property string $surgery
 * @property string $insurance
 *
 * @package App\Models
 */
class Basic extends Model
{
	protected $table = 'basic';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'fname',
		'lname',
		'photo',
		'gender',
		'dob',
		'religion',
		'social',
		'caste',
		'ms',
		'pmc',
		'pim1',
		'pim2',
		'paddress',
		'taddress',
		'state',
		'city',
		'zip',
		'country',
		'mobile',
		'email',
		'blood',
		'aadhar',
		'pan',
		'surgery',
		'insurance'
	];
}
