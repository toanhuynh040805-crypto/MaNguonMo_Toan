<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Phiếu đơn hàng {{ $order->code }} – OpenShop</title>
    <style>
        :root { font-family: Arial, "Segoe UI", sans-serif; color: #182039; }
        body { margin: 0; background: #f5f7fb; }
        main { max-width: 790px; background: #fff; margin: 32px auto; padding: 36px; box-shadow: 0 8px 36px #17234a14; }
        h1 { font-size: 28px; margin: 0; }
        h2 { font-size: 16px; margin: 30px 0 12px; }
        p { line-height: 1.5; }
        .muted { color: #64748b; font-size: 13px; }
        .row { display: flex; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
        .brand { font-weight: 900; color: #5d47d9; font-size: 24px; }
        table { border-collapse: collapse; width: 100%; margin-top: 18px; }
        th, td { text-align: left; padding: 12px 8px; border-bottom: 1px solid #e2e8f0; }
        th { font-size: 12px; color: #64748b; }
        .right { text-align: right; }
        .total { font-size: 20px; font-weight: 800; color: #4338ca; }
        .actions { display: flex; justify-content: end; gap: 12px; margin: 0 auto 16px; max-width: 862px; padding-top: 22px; }
        button { border: 0; border-radius: 9px; padding: 12px 20px; background: #4f46e5; color: #fff; cursor: pointer; }
        .notice { padding: 14px; background: #f1f5f9; border-radius: 10px; font-size: 13px; }
        @media print {
            body { background: white; }
            main { box-shadow: none; margin: 0; padding: 12px 0; max-width: 100%; }
            .actions { display: none; }
            @page { margin: 14mm; size: A4; }
        }
    </style>
</head>
<body>
    <div class="actions"><button onclick="window.print()">In / Lưu PDF</button></div>
    <main>
        <header class="row">
            <div><div class="brand">OpenShop.</div><p class="muted">Website bán phụ kiện công nghệ · Bản demo học phần</p></div>
            <div class="right"><h1>PHIẾU ĐƠN HÀNG</h1><p class="muted">Mã: {{ $order->code }}<br>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p></div>
        </header>
        <div class="notice">Phiếu xác nhận thông tin đơn hàng, <strong>không phải hóa đơn thuế hoặc chứng từ đã thanh toán</strong>. Phương thức: COD (thanh toán khi nhận).</div>
        <section class="row">
            <div><h2>Người nhận</h2><p>{{ $order->recipient_name }}<br>{{ $order->phone }}<br>{{ $order->address }}</p></div>
            <div><h2>Trạng thái</h2><p>{{ $order->status_label }}</p><p class="muted">{{ $order->note ?: 'Không có ghi chú' }}</p></div>
        </section>
        <h2>Sản phẩm</h2>
        <table>
            <thead><tr><th>Sản phẩm</th><th class="right">Đơn giá</th><th class="right">SL</th><th class="right">Thành tiền</th></tr></thead>
            <tbody>
            @foreach($order->items as $item)
                <tr><td>{{ $item->product_name }}</td><td class="right">{{ number_format($item->price, 0, ',', '.') }}₫</td><td class="right">{{ $item->quantity }}</td><td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}₫</td></tr>
            @endforeach
            </tbody>
            <tfoot><tr><td colspan="3" class="right"><strong>Tổng giá trị đơn hàng</strong></td><td class="right total">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td></tr></tfoot>
        </table>
        <p class="muted" style="margin-top:38px">OpenShop · Tài liệu in phục vụ việc đối chiếu đơn hàng trong hệ thống demo.</p>
    </main>
</body>
</html>
