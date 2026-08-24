<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'database.php';

$currentYear = (int) date('Y');


/*
|--------------------------------------------------------------------------
| SUMMARY JUMLAH KONTEN PER PLATFORM
|--------------------------------------------------------------------------
*/

$summary = [
    'INSTAGRAM' => 0,
    'FACEBOOK'  => 0,
    'TIKTOK'    => 0,
    'YOUTUBE'   => 0,
];

$stmt = $pdo->prepare("
    SELECT
        UPPER(platform) AS platform,
        COUNT(*) AS total
    FROM konten_sosmed
    WHERE tahun = ?
    GROUP BY UPPER(platform)
");
$stmt->execute([$currentYear]);

while ($row = $stmt->fetch()) {

    $platform = strtoupper(trim($row['platform']));

    if (isset($summary[$platform])) {
        $summary[$platform] = (int) $row['total'];
    }
}

$grandTotal = array_sum($summary);


/*
|--------------------------------------------------------------------------
| BULAN
|--------------------------------------------------------------------------
*/

$months = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];


/*
|--------------------------------------------------------------------------
| DATA GRAFIK
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

    $chartSeries[$platformKey] =
        array_fill(1, 12, 0);

}


$stmt = $pdo->prepare("
    SELECT
        UPPER(platform) AS platform,
        bulan,
        SUM(jumlah_konten) AS total

    FROM konten_sosmed

    WHERE tahun = ?

    GROUP BY
        UPPER(platform),
        bulan

    ORDER BY
        bulan ASC
");

$stmt->execute([$currentYear]);


while ($row = $stmt->fetch()) {

    $platformKey =
        strtoupper(trim($row['platform']));

    $bulanKey =
        (int) $row['bulan'];

    if (
        isset($chartSeries[$platformKey]) &&
        isset($chartSeries[$platformKey][$bulanKey])
    ) {

        $chartSeries[$platformKey][$bulanKey] =
            (int) $row['total'];

    }
}


$chartDataJson = json_encode(
    [
        'labels' => array_values($months),

        'series' => [

            'INSTAGRAM' =>
                array_values(
                    $chartSeries['INSTAGRAM']
                ),

            'FACEBOOK' =>
                array_values(
                    $chartSeries['FACEBOOK']
                ),

            'TIKTOK' =>
                array_values(
                    $chartSeries['TIKTOK']
                ),

            'YOUTUBE' =>
                array_values(
                    $chartSeries['YOUTUBE']
                ),

        ],
    ],
    JSON_UNESCAPED_UNICODE
);

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar">

    <div class="sidebar-logo">

        <img
            src="assets/logo-pemkab5.png"
            alt="Logo Pemkab Probolinggo"
        >
        <div>

            <strong>
                MEDIA SOSIAL
            </strong>

            <span>
                PEMERINTAH DAERAH <br>
                KABUPATEN PROBOLINGGO
            </span>

        </div>

    </div>


    <nav>

        <span class="nav-label">
            MENU
        </span>


        <a
            href="dashboard.php"
            class="nav-link active"
        >

            <i class="fas fa-gauge-high"></i>

            Dashboard

        </a>


        <a
            href="data_konten.php"
            class="nav-link"
        >

            <i class="fas fa-table"></i>

            Data Konten

        </a>


        <a
            href="data_follower.php"
            class="nav-link"
        >

            <i class="fas fa-users"></i>

            Data Follower

        </a>

    </nav>


    <div class="sidebar-bottom">

        <a href="index.php">

            <i class="fas fa-house"></i>

            Halaman Utama

        </a>


        <a
            href="logout.php"
            class="logout-link"
        >

            <i class="fas fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</aside>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="dashboard-main">


    <!-- HEADER -->

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


            <i
                class="fas fa-pen admin-profile-edit-icon"
            ></i>

        </button>

    </header>


    <!-- =====================================================
         DASHBOARD
    ===================================================== -->

    <div class="dashboard-view active" id="view-dashboard">

        <!-- SUMMARY -->

        <section class="summary-grid">


            <!-- INSTAGRAM -->

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


            <!-- FACEBOOK -->

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


            <!-- TIKTOK -->

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


            <!-- YOUTUBE -->

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


            <!-- TOTAL -->

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


        <!-- =====================================================
             GRAFIK
        ===================================================== -->

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
                        Grafik Konten Per Bulan
                        &mdash;
                        <?= $currentYear ?>
                    </h2>

                    <p class="panel-desc">

                        Perbandingan jumlah konten
                        yang diunggah tiap platform
                        setiap bulan pada tahun berjalan.

                    </p>

                </div>

            </div>


            <div class="chart-canvas-wrap">

                <canvas
                    id="contentTrendChart"
                    height="110"
                ></canvas>

            </div>

        </section>


    </div>

</main>


<script>

window.CHART_DATA =
    <?= $chartDataJson ?>;

</script>

<script src="js/dashboard.js"></script>

<!-- =========================================================
     MODAL PROFILE ADMIN
========================================================= -->
<div class="modal-overlay" id="adminProfile">
    <div class="modal-card profile-modal-card">

        <div class="modal-header">
            <div>
                <span class="modal-label">PROFILE ADMIN</span>
                <h2>Edit Profil</h2>
            </div>

            <button
                type="button"
                class="modal-close"
                id="closeProfileModal"
            >
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="profileForm">

            <div class="form-group">
                <label for="profilNama">
                    Nama
                </label>

                <input
                    type="text"
                    id="profilNama"
                    name="nama"
                    value="<?= htmlspecialchars($_SESSION['admin_nama'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="profilUsername">
                    Username
                </label>

                <input
                    type="text"
                    id="profilUsername"
                    name="username"
                    value="<?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="profilPasswordLama">
                    Password Lama
                </label>

                <input
                    type="password"
                    id="profilPasswordLama"
                    name="password_lama"
                    placeholder="Masukkan password lama"
                >
            </div>

            <div class="form-group">
                <label for="profilPasswordBaru">
                    Password Baru
                </label>

                <input
                    type="password"
                    id="profilPasswordBaru"
                    name="password_baru"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                >
            </div>

            <div class="form-group">
                <label for="profilPasswordKonfirmasi">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    id="profilPasswordKonfirmasi"
                    name="password_konfirmasi"
                    placeholder="Ulangi password baru"
                >
            </div>

            <div
                id="profileMessage"
                class="message"
                style="display:none;"
            ></div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-cancel"
                    id="btnCancelProfile"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn-save"
                >
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>

</body>

</html>