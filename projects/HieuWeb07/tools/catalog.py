# -*- coding: utf-8 -*-
"""
Nguồn dữ liệu duy nhất cho HieuMini Books.

Cả bìa sách (generate_covers.py) lẫn dữ liệu mẫu trong CSDL (generate_sql.py)
đều sinh ra từ tệp này, nên tên tệp ảnh và cột `cover` trong bảng `books`
không bao giờ lệch nhau — lỗi hay gặp nhất khi dựng seed bằng tay.
"""

# (slug, tên, mô tả ngắn, màu nền bìa, màu chữ nhấn trên bìa)
CATEGORIES = [
    ("van-hoc-viet-nam", "Văn học Việt Nam",
     "Truyện, tiểu thuyết và ký của các tác giả trong nước qua nhiều thời kỳ.",
     "#1C1917", "#C89B3C"),
    ("van-hoc-the-gioi", "Văn học thế giới",
     "Tác phẩm kinh điển và đương đại được dịch sang tiếng Việt.",
     "#16181D", "#B08D57"),
    ("khoa-hoc-cong-nghe", "Khoa học & Công nghệ",
     "Sách phổ biến khoa học, lập trình và tư duy kỹ thuật.",
     "#101820", "#7FB3B3"),
    ("lich-su-dia-chi", "Lịch sử & Địa chí",
     "Biên khảo lịch sử, địa chí và hồi ký tư liệu.",
     "#1A1614", "#C0703A"),
    ("triet-hoc-tu-tuong", "Triết học & Tư tưởng",
     "Tác phẩm triết học, luân lý và tư tưởng phương Đông lẫn phương Tây.",
     "#141419", "#9B8EC4"),
    ("thieu-nhi", "Thiếu nhi",
     "Truyện tranh, truyện kể và sách kỹ năng dành cho bạn đọc nhỏ tuổi.",
     "#131A17", "#7FBF8E"),
]

# (tên, quốc gia, năm sinh, tiểu sử ngắn)
AUTHORS = [
    ("Nam Cao", "Việt Nam", 1915,
     "Nhà văn hiện thực phê phán, nổi tiếng với các truyện ngắn viết về người nông dân và trí thức nghèo trước năm 1945."),
    ("Tô Hoài", "Việt Nam", 1920,
     "Cây bút văn xuôi bền bỉ bậc nhất của văn học Việt Nam hiện đại, để lại khối lượng tác phẩm đồ sộ cho cả người lớn lẫn thiếu nhi."),
    ("Nguyễn Nhật Ánh", "Việt Nam", 1955,
     "Nhà văn được bạn đọc trẻ yêu thích, chuyên viết về tuổi mới lớn với giọng văn trong trẻo và hóm hỉnh."),
    ("Vũ Trọng Phụng", "Việt Nam", 1912,
     "Nhà văn, nhà báo nổi bật của trào lưu hiện thực, được mệnh danh là ông vua phóng sự đất Bắc."),
    ("Thạch Lam", "Việt Nam", 1910,
     "Thành viên Tự Lực văn đoàn, viết truyện ngắn giàu chất thơ và tinh tế trong quan sát đời thường."),
    ("Antoine de Saint-Exupéry", "Pháp", 1900,
     "Phi công kiêm nhà văn, tác giả của những trang viết về bầu trời, tình bạn và trách nhiệm."),
    ("Ernest Hemingway", "Hoa Kỳ", 1899,
     "Nhà văn đoạt giải Nobel Văn học 1954, nổi tiếng với lối viết tiết chế và câu văn ngắn gọn."),
    ("Haruki Murakami", "Nhật Bản", 1949,
     "Tiểu thuyết gia đương đại có ảnh hưởng rộng, hòa trộn đời thường với yếu tố siêu thực."),
    ("George Orwell", "Anh", 1903,
     "Nhà văn, nhà báo, tác giả của những tác phẩm chính trị có sức ảnh hưởng lâu dài."),
    ("Fyodor Dostoevsky", "Nga", 1821,
     "Tiểu thuyết gia bậc thầy về tâm lý và các câu hỏi luân lý của con người."),
    ("Carl Sagan", "Hoa Kỳ", 1934,
     "Nhà thiên văn học và người phổ biến khoa học, nổi tiếng với khả năng diễn đạt trong sáng."),
    ("Yuval Noah Harari", "Israel", 1976,
     "Sử gia nghiên cứu lịch sử dài hạn của loài người và tác động của công nghệ."),
    ("Donald Knuth", "Hoa Kỳ", 1938,
     "Nhà khoa học máy tính, tác giả bộ sách nền tảng về thuật toán và là cha đẻ của TeX."),
    ("Trần Trọng Kim", "Việt Nam", 1883,
     "Học giả, nhà giáo dục, tác giả bộ sử phổ thông có ảnh hưởng lớn đầu thế kỷ 20."),
    ("Phan Bội Châu", "Việt Nam", 1867,
     "Chí sĩ yêu nước, đồng thời là tác giả của nhiều trước tác chính luận và tự truyện."),
    ("Nguyễn Hiến Lê", "Việt Nam", 1912,
     "Học giả, dịch giả và nhà văn hóa với hơn một trăm đầu sách biên khảo, dịch thuật."),
    ("Lão Tử", "Trung Quốc", None,
     "Nhân vật được xem là tác giả Đạo Đức Kinh, nền tảng của tư tưởng Đạo gia."),
    ("Marcus Aurelius", "La Mã", 121,
     "Hoàng đế La Mã, để lại tập ghi chép cá nhân trở thành tác phẩm trụ cột của phái Khắc Kỷ."),
    ("Tố Hữu", "Việt Nam", 1920,
     "Nhà thơ cách mạng, giọng thơ trữ tình chính trị tiêu biểu của văn học Việt Nam thế kỷ 20."),
    ("Astrid Lindgren", "Thụy Điển", 1907,
     "Nhà văn thiếu nhi Thụy Điển, người tạo ra những nhân vật trẻ em độc lập và giàu tưởng tượng."),
]

