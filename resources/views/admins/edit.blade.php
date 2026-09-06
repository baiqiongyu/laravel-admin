@extends('layouts.admin')

@section('title', '编辑管理员')
@section('page-title', '编辑管理员')

@section('content')
    <div class="card" style="max-width: 680px;">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square" style="color: #4f46e5;"></i>
            编辑管理员：{{ $admin->username }}
        </div>
        <div class="card-body p-4">
            @include('admins.form', [
                'admin'          => $admin,
                'roles'          => $roles,
                'checkedRoleIds' => $checkedRoleIds,
            ])
        </div>
    </div>
@endsection
