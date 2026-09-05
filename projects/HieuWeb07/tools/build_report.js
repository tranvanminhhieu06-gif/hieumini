/**
 * Dựng BaoCao.docx theo đúng dàn ý trong mucluc.txt.
 *
 * Chạy:  node tools/build_report.js
 * Ảnh lấy từ thư mục ../hieu07-report-assets (sinh bởi generate_covers.py,
 * generate_diagrams.py và bước kiểm thử bằng trình duyệt).
 */
const fs = require('fs');
const path = require('path');
const {
  Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType,
  Table, TableRow, TableCell, WidthType, ShadingType, BorderStyle,
  ImageRun, PageBreak, TableOfContents, Footer, PageNumber, LevelFormat,
  convertInchesToTwip,
} = require('docx');

const ASSETS = process.env.REPORT_ASSETS || path.join(__dirname, '..', '..', 'hieu07-report-assets');
const OUT = process.env.REPORT_OUT || path.join(__dirname, '..', 'BaoCao.docx');

const INK = '16130E', MUTED = '4A443A', ACCENT = '8A6412', HEAD_BG = 'F3EFE6', LINE = 'D8D2C4';
const CONTENT_W = 9020;   // bề rộng vùng nội dung của khổ A4 với lề 1 inch (DXA)

/* ---------- các hàm dựng khối nhỏ ---------- */

const p = (text, opts = {}) => new Paragraph({
  alignment: opts.align || AlignmentType.JUSTIFIED,
  spacing: { after: opts.after ?? 140, line: opts.line ?? 300 },
  indent: opts.indent,
  children: [new TextRun({
    text, font: 'Times New Roman', size: opts.size || 26,
    bold: opts.bold, italics: opts.italics, color: opts.color || INK,
  })],
});

/** Đoạn văn nhiều đoạn chữ có định dạng khác nhau (ví dụ in đậm giữa câu). */
const pRich = (runs, opts = {}) => new Paragraph({
  alignment: opts.align || AlignmentType.JUSTIFIED,
  spacing: { after: opts.after ?? 140, line: 300 },
  children: runs.map(r => new TextRun({
    text: r.t, font: r.mono ? 'Consolas' : 'Times New Roman',
    size: r.mono ? 22 : 26, bold: r.b, italics: r.i, color: r.c || INK,
  })),
});

const h = (text, level, opts = {}) => new Paragraph({
  heading: level,
  spacing: { before: opts.before ?? 280, after: opts.after ?? 160 },
  pageBreakBefore: opts.pageBreak || false,
  children: [new TextRun({
    text, font: 'Times New Roman', bold: true,
    size: opts.size || (level === HeadingLevel.HEADING_1 ? 34 : 30),
    color: level === HeadingLevel.HEADING_1 ? INK : ACCENT,
  })],
});

const bullet = (text, level = 0) => new Paragraph({
  numbering: { reference: 'cham-tron', level },
  spacing: { after: 90, line: 290 },
  children: [new TextRun({ text, font: 'Times New Roman', size: 26, color: INK })],
});

/** Khối mã nguồn: nền xám nhạt, chữ đều nét, viền trái làm dấu. */
const code = (lines) => new Table({
  columnWidths: [CONTENT_W],
  width: { size: CONTENT_W, type: WidthType.DXA },
  borders: {
    top: { style: BorderStyle.SINGLE, size: 2, color: LINE },
    bottom: { style: BorderStyle.SINGLE, size: 2, color: LINE },
    left: { style: BorderStyle.SINGLE, size: 12, color: ACCENT },
    right: { style: BorderStyle.SINGLE, size: 2, color: LINE },
    insideHorizontal: { style: BorderStyle.NONE }, insideVertical: { style: BorderStyle.NONE },
  },
  rows: [new TableRow({
    children: [new TableCell({
      width: { size: CONTENT_W, type: WidthType.DXA },
      shading: { type: ShadingType.CLEAR, fill: 'F7F5F0' },
      margins: { top: 120, bottom: 120, left: 180, right: 120 },
      children: lines.map(l => new Paragraph({
        spacing: { after: 0, line: 260 },
        children: [new TextRun({ text: l, font: 'Consolas', size: 20, color: INK })],
      })),
    })],
  })],
});

