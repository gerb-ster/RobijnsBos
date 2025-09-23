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
      'location' => 'required|array',
      'specie_id' => 'required|exists:species,id',
      'status_id' => 'required|exists:vegetation_status,id',
      'placed' => 'required|numeric|min:0|max:2200',
      'remarks' => 'nullable|string|min:1|max:1024',
      'show_text_on_map' => 'nullable|boolean',
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
