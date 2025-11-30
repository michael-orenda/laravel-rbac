<?php

namespace MichaelOrenda\Rbac\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MichaelOrenda\ApiResponse\Traits\ApiResponseTrait;
use MichaelOrenda\Rbac\Models\Permission;

/**
 * ModelPermissionController
 *
 * Responsibilities:
 * - List permissions directly assigned to a model
 * - Assign permission to a model (polymorphic)
 * - Revoke permission from a model (polymorphic)
 */
class ModelPermissionController extends Controller
{
    use ApiResponseTrait;
    /**
     * List permissions of a polymorphic model
     * GET /rbac/models/{type}/{id}/permissions
     */
    public function listPermissions($type, $id)
    {
        $permissionIds = DB::table('model_has_permissions')
            ->where('model_type', $type)
            ->where('model_id', $id)
            ->pluck('permission_id');

        $permissions = Permission::whereIn('id', $permissionIds)->get();

        return $this->success($permissions);
    }

    /**
     * Assign a direct permission to a model
     * POST /rbac/models/{type}/{id}/permissions
     */
    public function assignPermission(Request $request, $type, $id)
    {
        $v = Validator::make($request->all(), [
            'permission' => 'required'
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $input = $request->permission;

        $permission = is_numeric($input)
            ? Permission::find($input)
            : Permission::where('slug', $input)->first();

        if (!$permission) {
            return $this->error('permission_not_found', 404);
        }

        DB::table('model_has_permissions')->insertOrIgnore([
            'model_id' => $id,
            'model_type' => $type,
            'permission_id' => $permission->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'assigned']);
    }

    /**
     * Revoke a direct permission
     * DELETE /rbac/models/{type}/{id}/permissions
     */
    public function revokePermission(Request $request, $type, $id)
    {
        $v = Validator::make($request->all(), [
            'permission' => 'required'
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $input = $request->permission;

        $permission = is_numeric($input)
            ? Permission::find($input)
            : Permission::where('slug', $input)->first();

        if (!$permission) {
            return $this->error('permission_not_found', 404);
        }

        DB::table('model_has_permissions')
            ->where('model_id', $id)
            ->where('model_type', $type)
            ->where('permission_id', $permission->id)
            ->delete();

        return $this->success(null, 'revoked');
    }
}
