<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db_connect.php';
require_once '../classes/Kamar.php';
require_once '../classes/DetailKamar.php';
require_once '../classes/Penghuni.php';

$database = new Database();
$db = $database->dbConnection();

$kamar = new Kamar($db);
$penghuni = new Penghuni($db);

$stmt_penghuni = $penghuni->getActivePenghuni();
$stmt_kamar = $kamar->getAvailableKamar();

$message = '';
$id_penghuni = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_penghuni']) && !isset($_POST['nomor_kamar'])) {
        $id_penghuni = $_POST['id_penghuni'];
    } else {
        $detailKamar = new DetailKamar($db);

        $detailKamar->id_penghuni = $_POST['id_penghuni'];
        $detailKamar->nomor_kamar = $_POST['nomor_kamar'];
        $detailKamar->durasi_kamar = $_POST['durasi'];
        $detailKamar->tanggal_mulai_sewa = $_POST['tanggal_mulai_sewa'];
        $detailKamar->tanggal_selesai_sewa = date("Y-m-d", strtotime("+{$_POST['durasi']} months", strtotime($_POST['tanggal_mulai_sewa'])));
        $detailKamar->total_harga = $_POST['total_harga'];

        error_log("Detail Kamar: " . print_r($detailKamar, true));

        
        
        
            $stmt_check = $detailKamar->checkAvailability($detailKamar->nomor_kamar, $detailKamar->tanggal_mulai_sewa, $detailKamar->tanggal_selesai_sewa);
            
            if ($stmt_check->rowCount() == 0) {
                if ($detailKamar->create()) {
                    $message = "Penyewaan kamar berhasil.";
                } else {
                    $message = "Penyewaan kamar gagal.";
                }
            } else {
                $message = "Kamar sudah terisi.";
            }
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pilih Kamar</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
       function calculateEndDate() {
            const startDate = document.getElementById('tanggal_mulai_sewa').value;
            const duration = document.getElementById('durasi').value;
            const nomorKamarSelect = document.getElementById('nomor_kamar');
            const hargaKamar = parseFloat(nomorKamarSelect.options[nomorKamarSelect.selectedIndex].getAttribute('data-harga'));

            if (startDate && duration) {
                const endDate = new Date(startDate);
                endDate.setMonth(endDate.getMonth() + parseInt(duration));
                document.getElementById('tanggal_selesai_sewa').value = endDate.toISOString().split('T')[0];
                
                const totalHarga = hargaKamar * duration;
                document.getElementById('total_harga').value = totalHarga;
            }
        }

        function showAlert(message, type) {
            Swal.fire({
                title: type === 'success' ? 'Sukses!' : 'Gagal!',
                text: message,
                icon: type,
                confirmButtonText: 'OK'
            });
        }
    </script>
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
      

        <?php if ($message) { ?>
            <script>showAlert('<?php echo $message; ?>', '<?php echo strpos($message, "berhasil") !== false ? "success" : "error"; ?>');</script>
        <?php } ?>

        <?php if ($id_penghuni === null) { ?>
            <h1>Pilih Penghuni</h1>

            <form method="post">
                <label for="id_penghuni">ID Penghuni:</label>
                <select name="id_penghuni" id="id_penghuni" required>
                    <?php while ($row_penghuni = $stmt_penghuni->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row_penghuni['id']; ?>"><?php echo $row_penghuni['id'] . '-' . $row_penghuni['nama_penghuni']; ?></option>
                    <?php } ?>
                </select>
                <input type="submit" value="Lanjutkan">
            </form>
        <?php } else { ?>
            <h1>Pilih Kamar</h1>

            <form method="post">
                <input type="hidden" name="id_penghuni" value="<?php echo $id_penghuni; ?>">

                <label for="nomor_kamar">Nomor Kamar:</label>
                <select name="nomor_kamar" id="nomor_kamar">
                    <?php while ($row = $stmt_kamar->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['nomor_kamar']; ?>" data-harga="<?php echo $row['harga_kamar']; ?>"><?php echo $row['nomor_kamar']; ?></option>
                    <?php } ?>
                </select><br>

                <label for="durasi">Durasi (bulan):</label>
                <input type="number" name="durasi" id="durasi" min="1" required oninput="calculateEndDate()"><br>

                <label for="tanggal_mulai_sewa">Tanggal Mulai Sewa:</label>
                <input type="date" name="tanggal_mulai_sewa" id="tanggal_mulai_sewa" required onchange="calculateEndDate()"><br>

                <label for="total_harga">Total harga kamar:</label>
                <input type="number" name="total_harga" id="total_harga" readonly><br>

                <label for="tanggal_selesai_sewa">Tanggal Selesai Sewa:</label>
                <input type="date" id="tanggal_selesai_sewa" readonly><br>

                <input type="submit" value="Submit">
            </form>
        <?php } ?>
    </div>
</body>
</html>
