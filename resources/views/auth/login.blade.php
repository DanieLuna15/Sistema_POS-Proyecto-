@extends('layouts.login')
@section('content')
    <form class="pt-3" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <div class="input-group">

                <span class="input-group-text bg-transparent">
                    <i class="fa fa-user text-primary"></i>
                </span>

                <input id="email" type="email" name="email" class="form-control form-control-lg border-left-0 @error('email') is-invalid @enderror" id="email" placeholder="Ingrese su Correo" required>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="input-group">
                    <span class="input-group-text bg-transparent">
                        <i class="fa fa-lock text-primary"></i>
                    </span>
                <input id="password" type="password" name="password" class="form-control form-control-lg border-left-0 @error('password') is-invalid @enderror" id="password" placeholder="Ingrese su Contraseña" required>
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

        <div class="my-2 d-flex justify-content-between align-items-center">
        <div class="form-check">
            <label class="form-check-label text-muted">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="form-check-input">
                Mantener la Sesión
            </label>
        </div>
        <a class="btn btn-link" href="{{ route('password.request') }}">
            ¿Olvidaste tu contraseña?
        </a>
        </div>
        <div class="my-3">
            <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"type="submit">INICIAR SESIÓN</button>
        </div>
        <!--<div class="text-center mt-4 font-weight-light">
        Dont have an account? <a href="register-2.html" class="text-primary">Create</a>
        </div>-->
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
    </script>
@endsection


