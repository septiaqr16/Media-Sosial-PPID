<?php

require_once "database.php";



$currentYear = (int) date('Y');



$totals = [
    'INSTAGRAM' => 0,
    'FACEBOOK'  => 0,
    'TIKTOK'    => 0,
    'YOUTUBE'   => 0,
    'WEBSITE'   => 0
];



$stmt = $pdo->prepare("

    SELECT
        UPPER(platform) AS platform,
        SUM(jumlah_konten) AS total
    FROM konten_sosmed
    WHERE tahun = ?
    GROUP BY UPPER(platform)

");


$stmt->execute([

    $currentYear

]);


while (
    $row = $stmt->fetch()
) {

    $platform =
        strtoupper(
            trim(
                $row['platform']
            )
        );


    if (
        isset(
            $totals[$platform]
        )
    ) {

        $totals[$platform] =
            (int)$row['total'];

    }

}


// Platform meta: dipakai untuk modal grafik (judul, deskripsi, link kunjungi)
$platformMeta = [

    'INSTAGRAM' => [
        'name' => 'Instagram',
        'url'  => 'https://www.instagram.com/pemkab_probolinggo?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==',
    ],

    'FACEBOOK' => [
        'name' => 'Facebook',
        'url'  => 'https://www.facebook.com/Infokabprobolinggo',
    ],

    'TIKTOK' => [
        'name' => 'TikTok',
        'url'  => 'https://www.tiktok.com/@diskominfokabprobolinggo?is_from_webapp=1&sender_device=pc',
    ],

    'YOUTUBE' => [
        'name' => 'YouTube',
        'url'  => 'https://www.youtube.com/@infokabupatenprobolinggo606',
    ],

    'WEBSITE' => [
        'name' => 'Website Resmi',
        'url'  => 'https://probolinggokab.go.id/',
    ],

];

// Daftar akun per platform (hardcode, tanpa database/API).
// Platform yang tidak ada di sini akan tampil sebagai tombol link biasa.
$platformAccounts = [
    'INSTAGRAM' => [
        ['username' => '@diskominfokabprobolinggo', 'url' => 'https://www.instagram.com/diskominfokabprobolinggo?stkn=eDU1MG41cGE1bDRi'],
        ['username' => '@endlessprobolinggo',       'url' => 'https://www.instagram.com/endlessprobolinggo?stkn=bDdhMjBheGt1YXRh'],
        ['username' => '@halo.sae',                 'url' => 'https://www.instagram.com/halo_sae?stkn=MTcxOW5ucWpsamEycA=='],
        ['username' => '@pemkab_probolinggo',       'url' => 'https://www.instagram.com/pemkab_probolinggo?stkn=MW5idDNobDZyMTBtdw=='],
        ['username' => '@radiobromo.fm',            'url' => 'https://www.instagram.com/radiobromo.fm?stkn=MTM0eGJhbTN5ejlrOQ=='],
    ],
    'FACEBOOK' => [
        ['username' => 'Info Kab. Probolinggo',     'url' => 'https://www.facebook.com/share/18wf8x3AuD/?mibextid=wwXIfr'],
    ],
    'TIKTOK' => [
        ['username' => '@halo.sae',                 'url' => 'https://www.tiktok.com/@halo.sae'],
        ['username' => '@diskominfokabprobolinggo', 'url' => 'https://www.tiktok.com/@diskominfokabprobolinggo'],
        ['username' => '@radiobromofm923',          'url' => 'https://www.tiktok.com/@radiobromofm923'],
    ],
    'YOUTUBE' => [
        ['username' => 'Info Kabupaten Probolinggo','url' => 'https://www.youtube.com/@infokabupatenprobolinggo'],
    ],
    'WEBSITE' => [
        ['username' => 'Kabupaten Probolinggo',     'url' => 'https://probolinggokab.go.id/'],
    ],
];

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
        Media Sosial - Pemkab Probolinggo
    </title>


    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="css/style.css?v=<?= time() ?>"
    >


    <!--
        CSS dropdown "Kunjungi" ditaruh inline di sini (bukan hanya di
        css/style.css) supaya TIDAK mungkin ke-cache basi oleh
        browser/hosting/CDN. href css di atas juga sudah dikasih
        "?v=<?= time() ?>" supaya selalu dianggap file baru.
    -->
    <style>
        .dropdown-sosmed {
            position: relative;
            display: inline-block;
        }

        .dropdown-sosmed-toggle {
            border: none;
            font-family: inherit;
            cursor: pointer;
        }

        .dropdown-sosmed-toggle i {
            font-size: 8px;
            transition: transform 0.2s;
        }

        .dropdown-sosmed.open .dropdown-sosmed-toggle i {
            transform: rotate(180deg);
        }

        .dropdown-sosmed-menu {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 0;
            min-width: 170px;
            max-height: 220px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #eee;
            border-radius: 10px;
            box-shadow: 0 -8px 24px rgba(0,0,0,.14);
            padding: 6px;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        }

        .dropdown-sosmed.open .dropdown-sosmed-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-sosmed-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #222222;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: background-color .2s;
        }

        .dropdown-sosmed-item:hover {
            background-color: #f2f8f3;
            color: #218838;
        }

        .dropdown-sosmed-item i {
            flex-shrink: 0;
            font-size: 11px;
            color: #218838;
        }

        .dropdown-sosmed-all {
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 4px;
            padding-bottom: 8px;
        }

        .dropdown-sosmed-empty {
            padding: 8px 10px;
            font-size: 10.5px;
            color: #999;
            font-style: italic;
        }
    </style>


    <!-- CHART JS (untuk grafik statistik konten) -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>


