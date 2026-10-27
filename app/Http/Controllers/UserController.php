<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Company;
use App\Branch;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

use Illuminate\Support\Facades\DB;

//Para sweet alert en Usuarios
use RealRashid\SweetAlert\Facades\Alert;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:users.create')->only(['create','store']);
        $this->middleware('can:users.index')->only(['index']);
        $this->middleware('can:users.edit')->only(['edit','update']);
        $this->middleware('can:users.show')->only(['show']);
        $this->middleware('can:users.destroy')->only(['destroy']);

        $this->middleware('can:change.status.users')->only(['change_status']);
    }

    public function index()
    {
        $companyId = Company::currentId();
        $users = User::with('roles')
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->orderBy('id', 'DESC')
            ->get();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::get();
        return view('admin.user.create', compact('roles'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->except(['roles', 'password']);
        $data['password'] = Hash::make($request->password);
        $data['company_id'] = Company::currentId();
        $data['branch_id'] = $data['branch_id'] ?? optional(Branch::current())->id;
        $user = User::create($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->get('roles'));
        }

        Alert::toast('Usuario registrado con éxito.', 'success');
        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        $total_sales = $user->sales()->where('status', 'CONFIRMADO')->sum('total');
        $total_amount_sold = $user->purchases()->where('status', 'CONFIRMADO')->sum('total');
        return view('admin.user.show', compact('user', 'total_sales', 'total_amount_sold'));
    }

    public function edit(User $user)
    {
        $roles = Role::get();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(UpdateRequest $request, User $user)
    {
        $data = $request->except(['roles', 'password']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->get('roles'));
        }

        Alert::toast('Usuario modificado con éxito.', 'success');
        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back();
    }

    public function change_status(User $user)
    {
        if($user->status == 'HABILITADO'){
            $user->update([ 'status' => 'DESHABILITADO']);
            Alert::toast('Usuario Deshabilitado con éxito.', 'success');
            return redirect()->back();
        }else{
            $user->update([ 'status' =>'HABILITADO']);
            Alert::toast('Usuario Habilitado con éxito.', 'success');
            return redirect()->back();
        }
    }
}