/** Bảng dữ liệu: hàng đầu là tiêu đề có nền. */
const table = (headers, rows, widths) => {
  const w = widths || headers.map(() => Math.floor(CONTENT_W / headers.length));
  const cell = (text, { head = false, bold = false } = {}, i = 0) => new TableCell({
    width: { size: w[i], type: WidthType.DXA },
    shading: head ? { type: ShadingType.CLEAR, fill: HEAD_BG } : undefined,
    margins: { top: 80, bottom: 80, left: 110, right: 110 },
    children: String(text).split('\n').map(line => new Paragraph({
      spacing: { after: 0, line: 250 },
      children: [new TextRun({
        text: line, font: 'Times New Roman', size: 22,
        bold: head || bold, color: head ? INK : MUTED,
      })],
    })),
  });

  return new Table({
    columnWidths: w,
    width: { size: CONTENT_W, type: WidthType.DXA },
    borders: {
      top: { style: BorderStyle.SINGLE, size: 4, color: MUTED },
      bottom: { style: BorderStyle.SINGLE, size: 4, color: MUTED },
      left: { style: BorderStyle.NONE }, right: { style: BorderStyle.NONE },
      insideHorizontal: { style: BorderStyle.SINGLE, size: 2, color: LINE },
      insideVertical: { style: BorderStyle.NONE },
    },
    rows: [
      new TableRow({
        tableHeader: true,
        children: headers.map((t, i) => cell(t, { head: true }, i)),
      }),
      ...rows.map(r => new TableRow({ children: r.map((t, i) => cell(t, {}, i)) })),
    ],
  });
};

/** Ảnh kèm chú thích đánh số. */
let figNo = 0;
const figure = (file, caption, widthPx = 600) => {
  const full = path.join(ASSETS, file);
  if (!fs.existsSync(full)) {
    console.warn('  ! thiếu ảnh:', file);
    return [p(`[Thiếu ảnh: ${file}]`, { italics: true, color: 'AA0000' })];
  }
  const dim = require('image-size');
  let ratio = 0.62;
  try {
    const s = dim.imageSize ? dim.imageSize(fs.readFileSync(full)) : dim(full);
    ratio = s.height / s.width;
  } catch (e) { /* dùng tỉ lệ mặc định */ }

  figNo++;
  return [
    new Paragraph({
      alignment: AlignmentType.CENTER,
      spacing: { before: 160, after: 60 },
      children: [new ImageRun({
        type: 'png',
        data: fs.readFileSync(full),
        transformation: { width: widthPx, height: Math.round(widthPx * ratio) },
      })],
    }),
    new Paragraph({
      alignment: AlignmentType.CENTER,
      spacing: { after: 240 },
      children: [new TextRun({
        text: `Hình ${figNo}. ${caption}`,
        font: 'Times New Roman', size: 22, italics: true, color: MUTED,
      })],
    }),
  ];
};

let tblNo = 0;
const tableCaption = (caption) => {
  tblNo++;
  return new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { before: 100, after: 200 },
    children: [new TextRun({
      text: `Bảng ${tblNo}. ${caption}`,
      font: 'Times New Roman', size: 22, italics: true, color: MUTED,
    })],
  });
};

const spacer = (n = 1) => Array.from({ length: n }, () => new Paragraph({ text: '' }));

/* ---------- TRANG BÌA ---------- */

