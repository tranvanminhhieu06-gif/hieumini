# -*- coding: utf-8 -*-
"""
Sinh tệp database/hieumini_books_db.sql từ catalog.py.

Sinh bằng script thay vì gõ tay để tên tệp bìa trong cột `cover` luôn khớp
với ảnh mà generate_covers.py tạo ra, và để chạy lại lúc nào cũng cho kết quả
y hệt (random có gieo hạt cố định).

Chạy:  python tools/generate_sql.py
"""
import os
import random
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from catalog import BOOKS, CATEGORIES, AUTHORS, slugify  # noqa: E402

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, "database", "hieumini_books_db.sql")

# Băm bcrypt của mật khẩu "hieumini2026" — PHP password_verify() đọc được.
ADMIN_HASH = "$2y$10$fUFFXf/TqekJNEghad3NDunVMawk47OF93fIgiUoemGylwXCGJaiu"

REVIEWERS = ["Minh Thư", "Quốc Bảo", "Hà Linh", "Đăng Khoa", "Thu Trang", "Gia Huy",
             "Phương Nhi", "Tuấn Kiệt", "Bảo Ngọc", "Hoàng Long", "Mai Anh", "Đức Thắng"]

COMMENTS = [
    "Sách in đẹp, giấy dày, đọc rất đã mắt. Nội dung thì khỏi bàn.",
    "Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.",
    "Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.",
    "Bản dịch mượt, giữ được giọng văn của nguyên tác.",
    "Một cuốn nên có trong tủ sách gia đình. Con mình cũng thích.",
    "Đọc xong thấy suy nghĩ khác đi khá nhiều về chuyện cũ.",
    "Nội dung tốt nhưng phần chú thích còn hơi sơ sài.",
    "Mua làm quà tặng, người nhận rất ưng.",
    "Văn phong giản dị mà thấm. Đọc chậm mới thấy hay.",
    "Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.",
]


def esc(v):
    if v is None:
        return "NULL"
    if isinstance(v, int):
        return str(v)
    return "'" + str(v).replace("\\", "\\\\").replace("'", "\\'") + "'"