<body>


    <!-- Header -->

    <header>

        <div class="logo-container">

            <img
                src="assets/logo-pemkab2.png"
                alt="Logo Pemkab Probolinggo"
            >

            <div class="logo-text">

                PEMERINTAH DAERAH

                <br>

                KABUPATEN PROBOLINGGO

            </div>

        </div>



    </header>



    <!-- Hero Section -->

    <main class="hero">


        <!-- Building Image -->

        <img
            src="assets/gedung-pemkab3.png"
            alt="Gedung Pemkab"
            class="hero-bg-img"
        >

        <!-- Dark Shape overlay -->

        <div class="hero-dark-shape">
            <div class="dot-pattern"></div>
        </div>


        <!-- Content -->

        <div class="hero-content">
            <div class="text-content">
                <h1>
                    MEDIA
                    <span class="highlight">
                        SOSIAL
                    </span>
                </h1>


                <h2>
                    Pemerintah Daerah
                    <br>
                    Kabupaten Probolinggo
                </h2>

                <p>
                    Dapatkan informasi terbaru, transparan, dan terpercaya<br>
                    melalui kanal media sosial resmi kami.
                </p>

                <button
                    type="button"
                    class="btn-jelajahi"
                    id="btnJelajahi"
                >
                    Jelajahi Informasi
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </main>


    <!-- Bottom Content -->

    <div class="bottom-content">

        <!-- Platforms Section -->

        <section class="platforms-section">

            <div class="section-header">
                <span class="subtitle">
                    PLATFORM KAMI
                </span>


                <h2>
                    Kunjungi Kami di Berbagai Platform
                </h2>

                <p class="section-sub">
                    Klik kartu untuk melihat statistik jumlah konten setiap bulan.
                </p>

            </div>


            <div class="cards-container">

                <?php foreach ($platformMeta as $key => $meta): ?>

                <!-- <?= $meta['name'] ?> -->

                <div
                    class="card"
                    data-platform="<?= htmlspecialchars($key) ?>"
                    data-name="<?= htmlspecialchars($meta['name']) ?>"
                >

                    <div class="card-icon">

    <i class="<?= $key === 'INSTAGRAM' ? 'fab fa-instagram' : ($key === 'FACEBOOK' ? 'fab fa-facebook-f' : ($key === 'TIKTOK' ? 'fab fa-tiktok' : ($key === 'WEBSITE' ? 'fas fa-building-columns' : 'fab fa-youtube'))) ?>"></i>

