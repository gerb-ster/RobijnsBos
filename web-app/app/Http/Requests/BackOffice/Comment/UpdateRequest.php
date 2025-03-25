<?php

namespace App\Http\Requests\BackOffice\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\BackOffice\Comment
 */
class UpdateRequest extends FormRequest
{
  /**
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'status_id' => 'required|exists:comment_status,id',
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
      'name.required' => 'Name is required!',
    ];
  }
}
