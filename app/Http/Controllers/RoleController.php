<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function index(Request $request)
    {
        $roles = Role::orderBy('created_at','DESC')->get();
        $summary = [
            'totalRoles' => $roles->count(),
            'activeRoles' => $roles->where('status', 'active')->count(),
            'inactiveRoles' => $roles->where('status', 'inactive')->count(),
        ];

        return view(themeView('admin', 'roles.index'), compact('roles', 'summary'));
    }

    /**
     * Show the form for creating a new resource.
     */
    function create()
    {
        $menus = RolePermission::where('status', 'active')->where('type', 'menu')->orderBy('name')->get();
        $permissions = RolePermission::where('status', 'active')->where('type', 'link')->orderBy('name')->get();

        return view(themeView('admin', 'roles.form'), [
            'menus' => $menus,
            'permissions' => $permissions,
            'permissionGroups' => $this->buildPermissionGroups($menus, $permissions),
            'role' => null,
            'rolePermissions' => [],
            'pageTitle' => 'Add Role',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|unique:roles',
            'menus' => 'required',
            'status' => 'required',
            'permissions' => 'required',
        ]);

        $permissions = array_merge($data['menus'], $data['permissions']);
        $permissions = implode(",",$permissions);

        Role::create([
            'name' => $data['name'],
            'status' => $data['status'],
            'permissions' => $permissions
        ]);

        return redirect(route('role.index'))->with('message','Role added succesfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $menus = RolePermission::where('status', 'active')->where('type', 'menu')->orderBy('name')->get();
        $permissions = RolePermission::where('status', 'active')->where('type', 'link')->orderBy('name','ASC')->get();
        $rolePermissions = explode(",",$role->permissions);
    
        return view(themeView('admin', 'roles.form'), [
            'menus' => $menus,
            'permissions' => $permissions,
            'permissionGroups' => $this->buildPermissionGroups($menus, $permissions),
            'rolePermissions' => $rolePermissions,
            'role' => $role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $data = $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $role->id,
            'menus' => 'required',
            'status' => 'required',
            'permissions' => 'required',
        ]);
        
        $permissions = array_merge($data['menus'], $data['permissions']);
        $permissions = implode(",", $permissions);
    
        $role->update([
            'name' => $data['name'],
            'status' => $data['status'],
            'permissions' => $permissions
        ]);

        return redirect(route('role.index'))->with('message', 'Role updated succesfully');

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }

    private function buildPermissionGroups(Collection $menus, Collection $permissions): array
    {
        $menuByName = $menus->keyBy(fn ($menu) => trim((string) $menu->name));

        $definitions = [
            [
                'label' => 'Dashboard',
                'parent' => 'Dashboard',
                'parent_permissions' => [],
                'children' => [],
            ],
            [
                'label' => 'Announcement',
                'parent' => 'Announcement',
                'parent_permissions' => ['announcement.*'],
                'children' => [],
            ],
            [
                'label' => 'Catalogue',
                'parent' => 'Catalogue',
                'parent_permissions' => [],
                'children' => [
                    ['label' => 'API Providers', 'menu' => 'API Providers', 'permissions' => ['api.*']],
                    ['label' => 'Categories', 'menu' => 'Categories', 'permissions' => ['category.*']],
                    ['label' => 'Products', 'menu' => 'Products', 'permissions' => ['product.*']],
                ],
            ],
            [
                'label' => 'Email Management',
                'parent' => 'Email Management',
                'parent_permissions' => [],
                'children' => [
                    ['label' => 'Emails', 'menu' => 'Emails', 'permissions' => ['emails.index', 'emails.update']],
                    ['label' => 'Pending Emails', 'menu' => 'Pending Emails', 'permissions' => ['emails.pending', 'emails.resend', 'emails.sweep', 'emails-send', 'emails.destroy']],
                ],
            ],
            [
                'label' => 'Customers',
                'parent' => 'Customers',
                'parent_permissions' => [],
                'children' => [
                    ['label' => 'All Customers', 'menu' => 'All Customers', 'permissions' => ['customers', 'customers.edit', 'customers.update']],
                    ['label' => 'Active Customers', 'menu' => 'Active Customers', 'permissions' => ['customers.active*']],
                    ['label' => 'Suspended Customers', 'menu' => 'Suspended Customers', 'permissions' => ['customers.suspended*']],
                    ['label' => 'Blacklisted Customers', 'menu' => 'Blacklisted Customers', 'permissions' => ['customer-blacklist.*']],
                    ['label' => 'Unverified Customers', 'menu' => 'Unverified Customers', 'permissions' => ['customers.unverified*']],
                    ['label' => 'Customer Levels', 'menu' => 'Customer Levels', 'permissions' => ['customerlevel.*']],
                    ['label' => 'Level Benefits', 'menu' => 'Level Benefits', 'permissions' => ['levelbenefit.*']],
                    ['label' => 'Shop Creation Requests', 'menu' => 'Shop Creation Requests', 'permissions' => ['customer.shop.requests', 'approve.shop.requests', 'decline.shop.requests', 'delete.shop.request', 'delete.shop.requests', 'update.shop.requests']],
                ],
            ],
            [
                'label' => 'User Management',
                'parent' => 'User Management',
                'parent_permissions' => [],
                'children' => [
                    ['label' => 'All Admins', 'menu' => 'All Admins', 'permissions' => ['admins', 'adminSave']],
                    ['label' => 'All Roles', 'menu' => 'All Roles', 'permissions' => ['role*']],
                    ['label' => 'All Permissions', 'menu' => 'All Permissions', 'permissions' => ['permission*']],
                ],
            ],
            [
                'label' => 'Financials',
                'parent' => 'Financials',
                'parent_permissions' => [],
                'children' => [
                    ['label' => 'Product Purchase Log', 'menu' => 'Product Purchase Log', 'permissions' => ['admin.trans']],
                    ['label' => 'Wallet Funding Log', 'menu' => 'Wallet Funding Log', 'permissions' => ['admin.walletfundinglog']],
                    ['label' => 'Wallet Log', 'menu' => 'Wallet Log', 'permissions' => ['admin.walletlog']],
                    ['label' => 'Earnings Log', 'menu' => 'Earnings Log', 'permissions' => ['admin.earninglog']],
                    ['label' => 'Credit Customer', 'menu' => 'Credit Customer', 'permissions' => ['admin.credit.customer', 'admin.process.credit.debit']],
                    ['label' => 'Debit Customer', 'menu' => 'Debit Customer', 'permissions' => ['admin.debit.customer', 'admin.process.credit.debit']],
                    ['label' => 'Verify Biller', 'menu' => 'Verify Biller', 'permissions' => ['admin.verifybiller', 'admin.verify.post']],
                    ['label' => 'Biller Logs', 'menu' => 'Biller Logs', 'permissions' => ['billerlog.*']],
                    ['label' => 'Reserved Account Numbers', 'menu' => 'Reserved Account Numbers', 'permissions' => ['admin.reserved.accounts', 'admin.query.wallet', 'admin.requery.transaction', 'admin.single.transaction.view', 'admin.password.reset', 'admin.transaction.pin.reset']],
                ],
            ],
            [
                'label' => 'KYC Management',
                'parent' => 'KYC Management',
                'parent_permissions' => ['admin.kyc'],
                'children' => [],
            ],
            [
                'label' => 'Payment Gateways',
                'parent' => 'Payment Gateways',
                'parent_permissions' => ['paymentgateway.*'],
                'children' => [],
            ],
            [
                'label' => 'General Settings',
                'parent' => 'General Settings',
                'parent_permissions' => ['settings.*'],
                'children' => [],
            ],
            [
                'label' => 'Callback Analysis',
                'parent' => 'Callback Analysis',
                'parent_permissions' => ['callback.analysis'],
                'children' => [],
            ],
            [
                'label' => 'My Profile',
                'parent' => 'My Profile',
                'parent_permissions' => ['profile.*'],
                'children' => [],
            ],
        ];

        return collect($definitions)->map(function (array $definition) use ($menuByName, $permissions) {
            $parent = $menuByName[$definition['parent']] ?? null;
            $parentPermissions = $this->matchPermissions($permissions, $definition['parent_permissions'] ?? []);

            $children = collect($definition['children'] ?? [])
                ->map(function (array $child) use ($menuByName, $permissions) {
                    $menu = $menuByName[$child['menu']] ?? null;
                    $matchedPermissions = $this->matchPermissions($permissions, $child['permissions'] ?? []);

                    if (!$menu && $matchedPermissions->isEmpty()) {
                        return null;
                    }

                    return [
                        'label' => $child['label'],
                        'menu' => $menu ? [
                            'id' => (int) $menu->id,
                            'name' => $menu->name,
                            'route' => $menu->route,
                            'type' => $menu->type,
                        ] : null,
                        'permissions' => $matchedPermissions->map(fn ($permission) => [
                            'id' => (int) $permission->id,
                            'name' => $permission->name,
                            'route' => $permission->route,
                            'type' => $permission->type,
                        ])->values()->all(),
                    ];
                })
                ->filter()
                ->values()
                ->all();

            return [
                'key' => Str::slug($definition['parent']),
                'label' => $definition['label'],
                'parent' => $parent ? [
                    'id' => (int) $parent->id,
                    'name' => $parent->name,
                    'route' => $parent->route,
                    'type' => $parent->type,
                ] : null,
                'parent_permissions' => $parentPermissions->map(fn ($permission) => [
                    'id' => (int) $permission->id,
                    'name' => $permission->name,
                    'route' => $permission->route,
                    'type' => $permission->type,
                ])->values()->all(),
                'children' => $children,
            ];
        })->filter(fn (array $group) => !empty($group['parent']) || !empty($group['parent_permissions']) || !empty($group['children']))->values()->all();
    }

    private function matchPermissions(Collection $permissions, array $patterns): Collection
    {
        if (empty($patterns)) {
            return collect();
        }

        return $permissions->filter(function ($permission) use ($patterns) {
            $value = (string) ($permission->route ?? $permission->name);

            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $value) || Str::is($pattern, (string) $permission->name)) {
                    return true;
                }
            }

            return false;
        })->values();
    }
}