# (tựa, slug tác giả -> tên, slug danh mục, nhà xuất bản, năm, số trang, ngôn ngữ, ISBN, nổi bật, tóm tắt)
BOOKS = [
    ("Chí Phèo", "Nam Cao", "van-hoc-viet-nam", "NXB Văn học", 1941, 168, "Tiếng Việt", "978-604-1-00001-1", 1,
     "Tập truyện ngắn xoay quanh bi kịch bị tha hóa của người nông dân trong xã hội thuộc địa nửa phong kiến. Nhân vật Chí Phèo trở thành một điển hình văn học về khát vọng được làm người lương thiện."),
    ("Lão Hạc", "Nam Cao", "van-hoc-viet-nam", "NXB Văn học", 1943, 124, "Tiếng Việt", "978-604-1-00002-8", 0,
     "Câu chuyện về một người cha già nghèo khó chọn cái chết để giữ lại mảnh vườn cho con, khắc họa lòng tự trọng của người nông dân Việt Nam."),
    ("Dế Mèn phiêu lưu ký", "Tô Hoài", "van-hoc-viet-nam", "NXB Kim Đồng", 1941, 156, "Tiếng Việt", "978-604-2-00003-5", 1,
     "Hành trình trưởng thành của chú Dế Mèn qua những chuyến đi, những lần vấp ngã và tình bạn — tác phẩm thiếu nhi kinh điển của văn học Việt Nam."),
    ("Vợ chồng A Phủ", "Tô Hoài", "van-hoc-viet-nam", "NXB Văn học", 1953, 96, "Tiếng Việt", "978-604-1-00004-2", 0,
     "Truyện ngắn viết về số phận người dân miền núi Tây Bắc và hành trình tự giải phóng khỏi áp bức."),
    ("Mắt biếc", "Nguyễn Nhật Ánh", "van-hoc-viet-nam", "NXB Trẻ", 1990, 268, "Tiếng Việt", "978-604-3-00005-9", 1,
     "Chuyện tình đơn phương kéo dài từ tuổi thơ làng Đo Đo tới khi trưởng thành, giọng văn trong trẻo mà day dứt."),
    ("Số đỏ", "Vũ Trọng Phụng", "van-hoc-viet-nam", "NXB Văn học", 1936, 232, "Tiếng Việt", "978-604-1-00006-6", 0,
     "Tiểu thuyết trào phúng châm biếm xã hội thành thị Việt Nam thời Âu hóa, với nhân vật Xuân Tóc Đỏ nổi tiếng."),
    ("Hà Nội băm sáu phố phường", "Thạch Lam", "van-hoc-viet-nam", "NXB Văn học", 1943, 112, "Tiếng Việt", "978-604-1-00007-3", 0,
     "Tập tùy bút ghi lại nếp sống, món ăn và không khí phố cổ Hà Nội bằng lối viết nhẹ nhàng, tinh tế."),

    ("Hoàng tử bé", "Antoine de Saint-Exupéry", "van-hoc-the-gioi", "NXB Hội Nhà văn", 1943, 128, "Tiếng Việt", "978-604-4-00008-0", 1,
     "Câu chuyện ngụ ngôn về một cậu bé đến từ tiểu tinh cầu B612, nói về tình bạn, sự mất mát và cách nhìn thế giới bằng trái tim."),
    ("Ông già và biển cả", "Ernest Hemingway", "van-hoc-the-gioi", "NXB Văn học", 1952, 144, "Tiếng Việt", "978-604-1-00009-7", 0,
     "Cuộc chiến đơn độc giữa ông lão đánh cá Santiago với con cá kiếm khổng lồ, biểu tượng cho phẩm giá con người trước thất bại."),
    ("Rừng Na Uy", "Haruki Murakami", "van-hoc-the-gioi", "NXB Hội Nhà văn", 1987, 396, "Tiếng Việt", "978-604-4-00010-3", 1,
     "Tiểu thuyết về ký ức, mất mát và tuổi trẻ Nhật Bản cuối thập niên 1960."),
    ("Kafka bên bờ biển", "Haruki Murakami", "van-hoc-the-gioi", "NXB Văn học", 2002, 508, "Tiếng Việt", "978-604-1-00011-0", 0,
     "Hai tuyến truyện đan xen giữa hiện thực và siêu thực, đi tìm lời giải cho một lời nguyền và một ký ức bị đánh mất."),
    ("1984", "George Orwell", "van-hoc-the-gioi", "NXB Dân trí", 1949, 384, "Tiếng Việt", "978-604-5-00012-7", 1,
     "Bức tranh về một xã hội toàn trị nơi ngôn ngữ, ký ức và sự thật đều bị kiểm soát."),
    ("Trại súc vật", "George Orwell", "van-hoc-the-gioi", "NXB Hội Nhà văn", 1945, 152, "Tiếng Việt", "978-604-4-00013-4", 0,
     "Truyện ngụ ngôn chính trị mượn chuyện đàn gia súc nổi dậy để nói về sự tha hóa của quyền lực."),
    ("Tội ác và trừng phạt", "Fyodor Dostoevsky", "van-hoc-the-gioi", "NXB Văn học", 1866, 672, "Tiếng Việt", "978-604-1-00014-1", 0,
     "Tiểu thuyết tâm lý theo chân một sinh viên nghèo sau tội ác của anh ta, và hành trình dằn vặt đi tới sám hối."),
    ("Anh em nhà Karamazov", "Fyodor Dostoevsky", "van-hoc-the-gioi", "NXB Văn học", 1880, 912, "Tiếng Việt", "978-604-1-00015-8", 0,
     "Tác phẩm cuối cùng của Dostoevsky, đặt ra những câu hỏi lớn về đức tin, tự do và trách nhiệm."),

    ("Vũ trụ", "Carl Sagan", "khoa-hoc-cong-nghe", "NXB Thế giới", 1980, 432, "Tiếng Việt", "978-604-6-00016-5", 1,
     "Hành trình phổ biến khoa học đưa người đọc đi từ nguồn gốc vũ trụ tới vị trí nhỏ bé của Trái Đất trong không gian."),
    ("Chấm xanh mờ nhạt", "Carl Sagan", "khoa-hoc-cong-nghe", "NXB Thế giới", 1994, 288, "Tiếng Việt", "978-604-6-00017-2", 0,
     "Suy tưởng về vị trí của Trái Đất nhìn từ rìa Thái Dương hệ, và lời kêu gọi con người giữ gìn hành tinh duy nhất mình có."),
    ("Sapiens: Lược sử loài người", "Yuval Noah Harari", "khoa-hoc-cong-nghe", "NXB Tri thức", 2011, 554, "Tiếng Việt", "978-604-7-00018-9", 1,
     "Nhìn lại bảy mươi nghìn năm lịch sử loài người qua ba cuộc cách mạng: nhận thức, nông nghiệp và khoa học."),
    ("Homo Deus: Lược sử tương lai", "Yuval Noah Harari", "khoa-hoc-cong-nghe", "NXB Tri thức", 2015, 496, "Tiếng Việt", "978-604-7-00019-6", 0,
     "Phần tiếp nối của Sapiens, bàn về việc con người sẽ đi về đâu khi nắm trong tay công nghệ sinh học và trí tuệ nhân tạo."),
    ("Nghệ thuật lập trình máy tính", "Donald Knuth", "khoa-hoc-cong-nghe", "NXB Khoa học và Kỹ thuật", 1968, 650, "Tiếng Việt", "978-604-8-00020-2", 0,
     "Bộ sách nền tảng về thuật toán và phân tích độ phức tạp, được xem là tài liệu kinh điển của ngành khoa học máy tính."),

    ("Việt Nam sử lược", "Trần Trọng Kim", "lich-su-dia-chi", "NXB Văn học", 1920, 596, "Tiếng Việt", "978-604-1-00021-9", 1,
     "Bộ thông sử bằng chữ quốc ngữ đầu tiên trình bày lịch sử Việt Nam một cách hệ thống từ thời dựng nước tới đầu thế kỷ 20."),
    ("Ngục trung thư", "Phan Bội Châu", "lich-su-dia-chi", "NXB Văn học", 1914, 184, "Tiếng Việt", "978-604-1-00022-6", 0,
     "Tự truyện viết trong ngục, ghi lại chặng đường hoạt động và tâm sự của một chí sĩ đầu thế kỷ 20."),
    ("Đông Kinh nghĩa thục", "Nguyễn Hiến Lê", "lich-su-dia-chi", "NXB Văn hóa Thông tin", 1968, 216, "Tiếng Việt", "978-604-9-00023-3", 0,
     "Biên khảo về phong trào giáo dục và canh tân đầu thế kỷ 20 tại Hà Nội."),
    ("Đạo Đức Kinh", "Lão Tử", "triet-hoc-tu-tuong", "NXB Hồng Đức", -500, 176, "Tiếng Việt", "978-604-A-00025-7", 1,
     "Tác phẩm nền tảng của Đạo gia gồm 81 chương ngắn, bàn về Đạo, Đức và lối sống thuận tự nhiên."),
    ("Suy tưởng", "Marcus Aurelius", "triet-hoc-tu-tuong", "NXB Trẻ", 180, 264, "Tiếng Việt", "978-604-3-00026-4", 1,
     "Tập ghi chép riêng tư của một hoàng đế La Mã, trở thành cẩm nang thực hành của chủ nghĩa Khắc Kỷ."),
    ("Tự học — một nhu cầu thời đại", "Nguyễn Hiến Lê", "triet-hoc-tu-tuong", "NXB Văn hóa Thông tin", 1964, 248, "Tiếng Việt", "978-604-9-00028-8", 0,
     "Bàn về phương pháp tự học và thái độ đọc sách, viết cho người trẻ muốn tự bồi đắp tri thức."),

    ("Từ ấy", "Tố Hữu", "van-hoc-viet-nam", "NXB Văn học", 1946, 132, "Tiếng Việt", "978-604-2-00029-5", 0,
     "Tập thơ đầu tay đánh dấu bước ngoặt trong đời thơ của tác giả, nhiều bài đã đi vào sách giáo khoa."),
    ("Pippi Tất Dài", "Astrid Lindgren", "thieu-nhi", "NXB Kim Đồng", 1945, 208, "Tiếng Việt", "978-604-2-00030-1", 1,
     "Cô bé khỏe nhất thế giới sống một mình cùng con khỉ và con ngựa, mang tới cho trẻ em tinh thần tự do và tưởng tượng."),
    ("Cho tôi xin một vé đi tuổi thơ", "Nguyễn Nhật Ánh", "thieu-nhi", "NXB Trẻ", 2008, 208, "Tiếng Việt", "978-604-3-00031-8", 1,
     "Người lớn kể lại tuổi thơ của chính mình bằng giọng hài hước, gợi nhớ những trò nghịch ngợm ai cũng từng trải qua."),
    ("Kính vạn hoa", "Nguyễn Nhật Ánh", "thieu-nhi", "NXB Kim Đồng", 1995, 224, "Tiếng Việt", "978-604-2-00032-5", 0,
     "Bộ truyện dài nhiều tập về nhóm bạn học trò và những tình huống dở khóc dở cười của tuổi mới lớn."),
]


