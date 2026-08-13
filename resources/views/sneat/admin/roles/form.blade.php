@extends('sneat.layouts.app')

@php
    $isEdit = !empty($role?->id);
    $pageTitle = $pageTitle ?? ($isEdit ? 'Edit Role' : 'Add Role');
    $formAction = $isEdit ? route('role.update', $role->id) : route('role.store');
    $rolePermissionIds = collect(explode(',', (string) ($role->permissions ?? '')))->filter()->map(fn ($id) => (int) $id)->all();
    $menuIds = $menus->pluck('id')->map(fn ($id) => (int) $id)->all();
    $permissionIds = $permissions->pluck('id')->map(fn ($id) => (int) $id)->all();
    $selectedMenus = collect(old('menus', $isEdit ? array_values(array_intersect($rolePermissionIds, $menuIds)) : []))->map(fn ($id) => (int) $id)->all();
    $selectedPermissions = collect(old('permissions', $isEdit ? array_values(array_intersect($rolePermissionIds, $permissionIds)) : []))->map(fn ($id) => (int) $id)->all();
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">User management</span>
                    <h1>{{ $pageTitle }}</h1>
                    <p>{{ $isEdit ? 'Refine the role title, status, and grouped access it bundles together.' : 'Create a role and assign grouped menus plus permissions from the same clean form.' }}</p>
                </div>
                <a href="{{ route('role.index') }}" class="gateway-action">Back to roles</a>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PATCH')
                @endif

                <div class="row g-4">
                    <div class="col-xl-12">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Role details</h3>
                                <p>Set the display name and status for this role group.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="name">Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="name" name="name" value="{{ old('name', $role->name ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="status">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status" required>
                                            <option value="active" @selected(old('status', $role->status ?? 'active') === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status', $role->status ?? '') === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="modern-admin-card card">
                            <div class="card-header">
                                <h3>Access matrix</h3>
                                <p>Pick a parent menu, then drill down into the child permissions that belong to it.</p>
                            </div>
                            <div class="card-body">
                                <div class="role-tree">
                                    @foreach($permissionGroups as $group)
                                        @php
                                            $sectionKey = $group['key'];
                                            $parent = $group['parent'];
                                        @endphp

                                        <div class="role-section" data-role-section="{{ $sectionKey }}">
                                            <div class="role-section__header">
                                                <label class="role-section__title">
                                                    <input type="checkbox" class="form-check-input form-check-input-{{ checkBoxControlSize() }} role-section-select-all" data-role-section-toggle="{{ $sectionKey }}">
                                                    <span>{{ $group['label'] }}</span>
                                                </label>
                                                <div class="role-section__meta">
                                                    {{ count($group['children']) }} child groups
                                                </div>
                                            </div>

                                            <div class="role-section__body">
                                                @if(!empty($parent))
                                                    <div class="role-parent-row">
                                                        <label class="role-parent-row__label form-check">
                                                            <input
                                                                class="form-check-input form-check-input-{{ checkBoxControlSize() }} role-menu-checkbox"
                                                                type="checkbox"
                                                                name="menus[]"
                                                                value="{{ $parent['id'] }}"
                                                                data-role-section="{{ $sectionKey }}"
                                                                @checked(in_array($parent['id'], $selectedMenus, true))
                                                            >
                                                            <span class="form-check-label">{{ $parent['name'] }}</span>
                                                        </label>
                                                    </div>
                                                @endif

                                                @if(!empty($group['parent_permissions']))
                                                    <div class="role-permission-grid mb-2">
                                                        @foreach($group['parent_permissions'] as $permission)
                                                            <label class="modern-check-item role-permission-item form-check">
                                                                <input
                                                                    class="form-check-input form-check-input-{{ checkBoxControlSize() }} role-permission-checkbox"
                                                                    type="checkbox"
                                                                    name="permissions[]"
                                                                    value="{{ $permission['id'] }}"
                                                                    data-role-section="{{ $sectionKey }}"
                                                                    @checked(in_array($permission['id'], $selectedPermissions, true))
                                                                >
                                                                <span class="form-check-label">{{ $permission['name'] }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @foreach($group['children'] as $index => $child)
                                                    @php
                                                        $childKey = $sectionKey . '-' . $index;
                                                        $childMenu = $child['menu'] ?? null;
                                                    @endphp

                                                    <div class="role-child" data-role-child-key="{{ $childKey }}">
                                                        <div class="role-child__header">
                                                            <label class="role-child__label form-check">
                                                                @if(!empty($childMenu))
                                                                    <input
                                                                        class="form-check-input form-check-input-{{ checkBoxControlSize() }} role-menu-checkbox"
                                                                        type="checkbox"
                                                                        name="menus[]"
                                                                        value="{{ $childMenu['id'] }}"
                                                                        data-role-section="{{ $sectionKey }}"
                                                                        @checked(in_array($childMenu['id'], $selectedMenus, true))
                                                                    >
                                                                @endif
                                                                <span class="form-check-label">{{ $child['label'] }}</span>
                                                            </label>

                                                            @if(!empty($child['permissions']))
                                                                <label class="role-child__select-all form-check">
                                                                    <input
                                                                        type="checkbox"
                                                                        class="form-check-input form-check-input-{{ checkBoxControlSize() }} role-child-select-all"
                                                                        data-role-section-toggle="{{ $sectionKey }}"
                                                                        data-role-child-toggle="{{ $childKey }}"
                                                                    >
                                                                    <span class="form-check-label">Select all permissions</span>
                                                                </label>
                                                            @endif
                                                        </div>

                                                        @if(!empty($child['permissions']))
                                                            <div class="role-permission-grid">
                                                                @foreach($child['permissions'] as $permission)
                                                                    <label class="modern-check-item role-permission-item form-check">
                                                                        <input
                                                                            class="form-check-input form-check-input-{{ checkBoxControlSize() }} role-permission-checkbox"
                                                                            type="checkbox"
                                                                            name="permissions[]"
                                                                            value="{{ $permission['id'] }}"
                                                                            data-role-section="{{ $sectionKey }}"
                                                                            data-role-child="{{ $childKey }}"
                                                                            @checked(in_array($permission['id'], $selectedPermissions, true))
                                                                        >
                                                                        <span class="form-check-label">{{ $permission['name'] }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="modern-admin-footer">
                            <button class="btn btn-admin-submit" type="submit">{{ $isEdit ? 'Update role' : 'Save role' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        (function () {
            const $tree = $('.role-tree');

            function refreshRoleTreeState() {
                $('.role-section').each(function () {
                    const $section = $(this);
                    const sectionKey = $section.data('role-section');
                    const $sectionInputs = $section.find('input[data-role-section="' + sectionKey + '"]');
                    const sectionChecked = $sectionInputs.length > 0 && $sectionInputs.filter(':checked').length === $sectionInputs.length;

                    $section.find('.role-section-select-all[data-role-section-toggle="' + sectionKey + '"]').prop('checked', sectionChecked);

                    $section.find('.role-child').each(function () {
                        const $child = $(this);
                        const childKey = $child.data('role-child-key');
                        const $childInputs = $child.find('input[data-role-child="' + childKey + '"]');
                        const childChecked = $childInputs.length > 0 && $childInputs.filter(':checked').length === $childInputs.length;

                        $section.find('.role-child-select-all[data-role-child-toggle="' + childKey + '"]').prop('checked', childChecked);
                    });
                });
            }

            $(document).on('change', '.role-section-select-all', function () {
                const $section = $(this).closest('.role-section');
                const sectionKey = $section.data('role-section');
                const checked = $(this).is(':checked');

                $section.find('input[data-role-section="' + sectionKey + '"]').prop('checked', checked);
                refreshRoleTreeState();
            });

            $(document).on('change', '.role-child-select-all', function () {
                const $child = $(this).closest('.role-child');
                const childKey = $child.data('role-child-key');
                const checked = $(this).is(':checked');

                $child.find('input[data-role-child="' + childKey + '"]').prop('checked', checked);
                refreshRoleTreeState();
            });

            $(document).on('change', '.role-tree input[type="checkbox"]', function () {
                refreshRoleTreeState();
            });

            refreshRoleTreeState();
        })();
    </script>
@endsection
