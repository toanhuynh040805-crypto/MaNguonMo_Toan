# OpenShop – Website Bán hàng Online (Laravel 12 + MySQL)

> Đồ án môn **Lập trình mã nguồn mở** – Đề tài 03 – HUIT. Mã nguồn phục vụ học tập.

## Chức năng

- **Khách:** đăng ký, đăng nhập, đăng xuất; xem/tìm/lọc/sắp xếp sản phẩm, phân trang.
- **Customer:** quản lý giỏ hàng trong session (thêm nhanh bằng AJAX, vẫn có form POST dự phòng); đặt hàng COD; lọc đơn cá nhân, in phiếu COD và xem lịch sử xử lý; hủy đơn chờ xác nhận; cập nhật tài khoản, đổi mật khẩu.
- **Admin:** dashboard doanh thu 6 tháng theo ngày hoàn tất, cảnh báo tồn kho; CRUD danh mục và sản phẩm (có upload ảnh); tìm kiếm, phân trang, lọc tồn kho; quản lý đơn theo trạng thái/ngày đặt, in phiếu và xuất CSV theo bộ lọc.
- **Dữ liệu:** Eloquent ORM, 6 bảng nghiệp vụ gồm `users`, `categories`, `products`, `orders`, `order_items`, `order_status_histories`; Migration, Seeder, Validation, Middleware.
- **An toàn cơ bản:** CSRF, băm mật khẩu, không cho tự đăng ký Admin, chỉ xem đơn của chính mình, khóa hàng khi đặt và hoàn tồn kho một lần khi hủy; chặn xóa sản phẩm thuộc đơn chưa kết thúc; giảm rủi ro CSV injection.

Sơ đồ ERD, Use Case, kiến trúc, báo cáo, test case, kịch bản demo và phân công nhóm nằm trong `docs/`.

## ĐÃ CLONE VÀ CHẠY ĐƯỢC WEBSITE? CHỈ CẦN CẬP NHẬT

Mở **Terminal của VS Code** trong thư mục hiện tại (ví dụ `E:\MaNguonMo\MaNguonMo_Toan`), **không clone lại**:

```powershell
git status
git pull origin main
php artisan optimize:clear
php artisan migrate
php artisan storage:link
php artisan serve
```

- Nếu `php artisan serve` đang chạy, nhấn `Ctrl + C` ở Terminal chạy server trước khi khởi động lại.
- Nếu `storage:link` báo `The [public/storage] link already exists`, có thể bỏ qua.
- **KHÔNG chạy `migrate:fresh`**: lệnh đó xóa bảng và dữ liệu hiện có. Mã cập nhật lần này không cần tạo database mới và bổ sung migration cho `completed_at` và bảng `order_status_histories`. Chạy `php artisan migrate` để bổ sung, không xóa dữ liệu cũ.
- Nếu `git status` hiện có tệp đang sửa, hãy kiểm tra và lưu/commit/stash thay đổi trước khi pull; **không đưa `.env` lên Git**.

## Cài đặt nhanh trên Windows (Laragon)

