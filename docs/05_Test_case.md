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
| TC21 | Tài khoản | Customer sửa tên, email, gửi thêm `role=admin` | Chỉ cập nhật tên, email; giữ role customer | Chưa kiểm thử |
| TC22 | Tài khoản | Đổi mật khẩu khi mật khẩu cũ sai | Hiện lỗi validation | Chưa kiểm thử |
| TC23 | AJAX | Nhấn nút + trên thẻ sản phẩm | Giỏ tăng số lượng, không chuyển trang | Chưa kiểm thử |
| TC24 | AJAX | Thêm vượt tồn kho | Hiện thông báo lỗi, không tăng giỏ | Chưa kiểm thử |
| TC25 | CSV | Admin xuất đơn lọc theo trạng thái | Tải CSV UTF-8 đúng bộ lọc | Chưa kiểm thử |
| TC26 | Phân quyền | Customer mở URL xuất CSV Admin | HTTP 403 | Chưa kiểm thử |
| TC27 | Tồn kho | Lọc sản phẩm còn 1–5 và bằng 0 | Kết quả khớp bộ lọc | Chưa kiểm thử |
| TC28 | Xóa sản phẩm | Xóa sản phẩm có đơn đang chờ xác nhận | Bị chặn, có thông báo nên ẩn thay vì xóa | Chưa kiểm thử |
| TC29 | Thông tin đơn | Thử chèn công thức bảng tính vào tên người nhận, xuất CSV | Dữ liệu CSV không chạy công thức | Chưa kiểm thử |

| TC30 | Đơn cá nhân | Lọc mã đơn và trạng thái | Chỉ hiện đơn hợp lệ của tài khoản đang đăng nhập | Chưa kiểm thử |
| TC31 | Nhật ký | Tạo đơn và đổi trạng thái thành công | Ghi lịch sử trước/sau và người thực hiện | Chưa kiểm thử |
| TC32 | Nhật ký | Chuyển trạng thái không hợp lệ | Không tạo bản ghi lịch sử | Chưa kiểm thử |
| TC33 | Phiếu đơn | Customer in phiếu của chính mình | Hiện snapshot giá, COD, không ghi đã thanh toán | Chưa kiểm thử |
| TC34 | Phiếu đơn | Customer A mở phiếu của Customer B | HTTP 403 | Chưa kiểm thử |
| TC35 | Phiếu đơn | Admin in phiếu đơn từ trang quản lý | Có người nhận, đơn hàng và tổng tiền | Chưa kiểm thử |
| TC36 | Admin | Lọc đơn từ ngày đến ngày | Đúng thời điểm tạo đơn | Chưa kiểm thử |
| TC37 | CSV | Lọc ngày/trạng thái rồi xuất CSV | Các dòng CSV khớp danh sách đã lọc | Chưa kiểm thử |
| TC38 | Dashboard | Chuyển đơn thành hoàn tất | Ghi completed_at, doanh thu theo tháng hoàn tất | Chưa kiểm thử |
| TC39 | Seeder | Chạy db:seed khi Admin đã tồn tại | Không đặt lại mật khẩu Admin | Chưa kiểm thử |

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

## Kiểm thử tự động và thủ công

- Chạy `php artisan test` (dùng SQLite in-memory trong `phpunit.xml`) hoặc mở tab GitHub **Actions** để xem kết quả CI theo commit.
- Bộ `OnlineShopTest` có 34 bài kiểm thử mã nguồn; các tình huống upload ảnh, permission, trạng thái, giao dịch và CSV có test riêng.
- Kiểm thử giao diện, responsive, UX, luồng demo thực tế trên Laragon vẫn cần thực hiện thủ công; không đánh dấu tự động đạt nếu chưa xem ảnh hoặc thao tác thật.
