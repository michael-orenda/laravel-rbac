<?php

namespace MichaelOrenda\Rbac\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use MichaelOrenda\Rbac\Models\Role;
use MichaelOrenda\Rbac\Models\Permission;

/**
 * ModelRoleController
 *
 * Endpoints to assign/revoke roles and permissions to arbitrary models (polymorphic)
 */
class ModelRoleController extends Controller
{
    /**
     * List roles for a model
     * GET /rbac/models/{type}/{id}/roles
     */
    public function listRoles($type, $id)
    {
        $query = DB::table('model_has_roles')
            ->where('model_type', $type)
            ->where('model_id', $id)
            ->pluck('role_id');

        $roles = Role::whereIn('id', $query)->get();

        return response()->json($roles);
    }

    /**
     * Assign role to a model
     * POST /rbac/models/{type}/{id}/roles
     * body: { role: id|slug }
     */
    public function assignRole(Request $request, $type, $id)
    {
        $v = Validator::make($request->all(), [
            'role' => 'required'
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $roleInput = $request->input('role');

        $role = is_numeric($roleInput) ? Role::find($roleInput) : Role::where('slug', $roleInput)->first();

        if (! $role) {
            return response()->json(['error' => 'role_not_found'], 404);
        }

        DB::table('model_has_roles')->insertOrIgnore([
            'model_id' => $id,
            'model_type' => $type,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'assigned']);
    }

    /**
     * Revoke role from model
     * DELETE /rbac/models/{type}/{id}/roles
     * body: { role: id|slug }
     */
    public function revokeRole(Request $request, $type, $id)
    {
        $v = Validator::make($request->all(), [
            'role' => 'required'
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $roleInput = $request->input('role');
        $role = is_numeric($roleInput) ? Role::find($roleInput) : Role::where('slug', $roleInput)->first();

        if (! $role) {
            return response()->json(['error' => 'role_not_found'], 404);
        }

        DB::table('model_has_roles')
            ->where('model_type', $type)
            ->where('model_id', $id)
            ->where('role_id', $role->id)
            ->delete();

        return response()->json(['message' => 'revoked']);
    }

    /**
     * Assign permission directly to model
     * POST /rbac/models/{type}/{id}/permissions
     */
    public function assignPermission(Request $request, $type, $id)
    {
        $v = Validator::make($request->all(), [
            'permission' => 'required'
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $permInput = $request->input('permission');
        $perm = is_numeric($permInput) ? Permission::find($permInput) : Permission::where('slug', $permInput)->first();

        if (! $perm) {
            return response()->json(['error' => 'permission_not_found'], 404);
        }

        DB::table('model_has_permissions')->insertOrIgnore([
            'model_id' => $id,
            'model_type' => $type,
            'permission_id' => $perm->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'assigned']);
    }

    /**
     * Revoke permission from model
     */
    public function revokePermission(Request $request, $type, $id)
    {
        $v = Validator::make($request->all(), [
            'permission' => 'required'
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $permInput = $request->input('permission');
        $perm = is_numeric($permInput) ? Permission::find($permInput) : Permission::where('slug', $permInput)->first();

        if (! $perm) {
            return response()->json(['error' => 'permission_not_found'], 404);
        }

        DB::table('model_has_permissions')
            ->where('model_type', $type)
            ->where('model_id', $id)
            ->where('permission_id', $perm->id)
            ->delete();

        return response()->json(['message' => 'revoked']);
    }
}
