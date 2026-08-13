<?php

session_start();

require_once "database.php";

if (isset($_SESSION['admin_id'])) {

    header("Location: dashboard.php");

    exit;

}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim(
        $_POST['username'] ?? ''
    );

    $password = $_POST['password'] ?? '';

    if (
        $username === '' ||
        $password === ''
    ) {

        $error =
            "Username dan password wajib diisi.";

    } else {

        $stmt = $pdo->prepare("
            SELECT *
            FROM admin
            WHERE username = ?
            LIMIT 1
        ");

        $stmt->execute([
            $username
        ]);

        $admin = $stmt->fetch();

        if (
            $admin &&
            password_verify(
                $password,
                $admin['password']
            )
        ) {

            $_SESSION['admin_id'] =
                $admin['id'];

            $_SESSION['admin_username'] =
                $admin['username'];

            $_SESSION['admin_nama'] =
                $admin['nama'];

            header(
                "Location: dashboard.php"
            );

            exit;

        } else {

            $error =
                "Username atau password salah.";

        }

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
        Login Admin - Media Sosial Pemkab Probolinggo
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
        href="css/login.css"
    >

</head>

<body>

<div class="login-page">

    <!-- BACKGROUND -->

    <div class="login-bg">

        <img
            src="assets/gedung-pemkab3.png"
            alt="Gedung Pemkab Probolinggo"
            class="login-bg-img"
        >

        <div class="login-bg-overlay">
            <div class="dot-pattern"></div>
        </div>

    </div>


    <!-- KARTU LOGIN DI TENGAH -->

    <div class="login-center">

        <div class="login-box">

            <div class="login-box-brand">

                <img
                    src="assets/logo-pemkab2.png"
                    alt="Logo Pemkab Probolinggo"
                >

                <div class="login-box-brand-text">
                    PEMERINTAH DAERAH
                    <br>
                    KABUPATEN PROBOLINGGO
                </div>

            </div>

            <div class="login-box-header">

                <div class="login-icon">
                    <i class="fas fa-user-shield"></i>
                </div>

                <span class="login-brand-label">
                    PANEL ADMINISTRATOR
                </span>

                <h2>
                    Kelola Data Media Sosial
                </h2>

                <p class="login-description">
                    Silakan masuk dengan akun administrator untuk mengelola
                    statistik konten Instagram, Facebook, TikTok, dan YouTube
                    resmi Pemerintah Kabupaten Probolinggo.
                </p>

            </div>


            <?php if ($error): ?>

                <div class="error-message">

                    <i class="fas fa-circle-exclamation"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                action=""
                class="login-form"
            >

                <div class="form-group">

                    <label>
                        Username
                    </label>

                    <div class="input-box">

                        <i class="fas fa-user"></i>

                        <input
                            type="text"
                            name="username"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <div class="input-box">

                        <i class="fas fa-lock"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="show-password"
                            id="showPassword"
                            aria-label="Tampilkan password"
                        >

                            <i class="fas fa-eye"></i>

                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn-login"
                >

                    Masuk

                    <i class="fas fa-arrow-right"></i>

                </button>

            </form>


            <a
                href="index.php"
                class="back-home"
            >

                <i class="fas fa-arrow-left"></i>

                Kembali ke halaman utama

            </a>

        </div>

        <div class="login-footer-note">
            &copy; <?= date('Y') ?> Pemerintah Kabupaten Probolinggo &mdash; Panel Administrator Media Sosial
        </div>

    </div>

</div>
<!-- /.login-page -->


<script>

const showPassword =
    document.getElementById('showPassword');

const password =
    document.getElementById('password');

showPassword.addEventListener(
    'click',
    function () {

        if (
            password.type === 'password'
        ) {

            password.type = 'text';

            this.innerHTML =
                '<i class="fas fa-eye-slash"></i>';

        } else {

            password.type = 'password';

            this.innerHTML =
                '<i class="fas fa-eye"></i>';

        }

    }
);

</script>

</body>
</html>
