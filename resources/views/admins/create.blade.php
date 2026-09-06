@extends('layouts.admin')

@section('title', '新增管理员')
@section('page-title', '新增管理员')

@section('content')
    <div class="card" style="max-width: 680px;">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-person-plus" style="color: #4f46e5;"></i> 新增管理员
        </div>
        <div class="card-body p-4">
            {{-- include 引入共用表单，传入变量 --}}
            @include('admins.form', ['admin' => null, 'roles' => $roles])
        </div>
    </div>
@endsection
