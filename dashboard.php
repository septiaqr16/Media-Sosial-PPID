<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'database.php';

$currentYear = (int) date('Y');

$summary = [
    'INSTAGRAM' => 0,
    'FACEBOOK' => 0,
    'TIKTOK' => 0,
    'YOUTUBE' => 0,
];

$stmt = $pdo->prepare("
    SELECT platform, SUM(jumlah_konten) AS total
    FROM konten_sosmed
    WHERE tahun = ?
    GROUP BY platform
");

$stmt->execute([$currentYear]);

while ($row = $stmt->fetch()) {

    if (isset($summary[$row['platform']])) {
        $summary[$row['platform']] =
            (int) $row['total'];
    }
}

$stmt = $pdo->query("
    SELECT
        id,
        platform,
        tahun,
        bulan,
        jumlah_konten
    FROM konten_sosmed
    ORDER BY
        tahun DESC,
        bulan DESC,
        platform ASC
");

$rows = $stmt->fetchAll();

$months = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$platformMeta = [
    'INSTAGRAM' => ['label' => 'Instagram', 'icon' => 'fab fa-instagram'],
    'FACEBOOK'  => ['label' => 'Facebook',  'icon' => 'fab fa-facebook-f'],
    'TIKTOK'    => ['label' => 'TikTok',    'icon' => 'fab fa-tiktok'],
    'YOUTUBE'   => ['label' => 'YouTube',   'icon' => 'fab fa-youtube'],
];

$grandTotal = array_sum($summary);


/*
|--------------------------------------------------------------------------
| DATA GRAFIK: JUMLAH KONTEN PER BULAN (TAHUN BERJALAN)
|--------------------------------------------------------------------------
*/

$chartPlatforms = [
    'INSTAGRAM',
    'FACEBOOK',
    'TIKTOK',
    'YOUTUBE'
];

$chartSeries = [];

foreach ($chartPlatforms as $platformKey) {
    $chartSeries[$platformKey] = array_fill(1, 12, 0);
}

foreach ($rows as $row) {

    if ((int)$row['tahun'] !== $currentYear) {
        continue;
    }

    $platformKey = $row['platform'];
    $bulanKey = (int) $row['bulan'];

    if (
        isset($chartSeries[$platformKey]) &&
        isset($chartSeries[$platformKey][$bulanKey])
    ) {
        $chartSeries[$platformKey][$bulanKey] = (int) $row['jumlah_konten'];
    }

}

$chartLabels = array_values($months);

$chartDataJson = json_encode([
    'labels' => $chartLabels,
    'series' => [
        'INSTAGRAM' => array_values($chartSeries['INSTAGRAM']),
        'FACEBOOK'  => array_values($chartSeries['FACEBOOK']),
        'TIKTOK'    => array_values($chartSeries['TIKTOK']),
        'YOUTUBE'   => array_values($chartSeries['YOUTUBE']),
    ],
], JSON_UNESCAPED_UNICODE);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard Admin - Media Sosial Probolinggo
    </title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >

    <!-- CHART JS (untuk grafik statistik konten) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<aside class="sidebar">

    <div class="sidebar-logo">

        <img
            src="assets/logo-pemkab5.png"
            alt="Logo Pemkab Probolinggo"
        >

        <div>

            <strong>
                PEMERINTAH DAERAH
            </strong>

            <span>
                KABUPATEN PROBOLINGGO
            </span>

        </div>

    </div>


    <nav>

        <span class="nav-label">MENU</span>

        <a
            href="#"
            class="nav-link active"
            data-view="view-dashboard"
        >

            <i class="fas fa-gauge-high"></i>

            Dashboard

        </a>


        <a
            href="#"
            class="nav-link"
            data-view="view-input"
        >

            <i class="fas fa-database"></i>

            Input Data

        </a>


        <a
            href="#"
            class="nav-link"
            data-view="view-table"
        >

            <i class="fas fa-table"></i>

            Data Konten

        </a>

    </nav>


    <div class="sidebar-bottom">

        <a href="index.php">

            <i class="fas fa-house"></i>

            Halaman Utama

        </a>


        <a href="logout.php" class="logout-link">

            <i class="fas fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</aside>


<main
    class="dashboard-main"
>

    <header class="dashboard-header">

        <div>

            <span>
                DASHBOARD ADMIN
            </span>

            <h1>
                Kelola Data Media Sosial
            </h1>

        </div>


        <button
            type="button"
            class="admin-profile"
            id="adminProfileBtn"
            title="Klik untuk edit profil"
        >

            <div class="admin-icon">

                <i class="fas fa-user-shield"></i>

            </div>


            <div>

                <strong>

                    <?= htmlspecialchars(
                        $_SESSION['admin_nama']
                        ?? 'Administrator'
                    ) ?>

                </strong>

                <small>

                    @<?= htmlspecialchars(
                        $_SESSION['admin_username']
                        ?? 'admin'
                    ) ?>

                </small>

            </div>

            <i class="fas fa-pen admin-profile-edit-icon"></i>

        </button>

    </header>


    <!-- ============ VIEW: DASHBOARD (RINGKASAN + GRAFIK) ============ -->

    <div class="dashboard-view active" id="view-dashboard">

    <!-- SUMMARY -->

    <section class="summary-grid">


        <div class="summary-card instagram">

            <div class="summary-card-icon">
                <i class="fab fa-instagram"></i>
            </div>

            <div>

                <span>
                    Instagram
                </span>

                <strong>

                    <?= number_format(
                        $summary['INSTAGRAM'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </strong>

                <small>
                    Total konten <?= $currentYear ?>
                </small>

            </div>

        </div>


        <div class="summary-card facebook">

            <div class="summary-card-icon">
                <i class="fab fa-facebook-f"></i>
            </div>

            <div>

                <span>
                    Facebook
                </span>

                <strong>

                    <?= number_format(
                        $summary['FACEBOOK'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </strong>

                <small>
                    Total konten <?= $currentYear ?>
                </small>

            </div>

        </div>


        <div class="summary-card tiktok">

            <div class="summary-card-icon">
                <i class="fab fa-tiktok"></i>
            </div>

            <div>

                <span>
                    TikTok
                </span>

                <strong>

                    <?= number_format(
                        $summary['TIKTOK'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </strong>

                <small>
                    Total konten <?= $currentYear ?>
                </small>

            </div>

        </div>


        <div class="summary-card youtube">

            <div class="summary-card-icon">
                <i class="fab fa-youtube"></i>
            </div>

            <div>

                <span>
                    YouTube
                </span>

                <strong>

                    <?= number_format(
                        $summary['YOUTUBE'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </strong>

                <small>
                    Total konten <?= $currentYear ?>
                </small>

            </div>

        </div>


        <div class="summary-card total">

            <div class="summary-card-icon">
                <i class="fas fa-layer-group"></i>
            </div>

            <div>

                <span>
                    Semua Platform
                </span>

                <strong>

                    <?= number_format(
                        $grandTotal,
                        0,
                        ',',
                        '.'
                    ) ?>

                </strong>

                <small>
                    Total konten <?= $currentYear ?>
                </small>

            </div>

        </div>

    </section>


    <!-- GRAFIK STATISTIK -->

    <section class="panel chart-panel">

        <div class="panel-header">

            <div class="panel-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>

            <div>

                <span>
                    STATISTIK
                </span>

                <h2>
                    Grafik Konten Per Bulan &mdash; <?= $currentYear ?>
                </h2>

                <p class="panel-desc">
                    Perbandingan jumlah konten yang diunggah tiap platform
                    setiap bulan pada tahun berjalan.
                </p>

            </div>

        </div>

        <div class="chart-canvas-wrap">
            <canvas id="contentTrendChart" height="110"></canvas>
        </div>

    </section>

    </div>
    <!-- /#view-dashboard -->


    <!-- ============ VIEW: INPUT DATA ============ -->

    <div class="dashboard-view" id="view-input">

    <!-- INPUT DATA -->

    <section
        class="panel"
        id="input-data"
    >

        <div class="panel-header">

            <div class="panel-header-icon">
                <i class="fas fa-square-plus"></i>
            </div>

            <div>

                <span>
                    INPUT DATA
                </span>

                <h2>
                    Tambah / Edit Jumlah Konten
                </h2>

                <p class="panel-desc">
                    Masukkan jumlah konten yang diunggah pada platform,
                    tahun, dan bulan tertentu. Data otomatis dijumlahkan
                    pada grafik statistik di halaman utama.
                </p>

            </div>

        </div>


        <form
            id="dataForm"
            class="data-form"
        >

            <input
                type="hidden"
                name="id"
                id="dataId"
                value=""
            >


            <div class="form-group">

                <label for="platform">
                    <i class="fas fa-share-nodes"></i>
                    Platform
                </label>

                <select
                    name="platform"
                    id="platform"
                    required
                >

                    <option value="">
                        Pilih platform
                    </option>

                    <option value="INSTAGRAM">
                        Instagram
                    </option>

                    <option value="FACEBOOK">
                        Facebook
                    </option>

                    <option value="TIKTOK">
                        TikTok
                    </option>

                    <option value="YOUTUBE">
                        YouTube
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="tahun">
                    <i class="fas fa-calendar-days"></i>
                    Tahun
                </label>

                <input
                    type="number"
                    name="tahun"
                    id="tahun"
                    min="2000"
                    max="2100"
                    value="<?= $currentYear ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="bulan">
                    <i class="fas fa-calendar-week"></i>
                    Bulan
                </label>

                <select
                    name="bulan"
                    id="bulan"
                    required
                >

                    <option value="">
                        Pilih bulan
                    </option>

                    <?php foreach (
                        $months
                        as $number => $name
                    ): ?>

                        <option value="<?= $number ?>">

                            <?= $name ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="jumlah_konten">
                    <i class="fas fa-hashtag"></i>
                    Jumlah Konten
                </label>

                <input
                    type="number"
                    name="jumlah_konten"
                    id="jumlah_konten"
                    min="0"
                    value="0"
                    required
                >

            </div>


            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-save"
                >

                    <i class="fas fa-save"></i>

                    Simpan Data

                </button>


                <button
                    type="button"
                    class="btn-cancel"
                    id="btnCancel"
                    style="display:none;"
                >

                    <i class="fas fa-xmark"></i>

                    Batal

                </button>

            </div>

        </form>


        <div
            id="message"
            class="message"
        ></div>

    </section>

    </div>
    <!-- /#view-input -->


    <!-- ============ VIEW: DATA KONTEN (TABEL) ============ -->

    <div class="dashboard-view" id="view-table">

    <!-- DATA TABLE -->

    <section
        class="panel"
        id="data-table"
    >

        <div class="panel-header">

            <div class="panel-header-icon">
                <i class="fas fa-table-list"></i>
            </div>

            <div>

                <span>
                    DATA TERSIMPAN
                </span>

                <h2>
                    Data Konten Per Bulan
                </h2>

                <p class="panel-desc" id="tableCount">
                    <?= count($rows) ?> baris data tersimpan di database.
                </p>

            </div>

        </div>


        <div class="platform-filter" id="platformFilter">

            <button
                type="button"
                class="filter-tab active"
                data-filter="all"
            >
                Semua
                <span class="filter-count"><?= count($rows) ?></span>
            </button>

            <?php foreach ($platformMeta as $key => $meta):

                $countPlatform = count(array_filter(
                    $rows,
                    function ($r) use ($key) {
                        return $r['platform'] === $key;
                    }
                ));

            ?>

                <button
                    type="button"
                    class="filter-tab filter-tab-<?= strtolower($key) ?>"
                    data-filter="<?= strtolower($key) ?>"
                >
                    <i class="<?= $meta['icon'] ?>"></i>
                    <?= $meta['label'] ?>
                    <span class="filter-count"><?= $countPlatform ?></span>
                </button>

            <?php endforeach; ?>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Platform
                        </th>

                        <th>
                            Tahun
                        </th>

                        <th>
                            Bulan
                        </th>

                        <th>
                            Jumlah Konten
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (!$rows): ?>

                        <tr>

                            <td
                                colspan="6"
                                class="empty"
                            >

                                <i class="fas fa-inbox"></i>
                                <span>Belum ada data konten.</span>

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach (
                            $rows
                            as $index => $row
                        ): ?>

                            <tr data-platform="<?= strtolower(htmlspecialchars($row['platform'])) ?>">

                                <td>
                                    <?= $index + 1 ?>
                                </td>


                                <td>

                                    <span
                                        class="platform-chip chip-<?= strtolower(htmlspecialchars($row['platform'])) ?>"
                                    >

                                        <i class="<?= $platformMeta[$row['platform']]['icon'] ?? 'fas fa-share-nodes' ?>"></i>

                                        <?= htmlspecialchars(
                                            $platformMeta[$row['platform']]['label'] ?? $row['platform']
                                        ) ?>

                                    </span>

                                </td>


                                <td>
                                    <?= (int)$row['tahun'] ?>
                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $months[
                                            (int)$row['bulan']
                                        ] ?? '-'
                                    ) ?>

                                </td>


                                <td>

                                    <span class="content-count">
                                        <i class="fas fa-layer-group"></i>
                                        <?= number_format(
                                            (int)$row['jumlah_konten'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </span>

                                </td>


                                <td>

                                    <div class="action-buttons">

                                        <button
                                            type="button"
                                            class="btn-edit"
                                            title="Edit"
                                            data-id="<?= (int)$row['id'] ?>"
                                            data-platform="<?= htmlspecialchars(
                                                $row['platform'],
                                                ENT_QUOTES
                                            ) ?>"
                                            data-tahun="<?= (int)$row['tahun'] ?>"
                                            data-bulan="<?= (int)$row['bulan'] ?>"
                                            data-jumlah="<?= (int)$row['jumlah_konten'] ?>"
                                        >

                                            <i class="fas fa-pen"></i>

                                        </button>


                                        <button
                                            type="button"
                                            class="btn-delete"
                                            title="Hapus"
                                            data-id="<?= (int)$row['id'] ?>"
                                        >

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

    </div>
    <!-- /#view-table -->

</main>


<!-- ============ MODAL: EDIT PROFIL ADMIN ============ -->

<div class="profile-modal" id="profileModal">

    <div class="profile-modal-content">

        <button
            type="button"
            class="close-modal"
            id="closeProfileModal"
            aria-label="Tutup"
        >
            &times;
        </button>

        <div class="profile-modal-header">

            <div class="profile-modal-icon">
                <i class="fas fa-user-shield"></i>
            </div>

            <div>
                <span class="subtitle">PENGATURAN AKUN</span>
                <h2>Edit Profil Admin</h2>
                <p>Perbarui nama, username, atau kata sandi akun Anda.</p>
            </div>

        </div>

        <form id="profileForm" class="profile-form">

            <div class="form-group">
                <label for="profilNama">
                    <i class="fas fa-id-card"></i>
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    name="nama"
                    id="profilNama"
                    value="<?= htmlspecialchars($_SESSION['admin_nama'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="profilUsername">
                    <i class="fas fa-user"></i>
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    id="profilUsername"
                    value="<?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>"
                    required
                >
            </div>

            <div class="profile-form-divider">
                <span>Ganti Kata Sandi (opsional)</span>
            </div>

            <div class="form-group">
                <label for="profilPasswordLama">
                    <i class="fas fa-lock"></i>
                    Password Saat Ini
                </label>
                <div class="input-box">
                    <input
                        type="password"
                        name="password_lama"
                        id="profilPasswordLama"
                        placeholder="Wajib diisi untuk menyimpan perubahan"
                        autocomplete="current-password"
                    >
                </div>
                <small class="field-hint">Diperlukan untuk memverifikasi setiap perubahan profil.</small>
            </div>

            <div class="form-group">
                <label for="profilPasswordBaru">
                    <i class="fas fa-key"></i>
                    Password Baru
                </label>
                <div class="input-box">
                    <input
                        type="password"
                        name="password_baru"
                        id="profilPasswordBaru"
                        placeholder="Kosongkan jika tidak diganti"
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="profilPasswordKonfirmasi">
                    <i class="fas fa-key"></i>
                    Konfirmasi Password Baru
                </label>
                <div class="input-box">
                    <input
                        type="password"
                        name="password_konfirmasi"
                        id="profilPasswordKonfirmasi"
                        placeholder="Ulangi password baru"
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <div id="profileMessage" class="message"></div>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
                <button type="button" class="btn-cancel" id="btnCancelProfile">
                    <i class="fas fa-xmark"></i>
                    Batal
                </button>
            </div>

        </form>

    </div>

</div>


<script>
    window.CHART_DATA = <?= $chartDataJson ?>;
</script>
<script src="js/dashboard.js"></script>

</body>
</html>
