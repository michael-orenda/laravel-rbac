<?php

namespace MichaelOrenda\Rbac\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:191',
            'slug' => 'sometimes|required|string|max:191|unique:roles,slug,' . $this->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:roles,id',
        ];
    }
}
