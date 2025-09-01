<?php

namespace App\Models;

use App\Observers\AreaObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Area
 *
 * @property int $id
 * @property string|null $name
 * @property array $coordinates
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
#[ObservedBy([AreaObserver::class])]
class Area extends Model
{
  use SoftDeletes;

  /**
   * @var string
   */
	protected $table = 'areas';

  /**
   * @var string[]
   */
  protected $casts = [
    'coordinates' => 'array'
  ];

  /**
   * @var string[]
   */
	protected $fillable = [
		'name',
    'coordinates'
	];
}