const cover = [
  ...spacer(2),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 60 },
    children: [new TextRun({ text: 'TRƯỜNG ĐẠI HỌC', font: 'Times New Roman', size: 26, bold: true, color: INK })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 900 },
    children: [new TextRun({ text: 'KHOA CÔNG NGHỆ THÔNG TIN', font: 'Times New Roman', size: 26, bold: true, color: INK })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 200 },
    children: [new TextRun({ text: 'BÁO CÁO ĐỒ ÁN MÔN HỌC', font: 'Times New Roman', size: 30, color: MUTED })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 120 },
    children: [new TextRun({ text: 'LẬP TRÌNH PHÁT TRIỂN ỨNG DỤNG WEB', font: 'Times New Roman', size: 26, bold: true, color: ACCENT })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { before: 600, after: 200 },
    border: { top: { style: BorderStyle.SINGLE, size: 8, color: ACCENT } },
    children: [new TextRun({ text: '' })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 140 },
    children: [new TextRun({
      text: 'XÂY DỰNG WEBSITE THƯ VIỆN SÁCH TRỰC TUYẾN',
      font: 'Times New Roman', size: 44, bold: true, color: INK,
    })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 200 },
    children: [new TextRun({ text: 'HIEUMINI BOOKS', font: 'Times New Roman', size: 36, bold: true, color: ACCENT })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 300 },
    border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: ACCENT } },
    children: [new TextRun({ text: '' })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 700 },
    children: [new TextRun({
      text: 'Sử dụng PHP thuần và hệ quản trị cơ sở dữ liệu MySQL',
      font: 'Times New Roman', size: 26, italics: true, color: MUTED,
    })],
  }),
  ...[
    ['Sinh viên thực hiện', 'Trần Văn Minh Hiếu'],
    ['Mã dự án', 'HieuWeb07 — bộ sưu tập HieuMini'],
    ['Công nghệ sử dụng', 'PHP 8, MySQL/MariaDB, HTML5, CSS3, JavaScript'],
    ['Năm học', '2026'],
  ].map(([k, v]) => new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 100 },
    children: [
      new TextRun({ text: k + ': ', font: 'Times New Roman', size: 26, color: MUTED }),
      new TextRun({ text: v, font: 'Times New Roman', size: 26, bold: true, color: INK }),
    ],
  })),
  new Paragraph({ children: [new PageBreak()] }),
];

/* ---------- MỤC LỤC ---------- */

/**
 * Mục lục tĩnh, bám đúng dàn ý trong mucluc.txt.
 *
 * Cố ý KHÔNG dùng trường TableOfContents tự động của Word: trường đó chỉ được
 * điền khi mở bằng Word và bấm cập nhật, còn khi xuất PDF bằng công cụ khác thì
 * trang mục lục trắng trơn. Số trang dưới đây lấy từ chính bản PDF đã kết xuất.
 */
const TOC_ENTRIES = [
  ['Chương 1. Tổng quan lập trình web', 3, true],
  ['1.1 Ngôn ngữ lập trình PHP', 3, false],
  ['1.2 Hệ quản trị cơ sở dữ liệu MySQL', 5, false],
  ['1.3 Cài đặt máy chủ', 6, false],
  ['Chương 2. Phân tích và thiết kế website', 8, true],
  ['2.1 Chức năng (usecase)', 8, false],
  ['2.2 Cơ sở dữ liệu', 12, false],
  ['Chương 3. Chương trình thử nghiệm', 15, true],
  ['3.1 Giao diện', 15, false],
  ['3.2 Kết luận', 22, false],
];

const tocLine = (text, page, isChapter) => new Paragraph({
  spacing: { after: isChapter ? 60 : 40, before: isChapter ? 160 : 0, line: 300 },
  indent: { left: isChapter ? 0 : 420 },
  tabStops: [{ type: 'right', position: 9020, leader: 'dot' }],
  children: [
    new TextRun({
      text, font: 'Times New Roman', size: isChapter ? 27 : 26,
      bold: isChapter, color: INK,
    }),
    new TextRun({ text: '\t' + page, font: 'Times New Roman', size: 26, bold: isChapter, color: INK }),
  ],
});

const toc = [
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 420 },
    children: [new TextRun({ text: 'MỤC LỤC', font: 'Times New Roman', size: 34, bold: true, color: INK })],
  }),
  ...TOC_ENTRIES.map(([t, pg, ch]) => tocLine(t, pg, ch)),
  new Paragraph({ children: [new PageBreak()] }),
];

module.exports = { p, pRich, h, bullet, code, table, figure, tableCaption, spacer, cover, toc,
                   Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType, Footer,
                   PageNumber, LevelFormat, BorderStyle, OUT, CONTENT_W, INK, MUTED, ACCENT, figNoRef: () => figNo };
