<?php

namespace App\Http\Requests\BackOffice\Vegetation;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\BackOffice\Vegetation
 */
class UpdateRequest extends FormRequest
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
      'status_id' => 'required|exists:vegetation_status,id',
      'placed' => 'required|string|min:1|max:32',
      'amount' => 'required|numeric|min:0',
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
