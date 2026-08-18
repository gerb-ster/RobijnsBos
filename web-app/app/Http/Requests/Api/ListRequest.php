<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ListRequest
 * @package App\Http\Requests\Api
 */
class ListRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'status' => 'nullable|array',
    ];
  }
}
