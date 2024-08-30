<?php
    session_start();
    require_once '../includes/db_connect.php';
    require_once '../classes/DendaPelanggaran.php';
    require_once '../classes/Penghuni.php';

    $database = new Database();
    $db = $database->dbConnection();

    if(isset($_POST['Request'])){
        $denda = new DendaPelanggaran($db);
        $request = $_POST['Request'];

        
        switch($request){
            case 'add':
                if(empty($_POST['total_denda']) || empty($_POST['keterangan']) || empty($_POST['id_penghuni'])){
                    echo json_encode([
                        "success" => false,
                        "msg" => "Data tidak boleh kosong"
                    ]);
                    return;
                }
                if(!is_numeric($_POST['total_denda'])){
                    echo json_encode([
                        "success" => false,
                        "msg" => "Total denda harus berupa angka"
                    ]);
                    return;
                }
                $denda->total_denda = $_POST['total_denda'];
                $denda->keterangan = $_POST['keterangan'];
                $denda->id_admin = $_SESSION['id'] ;
                $denda->id_penghuni = $_POST['id_penghuni'];
                if($denda->create()){
                    echo json_encode([
                        "success" => true,
                        "msg" => "Denda berhasil ditambahkan"
                    
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "msg" => "Denda gagal ditambahkan"
                    ]);
                }
                break;
            case 'read':
                $data = $denda->readWithAdminPenghuni()->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([
                    "success" => true,
                    "data" => $data
                ]);
                break;
            case 'getById':
                if(empty($_POST['id'])){
                    echo json_encode([
                        "success" => false,
                        "msg" => "ID tidak boleh kosong"
                    ]);
                    return;
                }
                $data = $denda->getById($_POST['id'])->fetch(PDO::FETCH_ASSOC);
                echo json_encode([
                    "success" => true,
                    "data" => $data
                ]);
                break;
            case 'update':
                if(empty($_POST['id']) || empty($_POST['total_denda']) || empty($_POST['keterangan'])){
                    echo json_encode([
                        "success" => false,
                        "msg" => "Data tidak boleh kosong"
                    ]);
                    return;
                }
                if(!is_numeric($_POST['total_denda'])){
                    echo json_encode([
                        "success" => false,
                        "msg" => "Total denda harus berupa angka"
                    ]);
                    return;
                }
                $denda->id = $_POST['id'];
                $denda->total_denda = $_POST['total_denda'];
                $denda->keterangan = $_POST['keterangan'];
                $denda->id_admin = $_SESSION['id'] ;
                $denda->id_penghuni = $_POST['id_penghuni'];
                if($denda->update()){
                    echo json_encode([
                        "success" => true,
                        "msg" => "Denda berhasil diupdate"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "msg" => "Denda gagal diupdate"
                    ]);
                }
                break;
            case 'delete':
                if(empty($_POST['id'])){
                    echo json_encode([
                        "success" => false,
                        "msg" => "ID tidak boleh kosong"
                    ]);
                    return;
                }
                $denda->id = $_POST['id'];
                if($denda->delete()){
                    echo json_encode([
                        "success" => true,
                        "msg" => "Denda berhasil dihapus"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "msg" => "Denda gagal dihapus"
                    ]);
                }
                break;
            default:
                break;
        };
        return;
    }
?>

<?php
    $penghuni = new Penghuni($db);
    $listPenghuni = $penghuni->read()->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Fines</title>
<?php include '../includes/meta.php'; ?>
<style>
    .swal2-container {
        z-index: 2000 !important; /* Adjust the z-index as needed */
    }
    .modal-box{
        z-index: 10 !important;
    }
</style>
</head>

