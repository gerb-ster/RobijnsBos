<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CommentStatus
 *
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Comment[] $comments
 *
 * @package App\Models
 */
class CommentStatus extends Model
{
  const APPROVED = 1;
  const BLOCKED = 2;

  /**
   * @var string
   */
	protected $table = 'comment_status';

  /**
   * @var string[]
   */
	protected $fillable = [
		'name'
	];

  /**
   * @return HasMany
   */
	public function comments(): HasMany
  {
		return $this->hasMany(Comment::class, 'status_id');
	}
}
