<?php
require_once '../includes/db_connect.php';

abstract class Tagihan {
    private $conn;
    private $table_name;

    public $id;
    public $tanggal_maksimal_bayar;
    public $harga_tagihan;
    public $denda_keterlambatan;
    public $tanggal_bayar;


    // Konstruktor untuk inisialisasi properti umum
    public function __construct($db) {
        $this->conn = $db;
    }

    abstract public function create();
    abstract public function read();
    abstract public function update();
    abstract public function delete();

    abstract public function getDetailPayment();

}
?>
