# BÁO CÁO ĐỒ ÁN - BẢN NHÁP NỘI DUNG

**Đề tài:** Xây dựng Website Bán hàng Online (phiên bản đơn giản) bằng Laravel và MySQL.

> Chưa phải báo cáo Word/PDF cuối cùng. Nhóm cần điền thông tin thật, chụp giao diện sau khi chạy, ghi kết quả test thực tế và định dạng theo mẫu giảng viên yêu cầu.

## PHẦN ĐẦU

- Trang bìa: trường, khoa, tên học phần, tên đề tài, giảng viên, nhóm, lớp, MSSV, năm.
- Lời cảm ơn (theo quy định lớp).
- Mục lục, danh mục hình và bảng.
- Bảng phân công công việc và mức độ đóng góp của từng sinh viên.

## CHƯƠNG 1. GIỚI THIỆU ĐỀ TÀI

### 1.1. Lý do chọn đề tài

Nhu cầu quản lý danh mục, sản phẩm, đơn hàng và thông tin khách hàng có thể được đáp ứng bằng một website bán hàng trực tuyến. Khi học Laravel, nhóm lựa chọn mô hình cửa hàng phụ kiện công nghệ nhằm thực hành phát triển ứng dụng web động, thiết kế cơ sở dữ liệu quan hệ, xây dựng CRUD và tổ chức phân quyền người dùng.

### 1.2. Mục tiêu

Phát triển website có hai vai trò Admin và Customer. Customer xem, tìm kiếm sản phẩm, đặt hàng và xem các đơn của bản thân. Admin quản lý danh mục, sản phẩm, tồn kho và trạng thái đơn hàng. Website sử dụng Laravel, Eloquent ORM, Migration, Seeder, Blade Template, Bootstrap và MySQL.

### 1.3. Phạm vi

Đồ án thực hiện hình thức thanh toán khi nhận hàng (COD) ở mức mô phỏng; chưa có thanh toán trực tuyến, tích hợp với hãng vận chuyển, email marketing hay quản lý nhiều kho. Giỏ hàng lưu trong Session của tài khoản đang truy cập, không phải giỏ hàng đồng bộ nhiều thiết bị.

## CHƯƠNG 2. CƠ SỞ LÝ THUYẾT VÀ CÔNG NGHỆ

### 2.1. PHP và Laravel

PHP là ngôn ngữ lập trình phía máy chủ. Laravel là framework PHP hỗ trợ cấu trúc MVC, Router, Controller, Eloquent ORM, Migration, Authentication và Middleware. Đồ án tổ chức xử lý theo nhóm chức năng trong Controller, truy cập dữ liệu bằng Model và hiển thị bằng Blade.

### 2.2. MySQL và Eloquent ORM

MySQL lưu trữ dữ liệu dạng quan hệ. Các bảng users, categories, products, orders, order_items được tạo bằng Migration. Eloquent cung cấp các quan hệ một-nhiều và thuộc-về để truy xuất thông tin danh mục, sản phẩm và đơn hàng.

### 2.3. Blade và Bootstrap

Blade giúp tạo trang HTML động và tái sử dụng giao diện bằng layout, component. Bootstrap hỗ trợ bố cục responsive, form, bảng, button, navigation và phân trang.

### 2.4. Bảo mật và xác thực cơ bản

Laravel Auth dùng phiên đăng nhập; Middleware giới hạn truy cập trang Admin/Customer; CSRF token bảo vệ các form thay đổi dữ liệu; Validation kiểm tra dữ liệu người dùng; xử lý đơn hàng dùng transaction và khóa dòng khi kiểm tra tồn kho.

## CHƯƠNG 3. PHÂN TÍCH VÀ THIẾT KẾ

### 3.1. Tác nhân và nghiệp vụ

**Khách vãng lai:** tìm kiếm/xem sản phẩm, đăng ký, đăng nhập.

**Customer:** quản lý giỏ hàng, đặt hàng COD, xem và hủy đơn đang chờ xác nhận.

**Admin:** CRUD danh mục/sản phẩm, theo dõi đơn, cập nhật trạng thái, xem tổng quan.

### 3.2. Sơ đồ Use Case

Xuất ảnh từ mã `03_UseCase.puml` và chèn hình minh họa (không sử dụng lại ảnh của dự án khác).

### 3.3. Mô hình dữ liệu ERD