def main():
    rnd = random.Random(20260905)
    L = []
    add = L.append

    add("-- =====================================================================")
    add("-- HieuMini Books — Cơ sở dữ liệu thư viện sách trực tuyến")
    add("-- Sinh tự động bởi tools/generate_sql.py — không sửa tay tệp này.")
    add("-- Bộ mã utf8mb4 để lưu đầy đủ dấu tiếng Việt và ký tự mở rộng.")
    add("-- =====================================================================")
    add("")
    add("CREATE DATABASE IF NOT EXISTS `hieumini_books_db`")
    add("  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")
    add("USE `hieumini_books_db`;")
    add("")
    add("SET NAMES utf8mb4;")
    add("SET FOREIGN_KEY_CHECKS = 0;")
    add("DROP TABLE IF EXISTS `reviews`, `books`, `authors`, `categories`, `admins`, `messages`;")
    add("SET FOREIGN_KEY_CHECKS = 1;")
    add("")

    add("-- ---------- Danh mục thể loại ----------")
    add("""CREATE TABLE `categories` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `slug`        VARCHAR(80)  NOT NULL UNIQUE,
  `name`        VARCHAR(120) NOT NULL,
  `description` TEXT,
  `bg_color`    CHAR(7)      NOT NULL DEFAULT '#1C1917',
  `accent`      CHAR(7)      NOT NULL DEFAULT '#C89B3C',
  `sort_order`  SMALLINT     NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;""")
    add("")

    add("-- ---------- Tác giả ----------")
    add("""CREATE TABLE `authors` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `slug`       VARCHAR(120) NOT NULL UNIQUE,
  `name`       VARCHAR(160) NOT NULL,
  `country`    VARCHAR(80),
  `birth_year` SMALLINT,
  `bio`        TEXT,
  INDEX `idx_authors_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;""")
    add("")

    add("-- ---------- Sách ----------")
    add("""CREATE TABLE `books` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `slug`           VARCHAR(180) NOT NULL UNIQUE,
  `title`          VARCHAR(220) NOT NULL,
  `author_id`      INT NOT NULL,
  `category_id`    INT NOT NULL,
  `publisher`      VARCHAR(160),
  `published_year` SMALLINT,
  `pages`          SMALLINT,
  `language`       VARCHAR(40) DEFAULT 'Tiếng Việt',
  `isbn`           VARCHAR(24),
  `cover`          VARCHAR(180),
  `summary`        TEXT,
  `is_featured`    TINYINT(1) NOT NULL DEFAULT 0,
  `views`          INT NOT NULL DEFAULT 0,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_books_author`   FOREIGN KEY (`author_id`)   REFERENCES `authors`(`id`)    ON DELETE CASCADE,
  CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  INDEX `idx_books_category` (`category_id`),
  INDEX `idx_books_author`   (`author_id`),
  INDEX `idx_books_title`    (`title`),
  INDEX `idx_books_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;""")
    add("")

    add("-- ---------- Đánh giá của bạn đọc ----------")
    add("""CREATE TABLE `reviews` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `book_id`     INT NOT NULL,
  `reader_name` VARCHAR(120) NOT NULL,
  `rating`      TINYINT NOT NULL,
  `content`     TEXT,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_book` FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5),
  INDEX `idx_reviews_book` (`book_id`, `is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;""")
    add("")

    add("-- ---------- Tài khoản quản trị ----------")
    add("""CREATE TABLE `admins` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(60)  NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name`     VARCHAR(120),
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;""")
    add("")

    add("-- ---------- Tin nhắn liên hệ ----------")
    add("""CREATE TABLE `messages` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(160) NOT NULL,
  `subject`    VARCHAR(200),
  `content`    TEXT NOT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;""")
    add("")

    # ---------- Dữ liệu mẫu ----------
    add("-- ---------- Dữ liệu mẫu ----------")
    add("INSERT INTO `categories` (`slug`,`name`,`description`,`bg_color`,`accent`,`sort_order`) VALUES")
    rows = [f"({esc(s)},{esc(n)},{esc(d)},{esc(bg)},{esc(ac)},{i})"
            for i, (s, n, d, bg, ac) in enumerate(CATEGORIES)]
    add(",\n".join(rows) + ";")
    add("")

    add("INSERT INTO `authors` (`slug`,`name`,`country`,`birth_year`,`bio`) VALUES")
    rows = [f"({esc(slugify(n))},{esc(n)},{esc(c)},{esc(b)},{esc(bio)})"
            for n, c, b, bio in AUTHORS]
    add(",\n".join(rows) + ";")
    add("")

    add("INSERT INTO `books` (`slug`,`title`,`author_id`,`category_id`,`publisher`,`published_year`,")
    add("  `pages`,`language`,`isbn`,`cover`,`summary`,`is_featured`,`views`) VALUES")
    rows = []
    for title, author, cat, pub, year, pages, lang, isbn, feat, summary in BOOKS:
        rows.append(
            f"({esc(slugify(title))},{esc(title)},"
            f"(SELECT id FROM authors WHERE slug={esc(slugify(author))}),"
            f"(SELECT id FROM categories WHERE slug={esc(cat)}),"
            f"{esc(pub)},{esc(year)},{esc(pages)},{esc(lang)},{esc(isbn)},"
            f"{esc(slugify(title) + '.webp')},{esc(summary)},{feat},{rnd.randint(40, 2400)})")
    add(",\n".join(rows) + ";")
    add("")

    add("INSERT INTO `reviews` (`book_id`,`reader_name`,`rating`,`content`,`is_approved`) VALUES")
    rows = []
    for title, *_ in BOOKS:
        for _ in range(rnd.randint(1, 3)):
            rows.append(
                f"((SELECT id FROM books WHERE slug={esc(slugify(title))}),"
                f"{esc(rnd.choice(REVIEWERS))},{rnd.randint(3, 5)},"
                f"{esc(rnd.choice(COMMENTS))},{rnd.choice([1, 1, 1, 0])})")
    add(",\n".join(rows) + ";")
    add("")

    add("INSERT INTO `admins` (`username`,`password_hash`,`full_name`) VALUES")
    add(f"('admin',{esc(ADMIN_HASH)},'Trần Văn Minh Hiếu');")
    add("-- Mật khẩu mặc định: hieumini2026 — ĐỔI NGAY sau khi cài đặt xong.")
    add("")

    add("INSERT INTO `messages` (`name`,`email`,`subject`,`content`,`is_read`) VALUES")
    add("('Ngọc Anh','ngocanh@example.com','Hỏi về bản in mới',"
        "'Cho mình hỏi cuốn Chí Phèo có bản bìa cứng không ạ?',0),")
    add("('Trung Hiếu','trunghieu@example.com','Góp ý giao diện',"
        "'Trang web đọc rất dễ chịu, mong có thêm chức năng lưu sách yêu thích.',1);")
    add("")

    with open(OUT, "w", encoding="utf-8") as f:
        f.write("\n".join(L) + "\n")

    n_reviews = sum(1 for line in L if line.startswith("((SELECT id FROM books"))
    print(f"Đã ghi {OUT}")
    print(f"  {len(CATEGORIES)} danh mục · {len(AUTHORS)} tác giả · {len(BOOKS)} sách")
    print(f"  {os.path.getsize(OUT) / 1024:.0f} KB")


if __name__ == "__main__":
    main()
