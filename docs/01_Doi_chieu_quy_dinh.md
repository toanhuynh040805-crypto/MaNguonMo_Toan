# ĐỐI CHIẾU YÊU CẦU / RUBRIC - ĐỀ TÀI SỐ 3

## 1. Các yêu cầu trực tiếp từ quy định giảng dạy ngày 06/08/2026

Văn bản: **Quy định về giảng dạy và đánh giá môn học Lập trình mã nguồn mở**, trang 3–5.

| Điều kiện | Cách triển khai trong OpenShop | Mức độ |
|---|---|---|
| Nhóm 3–5 sinh viên | Tài liệu `06_Phan_cong_nhom.md` | Cần nhóm ghi tên thực tế |
| Laravel + MySQL | Migration Eloquent + `.env` MySQL | Có mã nguồn |
| Eloquent ORM, Migration, Seeder | `app/Models`, `database/migrations`, `database/seeders` | Có mã nguồn |
| Blade + Bootstrap | `resources/views` và CDN Bootstrap | Có mã nguồn |
| Ít nhất 2 bảng CRUD có quan hệ | `categories`, `products` | Có mã nguồn |
| Đăng nhập, đăng xuất, phân quyền | `AuthController`, `EnsureRole`, route groups | Có mã nguồn |
| Tìm kiếm và phân trang | Trang sản phẩm công khai, trang admin | Có mã nguồn |
| Validation dữ liệu | Các request validate trong Controller | Có mã nguồn |
| ERD, Use Case | `02_ERD.mmd`, `03_UseCase.puml` | Có mã sơ đồ |
| Mô tả chức năng | `04_Bao_cao_do_an.md` | Bản nháp |
| Ảnh chụp màn hình báo cáo | Chụp sau khi cài và chạy website | Chưa có |
| Phân công rõ ràng | `06_Phan_cong_nhom.md` | Mẫu, cần điền |

## 2. Đề tài số 3 nguyên gốc và phạm vi triển khai

- **Quản lý sản phẩm, loại sản phẩm, giỏ hàng, đơn hàng**: có.
- **Đăng ký tài khoản, quản lý đơn hàng cá nhân**: có.
- **Admin: quản lý sản phẩm, đơn hàng; Customer: mua hàng**: có.

**Lựa chọn triển khai bổ sung** (không phải quy định bắt buộc): COD, thống kê doanh thu hoàn tất, lưu snapshot giá sản phẩm, trừ/hoàn tồn kho, upload ảnh, lọc đơn theo trạng thái và kiểm soát trạng thái hợp lệ.

## 3. Gợi ý chấm điểm trong Quy định giảng dạy (trang 5–6)

- Hoàn thiện chức năng chính: **40%**.
- Áp dụng công nghệ Laravel: **25%**.
- Thiết kế và trải nghiệm: **15%**.
- Báo cáo & thuyết trình: **10%**.
- Sáng tạo & mở rộng: **10%**.

## 4. Rubric 2 trong Đề cương học phần (trang 13–14)

Rubric này trình bày cách chia **khác** với bảng *gợi ý* ở văn bản quy định:

- Hình thức báo cáo: **10%**.
- Nội dung/chất lượng báo cáo: **10%**.
- Giao diện phần mềm: **30%**.
- Sản phẩm phần mềm: **40%**.
- Mức độ tham gia/tương tác thành viên nhóm: **10%**.

**Lưu ý:** Hai văn bản có các bảng phân bổ khác nhau. Nên trình bày đủ tất cả tiêu chí và xác nhận với giảng viên cách áp dụng bảng điểm khi chấm, không tự coi hai bảng là một.

## 5. Tiến độ kiểm chứng

Mã nguồn, migration, seeder, Blade views và test mẫu đã được viết. Đã kiểm tra cú pháp PHP trước khi đóng gói. **Chưa được xác nhận chạy end-to-end** trong môi trường cài đặt của người học; kết quả tích hợp và ảnh minh chứng phải thu thập sau khi chạy trên Laragon.
