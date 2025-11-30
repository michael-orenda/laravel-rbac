<?php

namespace MichaelOrenda\Rbac\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'permission' => 'required'
        ];
    }
}
