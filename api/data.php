<?php

header(
    'Content-Type: application/json; charset=utf-8'
);

require_once "../database.php";


/*
|--------------------------------------------------------------------------
| AMBIL PLATFORM
|--------------------------------------------------------------------------
*/

$platform =
    strtoupper(
        trim(
            $_GET['platform'] ?? ''
        )
    );


/*
|--------------------------------------------------------------------------
| AMBIL TAHUN
|--------------------------------------------------------------------------
*/

$tahun =
    (int)(
        $_GET['tahun'] ?? date('Y')
    );


/*
|--------------------------------------------------------------------------
| PLATFORM YANG DIIZINKAN
|--------------------------------------------------------------------------
*/

$allowedPlatforms = [

    'INSTAGRAM',
    'FACEBOOK',
    'TIKTOK',
    'YOUTUBE'

];


if (
    !in_array(
        $platform,
        $allowedPlatforms,
        true
    )
) {

    echo json_encode([

        'success' => false,

        'message' =>
            'Platform tidak valid.'

    ]);

    exit;

}


/*
|--------------------------------------------------------------------------
| NAMA BULAN
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| AMBIL DATA DARI DATABASE
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    SELECT
        bulan,
        jumlah_konten

    FROM konten_sosmed

    WHERE platform = ?
    AND tahun = ?

    ORDER BY bulan ASC

");


$stmt->execute([

    $platform,
    $tahun

]);


/*
|--------------------------------------------------------------------------
| SIMPAN DATA DATABASE
|--------------------------------------------------------------------------
*/

$dbData = [];

while ($row = $stmt->fetch()) {

    $dbData[
        (int)$row['bulan']
    ] = (int)$row['jumlah_konten'];

}


/*
|--------------------------------------------------------------------------
| SUSUN 12 BULAN
|--------------------------------------------------------------------------
*/

$data = [];

$total = 0;


foreach (
    $months
    as $number => $name
) {

    $jumlah =
        $dbData[$number] ?? 0;

    $data[] = [

        'bulan' => $name,

        'jumlah' => $jumlah

    ];

    $total += $jumlah;

}


/*
|--------------------------------------------------------------------------
| RESPONSE API
|--------------------------------------------------------------------------
*/

echo json_encode([

    'success' => true,

    'platform' => $platform,

    'tahun' => $tahun,

    'data' => $data,

    'total' => $total

], JSON_UNESCAPED_UNICODE);

?>