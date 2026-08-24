<?php

session_start();

require_once 'database.php';


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

$filterTahun = (int) (
    $_GET['tahun'] ?? 0
);

$filterBulan = (int) (
    $_GET['bulan'] ?? 0
);


/*
|--------------------------------------------------------------------------
| DAFTAR PLATFORM
|--------------------------------------------------------------------------
*/

$platformMeta = [
    'INSTAGRAM' => 'Instagram',
    'FACEBOOK'  => 'Facebook',
    'TIKTOK'    => 'TikTok',
    'YOUTUBE'   => 'YouTube'
];


/*
|--------------------------------------------------------------------------
| DAFTAR BULAN
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
    !in_array($filterPlatform, $allowedPlatforms, true)
) {
    $filterPlatform = '';
}


/*
|--------------------------------------------------------------------------
| KONDISI FILTER
|--------------------------------------------------------------------------
*/

$isAllPlatform = ($filterPlatform === '');
$isAllMonth    = ($filterBulan <= 0);
$isSpecificYear = ($filterTahun > 0);


/*
|--------------------------------------------------------------------------
| TENTUKAN APAKAH FOLLOWER DITAMPILKAN
|--------------------------------------------------------------------------
|
| FOLLOWER DITAMPILKAN JIKA:
|
| 1. Bulan tertentu dipilih
|    ATAU
|
| 2. Salah satu platform dipilih
|    + tahun tertentu
|
| Jadi:
|
| Semua Platform + Semua Bulan + Tahun
| -> follower TIDAK ditampilkan
|
| Instagram + Semua Bulan + Tahun
| -> follower DITAMPILKAN
|
| Instagram + Juli + Tahun
| -> follower DITAMPILKAN
|
|--------------------------------------------------------------------------
*/

$showFollower =
    ($filterBulan > 0) ||
    (!$isAllPlatform && $isSpecificYear);


/*
|--------------------------------------------------------------------------
| KONDISI PENGELOMPOKAN PER BULAN
|--------------------------------------------------------------------------
|
| Pengelompokan bulan hanya digunakan ketika:
|
| - salah satu platform
| - semua bulan
| - tahun tertentu
|
|--------------------------------------------------------------------------
*/

$groupByMonth =
    (!$isAllPlatform &&
     $isAllMonth);


/*
|--------------------------------------------------------------------------
| QUERY DATA
|--------------------------------------------------------------------------
*/