**Yêu cầu:** PHP >= 8.2, Composer, MySQL (Laragon), các PHP extensions phổ biến của Laravel (`pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, ...). GitHub chỉ lưu mã nguồn, **không** lưu thư mục `vendor/` hoặc `.env`.

1. Khởi chạy Laragon (MySQL). Mở Terminal:

   ```powershell
   cd C:\laragon\www
   git clone https://github.com/toanhuynh040805-crypto/MaNguonMo_Toan.git OpenShop
   cd OpenShop
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

2. Trong HeidiSQL/phpMyAdmin, chạy:

   ```sql
   CREATE DATABASE openshop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Chỉnh `.env`:

   ```dotenv
   APP_NAME=OpenShop
   APP_URL=http://127.0.0.1:8000
   APP_ENV=local
   APP_DEBUG=true
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=openshop_db
   DB_USERNAME=root
   DB_PASSWORD=
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   SEED_ADMIN_EMAIL=admin@example.test
   SEED_ADMIN_PASSWORD="TU_DAT_MAT_KHAU_RIENG_TOI_THIEU_12_KY_TU"
   ```

   **Quan trọng:** Tự thay mật khẩu Admin bằng mật khẩu mạnh (tối thiểu 12 ký tự); không dùng giá trị ví dụ. Nếu MySQL của bạn dùng mật khẩu, chỉnh `DB_PASSWORD`. Không đưa `.env` lên GitHub.

4. Chạy:

   ```powershell
   php artisan config:clear
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

5. Mở **http://127.0.0.1:8000**. Đăng nhập Admin bằng email/mật khẩu đã tự đặt trong `.env`; tài khoản Customer tạo qua **Đăng ký**.

**Chạy lại Seeder không tự đổi mật khẩu Admin đã có**. Nếu muốn thay mật khẩu, hãy dùng chức năng đổi mật khẩu sau khi đăng nhập.

**Nếu Seeder chưa tạo Admin:** kiểm tra hai biến `SEED_ADMIN_EMAIL` / `SEED_ADMIN_PASSWORD` rồi chạy `php artisan db:seed`.

## Kiểm thử

```powershell
php artisan test
```

Bộ kiểm thử sử dụng database SQLite in-memory theo cấu hình PHPUnit; cần extension PHP `pdo_sqlite`, `sqlite3`, `gd` (bài test upload ảnh) và `fileinfo`. GitHub Actions chạy kiểm thử tự động khi push. **Chưa khẳng định kiểm thử thực tế trên máy Windows/Laragon** nếu chưa chạy các lệnh trên.

## Quy trình demo

1. Customer đăng ký/đăng nhập → tìm, lọc sản phẩm → thêm giỏ → sửa số lượng → đặt hàng COD.
2. Customer xem đơn vừa đặt, lọc và in phiếu COD; Admin xác nhận → giao hàng → hoàn thành; kiểm tra nhật ký xử lý và doanh thu 6 tháng.
3. Tạo đơn mới; Customer hủy đơn chờ xác nhận → kiểm tra tồn kho được hoàn lại.
4. Admin CRUD danh mục/sản phẩm, tìm kiếm, phân trang, theo dõi tồn kho và xuất đơn hàng CSV.
5. Customer vào **Tài khoản** sửa thông tin, đổi mật khẩu; thử đường dẫn `/admin` bằng Customer để kiểm tra phân quyền.
6. Xem kịch bản 8–12 phút trong `docs/08_Kich_ban_demo_hoan_chinh.md`.

## Lưu ý triển khai

- **Firebase Hosting tĩnh không chạy được backend Laravel/MySQL.** Cần host PHP + MySQL, document root trỏ đến `public/`, `APP_DEBUG=false`, HTTPS và không công khai `.env`.
- Bootstrap được tải qua CDN; máy cần mạng để tải phần giao diện mặc định.
- Thanh toán COD phục vụ demo; không có cổng thanh toán hoặc vận chuyển thật.
- Có thể đọc đối chiếu yêu cầu, bản mô tả chức năng, Use Case và ERD trong thư mục `docs/`.

## Nguồn tham khảo

- [Laravel 12 documentation](https://laravel.com/docs/12.x)
- Quy định đánh giá môn Lập trình mã nguồn mở, Khoa CNTT HUIT, ngày 06/08/2026.
- Đề cương học phần Lập trình mã nguồn mở của HUIT.


## Khắc phục lỗi thường gặp trên máy Windows

- **ERR_CONNECTION_REFUSED tại 127.0.0.1:8000**: kiểm tra Terminal VS Code còn chạy \`php artisan serve\` hay không; chạy lại nếu server đã dừng.
- **\`mysql\` is not recognized trong PowerShell**: đây là lỗi PATH của MySQL CLI, không nhất thiết là lỗi MySQL. Bạn có thể dùng **Laragon → Start All → Database (HeidiSQL)** để tạo \`openshop_db\`. Laravel kết nối qua PDO/MySQL nên không bắt buộc CLI \`mysql\` có trong PATH.
- **\`Nothing to migrate\`**: mọi migration đã chạy, không phải lỗi.
- **\`composer.lock\` untracked**: đây là tệp lock do Composer sinh trên máy local, nên được commit lên Git để khóa phiên bản gói trước khi triển khai nhóm; không xóa \`vendor\` chỉ để xử lý cảnh báo này.
- **Lỗi SQLite khi chạy test tại Windows**: bật \`pdo_sqlite\` và \`sqlite3\` trong PHP CLI hoặc xem kết quả test tương ứng trên GitHub Actions.
- **Trang vẫn như trước sau \`git pull\`**: chạy \`php artisan optimize:clear\`, tải lại Chrome bằng Ctrl+F5; đảm bảo server đang chạy từ đúng thư mục dự án.

## Kiểm tra sau mỗi lần cập nhật

\`\`\`powershell
cd E:\MaNguonMo\MaNguonMo_Toan
git pull origin main
php artisan optimize:clear
php artisan migrate
php artisan test --filter=OnlineShopTest
php artisan serve
\`\`\`

**Chú ý:** Giữ Terminal chạy \`php artisan serve\`; mở Terminal thứ hai nếu cần chạy thêm lệnh. Dữ liệu sản phẩm và tài khoản trong MySQL được giữ nguyên khi dùng \`php artisan migrate\`.
