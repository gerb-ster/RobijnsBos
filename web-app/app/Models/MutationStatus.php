<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
  const NEW = 1;
  const APPROVED = 2;
  const BLOCKED = 3;

  /**
   * @var string
   */
	protected $table = 'mutation_status';

  /**
   * @var string[]
   */
	protected $fillable = [
		'name'
	];

  /**
   * @return HasMany
   */
	public function mutations(): HasMany
  {
		return $this->hasMany(Mutation::class, 'status_id');
	}
}
