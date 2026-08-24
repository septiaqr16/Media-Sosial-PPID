<?php

header('Content-Type: application/json; charset=utf-8');

require_once "../database.php";

try {

    /*
    |--------------------------------------------------------------------------
    | AMBIL PLATFORM
    |--------------------------------------------------------------------------
    */

    $platform = strtoupper(
        trim($_GET['platform'] ?? '')
    );


    /*
    |--------------------------------------------------------------------------
    | AMBIL TAHUN
    |--------------------------------------------------------------------------
    */

    $tahun = (int)(
        $_GET['tahun'] ?? date('Y')
    );


    /*
    |--------------------------------------------------------------------------
    | AMBIL AKUN
    |--------------------------------------------------------------------------
    |
    | Kosong = semua akun
    |
    */

    $akunId = trim(
        $_GET['akun_id'] ?? ''
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
            'message' => 'Platform tidak valid.',
            'data' => [],
            'total' => 0
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
    | QUERY DATA
    |--------------------------------------------------------------------------
    |
    | Semua akun:
    |   SUM(jumlah_konten) berdasarkan bulan
    |
    | Akun tertentu:
    |   SUM(jumlah_konten) berdasarkan bulan
    |   dengan filter akun_id
    |
    */

    $sql = "

        SELECT
            bulan,
            SUM(jumlah_konten) AS jumlah_konten

        FROM konten_sosmed

        WHERE platform = ?
        AND tahun = ?

    ";


    $params = [

        $platform,
        $tahun

    ];


    /*
    |--------------------------------------------------------------------------
    | FILTER AKUN JIKA DIPILIH
    |--------------------------------------------------------------------------
    */

    if ($akunId !== '') {

        $sql .= "
            AND akun_id = ?
        ";

        $params[] = (int)$akunId;

    }


    /*
    |--------------------------------------------------------------------------
    | GROUP BULAN
    |--------------------------------------------------------------------------
    */

    $sql .= "

        GROUP BY bulan
        ORDER BY bulan ASC

    ";


    /*
    |--------------------------------------------------------------------------
    | EKSEKUSI
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare($sql);

    $stmt->execute($params);


    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA DATABASE
    |--------------------------------------------------------------------------
    */

    $dbData = [];


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

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
        $months as $number => $name
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

        'akun_id' =>
            $akunId !== ''
                ? (int)$akunId
                : null,

        'data' => $data,

        'total' => $total

    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([

        'success' => false,

        'message' =>
            'Terjadi kesalahan database.',

        'data' => [],

        'total' => 0

    ], JSON_UNESCAPED_UNICODE);

}

?>