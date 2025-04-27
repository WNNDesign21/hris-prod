<?php

namespace App\Http\Controllers\Superuser;

use Exception;
use Throwable;
use App\Models\User;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::whereNot('name', 'super user')->get();
        $organisasis = Organisasi::all();
        $dataPage = [
            'pageTitle' => "Superuser - User",
            'page' => 'superuser-user',
            'roles' => $roles,
            'organisasis' => $organisasis,
        ];
        return view('pages.superuser.user.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'organisasis.nama',
        );

        $totalData = User::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = (!empty($request->input('order.0.column'))) ? $columns[$request->input('order.0.column')] : $columns[0];
        $dir = (!empty($request->input('order.0.dir'))) ? $request->input('order.0.dir') : "DESC";

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $users = User::getData($dataFilter, $settings);
        $totalFiltered = User::countData($dataFilter);

        $dataTable = [];

        if (!empty($users)) {
            foreach ($users as $data) {
                $roles = $data->roles->pluck('name')->toArray();
                $formattedRoles = array_map(function ($role) {
                    return '<span class="badge badge-primary">' . strtoupper($role) . '</span>';
                }, $roles);

                if (!$data->roles->pluck('name')->contains('super user')) {
                    $formattedActions = '<div class="btn-group">
                        <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id.'" data-username="'.$data->username.'" data-email="'.$data->email.'" data-organisasi="'.$data->organisasi_id.'" data-roles="'.implode(',', $roles).'"><i class="fas fa-edit"></i></button>
                        <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                } else {
                    $formattedActions = '';
                }

                $nestedData['username'] = $data->username;
                $nestedData['email'] = $data->email;
                $nestedData['organisasi'] = $data->organisasi;
                $nestedData['role'] = $formattedRoles;
                $nestedData['aksi'] = $formattedActions;

                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "statusFilter" => !empty($dataFilter['statusFilter']) ? $dataFilter['statusFilter'] : "Kosong",
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organisasi' => 'required|exists:organisasis,id_organisasi',
            'roles' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $allowedRolesCombination = ['member', 'admin-dept'];
            $roles = $request->roles;

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'organisasi_id' => $request->organisasi,
            ]);

            foreach ($roles as $role) {
                foreach ($roles as $otherRole) {
                    if ($role !== $otherRole &&
                        !(in_array($role, $allowedRolesCombination) && in_array($otherRole, $allowedRolesCombination))) {
                        DB::rollBack();
                        return response()->json(['message' => 'Kombinasi role dilarang, hanya admin-dept + atasan/member yang bisa memiliki 2 role'], 400);
                    }
                }
            }

            $user->assignRole($roles);

            activity('store_user')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log('User created: ' . $user->username);
            DB::commit();
            return response()->json(['message' => 'User created successfully'], 201);
        } catch (Exception $e) {
            DB::rollBack();
            activity('store_user')
                ->causedBy(auth()->user())
                ->log('Failed to create user: ' . $e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();

            activity('delete_user')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log('User deleted: ' . $user->username);
            DB::commit();
            return response()->json(['message' => 'User deleted!'], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            activity('delete_user')
                ->causedBy(auth()->user())
                ->log('Failed to delete user: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            activity('delete_user')
                ->causedBy(auth()->user())
                ->log('Failed to delete user: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
