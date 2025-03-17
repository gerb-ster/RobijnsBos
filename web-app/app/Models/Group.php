<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 * 
 * @property int $id
 * @property string|null $name
 * @property int $area_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Area $area
 * @property Collection|Vegetation[] $vegetations
 *
 * @package App\Models
 */
class Group extends Model
{
	protected $table = 'groups';

	protected $casts = [
		'area_id' => 'int'
	];

	protected $fillable = [
		'name',
		'area_id'
	];

	public function area()
	{
		return $this->belongsTo(Area::class);
	}

	public function vegetations()
	{
		return $this->hasMany(Vegetation::class);
	}
}
