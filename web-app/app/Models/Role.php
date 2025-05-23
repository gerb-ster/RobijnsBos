<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Role
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Role extends Model
{
  const ADMINISTRATOR = 1;
  const OWNER = 2;
  const VOLUNTEER = 3;

	protected $table = 'roles';

  /**
   * @var string[]
   */
	protected $fillable = [
		'name'
	];

  /**
   * @return HasMany
   */
	public function users(): HasMany
  {
		return $this->hasMany(User::class);
	}
}
