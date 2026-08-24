<?php

header('Content-Type: application/json; charset=utf-8');

require_once '../database.php';

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
        $platform === '' ||
        !in_array($platform, $allowedPlatforms, true)
    ) {

        echo json_encode([
            'success' => false,
            'message' => 'Platform tidak valid.',
            'data' => []
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA AKUN
    |--------------------------------------------------------------------------
    |
    | nama_akun digunakan sebagai username yang
    | ditampilkan pada dropdown.
    |
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            platform,
            nama_akun
        FROM akun_sosmed
        WHERE UPPER(platform) = ?
        ORDER BY nama_akun ASC
    ");


    $stmt->execute([
        $platform
    ]);


    $accounts = [];


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $accounts[] = [
            'id' => (int) $row['id'],
            'platform' => strtoupper(
                trim($row['platform'])
            ),
            'username' => $row['nama_akun']
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        'success' => true,
        'message' => 'Data akun berhasil diambil.',
        'data' => $accounts
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan database.',
        'data' => []
    ], JSON_UNESCAPED_UNICODE);

}