@extends('layouts.admin')
@section('title', 'Configuración de Empresa, Rubro SaaS y Sucursales')

@section('styles')
<style>
    .rubro-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        height: 100%;
        background: #fff;
    }
    .rubro-card:hover {
        border-color: #6366f1;
        background: #f8fafc;
    }
    .rubro-card.active {
        border-color: #4f46e5;
        background: #eef2ff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    }
    .switch-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>
@endsection

@section('content')
@php
    $isSuperAdmin = (Auth::check() && (Auth::user()->is_superadmin || Auth::id() === 1));
    $currentRubro = $businessTypes[$company->business_type ?? 'GENERAL'] ?? $businessTypes['GENERAL'];
@endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-building text-primary mr-2"></i> Configuración de Mi Negocio y Sucursales
            </h3>
            <small class="text-muted">Personaliza la identidad comercial de tu empresa, logo, NIT, moneda, pie de comprobantes y puntos de venta.</small>
        </div>
        <div class="mt-2 mt-md-0">
            @if($isSuperAdmin)
                <a href="{{ route('saas.tenants.index') }}" class="btn btn-dark btn-sm mr-2 font-weight-bold text-warning">
                    <i class="fas fa-cloud mr-1"></i> Ir al Panel SuperAdmin SaaS
                </a>
            @endif
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newBranchModal">
                <i class="fas fa-plus-circle mr-1"></i> Nueva Sucursal
            </button>
        </div>
    </div>

    <!-- TARJETA INFORMATIVA DEL PLAN Y RUBRO ASIGNADO POR EL SAAS -->
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #fff;">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-5 d-flex align-items-center mb-2 mb-md-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:52px;height:52px;background:rgba(255,255,255,0.12);">
                        <i class="{{ $currentRubro['icon'] }} fa-lg text-warning"></i>
                    </div>
                    <div>
                        <span class="badge badge-warning text-dark font-weight-bold mb-1">RUBRO DESIGNADO POR EL SAAS</span>
                        <h5 class="mb-0 font-weight-bold text-white">{{ $currentRubro['label'] }}</h5>
                        <small style="color:#cbd5e1;">{{ $currentRubro['desc'] }}</small>
                    </div>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="small text-uppercase font-weight-bold" style="color:#94a3b8;">Suscripción y Límites</div>
                    <div class="font-weight-bold">
                        Plan: <span class="text-warning">{{ $company->plan_name ?? 'PYME PRO' }}</span> &bull;
                        Estado: <span class="badge badge-success">{{ $company->status ?? 'ACTIVO' }}</span>
                    </div>
                    <small style="color:#cbd5e1;">
                        Sucursales: {{ $branches->count() }} / {{ $company->max_branches ?? 5 }} máx. &bull;
                        Usuarios máx: {{ $company->max_users ?? 15 }}
                    </small>
                </div>
                <div class="col-md-3 text-md-right">
                    <div class="small font-weight-bold mb-1" style="color:#94a3b8;">Módulos Habilitados</div>
                    <div class="d-flex flex-wrap justify-content-md-end" style="gap:4px;">
                        @if($company->enable_pos) <span class="badge badge-info">POS</span> @endif
                        @if($company->enable_cash_register) <span class="badge badge-info">Caja</span> @endif
                        @if($company->enable_kardex) <span class="badge badge-info">Kardex</span> @endif
                        @if($company->enable_credits) <span class="badge badge-info">Créditos</span> @endif
                        @if($company->enable_quotations) <span class="badge badge-info">Proformas</span> @endif
                        @if($company->enable_lots_expiry) <span class="badge badge-warning text-dark">Lotes/Venc.</span> @endif
                        @if($company->enable_serials_warranties) <span class="badge badge-warning text-dark">Garantías</span> @endif
                        @if($company->enable_services) <span class="badge badge-warning text-dark">Servicios</span> @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: FORMULARIO DE EMPRESA -->
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('company.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @if($isSuperAdmin)
                        <div class="p-3 mb-4 rounded" style="background:#f8fafc; border: 2px dashed #6366f1;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="card-title text-primary mb-0">
                                    <i class="fas fa-user-shield mr-1"></i> Control Exclusivo SuperAdmin: Designar Giro de Negocio y Módulos
                                </h4>
                                <span class="badge badge-primary">Solo visible para ti (SuperAdmin)</span>
                            </div>
                            <p class="text-muted small mb-3">
                                Tu cliente NO puede ver ni modificar esta sección. Solo tú designas qué tipo de negocio es y qué módulos tiene activos.
                            </p>
                            <input type="hidden" name="business_type" id="input_business_type" value="{{ $company->business_type ?? 'GENERAL' }}">

                            <div class="row mb-2">
                                @foreach($businessTypes as $key => $info)
                                <div class="col-md-6 col-xl-3 mb-3">
                                    <div class="rubro-card {{ ($company->business_type ?? 'GENERAL') === $key ? 'active' : '' }}"
                                         onclick="selectBusinessType('{{ $key }}', this)">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="{{ $info['icon'] }} text-primary mr-2" style="font-size: 18px;"></i>
                                            <strong style="font-size: 12.5px;">{{ $info['label'] }}</strong>
                                        </div>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">{{ $info['desc'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <h4 class="card-title text-primary mb-3">
                            <i class="fas fa-building mr-1"></i> 1. Identidad Corporativa y Datos Fiscales (Aparecen en PDFs y Tickets)
                        </h4>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Nombre Comercial del Negocio *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Razón Social / Propietario</label>
                                <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name', $company->legal_name) }}">
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="font-weight-bold">Eslogan / Actividad Económica</label>
                                <input type="text" name="slogan" class="form-control" value="{{ old('slogan', $company->slogan) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">NIT / RUC / Identificación Fiscal</label>
                                <input type="text" name="nit" class="form-control" value="{{ old('nit', $company->nit) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">N° Autorización / Dosificación</label>
                                <input type="text" name="authorization_number" class="form-control" value="{{ old('authorization_number', $company->authorization_number) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Teléfono / WhatsApp</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Dirección Principal</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">Ciudad</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $company->city) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">País</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', $company->country) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Logo de la Empresa (Opcional)</label>
                                <input type="file" name="logo_file" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr>
                        <h4 class="card-title text-primary mb-3">
                            <i class="fas fa-coins mr-1"></i> 3. Parámetros de Moneda, Impuestos e Impresión
                        </h4>

                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">Símbolo Moneda *</label>
                                <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $company->currency_symbol ?? 'Bs.') }}" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">Nombre Moneda (Letras) *</label>
                                <input type="text" name="currency_name" class="form-control" value="{{ old('currency_name', $company->currency_name ?? 'Bolivianos') }}" required>
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="font-weight-bold">Sigla Impuesto</label>
                                <input type="text" name="tax_name" class="form-control" value="{{ old('tax_name', $company->tax_name ?? 'IVA') }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="font-weight-bold">% Impuesto</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ old('tax_rate', $company->tax_rate ?? 13) }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="font-weight-bold">Alerta Stock</label>
                                <input type="number" name="default_stock_alert" class="form-control" value="{{ old('default_stock_alert', $company->default_stock_alert ?? 10) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Pie de Ticket / Mensaje de Agradecimiento</label>
                                <textarea name="receipt_footer" class="form-control" rows="2">{{ old('receipt_footer', $company->receipt_footer) }}</textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Términos de Garantía / Condiciones en Notas y Proformas</label>
                                <textarea name="terms_conditions" class="form-control" rows="2">{{ old('terms_conditions', $company->terms_conditions) }}</textarea>
                            </div>
                        </div>

                        @if($isSuperAdmin)
                        <hr>
                        <h4 class="card-title text-primary mb-3">
                            <i class="fas fa-toggle-on mr-1"></i> 3. Módulos y Capacidades Activas (Exclusivo SuperAdmin)
                        </h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="switch-box">
                                    <div>
                                        <strong>Caja Rápida Táctil (POS)</strong><br>
                                        <small class="text-muted">Venta rápida con lector de código de barras y botones táctiles</small>
                                    </div>
                                    <input type="checkbox" name="enable_pos" id="chk_pos" value="1" {{ ($company->enable_pos ?? true) ? 'checked' : '' }}>
                                </div>
                                <div class="switch-box">
                                    <div>
                                        <strong>Ventas a Crédito y Plan de Cuotas</strong><br>
                                        <small class="text-muted">Cuentas por cobrar, pagos parciales y cronogramas</small>
                                    </div>
                                    <input type="checkbox" name="enable_credits" id="chk_credits" value="1" {{ ($company->enable_credits ?? true) ? 'checked' : '' }}>
                                </div>
                                <div class="switch-box">
                                    <div>
                                        <strong>Cotizaciones y Proformas en PDF</strong><br>
                                        <small class="text-muted">Emitir presupuestos y convertirlos a venta en 1 clic</small>
                                    </div>
                                    <input type="checkbox" name="enable_quotations" id="chk_quotations" value="1" {{ ($company->enable_quotations ?? true) ? 'checked' : '' }}>
                                </div>
                                <div class="switch-box">
                                    <div>
                                        <strong>Apertura, Arqueo de Caja y Gastos Chicos</strong><br>
                                        <small class="text-muted">Control de turnos de cajero, ingresos y egresos operativos</small>
                                    </div>
                                    <input type="checkbox" name="enable_cash_register" id="chk_cash" value="1" {{ ($company->enable_cash_register ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="switch-box">
                                    <div>
                                        <strong>Kardex Auditable y Ajustes / Mermas</strong><br>
                                        <small class="text-muted">Trazabilidad completa de movimientos de stock</small>
                                    </div>
                                    <input type="checkbox" name="enable_kardex" id="chk_kardex" value="1" {{ ($company->enable_kardex ?? true) ? 'checked' : '' }}>
                                </div>
                                <div class="switch-box">
                                    <div>
                                        <strong>Control de Lotes y Fechas de Vencimiento</strong><br>
                                        <small class="text-muted">Ideal para Farmacias, Supermercados, Alimentos e Insumos</small>
                                    </div>
                                    <input type="checkbox" name="enable_lots_expiry" id="chk_lots" value="1" {{ ($company->enable_lots_expiry ?? true) ? 'checked' : '' }}>
                                </div>
                                <div class="switch-box">
                                    <div>
                                        <strong>Garantías por Días y Códigos SKU / Serie</strong><br>
                                        <small class="text-muted">Ideal para Electrónica, Ferretería, Celulares y Repuestos</small>
                                    </div>
                                    <input type="checkbox" name="enable_serials_warranties" id="chk_warranties" value="1" {{ ($company->enable_serials_warranties ?? true) ? 'checked' : '' }}>
                                </div>
                                <div class="switch-box">
                                    <div>
                                        <strong>Venta de Servicios y Precios Mayoristas</strong><br>
                                        <small class="text-muted">Ítems sin descuento de stock físico y precios por volumen</small>
                                    </div>
                                    <input type="checkbox" name="enable_services" id="chk_services" value="1" {{ ($company->enable_services ?? true) ? 'checked' : '' }}>
                                    <input type="hidden" name="enable_wholesale" value="1">
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-1"></i> Guardar Datos de Mi Negocio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: SUCURSALES / PUNTOS DE VENTA -->
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title text-primary mb-0">
                            <i class="fas fa-store-alt mr-1"></i> Sucursales Registradas
                        </h4>
                        <span class="badge badge-info badge-pill">{{ $branches->count() }} Sucursal(es)</span>
                    </div>
                    <p class="text-muted" style="font-size: 12px;">
                        Puedes operar con múltiples sucursales o almacenes y cambiar entre ellas desde la barra superior.
                    </p>

                    @foreach($branches as $b)
                    <div class="border rounded p-3 mb-3 {{ (isset($activeBranch) && $activeBranch->id == $b->id) ? 'border-primary bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge badge-dark mb-1">{{ $b->code ?? 'SUC-'.$b->id }}</span>
                                @if($b->is_main)
                                    <span class="badge badge-warning mb-1">Casa Matriz</span>
                                @endif
                                @if(isset($activeBranch) && $activeBranch->id == $b->id)
                                    <span class="badge badge-success mb-1">En Uso Ahora</span>
                                @endif
                                <h5 class="mb-1 font-weight-bold">{{ $b->name }}</h5>
                                <div class="text-muted" style="font-size: 12px;">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $b->address ?? 'Sin dirección' }} ({{ $b->city }})<br>
                                    <i class="fas fa-phone mr-1"></i> {{ $b->phone ?? 'S/N' }} &bull;
                                    <i class="fas fa-user-tie mr-1"></i> {{ $b->manager_name ?? 'Admin' }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between">
                            @if(!isset($activeBranch) || $activeBranch->id != $b->id)
                            <form action="{{ route('branches.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-exchange-alt"></i> Activar Sucursal
                                </button>
                            </form>
                            @else
                            <span class="text-success font-weight-bold" style="font-size: 12px;">
                                <i class="fas fa-check-circle"></i> Sucursal Activa
                            </span>
                            @endif

                            <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#editBranchModal{{ $b->id }}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>

                    <!-- Modal Editar Sucursal -->
                    <div class="modal fade" id="editBranchModal{{ $b->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('branches.update', $b) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Sucursal: {{ $b->name }}</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Código de Sucursal</label>
                                            <input type="text" name="code" class="form-control" value="{{ $b->code }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Nombre de la Sucursal *</label>
                                            <input type="text" name="name" class="form-control" value="{{ $b->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Encargado / Administrador</label>
                                            <input type="text" name="manager_name" class="form-control" value="{{ $b->manager_name }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Dirección</label>
                                            <input type="text" name="address" class="form-control" value="{{ $b->address }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Teléfono</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $b->phone }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Ciudad</label>
                                                <input type="text" name="city" class="form-control" value="{{ $b->city }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Estado</label>
                                            <select name="status" class="form-control">
                                                <option value="ACTIVO" {{ $b->status == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                                                <option value="INACTIVO" {{ $b->status == 'INACTIVO' ? 'selected' : '' }}>INACTIVO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Sucursal -->
<div class="modal fade" id="newBranchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('branches.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-store-alt mr-2"></i> Registrar Nueva Sucursal / Punto de Venta</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Código</label>
                            <input type="text" name="code" class="form-control" placeholder="Ej: SUC-02">
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Nombre de la Sucursal *</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Sucursal Norte" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Encargado de Sucursal</label>
                        <input type="text" name="manager_name" class="form-control" placeholder="Nombre del responsable">
                    </div>
                    <div class="form-group">
                        <label>Dirección de la Sucursal</label>
                        <input type="text" name="address" class="form-control" placeholder="Av. / Calle / Número">
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Teléfono</label>
                            <input type="text" name="phone" class="form-control" placeholder="Teléfono de contacto">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Ciudad</label>
                            <input type="text" name="city" class="form-control" value="{{ $company->city ?? 'Cochabamba' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Sucursal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectBusinessType(typeKey, cardEl) {
        document.getElementById('input_business_type').value = typeKey;
        document.querySelectorAll('.rubro-card').forEach(function(el) {
            el.classList.remove('active');
        });
        cardEl.classList.add('active');

        // Pre-ajustar switches sugeridos según el rubro elegido
        var presets = {
            'GENERAL':      { pos: true, credits: true, quotations: true, cash: true, kardex: true, lots: true, warranties: true, services: true },
            'MINIMARKET':   { pos: true, credits: true, quotations: false, cash: true, kardex: true, lots: true, warranties: false, services: false },
            'FARMACIA':     { pos: true, credits: false, quotations: true, cash: true, kardex: true, lots: true, warranties: false, services: true },
            'FERRETERIA':   { pos: true, credits: true, quotations: true, cash: true, kardex: true, lots: false, warranties: true, services: true },
            'ELECTRONICA':  { pos: true, credits: true, quotations: true, cash: true, kardex: true, lots: false, warranties: true, services: true },
            'ROPA':         { pos: true, credits: true, quotations: false, cash: true, kardex: true, lots: false, warranties: false, services: false },
            'RESTAURANTE':  { pos: true, credits: false, quotations: false, cash: true, kardex: true, lots: true, warranties: false, services: true },
            'SERVICIOS':    { pos: true, credits: true, quotations: true, cash: true, kardex: true, lots: false, warranties: true, services: true }
        };

        if (presets[typeKey]) {
            var p = presets[typeKey];
            document.getElementById('chk_pos').checked = p.pos;
            document.getElementById('chk_credits').checked = p.credits;
            document.getElementById('chk_quotations').checked = p.quotations;
            document.getElementById('chk_cash').checked = p.cash;
            document.getElementById('chk_kardex').checked = p.kardex;
            document.getElementById('chk_lots').checked = p.lots;
            document.getElementById('chk_warranties').checked = p.warranties;
            document.getElementById('chk_services').checked = p.services;
        }
    }
</script>
@endsection