if ($groupByMonth) {

    $sql = "
        SELECT
            k.tahun,
            k.bulan,
            k.platform,
            k.akun_id,
            a.nama_akun,

            SUM(k.jumlah_konten) AS jumlah_konten,

            MAX(fs.jumlah_follower) AS jumlah_follower

        FROM konten_sosmed k

        LEFT JOIN akun_sosmed a
            ON k.akun_id = a.id

        LEFT JOIN follower_sosmed fs
            ON fs.id_akun = k.akun_id
            AND fs.tahun = k.tahun
            AND fs.bulan = k.bulan

        WHERE 1 = 1
    ";

} else {

    /*
    |--------------------------------------------------------------------------
    | QUERY NORMAL
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            k.platform,
            k.akun_id,
            a.nama_akun,

            SUM(k.jumlah_konten) AS jumlah_konten
    ";

    if ($showFollower) {

        $sql .= ",
            MAX(fs.jumlah_follower) AS jumlah_follower
        ";
    }

    $sql .= "
        FROM konten_sosmed k

        LEFT JOIN akun_sosmed a
            ON k.akun_id = a.id
    ";

    if ($showFollower) {

        $sql .= "
            LEFT JOIN follower_sosmed fs
                ON fs.id_akun = k.akun_id
                AND fs.tahun = k.tahun
                AND fs.bulan = k.bulan
        ";
    }

    $sql .= "
        WHERE 1 = 1
    ";
}


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

    $params[] = $filterPlatform;
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

    $params[] = $filterAkun;
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

    $params[] = $filterTahun;
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

    $params[] = $filterBulan;
}


/*
|--------------------------------------------------------------------------
| GROUPING
|--------------------------------------------------------------------------
*/

if ($groupByMonth) {

    $sql .= "
        GROUP BY
            k.tahun,
            k.bulan,
            k.platform,
            k.akun_id,
            a.nama_akun
    ";

} else {

    $sql .= "
        GROUP BY
            k.platform,
            k.akun_id,
            a.nama_akun
    ";
}


/*
|--------------------------------------------------------------------------
| URUTAN DATA
|--------------------------------------------------------------------------
*/

if ($groupByMonth) {

    $sql .= "
        ORDER BY
            k.tahun ASC,
            k.bulan ASC,

            FIELD(
                UPPER(k.platform),
                'INSTAGRAM',
                'FACEBOOK',
                'TIKTOK',
                'YOUTUBE'
            ),

            a.nama_akun ASC
    ";

} else {

    $sql .= "
        ORDER BY
            FIELD(
                UPPER(k.platform),
                'INSTAGRAM',
                'FACEBOOK',
                'TIKTOK',
                'YOUTUBE'
            ),

            a.nama_akun ASC
    ";
}


/*
|--------------------------------------------------------------------------
| EKSEKUSI QUERY
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| KELOMPOKKAN DATA
|--------------------------------------------------------------------------
*/

$groupedData = [];


/*
|--------------------------------------------------------------------------
| MODE PER BULAN
|--------------------------------------------------------------------------
*/

if ($groupByMonth) {

    foreach ($rows as $row) {

        $tahun = (int) $row['tahun'];
        $bulan = (int) $row['bulan'];

        $platform = strtoupper(
            trim($row['platform'])
        );

        if (!isset($groupedData[$tahun])) {
            $groupedData[$tahun] = [];
        }

        if (!isset($groupedData[$tahun][$bulan])) {
            $groupedData[$tahun][$bulan] = [];
        }

        if (!isset($groupedData[$tahun][$bulan][$platform])) {
            $groupedData[$tahun][$bulan][$platform] = [];
        }

        $groupedData[$tahun][$bulan][$platform][] = $row;
    }


/*
|--------------------------------------------------------------------------
| MODE NORMAL
|--------------------------------------------------------------------------
*/

} else {

    foreach ($rows as $row) {

        $platform = strtoupper(
            trim($row['platform'])
        );

        if (!isset($groupedData[$platform])) {
            $groupedData[$platform] = [];
        }

        $groupedData[$platform][] = $row;
    }
}


/*
|--------------------------------------------------------------------------
| JUDUL PERIODE
|--------------------------------------------------------------------------
*/

if (
    $filterBulan > 0 &&
    isset($months[$filterBulan])
) {

    $periodeBulan = strtoupper(
        $months[$filterBulan]
    );

} else {

    $periodeBulan = 'SEMUA BULAN';
}


if ($filterTahun > 0) {

    $periodeTahun = $filterTahun;

} else {

    $periodeTahun = 'SEMUA TAHUN';
}


/*
|--------------------------------------------------------------------------
| INFORMASI PLATFORM
|--------------------------------------------------------------------------
*/

if ($isAllPlatform) {

    $namaPlatform = 'Semua Platform';

} else {

    $namaPlatform =
        $platformMeta[$filterPlatform]
        ?? $filterPlatform;
}

?>


<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<title>
    Rekap Pemberitaan Media Sosial
</title>


<style>

/*
|--------------------------------------------------------------------------
| GLOBAL
|--------------------------------------------------------------------------
*/

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    padding: 20px 30px;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    color: #111;

    background: #ffffff;

    font-size: 11px;
}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.report-header {

    text-align: center;

    margin-bottom: 14px;
}


.report-header h1 {

    margin: 0;

    font-size: 15px;

    font-weight: 700;

    text-transform: uppercase;
}


.report-header h2 {

    margin: 2px 0 0;

    font-size: 13px;

    font-weight: 700;

    text-transform: uppercase;
}


.report-header h3 {

    margin: 3px 0 0;

    font-size: 11px;

    font-weight: 700;

    text-transform: uppercase;
}


/*
|--------------------------------------------------------------------------
| FILTER INFO
|--------------------------------------------------------------------------
*/

.filter-info {

    margin-bottom: 10px;

    padding: 7px 9px;

    background: #eef8f0;

    border-left:
        4px solid
        #218838;

    color: #204325;

    font-size: 10px;
}


/*
|--------------------------------------------------------------------------
| JUDUL BULAN
|--------------------------------------------------------------------------
*/

.month-title {

    margin-top: 18px;

    margin-bottom: 5px;

    padding: 7px 10px;

    background: #dcefe0;

    border-left:
        5px solid
        #218838;

    color: #204325;

    font-size: 11px;

    font-weight: 700;

    text-transform: uppercase;

    page-break-after: avoid;
}


/*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

.report-table {

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

    margin-bottom: 8px;
}


.report-table th,
.report-table td {

    border:
        1px solid
        #222;

    padding: 6px 7px;

    vertical-align: middle;
}


/*
|--------------------------------------------------------------------------
| HEADER TABLE
|--------------------------------------------------------------------------
*/

