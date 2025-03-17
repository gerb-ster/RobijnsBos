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
 * Class Vegetation
 * 
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property int $status_id
 * @property int $group_id
 * @property int $specie_id
 * @property int $amount
 * @property string|null $plant_year
 * @property string $remarks
 * @property string|null $qr_filename
 * @property string|null $location
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property Group $group
 * @property Species $species
 * @property VegetationStatus $vegetation_status
 * @property Collection|Comment[] $comments
 * @property Collection|Mutation[] $mutations
 *
 * @package App\Models
 */
class Vegetation extends Model
{
	use SoftDeletes;
	protected $table = 'vegetations';

	protected $casts = [
		'status_id' => 'int',
		'group_id' => 'int',
		'specie_id' => 'int',
		'amount' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'uuid',
		'number',
		'status_id',
		'group_id',
		'specie_id',
		'amount',
		'plant_year',
		'remarks',
		'qr_filename',
		'location',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function group()
	{
		return $this->belongsTo(Group::class);
	}

	public function species()
	{
		return $this->belongsTo(Species::class, 'specie_id');
	}

	public function vegetation_status()
	{
		return $this->belongsTo(VegetationStatus::class, 'status_id');
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	public function mutations()
	{
		return $this->hasMany(Mutation::class);
	}
}
