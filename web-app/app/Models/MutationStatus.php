<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MutationStatus
 * 
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Mutation[] $mutations
 *
 * @package App\Models
 */
class MutationStatus extends Model
{
	protected $table = 'mutation_status';

	protected $fillable = [
		'name'
	];

	public function mutations()
	{
		return $this->hasMany(Mutation::class, 'status_id');
	}
}
