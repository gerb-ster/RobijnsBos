<?php

namespace App\Http\Requests\BackOffice\Vegetation;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\BackOffice\Vegetation
 */
class CreateRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'label' => 'nullable|string|min:1|max:256',
      'location' => 'required|array',
      'group_id' => 'required|exists:groups,id',
      'specie_id' => 'required|exists:species,id',
      'placed' => 'required|string|min:1|max:32',
      'amount' => 'required|numeric|min:0',
      'remarks' => 'nullable|string|min:1|max:1024',
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
