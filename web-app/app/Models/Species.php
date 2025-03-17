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
 * Class Species
 * 
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property string $dutch_name
 * @property string $latin_name
 * @property int $latin_family_id
 * @property string|null $blossom_month
 * @property string|null $height
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property LatinFamily $latin_family
 * @property Collection|Vegetation[] $vegetations
 *
 * @package App\Models
 */
class Species extends Model
{
	use SoftDeletes;
	protected $table = 'species';

	protected $casts = [
		'latin_family_id' => 'int'
	];

	protected $fillable = [
		'uuid',
		'number',
		'dutch_name',
		'latin_name',
		'latin_family_id',
		'blossom_month',
		'height'
	];

	public function latin_family()
	{
		return $this->belongsTo(LatinFamily::class);
	}

	public function vegetations()
	{
		return $this->hasMany(Vegetation::class, 'specie_id');
	}
}
