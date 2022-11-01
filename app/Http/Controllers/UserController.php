<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Support\Facades\Hash;

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
        $users = User::get();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles= Role::get();
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());
        //Para encriptar la contraseña antes de guardar
        $user -> update(['password'=> Hash::make($request->password)]);
        $user->roles()->sync($request->get('roles'));
        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        return view('admin.user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles= Role::get();
        return view('admin.user.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        //$user -> update(['password'=> Hash::make($request->password)]);
        $user->roles()->sync($request->get('roles'));
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
