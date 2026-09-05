# -*- coding: utf-8 -*-
"""Sinh các sơ đồ dùng trong báo cáo: use case, ERD và luồng xử lý một yêu cầu."""
import os

import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
from matplotlib.patches import FancyBboxPatch, Ellipse, FancyArrowPatch, Rectangle

plt.rcParams["font.family"] = "DejaVu Sans"

OUT = os.environ.get("DIAGRAM_DIR", os.path.join(os.path.dirname(__file__), "..", "..", "hieu07-report-assets"))
os.makedirs(OUT, exist_ok=True)

INK, MUTED, ACCENT, LINE = "#16130E", "#4A443A", "#8A6412", "#B8B0A0"


def save(fig, name):
    path = os.path.join(OUT, name)
    fig.savefig(path, dpi=200, bbox_inches="tight", facecolor="white")
    plt.close(fig)
    print("  ", os.path.abspath(path))


# ---------------------------------------------------------------- use case
def usecase():
    fig, ax = plt.subplots(figsize=(11, 8.2))
    ax.set_xlim(0, 11); ax.set_ylim(0, 8.2); ax.axis("off")

    def actor(x, y, label):
        ax.add_patch(Ellipse((x, y + .52), .26, .26, fc="white", ec=INK, lw=1.4))
        ax.plot([x, x], [y + .38, y - .10], color=INK, lw=1.4)
        ax.plot([x - .24, x + .24], [y + .22, y + .22], color=INK, lw=1.4)
        ax.plot([x, x - .20], [y - .10, y - .44], color=INK, lw=1.4)
        ax.plot([x, x + .20], [y - .10, y - .44], color=INK, lw=1.4)
        ax.text(x, y - .74, label, ha="center", va="top", fontsize=10.5, color=INK, weight="bold")

    def uc(x, y, label, w=2.5, h=.62):
        ax.add_patch(Ellipse((x, y), w, h, fc="#FBF9F4", ec=LINE, lw=1.1))
        ax.text(x, y, label, ha="center", va="center", fontsize=8.6, color=INK)
        return (x, y, w, h)

    def link(ax_, actor_xy, uc_box, dashed=False):
        x1, y1 = actor_xy
        x2, y2, w, h = uc_box
        edge = x2 - w / 2 if x2 > x1 else x2 + w / 2
        ax_.add_patch(FancyArrowPatch((x1, y1), (edge, y2), arrowstyle="-",
                                      color=LINE, lw=.9,
                                      linestyle="--" if dashed else "-",
                                      shrinkA=14, shrinkB=2))

    ax.add_patch(Rectangle((2.55, .35), 5.9, 7.5, fc="none", ec=LINE, lw=1.2))
    ax.text(5.5, 7.62, "HỆ THỐNG HIEUMINI BOOKS", ha="center", fontsize=10,
            color=MUTED, weight="bold")

    actor(1.25, 4.5, "Bạn đọc")
    actor(9.75, 4.5, "Quản trị viên")

    reader = [
        uc(4.35, 7.00, "Xem trang chủ"),
        uc(4.35, 6.20, "Duyệt kho sách"),
        uc(4.35, 5.40, "Lọc theo thể loại"),
        uc(4.35, 4.60, "Tìm kiếm sách"),
        uc(4.35, 3.80, "Xem chi tiết sách"),
        uc(4.35, 3.00, "Xem trang tác giả"),
        uc(4.35, 2.20, "Gửi đánh giá"),
        uc(4.35, 1.40, "Gửi liên hệ"),
    ]
    for u in reader:
        link(ax, (1.25, 4.5), u)

    admin = [
        uc(6.75, 7.00, "Đăng nhập"),
        uc(6.75, 6.20, "Thêm / sửa sách"),
        uc(6.75, 5.40, "Xoá sách"),
        uc(6.75, 4.60, "Duyệt đánh giá"),
        uc(6.75, 3.80, "Ẩn / xoá đánh giá"),
        uc(6.75, 3.00, "Đọc tin nhắn"),
        uc(6.75, 2.20, "Xem thống kê"),
    ]
    for u in admin:
        link(ax, (9.75, 4.5), u)

    # Các chức năng quản trị đều đòi hỏi đã đăng nhập.
    # Định tuyến men theo mép phải của cột ellipse để nét đứt không cắt qua chữ.
    for u in admin[1:]:
        ax.add_patch(FancyArrowPatch((6.75 + 1.25, u[1]), (6.75 + 1.25, 7.00),
                                     arrowstyle="->", color=ACCENT, lw=.7,
                                     linestyle=":", mutation_scale=8,
                                     shrinkA=2, shrinkB=2,
                                     connectionstyle="arc3,rad=-0.18"))
    ax.text(8.18, 7.42, "«include» Đăng nhập", fontsize=7.2, color=ACCENT,
            style="italic", ha="left", va="center")

    save(fig, "so-do-usecase.png")


