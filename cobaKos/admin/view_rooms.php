<?php
require_once '../includes/db_connect.php';
require_once '../classes/Kamar.php';

$database = new Database();
$db = $database->dbConnection();

$kamar = new Kamar($db);
$stmt = $kamar->read();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Kamar</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">Sistem</span> Kos</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>Daftar Kamar</h1>
        <table>
            <thead>
                <tr>
                    <th>Nomor Kamar</th>
                    <th>Harga Kamar</th>
                    <th>Jenis Kamar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['nomor_kamar']; ?></td>
                        <td><?php echo $row['harga_kamar']; ?></td>
                        <td><?php echo $row['jenis_kamar']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
