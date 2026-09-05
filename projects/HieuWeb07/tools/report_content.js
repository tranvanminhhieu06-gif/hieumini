/**
 * Nội dung BaoCao.docx — bám đúng dàn ý của mucluc.txt.
 * Chạy:  NODE_PATH=<node_modules> node tools/report_content.js
 */
const fs = require('fs');
const B = require('./build_report.js');
const {
  p, pRich, h, bullet, code, table, figure, tableCaption, spacer, cover, toc,
  Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType, Footer,
  PageNumber, LevelFormat, BorderStyle, OUT, INK, MUTED, ACCENT,
} = B;

const H1 = HeadingLevel.HEADING_1, H2 = HeadingLevel.HEADING_2, H3 = HeadingLevel.HEADING_3;

/* ==================================================================
   CHƯƠNG 1 — TỔNG QUAN LẬP TRÌNH WEB
   ================================================================== */
const chuong1 = [
  h('Chương 1. Tổng quan lập trình web', H1, { pageBreak: true }),

  p('Chương này trình bày nền tảng kỹ thuật mà đồ án dựa vào: ngôn ngữ PHP ở phía máy chủ, '
    + 'hệ quản trị cơ sở dữ liệu MySQL ở phía lưu trữ, và môi trường máy chủ web dùng để chạy thử. '
    + 'Ba thành phần này ghép lại thành mô hình quen thuộc thường được gọi tắt là PHP + MySQL — '
    + 'nền tảng của phần lớn website động hiện nay.'),

  p('Trước khi đi vào từng thành phần, cần phân biệt hai khái niệm hay bị nhầm. Một trang web '
    + 'tĩnh là tệp HTML có sẵn trên đĩa, ai vào cũng nhận được nội dung y hệt nhau. Một trang web '
    + 'động thì được sinh ra ngay tại thời điểm có người truy cập: máy chủ chạy mã, lấy dữ liệu từ '
    + 'cơ sở dữ liệu, rồi ghép thành HTML gửi về. Website trong đồ án này thuộc loại thứ hai — '
    + 'thêm một cuốn sách vào cơ sở dữ liệu là trang chủ, trang kho sách và sơ đồ trang đều tự có '
    + 'cuốn đó, không phải sửa một dòng HTML nào.'),

  // ---------------- 1.1 ----------------
  h('1.1 Ngôn ngữ lập trình PHP', H2),

  p('PHP (viết tắt của "PHP: Hypertext Preprocessor") là ngôn ngữ kịch bản chạy ở phía máy chủ, '
    + 'ra đời năm 1995 và tới nay vẫn là ngôn ngữ phổ biến nhất cho web động. Đặc điểm quan trọng '
    + 'nhất của PHP là mã nguồn không bao giờ đến tay người dùng: trình duyệt chỉ nhận được HTML '
    + 'đã hoàn chỉnh. Nhờ vậy, các thông tin nhạy cảm như mật khẩu cơ sở dữ liệu hay logic kiểm '
    + 'tra quyền đều nằm an toàn trên máy chủ.'),

  h('1.1.1 Cách một tệp PHP được xử lý', H3),

  p('Khi trình duyệt yêu cầu một tệp có đuôi .php, máy chủ web không gửi thẳng tệp đó đi mà '
    + 'chuyển cho bộ thông dịch PHP. Bộ thông dịch đọc tệp từ trên xuống, gặp đoạn nằm giữa cặp '
    + 'thẻ mở và đóng thì thực thi, phần còn lại giữ nguyên. Kết quả cuối cùng là một chuỗi HTML '
    + 'thuần được gửi về trình duyệt.'),

  ...figure('so-do-luong-xu-ly.png',
    'Luồng xử lý một yêu cầu từ lúc người dùng bấm tới lúc trang hiện ra', 610),

  p('Sơ đồ trên cũng cho thấy hai chốt chặn an toàn quan trọng nhất của đồ án, sẽ được nói kỹ ở '
    + 'mục 2.2: giá trị người dùng nhập chỉ đi vào câu lệnh SQL dưới dạng tham số, và chỉ được in '
    + 'ra HTML sau khi đã mã hoá ký tự đặc biệt.'),

  h('1.1.2 Những đặc điểm của PHP được dùng trong đồ án', H3),

  bullet('Cú pháp trộn được với HTML: một tệp .php có thể vừa chứa mã xử lý vừa chứa khung giao diện, '
    + 'rất tiện cho các trang có cấu trúc đơn giản như trang chi tiết sách.'),
  bullet('Hàm và tệp dùng chung: câu lệnh require cho phép tách phần đầu trang, chân trang, thẻ sách '
    + 'ra tệp riêng rồi gọi lại ở nhiều nơi — sửa một chỗ là cả website đổi theo.'),
  bullet('Phiên làm việc (session): lưu trạng thái đăng nhập của quản trị viên và mã chống giả mạo '
    + 'biểu mẫu giữa các lần tải trang, vì bản thân giao thức HTTP không nhớ gì cả.'),
  bullet('PDO (PHP Data Objects): lớp trung gian thống nhất để làm việc với cơ sở dữ liệu, hỗ trợ '
    + 'câu lệnh có tham số — công cụ chính chống lỗ hổng SQL Injection.'),
  bullet('Hàm băm mật khẩu có sẵn: password_hash() và password_verify() dùng thuật toán bcrypt, '
    + 'tự sinh muối ngẫu nhiên, không cần cài thêm thư viện nào.'),

  h('1.1.3 Vì sao chọn PHP thuần, không dùng framework', H3),

  p('Các framework như Laravel hay Symfony giúp viết nhanh hơn nhiều, nhưng chúng che đi phần lớn '
    + 'những gì đang thực sự diễn ra: định tuyến, kết nối cơ sở dữ liệu, dựng truy vấn, dựng giao '
    + 'diện đều được framework lo sẵn. Với một đồ án môn học mà mục tiêu là hiểu cơ chế, việc tự '
    + 'viết từng lớp có giá trị hơn. Đổi lại, dự án phải tự làm những việc mà framework vốn làm hộ: '
    + 'tự escape đầu ra, tự sinh và kiểm tra mã CSRF, tự phân trang. Toàn bộ những phần đó nằm trong '
    + 'tệp includes/functions.php và được trình bày ở Chương 2.'),

  p('Một lợi ích thực tế nữa: dự án không cần Composer và không có thư mục vendor hàng chục nghìn '
    + 'tệp. Chép nguyên thư mục vào htdocs là chạy được — điều này quan trọng khi nộp bài và khi '
    + 'chấm trên máy khác.'),

  h('1.1.4 Một ví dụ cụ thể trong đồ án', H3),

  p('Đoạn mã dưới đây trích từ tệp config/database.php, là nơi mở kết nối tới cơ sở dữ liệu. '
    + 'Ba tuỳ chọn truyền vào PDO đều có lý do rõ ràng:'),

  code([
    "$pdo = new PDO($dsn, DB_USER, DB_PASS, [",
    "    // Lỗi ném ra dưới dạng ngoại lệ, không im lặng trả về false",
    "    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,",
    "    // Mặc định lấy mảng kết hợp, khỏi chỉ định ở từng lệnh fetch",
    "    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,",
    "    // Dùng prepared statement THẬT của MySQL thay vì bản giả lập",
    "    PDO::ATTR_EMULATE_PREPARES   => false,",
    "]);",
  ]),

  p('Tuỳ chọn thứ ba đáng chú ý nhất. Mặc định PDO tự ghép tham số vào chuỗi SQL rồi mới gửi đi '
    + '(gọi là giả lập). Khi tắt chế độ giả lập, câu lệnh và tham số được gửi riêng, máy chủ MySQL '
    + 'tự tách bạch hai thứ — kẻ tấn công không còn cách nào chèn cú pháp SQL vào ô tìm kiếm. '
    + 'Đây cũng là thiết lập đã làm lộ ra một lỗi thật trong quá trình kiểm thử, sẽ kể ở mục 3.2.'),

  // ---------------- 1.2 ----------------
  h('1.2 Hệ quản trị cơ sở dữ liệu MySQL', H2),

  p('MySQL là hệ quản trị cơ sở dữ liệu quan hệ mã nguồn mở, lưu dữ liệu dưới dạng các bảng có '
    + 'hàng và cột, liên kết với nhau qua khoá. Trong đồ án này, máy chủ MySQL đóng vai trò nơi '
    + 'lưu trữ duy nhất: toàn bộ sách, tác giả, thể loại, đánh giá, tin nhắn và tài khoản quản trị '
    + 'đều nằm trong cơ sở dữ liệu, không có dữ liệu nào viết cứng trong mã nguồn.'),

  h('1.2.1 Vì sao chọn mô hình quan hệ', H3),

  p('Dữ liệu của một thư viện sách có quan hệ rất rõ ràng: một tác giả viết nhiều cuốn, một thể '
    + 'loại chứa nhiều cuốn, một cuốn có nhiều đánh giá. Mô hình quan hệ diễn đạt được chính xác '
    + 'những ràng buộc này và bắt cơ sở dữ liệu tự bảo vệ tính toàn vẹn: không thể thêm một cuốn '
    + 'sách trỏ tới tác giả không tồn tại, và khi xoá một cuốn thì đánh giá của nó tự biến mất '
    + 'thay vì nằm lại thành dữ liệu mồ côi.'),

  h('1.2.2 Các khái niệm được sử dụng', H3),

  table(
    ['Khái niệm', 'Ý nghĩa', 'Áp dụng trong đồ án'],
    [
      ['Khoá chính\n(PRIMARY KEY)', 'Cột định danh duy nhất mỗi hàng',
       'Cột id của cả sáu bảng, tự tăng'],
      ['Khoá ngoại\n(FOREIGN KEY)', 'Ràng buộc một cột phải trỏ tới hàng có thật ở bảng khác',
       'books.author_id → authors.id;\nbooks.category_id → categories.id;\nreviews.book_id → books.id'],
      ['ON DELETE\nCASCADE', 'Xoá hàng cha thì hàng con tự xoá theo',
       'Xoá một cuốn sách thì mọi đánh giá của nó tự dọn'],
      ['Chỉ mục\n(INDEX)', 'Cấu trúc giúp tìm nhanh mà không phải quét cả bảng',
       'Trên category_id, author_id, title, is_featured'],
      ['Ràng buộc\n(CHECK)', 'Điều kiện dữ liệu phải thoả mãn mới được ghi',
       'reviews.rating phải nằm trong khoảng 1–5'],
      ['Bộ mã\nutf8mb4', 'Bộ ký tự lưu được toàn bộ Unicode, mỗi ký tự tối đa 4 byte',
       'Toàn bộ cơ sở dữ liệu — cần cho dấu tiếng Việt'],
      ['Engine\nInnoDB', 'Bộ máy lưu trữ hỗ trợ giao dịch và khoá ngoại',
       'Cả sáu bảng'],
    ],
    [2000, 3000, 4020]
  ),
  tableCaption('Các khái niệm cơ sở dữ liệu được dùng trong đồ án'),

  h('1.2.3 Vì sao bắt buộc dùng utf8mb4', H3),

  p('Đây là chi tiết nhỏ nhưng gây lỗi nhiều nhất với website tiếng Việt. Bộ mã utf8 cũ của MySQL '
    + 'thực chất chỉ lưu được tối đa 3 byte cho một ký tự, trong khi Unicode cần tới 4 byte cho một '
    + 'số ký tự. Nếu tạo cơ sở dữ liệu bằng bộ mã mặc định của máy chủ (thường là latin1), chữ '
    + 'tiếng Việt có dấu sẽ hiện thành dấu hỏi hoặc ký tự lạ, và một khi dữ liệu đã ghi sai thì '
    + 'gần như không cứu được. Vì vậy tệp SQL của đồ án khai báo bộ mã ngay từ câu lệnh tạo cơ sở '
    + 'dữ liệu:'),

  code([
    'CREATE DATABASE IF NOT EXISTS `hieumini_books_db`',
    '  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
  ]),

  p('Kiểu đối chiếu utf8mb4_unicode_ci còn mang lại một lợi ích ngoài dự tính: nó không phân biệt '
    + 'dấu, nên người dùng gõ "de men" không dấu vẫn tìm ra "Dế Mèn phiêu lưu ký". Điều này đã được '
    + 'kiểm chứng trong phần thử nghiệm ở Chương 3.'),

  // ---------------- 1.3 ----------------
  h('1.3 Cài đặt máy chủ', H2),

  p('Để chạy một website PHP trên máy cá nhân cần ba thành phần: máy chủ web nhận yêu cầu HTTP, '
    + 'bộ thông dịch PHP, và máy chủ cơ sở dữ liệu. Bộ cài XAMPP gói sẵn cả ba nên chỉ cần cài một '
    + 'lần là đủ.'),

  h('1.3.1 Các thành phần và vai trò', H3),

  table(
    ['Thành phần', 'Phiên bản dùng thử', 'Vai trò'],
    [
      ['Apache', '2.4', 'Nhận yêu cầu HTTP, chuyển tệp .php cho PHP, trả HTML về trình duyệt'],
      ['PHP', '8.0 trở lên', 'Thông dịch mã, kết nối cơ sở dữ liệu, sinh HTML'],
      ['MySQL / MariaDB', '5.7 / 10.4 trở lên', 'Lưu trữ và truy vấn dữ liệu'],
      ['phpMyAdmin', 'kèm XAMPP', 'Giao diện web để xem và nạp cơ sở dữ liệu'],
    ],
    [2200, 2200, 4620]
  ),
  tableCaption('Các thành phần của môi trường máy chủ'),

  h('1.3.2 Các bước cài đặt', H3),

  p('Bước 1 — Chép mã nguồn vào thư mục web. XAMPP phục vụ nội dung từ thư mục htdocs, nên mã nguồn '
    + 'phải nằm bên trong đó:'),
  code(['C:\\xampp\\htdocs\\HieuWebsite\\projects\\HieuWeb07\\']),

  p('Bước 2 — Khởi động dịch vụ. Mở XAMPP Control Panel, bấm Start ở hai dòng Apache và MySQL. '
    + 'Nếu Apache không khởi động được thì thường do cổng 80 đang bị chiếm (hay gặp nhất là do '
    + 'Skype hoặc IIS), có thể đổi sang cổng khác trong tệp cấu hình httpd.conf.'),

  p('Bước 3 — Nạp cơ sở dữ liệu. Chạy tệp SQL kèm theo dự án. Có thể dùng dòng lệnh:'),
  code([
    'cd C:\\xampp\\mysql\\bin',
    'mysql -u root < "...\\HieuWeb07\\database\\hieumini_books_db.sql"',
  ]),
  p('hoặc mở phpMyAdmin tại http://localhost/phpmyadmin, chọn tab Import rồi chỉ tới tệp đó. '
    + 'Tệp SQL tự tạo cơ sở dữ liệu, tạo sáu bảng và nạp sẵn 30 cuốn sách, 20 tác giả cùng 66 đánh '
    + 'giá mẫu, nên sau bước này website đã có dữ liệu để hiển thị ngay.'),

  p('Bước 4 — Kiểm tra thông tin kết nối. Bốn hằng số ở đầu tệp config/config.php phải khớp với '
    + 'máy chủ đang chạy. Với XAMPP mặc định thì giữ nguyên là được:'),
  code([
    "const DB_HOST = '127.0.0.1';",
    "const DB_NAME = 'hieumini_books_db';",
    "const DB_USER = 'root';",
    "const DB_PASS = '';        // XAMPP mặc định để trống",
  ]),

  p('Bước 5 — Mở website tại địa chỉ http://localhost/HieuWebsite/projects/HieuWeb07/. '
    + 'Nếu trang báo không kết nối được cơ sở dữ liệu thì gần như chắc chắn là MySQL chưa bật hoặc '
    + 'bước 3 chưa chạy xong.'),

  h('1.3.3 Hai chế độ chạy: phát triển và triển khai thật', H3),

  p('Tệp cấu hình có hằng số IS_DEV để phân biệt hai môi trường. Khi bằng true, mọi lỗi PHP được '
    + 'in thẳng ra màn hình kèm đường dẫn tệp và số dòng — rất tiện lúc đang viết. Khi đưa lên máy '
    + 'chủ thật phải đổi thành false: lúc đó lỗi được ghi vào tệp log, còn người dùng chỉ thấy một '
    + 'thông báo chung. Lý do là thông báo lỗi mặc định của PHP để lộ đường dẫn thư mục, tên tệp và '
    + 'đôi khi cả câu truy vấn — những thông tin rất có giá trị với người muốn tấn công.'),
];