# ---------------------------------------------------------------- ERD
def erd():
    fig, ax = plt.subplots(figsize=(12, 10.2))
    ax.set_xlim(0, 12); ax.set_ylim(0, 10.2); ax.axis("off")

    def table(x, y, name, cols, w=2.9):
        h = .34 + len(cols) * .265
        ax.add_patch(FancyBboxPatch((x, y - h), w, h, boxstyle="round,pad=0.02,rounding_size=0.04",
                                    fc="white", ec=INK, lw=1.3))
        ax.add_patch(Rectangle((x, y - .34), w, .34, fc="#F3EFE6", ec=INK, lw=1.3))
        ax.text(x + w / 2, y - .17, name, ha="center", va="center", fontsize=10, weight="bold", color=INK)
        for i, (c, kind) in enumerate(cols):
            yy = y - .34 - .13 - i * .265
            colr = ACCENT if kind in ("PK", "FK") else MUTED
            mark = {"PK": "[PK] ", "FK": "[FK] ", "": ""}[kind]
            ax.text(x + .12, yy, mark + c, ha="left", va="center", fontsize=7.8, color=colr,
                    weight="bold" if kind else "normal")
        return (x, y, w, h)

    cat = table(0.3, 9.7, "categories", [("id", "PK"), ("slug", ""), ("name", ""),
                                         ("description", ""), ("bg_color", ""), ("accent", ""), ("sort_order", "")])
    aut = table(0.3, 5.7, "authors", [("id", "PK"), ("slug", ""), ("name", ""),
                                      ("country", ""), ("birth_year", ""), ("bio", "")])
    bok = table(4.5, 8.8, "books", [("id", "PK"), ("slug", ""), ("title", ""), ("author_id", "FK"),
                                    ("category_id", "FK"), ("publisher", ""), ("published_year", ""),
                                    ("pages", ""), ("language", ""), ("isbn", ""), ("cover", ""),
                                    ("summary", ""), ("is_featured", ""), ("views", ""), ("created_at", "")])
    rev = table(8.7, 7.1, "reviews", [("id", "PK"), ("book_id", "FK"), ("reader_name", ""),
                                      ("rating", ""), ("content", ""), ("is_approved", ""), ("created_at", "")])
    adm = table(8.7, 3.4, "admins", [("id", "PK"), ("username", ""), ("password_hash", ""),
                                     ("full_name", ""), ("created_at", "")])
    msg = table(4.5, 3.4, "messages", [("id", "PK"), ("name", ""), ("email", ""), ("subject", ""),
                                       ("content", ""), ("is_read", ""), ("created_at", "")])

    def rel(a, b, label, ya, yb):
        ax.add_patch(FancyArrowPatch((a[0] + a[2], ya), (b[0], yb), arrowstyle="-",
                                     color=ACCENT, lw=1.5, connectionstyle="arc3,rad=0.08"))
        ax.text((a[0] + a[2] + b[0]) / 2, (ya + yb) / 2 + .16, label, ha="center",
                fontsize=8, color=ACCENT, weight="bold")

    rel(cat, bok, "1 : N", 8.5, 7.7)
    rel(aut, bok, "1 : N", 4.9, 5.5)
    rel(bok, rev, "1 : N", 6.3, 6.1)

    ax.text(6.0, 0.55, "Khoá ngoại đặt ON DELETE CASCADE — xoá sách thì đánh giá của nó tự dọn theo.\n"
                       "Hai bảng admins và messages đứng độc lập, không ràng buộc với phần còn lại.",
            ha="center", fontsize=8.5, color=MUTED, style="italic")

    save(fig, "so-do-erd.png")


# ---------------------------------------------------------------- luồng xử lý
def flow():
    fig, ax = plt.subplots(figsize=(12, 4.6))
    ax.set_xlim(0, 12); ax.set_ylim(0, 4.6); ax.axis("off")

    steps = [
        ("Trình duyệt", "Yêu cầu\nbooks.php?q=…", "#F3EFE6"),
        ("Apache", "Chuyển tệp .php\ncho PHP xử lý", "white"),
        ("config/", "Nạp hằng số,\nmở phiên, kết nối PDO", "white"),
        ("Trang PHP", "Lọc tham số,\ndựng câu truy vấn", "white"),
        ("MySQL", "Prepared statement\ntrả về dữ liệu", "#F3EFE6"),
        ("includes/", "Đổ dữ liệu vào\nHTML, escape bằng e()", "white"),
        ("Trình duyệt", "Vẽ trang, nạp CSS/JS,\nchạy hiệu ứng", "#F3EFE6"),
    ]

    w, gap = 1.42, .22
    x = .28
    for i, (title, desc, fc) in enumerate(steps):
        ax.add_patch(FancyBboxPatch((x, 1.55), w, 1.5, boxstyle="round,pad=0.02,rounding_size=0.06",
                                    fc=fc, ec=INK, lw=1.2))
        ax.text(x + w / 2, 2.78, title, ha="center", va="center", fontsize=9.2, weight="bold", color=INK)
        ax.text(x + w / 2, 2.15, desc, ha="center", va="center", fontsize=7.4, color=MUTED)
        if i < len(steps) - 1:
            ax.add_patch(FancyArrowPatch((x + w, 2.3), (x + w + gap, 2.3),
                                         arrowstyle="->", color=ACCENT, lw=1.5, mutation_scale=11))
        x += w + gap

    ax.text(6.0, 1.05, "Mọi giá trị người dùng nhập chỉ đi vào câu lệnh SQL dưới dạng THAM SỐ (bước 4→5),\n"
                       "và chỉ ra HTML sau khi đã escape (bước 6). Đó là hai chốt chặn SQL Injection và XSS.",
            ha="center", fontsize=8.6, color=MUTED, style="italic")
    ax.text(6.0, 3.55, "LUỒNG XỬ LÝ MỘT YÊU CẦU", ha="center", fontsize=11, weight="bold", color=INK)

    save(fig, "so-do-luong-xu-ly.png")


if __name__ == "__main__":
    print("Đang sinh sơ đồ:")
    usecase()
    erd()
    flow()
