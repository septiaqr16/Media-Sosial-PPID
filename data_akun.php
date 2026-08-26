<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'database.php';


/*
|--------------------------------------------------------------------------
| AJAX CRUD DATA AKUN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = trim($_POST['action'] ?? '');

    if ($action === 'save_account') {

        header('Content-Type: application/json; charset=UTF-8');

        $id = (int) ($_POST['id'] ?? 0);
        $platform = strtoupper(trim($_POST['platform'] ?? ''));
        $namaAkun = trim($_POST['nama_akun'] ?? '');

        $allowedPlatforms = [
            'INSTAGRAM',
            'FACEBOOK',
            'TIKTOK',
            'YOUTUBE'
        ];

        if (!in_array($platform, $allowedPlatforms, true)) {
            echo json_encode([
                'success' => false,
                'message' => 'Platform tidak valid.'
            ]);
            exit;
        }

        if ($namaAkun === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Nama akun wajib diisi.'
            ]);
            exit;
        }

        try {
            if ($id > 0) {
                $stmt = $pdo->prepare(''
                    . 'UPDATE akun_sosmed '
                    . 'SET platform = ?, nama_akun = ? '
                    . 'WHERE id = ?'
                );

                $stmt->execute([
                    $platform,
                    $namaAkun,
                    $id
                ]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Data akun berhasil diperbarui.'
                ]);
                exit;
            }

            $stmt = $pdo->prepare(''
                . 'INSERT INTO akun_sosmed '
                . '(platform, nama_akun) '
                . 'VALUES (?, ?)'
            );

            $stmt->execute([
                $platform,
                $namaAkun
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Akun baru berhasil ditambahkan.'
            ]);
            exit;

        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menyimpan data akun. Periksa struktur tabel atau data yang dimasukkan.'
            ]);
            exit;
        }
    }


    if ($action === 'delete_account') {

        header('Content-Type: application/json; charset=UTF-8');

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID akun tidak valid.'
            ]);
            exit;
        }

        try {
            /*
             * Jangan menghapus akun yang masih dipakai oleh
             * data konten atau data follower.
             */
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM konten_sosmed WHERE akun_id = ?'
            );
            $stmt->execute([$id]);
            $jumlahKonten = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM follower_sosmed WHERE id_akun = ?'
            );
            $stmt->execute([$id]);
            $jumlahFollower = (int) $stmt->fetchColumn();

            if ($jumlahKonten > 0 || $jumlahFollower > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Akun tidak dapat dihapus karena masih memiliki data konten atau follower yang terhubung.'
                ]);
                exit;
            }

            $stmt = $pdo->prepare(
                'DELETE FROM akun_sosmed WHERE id = ?'
            );
            $stmt->execute([$id]);

            echo json_encode([
                'success' => true,
                'message' => 'Akun berhasil dihapus.'
            ]);
            exit;

        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus akun.'
            ]);
            exit;
        }
    }
}

