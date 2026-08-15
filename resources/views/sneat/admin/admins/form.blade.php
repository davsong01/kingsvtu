@extends('sneat.layouts.app')

@php
    $isEdit = !empty($admin?->id);
    $pageTitle = $pageTitle ?? ($isEdit ? 'Edit Admin' : 'Add Admin');
    $formAction = $isEdit ? route('updateAdmin') : route('adminSave');
    $selectedRoles = collect(old('roles', $permissions ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="admin-page-hero mb-4">
                <div>
                    <span class="admin-page-hero__kicker">User management</span>
                    <h1>{{ $pageTitle }}</h1>
                    <p>{{ $isEdit ? 'Update the admin account and assigned roles from this single workspace.' : 'Create a new admin and assign roles from the same clean form.' }}</p>
                </div>
                <a href="{{ route('admins') }}" class="gateway-action">Back to admins</a>
            </div>

            @include('sneat.layouts.alerts')

            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($isEdit)
                    <input type="hidden" name="id" value="{{ $admin->id }}">
                @endif
                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Admin details</h3>
                                <p>Set the profile information and account status.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="first_name">First Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="first_name" name="first_name" value="{{ old('first_name', $admin->firstname ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="last_name">Last Name</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="last_name" name="last_name" value="{{ old('last_name', $admin->lastname ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="email">Email</label>
                                        <input type="email" class="form-control form-control-{{ formControlSize() }}" id="email" name="email" value="{{ old('email', $admin->email ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="phone">Phone</label>
                                        <input type="text" class="form-control form-control-{{ formControlSize() }}" id="phone" name="phone" value="{{ old('phone', $admin->phone ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="status">Status</label>
                                        <select class="form-select form-select-{{ formControlSize() }}" id="status" name="status" required>
                                            <option value="active" @selected(old('status', $admin->status ?? 'active') === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status', $admin->status ?? '') === 'inactive')>Suspended</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="modern-admin-label" for="password">Password</label>
                                        <input type="password" class="form-control form-control-{{ formControlSize() }}" id="password" name="password" placeholder="{{ $isEdit ? 'Leave blank to keep current password' : 'Optional, uses default if empty' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="modern-admin-card card h-100">
                            <div class="card-header">
                                <h3>Assign roles</h3>
                                <p>Select the roles this admin should inherit.</p>
                            </div>
                            <div class="card-body">
                                <div class="modern-check-grid">
                                    @foreach($roles as $role)
                                        <label class="modern-check-item form-check">
                                            <input
                                                class="form-check-input form-check-input-{{ checkBoxControlSize() }}"
                                                type="checkbox"
                                                name="roles[]"
                                                value="{{ $role->id }}"
                                                @checked(in_array($role->id, $selectedRoles, true))
                                            >
                                            <span class="form-check-label">{{ $role->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="modern-admin-footer mt-4">
                            <button class="btn btn-admin-submit" type="submit">{{ $isEdit ? 'Update admin' : 'Save admin' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
