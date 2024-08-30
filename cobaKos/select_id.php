<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Masukkan ID Penghuni</title>
    <link rel="stylesheet" href="css/style.css">
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
        <h1>Masukkan ID Penghuni</h1>
        <form action="choose_room.php" method="post">
            <label for="id_penghuni">ID Penghuni:</label>
            <input type="number" name="id_penghuni" id="id_penghuni" required>
            <input type="submit" value="Lanjutkan">
        </form>
    </div>
</body>
</html>
