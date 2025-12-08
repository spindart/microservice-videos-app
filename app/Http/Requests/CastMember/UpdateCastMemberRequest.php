<?php

namespace App\Http\Requests\CastMember;

use Core\Domain\Enum\CastMemberType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCastMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'type' => ['required', new Enum(CastMemberType::class)],
            'is_active' => 'nullable|boolean',
        ];
    }
}
