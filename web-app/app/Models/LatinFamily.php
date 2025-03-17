<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LatinFamily
 * 
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Species[] $species
 *
 * @package App\Models
 */
class LatinFamily extends Model
{
	use SoftDeletes;
	protected $table = 'latin_families';

	protected $fillable = [
		'uuid',
		'number',
		'name'
	];

	public function species()
	{
		return $this->hasMany(Species::class);
	}
}
