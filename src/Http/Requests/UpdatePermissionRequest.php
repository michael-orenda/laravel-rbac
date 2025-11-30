<?php

namespace MichaelOrenda\Rbac\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:191',
            'slug' => 'sometimes|required|string|max:191|unique:permissions,slug,' . $this->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:permissions,id',
        ];
    }
}