/* ==================================================================
   CHƯƠNG 2 — PHÂN TÍCH VÀ THIẾT KẾ WEBSITE
   ================================================================== */
const chuong2 = [
  h('Chương 2. Phân tích và thiết kế website', H1, { pageBreak: true }),

  p('Chương này mô tả website được phân tích và thiết kế ra sao trước khi viết dòng mã đầu tiên: '
    + 'ai sẽ dùng, họ làm được những gì, và dữ liệu được tổ chức thế nào để đáp ứng các chức năng đó.'),

  p('Phạm vi của đồ án được xác định ngay từ đầu là một thư viện tra cứu, không phải một cửa hàng '
    + 'bán sách. Nghĩa là có duyệt, tìm kiếm, xem chi tiết và đánh giá, nhưng không có giỏ hàng, '
    + 'đặt hàng hay thanh toán. Quyết định này giúp dồn công sức vào chất lượng phần tra cứu và '
    + 'phần giao diện thay vì làm mỏng đều mọi thứ.'),

  // ---------------- 2.1 ----------------
  h('2.1 Chức năng (usecase)', H2),

  h('2.1.1 Xác định tác nhân', H3),

  p('Hệ thống có hai tác nhân với quyền hạn tách bạch hoàn toàn:'),

  bullet('Bạn đọc — người dùng không cần đăng nhập. Có thể xem mọi nội dung công khai và gửi đánh '
    + 'giá, gửi tin nhắn liên hệ. Không sửa được bất cứ dữ liệu nào đã có.'),
  bullet('Quản trị viên — phải đăng nhập bằng tài khoản trong bảng admins. Toàn quyền thêm, sửa, '
    + 'xoá sách và duyệt hoặc gỡ đánh giá của bạn đọc.'),

  p('Điểm đáng chú ý trong thiết kế: đánh giá do bạn đọc gửi lên không hiển thị ngay mà được lưu '
    + 'với trạng thái chờ duyệt. Quản trị viên phải duyệt thì nội dung mới xuất hiện công khai. '
    + 'Đây là cách đơn giản và hiệu quả để chặn nội dung rác, vốn là vấn đề chắc chắn xảy ra với '
    + 'mọi biểu mẫu mở cho người lạ.'),

  h('2.1.2 Sơ đồ use case', H3),

  ...figure('so-do-usecase.png', 'Sơ đồ use case của hệ thống HieuMini Books', 590),

  p('Các nét đứt trong sơ đồ thể hiện quan hệ «include»: mọi chức năng quản trị đều bao hàm việc '
    + 'đã đăng nhập thành công. Trong mã nguồn, ràng buộc này được hiện thực bằng một tệp duy nhất '
    + 'là admin/guard.php — mọi trang trong thư mục admin đều gọi nó ở dòng đầu tiên, nên không thể '
    + 'quên sót ở trang nào.'),

  h('2.1.3 Đặc tả chi tiết các use case chính', H3),

  p('Ba use case dưới đây được đặc tả kỹ vì chúng chứa phần lớn logic nghiệp vụ của hệ thống.'),

  table(
    ['Mục', 'Nội dung'],
    [
      ['Tên use case', 'Tìm kiếm sách'],
      ['Tác nhân', 'Bạn đọc'],
      ['Điều kiện trước', 'Đang ở trang Kho sách'],
      ['Luồng chính',
       '1. Người dùng gõ từ khoá vào ô tìm kiếm\n'
       + '2. Sau 220 mili giây kể từ phím cuối, hệ thống gọi api/suggest.php\n'
       + '3. Hệ thống hiện tối đa 6 gợi ý kèm bìa sách, ưu tiên tựa bắt đầu bằng từ khoá\n'
       + '4. Người dùng chọn một gợi ý hoặc nhấn Enter để xem toàn bộ kết quả\n'
       + '5. Hệ thống hiển thị danh sách sách khớp, có phân trang'],
      ['Luồng thay thế',
       '3a. Không có kết quả: hiện thông báo kèm gợi ý các thể loại để người dùng đi tiếp,\n'
       + '     tuyệt đối không để màn hình trống\n'
       + '2a. Từ khoá dưới 2 ký tự: không gọi máy chủ (mọi thứ đều khớp, gọi cũng vô ích)'],
      ['Điều kiện sau', 'Danh sách kết quả được hiển thị; điều kiện lọc nằm trên URL nên chia sẻ được'],
    ],
    [2200, 6820]
  ),
  tableCaption('Đặc tả use case "Tìm kiếm sách"'),

  table(
    ['Mục', 'Nội dung'],
    [
      ['Tên use case', 'Gửi đánh giá'],
      ['Tác nhân', 'Bạn đọc'],
      ['Điều kiện trước', 'Đang ở trang chi tiết của một cuốn sách'],
      ['Luồng chính',
       '1. Người dùng nhập tên, chọn số sao và viết nhận xét\n'
       + '2. Hệ thống kiểm tra mã CSRF của biểu mẫu\n'
       + '3. Hệ thống kiểm tra dữ liệu: tên ≥ 2 ký tự, sao trong 1–5, nhận xét 10–1000 ký tự\n'
       + '4. Ghi vào bảng reviews với is_approved = 0\n'
       + '5. Chuyển hướng về trang sách kèm thông báo đã nhận'],
      ['Luồng thay thế',
       '2a. Mã CSRF sai hoặc hết hạn: từ chối ghi, báo người dùng tải lại trang\n'
       + '3a. Dữ liệu không hợp lệ: hiện lỗi ngay dưới đúng ô sai, giữ nguyên nội dung đã gõ'],
      ['Điều kiện sau', 'Đánh giá nằm trong hàng chờ duyệt, chưa hiển thị công khai'],
    ],
    [2200, 6820]
  ),
  tableCaption('Đặc tả use case "Gửi đánh giá"'),

  table(
    ['Mục', 'Nội dung'],
    [
      ['Tên use case', 'Đăng nhập quản trị'],
      ['Tác nhân', 'Quản trị viên'],
      ['Luồng chính',
       '1. Nhập tên đăng nhập và mật khẩu\n'
       + '2. Hệ thống tìm tài khoản và so mật khẩu bằng password_verify()\n'
       + '3. Sinh lại ID phiên (chống tấn công cố định phiên)\n'
       + '4. Lưu admin_id vào phiên và chuyển tới trang người dùng định vào ban đầu'],
      ['Luồng thay thế',
       '2a. Sai thông tin: tăng bộ đếm, báo lỗi chung không nói rõ sai tên hay sai mật khẩu\n'
       + '2b. Sai quá 5 lần trong 10 phút: khoá biểu mẫu, buộc chờ'],
      ['Ghi chú thiết kế',
       'Thông báo lỗi cố tình mơ hồ. Nếu báo "tên đăng nhập không tồn tại", kẻ tấn công sẽ\n'
       + 'biết được tài khoản nào có thật và chỉ cần tập trung dò mật khẩu của tài khoản đó.'],
    ],
    [2200, 6820]
  ),
  tableCaption('Đặc tả use case "Đăng nhập quản trị"'),

  h('2.1.4 Danh sách trang và chức năng tương ứng', H3),

  table(
    ['Tệp', 'Chức năng chính'],
    [
      ['index.php', 'Trang chủ: banner, sách nổi bật, danh sách thể loại, sách mới'],
      ['books.php', 'Kho sách: tìm kiếm, lọc theo thể loại, sắp xếp, phân trang'],
      ['book.php', 'Chi tiết sách: thông tin xuất bản, tóm tắt, đánh giá, biểu mẫu gửi đánh giá'],
      ['authors.php / author.php', 'Danh sách tác giả và trang riêng từng tác giả'],
      ['about.php / contact.php', 'Giới thiệu dự án và biểu mẫu liên hệ'],
      ['api/suggest.php', 'Trả JSON gợi ý tìm kiếm cho ô tìm kiếm'],
      ['sitemap.php', 'Sinh sơ đồ trang XML cho công cụ tìm kiếm'],
      ['admin/login.php', 'Đăng nhập quản trị, có chặn dò mật khẩu'],
      ['admin/index.php', 'Bảng điều khiển: số liệu tổng quan, việc đang chờ xử lý'],
      ['admin/books.php\nadmin/book_form.php', 'Danh sách sách và biểu mẫu thêm/sửa'],
      ['admin/reviews.php', 'Duyệt, ẩn hoặc xoá đánh giá'],
      ['admin/messages.php', 'Hộp thư liên hệ'],
    ],
    [2800, 6220]
  ),
  tableCaption('Danh sách trang và chức năng'),

  // ---------------- 2.2 ----------------
  h('2.2 Cơ sở dữ liệu', H2),

  h('2.2.1 Sơ đồ quan hệ thực thể', H3),

  ...figure('so-do-erd.png', 'Sơ đồ quan hệ thực thể (ERD) của cơ sở dữ liệu hieumini_books_db', 610),

  p('Cơ sở dữ liệu gồm sáu bảng. Bốn bảng đầu (categories, authors, books, reviews) liên kết với '
    + 'nhau thành một cụm phục vụ phần nội dung. Hai bảng còn lại (admins, messages) đứng độc lập '
    + 'vì chúng không có quan hệ nghiệp vụ với sách: một tài khoản quản trị không "sở hữu" cuốn '
    + 'sách nào, và một tin nhắn liên hệ cũng không gắn với cuốn nào.'),

  h('2.2.2 Mô tả chi tiết các bảng', H3),

  p('Bảng books là bảng trung tâm, mọi truy vấn hiển thị đều đi qua nó:'),

  table(
    ['Cột', 'Kiểu dữ liệu', 'Ràng buộc', 'Ý nghĩa'],
    [
      ['id', 'INT', 'PK, tự tăng', 'Định danh'],
      ['slug', 'VARCHAR(180)', 'UNIQUE', 'Chuỗi không dấu dùng trên URL'],
      ['title', 'VARCHAR(220)', 'NOT NULL', 'Tựa sách'],
      ['author_id', 'INT', 'FK → authors', 'Tác giả'],
      ['category_id', 'INT', 'FK → categories', 'Thể loại'],
      ['publisher', 'VARCHAR(160)', '', 'Nhà xuất bản'],
      ['published_year', 'SMALLINT', '', 'Năm xuất bản (số âm = trước Công nguyên)'],
      ['pages', 'SMALLINT', '', 'Số trang'],
      ['language', 'VARCHAR(40)', 'mặc định Tiếng Việt', 'Ngôn ngữ'],
      ['isbn', 'VARCHAR(24)', '', 'Mã ISBN'],
      ['cover', 'VARCHAR(180)', '', 'Tên tệp ảnh bìa'],
      ['summary', 'TEXT', '', 'Tóm tắt nội dung'],
      ['is_featured', 'TINYINT(1)', 'mặc định 0', 'Có hiện ở trang chủ hay không'],
      ['views', 'INT', 'mặc định 0', 'Số lượt xem'],
      ['created_at', 'DATETIME', 'mặc định hiện tại', 'Thời điểm thêm vào kho'],
    ],
    [2000, 2000, 2200, 2820]
  ),
  tableCaption('Cấu trúc bảng books'),

  table(
    ['Bảng', 'Số cột', 'Vai trò', 'Quan hệ'],
    [
      ['categories', '7', 'Sáu thể loại, mỗi thể loại có màu nền và màu nhấn riêng dùng khi sinh bìa sách', '1–N với books'],
      ['authors', '6', 'Hai mươi tác giả kèm quốc gia, năm sinh và tiểu sử ngắn', '1–N với books'],
      ['books', '15', 'Ba mươi đầu sách — bảng trung tâm', 'N–1 với authors và categories; 1–N với reviews'],
      ['reviews', '7', 'Đánh giá của bạn đọc, có cờ duyệt', 'N–1 với books'],
      ['admins', '5', 'Tài khoản quản trị, mật khẩu băm bcrypt', 'Độc lập'],
      ['messages', '7', 'Tin nhắn từ biểu mẫu liên hệ', 'Độc lập'],
    ],
    [1700, 900, 4200, 2220]
  ),
  tableCaption('Tổng hợp sáu bảng của cơ sở dữ liệu'),

  h('2.2.3 Chuẩn hoá dữ liệu', H3),

  p('Thiết kế đạt dạng chuẩn 3 (3NF). Cụ thể: mỗi ô chỉ chứa một giá trị đơn (1NF); mọi cột không '
    + 'khoá đều phụ thuộc vào toàn bộ khoá chính (2NF); và không có cột nào phụ thuộc bắc cầu qua '
    + 'cột không khoá khác (3NF).'),

  p('Ví dụ cụ thể cho 3NF: tên tác giả không được lưu trong bảng books mà chỉ lưu author_id. '
    + 'Nếu lưu cả tên, khi sửa chính tả tên một tác giả sẽ phải sửa ở tất cả các dòng sách của người '
    + 'đó — sót một dòng là dữ liệu mâu thuẫn. Tách ra bảng riêng thì chỉ có đúng một chỗ để sửa.'),

  p('Có một ngoại lệ cố ý: điểm trung bình và số lượng đánh giá của mỗi cuốn không được lưu thành '
    + 'cột trong bảng books mà tính trực tiếp bằng truy vấn con mỗi lần hiển thị. Cách này chậm hơn '
    + 'một chút nhưng không bao giờ có nguy cơ số liệu lệch với thực tế — với quy mô 30 cuốn sách '
    + 'thì đánh đổi đó hoàn toàn xứng đáng.'),

  h('2.2.4 Truy vấn tiêu biểu', H3),

  p('Truy vấn dưới đây được dùng lại ở hầu hết các trang, thông qua hàm book_select_sql() trong '
    + 'includes/functions.php. Nó nối bảng sách với tác giả và thể loại, đồng thời tính sẵn điểm '
    + 'đánh giá trung bình:'),

  code([
    'SELECT b.*, a.name AS author_name, c.name AS category_name,',
    '       (SELECT ROUND(AVG(r.rating), 1) FROM reviews r',
    '         WHERE r.book_id = b.id AND r.is_approved = 1) AS rating_avg,',
    '       (SELECT COUNT(*) FROM reviews r',
    '         WHERE r.book_id = b.id AND r.is_approved = 1) AS rating_count',
    'FROM books b',
    'JOIN authors a    ON a.id = b.author_id',
    'JOIN categories c ON c.id = b.category_id',
  ]),

  p('Điều kiện is_approved = 1 xuất hiện trong cả hai truy vấn con, bảo đảm đánh giá chưa duyệt '
    + 'không ảnh hưởng tới điểm trung bình hiển thị công khai.'),

  h('2.2.5 Chống SQL Injection', H3),

  p('Mọi giá trị đến từ người dùng đều được truyền dưới dạng tham số, không bao giờ nối vào chuỗi '
    + 'SQL. So sánh hai cách viết sau:'),

  code([
    '// SAI — kẻ tấn công gõ:  a\' OR 1=1 --  là lấy được toàn bộ dữ liệu',
    '$sql = "SELECT * FROM books WHERE title LIKE \'%$q%\'";',
    '',
    '// ĐÚNG — câu lệnh và dữ liệu đi riêng, MySQL tự tách bạch',
    '$sql = "SELECT * FROM books WHERE title LIKE :q_title";',
    'db_query($sql, [\':q_title\' => "%$q%"]);',
  ]),

  p('Riêng tham số sắp xếp không thể truyền dạng tham số vì nó là một phần cú pháp của câu lệnh, '
    + 'không phải giá trị. Trường hợp này dùng danh sách trắng: giá trị từ URL chỉ được dùng làm '
    + 'khoá tra vào một mảng định sẵn, gõ gì khác cũng rơi về giá trị mặc định.'),

  code([
    "$sortMap = [",
    "    'moi'       => 'b.published_year DESC, b.id DESC',",
    "    'cu'        => 'b.published_year ASC, b.id ASC',",
    "    'ten'       => 'b.title ASC',",
    "    'xem-nhieu' => 'b.views DESC',",
    "];",
    "$orderBy = $sortMap[$sort] ?? $sortMap['moi'];",
  ]),
];

