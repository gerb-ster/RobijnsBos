<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 * 
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Group[] $groups
 *
 * @package App\Models
 */
class Area extends Model
{
	protected $table = 'areas';

	protected $fillable = [
		'name'
	];

	public function groups()
	{
		return $this->hasMany(Group::class);
	}
}
