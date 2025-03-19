<?php

namespace App\Http\Requests\BackOffice\Administration\Species;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\BackOffice\Administration\Species
 */
class UpdateRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|min:1|max:64'
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
