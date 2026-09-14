<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda belum login.'
    ]);
    exit;
}

require_once '../database.php';

$action = $_POST['action'] ?? '';

/* =========================================================
   DELETE DATA FOLLOWER
========================================================= */

if ($action === 'delete') {

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID data tidak valid.'
        ]);
        exit;
    }

    try {

        /* ================================================
           CEK DATA TERLEBIH DAHULU
        ================================================= */

        $stmt = $pdo->prepare("
            SELECT id
            FROM follower_sosmed
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $id
        ]);

        if (!$stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Data follower tidak ditemukan.'
            ]);
            exit;
        }

        /* ================================================
           HAPUS DATA
        ================================================= */

        $stmt = $pdo->prepare("
            DELETE FROM follower_sosmed
            WHERE id = ?
        ");

        $stmt->execute([
            $id
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Data follower berhasil dihapus.'
        ]);
        exit;

    } catch (PDOException $e) {

        echo json_encode([
            'success' => false,
            'message' => 'Gagal menghapus data follower.'
        ]);
        exit;
    }
}


/* =========================================================
   AMBIL DATA FORM
   Hanya digunakan untuk SAVE / UPDATE
========================================================= */

$id = (int) ($_POST['id'] ?? 0);

$idAkun = (int) ($_POST['id_akun'] ?? 0);

$tahun = (int) ($_POST['tahun'] ?? 0);

$bulanRaw = $_POST['bulan'] ?? '';

$jumlahFollowerRaw = $_POST['jumlah_follower'] ?? 0;


/* =========================================================
   KONVERSI BULAN
========================================================= */

$namaBulan = [
    'januari'   => 1,
    'februari'  => 2,
    'maret'     => 3,
    'april'     => 4,
    'mei'       => 5,
    'juni'      => 6,
    'juli'      => 7,
    'agustus'   => 8,
    'september' => 9,
    'oktober'   => 10,
    'november'  => 11,
    'desember'  => 12
];

$bulanLower = strtolower(trim((string) $bulanRaw));

if (isset($namaBulan[$bulanLower])) {
    $bulan = $namaBulan[$bulanLower];
} else {
    $bulan = (int) $bulanRaw;
}


/* =========================================================
   KONVERSI JUMLAH FOLLOWER
========================================================= */

$jumlahFollowerRaw = str_replace('.', '', (string) $jumlahFollowerRaw);
$jumlahFollowerRaw = str_replace(',', '', $jumlahFollowerRaw);

$jumlahFollower = (int) $jumlahFollowerRaw;


/* =========================================================
   VALIDASI DATA
========================================================= */

if (
    $idAkun <= 0 ||
    $tahun < 2000 ||
    $tahun > 2100 ||
    $bulan < 1 ||
    $bulan > 12 ||
    $jumlahFollower < 0
) {
    echo json_encode([
        'success' => false,
        'message' => 'Data follower tidak valid.'
    ]);
    exit;
}


/* =========================================================
   PROSES SAVE / UPDATE
========================================================= */

try {

    /* =====================================================
       CEK AKUN
    ===================================================== */

    $stmt = $pdo->prepare("
        SELECT id
        FROM akun_sosmed
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $idAkun
    ]);

    if (!$stmt->fetch()) {

        echo json_encode([
            'success' => false,
            'message' => 'Akun media sosial tidak ditemukan.'
        ]);

        exit;
    }


    /* =====================================================
       TAMBAH DATA
       action = save
    ===================================================== */

    if ($action === 'save') {

        /* ================================================
           CEK DATA DUPLIKAT
        ================================================= */

        $stmt = $pdo->prepare("
            SELECT id
            FROM follower_sosmed
            WHERE
                id_akun = ?
                AND tahun = ?
                AND bulan = ?
            LIMIT 1
        ");

        $stmt->execute([
            $idAkun,
            $tahun,
            $bulan
        ]);

        $existing = $stmt->fetch();


        /* ================================================
           JIKA SUDAH ADA → UPDATE
        ================================================= */

        if ($existing) {

            $stmt = $pdo->prepare("
                UPDATE follower_sosmed
                SET jumlah_follower = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $jumlahFollower,
                $existing['id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Data follower sudah ada dan berhasil diperbarui.'
            ]);

            exit;
        }


        /* ================================================
           INSERT DATA BARU
        ================================================= */

        $stmt = $pdo->prepare("
            INSERT INTO follower_sosmed
            (
                id_akun,
                tahun,
                bulan,
                jumlah_follower
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $idAkun,
            $tahun,
            $bulan,
            $jumlahFollower
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Data follower berhasil disimpan.'
        ]);

        exit;
    }


    /* =====================================================
       EDIT DATA
       action = update
    ===================================================== */

    if ($action === 'update') {

        /* ================================================
           CEK ID
        ================================================= */

        if ($id <= 0) {

            echo json_encode([
                'success' => false,
                'message' => 'ID data follower tidak valid.'
            ]);

            exit;
        }


        /* ================================================
           CEK DATA
        ================================================= */

        $stmt = $pdo->prepare("
            SELECT id
            FROM follower_sosmed
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $id
        ]);

        if (!$stmt->fetch()) {

            echo json_encode([
                'success' => false,
                'message' => 'Data follower tidak ditemukan.'
            ]);

            exit;
        }


        /* ================================================
           CEK DUPLIKAT
        ================================================= */

        $stmt = $pdo->prepare("
            SELECT id
            FROM follower_sosmed
            WHERE
                id_akun = ?
                AND tahun = ?
                AND bulan = ?
                AND id != ?
            LIMIT 1
        ");

        $stmt->execute([
            $idAkun,
            $tahun,
            $bulan,
            $id
        ]);

        $duplicate = $stmt->fetch();


        if ($duplicate) {

            echo json_encode([
                'success' => false,
                'message' => 'Data follower untuk akun, tahun, dan bulan tersebut sudah ada.'
            ]);

            exit;
        }


        /* ================================================
           UPDATE DATA
        ================================================= */

        $stmt = $pdo->prepare("
            UPDATE follower_sosmed
            SET
                id_akun = ?,
                tahun = ?,
                bulan = ?,
                jumlah_follower = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $idAkun,
            $tahun,
            $bulan,
            $jumlahFollower,
            $id
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Data follower berhasil diperbarui.'
        ]);

        exit;
    }


    /* =====================================================
       ACTION TIDAK DIKENALI
    ===================================================== */

    echo json_encode([
        'success' => false,
        'message' => 'Action tidak dikenali: ' . $action
    ]);

    exit;


} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Gagal memproses data follower.'
    ]);

    exit;
}