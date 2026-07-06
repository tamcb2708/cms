# EIOS CMS & DB Tracker System

Hệ thống quản trị nội dung (CMS) tích hợp tính năng **Theo dõi Cơ sở dữ liệu theo thời gian thực (DB Tracker Pro)** được xây dựng trên nền tảng Laravel 11.

## 🚀 Tính năng nổi bật
*   **Quản trị CMS đa dạng:** Bảng điều khiển admin đầy đủ (Sidebar, Phân quyền, User Management).
*   **DB Tracker Pro:** Công cụ siêu mạnh để theo dõi các thao tác `INSERT`, `UPDATE`, `DELETE` trên nhiều Database PostgreSQL cùng một lúc.
*   **Persistent Connections:** Tự động mã hóa (Encrypt) và lưu trữ thông tin kết nối DB của người dùng vào cơ sở dữ liệu.
*   **Kiến trúc Multi-Tab & Ajax:** Tải Log liên tục qua Web API mà không cần refresh lại trang, tiết kiệm tối đa tài nguyên hệ thống nhờ tính năng tạm ngưng truy vấn khi ẩn tab.

## ⚙️ Hướng dẫn cài đặt nhanh cho Developer
Nếu bạn mới clone source code này từ Git về, bạn chỉ cần thực hiện 1 thao tác duy nhất để chạy toàn bộ hệ thống:

**Dành cho Linux / macOS:**
Mở Terminal, di chuyển vào thư mục dự án và chạy file setup tự động:
```bash
./setup.sh
```

**Hoặc cài đặt thủ công (Nếu bạn dùng Windows):**
Chạy lần lượt các lệnh sau trong Terminal/Command Prompt:
```bash
# 1. Cài đặt các gói PHP
composer install

# 2. Cài đặt thư viện Frontend và Build giao diện Tailwind
npm install
npm run build

# 3. Tạo file biến môi trường
cp .env.example .env

# 4. Sinh mã bảo mật cho ứng dụng
php artisan key:generate

# 5. Cài đặt CSDL (Tạo bảng db_tracker_connections)
php artisan migrate
```

## 🛠️ Cấu hình Database
Sau khi cài đặt xong, bạn hãy mở file `.env` và cấu hình lại thông tin kết nối Database của ứng dụng (nơi lưu trữ tài khoản Admin và cấu hình DB Tracker):

```ini
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_cms_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Lưu ý: Bạn cũng cần cấu hình chuỗi mã hóa cho ứng dụng nếu báo lỗi liên quan đến Crypt. Lệnh `php artisan key:generate` đã xử lý việc này cho bạn.

## 🏃 Khởi chạy Server
Để mở server và vào trang quản trị:
```bash
php artisan serve
```
Sau đó truy cập trình duyệt theo địa chỉ: [http://localhost:8000/db-tracker](http://localhost:8000/db-tracker)

## 👤 Hướng dẫn sử dụng DB Tracker
1. Truy cập vào menu **DB Tracker** trên Sidebar.
2. Nhập thông tin kết nối đến PostgreSQL (ví dụ AWS RDS).
3. Ấn nút **Khởi tạo hệ thống** nếu đây là lần đầu tiên bạn kết nối DB này (hệ thống sẽ tự tạo bảng `pg_audit_logs` và triggers).
4. Bật chế độ **Đang theo dõi** cho bất kỳ bảng nào bạn muốn soi log. Dữ liệu sẽ tự động đổ về bảng Hoạt động gần đây theo chu kỳ 3 giây.
