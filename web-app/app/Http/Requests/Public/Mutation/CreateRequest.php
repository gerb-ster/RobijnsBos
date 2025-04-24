<?php

namespace App\Http\Requests\Public\Mutation;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Public\Mutation
 */
class CreateRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'title' => 'nullable|string|min:1|max:128',
      'remarks' => 'nullable|string'
    ];
  }

  /**
   * Custom message for validation
   *
   * @return array
   */
  public function messages(): array
  {
    return [
      'title.required' => 'Name is required!',
    ];
  }
}
