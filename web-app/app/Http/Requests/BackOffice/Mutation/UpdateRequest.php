<?php

namespace App\Http\Requests\BackOffice\Mutation;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\BackOffice\Mutation
 */
class UpdateRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'title' => 'nullable|string|min:1|max:32',
      'remarks' => 'nullable|string|min:1|max:32'
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
      'name.required' => 'Name is required!',
    ];
  }
}
