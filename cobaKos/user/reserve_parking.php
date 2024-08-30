<?php
require_once 'includes/db_connect.php';
require_once 'classes/DetailKamar.php';
require_once 'classes/LahanParkir.php';
require_once 'classes/LogParkir.php';

$database = new Database();
$db = $database->dbConnection();

$detailKamar = new DetailKamar($db);
$lahanParkir = new LahanParkir($db);
$logParkir = new LogParkir($db);

$penghuniStmt = $detailKamar->getActivePenghuni();
$selectedPenghuni = null;
$availableParkings = [];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['get_available_parking'])) {
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        
        $stmt = $lahanParkir->readAvailable($startDate, $endDate);
        $availableParkings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($availableParkings);
        exit;
    }
    
    if (isset($_POST['id_penghuni'])) {
        $selectedPenghuni = $_POST['id_penghuni'];
        $penghuniDetail = $detailKamar->getPenghuniDetail($selectedPenghuni);

        if ($penghuniDetail) {
            $tanggalMulaiSewa = $penghuniDetail['tanggal_mulai_sewa'];
            $tanggalSelesaiSewa = $penghuniDetail['tanggal_selesai_sewa'];

            $startDate = max(date("Y-m-d"), $tanggalMulaiSewa);
            $endDate = $tanggalSelesaiSewa;

            $availableParkings = $lahanParkir->readAvailable($startDate, $endDate)->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = "Penghuni tidak ditemukan atau tidak aktif.";
        }
    } if (isset($_POST['submit'])) {
        $id_penghuni = $_POST['id_penghuni'];
        $nomor_lahan_parkir = $_POST['nomor_lahan_parkir'];
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $total_harga = $_POST['total_harga'];

        if (empty($id_penghuni) || empty($nomor_lahan_parkir) || empty($tanggal_mulai) || empty($tanggal_selesai) || empty($total_harga)) {
            $error = "Data tidak lengkap. Silakan lengkapi semua field.";
        } else {
            $logParkir->id_penghuni = $id_penghuni;
            $logParkir->nomor_lahan_parkir = $nomor_lahan_parkir;
            $logParkir->tanggal_masuk = $tanggal_mulai;
            $logParkir->tanggal_keluar = $tanggal_selesai;
            $logParkir->total_harga = $total_harga;

            $result = $logParkir->create();
            if ($result === true) {
                $success = "Reservasi lahan parkir berhasil.";
            } else {
                $error = "Reservasi lahan parkir gagal: " . implode(", ", $result);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservasi Lahan Parkir</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            padding-top: 60px; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        const startDate = "<?php echo isset($startDate) ? $startDate : ''; ?>";

        function updatePrice() {
            const selectedParking = document.getElementById('nomor_lahan_parkir').value;
            const pricePerDay = parseFloat(document.getElementById('harga_lahan_parkir').value);
            calculateTotal(pricePerDay);
        }

        function calculateTotal(pricePerDay) {
            const startDate = new Date(document.getElementById('tanggal_mulai').value);
            const endDate = new Date(document.getElementById('tanggal_selesai').value);
            const oneDay = 24 * 60 * 60 * 1000;
            const days = Math.round((endDate - startDate) / oneDay) + 1;

            if (days > 0) {
                document.getElementById('total_harga').value = days * pricePerDay;
            } else {
                document.getElementById('total_harga').value = 0;
            }
        }

        function openModal() {
            document.getElementById('myModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('myModal').style.display = "none";
        }

        function selectParking(nomorLahanParkir, jenisLahanParkir, hargaLahanParkir) {
            document.getElementById('nomor_lahan_parkir').value = nomorLahanParkir;
            document.getElementById('selected_parking').innerHTML = nomorLahanParkir + ' - ' + jenisLahanParkir;
            document.getElementById('harga_lahan_parkir').value = hargaLahanParkir;
            calculateTotal(hargaLahanParkir);
            closeModal();
        }

        function setDefaultDate() {
            const tanggalMulaiElem = document.getElementById('tanggal_mulai');
            const tanggalSelesaiElem = document.getElementById('tanggal_selesai');
            if (!tanggalMulaiElem || !tanggalSelesaiElem) {
                return;
            }
            const today = new Date().toISOString().split('T')[0];
            const defaultDate = new Date(startDate) > new Date(today) ? startDate : today;
            tanggalMulaiElem.value = defaultDate;
            tanggalSelesaiElem.min = defaultDate;
            calculateTotal();
        }

        function updateAvailableParking() {
            const startDate = document.getElementById('tanggal_mulai').value;
            const endDate = document.getElementById('tanggal_selesai').value;

            if (startDate && endDate) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const parkings = JSON.parse(xhr.responseText);
                        const parkingList = document.getElementById('parkingList');
                        parkingList.innerHTML = '';
                        parkings.forEach(function(parking) {
                            const li = document.createElement('li');
                            li.innerHTML = parking.nomor_lahan_parkir + ' - ' + parking.jenis_lahan_parkir + ' - Harga: ' + parking.harga_lahan_parkir;
                            li.onclick = function() {
                                selectParking(parking.nomor_lahan_parkir, parking.jenis_lahan_parkir, parking.harga_lahan_parkir);
                            };
                            parkingList.appendChild(li);
                        });
                    }
                };
                xhr.send('startDate=' + startDate + '&endDate=' + endDate + '&get_available_parking=1');
            }
        }
    </script>
</head>
<body onload="setDefaultDate()">
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
        <h1>Reservasi Lahan Parkir</h1>

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php } ?>
        <?php if (!empty($success)) { ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php } ?>

        <?php if ($selectedPenghuni === null) { ?>
            <form method="post">
                <label for="id_penghuni">Pilih Penghuni:</label>
                <select name="id_penghuni" id="id_penghuni">
                    <option value="">Pilih Penghuni</option>
                    <?php while ($row = $penghuniStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['id_penghuni']; ?>"><?php echo $row['id_penghuni'] . ' - ' . $row['nama_penghuni']; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Lanjutkan">
            </form>
        <?php } else { ?>
            <form method="post">
                <input type="hidden" name="id_penghuni" value="<?php echo $selectedPenghuni; ?>">

                <label for="tanggal_mulai">Tanggal Mulai Sewa:</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" min="<?php echo $startDate; ?>" max="<?php echo $endDate; ?>" required onchange="updateAvailableParking(); calculateTotal()"><br>

                <label for="tanggal_selesai">Tanggal Selesai Sewa:</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" min="<?php echo $startDate; ?>" max="<?php echo $endDate; ?>" required onchange="updateAvailableParking(); calculateTotal()"><br>

                <label for="nomor_lahan_parkir">Nomor Lahan Parkir:</label>
                <input type="hidden" name="nomor_lahan_parkir" id="nomor_lahan_parkir">
                <button type="button" onclick="openModal()">Pilih Parkiran</button>
                <span id="selected_parking">Pilih parkiran</span><br>

                <input type="hidden" id="harga_lahan_parkir" value="0">
                
                <label for="total_harga">Total Harga:</label>
                <input type="number" name="total_harga" id="total_harga" readonly><br>

                <input type="submit" name="submit" value="Reservasi">
            </form>
        <?php } ?>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Pilih Lahan Parkir</h2>
            <ul id="parkingList">
                <?php foreach ($availableParkings as $parking) { ?>
                    <li onclick="selectParking('<?php echo $parking['nomor_lahan_parkir']; ?>', '<?php echo $parking['jenis_lahan_parkir']; ?>', '<?php echo $parking['harga_lahan_parkir']; ?>')">
                        <?php echo $parking['nomor_lahan_parkir'] . ' - ' . $parking['jenis_lahan_parkir'] . ' - Harga: ' . $parking['harga_lahan_parkir']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</body>
</html>
