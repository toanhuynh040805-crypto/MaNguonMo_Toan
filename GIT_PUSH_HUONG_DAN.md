# Đưa OpenShop lên repository GitHub

Repository đích: https://github.com/toanhuynh040805-crypto/MaNguonMo_Toan

## Cách A: Mở quyền ghi cho ứng dụng GitHub trong ChatGPT

GitHub hiện trả lỗi `403 Resource not accessible by integration`, do repository mới chưa được chọn trong GitHub App installation. Đăng nhập GitHub, mở https://github.com/settings/installations/163668750, chọn **Configure**, tại **Repository access** bổ sung `MaNguonMo_Toan` rồi **Save**. Khi đã cấp quyền, có thể yêu cầu ChatGPT đẩy mã nguồn lên nhánh `main` qua GitHub connector.

## Cách B: Tự push bằng Git (chỉ dùng khi bạn muốn thao tác trên máy)

1. Tải ZIP và giải nén toàn bộ (các file `app`, `bootstrap`, `config`, `database`, `docs`, `public`, `resources`, `routes`, `tests`, `.github`, `composer.json`, `.env.example`... phải nằm trực tiếp ở thư mục gốc).
2. Trong terminal tại thư mục đã giải nén, chạy:

```powershell
git init
git branch -M main
git remote add origin https://github.com/toanhuynh040805-crypto/MaNguonMo_Toan.git
git add .
git commit -m "feat: complete OpenShop Laravel ecommerce coursework"
git push -u origin main
```

Không chạy `git push --force`. Nếu báo remote có commit, cần fetch/merge hoặc dùng cách clone repository trước khi chép file. Không đưa `.env`, mật khẩu thật hoặc `vendor/` lên GitHub.

Sau khi push, cài đặt theo `README.md`.
