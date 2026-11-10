# BẢNG KIỂM THỬ - CẦN THỰC HIỆN TRÊN MÁY CỦA NHÓM

**Không được ghi “Đạt” trước khi thực sự chạy và có bằng chứng.** File này mô tả kết quả kỳ vọng, để trống kết quả thực tế cho nhóm điền.

| ID | Chức năng | Thao tác / trường hợp | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|---|
| TC01 | Trang chủ | Mở `/` khi chưa đăng nhập | Hiện sản phẩm và danh mục | Chưa kiểm thử |
| TC02 | Tìm kiếm | Nhập một phần tên sản phẩm | Danh sách khớp từ khóa | Chưa kiểm thử |
| TC03 | Phân trang | Có >9 sản phẩm, chuyển trang | Trang sau hiện dữ liệu mới | Chưa kiểm thử |
| TC04 | Đăng ký | Email mới, 2 mật khẩu trùng | Customer được tạo và đăng nhập | Chưa kiểm thử |
| TC05 | Đăng ký | Email đã tồn tại | Hiện lỗi validation | Chưa kiểm thử |
| TC06 | Phân quyền | Customer vào `/admin` | HTTP 403 | Chưa kiểm thử |
| TC07 | CRUD danh mục | Admin thêm, sửa, xóa danh mục không có sản phẩm | Thành công | Chưa kiểm thử |
| TC08 | Danh mục | Xóa danh mục đang chứa sản phẩm | Bị chặn và có thông báo | Chưa kiểm thử |
| TC09 | CRUD sản phẩm | Admin thêm/sửa/xóa sản phẩm | Danh sách thay đổi chính xác | Chưa kiểm thử |
| TC10 | Upload | Upload JPG/PNG/WebP <=2 MB | Ảnh hiển thị tại trang sản phẩm | Chưa kiểm thử |
| TC11 | Giỏ hàng | Thêm 2 sản phẩm rồi sửa số lượng | Tổng tiền cập nhật đúng | Chưa kiểm thử |
| TC12 | Tồn kho | Thêm số lượng vượt tồn | Cảnh báo không đủ số lượng | Chưa kiểm thử |
| TC13 | Đặt hàng | Giỏ hợp lệ, nhập thông tin giao hàng | Tạo đơn pending, trừ tồn kho | Chưa kiểm thử |
| TC14 | Đơn cá nhân | Customer A truy cập đơn của Customer B | HTTP 403 | Chưa kiểm thử |
| TC15 | Hủy đơn | Customer hủy đơn pending | Đơn cancelled, hoàn kho 1 lần | Chưa kiểm thử |
| TC16 | Hủy đơn | Customer cố hủy đơn shipping | Không cho hủy | Chưa kiểm thử |
| TC17 | Trạng thái | Admin xác nhận → đang giao → hoàn tất | Trạng thái thay đổi tuần tự | Chưa kiểm thử |
| TC18 | Trạng thái | Admin chuyển pending thẳng completed | Từ chối trạng thái không hợp lệ | Chưa kiểm thử |
| TC19 | Responsive | Thu hẹp màn hình 375px | Navbar, card, form dùng được | Chưa kiểm thử |
| TC20 | Bảo mật | Gửi role=admin khi đăng ký | Tài khoản vẫn là customer | Chưa kiểm thử |

## Kịch bản demo đề xuất (7–10 phút)

1. Mở trang khách vãng lai, tìm kiếm/lọc sản phẩm.
2. Customer đăng ký và đăng nhập.
3. Chọn sản phẩm, thêm và cập nhật giỏ.
4. Đặt hàng COD, xem mã đơn và trạng thái pending.
5. Đăng xuất Customer, đăng nhập Admin (tài khoản cấu hình riêng).
6. Admin xem dashboard và chỉnh CRUD danh mục/sản phẩm.
7. Admin mở đơn mới, xác nhận, chuyển sang đang giao, hoàn tất.
8. Đăng nhập lại Customer, xem lịch sử đơn đã cập nhật.
9. Thử tình huống sai: Customer truy cập `/admin`, số lượng vượt tồn, form rỗng.

**Lưu ý:** Một đơn `completed` không thể hủy. Nếu cần demo hủy đơn/hồi kho, tạo đơn thứ hai rồi hủy ngay khi còn `pending`.
