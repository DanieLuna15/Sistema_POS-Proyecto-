@extends('layouts.reset')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group row">
            <label for="email" class="col-md-12 col-form-label text-md-left">'Dirección de correo electrónico'</label>

            <div class="col-md-12">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="password">Nueva Contraseña</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent ">
                    <i class="fa fa-lock text-primary"></i>
                </span>
                <input id="password" type="password" name="password"  class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Ingrese su nueva contraseña" required autocomplete="new-password">
                <div class="input-group-append" onclick="Vista();">
                    <button class="btn btn-sm btn-primary" title="Ver Contraseña" id="ver" type="button">
                        <i class="far fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" title="Ocultar Contraseña" style="display:none;" id="ocultar" type="button">
                        <i class="far fa-eye-slash"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="password">Confirmar Contraseña</label>
            <div class="input-group">
                    <span class="input-group-text bg-transparent border-right-0">
                        <i class="fa fa-lock text-primary"></i>
                    </span>

                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirme su nueva Contraseña" required autocomplete="new-password">
                <div class="input-group-append" onclick="VistaC();">
                    <button class="btn btn-sm btn-primary" title="Ver Contraseña" id="ver" type="button">
                        <i class="far fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" title="Ocultar Contraseña" style="display:none;" id="ocultar" type="button">
                        <i class="far fa-eye-slash"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group row mb-12">
            <div class="col-md-12 offset-md-12" align="center">
                <button type="submit" class="btn btn-block btn-primary">
                    Restablecer Contraseña
                </button>
            </div>
        </div>
    </form>
    <script>
        function Vista(){
            let password=document.getElementById('password');
            let ver=document.getElementById('ver');
            let ocultar=document.getElementById('ocultar');
            if(password.type=='password'){
                password.type='text';
                ver.style.display='none';
                ocultar.style.display='block';
            } else {
                password.type='password';
                ver.style.display='block';
                ocultar.style.display='none';
            }
        }
        function VistaC(){
            let password=document.getElementById('password-confirm');
            let ver=document.getElementById('ver');
            let ocultar=document.getElementById('ocultar');
            if(password.type=='password'){
                password.type='text';
                ver.style.display='none';
                ocultar.style.display='block';
            } else {
                password.type='password';
                ver.style.display='block';
                ocultar.style.display='none';
            }
        }
    </script>
@endsection
