@extends('layouts.admin')
@section('title', 'Editar Forma de Pago')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <i class="fas fa-edit text-info mr-2"></i> Editar Forma de Pago: {{ $paymentMethod->name }}
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment_methods.index') }}">Formas de Pago</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar: {{ $paymentMethod->id }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1 grid-margin stretch-card">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="card-title text-dark">Modificar Parámetros de Cobro</h4>
                    <p class="card-description text-muted">
                        Actualice las condiciones y detalles de esta forma de pago.
                    </p>

                    <form action="{{ route('payment_methods.update', $paymentMethod) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- NOMBRE -->
                            <div class="col-md-8 form-group">
                                <label for="name" class="font-weight-bold text-dark">
                                    Nombre de la Forma de Pago <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-tag text-primary"></i></span>
                                    </div>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $paymentMethod->name) }}" required>
                                </div>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- CÓDIGO INTERNO -->
                            <div class="col-md-4 form-group">
                                <label for="code" class="font-weight-bold text-dark">
                                    Código Interno
                                </label>
                                <input type="text" name="code" id="code" 
                                       class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $paymentMethod->code) }}">
                                @error('code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- CONDICIÓN DE PAGO (CONTADO / CRÉDITO / AMBOS) -->
                            <div class="col-md-6 form-group">
                                <label for="payment_condition" class="font-weight-bold text-dark">
                                    Método / Condición de Pago <span class="text-danger">*</span>
                                </label>
                                <select name="payment_condition" id="payment_condition" 
                                        class="form-control @error('payment_condition') is-invalid @enderror" required>
                                    <option value="CONTADO" {{ old('payment_condition', $paymentMethod->payment_condition) == 'CONTADO' ? 'selected' : '' }}>
                                        💵 PAGO AL CONTADO (Cobro inmediato)
                                    </option>
                                    <option value="CREDITO" {{ old('payment_condition', $paymentMethod->payment_condition) == 'CREDITO' ? 'selected' : '' }}>
                                        📅 PAGO A CRÉDITO (Financiamiento a plazos / cuotas)
                                    </option>
                                    <option value="AMBOS" {{ old('payment_condition', $paymentMethod->payment_condition) == 'AMBOS' ? 'selected' : '' }}>
                                        🔄 AMBOS (Aplica para Contado y Cuotas de Crédito)
                                    </option>
                                </select>
                            </div>

                            <!-- ESTADO Y ORDEN -->
                            <div class="col-md-3 form-group">
                                <label for="status" class="font-weight-bold text-dark">Estado</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="ACTIVO" {{ old('status', $paymentMethod->status) == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                                    <option value="INACTIVO" {{ old('status', $paymentMethod->status) == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="sort_order" class="font-weight-bold text-dark">Orden de Visualización</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $paymentMethod->sort_order) }}" min="0">
                            </div>

                            <!-- ICONO Y COLOR -->
                            <div class="col-md-6 form-group">
                                <label for="icon" class="font-weight-bold text-dark">Ícono Distintivo</label>
                                <select name="icon" id="icon" class="form-control">
                                    <option value="fas fa-money-bill-wave" {{ old('icon', $paymentMethod->icon) == 'fas fa-money-bill-wave' ? 'selected' : '' }}>💵 Billete / Efectivo (fas fa-money-bill-wave)</option>
                                    <option value="fas fa-qrcode" {{ old('icon', $paymentMethod->icon) == 'fas fa-qrcode' ? 'selected' : '' }}>📱 Código QR (fas fa-qrcode)</option>
                                    <option value="fas fa-university" {{ old('icon', $paymentMethod->icon) == 'fas fa-university' ? 'selected' : '' }}>🏦 Banco / Transferencia (fas fa-university)</option>
                                    <option value="fas fa-credit-card" {{ old('icon', $paymentMethod->icon) == 'fas fa-credit-card' ? 'selected' : '' }}>💳 Tarjeta de Débito (fas fa-credit-card)</option>
                                    <option value="far fa-credit-card" {{ old('icon', $paymentMethod->icon) == 'far fa-credit-card' ? 'selected' : '' }}>💳 Tarjeta de Crédito (far fa-credit-card)</option>
                                    <option value="fas fa-hand-holding-usd" {{ old('icon', $paymentMethod->icon) == 'fas fa-hand-holding-usd' ? 'selected' : '' }}>🤝 Crédito Directo (fas fa-hand-holding-usd)</option>
                                    <option value="fas fa-mobile-alt" {{ old('icon', $paymentMethod->icon) == 'fas fa-mobile-alt' ? 'selected' : '' }}>📲 Pago Móvil / Billetera (fas fa-mobile-alt)</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="color" class="font-weight-bold text-dark">Color del Ícono / Botón</label>
                                <input type="color" name="color" id="color" class="form-control form-control-color w-100" value="{{ old('color', $paymentMethod->color) }}" style="height: 42px;">
                            </div>

                            <!-- REQUIERE REFERENCIA -->
                            <div class="col-md-12 form-group">
                                <div class="form-check form-check-flat form-check-primary bg-light p-3 rounded border">
                                    <label class="form-check-label font-weight-bold text-dark">
                                        <input type="checkbox" name="requires_reference" value="1" class="form-check-input" {{ old('requires_reference', $paymentMethod->requires_reference) ? 'checked' : '' }}>
                                        <i class="fas fa-receipt text-info mr-1"></i> Requiere ingresar N° de Comprobante / Voucher / Transacción
                                    </label>
                                </div>
                            </div>

                            <!-- DESCRIPCIÓN -->
                            <div class="col-md-6 form-group">
                                <label for="description" class="font-weight-bold text-dark">Descripción General</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $paymentMethod->description) }}</textarea>
                            </div>

                            <!-- INSTRUCCIONES AL CAJERO -->
                            <div class="col-md-6 form-group">
                                <label for="instructions" class="font-weight-bold text-dark">Instrucciones para el Cajero</label>
                                <textarea name="instructions" id="instructions" class="form-control" rows="3">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                            </div>
                        </div>

                        <hr class="mt-4 mb-3">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payment_methods.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Cancelar y Volver
                            </a>
                            <button type="submit" class="btn btn-primary font-weight-bold">
                                <i class="fas fa-sync-alt mr-1"></i> Actualizar Forma de Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
