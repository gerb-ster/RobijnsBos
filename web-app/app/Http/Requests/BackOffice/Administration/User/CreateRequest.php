<?php

namespace App\Http\Requests\BackOffice\Administration\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\BackOffice\Administration\User
 */
class CreateRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|min:3|max:50',
      'email' => 'required|email:rfc,dns',
      'password' => 'nullable|string|min:3|max:64',
      'role_id' => 'required|numeric',
      'admin' => 'nullable|boolean'
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
