<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
	protected $table = 'comment_status';

	protected $fillable = [
		'name'
	];

	public function comments()
	{
		return $this->hasMany(Comment::class, 'status_id');
	}
}