</div>


                    <div class="card-info">

                        <h3>
                            <?= htmlspecialchars($meta['name']) ?>
                        </h3>


                        <p>
                            <?= number_format($totals[$key], 0, ',', '.') ?> konten tahun <?= $currentYear ?>
                        </p>

                        <div class="card-actions">

                            <button
                                type="button"
                                class="btn-visit btn-grafik"
                            >
                                Lihat Grafik
                                <i class="fas fa-chart-line"></i>
                            </button>

                                <!--
                                    Dropdown "Kunjungi" statis (hardcode), tanpa
                                    database/API. Klik nama akun langsung membuka
                                    link akun tersebut di tab baru.
                                -->
                                <div class="dropdown-sosmed">

                                    <button
                                        type="button"
                                        class="btn-visit btn-kunjungi dropdown-sosmed-toggle"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        Kunjungi
                                        <i class="fas fa-chevron-down"></i>
                                    </button>

                                    <div class="dropdown-sosmed-menu">



                                        <?php if (!empty($platformAccounts[$key])): ?>
                                            <?php foreach ($platformAccounts[$key] as $account): ?>
                                                <a
                                                    href="<?= htmlspecialchars($account['url']) ?>"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="dropdown-sosmed-item"
                                                >
                                                    <i class="<?= $key === 'INSTAGRAM' ? 'fab fa-instagram' : ($key === 'FACEBOOK' ? 'fab fa-facebook-f' : ($key === 'TIKTOK' ? 'fab fa-tiktok' : ($key === 'WEBSITE' ? 'fas fa-building-columns' : 'fab fa-youtube'))) ?>"></i>
                                                    <span><?= htmlspecialchars($account['username']) ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </div>

                                </div>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </section>

        <!-- Footer -->

        <footer>

            <div class="footer-dots"></div>

            <div class="footer-container">

                <div class="footer-col brand-col">

                    <div class="footer-logo">
                        <img
                            src="assets/logo-pemkab5.png"
                            alt="Logo"
                        >

                        <div class="footer-logo-text">
                            PEMERINTAH DAERAH
                            <br>
                            KABUPATEN PROBOLINGGO
                        </div>
                    </div>

                    <p class="address">
                        Jl. Panglima Sudirman No. 59
                        <br>
                        Kraksaan, Probolinggo,
                        Jawa Timur
                    </p>

                </div>



                <div class="footer-col contact-col">


                    <h4>

                        Hubungi Kami

                    </h4>


                    <ul>


                        <li>

                            <i class="fas fa-phone-alt"></i>

                            (0335) – 846665

                        </li>


                        <li>

                            <i class="fas fa-envelope"></i>

                            diskominfo@probolinggokab.go.id

                        </li>

                        <li>
                            <i class="fas fa-globe"></i>
                            www.probolinggokab.go.id
                        </li>

                    </ul>

                </div>


                <div class="footer-col social-col">

                    <h4>
                        Ikuti Kami
                    </h4>

                    <div class="social-icons">
                        <a
                            href="https://www.instagram.com/pemkab_probolinggo?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                            target="_blank"
                            rel="noopener"
                        >
                            <i class="fab fa-instagram"></i>
                        </a>

                        <a
                            href="https://www.facebook.com/Infokabprobolinggo"
                            target="_blank"
                            rel="noopener"
                        >
                            <i class="fab fa-facebook-f"></i>
                        </a>

                        <a
                            href="https://www.tiktok.com/@diskominfokabprobolinggo?is_from_webapp=1&sender_device=pc"
                            target="_blank"
                            rel="noopener"
                        >
                            <i class="fab fa-tiktok"></i>
                        </a>

                        <a
                            href="https://www.youtube.com/@infokabupatenprobolinggo606"
                            target="_blank"
                            rel="noopener"
                        >
                            <i class="fab fa-youtube"></i>
                        </a>

                         <a
                            href="https://probolinggokab.go.id/"
                            target="_blank"
                            rel="noopener"
                        >
                            <i class="fas fa-building-columns"></i>
                        </a>

                    </div>

                </div>

            </div>

        </footer>

    </div>


    <!-- =========================================================
         MODAL GRAFIK
    ========================================================= -->

    <div
        class="chart-modal"
        id="chartModal"
    >
        <div class="chart-modal-content">
            <button
                type="button"
                class="close-modal"
                id="closeModal"
                aria-label="Tutup"
            >
                &times;
            </button>


            <div class="modal-header-banner">
                <div class="modal-header">
                    <div
                        class="modal-icon"
                        id="modalIcon"
                    >
                        <i id="modalIconGlyph" class="fas fa-chart-simple"></i>
                    </div>

                    <div class="modal-header-text">

                        <span class="subtitle">
                            STATISTIK KONTEN
                        </span>

                        <h2 id="chartTitle">
                            Jumlah Konten
                        </h2>

                        <p id="chartDescription">
                            Statistik jumlah konten per bulan.
                        </p>

                    </div>

                </div>

            </div>


            <div class="chart-modal-body">

            <div class="chart-toolbar">

                <div class="chart-toolbar-select">

                    <label for="yearSelect">
                        <i class="fas fa-calendar-days"></i>
                        Tahun
                    </label>

                    <select id="yearSelect">

                        <?php
                        for ($year = $currentYear - 5; $year <= $currentYear + 1; $year++):
                        ?>

                            <option
                                value="<?= $year ?>"
                                <?= ($year === $currentYear ? 'selected' : '') ?>
                            >

                                <?= $year ?>

                            </option>

                        <?php endfor; ?>

                    </select>

                </div>

                <div class="chart-type-toggle" id="chartTypeToggle">

                    <button type="button" class="chart-type-btn active" data-type="bar">
                        <i class="fas fa-chart-column"></i>
                        Batang
                    </button>

                    <button type="button" class="chart-type-btn" data-type="line">
                        <i class="fas fa-chart-line"></i>
                        Tren
                    </button>

                </div>

            </div>


            <div class="chart-canvas-card">

                <div class="chart-wrapper" id="chartWrapper">

                    <div class="chart-loading" id="chartLoading">
                        <div class="chart-spinner"></div>
                        <span>Memuat data...</span>
                    </div>

                    <canvas id="contentChart"></canvas>

                </div>

            </div>


            <div class="chart-stats">

                <div class="stat-card stat-total">
                    <i class="fas fa-database"></i>

                    <div>
                        <strong id="chartTotal">0</strong>
                        <span>Total Konten</span>
                    </div>
                </div>

                <div class="stat-card stat-average">
                    <i class="fas fa-calculator"></i>
                    <div>
                        <strong id="chartAverage">0</strong>
                        <span>Rata-rata / Bulan</span>
                    </div>
                </div>

                <div class="stat-card stat-highest">
                    <i class="fas fa-user"></i>

                    <div class="stat-account">
                        <label for="accountSelect">Username Akun</label>

                        <select id="accountSelect">
                            <option value="@pemkab_probolinggo">
                            </option>
                        </select>
                    </div>
                </div>

            </div>

            </div>
            <!-- /.chart-modal-body -->

        </div>

    </div>


    <script src="js/script.js"></script>


</body>

</html>