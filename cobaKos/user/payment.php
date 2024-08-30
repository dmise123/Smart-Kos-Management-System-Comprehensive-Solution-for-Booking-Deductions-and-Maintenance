<?php
require_once '../includes/db_connect.php';
require_once '../classes/TagihanKamar.php';

$database = new Database();
$db = $database->dbConnection();

$tagihanKamar = new TagihanKamar($db);

// jika request bukan post redirect
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header('Location: listTagihan.php');
    exit;
}

if(isset($_POST['Request'])){
    $tagihanKamar = new TagihanKamar($db);
    $request = $_POST['Request'];

    switch($request){
        case 'complete': 
            if(empty($_POST['id']) || !isset($_POST['denda_keterlambatan'])){
                echo json_encode([
                    'success' => false,
                    'msg' => 'membutuhkan Id'
                ]);
                return;
            }
            $tagihanKamar->id = $_POST['id'];
            $tagihanKamar->tanggal_bayar = date('Y-m-d H:i:s');
            $tagihanKamar->denda_keterlambatan = $_POST['denda_keterlambatan'];

            if($tagihanKamar->updatePartial()){
                echo json_encode([
                    'success' => true,
                    'msg' => 'Tagihan berhasil dibayar'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'msg' => 'Tagihan gagal dibayar'
                ]);
            }
            break;
            if(empty($_POST['detail_kamar']) || empty($_POST['bulan']) || empty($_POST['harga_per_bulan'])){
                echo json_encode([
                    'success' => false,
                    'msg' => 'Semua field harus diisi'
                ]);
                return;
            }
            $bulan = $_POST['bulan'];
            $date  = date('Y-m-d', strtotime('+7 days'));
            for($b = 1; $b <= $bulan; $b++){
                $iniTagihanKamar = new TagihanKamar($db);
                $iniTagihanKamar->detail_kamar = $_POST['detail_kamar'];
                $iniTagihanKamar->bulan = $b;
                $iniTagihanKamar->tanggal_maksimal_bayar = $date;
                $iniTagihanKamar->harga_tagihan = $_POST['harga_per_bulan'];
                $iniTagihanKamar->denda_keterlambatan = 0;
                $iniTagihanKamar->tanggal_bayar = null;
                $iniTagihanKamar->create();
                $date = date('Y-m-d', strtotime('+1 month', strtotime($date)));

            }
            echo json_encode([
                'success' => true,
                'msg' => 'Tagihan berhasil dibuat'
            ]);

            break;
        default:
            break;
    };
    return;
}


// jika post
if($_SERVER['REQUEST_METHOD'] == 'POST' ){
    // cek apakah set for, id, dan id tidak kosong, jika tidak redirect balek ke listTagihan.php
    if(!isset($_POST['for']) || !isset($_POST['id']) || empty($_POST['id'])){
        header('Location: listTagihan.php');
        exit;
    }

    //for = kamar
    if($_POST['for'] == 'kamar'){
        $tagihanKamar->id = $_POST['id'];
        $paymentDetail = $tagihanKamar->getDetailPayment();
        $paymentDetail['tanggal_bayar']?  header('Location: listTagihan.php') : $paymentDetail['nama_tagihan'] = 'Tagihan Kamar';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bills</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.tailwindcss.com/3.3.0"></script>
<link href="https://cdn.jsdelivr.net/npm/daisyui@4.11.1/dist/full.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
</head>

<body class="bg-gray-100 font-sans m-0 p-0 min-h-screen">
    <!-- header -->
    <header class="bg-gray-800 text-white p-4 border-b-4 border-blue-500">
        <div class="container mx-auto flex justify-between items-center md:w-4/5">
            <div id="branding" class="text-lg font-bold">
                <h1><a href="#" class="text-white uppercase">Manage Fine</a></h1>
            </div>
            <nav class="hidden md:flex space-x-6">
                <a href="#" class="text-white uppercase">Home</a>
                <a href="#" class="text-white uppercase">About</a>
                <a href="#" class="text-white uppercase">Services</a>
                <a href="#" class="text-white uppercase">Contact</a>
            </nav>
            <div class="md:hidden">
                <button id="nav-toggle" class="text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="nav-menu" class="md:hidden hidden">
            <nav class="flex flex-col items-center space-y-4 mt-4">
                <a href="#" class="text-white uppercase">Home</a>
                <a href="#" class="text-white uppercase">About</a>
                <a href="#" class="text-white uppercase">Services</a>
                <a href="#" class="text-white uppercase">Contact</a>
            </nav>
        </div>
    </header>

    <?php if(isset($paymentDetail)): ?>
    <div class="container mx-auto md:p-4 md:w-4/5">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">Detail Pembayaran</h1>
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4 text-blue-500">Detail Payment</h2>
            <div class="mb-2">
                <label class="block text-gray-700 font-bold">Nama Tagihan:</label>
                <p><?= $paymentDetail['nama_tagihan'] ?></p>
            </div>
            <div class="mb-2">
                <label class="block text-gray-700 font-bold">Tanggal Maksimal Bayar:</label>
                <p><?= $paymentDetail['tanggal_maksimal_bayar'] ?></p>
            </div>
            <!-- <div class="mb-2">
                <label class="block text-gray-700 font-bold">Nama Pelanggan:</label>
                <p>John Doe</p>
            </div> -->
            <div class="mb-2">
                <label class="block text-gray-700 font-bold">Jumlah Tagihan:</label>
                <p><?=$paymentDetail['harga_tagihan'] ?></p>
            </div>
            <div class="mb-2">
                <label class="block text-gray-700 font-bold">Denda:</label>
                <p><?=$paymentDetail['denda_keterlambatan'] ?></p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold">Total Tagihan:</label>
                <p><?php echo ($paymentDetail['harga_tagihan'] + $paymentDetail['denda_keterlambatan'] ) ?></p>
            </div>
            <button data-denda="<?=$paymentDetail['denda_keterlambatan']?>" data-id="<?= $paymentDetail['id']?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="complete" >Pay Now</button>
        </div>
    </div>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            $('#nav-toggle').click(function() {
                $('#nav-menu').toggleClass('hidden');
            });

            $(document).ready(function(){
                $('button[type="complete"]').click(function(event){
                    event.preventDefault();
                    const id_tagihan = $(this).data('id');
                    const denda = $(this).data('denda');
                    console.log(id_tagihan)

                    Swal.fire({
                        title: 'Apakah Anda yakin ?',
                        text: 'Total Tagihan Anda sebesar Rp.' +<?php echo ($paymentDetail['harga_tagihan'] + $paymentDetail['denda_keterlambatan'] ) ?> + ',00',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, complete it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.ajax({
                                url: 'payment.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    Request: 'complete',
                                    id: id_tagihan,
                                    denda_keterlambatan:  denda,
                                },
                                success: function(response) {
                                    console.log(response)
                                    if(response.success){
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: 'Fine updated successfully',
                                        }).then((result) => {
                                            window.location.href = 'listTagihan.php'
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.msg,
                                        });
                                    }
                                },
                                error: function(err) {
                                    console.log(err)
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to update fine',
                                    });
                                }
                                
                            
                            })
                            

                            Swal.fire('Completed!', 'The bill has been marked as complete.', 'success');

                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire('Cancelled', 'The bill is not completed', 'error');
                        }
                    });
                });
        })
        });
    </script>
</body>
</html>
