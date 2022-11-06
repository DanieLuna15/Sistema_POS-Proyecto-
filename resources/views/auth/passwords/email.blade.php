@extends('layouts.reset')

@section('content')

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <div class="input-group">

                                <span class="input-group-text bg-transparent">
                                    <i class="fa fa-user text-primary"></i>
                                </span>

                                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" id="email" placeholder="Ingrese su Correo"  autofocus required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-12">
                            <div class="col-md-12 offset-md-12" align="center">
                                <button type="submit" class="btn btn-block btn-primary">
                                    Enviar enlace de restablecimiento
                                </button>
                            </div>
                        </div>
                    </form>

@endsection
