@extends('layouts.admin')
@section('title', 'Quản lý danh mục')
@section('admin-content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"><h1 class="section-title mb-0">Quản lý danh mục</h1><a class="btn btn-primary" href="{{ route('admin.categories.create') }}">+ Thêm danh mục</a></div>
<form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex gap-2 mb-3"><input name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm danh mục..." aria-label="Tìm danh mục"><button class="btn btn-outline-primary">Tìm kiếm</button></form>
<div class="panel overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>#</th><th>Danh mục</th><th>Đường dẫn</th><th>Số sản phẩm</th><th>Thao tác</th></tr></thead><tbody>
@forelse($categories as $category)
<tr><td>{{ $category->id }}</td><td class="fw-semibold">{{ $category->name }}</td><td class="text-muted">{{ $category->slug }}</td><td>{{ $category->products_count }}</td><td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.categories.edit', $category) }}">Sửa</a><form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Xóa danh mục này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button></form></div></td></tr>
@empty<tr><td colspan="5" class="text-center py-4 text-muted">Không có dữ liệu.</td></tr>@endforelse
</tbody></table></div></div><div class="mt-4">{{ $categories->links() }}</div>
@endsection
