<?php
require_once __DIR__ . '/../includes/db_connect.php';

class LahanParkir {
    private $conn;
    private $table_name = "Lahan_Parkir";

    public $nomor_lahan_parkir;
    public $harga_lahan_parkir;
    public $jenis_lahan_parkir;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nomor_lahan_parkir, harga_lahan_parkir, jenis_lahan_parkir, status) VALUES (:nomor_lahan_parkir, :harga_lahan_parkir, :jenis_lahan_parkir, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nomor_lahan_parkir", $this->nomor_lahan_parkir);
        $stmt->bindParam(":harga_lahan_parkir", $this->harga_lahan_parkir);
        $stmt->bindParam(":jenis_lahan_parkir", $this->jenis_lahan_parkir);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET harga_lahan_parkir = :harga_lahan_parkir, jenis_lahan_parkir = :jenis_lahan_parkir, status = :status WHERE nomor_lahan_parkir = :nomor_lahan_parkir";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nomor_lahan_parkir", $this->nomor_lahan_parkir);
        $stmt->bindParam(":harga_lahan_parkir", $this->harga_lahan_parkir);
        $stmt->bindParam(":jenis_lahan_parkir", $this->jenis_lahan_parkir);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE nomor_lahan_parkir = :nomor_lahan_parkir";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nomor_lahan_parkir", $this->nomor_lahan_parkir);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function readAvailable($startDate, $endDate) {
        $query = "SELECT lp.nomor_lahan_parkir, lp.harga_lahan_parkir, lp.jenis_lahan_parkir 
                  FROM " . $this->table_name . " lp
                  WHERE lp.status = 'active' AND lp.nomor_lahan_parkir NOT IN 
                  (SELECT nomor_lahan_parkir FROM Log_Parkir WHERE 
                  (tanggal_masuk BETWEEN :start_date AND :end_date) OR 
                  (tanggal_keluar BETWEEN :start_date AND :end_date))";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();

        return $stmt;
    }
}

?>
