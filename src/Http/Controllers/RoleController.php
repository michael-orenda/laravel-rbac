<?php

namespace MichaelOrenda\Rbac\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MichaelOrenda\ApiResponse\Traits\ApiResponseTrait;
use MichaelOrenda\Rbac\Models\Role;
use MichaelOrenda\Rbac\Models\Permission;

/**
 * RoleController
 *
 * Responsibilities:
 * - CRUD for roles
 * - attach / detach permissions to roles
 * - search / list endpoints
 * - list assigned models (which models have this role)
 */
class RoleController extends Controller
{
    use ApiResponseTrait; 

    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('root') && $request->boolean('root')) {
            $query->root();
        }

        $perPage = $request->integer('per_page', 25);

        return $this->success("Roles fetched successfully", $query->paginate($perPage));

    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return $this->success("Role details", $role);

    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:roles,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:roles,id',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $role = Role::create($v->validated());

        return $this->success("Role created successfully", $role, 201);

    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $v = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:191',
            'slug' => 'sometimes|required|string|max:191|unique:roles,slug,'.$role->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:roles,id',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $role->update($v->validated());

        return $this->success("Role updated successfully", $role);

    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        DB::transaction(function () use ($role) {
            // detach permissions
            $role->permissions()->detach();
            // delete role assignments
            DB::table('model_has_roles')->where('role_id', $role->id)->delete();
            $role->delete();
        });

        return $this->success("Role deleted successfully");

    }

    /**
     * Attach permissions to role
     *
     * Accepts:
     * - permissions: array of permission ids or slugs
     */
    public function attachPermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $v = Validator::make($request->all(), [
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $permissions = $request->input('permissions');

        $ids = collect($permissions)->map(function ($p) {
            if (is_numeric($p)) {
                return (int)$p;
            }
            $perm = Permission::where('slug', $p)->first();
            return $perm ? $perm->id : null;
        })->filter()->unique()->values()->toArray();

        $role->permissions()->syncWithoutDetaching($ids);

        return $this->success("Permissions attached successfully", $role->permissions()->get());

    }

    public function detachPermission(Request $request, $id, $permissionId)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach($permissionId);
        return $this->success("Permission detached successfully");

    }

    /**
     * List models that have this role
     */
    public function assignedModels($id)
    {
        $role = Role::findOrFail($id);

        // Return a simple listing grouped by model_type
        $rows = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->get()
            ->groupBy('model_type')
            ->map(function ($items) {
                return $items->map(function ($it) {
                    return ['model_id' => $it->model_id];
                })->values();
            });

        return $this->success("Assigned models fetched successfully", $rows);
    }
}
