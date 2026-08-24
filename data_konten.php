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
| DATA AKUN SOSMED
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
| PLATFORM META
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
| FILTER
|--------------------------------------------------------------------------
*/

$filterPlatform = strtoupper(
    trim($_GET['platform'] ?? '')
);

$filterAkun = (int) (
    $_GET['akun_id'] ?? 0
);

$filterBulan = (int) (
    $_GET['bulan'] ?? 0
);

$filterTahun = (int) (
    $_GET['tahun'] ?? 0
);


/*
|--------------------------------------------------------------------------
| VALIDASI PLATFORM
|--------------------------------------------------------------------------
*/

$allowedPlatforms = [

    'INSTAGRAM',
    'FACEBOOK',
    'TIKTOK',
    'YOUTUBE'

];

if (
    $filterPlatform !== '' &&
    !in_array(
        $filterPlatform,
        $allowedPlatforms,
        true
    )
) {

    $filterPlatform = '';

}


/*
|--------------------------------------------------------------------------
| DATA TAHUN
|--------------------------------------------------------------------------
*/

$availableYears = [];

$stmt = $pdo->query("
    SELECT DISTINCT tahun
    FROM konten_sosmed
    ORDER BY tahun DESC
");

while ($row = $stmt->fetch()) {

    $availableYears[] =
        (int) $row['tahun'];

}


/*
|--------------------------------------------------------------------------
| QUERY DATA KONTEN
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT

        k.id,
        k.akun_id,
        k.platform,
        a.nama_akun,
        k.tahun,
        k.bulan,
        k.jumlah_konten

    FROM konten_sosmed k

    LEFT JOIN akun_sosmed a
        ON k.akun_id = a.id

    WHERE 1 = 1

";

$params = [];


/*
|--------------------------------------------------------------------------
| FILTER PLATFORM
|--------------------------------------------------------------------------
*/

if ($filterPlatform !== '') {

    $sql .= "
        AND UPPER(k.platform) = ?
    ";

    $params[] =
        $filterPlatform;

}


/*
|--------------------------------------------------------------------------
| FILTER AKUN
|--------------------------------------------------------------------------
*/

if ($filterAkun > 0) {

    $sql .= "
        AND k.akun_id = ?
    ";

    $params[] =
        $filterAkun;

}


/*
|--------------------------------------------------------------------------
| FILTER BULAN
|--------------------------------------------------------------------------
*/

if ($filterBulan > 0) {

    $sql .= "
        AND k.bulan = ?
    ";

    $params[] =
        $filterBulan;

}


/*
|--------------------------------------------------------------------------
| FILTER TAHUN
|--------------------------------------------------------------------------
*/

if ($filterTahun > 0) {

    $sql .= "
        AND k.tahun = ?
    ";

    $params[] =
        $filterTahun;

}


/*
|--------------------------------------------------------------------------
| URUTKAN
|--------------------------------------------------------------------------
*/

$sql .= "

    ORDER BY

        k.tahun DESC,
        k.bulan DESC,
        k.platform ASC,
        a.nama_akun ASC

";


$stmt =
    $pdo->prepare($sql);

$stmt->execute($params);

$rows =
    $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| JUMLAH DATA PER PLATFORM
|--------------------------------------------------------------------------
*/

$platformCounts = [

    'INSTAGRAM' => 0,
    'FACEBOOK'  => 0,
    'TIKTOK'    => 0,
    'YOUTUBE'   => 0,

];


$stmt = $pdo->query("
    SELECT
        UPPER(platform) AS platform,
        COUNT(*) AS total
    FROM konten_sosmed
    GROUP BY UPPER(platform)
");

while ($row = $stmt->fetch()) {

    $platform =
        strtoupper(
            trim($row['platform'])
        );

    if (
        isset(
            $platformCounts[$platform]
        )
    ) {

        $platformCounts[$platform] =
            (int) $row['total'];

    }

}


$totalContentRows =
    array_sum($platformCounts);

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
        Data Konten - Media Sosial Probolinggo
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
        href="css/data_konten.css"
    >

    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >

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
            class="nav-link active"
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


<main class="dashboard-main">


<header class="dashboard-header">

    <div>

        <span>
            DATA KONTEN
        </span>

        <h1>
            Kelola Data Konten
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


<div class="dashboard-view active" id="view-data-konten">
<section
    class="panel"
    id="data-table"
>


<!-- HEADER -->

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

        <p
            class="panel-desc"
            id="tableCount"
        >

            <?= count($rows) ?>
            baris data tersimpan di database.

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


<!-- =====================================================
     FILTER
===================================================== -->

<div class="content-filter-bar">

    <!-- PLATFORM -->
    <div
        class="platform-filter"
        id="platformFilter"
    >

        <button
            type="button"
            class="filter-tab <?= $filterPlatform === '' ? 'active' : '' ?>"
            data-filter="all"
        >

            Semua

            <span class="filter-count">
                <?= $totalContentRows ?>
            </span>

        </button>


        <?php foreach (
            $platformMeta
            as $key => $meta
        ): ?>

            <button
                type="button"
                class="
                    filter-tab
                    filter-tab-<?= strtolower($key) ?>
                    <?= $filterPlatform === $key
                        ? 'active'
                        : ''
                    ?>
                "
                data-filter="<?= strtolower($key) ?>"
            >

                <i class="<?= $meta['icon'] ?>"></i>

                <?= $meta['label'] ?>

                <span class="filter-count">

                    <?= $platformCounts[$key] ?>

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
            class="date-filter-container"
            id="monthFilterContainer"
        >

            <select
                id="filterBulan"
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
                        <?= $filterBulan === $number
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= htmlspecialchars($name) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- TAHUN -->

        <div
            class="date-filter-container"
            id="yearFilterContainer"
        >

            <select
                id="filterTahun"
                class="date-filter-select"
            >

                <option value="">
                    Semua Tahun
                </option>


                <?php foreach (
                    $availableYears
                    as $year
                ): ?>

                    <option
                        value="<?= (int)$year ?>"
                        <?= $filterTahun ===
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


        <!-- PRINT -->

        <div class="print-report-container">

            <button
                type="button"
                class="btn-print"
                id="btnPrint"
            >

                <i class="fas fa-print"></i>

                Print

            </button>

        </div>

    </div>

</div>


<!-- =====================================================
     TABLE
===================================================== -->

<div class="table-wrapper">

<table>

<thead>

<tr>

    <th>No</th>

    <th>Platform</th>

    <th>Akun</th>

    <th>Tahun</th>

    <th>Bulan</th>

    <th>Jumlah Konten</th>

    <th>Aksi</th>

</tr>

</thead>


<tbody>


<?php if (!$rows): ?>

<tr>

    <td
        colspan="7"
        class="empty"
    >

        <i class="fas fa-inbox"></i>

        <span>
            Belum ada data konten.
        </span>

    </td>

</tr>


<?php else: ?>


<?php foreach (
    $rows
    as $index => $row
): ?>


<?php

$rowPlatform =
    strtoupper(
        trim($row['platform'])
    );

$rowPlatformLower =
    strtolower($rowPlatform);

?>


<tr

    data-platform="<?= htmlspecialchars(
        $rowPlatform,
        ENT_QUOTES,
        'UTF-8'
    ) ?>"

    data-akun-id="<?= (int)$row['akun_id'] ?>"

    data-tahun="<?= (int)$row['tahun'] ?>"

    data-bulan="<?= (int)$row['bulan'] ?>"
>


<td>
    <?= $index + 1 ?>
</td>


<td>

    <span
        class="platform-chip chip-<?= $rowPlatformLower ?>"
    >

        <i
            class="<?= htmlspecialchars(
                $platformMeta[$rowPlatform]['icon']
                ?? 'fas fa-share-nodes',
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        ></i>

        <?= htmlspecialchars(
            $platformMeta[$rowPlatform]['label']
            ?? $rowPlatform,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </span>

</td>


<td>

    <div class="account-cell">

        <strong>

            <?= htmlspecialchars(
                $row['nama_akun']
                ?? 'Belum ditentukan',
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </strong>

    </div>

</td>


<td>
    <?= (int)$row['tahun'] ?>
</td>


<td>

    <?= htmlspecialchars(
        $months[
            (int)$row['bulan']
        ] ?? '-',
        ENT_QUOTES,
        'UTF-8'
    ) ?>

</td>


<td>

    <span class="content-count">

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

            data-akun-id="<?= (int)$row['akun_id'] ?>"

            data-platform="<?= htmlspecialchars(
                $row['platform'],
                ENT_QUOTES,
                'UTF-8'
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


<!-- =====================================================
     MODAL TAMBAH DATA KONTEN
===================================================== -->

<div
    class="modal-overlay"
    id="modalTambahData"
>

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h3>
                    Tambah Data Konten
                </h3>

                <p>
                    Masukkan data konten baru
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                id="closeTambahData"
            >

                &times;

            </button>

        </div>


        <div
            id="messageTambahData"
            class="message"
            style="display:none;"
        ></div>


        <form id="formTambahData">

            <input
                type="hidden"
                name="action"
                value="save"
            >

            <input
                type="hidden"
                name="id"
                value=""
            >


            <div class="form-group">

                <label>
                    Platform
                </label>

                <select
                    name="platform"
                    id="modalPlatform"
                    required
                >

                    <option value="">
                        -- Pilih Platform --
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

                <label>
                    Nama Akun
                </label>

                <select
                    name="akun_id"
                    id="modalAkunId"
                    required
                >

                    <option value="">
                        -- Pilih Akun --
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

            
            <div class="form-group">

                <label>
                    Bulan
                </label>

                <select
                    name="bulan"
                    id="modalBulan"
                    required
                >

                    <option value="">
                        -- Pilih Bulan --
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

                <label>
                    Tahun
                </label>

                <select
                    name="tahun"
                    id="modalTahun"
                    required
                >

                    <?php

                    $tahunSekarang =
                        date('Y');

                    for (
                        $i = $tahunSekarang;
                        $i >= $tahunSekarang - 5;
                        $i--
                    ):

                    ?>

                        <option value="<?= $i ?>">
                            <?= $i ?>
                        </option>

                    <?php endfor; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Jumlah Konten
                </label>

                <input
                    type="number"
                    name="jumlah_konten"
                    id="modalJumlahKonten"
                    min="0"
                    value="0"
                    required
                >

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-modal-cancel"
                    id="btnBatalTambahData"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn-modal-save"
                >

                    <i class="fas fa-save"></i>

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

</main>

<script src="js/data_konten.js"></script>

</body>

</html>