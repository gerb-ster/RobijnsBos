<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Comment
 * 
 * @property int $id
 * @property string $uuid
 * @property string $number
 * @property int $status_id
 * @property int $vegetation_id
 * @property string $title
 * @property string $remarks
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User|null $user
 * @property CommentStatus $comment_status
 * @property Vegetation $vegetation
 *
 * @package App\Models
 */
class Comment extends Model
{
	use SoftDeletes;
	protected $table = 'comments';

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

	public function comment_status()
	{
		return $this->belongsTo(CommentStatus::class, 'status_id');
	}

	public function vegetation()
	{
		return $this->belongsTo(Vegetation::class);
	}
}
