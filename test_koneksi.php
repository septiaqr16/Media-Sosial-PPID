<?php

require_once "database.php";

echo "<h1>TEST DATABASE</h1>";

try {

    // Cek database yang sedang digunakan
    $stmt = $pdo->query("SELECT DATABASE() AS nama_database");

    $db = $stmt->fetch();

    echo "<p><b>Database aktif:</b> "
        . htmlspecialchars($db['nama_database'])
        . "</p>";


    // Cek server MySQL
    $stmt = $pdo->query("
        SELECT
            @@hostname AS hostname,
            @@port AS port,
            VERSION() AS versi
    ");

    $server = $stmt->fetch();

    echo "<p><b>Hostname:</b> "
        . htmlspecialchars($server['hostname'])
        . "</p>";

    echo "<p><b>Port:</b> "
        . htmlspecialchars($server['port'])
        . "</p>";

    echo "<p><b>Versi MySQL:</b> "
        . htmlspecialchars($server['versi'])
        . "</p>";


    // Cek tabel konten_sosmed
    $stmt = $pdo->query("
        SHOW TABLES LIKE 'konten_sosmed'
    ");

    $table = $stmt->fetch();

    if (!$table) {

        echo "<p style='color:red;'>
            ❌ Tabel konten_sosmed TIDAK ditemukan.
        </p>";

        exit;

    }

    echo "<p style='color:green;'>
        ✅ Tabel konten_sosmed ditemukan.
    </p>";


    // Hitung jumlah data
    $stmt = $pdo->query("
        SELECT COUNT(*) AS jumlah
        FROM konten_sosmed
    ");

    $data = $stmt->fetch();

    echo "<h2>
        Jumlah data: "
        . (int)$data['jumlah']
        . "
    </h2>";


    // Tampilkan 10 data pertama
    $stmt = $pdo->query("
        SELECT
            id,
            platform,
            tahun,
            bulan,
            jumlah_konten
        FROM konten_sosmed
        ORDER BY id ASC
        LIMIT 10
    ");

    $rows = $stmt->fetchAll();


    echo "<h2>Data Pertama</h2>";

    if (!$rows) {

        echo "<p style='color:red;'>
            ❌ Tabel ditemukan tetapi TIDAK memiliki data.
        </p>";

    } else {

        echo "<table border='1'
                cellpadding='8'
                cellspacing='0'>";

        echo "
            <tr>
                <th>ID</th>
                <th>Platform</th>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Jumlah Konten</th>
            </tr>
        ";

        foreach ($rows as $row) {

            echo "<tr>";

            echo "<td>"
                . htmlspecialchars($row['id'])
                . "</td>";

            echo "<td>"
                . htmlspecialchars($row['platform'])
                . "</td>";

            echo "<td>"
                . htmlspecialchars($row['tahun'])
                . "</td>";

            echo "<td>"
                . htmlspecialchars($row['bulan'])
                . "</td>";

            echo "<td>"
                . htmlspecialchars($row['jumlah_konten'])
                . "</td>";

            echo "</tr>";

        }

        echo "</table>";

    }


} catch (PDOException $e) {

    echo "<h2 style='color:red;'>
        ❌ ERROR
    </h2>";

    echo "<pre>";

    echo htmlspecialchars(
        $e->getMessage()
    );

    echo "</pre>";

}

?>