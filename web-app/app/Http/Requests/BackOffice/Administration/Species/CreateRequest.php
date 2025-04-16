<?php

namespace App\Http\Requests\BackOffice\Administration\Species;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\BackOffice\Administration\Species
 */
class CreateRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'dutch_name' => 'required|string|min:1|max:512',
      'latin_name' => 'required|string|min:1|max:512',
      'blossom_month' => 'required|array',
      'height' => 'required|string|min:1|max:32'
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
