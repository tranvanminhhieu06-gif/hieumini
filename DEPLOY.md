# Triển khai HieuMini lên Render + TiDB Cloud

> Đưa toàn bộ 7 website (HieuMini + 6 dự án con) lên Internet miễn phí.
> Làm theo đúng thứ tự 5 bước dưới đây, tổng thời gian khoảng 20 phút.

**Kiến trúc sau khi triển khai:**

```
   Người dùng
       │  https://hieumini.onrender.com
       ▼
┌──────────────────────────┐        TLS cổng 4000       ┌─────────────────┐
│   RENDER (Docker)        │ ─────────────────────────▶ │  TIDB CLOUD     │
│   php:8.2-apache         │                            │  7 cơ sở dữ liệu│
│                          │                            │  55 bảng        │
│   /              HieuMini│                            └─────────────────┘
│   /projects/HieuWeb01/   │
│   /projects/HieuWeb02/   │  ← khung <iframe> trỏ đường dẫn tương đối
│   ...          HieuWeb06/│     nên chạy được ngay trong cùng container
└──────────────────────────┘
```

---

## Bước 1 — Tạo mật khẩu TiDB

Trong cửa sổ **Connect to hieumini** mà bạn đang mở:

1. Bấm nút **Generate Password** (góc phải)
2. **Chép mật khẩu ra chỗ an toàn ngay lập tức** — TiDB chỉ hiện đúng một lần, đóng cửa sổ là mất
3. Ghi lại luôn bốn thông số bên dưới (đã có sẵn trong ảnh bạn gửi):

| Thông số | Giá trị |
|---|---|
| HOST | `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` |
| PORT | `4000` |
| USERNAME | `4WXwYbGLHmDTbak.root` |
| PASSWORD | *(vừa tạo ở trên)* |

> Nếu lỡ mất mật khẩu: quay lại cửa sổ này bấm **Generate Password** lần nữa.
> Mật khẩu cũ sẽ bị vô hiệu, nhớ cập nhật lại trên Render.

---

## Bước 2 — Nạp 7 cơ sở dữ liệu lên TiDB

Tệp `database/tidb_all.sql` (187 KB) đã gộp sẵn cả 7 cơ sở dữ liệu và **đã được
điều chỉnh cho TiDB**: gỡ chỉ mục FULLTEXT (TiDB không hỗ trợ), gỡ chú thích
điều kiện của mysqldump, sắp lại thứ tự kiểm tra khóa ngoại.

### Cách A — Qua SQL Editor trên web (không cần cài gì)

1. Vào <https://tidbcloud.com> → chọn cluster **hieumini**
2. Mở tab **SQL Editor** (hoặc **Chat2Query**)
3. Mở tệp `database/tidb_all.sql` bằng Notepad, chọn tất cả (Ctrl+A), sao chép
4. Dán vào SQL Editor rồi bấm **Run**

> Nếu SQL Editor báo nội dung quá dài, dùng Cách B.

### Cách B — Chạy tệp `import-tidb.bat` (khuyến nghị)

Tệp này dùng chương trình `mysql.exe` có sẵn trong XAMPP nên không phải cài thêm.

1. Double-click **`import-tidb.bat`** trong thư mục `HieuWebsite`
2. Dán mật khẩu TiDB khi được hỏi
3. Chờ khoảng 1–2 phút

### Kiểm tra đã nạp đúng chưa

Chạy lệnh này trong SQL Editor, phải thấy đủ 7 dòng:

```sql
SELECT table_schema AS 'CSDL', COUNT(*) AS 'Số bảng'
FROM information_schema.tables
WHERE table_schema IN ('hieumini_portfolio','hieumini_db','hieumini_bookstore_db',
                       'hieumini_furniture_db','datcyber_appliances_db',
                       'hieumini_gym_db','hieumini_market_db')
GROUP BY table_schema;
```

Kết quả đúng phải là:

| CSDL | Số bảng | Dữ liệu |
|---|---|---|
| `hieumini_portfolio` | 4 | 6 dự án, 24 tính năng |
| `hieumini_db` | 7 | 17 sản phẩm thời trang |
| `hieumini_bookstore_db` | 8 | 10 sản phẩm công nghệ |
| `hieumini_furniture_db` | 8 | 30 sản phẩm học tập |
| `datcyber_appliances_db` | 8 | 11 sản phẩm gia dụng |
| `hieumini_gym_db` | 8 | 30 sản phẩm thể hình |
| `hieumini_market_db` | 12 | 18 dự án, 6 bài viết |

