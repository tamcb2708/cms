#!/bin/bash

echo "🚀 Bắt đầu quá trình cài đặt tự động cho EIOS CMS & DB Tracker..."
echo "------------------------------------------------------------"

# 1. Cài đặt Composer
echo "📦 1/5 Cài đặt thư viện PHP (Composer)..."
composer install --no-interaction --optimize-autoloader

# 2. Cài đặt NPM
echo "📦 2/5 Cài đặt thư viện Node.js (NPM) và Build frontend..."
npm install
npm run build

# 3. Thiết lập Môi trường (.env)
if [ ! -f .env ]; then
    echo "⚙️ 3/5 File .env chưa tồn tại. Đang copy từ .env.example..."
    cp .env.example .env
else
    echo "⚠️ 3/5 File .env đã tồn tại. Bỏ qua bước copy."
fi

# 4. Tạo Key Bảo mật
echo "🔑 4/5 Khởi tạo Application Key..."
php artisan key:generate --force

# 5. Khởi tạo Database
echo "🗄️ 5/5 Chạy Database Migrations..."
php artisan migrate --force

# 6. Dọn dẹp
echo "🧹 Dọn dẹp cache..."
php artisan optimize:clear

echo "------------------------------------------------------------"
echo "✅ CÀI ĐẶT HOÀN TẤT!"
echo "👉 Bạn hãy chỉnh sửa thông tin kết nối DB trong file .env"
echo "👉 Sau đó chạy lệnh: php artisan serve"
echo "------------------------------------------------------------"