/* ==================================================================
   CHƯƠNG 3 — CHƯƠNG TRÌNH THỬ NGHIỆM
   ================================================================== */
const chuong3 = [
  h('Chương 3. Chương trình thử nghiệm', H1, { pageBreak: true }),

  p('Chương này trình bày kết quả thực tế: giao diện của các trang chính, cách kiểm thử được tiến '
    + 'hành, những lỗi tìm ra và đánh giá tổng kết.'),

  // ---------------- 3.1 ----------------
  h('3.1 Giao diện', H2),

  h('3.1.1 Định hướng thiết kế', H3),

  p('Giao diện đi theo phong cách Swiss Modernism biến thể tối — lưới 12 cột, khoảng thở rộng, '
    + 'gần như không có hoạ tiết trang trí. Mọi nhấn nhá dồn vào chữ và một sắc đồng thau duy nhất. '
    + 'Lựa chọn này phù hợp với nội dung sách: người vào đây để đọc thông tin, nên chữ phải là thứ '
    + 'nổi bật nhất chứ không phải hiệu ứng.'),

  p('Bộ chữ gồm hai phông: Cormorant Garamond cho tiêu đề và số liệu, Crimson Pro cho thân bài. '
    + 'Cả hai đều là phông serif có bộ ký tự tiếng Việt đầy đủ và được tự host trong thư mục dự án, '
    + 'nên website chạy được cả khi máy không nối mạng và không để lộ địa chỉ IP người đọc sang máy '
    + 'chủ của bên thứ ba.'),

  table(
    ['Token màu', 'Giao diện tối (mặc định)', 'Giao diện sáng'],
    [
      ['Nền chính', '#0E0D0C', '#FBF9F4'],
      ['Chữ chính', '#EFEADF', '#16130E'],
      ['Chữ phụ', '#B4AB99', '#4A443A'],
      ['Màu nhấn', '#D8A94A', '#8A6412'],
      ['Đường viền', '#2B2620', '#E0D9CA'],
    ],
    [3000, 3010, 3010]
  ),
  tableCaption('Bảng màu của hai giao diện'),

  p('Màu nhấn ở giao diện sáng đậm hơn hẳn giao diện tối. Đây không phải lựa chọn thẩm mỹ mà là '
    + 'yêu cầu bắt buộc: sắc vàng đồng #D8A94A rất hợp trên nền đen nhưng đặt trên nền giấy sáng '
    + 'thì tỉ lệ tương phản tụt xuống dưới ngưỡng đọc được. Phải chuyển sang #8A6412 mới đạt chuẩn '
    + '4,5:1 của WCAG AA.'),

  h('3.1.2 Trang chủ', H3),

  ...figure('shot-trangchu-toi.png', 'Trang chủ — giao diện tối (mặc định)', 600),

  p('Banner dùng bố cục bất đối xứng: phần chữ chiếm 7 trên 12 cột, khối ba bìa sách xếp chồng '
    + 'chiếm 4 cột còn lại. Bốn con số thống kê phía dưới được lấy trực tiếp từ cơ sở dữ liệu chứ '
    + 'không viết cứng, nên thêm sách vào là số tự đổi theo.'),

  ...figure('shot-trangchu-sang.png', 'Trang chủ — giao diện sáng', 600),

  p('Nút chuyển sáng/tối nằm ở góc phải thanh điều hướng. Lựa chọn của người đọc được lưu trong '
    + 'localStorage và áp dụng lại ngay ở lần vào sau, trước khi trình duyệt vẽ trang, nên không có '
    + 'hiện tượng chớp nền trắng rồi mới đổi sang tối.'),

  h('3.1.3 Trang kho sách', H3),

  ...figure('shot-khosach.png', 'Trang kho sách với ô tìm kiếm, chip lọc thể loại và lưới sách', 600),

  p('Toàn bộ điều kiện lọc nằm trên URL dưới dạng tham số, ví dụ books.php?the-loai=van-hoc-viet-nam'
    + '&sap-xep=ten. Cách này đem lại ba lợi ích: người dùng chia sẻ được đường dẫn kết quả, nút '
    + 'Quay lại của trình duyệt hoạt động đúng như mong đợi, và công cụ tìm kiếm lập chỉ mục được '
    + 'từng trang thể loại riêng.'),

  p('Khi không có kết quả, trang không để màn hình trống mà hiện thông báo nói rõ vì sao, kèm các '
    + 'chip thể loại để người dùng đi tiếp. Đây là chi tiết nhỏ nhưng tạo khác biệt lớn: một màn '
    + 'hình trắng khiến người dùng nghĩ website bị lỗi và đóng tab.'),

  h('3.1.4 Trang chi tiết sách', H3),

  ...figure('shot-chitiet.png', 'Trang chi tiết sách với bảng thông tin xuất bản', 600),

  p('Trang này còn nhúng dữ liệu có cấu trúc theo chuẩn schema.org kiểu Book, gồm cả điểm đánh giá '
    + 'tổng hợp. Nhờ vậy khi trang xuất hiện trên Google, kết quả có thể hiện kèm tên tác giả, nhà '
    + 'xuất bản và số sao ngay dưới tiêu đề.'),

  h('3.1.5 Giao diện trên điện thoại', H3),

  ...figure('shot-mobile.png', 'Trang kho sách trên màn hình rộng 390 pixel', 300),

  p('Thiết kế theo hướng ưu tiên di động. Lưới sách tự giảm số cột, thanh điều hướng thu thành nút '
    + 'menu, và mọi vùng bấm đều giữ kích thước tối thiểu 44×44 pixel theo khuyến nghị của WCAG — '
    + 'nhỏ hơn ngưỡng này thì ngón tay bấm hay trượt.'),

  h('3.1.6 Khu vực quản trị', H3),

  ...figure('shot-admin.png', 'Bảng điều khiển quản trị', 600),

  p('Bảng điều khiển đặt hai số liệu cần hành động — đánh giá chờ duyệt và tin nhắn chưa đọc — '
    + 'nổi bật bằng viền màu nhấn khi khác không, để quản trị viên nhìn một cái là biết có việc cần làm.'),

  h('3.1.7 Hiệu ứng chuyển động', H3),

  p('Website dùng bốn thư viện: Lenis cho cuộn mượt, GSAP làm nền tảng hoạt ảnh, ScrollTrigger để '
    + 'kích hoạt hiệu ứng theo vị trí cuộn, và SplitText để tách tiêu đề thành từng dòng, từng từ.'),

  p('Nguyên tắc xuyên suốt: hiệu ứng là lớp phủ thêm, không phải điều kiện để đọc được nội dung. '
    + 'Nguyên tắc này được bảo đảm bằng ba tầng:'),

  bullet('CSS chỉ ẩn nội dung khi JavaScript thực sự chạy. Lớp .js do chính JavaScript gắn vào thẻ '
    + 'html, và quy tắc ẩn nằm sau bộ chọn đó. Tắt JavaScript hoặc robot tìm kiếm ghé qua thì chữ '
    + 'hiện bình thường — nếu ẩn vô điều kiện, nội dung sẽ biến mất khỏi kết quả tìm kiếm.'),
  bullet('Thiếu thư viện thì tự lui về cách viết tay. Mã kiểm tra sự tồn tại của từng thư viện '
    + 'trước khi dùng; không có GSAP thì chuyển sang IntersectionObserver của trình duyệt.'),
  bullet('Tôn trọng thiết lập giảm chuyển động của hệ điều hành. Khi người dùng bật tuỳ chọn này '
    + '(thường vì lý do sức khoẻ như say chuyển động), Lenis không khởi tạo, tiêu đề không tách chữ, '
    + 'nội dung hiện ngay lập tức.'),

  p('Một quyết định kỹ thuật riêng cho tiếng Việt: tiêu đề được tách theo TỪ chứ không theo ký tự. '
    + 'Tiếng Việt đặt dấu thanh trên nguyên âm, nếu chẻ tới từng ký tự rồi cho chúng chuyển động '
    + 'riêng thì dấu dễ tách rời khỏi chữ trong lúc chạy, đọc rất khó chịu.'),

  // ---------------- 3.2 ----------------
  h('3.2 Kết luận', H2),

  h('3.2.1 Phương pháp kiểm thử', H3),

  p('Chương trình được kiểm thử trên môi trường PHP 8.4 và MariaDB 10.11, dùng trình duyệt Chromium '
    + 'điều khiển tự động để kiểm tra những thứ mắt thường khó bắt: lỗi console, tràn ngang ở các '
    + 'kích thước màn hình, tỉ lệ tương phản màu, và hành vi khi tắt JavaScript.'),

  h('3.2.2 Kết quả kiểm thử', H3),

  table(
    ['Hạng mục kiểm thử', 'Kết quả'],
    [
      ['Cú pháp toàn bộ 26 tệp PHP', 'Không lỗi'],
      ['Lỗi console trên 8 trang chính', 'Không có'],
      ['Tràn ngang tại 375 / 768 / 1024 / 1440 pixel', 'Không có ở mọi kích thước'],
      ['Hiển thị khi tắt JavaScript', '12/12 thẻ sách vẫn hiện, tiêu đề H1 còn nguyên'],
      ['Chế độ giảm chuyển động', 'Lenis không khởi tạo, nội dung hiện ngay, không lỗi'],
      ['Tương phản màu (8 cặp × 2 giao diện)', 'Đạt WCAG AA, thấp nhất 4,77:1'],
      ['Thẻ SEO trang chi tiết', 'Đủ title, description, canonical, Open Graph, JSON-LD kiểu Book'],
      ['Ảnh thiếu thuộc tính alt', '0'],
      ['Số thẻ H1 trên một trang', '1 (đúng chuẩn)'],
      ['Thử SQL Injection với chuỗi \' OR 1=1 --', 'Trả về 0 kết quả, không lỗi — bị xử lý như chuỗi thường'],
      ['Gửi biểu mẫu không kèm mã CSRF', 'Bị từ chối đúng như thiết kế'],
      ['Đăng nhập sai mật khẩu', 'Đếm số lần thử đúng, báo lỗi không tiết lộ tài khoản có thật hay không'],
      ['Đăng nhập đúng', 'Vào được bảng điều khiển, phiên làm việc sinh lại ID'],
      ['Gửi đánh giá mới', 'Lưu với is_approved = 0, chưa hiển thị công khai'],
      ['Lưu trữ tiếng Việt', 'Đúng utf8mb4 — "Chấm xanh mờ nhạt" = 17 ký tự / 23 byte'],
    ],
    [4600, 4420]
  ),
  tableCaption('Kết quả kiểm thử chi tiết'),

  h('3.2.3 Một lỗi thật đã phát hiện và sửa', H3),

  p('Quá trình kiểm thử phát hiện một lỗi đáng kể mà việc bấm dạo qua các trang sẽ không bao giờ '
    + 'thấy. Câu truy vấn tìm kiếm ban đầu được viết như sau:'),

  code([
    "$where[] = '(b.title LIKE :q OR a.name LIKE :q OR b.summary LIKE :q)';",
    "$params[':q'] = '%' . $q . '%';",
  ]),

  p('Cách viết này trông hoàn toàn hợp lý và chạy tốt nếu PDO ở chế độ giả lập. Nhưng vì dự án đã '
    + 'tắt chế độ giả lập để dùng prepared statement thật của MySQL, việc dùng lại cùng một tên tham '
    + 'số ở ba vị trí khiến máy chủ ném lỗi SQLSTATE[HY093] Invalid parameter number. Hậu quả: mọi '
    + 'lượt tìm kiếm đều trả về trang lỗi trắng, trong khi trang chủ, trang kho sách và trang chi '
    + 'tiết vẫn chạy bình thường.'),

  p('Cách sửa là đặt tên riêng cho từng vị trí:'),

  code([
    "$where[] = '(b.title LIKE :q_title OR a.name LIKE :q_author",
    "             OR b.summary LIKE :q_summary)';",
    "$params[':q_title'] = $params[':q_author']",
    "                    = $params[':q_summary'] = '%' . $q . '%';",
  ]),

  p('Bài học rút ra: những lỗi chỉ xuất hiện trên một nhánh chức năng cụ thể rất dễ lọt qua khâu '
    + 'kiểm tra bằng mắt. Phải chủ động thử từng chức năng bằng dữ liệu thật thì mới lộ ra.'),

  h('3.2.4 Kết quả đạt được', H3),

  bullet('Hoàn thành một website thư viện sách chạy được đầy đủ với PHP thuần và MySQL, gồm 8 trang '
    + 'phía người đọc và 6 trang quản trị.'),
  bullet('Cơ sở dữ liệu 6 bảng đạt chuẩn 3NF, có khoá ngoại và ràng buộc toàn vẹn, nạp sẵn 30 đầu '
    + 'sách của 20 tác giả qua 6 thể loại.'),
  bullet('Áp dụng đủ bốn lớp bảo mật cơ bản của một ứng dụng web: chống SQL Injection bằng prepared '
    + 'statement, chống XSS bằng escape đầu ra, chống CSRF bằng mã một lần, và băm mật khẩu bcrypt.'),
  bullet('Giao diện đạt chuẩn tiếp cận WCAG AA về tương phản màu ở cả hai chế độ sáng và tối, hoạt '
    + 'động tốt từ màn hình 375 pixel trở lên.'),
  bullet('Tối ưu cho công cụ tìm kiếm ở mức đầy đủ: thẻ mô tả riêng từng trang, canonical, Open '
    + 'Graph, dữ liệu có cấu trúc JSON-LD và sơ đồ trang sinh động từ cơ sở dữ liệu.'),
  bullet('Ba mươi ảnh bìa sách do dự án tự sinh bằng script Python, tránh hoàn toàn vấn đề bản '
    + 'quyền ảnh bìa của nhà xuất bản.'),

  h('3.2.5 Hạn chế', H3),

  bullet('Chưa có tài khoản cho bạn đọc, nên chưa làm được các chức năng cá nhân như đánh dấu sách '
    + 'yêu thích hay lưu lịch sử đọc.'),
  bullet('Tìm kiếm dùng toán tử LIKE nên phải quét bảng; với vài chục nghìn đầu sách sẽ chậm và cần '
    + 'chuyển sang chỉ mục toàn văn hoặc một công cụ tìm kiếm chuyên dụng.'),
  bullet('Ảnh bìa phải đặt sẵn trong thư mục, trang quản trị mới chỉ nhập tên tệp chứ chưa tải ảnh '
    + 'lên trực tiếp được.'),
  bullet('Chưa có phân quyền nhiều mức trong khu vực quản trị — mọi tài khoản admin đều có toàn quyền.'),

  h('3.2.6 Hướng phát triển', H3),

  bullet('Thêm đăng ký và đăng nhập cho bạn đọc, kèm chức năng đánh dấu sách yêu thích và tủ sách cá nhân.'),
  bullet('Bổ sung tải ảnh bìa trực tiếp từ trang quản trị, có kiểm tra định dạng và tự tạo ảnh thu nhỏ.'),
  bullet('Chuyển tìm kiếm sang chỉ mục toàn văn để xử lý được kho sách lớn hơn.'),
  bullet('Thêm phân quyền theo vai trò: người biên tập chỉ sửa nội dung, quản trị viên mới được xoá.'),
  bullet('Xây dựng API JSON đầy đủ để có thể làm ứng dụng di động dùng chung cơ sở dữ liệu.'),

  h('3.2.7 Kết luận chung', H3),

  p('Đồ án đã hoàn thành mục tiêu đặt ra: xây dựng một website thư viện sách hoàn chỉnh bằng PHP '
    + 'thuần và MySQL, có đầy đủ chức năng tra cứu cho bạn đọc và quản trị nội dung cho người vận '
    + 'hành. Quan trọng hơn kết quả là quá trình: vì không dùng framework, mọi lớp của ứng dụng — '
    + 'từ kết nối cơ sở dữ liệu, dựng truy vấn an toàn, quản lý phiên, tới cách trình duyệt vẽ ra '
    + 'trang và chạy hiệu ứng — đều phải tự viết và tự hiểu.'),

  p('Việc kiểm thử có hệ thống cũng chứng minh giá trị của nó: một lỗi nghiêm trọng ở chức năng tìm '
    + 'kiếm đã bị bỏ sót hoàn toàn khi kiểm tra bằng mắt, chỉ lộ ra khi thử từng chức năng với dữ '
    + 'liệu thật. Đó là bài học đáng giá nhất mà đồ án này mang lại.'),
];

