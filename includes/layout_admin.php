<?php
/* ================================================================
   Layout bersama halaman Admin
   Pemakaian:
     require __DIR__ . '/../includes/layout_admin.php';
     admin_buka('Judul', 'Subjudul', 'fakultas', true);  // true = pakai DataTables
     ... isi halaman ...
     admin_tutup();

   Gaya khusus halaman admin (tabel, tombol aksi, form, DataTables)
   ada di dalam file ini, jadi tidak bergantung pada versi style.css.
   ================================================================ */

if (!function_exists('teks')) {
    function teks($nilai) {
        return htmlspecialchars($nilai ?? '-', ENT_QUOTES, 'UTF-8');
    }
}

function admin_ikon($nama) {
    $ikon = [
        'grid'   => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'gedung' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="12" y2="16"/>',
        'topi'   => '<path d="M12 3 2 8l10 5 10-5-10-5z"/><path d="M6 10.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-5.5"/>',
        'orang'  => '<circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><circle cx="17" cy="9" r="2.3"/><path d="M16 14.2c2.6.3 4.5 2.3 4.5 5.8"/>',
        'rumah'  => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
        'keluar' => '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'tambah' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'pensil' => '<path d="M4 20h4l11-11-4-4L4 16v4z"/><line x1="13.5" y1="6.5" x2="17.5" y2="10.5"/>',
        'sampah' => '<polyline points="4 7 20 7"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/>',
        'kembali'=> '<polyline points="15 18 9 12 15 6"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $ikon[$nama] . '</svg>';
}

function admin_buka($judul, $subjudul, $aktif, $pakai_datatable = false) {
    $GLOBALS['admin_pakai_datatable'] = $pakai_datatable;

    $menu = [
        'dashboard' => ['/administrator/dashboard.php', 'Dashboard', 'grid'],
        'fakultas'  => ['/administrator/fakultas.php',  'Fakultas', 'gedung'],
        'prodi'     => ['/administrator/prodi.php',     'Program studi', 'topi'],
        'mahasiswa' => ['/administrator/mahasiswa.php', 'Mahasiswa', 'orang'],
    ];

    $pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
    $pesan_galat  = $_SESSION['pesan_galat'] ?? '';
    unset($_SESSION['pesan_sukses'], $_SESSION['pesan_galat']);
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= teks($judul) ?> — Sistem Informasi Akademik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,900&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/style.css">
<style>
  /* ---------- Panel tabel ---------- */
  .panel-tabel { padding: 0; overflow: hidden; }
  .panel-kepala {
    display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
    padding: 22px 24px; border-bottom: 1px solid var(--garis);
  }
  .panel-kepala h3 { font-size: 17px; margin: 0; }
  .panel-kepala p { font-size: 13px; color: var(--tinta-lembut); margin: 4px 0 0; }
  .panel-isi { padding: 18px 24px 22px; }
  .tabel-gulir { overflow-x: auto; }

  .tombol-utama {
    display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0;
    background: var(--hijau-tua); color: #fff; text-decoration: none; border: none; cursor: pointer;
    border-radius: 8px; padding: 10px 18px; font-family: inherit; font-size: 14px; font-weight: 500;
  }
  .tombol-utama:hover { background: var(--hijau); }
  .tombol-utama svg { width: 16px; height: 16px; }

  .tombol-garis {
    display: inline-flex; align-items: center; gap: 8px; background: #fff; color: var(--tinta);
    border: 1px solid var(--garis); border-radius: 8px; padding: 9px 16px;
    font-family: inherit; font-size: 14px; font-weight: 500; text-decoration: none; cursor: pointer;
  }
  .tombol-garis:hover { border-color: #c9c3ae; }
  .tombol-garis svg { width: 16px; height: 16px; }

  /* ---------- Tabel data ---------- */
  table.tabel-data { width: 100%; min-width: 640px; border-collapse: collapse; font-size: 14px; }
  table.tabel-data > thead > tr > th {
    background: #F3F1EA; color: var(--tinta-lembut); text-align: left; font-weight: 600;
    font-size: 12px; text-transform: uppercase; letter-spacing: .05em;
    padding: 12px 16px; border-bottom: 1px solid var(--garis); white-space: nowrap;
  }
  table.tabel-data > thead > tr > th:first-child { border-top-left-radius: 8px; }
  table.tabel-data > thead > tr > th:last-child  { border-top-right-radius: 8px; }
  table.tabel-data > tbody > tr > td { padding: 13px 16px; border-bottom: 1px solid #EFECE3; vertical-align: middle; }
  table.tabel-data > tbody > tr:nth-child(even) { background: transparent; }
  table.tabel-data > tbody > tr:hover { background: #FAF8F2; }
  table.tabel-data th.kanan, table.tabel-data td.kanan { text-align: right; }

  .kode-chip {
    display: inline-block; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    font-size: 12.5px; font-weight: 600; color: var(--hijau-tua);
    background: rgba(31,92,70,.08); border-radius: 6px; padding: 3px 8px;
  }
  .nama-utama { font-weight: 500; color: var(--tinta); }
  .lencana {
    display: inline-block; padding: 3px 10px; border-radius: 999px;
    background: rgba(184,134,43,.12); color: #8a6420; font-size: 12.5px; font-weight: 500;
  }
  .lencana.nol { background: rgba(0,0,0,.05); color: var(--tinta-lembut); }

  .aksi-baris { display: inline-flex; gap: 6px; justify-content: flex-end; }
  .aksi-baris form { display: inline; margin: 0; }
  .tombol-aksi {
    display: inline-flex; align-items: center; gap: 6px; background: #fff; color: var(--hijau-tua);
    border: 1px solid var(--garis); border-radius: 7px; padding: 6px 11px;
    font-family: inherit; font-size: 13px; font-weight: 500; line-height: 1.3; text-decoration: none; cursor: pointer;
  }
  .tombol-aksi svg { width: 14px; height: 14px; }
  .tombol-aksi:hover { border-color: var(--hijau-tua); background: rgba(31,92,70,.04); }
  .tombol-aksi.bahaya { color: var(--merah); }
  .tombol-aksi.bahaya:hover { border-color: var(--merah); background: var(--merah-lembut); }
  .tombol-aksi:disabled { opacity: .4; cursor: not-allowed; }
  .tombol-aksi:disabled:hover { border-color: var(--garis); background: #fff; }

  .kosong-tabel { padding: 48px 24px; text-align: center; color: var(--tinta-lembut); }
  .kosong-tabel b { display: block; color: var(--tinta); font-weight: 500; margin-bottom: 4px; }

  /* ---------- Form tambah/ubah ---------- */
  .form-crud { width: 100%; max-width: none; }
  .form-crud .panel-isi { padding: 24px; }
  .bantuan { font-size: 12.5px; color: var(--tinta-lembut); margin-top: 6px; }
  .aksi-form { display: flex; align-items: center; gap: 10px; margin-top: 10px; padding-top: 18px; border-top: 1px solid var(--garis); }
  .form-crud input[type="text"], .form-crud select { border-radius: 8px; }

  /* ---------- Tema DataTables (menggantikan CSS bawaannya) ---------- */
  .dt-container .dt-layout-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin: 0 0 14px; }
  .dt-container .dt-layout-row.dt-layout-table { display: block; margin: 0; }
  .dt-container .dt-layout-row:last-child { margin: 14px 0 0; }
  .dt-container .dt-layout-cell:empty { display: none; }
  .dt-container .dt-length, .dt-container .dt-info { font-size: 13px; color: var(--tinta-lembut); }
  .dt-container .dt-length label, .dt-container .dt-search label { font-size: 13px; color: var(--tinta-lembut); }
  .dt-container select.dt-input {
    font-family: inherit; font-size: 13px; padding: 6px 8px; margin: 0 6px;
    border: 1px solid var(--garis); border-radius: 7px; background: #fff; color: var(--tinta);
  }
  .dt-container .dt-search input.dt-input {
    font-family: inherit; font-size: 13.5px; width: 240px; max-width: 100%;
    padding: 8px 12px 8px 34px; border: 1px solid var(--garis); border-radius: 8px; color: var(--tinta);
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235B6560' stroke-width='2' stroke-linecap='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cline x1='20' y1='20' x2='16' y2='16'/%3E%3C/svg%3E") no-repeat 11px center / 15px;
  }
  .dt-container .dt-search input.dt-input:focus { outline: none; border-color: var(--hijau); box-shadow: 0 0 0 3px rgba(31,92,70,.12); }
  .dt-container .dt-paging nav { display: flex; gap: 4px; }
  .dt-container .dt-paging .dt-paging-button {
    min-width: 32px; height: 32px; padding: 0 9px; border-radius: 7px; border: 1px solid transparent;
    background: transparent; color: var(--tinta-lembut); font-family: inherit; font-size: 13px; cursor: pointer;
  }
  .dt-container .dt-paging .dt-paging-button:hover:not(.disabled):not(.current) { border-color: var(--garis); color: var(--tinta); }
  .dt-container .dt-paging .dt-paging-button.current { background: var(--hijau-tua); color: #fff; }
  .dt-container .dt-paging .dt-paging-button.disabled { opacity: .35; cursor: default; }
  .dt-container td.dt-empty { text-align: center; padding: 32px 16px; color: var(--tinta-lembut); }

  table.tabel-data th .dt-column-header, table.tabel-data th { position: relative; }
  table.tabel-data th .dt-column-header { display: flex; align-items: center; gap: 8px; }
  table.tabel-data th.dt-orderable-asc, table.tabel-data th.dt-orderable-desc { cursor: pointer; }
  table.tabel-data th .dt-column-order { position: relative; display: inline-block; width: 8px; height: 12px; vertical-align: middle; margin-left: 6px; }
  table.tabel-data th .dt-column-header .dt-column-order { margin-left: 0; }
  table.tabel-data th .dt-column-order::before,
  table.tabel-data th .dt-column-order::after {
    content: ""; position: absolute; left: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; opacity: .3;
  }
  table.tabel-data th .dt-column-order::before { top: 0; border-bottom: 5px solid currentColor; }
  table.tabel-data th .dt-column-order::after { bottom: 0; border-top: 5px solid currentColor; }
  table.tabel-data th.dt-ordering-asc .dt-column-order::before,
  table.tabel-data th.dt-ordering-desc .dt-column-order::after { opacity: 1; }
  table.tabel-data th:not(.dt-orderable-asc):not(.dt-orderable-desc) .dt-column-order { display: none; }

  @media (max-width: 640px) {
    .panel-kepala { padding: 18px; }
    .panel-isi { padding: 14px 18px 18px; }
    .dt-container .dt-search input.dt-input { width: 100%; }
  }
</style>
</head>
<body>
<div class="app-shell">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="lambang">A</div>
      <div class="sidebar-brand-teks">UM Bengkulu<br>Portal Admin</div>
    </div>
    <div class="sidebar-menu-label">Menu</div>
    <nav class="sidebar-nav">
      <?php foreach ($menu as $kunci => [$url, $label, $ikon]): ?>
        <a href="<?= $url ?>" class="sidebar-link<?= $kunci === $aktif ? ' aktif' : '' ?>">
          <?= admin_ikon($ikon) ?>
          <?= $label ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-keluar">
      <a href="/index.php" class="sidebar-link"><?= admin_ikon('rumah') ?>Beranda situs</a>
      <a href="/logout.php" class="sidebar-link"><?= admin_ikon('keluar') ?>Keluar</a>
    </div>
  </aside>

  <div class="app-main">
    <header class="app-topbar">
      <div>
        <h1><?= teks($judul) ?></h1>
        <p><?= teks($subjudul) ?></p>
      </div>
      <div class="app-topbar-profil">
        <div class="avatar-kecil">A</div>
        <div>
          <div class="topbar-nama"><?= teks($_SESSION['nama_tampil'] ?? 'Admin') ?></div>
          <div class="topbar-sub">Administrator</div>
        </div>
      </div>
    </header>

    <main class="app-content">
      <?php if ($pesan_sukses): ?><div class="pesan-sukses"><?= teks($pesan_sukses) ?></div><?php endif; ?>
      <?php if ($pesan_galat): ?><div class="pesan-galat"><?= teks($pesan_galat) ?></div><?php endif; ?>
<?php
}

function admin_tutup() {
    ?>
    </main>
  </div>
</div>
<?php if (!empty($GLOBALS['admin_pakai_datatable'])): ?>
<!-- DataTables: kalau CDN gagal dimuat, tabel tetap tampil sebagai tabel biasa -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
  if (window.jQuery && jQuery.fn.dataTable) {
    jQuery('table.tabel-data').each(function () {
      var $t = jQuery(this);
      $t.DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[1, 'asc']],
        columnDefs: [
          { targets: -1, orderable: false, searchable: false },
          { targets: '_all', type: 'html' }
        ],
        language: {
          search: '',
          searchPlaceholder: $t.data('cari') || 'Cari data...',
          lengthMenu: 'Tampilkan _MENU_ data',
          info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
          infoEmpty: 'Tidak ada data',
          infoFiltered: '(disaring dari _MAX_ data)',
          zeroRecords: 'Data tidak ditemukan',
          emptyTable: 'Belum ada data',
          paginate: { first: '«', previous: '‹', next: '›', last: '»' }
        }
      });
    });
  }
</script>
<?php endif; ?>
</body>
</html>
<?php
}