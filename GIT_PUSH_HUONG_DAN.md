# Hướng dẫn làm việc với GitHub – OpenShop

Repository đồ án: https://github.com/toanhuynh040805-crypto/MaNguonMo_Toan

**Trạng thái:** Mã nguồn website bán hàng Laravel 12 đã được đưa lên nhánh `main`. Không cần tải ZIP để push lại.

## 1. Clone về máy Windows / Laragon

Mở PowerShell hoặc Terminal trong Laragon:

```powershell
cd C:\laragon\www
git clone https://github.com/toanhuynh040805-crypto/MaNguonMo_Toan.git OpenShop
cd OpenShop
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Tạo cơ sở dữ liệu `openshop_db` trong MySQL, đặt thông tin kết nối và mật khẩu Admin trong `.env`, rồi chạy:

```powershell
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Mở http://127.0.0.1:8000. Xem **README.md** để biết hướng dẫn cấu hình đầy đủ và các bước kiểm thử.

## 2. Cập nhật khi làm việc nhóm

```powershell
git pull origin main
# chỉnh sửa và kiểm thử
php artisan test --filter=OnlineShopTest
git add .
git commit -m "feat: mo ta thay doi"
git push origin main
```

Khi nhiều thành viên cùng làm, nên tạo nhánh riêng và Pull Request thay vì sửa trực tiếp `main`.

## 3. Lưu ý bảo mật

- Không commit tệp `.env`, mật khẩu, token, thông tin khách hàng thật hoặc thư mục `vendor/`.
- Chỉ chạy `php artisan migrate:fresh --seed` khi chủ động muốn xóa dữ liệu cũ.
- Firebase Hosting tĩnh không hỗ trợ trực tiếp backend Laravel/MySQL; cần máy chủ PHP và MySQL.

## 4. Kiểm thử

Workflow `.github/workflows/tests.yml` được cấu hình chạy khi push/Pull Request; xem kết quả trong tab **Actions** của repository.
