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

$isAllPlatform  = ($filterPlatform === '');
$isAllMonth     = ($filterBulan <= 0);
$isSpecificYear = ($filterTahun > 0);


/*
|--------------------------------------------------------------------------
| TENTUKAN APAKAH FOLLOWER DITAMPILKAN
|--------------------------------------------------------------------------
*/

$showFollower =
    ($filterBulan > 0) ||
    (!$isAllPlatform && $isSpecificYear);


/*
|--------------------------------------------------------------------------
| KONDISI PENGELOMPOKAN PER BULAN
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


/*
|--------------------------------------------------------------------------
| PARAMETER QUERY
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| DOWNLOAD CSV
|--------------------------------------------------------------------------
|
| Jika URL memiliki:
| ?download=csv
|
| Browser akan langsung mendownload file CSV.
|
*/

if (
    isset($_GET['download']) &&
    $_GET['download'] === 'csv'
) {

    /*
    |--------------------------------------------------------------------------
    | NAMA FILE
    |--------------------------------------------------------------------------
    */

    $fileName = 'rekap_pemberitaan_';


    if ($filterPlatform !== '') {

        $fileName .=
            strtolower($filterPlatform) . '_';

    } else {

        $fileName .=
            'semua_platform_';
    }


    if ($filterAkun > 0) {

        $fileName .=
            'akun_' .
            $filterAkun .
            '_';
    }


    if (
        $filterBulan > 0 &&
        isset($months[$filterBulan])
    ) {

        $fileName .=
            strtolower($months[$filterBulan]) .
            '_';
    } else {

        $fileName .=
            'semua_bulan_';
    }


    if ($filterTahun > 0) {

        $fileName .=
            $filterTahun;

    } else {

        $fileName .=
            'semua_tahun';
    }


    $fileName .= '.csv';


    /*
    |--------------------------------------------------------------------------
    | HEADER DOWNLOAD
    |--------------------------------------------------------------------------
    */

    header(
        'Content-Type: text/csv; charset=UTF-8'
    );

    header(
        'Content-Disposition: attachment; filename="' .
        $fileName .
        '"'
    );

    header(
        'Pragma: no-cache'
    );

    header(
        'Expires: 0'
    );


    /*
    |--------------------------------------------------------------------------
    | BUAT FILE CSV
    |--------------------------------------------------------------------------
    */

    $output = fopen(
        'php://output',
        'w'
    );


    /*
    |--------------------------------------------------------------------------
    | UTF-8 BOM
    |--------------------------------------------------------------------------
    |
    | Agar karakter Indonesia terbaca dengan baik di Excel.
    |
    */

    fwrite(
        $output,
        "\xEF\xBB\xBF"
    );


    /*
    |--------------------------------------------------------------------------
    | HEADER CSV
    |--------------------------------------------------------------------------
    */

    $csvHeader = [];


    if ($groupByMonth) {

        $csvHeader[] = 'Tahun';

        $csvHeader[] = 'Bulan';
    }


    $csvHeader[] = 'No';

    $csvHeader[] = 'Nama Media Sosial';

    $csvHeader[] = 'Nama Akun';

    $csvHeader[] = 'Jumlah Konten';


    if ($showFollower) {

        $csvHeader[] = 'Jumlah Follower';
    }


    fputcsv(
        $output,
        $csvHeader,
        ';'
    );


    /*
    |--------------------------------------------------------------------------
    | DATA CSV
    |--------------------------------------------------------------------------
    */

    $no = 1;


    foreach ($rows as $row) {

        $csvData = [];


        if ($groupByMonth) {

            $csvData[] =
                $row['tahun'] ?? '';

            $csvData[] =
                $months[
                    (int) (
                        $row['bulan'] ?? 0
                    )
                ]
                ?? '';
        }


        $platform = strtoupper(
            trim(
                $row['platform'] ?? ''
            )
        );


        $csvData[] = $no;

        $csvData[] =
            $platformMeta[$platform]
            ?? $platform;

        $csvData[] =
            $row['nama_akun']
            ?? '-';

        $csvData[] =
            (int) (
                $row['jumlah_konten']
                ?? 0
            );


        if ($showFollower) {

            if (
                isset($row['jumlah_follower']) &&
                $row['jumlah_follower'] !== null
            ) {

                $csvData[] =
                    (int) $row['jumlah_follower'];

            } else {

                $csvData[] = '-';
            }
        }


        fputcsv(
            $output,
            $csvData,
            ';'
        );


        $no++;
    }


    fclose($output);

    exit;
}


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

        $tahun = (int) (
            $row['tahun'] ?? 0
        );

        $bulan = (int) (
            $row['bulan'] ?? 0
        );

        $platform = strtoupper(
            trim(
                $row['platform'] ?? ''
            )
        );


        if (
            !isset($groupedData[$tahun])
        ) {

            $groupedData[$tahun] = [];
        }


        if (
            !isset(
                $groupedData[$tahun][$bulan]
            )
        ) {

            $groupedData[$tahun][$bulan] = [];
        }


        if (
            !isset(
                $groupedData[$tahun][$bulan][$platform]
            )
        ) {

            $groupedData[$tahun][$bulan][$platform] = [];
        }


        $groupedData[$tahun][$bulan][$platform][] =
            $row;
    }