/*
|--------------------------------------------------------------------------
| DATA AKUN SOSIAL MEDIA
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

$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
        Data Akun - Media Sosial Probolinggo
    </title>


    <!-- FONT -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >


    <!-- CSS DASHBOARD -->

    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >


    <!-- CSS DATA AKUN -->

    <link
        rel="stylesheet"
        href="css/data_akun.css"
    >

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar">


    <!-- LOGO -->

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



    <!-- NAVIGATION -->

    <nav>


        <span class="nav-label">
            MENU
        </span>


        <!-- DASHBOARD -->

        <a
            href="dashboard.php"
            class="nav-link"
        >

            <i class="fas fa-gauge-high"></i>

            Dashboard

        </a>



        <!-- DATA KONTEN -->

        <a
            href="data_konten.php"
            class="nav-link"
        >

            <i class="fas fa-table"></i>

            Data Konten

        </a>



        <!-- DATA FOLLOWER -->

        <a
            href="data_follower.php"
            class="nav-link"
        >

            <i class="fas fa-users"></i>

            Data Follower

        </a>



        <!-- DATA AKUN -->

        <a
            href="data_akun.php"
            class="nav-link active"
        >

            <i class="fas fa-id-card"></i>

            Data Akun

        </a>


    </nav>



    <!-- SIDEBAR BOTTOM -->

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



<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<main class="dashboard-main">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <header class="dashboard-header">


        <div>

            <span>
                DATA AKUN
            </span>

            <h1>
                Kelola Data Akun Media Sosial
            </h1>

        </div>



        <!-- ADMIN PROFILE -->

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
                        ?? 'Administrator',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </strong>


                <small>

                    @<?= htmlspecialchars(
                        $_SESSION['admin_username']
                        ?? 'admin',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </small>

            </div>



            <i
                class="fas fa-pen admin-profile-edit-icon"
            ></i>


        </button>


    </header>



    <!-- =====================================================
         DATA AKUN
    ====================================================== -->

    <div class="dashboard-view active">


        <section
            class="panel"
            id="account-table"
        >


            <!-- PANEL HEADER -->

            <div class="panel-header">


                <div class="panel-header-left">


                    <div class="panel-header-icon">

                        <i class="fas fa-id-card"></i>

                    </div>



                    <div>

                        <span>
                            DATA TERSIMPAN
                        </span>

                        <h2>
                            Data Akun Media Sosial
                        </h2>


                        <p
                            class="panel-desc"
                            id="accountTableCount"
                        >

                            <?= count($accounts) ?>

                            akun media sosial tersimpan
                            di database.

                        </p>


                    </div>


                </div>



                <!-- TOMBOL TAMBAH -->

                <button
                    type="button"
                    class="btn-add-data"
                    id="btnTambahAkun"
                >

                    <i class="fas fa-plus"></i>

                    Tambah Akun

                </button>


            </div>



            <!-- =================================================
                 FILTER PLATFORM
            ================================================== -->

            <div class="content-filter-bar">


                <div
                    class="platform-filter"
                    id="accountPlatformFilter"
                >


                    <!-- SEMUA -->

                    <button
                        type="button"
                        class="filter-tab account-filter-tab active"
                        data-account-filter="all"
                    >

                        Semua

                        <span
                            class="filter-count"
                            id="countAll"
                        >

                            <?= count($accounts) ?>

                        </span>

                    </button>



                    <!-- INSTAGRAM -->

                    <button
                        type="button"
                        class="filter-tab account-filter-tab filter-tab-instagram"
                        data-account-filter="instagram"
                    >

                        <i class="fab fa-instagram"></i>

                        Instagram

                    </button>



                    <!-- FACEBOOK -->

                    <button
                        type="button"
                        class="filter-tab account-filter-tab filter-tab-facebook"
                        data-account-filter="facebook"
                    >

                        <i class="fab fa-facebook-f"></i>

                        Facebook

                    </button>



                    <!-- TIKTOK -->

                    <button
                        type="button"
                        class="filter-tab account-filter-tab filter-tab-tiktok"
                        data-account-filter="tiktok"
                    >

                        <i class="fab fa-tiktok"></i>

                        TikTok

                    </button>



                    <!-- YOUTUBE -->

                    <button
                        type="button"
                        class="filter-tab account-filter-tab filter-tab-youtube"
                        data-account-filter="youtube"
                    >

                        <i class="fab fa-youtube"></i>

                        YouTube

                    </button>


                </div>

            </div>



            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                Platform
                            </th>

                            <th>
                                Nama Akun
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        id="accountTableBody"
                    >


                        <?php if (empty($accounts)): ?>


                            <tr>

                                <td
                                    colspan="4"
                                    class="empty"
                                >

                                    <i class="fas fa-inbox"></i>

                                    <span>
                                        Belum ada data akun.
                                    </span>

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach (
                                $accounts
                                as $index => $account
                            ): ?>


                                <?php

                                $platform =
                                    strtoupper(
                                        trim(
                                            $account['platform']
                                        )
                                    );

                                $platformLower =
                                    strtolower(
                                        $platform
                                    );

                                $platformLabel =
                                    $platformMeta[$platform]['label']
                                    ?? $platform;

                                $platformIcon =
                                    $platformMeta[$platform]['icon']
                                    ?? 'fas fa-share-nodes';

                                ?>


                                <tr
                                    class="account-row"
                                    data-platform="<?= htmlspecialchars(
                                        $platformLower,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    data-search="<?= htmlspecialchars(
                                        strtolower(
                                            $platform . ' ' .
                                            $account['nama_akun']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >


                                    <!-- NOMOR -->

                                    <td
                                        class="account-number"
                                    >

                                        <?= $index + 1 ?>

                                    </td>



                                    <!-- PLATFORM -->

                                    <td>

                                        <span
                                            class="
                                                platform-chip
                                                chip-<?= htmlspecialchars(
                                                    $platformLower,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            "
                                        >

                                            <i
                                                class="<?= htmlspecialchars(
                                                    $platformIcon,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            ></i>


                                            <?= htmlspecialchars(
                                                $platformLabel,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>


                                        </span>

                                    </td>



                                    <!-- NAMA AKUN -->

                                    <td>

                                        <div
                                            class="account-cell"
                                        >

                                            <i
                                                class="fas fa-user-circle"
                                            ></i>


                                            <strong>

                                                <?= htmlspecialchars(
                                                    $account['nama_akun']
                                                    ?? '-',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </strong>


                                        </div>

                                    </td>



                                    <!-- AKSI -->

                                    <td>

                                        <div
                                            class="action-buttons"
                                        >


                                            <!-- EDIT -->

                                            <button
                                                type="button"
                                                class="
                                                    btn-edit
                                                    btn-edit-account
                                                "
                                                title="Edit"
                                                data-id="<?= (int)$account['id'] ?>"
                                                data-platform="<?= htmlspecialchars(
                                                    $platform,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                data-nama="<?= htmlspecialchars(
                                                    $account['nama_akun'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                            >

                                                <i
                                                    class="fas fa-pen"
                                                ></i>

                                            </button>



                                            <!-- HAPUS -->

                                            <button
                                                type="button"
                                                class="
                                                    btn-delete
                                                    btn-delete-account
                                                "
                                                title="Hapus"
                                                data-id="<?= (int)$account['id'] ?>"
                                            >

                                                <i
                                                    class="fas fa-trash"
                                                ></i>

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
         MODAL TAMBAH / EDIT AKUN
    ====================================================== -->

    <div
        class="profile-modal"
        id="modalAkun"
    >


        <div
            class="profile-modal-content"
        >


            <!-- CLOSE -->

            <button
                type="button"
                class="close-modal"
                id="closeAccountModal"
            >

                &times;

            </button>



            <!-- MODAL HEADER -->

            <div
                class="profile-modal-header"
            >


                <div
                    class="profile-modal-icon"
                >

                    <i
                        class="fas fa-id-card"
                    ></i>

                </div>



                <div>

                    <span
                        class="subtitle"
                    >

                        DATA AKUN

                    </span>


                    <h2
                        id="accountModalTitle"
                    >

                        Tambah Akun Media Sosial

                    </h2>


                    <p>

                        Masukkan platform dan
                        nama akun media sosial.

                    </p>


                </div>


            </div>



            <!-- FORM -->

            <form
                id="accountForm"
                class="profile-form"
            >


                <!-- ID -->

                <input
                    type="hidden"
                    name="id"
                    id="accountId"
                    value=""
                >



                <!-- PLATFORM -->

                <div
                    class="form-group"
                >


                    <label
                        for="accountPlatform"
                    >

                        <i
                            class="fas fa-share-nodes"
                        ></i>

                        Platform

                    </label>



                    <select
                        id="accountPlatform"
                        name="platform"
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



                <!-- NAMA AKUN -->

                <div
                    class="form-group"
                >


                    <label
                        for="accountName"
                    >

                        <i
                            class="fas fa-user-circle"
                        ></i>

                        Nama Akun

                    </label>



                    <input
                        type="text"
                        id="accountName"
                        name="nama_akun"
                        placeholder="Contoh: @pemkab_probolinggo"
                        required
                    >


                </div>



                <!-- MESSAGE -->

                <div
                    id="accountMessage"
                    class="message"
                ></div>



                <!-- BUTTON -->

                <div
                    class="form-actions"
                >


                    <button
                        type="submit"
                        class="btn-save"
                    >

                        <i
                            class="fas fa-save"
                        ></i>

                        Simpan Data

                    </button>



                    <button
                        type="button"
                        class="btn-cancel"
                        id="btnCancelAccount"
                    >

                        <i
                            class="fas fa-xmark"
                        ></i>

                        Batal

                    </button>


                </div>


            </form>


        </div>


    </div>


</main>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="js/data_akun.js?v=20260826"></script>


</body>

</html>