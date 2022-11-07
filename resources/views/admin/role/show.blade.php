@extends('layouts.admin')
@section('title','Información sobre el rol')
@section('styles')
@endsection
@section('create')

@endsection
@section('options')

@endsection
@section('preference')

@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            {{$role->name}}
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$role->name}}</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="border-bottom text-center pb-4">
                                <h3>{{$role->name}}</h3>
                                <div class="d-flex justify-content-between">
                                </div>
                            </div>
                            <div class="border-bottom py-4">
                                <div class="list-group">
                                    <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">
                                        Permisos
                                    </a>
                                    <a type="button" class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">Usuarios</a>
                                    {{--  <button type="button" class="list-group-item list-group-item-action">Registrar
                                        producto</button>  --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 pl-lg-5">

                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">

                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Permisos asignados al rol</h4>
                                        </div>
                                    </div>
                                    <div class="profile-feed">
                                        <div class="d-flex align-items-start profile-feed-item">

                                            <div class="table-responsive">
                                                <table id="order-listing" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Nombre</th>
                                                            <th>Slug</th>
                                                            <th>Descripción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($role->permissions as $permission)
                                                        <tr>
                                                            <th scope="row">{{$permission->id}}</th>
                                                            <td>{{$permission->name}}</td>
                                                            <td>{{$permission->slug}}</td>
                                                            <td>{{$permission->description}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Usuarios con el rol</h4>
                                        </div>
                                    </div>
                                    <div class="profile-feed">
                                        <div class="d-flex align-items-start profile-feed-item">
                                            <div class="table-responsive">
                                                <table id="order-listing1" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Nombre</th>
                                                            <th>Correo Electrónico</th>
                                                            <th>Estado</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($role->users as $user)
                                                            <tr>
                                                                <th scope="row" style="width: 5%;">{{$user->id}}</th>
                                                                <td style="width: 10%;">
                                                                    <a href="{{route('users.show',$user)}}">{{$user->name}}</a>
                                                                </td>
                                                                <td style="width: 15%;">{{$user->email}}</td>
                                                                <td style="width: 10%;">
                                                                    @if ($user->status=='HABILITADO')
                                                                        <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.users', $user)}}">
                                                                            {{$user->status}} <i class="fas fa-check"></i>
                                                                        </a>
                                                                    @else
                                                                        <a class="jsgrid-button btn btn-danger btn-sm btn-block" href="{{route('change.status.users', $user)}}">
                                                                            {{$user->status}} <i class="fas fa-times"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                <td style="width: 20%;" align="center">
                                                                    {!! Form::open(['route'=>['users.destroy',$user], 'method'=>'DELETE']) !!}
                                                                        <a class="btn btn-outline-warning" href="{{route('users.edit', $user)}}" title="Editar">
                                                                            <i class="far fa-edit"></i>
                                                                        </a>
                                                                        <a class="btn btn-outline-info" href="{{route('users.show',$user)}}" title="Ver Detalle">
                                                                            <i class="far fa-eye"></i>
                                                                        </a>
                                                                    {!! Form::close() !!}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <a href="{{route('roles.index')}}" class="btn btn-primary float-right">Regresar</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/profile-demo.js') !!}
{!! Html::script('melody/js/data-table.js') !!}
@endsection
