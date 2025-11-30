<?php

namespace MichaelOrenda\Rbac\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevokePermissionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'permission' => 'required'
        ];
    }
}
