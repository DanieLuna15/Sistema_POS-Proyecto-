<?php

namespace App\Http\Controllers;

use App\Company;
use App\Branch;
use App\Product;
use App\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Panel de Configuración de Empresa, Rubro SaaS y Sucursales
     */
    public function index()
    {
        $company = Company::current();
        $branches = Branch::where('company_id', $company->id)->orderBy('is_main', 'DESC')->orderBy('id', 'ASC')->get();
        $businessTypes = Company::businessTypes();

        return view('admin.company.index', compact('company', 'branches', 'businessTypes'));
    }

    /**
     * Actualizar Configuración General de la Empresa y Perfil de Rubro
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'nit' => 'nullable|string|max:50',
            'currency_symbol' => 'required|string|max:10',
            'currency_name' => 'required|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $company = Company::current();
        $isSuperAdmin = (Auth::check() && (Auth::user()->is_superadmin || Auth::id() === 1));

        // Campos corporativos y fiscales que el dueño del negocio SÍ puede personalizar libremente
        $data = $request->only([
            'name',
            'legal_name',
            'slogan',
            'nit',
            'authorization_number',
            'phone',
            'address',
            'city',
            'country',
            'email',
            'currency_symbol',
            'currency_name',
            'tax_name',
            'tax_rate',
            'default_stock_alert',
            'receipt_footer',
            'terms_conditions',
        ]);

        // EXCLUSIVO SUPERADMIN SAAS: Solo el dueño del SaaS designa el Tipo de Negocio (Rubro) y los Módulos activos
        if ($isSuperAdmin && $request->has('business_type')) {
            $data['business_type'] = $request->business_type;
            $data['enable_pos'] = $request->has('enable_pos');
            $data['enable_credits'] = $request->has('enable_credits');
            $data['enable_quotations'] = $request->has('enable_quotations');
            $data['enable_cash_register'] = $request->has('enable_cash_register');
            $data['enable_kardex'] = $request->has('enable_kardex');
            $data['enable_lots_expiry'] = $request->has('enable_lots_expiry');
            $data['enable_serials_warranties'] = $request->has('enable_serials_warranties');
            $data['enable_services'] = $request->has('enable_services');
            $data['enable_wholesale'] = $request->has('enable_wholesale');
        }

        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $logoName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/image'), $logoName);
            $data['logo'] = $logoName;
        }

        $company->update($data);

        Alert::toast('Datos del negocio actualizados con éxito.', 'success');
        return redirect()->route('company.index');
    }

    /**
     * Crear nueva Sucursal / Punto de Venta
     */
    public function storeBranch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:250',
            'phone' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:100',
        ]);

        $company = Company::current();
        $count = Branch::withoutGlobalScope('company_tenant')->where('company_id', $company->id)->count();

        if ($company->max_branches && $count >= $company->max_branches && !(Auth::check() && Auth::user()->is_superadmin)) {
            Alert::error('Límite de Sucursales Alcanzado', "Su plan actual ({$company->subscription_plan}) permite hasta {$company->max_branches} sucursales.");
            return redirect()->route('company.index');
        }

        $branch = Branch::create([
            'company_id' => $company->id,
            'code' => $request->code ?: 'SUC-0' . ($count + 1),
            'name' => $request->name,
            'manager_name' => $request->manager_name,
            'phone' => $request->phone ?: $company->phone,
            'email' => $request->email ?: $company->email,
            'address' => $request->address ?: $company->address,
            'city' => $request->city ?: $company->city,
            'authorization_number' => $request->authorization_number ?: $company->authorization_number,
            'is_main' => ($count === 0),
            'status' => 'ACTIVO',
        ]);

        Alert::toast("Sucursal '{$branch->name}' registrada con éxito.", 'success');
        return redirect()->route('company.index');
    }

    /**
     * Actualizar datos de una Sucursal
     */
    public function updateBranch(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:30',
        ]);

        $branch->update($request->only([
            'code',
            'name',
            'manager_name',
            'phone',
            'email',
            'address',
            'city',
            'authorization_number',
            'status',
        ]));

        Alert::toast("Sucursal '{$branch->name}' actualizada.", 'success');
        return redirect()->route('company.index');
    }

    /**
     * Cambiar la Sucursal Activa en la sesión del usuario
     */
    public function switchBranch(Request $request)
    {
        $branch = Branch::findOrFail($request->branch_id);
        session(['active_branch_id' => $branch->id]);

        Alert::toast("Sucursal activa cambiada a: {$branch->name}", 'info');
        return redirect()->back();
    }

    /**
     * PANEL SUPERADMIN SAAS: Gestión de Empresas Clientes (Multi-Tenant)
     */
    public function tenants()
    {
        if (!Auth::user()->is_superadmin && Auth::id() !== 1) {
            abort(403, 'Acceso exclusivo para el SuperAdministrador del SaaS.');
        }

        $tenants = Company::with(['owner', 'users'])->orderBy('id', 'ASC')->get()->map(function ($tenant) {
            $tenant->branches_count = Branch::withoutGlobalScope('company_tenant')->where('company_id', $tenant->id)->count();
            $tenant->products_count = Product::withoutGlobalScope('company_tenant')->where('company_id', $tenant->id)->count();
            $tenant->sales_count = Sale::withoutGlobalScope('company_tenant')->where('company_id', $tenant->id)->where('status', 'CONFIRMADO')->count();
            $tenant->sales_total = Sale::withoutGlobalScope('company_tenant')->where('company_id', $tenant->id)->where('status', 'CONFIRMADO')->sum('total');
            return $tenant;
        });

        $businessTypes = Company::businessTypes();
        $activeCompany = Company::current();

        return view('admin.company.tenants', compact('tenants', 'businessTypes', 'activeCompany'));
    }

    /**
     * Provisionar un Nuevo Cliente / Empresa en el SaaS con aislamiento completo
     */
    public function storeTenant(Request $request)
    {
        if (!Auth::user()->is_superadmin && Auth::id() !== 1) {
            abort(403, 'Acceso exclusivo para el SuperAdministrador del SaaS.');
        }

        $request->validate([
            'name' => 'required|string|max:150',
            'business_type' => 'required|string|max:50',
            'owner_name' => 'required|string|max:120',
            'owner_email' => 'required|email|max:120|unique:users,email',
            'owner_password' => 'required|string|min:6|max:60',
            'nit' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:100',
            'subscription_plan' => 'required|string|max:40',
        ], [
            'owner_email.unique' => 'El correo electrónico del propietario ya está registrado en el sistema.',
        ]);

        $tenant = Company::provisionNewTenant($request->all());

        Alert::success(
            '¡Empresa Cliente Creada!',
            "Se creó el entorno aislado para '{$tenant->name}' con su propietario ({$request->owner_email}), sucursal matriz y métodos de pago propios."
        );

        return redirect()->route('saas.tenants.index');
    }

    /**
     * Cambiar la Empresa Activa (Modo Soporte / SuperAdmin Switcher)
     */
    public function switchTenant(Request $request)
    {
        if (!Auth::user()->is_superadmin && Auth::id() !== 1) {
            abort(403, 'No autorizado.');
        }

        $company = Company::findOrFail($request->company_id);
        $firstBranch = Branch::withoutGlobalScope('company_tenant')
            ->where('company_id', $company->id)
            ->orderBy('is_main', 'DESC')
            ->first();

        session([
            'active_company_id' => $company->id,
            'active_branch_id' => $firstBranch ? $firstBranch->id : null,
        ]);

        Alert::toast("Entorno cambiado a la empresa: {$company->name}", 'info');
        return redirect()->route('home');
    }

    /**
     * Actualizar Rubro, Módulos, Plan o Estado (ACTIVO / SUSPENDIDO) de una Empresa Cliente (Exclusivo SuperAdmin)
     */
    public function updateTenantPlan(Request $request, Company $company)
    {
        if (!Auth::user()->is_superadmin && Auth::id() !== 1) {
            abort(403, 'No autorizado.');
        }

        $newBusinessType = $request->business_type ?? $company->business_type;
        $profile = Company::rubroProfile($newBusinessType);

        // Si el SuperAdmin marcó "Aplicar plantilla recomendada del rubro", preconfigura módulos y categorías del giro
        if ($request->has('apply_rubro_preset')) {
            $mods = $profile['modules'];
            $enablePos = $mods['pos'];
            $enableCredits = $mods['credits'];
            $enableQuotations = $mods['quotations'];
            $enableCash = $mods['cash'];
            $enableKardex = $mods['kardex'];
            $enableLots = $mods['lots'];
            $enableWarranties = $mods['warranties'];
            $enableServices = $mods['services'];
            $enableWholesale = $mods['wholesale'];
        } else {
            $enablePos = $request->has('enable_pos');
            $enableCredits = $request->has('enable_credits');
            $enableQuotations = $request->has('enable_quotations');
            $enableCash = $request->has('enable_cash_register');
            $enableKardex = $request->has('enable_kardex');
            $enableLots = $request->has('enable_lots_expiry');
            $enableWarranties = $request->has('enable_serials_warranties');
            $enableServices = $request->has('enable_services');
            $enableWholesale = $request->has('enable_wholesale');
        }

        $company->update([
            'business_type' => $newBusinessType,
            'plan_name' => $request->subscription_plan ?? $company->plan_name,
            'subscription_ends_at' => $request->subscription_expires_at ?: $company->subscription_ends_at,
            'max_branches' => intval($request->max_branches ?? $company->max_branches),
            'max_users' => intval($request->max_users ?? $company->max_users),
            'status' => $request->status ?? $company->status,
            'enable_pos' => $enablePos,
            'enable_credits' => $enableCredits,
            'enable_quotations' => $enableQuotations,
            'enable_cash_register' => $enableCash,
            'enable_kardex' => $enableKardex,
            'enable_lots_expiry' => $enableLots,
            'enable_serials_warranties' => $enableWarranties,
            'enable_services' => $enableServices,
            'enable_wholesale' => $enableWholesale,
        ]);

        // Sembrar categorías base del rubro si el cliente aún no las tiene
        if ($request->has('seed_rubro_catalog')) {
            foreach ($profile['seed_categories'] as $catName => $catDesc) {
                \Illuminate\Support\Facades\DB::table('categories')->updateOrInsert(
                    ['company_id' => $company->id, 'name' => $catName],
                    ['description' => $catDesc, 'updated_at' => now(), 'created_at' => now()]
                );
            }
            foreach ($profile['seed_brands'] as $brandName => $brandDesc) {
                \Illuminate\Support\Facades\DB::table('brands')->updateOrInsert(
                    ['company_id' => $company->id, 'name' => $brandName],
                    ['description' => $brandDesc, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        Alert::toast("Rubro ({$profile['short_label']}), módulos y plan de '{$company->name}' actualizados.", 'success');
        return redirect()->route('saas.tenants.index');
    }
}