/* ==================================================================
   DỰNG TÀI LIỆU
   ================================================================== */
const doc = new Document({
  creator: 'Trần Văn Minh Hiếu',
  title: 'Báo cáo đồ án — Website thư viện sách HieuMini Books',
  description: 'Đồ án môn Lập trình phát triển ứng dụng Web',
  features: { updateFields: true },
  numbering: {
    config: [{
      reference: 'cham-tron',
      levels: [
        { level: 0, format: LevelFormat.BULLET, text: '\u2022', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 560, hanging: 260 } } } },
        { level: 1, format: LevelFormat.BULLET, text: '\u25E6', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 1000, hanging: 260 } } } },
      ],
    }],
  },
  styles: {
    default: {
      document: { run: { font: 'Times New Roman', size: 26, color: INK } },
    },
  },
  sections: [{
    properties: {
      page: { margin: { top: 1440, right: 1440, bottom: 1440, left: 1700 } },
    },
    footers: {
      default: new Footer({
        children: [new Paragraph({
          alignment: AlignmentType.CENTER,
          children: [new TextRun({
            children: [PageNumber.CURRENT],
            font: 'Times New Roman', size: 22, color: MUTED,
          })],
        })],
      }),
    },
    children: [...cover, ...toc, ...chuong1, ...chuong2, ...chuong3],
  }],
});

Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync(OUT, buf);
  console.log('Đã ghi', OUT, '—', (buf.length / 1024).toFixed(0), 'KB');
});