Xuất ảnh từ `02_ERD.mmd`. Gồm năm bảng nghiệp vụ bổ sung cho các bảng mặc định của Laravel:

- `users`: người dùng và vai trò.
- `categories`: nhóm sản phẩm.
- `products`: sản phẩm, đơn giá VND, tồn kho, ảnh, trạng thái.
- `orders`: thông tin giao hàng, người mua, tổng tiền, trạng thái.
- `order_items`: từng dòng hàng, số lượng, giá và thành tiền tại thời điểm mua.

**Mối quan hệ:** Category (1)–(N) Product; User (1)–(N) Order; Order (1)–(N) OrderItem; Product (0..1)–(N) OrderItem vì sản phẩm có thể bị xóa nhưng thông tin mua vẫn được lưu.

### 3.4. Luồng đặt hàng

1. Customer đăng nhập, chọn sản phẩm và số lượng.
2. Website kiểm tra tình trạng sản phẩm rồi thêm vào Session cart.
3. Customer kiểm tra giỏ hàng và nhập thông tin nhận hàng.
4. Server mở transaction, khóa sản phẩm, xác thực lại tồn kho và tạo Order, OrderItem.
5. Server trừ số lượng tồn kho; khi thành công chuyển đến trang chi tiết đơn.
6. Admin xác nhận → đang giao → hoàn tất; hủy đơn hợp lệ sẽ hoàn lại tồn kho.

## CHƯƠNG 4. XÂY DỰNG VÀ CÀI ĐẶT HỆ THỐNG

### 4.1. Môi trường và công nghệ

- Windows + Laragon + VS Code.
- Laravel 12, PHP phiên bản tương thích, MySQL/MariaDB.
- Eloquent ORM, Migration, Seeder, Blade Template, Bootstrap 5.

### 4.2. Các giao diện cần chụp trong báo cáo

1. Trang chủ với tìm kiếm/lọc/phân trang.
2. Trang chi tiết sản phẩm.
3. Đăng ký và đăng nhập.
4. Giỏ hàng và form đặt hàng.
5. Lịch sử và chi tiết đơn hàng Customer.
6. Dashboard quản trị.
7. CRUD danh mục.
8. CRUD sản phẩm và upload ảnh.
9. Quản lý đơn và chuyển trạng thái.

*Mỗi hình cần chú thích “Hình X.Y – …” và 2–4 câu mô tả nghiệp vụ được trình bày. Chỉ chèn ảnh chụp thật sau khi đã chạy ứng dụng.*

## CHƯƠNG 5. KIỂM THỬ VÀ ĐÁNH GIÁ

- Dựa trên các test case trong `05_Test_case.md`.
- Ghi cột kết quả thực tế: Pass/Fail và ảnh minh chứng.
- Kiểm tra tách biệt quyền Admin/Customer.
- Kiểm tra đặt hàng thành công, vượt tồn, lịch sử đơn không bị lộ giữa các Customer.
- Kiểm tra responsive trên máy tính/điện thoại.
- Kiểm tra form validation và thông báo lỗi.

## KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN (BẢN NHÁP)

Website được thiết kế để đáp ứng những chức năng cơ bản của một cửa hàng bán hàng trực tuyến: quản lý danh mục, sản phẩm, giỏ hàng, đơn hàng và vai trò người dùng. Mã nguồn sử dụng Laravel theo hướng MVC, kết nối cơ sở dữ liệu quan hệ qua Eloquent ORM, xây dựng giao diện Blade và Bootstrap. Sau khi triển khai, nhóm cần đánh giá độ ổn định thông qua việc chạy các ca kiểm thử cụ thể. Trong tương lai, có thể mở rộng bằng thanh toán trực tuyến, tích hợp dịch vụ vận chuyển, đánh giá sản phẩm, mã giảm giá và trang thống kê chuyên sâu.

## TÀI LIỆU THAM KHẢO

1. Khoa CNTT, HUIT, *Quy định giảng dạy và đánh giá môn học Lập trình mã nguồn mở*, 06/08/2026.
2. Khoa CNTT, HUIT, *Đề cương học phần Lập trình mã nguồn mở*, phiên bản người học cung cấp.
3. Laravel, *Laravel 12 Documentation*, https://laravel.com/docs/12.x (cần cập nhật ngày truy cập khi hoàn thiện báo cáo).
4. Bootstrap, *Bootstrap 5 Documentation*, https://getbootstrap.com/docs/5.3/.
