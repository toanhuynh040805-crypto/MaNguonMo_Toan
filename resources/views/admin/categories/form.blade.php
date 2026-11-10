@extends('layouts.admin')
@section('title', $category->exists ? 'Sửa danh mục' : 'Thêm danh mục')
@section('admin-content')
<a class="text-decoration-none small" href="{{ route('admin.categories.index') }}">← Quay lại</a><h1 class="section-title mt-2 mb-4">{{ $category->exists ? 'Sửa danh mục' : 'Thêm danh mục' }}</h1>
<div class="panel p-4 p-lg-5"><form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">@csrf @if($category->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label fw-semibold" for="name">Tên danh mục *</label><input id="name" name="name" class="form-control form-control-lg" value="{{ old('name', $category->name) }}" maxlength="150" required></div>
    <div class="mb-4"><label class="form-label fw-semibold" for="description">Mô tả</label><textarea id="description" name="description" class="form-control" rows="4" maxlength="1500">{{ old('description', $category->description) }}</textarea></div>
    <button class="btn btn-primary px-4">{{ $category->exists ? 'Lưu thay đổi' : 'Thêm danh mục' }}</button>
</form></div>
@endsection