.report-table thead th {

    background: #dcefe0;

    color: #204325;

    text-align: center;

    font-size: 9px;

    font-weight: 700;

    text-transform: uppercase;
}


/*
|--------------------------------------------------------------------------
| KOLOM
|--------------------------------------------------------------------------
*/

.col-no {

    width: 5%;

    text-align: center;
}


.col-platform {

    width: 17%;

    text-align: center;
}


.col-account {

    width: 48%;

    text-align: left;
}


.col-content {

    width: 15%;

    text-align: center;
}


.col-follower {

    width: 15%;

    text-align: center;
}


/*
|--------------------------------------------------------------------------
| PLATFORM CELL
|--------------------------------------------------------------------------
*/

.platform-cell {

    text-align: center;

    font-weight: 700;

    text-transform: uppercase;

    background: #f1f9f2;

    color: #204325;
}


/*
|--------------------------------------------------------------------------
| NO CELL
|--------------------------------------------------------------------------
*/

.number-cell {

    text-align: center;

    font-weight: 600;

    background: #f7fbf7;
}


/*
|--------------------------------------------------------------------------
| ACCOUNT
|--------------------------------------------------------------------------
*/

.account-cell {

    font-weight: 500;

    padding-left: 10px !important;
}


/*
|--------------------------------------------------------------------------
| JUMLAH KONTEN
|--------------------------------------------------------------------------
*/

.content-cell {

    text-align: center;

    font-weight: 700;

    color: #286035;
}


/*
|--------------------------------------------------------------------------
| FOLLOWER
|--------------------------------------------------------------------------
*/

.follower-cell {

    text-align: center;

    font-weight: 600;

    color: #204325;
}


/*
|--------------------------------------------------------------------------
| ZEBRA
|--------------------------------------------------------------------------
*/

.report-table tbody tr:nth-child(even) td {

    background: #eaf6ec;
}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

@media print {

    @page {

        size: A4 landscape;

        margin: 10mm;
    }


    body {

        padding: 0;

        margin: 0;

        font-size: 10px;
    }


    .report-header {

        margin-bottom: 10px;
    }


    .report-header h1 {

        font-size: 14px;
    }


    .report-header h2 {

        font-size: 12px;
    }


    .report-header h3 {

        font-size: 10px;
    }


    .filter-info {

        margin-bottom: 7px;

        padding: 5px 7px;
    }


    .month-title {

        margin-top: 12px;

        margin-bottom: 4px;

        padding: 5px 8px;

        font-size: 10px;

        page-break-after: avoid;
    }


    .report-table {

        page-break-inside: auto;

        margin-bottom: 7px;
    }


    .report-table tr {

        page-break-inside: avoid;

        page-break-after: auto;
    }


    .report-table thead {

        display: table-header-group;
    }

}

</style>

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

<div class="report-header">

    <h1>
        REKAP PEMBERITAAN PEMERINTAH KABUPATEN PROBOLINGGO
    </h1>

    <h2>
        DI MEDIA SOSIAL
    </h2>

    <h3>

        <?= htmlspecialchars($periodeBulan) ?>

        TAHUN

        <?= htmlspecialchars($periodeTahun) ?>

    </h3>

</div>


<!-- =========================================================
     INFORMASI FILTER
========================================================= -->

<div class="filter-info">

    <strong>Platform :</strong>

    <?= htmlspecialchars($namaPlatform) ?>

    &nbsp;&nbsp; | &nbsp;&nbsp;

    <strong>Periode :</strong>

    <?= htmlspecialchars($periodeBulan) ?>

    <?= htmlspecialchars($periodeTahun) ?>

</div>



<?php if (empty($groupedData)): ?>


    <table class="report-table">

        <tbody>

            <tr>

                <td
                    colspan="<?= $showFollower ? 5 : 4 ?>"
                    style="
                        text-align:center;
                        padding:20px;
                        color:#777;
                    "
                >

                    Tidak ada data
                    untuk filter yang dipilih.

                </td>

            </tr>

        </tbody>

    </table>



<?php else: ?>

<?php

if ($groupByMonth):

    /*
    |--------------------------------------------------------------------------
    | MODE PER TAHUN → BULAN → PLATFORM → AKUN
    |--------------------------------------------------------------------------
    */

    foreach ($groupedData as $tahun => $monthGroups):

        foreach ($monthGroups as $bulan => $platformGroups):

