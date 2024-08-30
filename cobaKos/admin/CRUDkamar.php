<?php
require_once '../includes/db_connect.php';
require_once '../classes/Kamar.php';
require_once '../classes/DetailKamar.php';

$database = new Database();
$db = $database->dbConnection();

$kamar = new Kamar($db);
$action = isset($_POST['action']) ? $_POST['action'] : '';
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($action) {
        case 'create':
            $kamar->nomor_kamar = $_POST['nomor_kamar'];
            $kamar->harga_kamar = $_POST['harga_kamar'];
            $kamar->jenis_kamar = $_POST['jenis_kamar'];
            $kamar->status = $_POST['status'];

            if ($kamar->create()) {
                $message = "Kamar berhasil ditambahkan.";
                $messageType = "success";
            } else {
                $message = "Gagal menambahkan kamar.";
                $messageType = "error";
            }
            break;

        case 'update':
            $kamar->nomor_kamar = $_POST['nomor_kamar'];
            $kamar->harga_kamar = $_POST['harga_kamar'];
            $kamar->jenis_kamar = $_POST['jenis_kamar'];
            $kamar->status = $_POST['status'];

            if ($kamar->update()) {
                $message = "Kamar berhasil diupdate.";
                $messageType = "success";
            } else {
                $message = "Gagal mengupdate kamar.";
                $messageType = "error";
            }
            break;

        case 'delete':
            $nomor_kamar = $_POST['nomor_kamar'];
            $detailKamar = new DetailKamar($db); 
            $stmt = $detailKamar->getRoomDetails($nomor_kamar); 
            if ($stmt->rowCount() > 0) {
                $message = "Ada penghuni di kamar ini, tidak dapat dihapus.";
                $messageType = "warning";
            } else {
                $kamar->nomor_kamar = $nomor_kamar;
                if ($kamar->delete()) {
                    $message = "Kamar berhasil dihapus.";
                    $messageType = "success";
                } else {
                    $message = "Gagal menghapus kamar.";
                    $messageType = "error";
                }
            }
            break;

        default:
            $message = "Aksi tidak valid.";
            $messageType = "error";
            break;
    }
}

// Fetch the list of rooms
$stmt = $kamar->read();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD Kamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Tambahkan ini -->
    <style>
        .container {
            margin-top: 50px;
        }
    </style>
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
      
        <h1 class="text-center mb-5">CRUD Kamar</h1>

        <!-- Message Display -->
        <?php if ($message): ?>
            <script>
                Swal.fire({
                    title: "<?php echo $messageType == 'success' ? 'Sukses!' : 'Gagal!'; ?>",
                    text: "<?php echo $message; ?>",
                    icon: "<?php echo $messageType; ?>",
                    confirmButtonText: 'OK'
                });
            </script>
        <?php endif; ?>

        <!-- Form for Create/Update Kamar -->
        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-primary" onclick="showCreateForm()">Create Kamar</button>
            </div>
        </div>
        <div id="createForm" style="display:none;">
            <form method="POST" action="CRUDkamar.php">
                <input type="hidden" id="action" name="action" value="create">
                
                <div class="row mb-3">
                    <label for="nomor_kamar" class="col-sm-2 col-form-label">Nomor Kamar:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nomor_kamar" name="nomor_kamar">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="harga_kamar" class="col-sm-2 col-form-label">Harga Kamar:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="harga_kamar" name="harga_kamar">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="jenis_kamar" class="col-sm-2 col-form-label">Jenis Kamar:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="jenis_kamar" name="jenis_kamar">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="status" class="col-sm-2 col-form-label">Status:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="status" name="status">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2">
                        <input type="submit" class="btn btn-primary" value="Submit" onclick="hideCreateForm()">
                    </div>
                </div>
            </form>
        </div>

        <!-- Daftar Kamar -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-3">Daftar Kamar</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nomor Kamar</th>
                            <th>Harga Kamar</th>
                            <th>Jenis Kamar</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>{$row['nomor_kamar']}</td>";
                            echo "<td>{$row['harga_kamar']}</td>";
                            echo "<td>{$row['jenis_kamar']}</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "<td>
                                    <form method='POST' action='CRUDkamar.php' style='display:inline;'>
                                        <input type='hidden' name='action' value='delete'>
                                        <input type='hidden' name='nomor_kamar' value='{$row['nomor_kamar']}'>
                                        <button type='submit' class='btn btn-danger'>Delete</button>
                                    </form>
                                    <button class='btn btn-warning' onclick='editRoom(\"{$row['nomor_kamar']}\", \"{$row['harga_kamar']}\", \"{$row['jenis_kamar']}\", \"{$row['status']}\")'>Edit</button>
                                    </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
            <script>
                function editRoom(nomor_kamar, harga_kamar, jenis_kamar, status) {
                    showCreateForm();
                    document.getElementById('nomor_kamar').value = nomor_kamar;
                    document.getElementById('harga_kamar').value = harga_kamar;
                    document.getElementById('jenis_kamar').value = jenis_kamar;
                    document.getElementById('status').value = status;
                    document.getElementById('action').value = 'update';

                  
                }
        
                function showCreateForm() {
                    document.getElementById('createForm').style.display = 'block';
                }
        
                function hideCreateForm() {
                    document.getElementById('createForm').style.display = 'none';
                }
            </script>
        </body>
        </html>