<body class="bg-gray-100 font-sans m-0 p-0 min-h-svh">
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

    <div class="container mx-auto  md:p-4 md:w-4/5">
        <h1 class="text-2xl font-bold mb-4">Management Fine</h1>
        <button class="btn mb-4 bg-blue-500 text-white hover:bg-blue-700" onclick="my_modal_3.showModal()">add user</button>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-black text-white">
                    <tr>
                        <th class="py-2 px-2 md:px-4 border border-gray-300">No</th>
                        <th class="py-2 px-2 md:px-4 border border-gray-300">Penghuni</th>
                        <th class="py-2 px-2 md:px-4 border border-gray-300">Total Denda</th>
                        <th class="py-2 px-2 md:px-4 border border-gray-300">Keterangan</th>
                        <th class="py-2 px-2 md:px-4 border border-gray-300">ID Admin</th>
                        <th class="py-2 px-2 md:px-4 border border-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Additional rows as needed -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- add modal -->
    <dialog id="my_modal_3" class="modal">
        <div class="modal-box">
            <form method="dialog" class="p-4" id="addForm" >
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" data-close>✕</button>
                
                <div class="mb-4">
                    <label for="penghuni" class="block text-sm font-medium text-gray-700">Select User</label>
                    <select id="penghuni" name="penghuni" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <!-- Add options for users here -->
                        <?php foreach ($listPenghuni as $penghuni): ?>
                            <option value="<?php echo $penghuni['id']; ?>"><?= $penghuni['nama_penghuni']; ?> - <?= $penghuni['status']  ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700">Fine Category</label>
                    <select id="category" name="category" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="category1">Category 1</option>
                        <option value="category2">Category 2</option>
                        <option value="category3">Category 3</option>
                    </select>
                </div> -->

                <div class="mb-4">
                    <label for="total_denda" class="block text-sm font-medium text-gray-700">Total Fine</label>
                    <input type="text" id="total_denda" name="total_denda" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="keterangan" name="keterangan" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                </div>
                <div class="flex justify-end">
                    <button id="add_denda" type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- edit modal -->
    <div id="editModal"></div>

    


    <script>
        document.getElementById('nav-toggle').onclick = function() {
            var navMenu = document.getElementById('nav-menu');
            navMenu.classList.toggle('hidden');
        };
    </script>

    <script>
        $(document).ready(function() {
            // add denda 
            $('#add_denda').click(function(e) {
                e.preventDefault();
                var id_penghuni = $('#penghuni').val();
                var total_denda = $('#total_denda').val();
                var keterangan = $('#keterangan').val();

                $.ajax({
                    type: 'POST',
                    url: 'manage_denda.php',
                    dataType: 'json',
                    data: {
                        Request: 'add',
                        id_penghuni: id_penghuni,
                        total_denda: total_denda,
                        keterangan: keterangan
                    },
                    success: function(response) {
                        console.log(response);
                        my_modal_3.close();
                        if(response.success) {
                            $('#addForm').trigger('reset');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Fine added successfully',
                            });
                            $('tbody').empty();
                            getAllData();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg,
                            });
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add fine',
                        });
                    }
                });
                
            });
            function getAllData(){
                 // get all denda
                $.ajax({
                    type: 'POST',
                    url: 'manage_denda.php',
                    dataType: 'json',
                    data: {
                        Request: 'read'
                    },
                    success: function(response) {
                        if(response.success){
                            let i = 1;
                            response.data.forEach(denda => {
                                $('tbody').append(`
                                    <tr class="hover:bg-gray-200">
                                        <td class="py-2 px-2 md:px-4 border border-gray-300">${i}</td>
                                        <td class="py-2 px-2 md:px-4 border border-gray-300">${denda.nama_penghuni}</td>
                                        <td class="py-2 px-2 md:px-4 border border-gray-300">${denda.total_denda}</td>
                                        <td class="py-2 px-2 md:px-4 border border-gray-300">${denda.keterangan}</td>
                                        <td class="py-2 px-2 md:px-4 border border-gray-300">${denda.nama_admin}</td>
                                        <td class="py-2 px-2 md:px-4 border border-gray-300">
                                            <button data-id = ${denda.id} class="btn-edit bg-blue-500 text-white px-2 md:px-3 py-1 rounded hover:bg-blue-700">Edit</button>
                                            <button data-id = ${denda.id} class="btn-delete bg-red-500 text-white px-2 md:px-3 py-1 rounded hover:bg-red-700">Delete</button>
                                        </td>
                                    </tr>
                                `);
                                i++;
                            });
                        }
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            }
            getAllData();

            function getById(id){
                $.ajax({
                    type: 'POST',
                    url: 'manage_denda.php',
                    dataType: 'json',
                    data: {
                        Request: 'getById',
                        id: id
                    },
                    success: function(response) {
                        console.log(response);
                        if(response.success){
                            let denda = response.data;
                            $('#editModal').html(`
                            <dialog id="my_modal_4" class="modal">
                                <div class="modal-box">
                                    <form method="dialog" class="p-4" id="addForm" >
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" data-close>✕</button>
                                        
                                        <div class="mb-4">
                                            <label for="penghuni" class="block text-sm font-medium text-gray-700">Select User</label>
                                            <input value="${denda.nama_penghuni}" type="text" name="penghuni" id="edit_penghuni" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-gray-200 cursor-not-allowed" readonly>            
                                        </div>


                                        <div class="mb-4">
                                            <label for="total_denda" class="block text-sm font-medium text-gray-700">Total Fine</label>
                                            <input value=${denda.total_denda} type="text" id="edit_total_denda" name="total_denda" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </div>

                                        <div class="mb-4">
                                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Description</label>
                                            <textarea id="edit_keterangan" name="keterangan" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">${denda.keterangan} </textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button data-id=${denda.id} id="edit_denda" type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                        <input value=${denda.id_penghuni} type="hidden" id="id_penghuni" name="total_denda" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        
                                    </form>
                                </div>
                            </dialog>
                            `);
                            my_modal_4.showModal();
                        }
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            }

            //delete denda
            $(document).on('click', '.btn-delete', function(){
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'manage_denda.php',
                            dataType: 'json',
                            data: {
                                Request: 'delete',
                                id: id
                            },
                            success: function(response) {
                                if(response.success){
                                    Swal.fire(
                                        'Deleted!',
                                        'Fine has been deleted.',
                                        'success'
                                    );
                                    $('tbody').empty();
                                    getAllData();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to delete fine.',
                                        'error'
                                    );
                                }
                            },
                            error: function(response) {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete fine.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            //edit denda
            $(document).on('click', '.btn-edit', function(){
                var id = $(this).data('id');
                getById(id);
            });

            //save edit
            $(document).on('click', '#edit_denda', function(e){
                e.preventDefault();
                var id = $(this).data('id');
                var total_denda = $('#edit_total_denda').val();
                var keterangan = $('#edit_keterangan').val();
                var id_penghuni = $('#id_penghuni').val();

                $.ajax({
                    type: 'POST',
                    url: 'manage_denda.php',
                    dataType: 'json',
                    data: {
                        Request: 'update',
                        id: id,
                        total_denda: total_denda,
                        keterangan: keterangan,
                        id_penghuni: id_penghuni
                        
                    },
                    success: function(response) {
                        console.log(response);
                        my_modal_4.close();
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Fine updated successfully',
                            });
                            $('tbody').empty();
                            getAllData();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg,
                            });
                        }
                    },
                    error: function(response) {
                        console.log(response)
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update fine',
                        });
                    }
                });
            });





        });
    </script>
</body>

</html>
