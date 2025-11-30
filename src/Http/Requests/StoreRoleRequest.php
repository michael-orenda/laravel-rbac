<?php

namespace MichaelOrenda\Rbac\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:roles,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:roles,id',
        ];
    }
}
