@extends('layouts.app')
@section('title', 'Nguồn ảnh sản phẩm')
@section('content')
<div class="container pb-5">
    <div class="mb-4"><span class="eyebrow text-primary">MINH BẠCH NGUỒN ẢNH</span>
        <h1 class="section-title">Ảnh chụp sản phẩm thật và tác giả</h1>
        <p class="text-muted">Ảnh được cung cấp bởi các tác giả trên Wikimedia Commons theo giấy phép được ghi rõ bên dưới. Đây là ảnh chụp thiết bị cùng loại dùng cho đồ án, không phải ảnh giả lập hoặc do AI tạo ra.</p>
        <div class="alert alert-info">Tên, giá và thông số trong danh mục OpenShop là <strong>dữ liệu mẫu</strong>. Ảnh chụp có thể là sản phẩm của thương hiệu hoặc dung lượng khác, không chứng minh đặc tính kỹ thuật của sản phẩm đang được mô phỏng.</div>
    </div>
    <div class="table-responsive panel">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Sản phẩm demo</th><th>Ảnh chụp thực tế</th><th>Tác giả</th><th>Giấy phép</th></tr></thead>
            <tbody>
            @foreach(\App\Support\RealProductPhotos::all() as $photo)
            <tr>
                <td>{{ $photo['product'] }}</td>
                <td><a href="{{ $photo['source_url'] }}" target="_blank" rel="noopener noreferrer">{{ $photo['file'] }}</a></td>
                <td>{{ $photo['author'] }}</td>
                <td><a href="{{ $photo['license_url'] }}" target="_blank" rel="noopener noreferrer">{{ $photo['license'] }}</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p class="text-muted small mt-3">Bản ảnh tối ưu hóa (WebP) có thể được tạo từ ảnh nguồn bằng script tải ảnh, có thay đổi kích thước và định dạng. Vui lòng giữ phần ghi công nếu sao chép hoặc phân phối lại ảnh; các tệp ảnh chịu giấy phép riêng của tác giả.</p>
</div>
@endsection
