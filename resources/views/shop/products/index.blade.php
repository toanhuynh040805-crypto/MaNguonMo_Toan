@extends('layouts.app')
@section('title', 'Cửa hàng')
@section('content')
<section class="container pb-3">
    <div class="hero d-flex align-items-center mb-4 mb-md-5">
        <div class="hero-content p-4 p-md-5">
            <span class="eyebrow">CÔNG NGHỆ CHO MỖI NGÀY</span>
            <h1 class="display-5 fw-bold lh-sm mt-3 mb-3">Mọi điều bạn cần.<br><span class="hero-gradient">Trong một nơi.</span></h1>
            <p class="hero-subtitle mb-4">Khám phá phụ kiện công nghệ hữu ích cho học tập, công việc và giải trí.</p>
            <a href="#danh-sach" class="btn btn-light rounded-pill fw-bold px-4 py-3">Khám phá sản phẩm ↗</a>
        </div>
        <div class="hero-visual" aria-hidden="true"><div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div><img src="{{ asset('images/hero-tech.svg') }}" class="hero-product-image" alt="" loading="eager" decoding="async"></div>
    </div>
</section>
<section class="container pb-5" id="danh-sach">
    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-end mb-3">
        <div><div class="eyebrow text-primary">KHÁM PHÁ OPENSHOP</div><h2 class="section-title mb-0">Sản phẩm nổi bật</h2></div>
        <span class="small text-muted">{{ $products->total() }} sản phẩm</span>
    </div>
    <form method="GET" action="{{ route('home') }}" class="row g-2 filter-bar mb-4 align-items-center">
        <div class="col-md-5"><label class="visually-hidden" for="q">Tìm kiếm</label><input id="q" class="form-control form-control-lg" name="q" value="{{ request('q') }}" placeholder="🔍  Tìm tên sản phẩm..."></div>
        <div class="col-md-3"><label class="visually-hidden" for="category">Danh mục</label><select id="category" class="form-select form-select-lg" name="category"><option value="">Tất cả danh mục</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="visually-hidden" for="sort">Sắp xếp</label><select id="sort" class="form-select form-select-lg" name="sort"><option value="latest" @selected(request('sort', 'latest') === 'latest')>Mới nhất</option><option value="price_asc" @selected(request('sort') === 'price_asc')>Giá tăng dần</option><option value="price_desc" @selected(request('sort') === 'price_desc')>Giá giảm dần</option></select></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary btn-lg">Lọc sản phẩm</button></div>
    </form>
    <div class="row g-3 g-md-4">
        @forelse($products as $product)
            <div class="col-6 col-lg-4"><x-product-card :product="$product" /></div>
        @empty
            <div class="col-12"><div class="empty-state"><div class="display-5 mb-2">🔎</div><h3 class="h5">Không tìm thấy sản phẩm</h3><p class="text-muted">Hãy thử từ khóa hoặc danh mục khác.</p><a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill">Xóa bộ lọc</a></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</section>
@endsection