def slugify(text: str) -> str:
    """Chuyển tiếng Việt có dấu thành slug ASCII dùng cho URL và tên tệp."""
    import re
    import unicodedata
    text = text.replace("đ", "d").replace("Đ", "D")
    text = unicodedata.normalize("NFD", text)
    text = "".join(c for c in text if unicodedata.category(c) != "Mn")
    text = text.lower()
    text = re.sub(r"[^a-z0-9]+", "-", text)
    return re.sub(r"-+", "-", text).strip("-")


def cover_filename(title: str) -> str:
    return f"{slugify(title)}.png"


CATEGORY_BY_SLUG = {c[0]: c for c in CATEGORIES}
AUTHOR_BY_NAME = {a[0]: a for a in AUTHORS}

if __name__ == "__main__":
    print(f"{len(CATEGORIES)} danh mục · {len(AUTHORS)} tác giả · {len(BOOKS)} cuốn sách")
    dup = len(BOOKS) - len({b[0] for b in BOOKS})
    print("Tựa trùng lặp:", dup)
    missing = {b[1] for b in BOOKS} - set(AUTHOR_BY_NAME)
    print("Tác giả thiếu:", missing or "không")
    badcat = {b[2] for b in BOOKS} - set(CATEGORY_BY_SLUG)
    print("Danh mục sai:", badcat or "không")