/*
|--------------------------------------------------------------------------
| MODE NORMAL
|--------------------------------------------------------------------------
*/

} else {

    foreach ($rows as $row) {

        $platform = strtoupper(
            trim(
                $row['platform'] ?? ''
            )
        );


        if (
            !isset(
                $groupedData[$platform]
            )
        ) {

            $groupedData[$platform] = [];
        }


        $groupedData[$platform][] =
            $row;
    }
}

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
    Rekap Pemberitaan Media Sosial
</title>


<style>

/* =========================================================
   GLOBAL
========================================================= */

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


/* =========================================================
   REPORT ACTIONS
========================================================= */

.report-actions {
    display: flex;

    align-items: center;

    justify-content: flex-end;

    flex-wrap: wrap;

    gap: 10px;

    margin-bottom: 20px;

    padding-bottom: 15px;

    border-bottom:
        1px solid #e5e7eb;
}


.btn-report {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    min-height: 38px;

    padding: 0 16px;

    border: none;

    border-radius: 7px;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size: 12px;

    font-weight: 600;

    cursor: pointer;

    transition:
        transform .2s ease,
        box-shadow .2s ease,
        background .2s ease;
}


.btn-report:hover {
    transform:
        translateY(-1px);
}


/* CETAK */

.btn-print {
    background: #218838;

    color: #ffffff;
}


.btn-print:hover {
    background: #1c7430;

    box-shadow:
        0 5px 14px
        rgba(33, 136, 56, .25);
}


/* DOWNLOAD */

.btn-download {
    background: #1877f2;

    color: #ffffff;
}


.btn-download:hover {
    background: #0d65d9;

    box-shadow:
        0 5px 14px
        rgba(24, 119, 242, .25);
}


/* TUTUP */

.btn-close {
    background: #f1f3f5;

    color: #495057;
}


.btn-close:hover {
    background: #e2e6ea;
}


/* =========================================================
   HEADER
========================================================= */

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


/* =========================================================
   FILTER INFO
========================================================= */

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


/* =========================================================
   JUDUL BULAN
========================================================= */

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


/* =========================================================
   TABLE
========================================================= */

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


/* =========================================================
   HEADER TABLE
========================================================= */

.report-table thead th {
    background: #dcefe0;

    color: #204325;

    text-align: center;

    font-size: 9px;

    font-weight: 700;

    text-transform: uppercase;
}


/* =========================================================
   KOLOM
========================================================= */

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


/* =========================================================
   PLATFORM CELL
========================================================= */

.platform-cell {
    text-align: center;

    font-weight: 700;

    text-transform: uppercase;

    background: #f1f9f2;

    color: #204325;
}


/* =========================================================
   NOMOR
========================================================= */

.number-cell {
    text-align: center;

    font-weight: 600;

    background: #f7fbf7;
}


/* =========================================================
   AKUN
========================================================= */

.account-cell {
    font-weight: 500;

    padding-left: 10px !important;
}


/* =========================================================
   JUMLAH KONTEN
========================================================= */

.content-cell {
    text-align: center;

    font-weight: 700;

    color: #286035;
}


/* =========================================================
   FOLLOWER
========================================================= */

.follower-cell {
    text-align: center;

    font-weight: 600;

    color: #204325;
}


/* =========================================================
   ZEBRA
========================================================= */

.report-table tbody tr:nth-child(even) td {
    background: #eaf6ec;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 600px) {

    body {
        padding: 15px;
    }


    .report-actions {
        justify-content: stretch;
    }


    .btn-report {
        flex: 1;

        min-width: 140px;
    }
}


/* =========================================================
   PRINT
========================================================= */