**Tổng: 55 bảng.**

---

## Bước 3 — Đẩy mã nguồn lên GitHub

Double-click **`push-github.bat`** trong thư mục `HieuWebsite`.

Script sẽ tự động: khởi tạo Git → thêm remote → commit → push lên nhánh `main`.
Lần đầu Git sẽ mở cửa sổ đăng nhập GitHub, bạn đăng nhập bằng tài khoản
`tranvanminhhieu06-gif`.

Hoặc chạy tay trong Command Prompt:

```bat
cd C:\xampp\htdocs\HieuWebsite
git init
git add .
git commit -m "HieuMini: cong trung bay 6 du an website PHP MySQL"
git branch -M main
git remote add origin https://github.com/tranvanminhhieu06-gif/hieumini.git
git push -u origin main
```

> `.gitignore` đã loại sẵn tệp `.env`, mật khẩu và tệp tạm.
> **Mật khẩu TiDB không nằm ở bất kỳ tệp nào trong mã nguồn** — nó chỉ được đặt
> làm biến môi trường trên Render ở bước 4.

---

## Bước 4 — Tạo dịch vụ trên Render

1. Vào <https://render.com> → **New +** → **Web Service**
2. Chọn **Build and deploy from a Git repository** → **Connect** repo `hieumini`
3. Điền cấu hình:

| Mục | Giá trị |
|---|---|
| Name | `hieumini` |
| Region | **Singapore** *(gần TiDB ap-southeast-1 nhất → nhanh nhất)* |
| Branch | `main` |
| Runtime / Language | **Docker** |
| Dockerfile Path | `./Dockerfile` |
| Instance Type | **Free** |

4. Kéo xuống mục **Environment Variables**, bấm **Add Environment Variable**
   và thêm **11 biến** sau:

| Key | Value |
|---|---|
| `DB_HOST` | `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` |
| `DB_PORT` | `4000` |
| `DB_USER` | `4WXwYbGLHmDTbak.root` |
| `DB_PASS` | *(mật khẩu TiDB từ bước 1)* |
| `DB_SSL` | `true` |
| `DB_NAME_PORTAL` | `hieumini_portfolio` |
| `DB_NAME_WEB01` | `hieumini_db` |
| `DB_NAME_WEB02` | `hieumini_bookstore_db` |
| `DB_NAME_WEB03` | `hieumini_furniture_db` |
| `DB_NAME_WEB04` | `datcyber_appliances_db` |
| `DB_NAME_WEB05` | `hieumini_gym_db` |
| `DB_NAME_WEB06` | `hieumini_market_db` |

5. Bấm **Create Web Service**

Lần build đầu mất khoảng 4–6 phút. Xong sẽ có địa chỉ dạng
`https://hieumini.onrender.com`.

> **Đừng thêm `ADMIN_PASSWORD` ở bước này.** Không có biến đó thì trang quản trị
> tắt hoàn toàn — đúng như yêu cầu thiết kế. Xem bước 5 khi nào cần bật.

---

## Tài khoản quản trị dùng chung cho cả 6 dự án con

Trên trang chi tiết mỗi dự án có nút **"Trang quản trị dự án"** và một thẻ hiển
thị **tài khoản quản trị demo dùng chung cho cả sáu dự án**:

| | |
|---|---|
| **Email** | `admin@hieumini.vn` |
| **Mật khẩu** | `demo123` |

Người xem bấm nút, đăng nhập bằng tài khoản trên là vào được trang quản trị của
bất kỳ dự án nào — không phải nhớ sáu tài khoản khác nhau.

Tệp `database/tidb_all.sql` đã tự đặt **cùng một email và mật khẩu** cho tài
khoản quản trị của cả sáu dự án (khối `UPDATE ... WHERE role='admin'` ở cuối
tệp), nên trên Render tài khoản chung chạy được ngay sau khi nạp CSDL.

