@extends('layouts.admin')
@section('title', 'Nueva Forma de Pago')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <i class="fas fa-plus-circle text-primary mr-2"></i> Registrar Nueva Forma de Pago
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment_methods.index') }}">Formas de Pago</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nueva</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1 grid-margin stretch-card">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="card-title text-dark">Información de la Forma de Pago</h4>
                    <p class="card-description text-muted">
                        Defina el nombre, la condición de pago (Contado / Crédito) y las directrices de cobro para el cajero.
                    </p>

                    <form action="{{ route('payment_methods.store') }}" method="POST">
                        @csrf

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
                                           placeholder="Ej: Código QR Simple, Tarjeta de Débito, Transferencia BNB..." 
                                           value="{{ old('name') }}" required>
                                </div>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- CÓDIGO INTERNO -->
                            <div class="col-md-4 form-group">
                                <label for="code" class="font-weight-bold text-dark">
                                    Código Interno <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="text" name="code" id="code" 
                                       class="form-control text-uppercase @error('code') is-invalid @enderror" 
                                       placeholder="Ej: QR_SIMPLE, BCP_TRANS" 
                                       value="{{ old('code') }}">
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
                                    <option value="CONTADO" {{ old('payment_condition') == 'CONTADO' ? 'selected' : '' }}>
                                        💵 PAGO AL CONTADO (Cobro inmediato)
                                    </option>
                                    <option value="CREDITO" {{ old('payment_condition') == 'CREDITO' ? 'selected' : '' }}>
                                        📅 PAGO A CRÉDITO (Financiamiento a plazos / cuotas)
                                    </option>
                                    <option value="AMBOS" {{ old('payment_condition') == 'AMBOS' ? 'selected' : '' }}>
                                        🔄 AMBOS (Aplica para Contado y Cuotas de Crédito)
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    Determina en qué módulos de venta estará disponible esta opción.
                                </small>
                            </div>

                            <!-- ESTADO Y ORDEN -->
                            <div class="col-md-3 form-group">
                                <label for="status" class="font-weight-bold text-dark">Estado</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="ACTIVO" {{ old('status') == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                                    <option value="INACTIVO" {{ old('status') == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="sort_order" class="font-weight-bold text-dark">Orden de Visualización</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 1) }}" min="0">
                            </div>

                            <!-- ICONO Y COLOR -->
                            <div class="col-md-6 form-group">
                                <label for="icon" class="font-weight-bold text-dark">Ícono Distintivo</label>
                                <select name="icon" id="icon" class="form-control">
                                    <option value="fas fa-money-bill-wave">💵 Billete / Efectivo (fas fa-money-bill-wave)</option>
                                    <option value="fas fa-qrcode">📱 Código QR (fas fa-qrcode)</option>
                                    <option value="fas fa-university">🏦 Banco / Transferencia (fas fa-university)</option>
                                    <option value="fas fa-credit-card">💳 Tarjeta de Débito (fas fa-credit-card)</option>
                                    <option value="far fa-credit-card">💳 Tarjeta de Crédito (far fa-credit-card)</option>
                                    <option value="fas fa-hand-holding-usd">🤝 Crédito Directo (fas fa-hand-holding-usd)</option>
                                    <option value="fas fa-mobile-alt">📲 Pago Móvil / Billetera (fas fa-mobile-alt)</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="color" class="font-weight-bold text-dark">Color del Ícono / Botón</label>
                                <input type="color" name="color" id="color" class="form-control form-control-color w-100" value="{{ old('color', '#2563eb') }}" style="height: 42px;">
                            </div>

                            <!-- REQUIERE REFERENCIA -->
                            <div class="col-md-12 form-group">
                                <div class="form-check form-check-flat form-check-primary bg-light p-3 rounded border">
                                    <label class="form-check-label font-weight-bold text-dark">
                                        <input type="checkbox" name="requires_reference" value="1" class="form-check-input" {{ old('requires_reference') ? 'checked' : '' }}>
                                        <i class="fas fa-receipt text-info mr-1"></i> Requiere ingresar N° de Comprobante / Voucher / Transacción
                                    </label>
                                    <small class="text-muted d-block ml-4">
                                        Active esta opción si el cajero debe obligatoriamente solicitar y registrar el número de operación bancaria o lote del POS.
                                    </small>
                                </div>
                            </div>

                            <!-- DESCRIPCIÓN -->
                            <div class="col-md-6 form-group">
                                <label for="description" class="font-weight-bold text-dark">Descripción General</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Breve descripción informativa...">{{ old('description') }}</textarea>
                            </div>

                            <!-- INSTRUCCIONES AL CAJERO -->
                            <div class="col-md-6 form-group">
                                <label for="instructions" class="font-weight-bold text-dark">Instrucciones para el Cajero</label>
                                <textarea name="instructions" id="instructions" class="form-control" rows="3" placeholder="Instrucciones al momento de cobrar...">{{ old('instructions') }}</textarea>
                            </div>
                        </div>

                        <hr class="mt-4 mb-3">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payment_methods.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Cancelar y Volver
                            </a>
                            <button type="submit" class="btn btn-primary font-weight-bold">
                                <i class="fas fa-save mr-1"></i> Guardar Forma de Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
