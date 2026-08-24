<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'database.php';


/*
|--------------------------------------------------------------------------
| TAHUN BERJALAN
|--------------------------------------------------------------------------
*/

$currentYear = (int) date('Y');


/*
|--------------------------------------------------------------------------
| DATA AKUN
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        id,
        platform,
        nama_akun
    FROM akun_sosmed
    ORDER BY
        platform ASC,
        nama_akun ASC
");

$accounts = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| DATA FOLLOWER
|--------------------------------------------------------------------------
*/

$stmtFollower = $pdo->query("
    SELECT

        fs.id,
        fs.id_akun,
        a.platform,
        a.nama_akun,
        fs.tahun,
        fs.bulan,
        fs.jumlah_follower

    FROM follower_sosmed fs

    INNER JOIN akun_sosmed a
        ON fs.id_akun = a.id

    ORDER BY

        fs.tahun DESC,
        fs.bulan DESC,
        a.platform ASC,
        a.nama_akun ASC
");

$followerRows =
    $stmtFollower->fetchAll();


/*
|--------------------------------------------------------------------------
| DATA TAHUN FOLLOWER
|--------------------------------------------------------------------------
*/

$followerYears = [];

$stmt = $pdo->query("
    SELECT DISTINCT tahun
    FROM follower_sosmed
    ORDER BY tahun DESC
");

while ($row = $stmt->fetch()) {

    $followerYears[] =
        (int)$row['tahun'];

}


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
| PLATFORM
|--------------------------------------------------------------------------
*/

$platformMeta = [

    'INSTAGRAM' => [
        'label' => 'Instagram',
        'icon'  => 'fab fa-instagram'
    ],

    'FACEBOOK' => [
        'label' => 'Facebook',
        'icon'  => 'fab fa-facebook-f'
    ],

    'TIKTOK' => [
        'label' => 'TikTok',
        'icon'  => 'fab fa-tiktok'
    ],

    'YOUTUBE' => [
        'label' => 'YouTube',
        'icon'  => 'fab fa-youtube'
    ],

];


/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$followerPlatform =
    strtoupper(
        trim(
            $_GET['f_platform'] ?? ''
        )
    );

$followerAkun =
    (int)(
        $_GET['f_akun_id'] ?? 0
    );

$followerTahun =
    (int)(
        $_GET['f_tahun'] ?? 0
    );

$followerBulan =
    (int)(
        $_GET['f_bulan'] ?? 0
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
        Data Follower - Media Sosial Probolinggo
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
        href="css/data_follower.css"
    >

    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >
    
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
            class="nav-link"
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
            class="nav-link active"
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


<main class="dashboard-main">


<!-- =========================================================
     HEADER
========================================================= -->

<header class="dashboard-header">

    <div>

        <span>
            DATA FOLLOWER
        </span>

        <h1>
            Kelola Data Follower
        </h1>

    </div>


    <button
        type="button"
        class="admin-profile"
        id="adminProfileBtn"
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


<!-- =========================================================
     DATA FOLLOWER
========================================================= -->

<div class="dashboard-view active">

<section
    class="panel"
    id="follower-table"
>


<!-- HEADER -->

<div class="panel-header">

    <div class="panel-header-icon">

        <i class="fas fa-users"></i>

    </div>


    <div>

        <span>
            DATA TERSIMPAN
        </span>

        <h2>
            Data Follower Per Bulan
        </h2>

        <p
            class="panel-desc"
            id="followerTableCount"
        >

            <?= count($followerRows) ?>

            baris data follower
            tersimpan di database.

        </p>

    </div>


    <button
        type="button"
        class="btn-add-data"
        id="btnTambahData"
    >

        <i class="fas fa-plus"></i>

        Tambah

    </button>

</div>


<!-- =========================================================
     FILTER
========================================================= -->

<div class="content-filter-bar">


    <!-- PLATFORM -->

    <div
        class="platform-filter"
        id="followerPlatformFilter"
    >


        <!-- SEMUA -->

        <button
            type="button"
            class="
                filter-tab
                follower-filter-tab
                <?= $followerPlatform === ''
                    ? 'active'
                    : ''
                ?>
            "
            data-follower-filter="all"
        >

            Semua

            <span class="filter-count">

                <?= count($followerRows) ?>

            </span>

        </button>


        <?php foreach (
            $platformMeta
            as $key => $meta
        ): ?>


            <?php

            $platformFollowerCount = 0;

            foreach (
                $followerRows
                as $fr
            ) {

                if (
                    strtoupper(
                        trim($fr['platform'])
                    ) === $key
                ) {

                    $platformFollowerCount++;

                }

            }

            ?>


            <button
                type="button"
                class="
                    filter-tab
                    follower-filter-tab
                    filter-tab-<?= strtolower($key) ?>
                    <?= $followerPlatform === $key
                        ? 'active'
                        : ''
                    ?>
                "
                data-follower-filter="<?= strtolower($key) ?>"
            >

                <i class="<?= $meta['icon'] ?>"></i>

                <?= htmlspecialchars(
                    $meta['label'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>


                <span class="filter-count">

                    <?= $platformFollowerCount ?>

                </span>

            </button>


        <?php endforeach; ?>

    </div>


    <!-- FILTER DETAIL -->

    <div
        class="filter-secondary-row"
        id="filterSecondaryRow"
    >


        <!-- AKUN -->

        <div
            class="account-filter-container"
            id="accountFilterContainer"
        >

            <select
                id="filterAkun"
                class="account-filter-select"
            >

                <option value="">
                    Semua Akun
                </option>


                <?php foreach (
                    $accounts
                    as $account
                ): ?>

                    <option
                        value="<?= (int)$account['id'] ?>"
                        data-platform="<?= htmlspecialchars(
                            strtoupper(
                                $account['platform']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        <?= $filterAkun ===
                            (int)$account['id']
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= htmlspecialchars(
                            $account['nama_akun'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- BULAN -->

        <div
            class="date-filter-container show"
            id="followerMonthFilterContainer"
        >

            <select
                id="followerFilterBulan"
                class="date-filter-select"
            >

                <option value="">
                    Semua Bulan
                </option>


                <?php foreach (
                    $months
                    as $number => $name
                ): ?>

                    <option
                        value="<?= $number ?>"
                        <?= $followerBulan ===
                            $number
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= htmlspecialchars(
                            $name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- TAHUN -->

        <div
            class="date-filter-container show"
            id="followerYearFilterContainer"
        >

            <select
                id="followerFilterTahun"
                class="date-filter-select"
            >

                <option value="">
                    Semua Tahun
                </option>


                <?php foreach (
                    $followerYears
                    as $year
                ): ?>

                    <option
                        value="<?= (int)$year ?>"
                        <?= $followerTahun ===
                            (int)$year
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= (int)$year ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>

</div>


<!-- =========================================================
     TABLE
========================================================= -->

<div class="table-wrapper">

<table>

<thead>

<tr>

    <th>No</th>

    <th>Platform</th>

    <th>Akun</th>

    <th>Tahun</th>

    <th>Bulan</th>

    <th>Jumlah Follower</th>

    <th>Aksi</th>

</tr>

</thead>


<tbody>


<?php if (!$followerRows): ?>

<tr>

    <td
        colspan="7"
        class="empty"
    >

        <i class="fas fa-inbox"></i>

        <span>
            Belum ada data follower.
        </span>

    </td>

</tr>


<?php else: ?>


<?php foreach (
    $followerRows
    as $index => $follower
): ?>


<?php

$fPlatform =
    strtoupper(
        trim(
            $follower['platform']
        )
    );

$fPlatformLower =
    strtolower(
        $fPlatform
    );

?>


<tr

    data-platform="<?= htmlspecialchars(
        $fPlatformLower,
        ENT_QUOTES,
        'UTF-8'
    ) ?>"

    data-akun-id="<?= (int)$follower['id_akun'] ?>"

    data-tahun="<?= (int)$follower['tahun'] ?>"

    data-bulan="<?= (int)$follower['bulan'] ?>"
>


<td>

    <?= $index + 1 ?>

</td>


<td>

    <span
        class="
            platform-chip
            chip-<?= htmlspecialchars(
                $fPlatformLower,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        "
    >

        <i
            class="<?= htmlspecialchars(
                $platformMeta[$fPlatform]['icon']
                ?? 'fas fa-share-nodes',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        ></i>


        <?= htmlspecialchars(
            $platformMeta[$fPlatform]['label']
            ?? $fPlatform,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </span>

</td>


<td>

    <div class="account-cell">

        <strong>

            <?= htmlspecialchars(
                $follower['nama_akun']
                ?? 'Belum ditentukan',
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </strong>

    </div>

</td>


<td>

    <?= (int)$follower['tahun'] ?>

</td>


<td>

    <?= htmlspecialchars(
        $months[
            (int)$follower['bulan']
        ] ?? '-',
        ENT_QUOTES,
        'UTF-8'
    ) ?>

</td>


<td>

    <span class="content-count">

        <?= number_format(
            (int)$follower['jumlah_follower'],
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
            class="
                btn-edit
                btn-edit-follower
            "
            title="Edit"

            data-id="<?= (int)$follower['id'] ?>"

            data-akun-id="<?= (int)$follower['id_akun'] ?>"

            data-platform="<?= htmlspecialchars(
                $fPlatform,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"

            data-tahun="<?= (int)$follower['tahun'] ?>"

            data-bulan="<?= (int)$follower['bulan'] ?>"

            data-jumlah="<?= (int)$follower['jumlah_follower'] ?>"
        >

            <i class="fas fa-pen"></i>

        </button>


        <button
            type="button"
            class="
                btn-delete
                btn-delete-follower
            "
            title="Hapus"

            data-id="<?= (int)$follower['id'] ?>"
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


<!-- =========================================================
     MODAL TAMBAH / EDIT FOLLOWER
========================================================= -->

<div
    class="profile-modal"
    id="modalTambahData"
>

    <div class="profile-modal-content">


        <button
            type="button"
            class="close-modal"
            id="closeFollowerModal"
        >

            &times;

        </button>


        <div class="profile-modal-header">

            <div class="profile-modal-icon">

                <i class="fas fa-users"></i>

            </div>


            <div>

                <span class="subtitle">
                    DATA FOLLOWER
                </span>

                <h2 id="followerModalTitle">
                    Tambah Data Follower
                </h2>

                <p>
                    Masukkan jumlah follower berdasarkan akun,
                    bulan, dan tahun.
                </p>

            </div>

        </div>


        <form
            id="followerForm"
            class="profile-form"
        >

            <input
                type="hidden"
                name="id"
                id="followerId"
                value=""
            >


            <!-- PLATFORM -->

            <div class="form-group">

                <label>

                    <i class="fas fa-share-nodes"></i>

                    Platform

                </label>


                    <select id="followerPlatform" name="platform">

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


            <!-- AKUN -->

            <div class="form-group">

                <label>

                    <i class="fas fa-user-circle"></i>

                    Nama Akun

                </label>

                <select id="followerAkun" name="id_akun">

                    <option value="">
                        -- Pilih akun --
                    </option>


                    <?php foreach (
                        $accounts
                        as $account
                    ): ?>

                        <option
                            value="<?= (int)$account['id'] ?>"
                            data-platform="<?= htmlspecialchars(
                                strtoupper(
                                    $account['platform']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                            <?= htmlspecialchars(
                                $account['nama_akun'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- BULAN -->

            <div class="form-group">

                <label>

                    <i class="fas fa-calendar-week"></i>

                    Bulan

                </label>


                <select
                    name="bulan"
                    id="followerBulan"
                    required
                >

                    <option value="">
                        -- Pilih bulan --
                    </option>


                    <?php foreach (
                        $months
                        as $number => $name
                    ): ?>

                        <option value="<?= $number ?>">

                            <?= htmlspecialchars($name) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- TAHUN -->

            <div class="form-group">

                <label>

                    <i class="fas fa-calendar-days"></i>

                    Tahun

                </label>


                <input
                    type="number"
                    name="tahun"
                    id="followerTahun"
                    min="2000"
                    max="2100"
                    value="<?= $currentYear ?>"
                    required
                >

            </div>

            <!-- JUMLAH -->

            <div class="form-group">

                <label>

                    <i class="fas fa-users"></i>

                    Jumlah Follower

                </label>


                <input
                    type="number"
                    name="jumlah_follower"
                    id="jumlahFollower"
                    min="0"
                    value="0"
                    required
                >

            </div>


            <div
                id="followerMessage"
                class="message"
            ></div>


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
                    id="btnCancelFollower"
                >

                    <i class="fas fa-xmark"></i>

                    Batal

                </button>

            </div>

        </form>

    </div>

</div>


</main>

<script src="js/data_follower.js"></script>

</body>

</html>