@extends('sneat.layouts.app')

@php
    $isEdit = !empty($permission?->id);
    $pageTitle = $pageTitle ?? ($isEdit ? 'Edit Permission' : 'Add Permission');
    $formAction = $isEdit ? route('permission.update', $permission->id) : route('permission.store');
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">User management</span>
                    <h1>{{ $pageTitle }}</h1>
                    <p>{{ $isEdit ? 'Update the route, type, and status for this permission.' : 'Create a new permission that can be assigned to roles.' }}</p>
                </div>
                <a href="{{ route('permission.index') }}" class="gateway-action">Back to permissions</a>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PATCH')
                @endif

                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Permission details</h3>
                                <p>Set the permission name and system route.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="name">Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="name" name="name" value="{{ old('name', $permission->name ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="route">Route</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="route" name="route" value="{{ old('route', $permission->route ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="status">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status" required>
                                            <option value="active" @selected(old('status', $permission->status ?? 'active') === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status', $permission->status ?? '') === 'inactive')>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="type">Type</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="type" name="type" required>
                                            <option value="menu" @selected(old('type', $permission->type ?? '') === 'menu')>Menu</option>
                                            <option value="link" @selected(old('type', $permission->type ?? '') === 'link')>Link</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Permission notes</h3>
                                <p>Keep these values aligned with the route definitions in the app.</p>
                            </div>
                            <div class="card-body">
                                <div class="gateway-helper">
                                    This screen is intentionally minimal to keep route names and permission types accurate.
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">{{ $isEdit ? 'Update permission' : 'Save permission' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
