<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $activeCompany->name ?? 'Sistema Comercial & POS SaaS' }}</title>
    {!! Html::style('melody/vendors/iconfonts/font-awesome/css/all.min.css') !!}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .hero-card {
            max-width: 780px;
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 20px;
            padding: 48px 40px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .badge-saas {
            display: inline-block;
            background: rgba(251, 191, 36, 0.18);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.4);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .company-title {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
            color: #ffffff;
        }
        .company-slogan {
            font-size: 16px;
            color: #cbd5e1;
            margin-bottom: 30px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 36px;
            text-align: left;
        }
        .feature-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 14px;
            border-radius: 12px;
            font-size: 13px;
            color: #e2e8f0;
        }
        .feature-item i {
            color: #60a5fa;
            font-size: 18px;
            margin-bottom: 8px;
            display: block;
        }
        .btn-enter {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            padding: 14px 36px;
            border-radius: 12px;
            transition: all 0.2s ease;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
        }
        .btn-enter:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
        .meta-footer {
            margin-top: 28px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="hero-card">
        <div class="badge-saas">
            <i class="fas fa-layer-group mr-1"></i> Plataforma SaaS Multi-Sucursal &bull; Rubro: {{ $activeCompany->business_type ?? 'GENERAL' }}
        </div>
        <h1 class="company-title">{{ $activeCompany->name ?? 'SISTEMA POS & COMERCIAL' }}</h1>
        <p class="company-slogan">{{ $activeCompany->slogan ?? 'Gestión Integral de Inventario, Caja Rápida POS, Créditos, Cotizaciones y Analítica' }}</p>

        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-cash-register"></i>
                <strong>POS Táctil & Turnos</strong><br>
                Apertura, arqueo de caja y cobros multimétodo.
            </div>
            <div class="feature-item">
                <i class="fas fa-boxes"></i>
                <strong>Kardex & Multi-Rubro</strong><br>
                Control de lotes, vencimientos, servicios y costos.
            </div>
            <div class="feature-item">
                <i class="fas fa-file-contract"></i>
                <strong>Proformas & Créditos</strong><br>
                Cotizaciones en PDF y plan de cuotas por cobrar.
            </div>
            <div class="feature-item">
                <i class="fas fa-store-alt"></i>
                <strong>Multi-Sucursal</strong><br>
                Configuración adaptable a cualquier tipo de negocio.
            </div>
        </div>

        @if (Route::has('login'))
            @auth
                <a href="{{ url('/home') }}" class="btn-enter">
                    <i class="fas fa-tachometer-alt"></i> Ir al Panel Principal
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-enter">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión en el Sistema
                </a>
            @endauth
        @endif

        <div class="meta-footer">
            {{ $activeCompany->legal_name ?? $activeCompany->name }} &bull; NIT: {{ $activeCompany->nit ?? 'S/N' }} &bull; {{ $activeCompany->city ?? 'Bolivia' }}
        </div>
    </div>
</body>
</html>