> **Nếu muốn dùng cả trên localhost:** mỗi dự án con trên máy bạn dùng CSDL riêng
> với tài khoản gốc, nên cần đặt lại email và mật khẩu. Mở phpMyAdmin → tab
> **SQL** và chạy:
>
> ```sql
> SET @h = '$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS';
> UPDATE hieumini_db.users            SET email='admin@hieumini.vn', password=@h WHERE role='admin';
> UPDATE hieumini_bookstore_db.users  SET email='admin@hieumini.vn', password=@h WHERE role='admin';
> UPDATE hieumini_furniture_db.users  SET email='admin@hieumini.vn', password=@h WHERE role='admin';
> UPDATE datcyber_appliances_db.users SET email='admin@hieumini.vn', password=@h WHERE role='admin';
> UPDATE hieumini_gym_db.users        SET email='admin@hieumini.vn', password=@h WHERE role='admin';
> UPDATE hieumini_market_db.users     SET email='admin@hieumini.vn', password=@h WHERE role='admin';
> ```
>
> **Muốn đổi tài khoản demo hiển thị trên trang:** sửa hai hằng số
> `DEMO_ADMIN_USER` và `DEMO_ADMIN_PASS` trong `config/config.php` (hoặc đặt biến
> môi trường cùng tên trên Render), rồi cập nhật lại email/mật khẩu trong CSDL
> cho khớp.

> Tệp `open-admin.php` (chức năng tự đăng nhập cũ) đã ngừng dùng, nay chỉ chuyển
> hướng về trang dự án. Có thể xóa an toàn.

---

## Bước 5 — Bật trang quản trị của HieuMini (chỉ khi cần)

Trang quản trị **mặc định tắt**: mọi đường dẫn `/admin/...` trả về 404.

**Khi cần vào quản trị:**

1. Render Dashboard → dịch vụ `hieumini` → tab **Environment**
2. **Add Environment Variable**: Key `ADMIN_PASSWORD`, Value = một mật khẩu mạnh
   (tối thiểu 16 ký tự, có chữ hoa, số và ký tự đặc biệt)
3. Bấm **Save Changes** — Render tự triển khai lại, khoảng 1 phút
4. Vào `https://hieumini.onrender.com/admin/login.php`

**Khi làm xong, tắt lại:** xóa biến `ADMIN_PASSWORD` → Save Changes.
Trang quản trị trở về 404 ngay sau lần triển khai lại.

---

## Xử lý sự cố

<details>
<summary><b>Render báo build thất bại</b></summary>

Mở tab **Logs**, tìm dòng bắt đầu bằng `ERROR`. Hai nguyên nhân hay gặp:

- **Dockerfile Path sai** — phải đúng `./Dockerfile` (có dấu chấm và gạch chéo)
- **Chưa chọn Docker** — mục Language/Runtime phải là **Docker**, không phải PHP
</details>

<details>
<summary><b>Trang hiện "Chưa kết nối được cơ sở dữ liệu"</b></summary>

Theo thứ tự kiểm tra:

1. **Mật khẩu sai** — vào Render → Environment, sửa lại `DB_PASS`. Chú ý không có
   dấu cách thừa ở đầu hoặc cuối khi dán.
2. **Chưa đặt `DB_SSL=true`** — TiDB bắt buộc kết nối mã hóa, thiếu biến này sẽ
   bị từ chối kết nối.
3. **Chưa nạp CSDL** — quay lại bước 2, chạy lại câu lệnh kiểm tra.
4. **IP bị chặn** — TiDB Console → **Networking** → bật *Allow access from anywhere*
   (`0.0.0.0/0`). Render dùng IP động nên không thể khai báo danh sách cố định.

Muốn xem chi tiết lỗi: tạm thêm biến `APP_DEBUG` = `1`, xem xong nhớ đổi về `0`.
</details>

<details>
<summary><b>Một dự án con báo lỗi CSDL, các dự án khác vẫn bình thường</b></summary>

Sai tên biến `DB_NAME_WEBxx` của riêng dự án đó. Đối chiếu lại bảng 11 biến ở
bước 4 — tên phải khớp **chính xác từng ký tự**, kể cả chữ hoa chữ thường.
</details>

<details>
<summary><b>Lần đầu vào trang mất 50 giây mới hiện</b></summary>

Đây là đặc điểm của gói Free trên Render: dịch vụ tự ngủ sau 15 phút không có
lượt truy cập, lần truy cập kế tiếp phải khởi động lại container. TiDB Serverless
cũng tự ngủ tương tự. Các lần sau sẽ nhanh bình thường.

