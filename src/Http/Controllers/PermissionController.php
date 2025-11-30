<?php

namespace MichaelOrenda\Rbac\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MichaelOrenda\Rbac\Models\Permission;

/**
 * PermissionController
 *
 * CRUD for permissions, search, and listing roles that have a permission
 */
class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('root') && $request->boolean('root')) {
            $query->root();
        }

        $perPage = $request->integer('per_page', 25);

        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        return response()->json($permission);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:permissions,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:permissions,id',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $permission = Permission::create($v->validated());

        return response()->json($permission, 201);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $v = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:191',
            'slug' => 'sometimes|required|string|max:191|unique:permissions,slug,'.$permission->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:permissions,id',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $permission->update($v->validated());

        return response()->json($permission);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        DB::transaction(function () use ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        });

        return response()->json(['message' => 'deleted']);
    }

    /**
     * List models that have this permission
     */
    public function assignedModels($id)
    {
        $permission = Permission::findOrFail($id);

        $rows = DB::table('model_has_permissions')
            ->where('permission_id', $permission->id)
            ->get()
            ->groupBy('model_type')
            ->map(function ($items) {
                return $items->map(function ($it) {
                    return ['model_id' => $it->model_id];
                })->values();
            });

        return response()->json($rows);
    }
}