@media print {

    .report-actions {
        display: none !important;
    }


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
     TOMBOL AKSI
========================================================= -->

<div class="report-actions">

    <button
        type="button"
        class="btn-report btn-print"
        id="btnPrint"
    >
        🖨️ Cetak / Simpan PDF
    </button>


    <button
        type="button"
        class="btn-report btn-download"
        id="btnDownload"
    >
        ⬇️ Download CSV
    </button>


    <button
        type="button"
        class="btn-report btn-close"
        id="btnClose"
    >
        ✕ Tutup
    </button>

</div>


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

        <?= htmlspecialchars(
            (string) $periodeTahun
        ) ?>

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

    <?= htmlspecialchars(
        (string) $periodeTahun
    ) ?>

</div>


<!-- =========================================================
     DATA KOSONG
========================================================= -->

<?php if (empty($groupedData)): ?>

    <table class="report-table">

        <tbody>

            <tr>

                <td
                    colspan="<?= $showFollower ? 5 : 4 ?>"
                    style="
                        text-align: center;
                        padding: 20px;
                        color: #777;
                    "
                >

                    Tidak ada data
                    untuk filter yang dipilih.

                </td>

            </tr>

        </tbody>

    </table>


<!-- =========================================================
     DATA ADA
========================================================= -->

<?php else: ?>


<?php if ($groupByMonth): ?>


    <!-- =====================================================
         MODE PER TAHUN → BULAN → PLATFORM → AKUN
    ====================================================== -->

    <?php
    foreach (
        $groupedData
        as
        $tahun => $monthGroups
    ):
    ?>


        <?php
        foreach (
            $monthGroups
            as
            $bulan => $platformGroups
        ):
        ?>


            <div class="month-title">

                <?= htmlspecialchars(
                    strtoupper(
                        $months[$bulan]
                        ?? 'Bulan ' . $bulan
                    )
                ) ?>

                <?= htmlspecialchars(
                    (string) $tahun
                ) ?>

            </div>


            <?php

            $noPlatform = 1;

            foreach (
                $platformGroups
                as
                $platform => $accounts
            ):

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
                    foreach (
                        $accounts
                        as
                        $index => $row
                    ):
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
                                    $row['nama_akun']
                                    ?? '-'
                                ) ?>

                            </td>


                            <td class="content-cell">

                                <?= number_format(
                                    (int) (
                                        $row['jumlah_konten']
                                        ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </td>


                            <td class="follower-cell">

                                <?php

                                if (
                                    isset(
                                        $row['jumlah_follower']
                                    ) &&
                                    $row['jumlah_follower'] !== null
                                ) {

                                    echo number_format(
                                        (int)
                                        $row['jumlah_follower'],
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
            ?>


        <?php endforeach; ?>


    <?php endforeach; ?>


<?php else: ?>


    <!-- =====================================================
         MODE NORMAL
    ====================================================== -->

    <?php

    $noPlatform = 1;

    foreach (
        $groupedData
        as
        $platform => $accounts
    ):

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
            foreach (
                $accounts
                as
                $index => $row
            ):
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
                            $row['nama_akun']
                            ?? '-'
                        ) ?>

                    </td>


                    <td class="content-cell">

                        <?= number_format(
                            (int) (
                                $row['jumlah_konten']
                                ?? 0
                            ),
                            0,
                            ',',
                            '.'
                        ) ?>

                    </td>


                    <?php if ($showFollower): ?>

                        <td class="follower-cell">

                            <?php

                            if (
                                isset(
                                    $row['jumlah_follower']
                                ) &&
                                $row['jumlah_follower'] !== null
                            ) {

                                echo number_format(
                                    (int)
                                    $row['jumlah_follower'],
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
    ?>


<?php endif; ?>


<?php endif; ?>


<script>

/* =========================================================
   CETAK / SIMPAN PDF
========================================================= */

document
    .getElementById('btnPrint')
    .addEventListener(
        'click',
        function () {

            window.print();

        }
    );


/* =========================================================
   DOWNLOAD CSV
========================================================= */

document
    .getElementById('btnDownload')
    .addEventListener(
        'click',
        function () {

            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.set(
                'download',
                'csv'
            );


            window.location.href =
                url.toString();

        }
    );


/* =========================================================
   TUTUP HALAMAN
========================================================= */

document
    .getElementById('btnClose')
    .addEventListener(
        'click',
        function () {

            /*
            | Jika halaman dibuka dengan target _blank
            | coba tutup tab.
            */

            if (window.opener) {

                window.close();

            } else {

                /*
                | Jika bukan popup/tab baru,
                | kembali ke halaman sebelumnya.
                */

                window.history.back();

            }

        }
    );

</script>


</body>

</html>