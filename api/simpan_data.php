    <?php

    session_start();

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    require_once "../database.php";


    if (
        !isset($_SESSION['admin_id'])
    ) {

        http_response_code(401);

        echo json_encode([

            'success' => false,

            'message' =>
                'Anda belum login.'

        ]);

        exit;

    }


    $action =
        $_POST['action'] ?? 'save';


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    if ($action === 'delete') {

        $id =
            (int)(
                $_POST['id'] ?? 0
            );


        if ($id <= 0) {

            echo json_encode([

                'success' => false,

                'message' =>
                    'ID tidak valid.'

            ]);

            exit;

        }


        $stmt = $pdo->prepare("

            DELETE FROM konten_sosmed

            WHERE id = ?

        ");


        $stmt->execute([
            $id
        ]);


        echo json_encode([

            'success' => true,

            'message' =>
                'Data berhasil dihapus.'

        ]);

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | INPUT
    |--------------------------------------------------------------------------
    */

    $id =
        (int)(
            $_POST['id'] ?? 0
        );

    $akun_id =
        (int)(
            $_POST['akun_id'] ?? 0
        );

    $platform =
        strtoupper(
            trim(
                $_POST['platform'] ?? ''
            )
        );


    $tahun =
        (int)(
            $_POST['tahun'] ?? 0
        );


    $bulan =
        (int)(
            $_POST['bulan'] ?? 0
        );


    $jumlah =
        (int)(
            $_POST['jumlah_konten'] ?? 0
        );


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
    | VALIDASI AKUN
    |--------------------------------------------------------------------------
    */

    if ($akun_id <= 0) {

        echo json_encode([

            'success' => false,

            'message' =>
                'Akun media sosial wajib dipilih.'

        ]);

        exit;

    }


    $checkAccount = $pdo->prepare("
        SELECT id
        FROM akun_sosmed
        WHERE id = ?
        AND platform = ?
        LIMIT 1
    ");

    $checkAccount->execute([
        $akun_id,
        $platform
    ]);


    if (!$checkAccount->fetch()) {

        echo json_encode([

            'success' => false,

            'message' =>
                'Akun tidak sesuai dengan platform.'

        ]);

        exit;

    }

    if (
        $tahun < 2000 ||
        $tahun > 2100
    ) {

        echo json_encode([

            'success' => false,

            'message' =>
                'Tahun tidak valid.'

        ]);

        exit;

    }


    if (
        $bulan < 1 ||
        $bulan > 12
    ) {

        echo json_encode([

            'success' => false,

            'message' =>
                'Bulan tidak valid.'

        ]);

        exit;

    }


    if ($jumlah < 0) {

        echo json_encode([

            'success' => false,

            'message' =>
                'Jumlah konten tidak boleh negatif.'

        ]);

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | EDIT DATA
    |--------------------------------------------------------------------------
    */

    if ($id > 0) {

        $checkEdit = $pdo->prepare("
            SELECT id
            FROM konten_sosmed
            WHERE akun_id = ?
            AND tahun = ?
            AND bulan = ?
            AND id != ?
            LIMIT 1
        ");

        $checkEdit->execute([
            $akun_id,
            $tahun,
            $bulan,
            $id
        ]);

        $existingEdit = $checkEdit->fetch(PDO::FETCH_ASSOC);


        if ($existingEdit) {

            echo json_encode([

                'success' => false,

                'message' =>
                    'Data untuk akun tersebut pada bulan dan tahun yang dipilih sudah ada. Gunakan data yang sudah tersedia.'

            ]);

            exit;

        }


        /*
        |----------------------------------------------------------------------
        | UPDATE DATA
        |----------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("

            UPDATE konten_sosmed

            SET
                akun_id = ?,
                platform = ?,
                tahun = ?,
                bulan = ?,
                jumlah_konten = ?

            WHERE id = ?

        ");


        $stmt->execute([

            $akun_id,
            $platform,
            $tahun,
            $bulan,
            $jumlah,
            $id

        ]);


        echo json_encode([

            'success' => true,

            'message' =>
                'Data berhasil diperbarui.'

        ]);

        exit;

    }

    /*
    |--------------------------------------------------------------------------
    | CEK DATA DUPLIKAT
    |--------------------------------------------------------------------------
    */

    $check = $pdo->prepare("
        SELECT id, akun_id, platform, tahun, bulan
        FROM konten_sosmed
        WHERE akun_id = ?
        AND tahun = ?
        AND bulan = ?
        LIMIT 1
    ");

    $check->execute([
        $akun_id,
        $tahun,
        $bulan
    ]);

    $existing = $check->fetch(PDO::FETCH_ASSOC);


    if ($existing) {

        echo json_encode([

            'success' => false,

            'message' =>
                'Data untuk akun tersebut pada bulan dan tahun yang dipilih sudah ada. Gunakan Edit.'

        ]);

        exit;

    }
    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA BARU
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("

        INSERT INTO konten_sosmed

        (
            akun_id,
            platform,
            tahun,
            bulan,
            jumlah_konten
        )

        VALUES (?, ?, ?, ?, ?)

    ");


    $stmt->execute([

        $akun_id,

        $platform,

        $tahun,

        $bulan,

        $jumlah

    ]);


    echo json_encode([

        'success' => true,

        'message' =>
            'Data berhasil disimpan.'

    ]);

    ?>