Muốn không bị ngủ: nâng lên gói Starter của Render (7 USD/tháng).
</details>

<details>
<summary><b>Khung xem trực tiếp trên trang chủ bị trống</b></summary>

Kiểm tra trực tiếp một dự án con, ví dụ
`https://hieumini.onrender.com/projects/HieuWeb05/index.php`.

- Nếu dự án con **cũng lỗi** → vấn đề nằm ở CSDL của nó, xem mục trên.
- Nếu dự án con **chạy bình thường** nhưng iframe trống → tệp `.htaccess` của dự
  án đó gửi tiêu đề `X-Frame-Options: DENY`. Mở
  `projects/HieuWeb05/.htaccess` hoặc `projects/HieuWeb06/.htaccess`, xóa dòng
  chứa `X-Frame-Options`, rồi push lại lên GitHub.
</details>

<details>
<summary><b>Tiếng Việt hiển thị thành dấu hỏi</b></summary>

CSDL nạp sai bộ mã. Xóa cả 7 CSDL trên TiDB rồi nạp lại `tidb_all.sql` — tệp này
đã khai báo `utf8mb4_unicode_ci` ở cả cấp CSDL lẫn từng bảng.

```sql
DROP DATABASE IF EXISTS hieumini_portfolio;
DROP DATABASE IF EXISTS hieumini_db;
DROP DATABASE IF EXISTS hieumini_bookstore_db;
DROP DATABASE IF EXISTS hieumini_furniture_db;
DROP DATABASE IF EXISTS datcyber_appliances_db;
DROP DATABASE IF EXISTS hieumini_gym_db;
DROP DATABASE IF EXISTS hieumini_market_db;
```
</details>

---

## Cập nhật website sau này

Render tự động triển khai lại mỗi khi bạn đẩy commit mới lên nhánh `main`:

```bat
cd C:\xampp\htdocs\HieuWebsite
git add .
git commit -m "Mo ta thay doi"
git push
```

Xem tiến trình ở tab **Events** trên Render Dashboard.

---

## Chạy song song localhost và Render

Mã nguồn tự nhận môi trường, **không cần đổi gì khi chuyển qua lại**:

| | Localhost (XAMPP) | Render |
|---|---|---|
| Máy chủ CSDL | `127.0.0.1:3306` | TiDB `:4000` |
| Kết nối mã hóa | Tắt | Tự bật (nhận ra `tidbcloud.com`) |
| Nguồn cấu hình | Giá trị mặc định trong mã | Biến môi trường |
| Trang quản trị | `set ADMIN_PASSWORD=...` | Render → Environment |

Cơ chế: mọi tệp cấu hình đều đọc biến môi trường trước, nếu không có thì quay về
giá trị mặc định của XAMPP. Riêng TLS thì tự bật khi thấy tên máy chủ chứa
`tidbcloud.com` hoặc cổng là `4000`.

---

## Những gì đã được kiểm chứng trước khi bàn giao

| Hạng mục | Cách kiểm chứng | Kết quả |
|---|---|---|
| Nạp `tidb_all.sql` | Import thật vào MariaDB 10.11 sạch | 7 CSDL, 55 bảng, không lỗi |
| Tiếng Việt trong dữ liệu | Truy vấn với `--default-character-set=utf8mb4` | Hiển thị đúng dấu |
| 6 cấu hình dự án con | Nạp từng tệp config với biến môi trường kiểu Render | 6/6 kết nối được |
| HieuMini trên MySQL thật | `php -S` + biến môi trường Render | 5/5 trang trả 200 |
| Trang quản trị khi tắt | Không đặt `ADMIN_PASSWORD` | Trả 404 |
| Trang quản trị khi bật | Đặt `ADMIN_PASSWORD` | Đăng nhập được, biểu đồ 14 cột |
| Lệnh đổi cổng Apache | Chạy thử `sed` trên `ports.conf` thật | `Listen 10000`, không nhân đôi |
| Cú pháp PHP | `php -l` toàn bộ tệp | Không lỗi |

Chưa kiểm chứng được trong môi trường của tôi: build ảnh Docker (sandbox không có
Docker daemon) và kết nối thẳng tới TiDB (cổng 4000 bị chặn). Hai việc này sẽ
diễn ra lần đầu khi bạn thực hiện bước 2 và bước 4.
