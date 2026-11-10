# KỊCH BẢN DEMO OPENSHOP – ĐỒ ÁN LẬP TRÌNH MÃ NGUỒN MỞ

> Tài liệu hướng dẫn trình diễn. Không giả định kết quả đạt trên máy của nhóm trước khi thử thật.

## Chuẩn bị

1. Khởi động **MySQL** trong Laragon, mở VS Code ở thư mục `E:\MaNguonMo\MaNguonMo_Toan`.
2. Cấu hình `.env` cho MySQL và email/mật khẩu Admin demo (không đưa mật khẩu vào slide).
3. Chạy `php artisan migrate` (không cần chạy `migrate:fresh`), `php artisan serve`.
4. Truy cập `http://127.0.0.1:8000`. Sản phẩm mẫu được tạo bằng `php artisan db:seed` nếu chưa có.
5. Mở **hai cửa sổ ẩn danh/trình duyệt khác nhau** nếu muốn mô phỏng Admin và Customer cùng lúc.

## Luồng demo khoảng 8–12 phút

| Thứ tự | Vai trò | Thao tác | Điểm cần trình bày |
|---|---|---|---|
| 1 | Guest | Trang chủ, tìm kiếm, lọc danh mục, sắp xếp giá, chuyển trang | Eloquent, Validation, Pagination |
| 2 | Guest | Đăng ký tài khoản | Laravel Auth, băm mật khẩu, mặc định role Customer |
| 3 | Customer | Cập nhật tên/email và đổi mật khẩu (có kiểm tra mật khẩu cũ) | Profile, Validation, Authorization |
| 4 | Customer | Mở chi tiết sản phẩm, nhấn + thêm vào giỏ | AJAX fetch / cập nhật số lượng, fallback POST |
| 5 | Customer | Chỉnh số lượng, kiểm tra giá và tồn kho | Session cart, server validation |
| 6 | Customer | Đặt hàng COD, xem trang chi tiết đơn | Transaction, Order/OrderItem, snapshot giá, giảm tồn kho |
| 7 | Admin | Dashboard, xem số đơn chờ, sản phẩm sắp hết | Eloquent aggregate, báo cáo |
| 8 | Admin | CRUD một danh mục mới, sản phẩm mới, upload ảnh | Hai CRUD trên các bảng có quan hệ |
| 9 | Admin | Tìm/lọc đơn và Xuất CSV | Filter + stream download, chống CSV injection |
| 10 | Admin | Mở đơn vừa tạo và chuyển `pending → confirmed → shipping → completed` | Ràng buộc trạng thái |
| 11 | Customer | Quay lại lịch sử đơn | Chỉ xem đơn của chính mình |
| 12 | Admin | Tạo một đơn mới để demo hủy khi `pending` hoặc `confirmed` | Hoàn kho đúng một lần |

## Tình huống đặc biệt nên demo

- Customer truy cập `/admin` hoặc `/admin/orders/export`: HTTP 403.
- Admin sửa tồn kho còn 0: sản phẩm vẫn hiển thị nhưng không cho đặt.
- Customer thêm giỏ vượt tồn: trả lỗi, không ghi đơn.
- Admin cố xóa sản phẩm đang thuộc đơn chưa kết thúc: bị chặn; có thể chuyển **Hiển thị để bán** thành tắt.
- Thay thông tin giá hoặc tồn kho giữa lúc thêm giỏ và xác nhận: server kiểm tra lại số lượng trước khi ghi đơn.
- Admin không thể nhảy trạng thái từ `pending` sang `completed`.

## Chụp ảnh cho báo cáo

Chụp trang chủ, bộ lọc, đăng ký/đăng nhập, tài khoản, giỏ hàng, checkout, đơn cá nhân, Dashboard, CRUD danh mục, CRUD sản phẩm kèm ảnh, quản lý đơn, kết quả xuất CSV và thông báo lỗi phân quyền. Tên hình nên thống nhất theo định dạng `Hình 4.x – ...`.

## Kiểm tra trước khi nộp

- `php artisan route:list`
- `php artisan migrate:status`
- `php artisan test` (cần PHP extensions `pdo_sqlite`, `sqlite3`, `gd`, xem file `phpunit.xml`)
- Tab **Actions** trên GitHub cho commit đang chuẩn bị báo cáo
- Chạy trên trình duyệt Chrome desktop và chế độ màn hình mobile (F12 → Toggle device toolbar)
- Tuyệt đối không commit file `.env`, mật khẩu hoặc dữ liệu khách thật.
