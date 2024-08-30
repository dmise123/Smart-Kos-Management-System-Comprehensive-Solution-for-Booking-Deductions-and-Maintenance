<?php
require_once '../includes/db_connect.php';

class Penghuni {
    private $conn;
    private $table_name = "Penghuni";

    public $id;
    public $nama_penghuni;
    public $no_ktp;
    public $no_telpon;
    public $kontak_wali;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nama_penghuni, no_ktp, no_telpon, kontak_wali, status) VALUES (:nama_penghuni, :no_ktp, :no_telpon, :kontak_wali, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nama_penghuni", $this->nama_penghuni);
        $stmt->bindParam(":no_ktp", $this->no_ktp);
        $stmt->bindParam(":no_telpon", $this->no_telpon);
        $stmt->bindParam(":kontak_wali", $this->kontak_wali);
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
        $query = "UPDATE " . $this->table_name . " SET nama_penghuni = :nama_penghuni, no_ktp = :no_ktp, no_telpon = :no_telpon, kontak_wali = :kontak_wali, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nama_penghuni", $this->nama_penghuni);
        $stmt->bindParam(":no_ktp", $this->no_ktp);
        $stmt->bindParam(":no_telpon", $this->no_telpon);
        $stmt->bindParam(":kontak_wali", $this->kontak_wali);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getActivePenghuni() {
        $query = "SELECT id, nama_penghuni FROM " . $this->table_name . " WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
