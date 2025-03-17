<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VegetationStatus
 * 
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Vegetation[] $vegetations
 *
 * @package App\Models
 */
class VegetationStatus extends Model
{
	protected $table = 'vegetation_status';

	protected $fillable = [
		'name'
	];

	public function vegetations()
	{
		return $this->hasMany(Vegetation::class, 'status_id');
	}
}
