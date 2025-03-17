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
 * Class User
 * 
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property int $role_id
 * @property string $locale
 * @property string $email
 * @property string $password
 * @property bool $admin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Role $role
 * @property Collection|Comment[] $comments
 * @property Collection|Mutation[] $mutations
 * @property Collection|Vegetation[] $vegetations
 *
 * @package App\Models
 */
class User extends Model
{
	use SoftDeletes;
	protected $table = 'users';

	protected $casts = [
		'role_id' => 'int',
		'admin' => 'bool'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'uuid',
		'name',
		'role_id',
		'locale',
		'email',
		'password',
		'admin'
	];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

	public function comments()
	{
		return $this->hasMany(Comment::class, 'created_by');
	}

	public function mutations()
	{
		return $this->hasMany(Mutation::class, 'created_by');
	}

	public function vegetations()
	{
		return $this->hasMany(Vegetation::class, 'created_by');
	}
}