?>

            <!-- =================================================
                 JUDUL TAHUN + BULAN
            ================================================== -->

            <div class="month-title">

                <?= htmlspecialchars(
                    strtoupper(
                        $months[$bulan] ?? 'Bulan ' . $bulan
                    )
                ) ?>

                <?= htmlspecialchars($tahun) ?>

            </div>


            <?php

            $noPlatform = 1;

            foreach ($platformGroups as $platform => $accounts):

                $rowspan = count($accounts);

            ?>

                <table class="report-table">

                    <thead>

                        <tr>

                            <th class="col-no">
                                No
                            </th>

                            <th class="col-platform">
                                Nama Media Sosial
                            </th>

                            <th class="col-account">
                                Nama Akun
                            </th>

                            <th class="col-content">
                                Jumlah Konten
                            </th>

                            <th class="col-follower">
                                Jumlah Follower
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    foreach ($accounts as $index => $row):

                    ?>

                        <tr>

                            <?php if ($index === 0): ?>

                                <td
                                    class="number-cell"
                                    rowspan="<?= $rowspan ?>"
                                >

                                    <?= $noPlatform ?>

                                </td>


                                <td
                                    class="platform-cell"
                                    rowspan="<?= $rowspan ?>"
                                >

                                    <?= htmlspecialchars(
                                        $platformMeta[$platform]
                                        ?? $platform
                                    ) ?>

                                </td>

                            <?php endif; ?>


                            <!-- AKUN -->

                            <td class="account-cell">

                                <?= htmlspecialchars(
                                    $row['nama_akun'] ?? '-'
                                ) ?>

                            </td>


                            <!-- JUMLAH KONTEN -->

                            <td class="content-cell">

                                <?= number_format(
                                    (int) ($row['jumlah_konten'] ?? 0),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </td>


                            <!-- FOLLOWER -->

                            <td class="follower-cell">

                                <?php

                                if (
                                    isset($row['jumlah_follower']) &&
                                    $row['jumlah_follower'] !== null
                                ) {

                                    echo number_format(
                                        (int) $row['jumlah_follower'],
                                        0,
                                        ',',
                                        '.'
                                    );

                                } else {

                                    echo '-';

                                }

                                ?>

                            </td>

                        </tr>


                    <?php endforeach; ?>

                    </tbody>

                </table>


            <?php

                $noPlatform++;

            endforeach;


        endforeach;

    endforeach;


else:

    /*
    |--------------------------------------------------------------------------
    | MODE NORMAL
    |--------------------------------------------------------------------------
    */

    $noPlatform = 1;

    foreach ($groupedData as $platform => $accounts):

        $rowspan = count($accounts);

    ?>

        <table class="report-table">

            <thead>

                <tr>

                    <th class="col-no">
                        No
                    </th>

                    <th class="col-platform">
                        Nama Media Sosial
                    </th>

                    <th class="col-account">
                        Nama Akun
                    </th>

                    <th class="col-content">
                        Jumlah Konten
                    </th>

                    <?php if ($showFollower): ?>

                        <th class="col-follower">
                            Jumlah Follower
                        </th>

                    <?php endif; ?>

                </tr>

            </thead>


            <tbody>

            <?php

            foreach ($accounts as $index => $row):

            ?>

                <tr>

                    <?php if ($index === 0): ?>

                        <td
                            class="number-cell"
                            rowspan="<?= $rowspan ?>"
                        >

                            <?= $noPlatform ?>

                        </td>


                        <td
                            class="platform-cell"
                            rowspan="<?= $rowspan ?>"
                        >

                            <?= htmlspecialchars(
                                $platformMeta[$platform]
                                ?? $platform
                            ) ?>

                        </td>

                    <?php endif; ?>


                    <td class="account-cell">

                        <?= htmlspecialchars(
                            $row['nama_akun'] ?? '-'
                        ) ?>

                    </td>


                    <td class="content-cell">

                        <?= number_format(
                            (int) ($row['jumlah_konten'] ?? 0),
                            0,
                            ',',
                            '.'
                        ) ?>

                    </td>


                    <?php if ($showFollower): ?>

                        <td class="follower-cell">

                            <?php

                            if (
                                isset($row['jumlah_follower']) &&
                                $row['jumlah_follower'] !== null
                            ) {

                                echo number_format(
                                    (int) $row['jumlah_follower'],
                                    0,
                                    ',',
                                    '.'
                                );

                            } else {

                                echo '-';

                            }

                            ?>

                        </td>

                    <?php endif; ?>

                </tr>


            <?php endforeach; ?>

            </tbody>

        </table>


    <?php

        $noPlatform++;

    endforeach;

endif;

?>


<?php endif; ?>


</body>

</html>