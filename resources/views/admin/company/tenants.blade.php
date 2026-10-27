@extends('layouts.admin')
@section('title', 'Panel SuperAdmin SaaS — Empresas Clientes')

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h3 class="page-title font-weight-bold mb-1">
                <i class="fas fa-cloud text-primary mr-2"></i>Panel Maestro SaaS — Gestión de Empresas Clientes (Multi-Tenant)
            </h3>
            <p class="text-muted mb-0 small">
                Cada empresa cliente tiene aislamiento total (`company_id`) de sus formas de pago, sucursales, inventario, clientes, cajas y ventas.
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalNewTenant">
                <i class="fas fa-plus-circle mr-1"></i> Aprovisionar Nueva Empresa Cliente
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Resumen Global SaaS -->
    <div class="row mb-4">
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-primary text-white shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small text-uppercase font-weight-bold" style="opacity:0.85;">Empresas Registradas</p>
                            <h3 class="font-weight-bold mb-0">{{ $tenants->count() }}</h3>
                            <small>Clientes en la plataforma</small>
                        </div>
                        <i class="fas fa-building fa-2x" style="opacity:0.4;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-success text-white shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small text-uppercase font-weight-bold" style="opacity:0.85;">Suscripciones Activas</p>
                            <h3 class="font-weight-bold mb-0">{{ $tenants->where('status', 'ACTIVO')->count() }}</h3>
                            <small>Empresas operando</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x" style="opacity:0.4;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-info text-white shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small text-uppercase font-weight-bold" style="opacity:0.85;">Total Sucursales</p>
                            <h3 class="font-weight-bold mb-0">{{ $tenants->sum('branches_count') }}</h3>
                            <small>Puntos de venta activos</small>
                        </div>
                        <i class="fas fa-store-alt fa-2x" style="opacity:0.4;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-dark text-white shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small text-uppercase font-weight-bold text-warning">Entorno Activo Ahora</p>
                            <h5 class="font-weight-bold mb-0 text-truncate" style="max-width: 170px;">{{ $activeCompany->name }}</h5>
                            <small class="text-info">ID #{{ $activeCompany->id }} ({{ $activeCompany->business_type }})</small>
                        </div>
                        <i class="fas fa-exchange-alt fa-2x text-warning" style="opacity:0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Empresas Clientes (Tenants) -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0 font-weight-bold">
                    <i class="fas fa-network-wired text-primary mr-2"></i>Directorio de Negocios / Clientes SaaS
                </h5>
                <span class="badge badge-light border px-3 py-2">
                    <i class="fas fa-shield-alt text-success mr-1"></i> Aislamiento Multi-Tenant Activo (`company_id`)
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Empresa / Negocio</th>
                            <th>Rubro</th>
                            <th>Propietario / Admin</th>
                            <th>Plan SaaS</th>
                            <th class="text-center">Sucursales</th>
                            <th class="text-center">Catálogo / Ventas</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones SuperAdmin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                        @php
                            $bType = $businessTypes[$tenant->business_type] ?? $businessTypes['GENERAL'];
                            $isCurrent = ($activeCompany && $activeCompany->id === $tenant->id);
                        @endphp
                        <tr class="{{ $isCurrent ? 'table-primary' : '' }}">
                            <td class="font-weight-bold">#{{ $tenant->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('image/' . ($tenant->logo ?: 'logo_default.png')) }}"
                                         alt="Logo" class="rounded mr-2 border"
                                         style="width:38px;height:38px;object-fit:contain;background:#fff;"
                                         onerror="this.src='{{ asset('melody/images/logo.svg') }}'">
                                    <div>
                                        <div class="font-weight-bold text-dark">
                                            {{ $tenant->name }}
                                            @if($isCurrent)
                                                <span class="badge badge-primary ml-1">EN USO</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">NIT: {{ $tenant->nit ?: 'S/N' }} · {{ $tenant->city ?: 'Bolivia' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-outline-info px-2 py-1">
                                    <i class="fas {{ $bType['icon'] }} mr-1"></i>{{ $tenant->business_type }}
                                </span>
                            </td>
                            <td>
                                @if($tenant->owner)
                                    <div class="font-weight-bold">{{ $tenant->owner->name }}</div>
                                    <small class="text-muted"><i class="fas fa-envelope mr-1"></i>{{ $tenant->owner->email }}</small>
                                @else
                                    <span class="text-muted small">Usuario Principal #1</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-dark px-2 py-1">{{ $tenant->subscription_plan ?? 'ENTERPRISE' }}</span>
                                <div class="small text-muted mt-1">
                                    Máx: {{ $tenant->max_branches ?? 5 }} suc. / {{ $tenant->max_users ?? 15 }} usu.
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-info px-3 py-1 font-weight-bold">
                                    {{ $tenant->branches_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="small font-weight-bold text-dark">{{ $tenant->products_count }} ítems</div>
                                <small class="text-success font-weight-bold">
                                    {{ $tenant->sales_count }} ventas ({{ $tenant->currency_symbol }} {{ number_format($tenant->sales_total, 2) }})
                                </small>
                            </td>
                            <td>
                                @if(($tenant->status ?? 'ACTIVO') === 'ACTIVO')
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>ACTIVO</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-ban mr-1"></i>SUSPENDIDO</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="btn-group">
                                    @if(!$isCurrent)
                                        <form action="{{ route('saas.tenants.switch') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="company_id" value="{{ $tenant->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-primary font-weight-bold" title="Entrar al panel de este cliente">
                                                <i class="fas fa-sign-in-alt mr-1"></i> Gestionar
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('company.index') }}" class="btn btn-sm btn-primary font-weight-bold">
                                            <i class="fas fa-cog mr-1"></i> Configurar
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-warning font-weight-bold text-dark"
                                            data-toggle="modal" data-target="#modalPlanTenant{{ $tenant->id }}"
                                            title="Designar Rubro, Módulos y Plan SaaS">
                                        <i class="fas fa-sliders-h mr-1"></i> Rubro / Plan
                                    </button>
                                </div>

                                <!-- Modal Designar Rubro, Módulos y Plan SaaS (Exclusivo SuperAdmin) -->
                                <div class="modal fade text-left" id="modalPlanTenant{{ $tenant->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form action="{{ route('saas.tenants.plan', $tenant) }}" method="POST" class="modal-content">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title font-weight-bold">
                                                    <i class="fas fa-crown mr-2 text-warning"></i>Designar Rubro, Módulos y Plan SaaS — {{ $tenant->name }}
                                                </h5>
                                                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
                                                    <i class="fas fa-briefcase mr-1"></i> 1. Giro / Tipo de Negocio Designado por Ti
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-12 form-group">
                                                        <label class="font-weight-bold">Seleccionar Rubro del Cliente</label>
                                                        <select name="business_type" class="form-control font-weight-bold">
                                                            @foreach($businessTypes as $key => $typeInfo)
                                                                <option value="{{ $key }}" {{ ($tenant->business_type ?? 'GENERAL') === $key ? 'selected' : '' }}>
                                                                    {{ $typeInfo['label'] }} — {{ $typeInfo['desc'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <div class="custom-control custom-checkbox bg-light p-2 rounded border">
                                                            <input type="checkbox" class="custom-control-input" id="preset_{{ $tenant->id }}" name="apply_rubro_preset" value="1">
                                                            <label class="custom-control-label font-weight-bold text-primary" for="preset_{{ $tenant->id }}">
                                                                Auto-activar módulos ideales de este rubro
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <div class="custom-control custom-checkbox bg-light p-2 rounded border">
                                                            <input type="checkbox" class="custom-control-input" id="seed_{{ $tenant->id }}" name="seed_rubro_catalog" value="1">
                                                            <label class="custom-control-label font-weight-bold text-success" for="seed_{{ $tenant->id }}">
                                                                Crear categorías y marcas/laboratorios del rubro
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3 mt-2">
                                                    <i class="fas fa-toggle-on mr-1"></i> 2. Módulos Contratados para este Negocio
                                                </h6>
                                                <div class="row mb-2">
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_pos" value="1" class="mr-2" {{ ($tenant->enable_pos ?? true) ? 'checked' : '' }}>
                                                            <span>Caja Rápida (POS)</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_cash_register" value="1" class="mr-2" {{ ($tenant->enable_cash_register ?? true) ? 'checked' : '' }}>
                                                            <span>Turnos y Arqueo de Caja</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_kardex" value="1" class="mr-2" {{ ($tenant->enable_kardex ?? true) ? 'checked' : '' }}>
                                                            <span>Kardex y Mermas</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_credits" value="1" class="mr-2" {{ ($tenant->enable_credits ?? true) ? 'checked' : '' }}>
                                                            <span>Ventas a Crédito / Cuotas</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_quotations" value="1" class="mr-2" {{ ($tenant->enable_quotations ?? true) ? 'checked' : '' }}>
                                                            <span>Cotizaciones / Proformas</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_lots_expiry" value="1" class="mr-2" {{ ($tenant->enable_lots_expiry ?? true) ? 'checked' : '' }}>
                                                            <span>Lotes y Vencimientos</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_serials_warranties" value="1" class="mr-2" {{ ($tenant->enable_serials_warranties ?? true) ? 'checked' : '' }}>
                                                            <span>Series y Garantías</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_services" value="1" class="mr-2" {{ ($tenant->enable_services ?? true) ? 'checked' : '' }}>
                                                            <span>Ítems Tipo Servicio</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="d-flex align-items-center mb-0">
                                                            <input type="checkbox" name="enable_wholesale" value="1" class="mr-2" {{ ($tenant->enable_wholesale ?? true) ? 'checked' : '' }}>
                                                            <span>Precios Mayoristas</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 mt-3">
                                                    <i class="fas fa-id-card-alt mr-1"></i> 3. Plan de Suscripción, Límites y Estado
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-4 form-group">
                                                        <label class="font-weight-bold">Plan de Suscripción</label>
                                                        <select name="subscription_plan" class="form-control">
                                                            @foreach(['EMPRENDEDOR', 'PYME PRO', 'ENTERPRISE', 'ILIMITADO'] as $plan)
                                                                <option value="{{ $plan }}" {{ ($tenant->subscription_plan ?? 'ENTERPRISE') == $plan ? 'selected' : '' }}>
                                                                    PLAN {{ $plan }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label class="font-weight-bold">Límite de Sucursales</label>
                                                        <input type="number" name="max_branches" class="form-control" min="1" value="{{ $tenant->max_branches ?? 5 }}">
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label class="font-weight-bold">Límite de Usuarios</label>
                                                        <input type="number" name="max_users" class="form-control" min="1" value="{{ $tenant->max_users ?? 15 }}">
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label class="font-weight-bold">Vencimiento de Suscripción</label>
                                                        <input type="date" name="subscription_expires_at" class="form-control" value="{{ $tenant->subscription_expires_at }}">
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label class="font-weight-bold">Estado del Servicio</label>
                                                        <select name="status" class="form-control">
                                                            <option value="ACTIVO" {{ ($tenant->status ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' }}>ACTIVO (Operativo)</option>
                                                            <option value="SUSPENDIDO" {{ ($tenant->status ?? '') === 'SUSPENDIDO' ? 'selected' : '' }}>SUSPENDIDO</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary font-weight-bold">
                                                    <i class="fas fa-save mr-1"></i> Guardar Rubro, Módulos y Plan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprovisionar Nueva Empresa Cliente (Onboarding SaaS en 1 Clic) -->
<div class="modal fade" id="modalNewTenant" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('saas.tenants.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-rocket mr-2"></i>Aprovisionar Nueva Empresa Cliente (Onboarding SaaS)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>¿Qué hace este asistente automáticamente?</strong> Crea la empresa aislada, su Casa Matriz, su Usuario Propietario Administrador, y sus propias Formas de Pago (`Efectivo`, `QR`, `Transferencia`, `Tarjeta`, `Crédito`) para que el cliente tenga control 100% independiente.
                </div>

                <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
                    <i class="fas fa-building mr-1"></i> 1. Datos del Negocio del Cliente
                </h6>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Nombre Comercial del Negocio <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Ej: Farmacia Santa María / Ferretería El Tornillo">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Rubro / Tipo de Negocio <span class="text-danger">*</span></label>
                        <select name="business_type" class="form-control" required>
                            @foreach($businessTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type['label'] }} — {{ $type['desc'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">NIT / RUC Fiscal</label>
                        <input type="text" name="nit" class="form-control" placeholder="Ej: 4589210015">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Teléfono / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="Ej: +591 70000000">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Ciudad</label>
                        <input type="text" name="city" class="form-control" value="La Paz">
                    </div>
                </div>

                <h6 class="font-weight-bold text-success border-bottom pb-2 mb-3 mt-2">
                    <i class="fas fa-user-shield mr-1"></i> 2. Cuenta del Propietario / Administrador del Negocio
                </h6>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Nombre del Propietario <span class="text-danger">*</span></label>
                        <input type="text" name="owner_name" class="form-control" required placeholder="Ej: Carlos Mendoza">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Email de Acceso (Login) <span class="text-danger">*</span></label>
                        <input type="email" name="owner_email" class="form-control" required placeholder="carlos@minegocio.com">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Contraseña Inicial <span class="text-danger">*</span></label>
                        <input type="text" name="owner_password" class="form-control" required value="Cliente2026*" minlength="6">
                    </div>
                </div>

                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 mt-2">
                    <i class="fas fa-crown text-warning mr-1"></i> 3. Plan de Suscripción y Límites
                </h6>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Plan Contratado</label>
                        <select name="subscription_plan" class="form-control">
                            <option value="EMPRENDEDOR">PLAN EMPRENDEDOR</option>
                            <option value="PYME PRO" selected>PLAN PYME PRO</option>
                            <option value="ENTERPRISE">PLAN ENTERPRISE</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Máximo de Sucursales</label>
                        <input type="number" name="max_branches" class="form-control" value="3" min="1">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Máximo de Usuarios</label>
                        <input type="number" name="max_users" class="form-control" value="10" min="1">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-check-circle mr-1"></i> Crear Empresa Cliente y Aprovisionar Entorno
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

