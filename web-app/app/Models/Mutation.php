<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Mutation
 * 
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property int $status_id
 * @property int $vegetation_id
 * @property string $title
 * @property string $remarks
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property MutationStatus $mutation_status
 * @property Vegetation $vegetation
 *
 * @package App\Models
 */
class Mutation extends Model
{
	use SoftDeletes;
	protected $table = 'mutations';

	protected $casts = [
		'status_id' => 'int',
		'vegetation_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'uuid',
		'number',
		'status_id',
		'vegetation_id',
		'title',
		'remarks',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function mutation_status()
	{
		return $this->belongsTo(MutationStatus::class, 'status_id');
	}

	public function vegetation()
	{
		return $this->belongsTo(Vegetation::class);
	}
}
