<?php
require_once __DIR__ . '/../includes/db_connect.php';

class LogParkir {
    private $conn;
    private $table_name = "Log_Parkir";

    public $id;
    public $id_penghuni;
    public $nomor_lahan_parkir;
    public $tanggal_masuk;
    public $tanggal_keluar;
    public $total_harga;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_penghuni, nomor_lahan_parkir, tanggal_masuk, tanggal_keluar, total_harga) 
                  VALUES (:id_penghuni, :nomor_lahan_parkir, :tanggal_masuk, :tanggal_keluar, :total_harga)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_penghuni', $this->id_penghuni);
        $stmt->bindParam(':nomor_lahan_parkir', $this->nomor_lahan_parkir);
        $stmt->bindParam(':tanggal_masuk', $this->tanggal_masuk);
        $stmt->bindParam(':tanggal_keluar', $this->tanggal_keluar);
        $stmt->bindParam(':total_harga', $this->total_harga);

        if ($stmt->execute()) {
            return true;
        } else {
            return $stmt->errorInfo(); // Return the error info
        }
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_penghuni = :id_penghuni, nomor_lahan_parkir = :nomor_lahan_parkir, 
                      tanggal_masuk = :tanggal_masuk, tanggal_keluar = :tanggal_keluar, total_harga = :total_harga 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_penghuni', $this->id_penghuni);
        $stmt->bindParam(':nomor_lahan_parkir', $this->nomor_lahan_parkir);
        $stmt->bindParam(':tanggal_masuk', $this->tanggal_masuk);
        $stmt->bindParam(':tanggal_keluar', $this->tanggal_keluar);
        $stmt->bindParam(':total_harga', $this->total_harga);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            return $stmt->errorInfo(); // Return the error info
        }
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            return $stmt->errorInfo(); // Return the error info
        }
    }
}
?>
