<?php
require_once '../includes/db_connect.php';

class Komplain {
    private $conn;
    private $table_name = "Komplain";

    public $id_penghuni;
    public $tambahan;
    public $tanggal_komplain;
    public $deskripsi;
    public $bukti;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (id_penghuni, tambahan, tanggal_komplain, deskripsi, bukti) VALUES (:id_penghuni, :tambahan, :tanggal_komplain, :deskripsi, :bukti)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_penghuni", $this->id_penghuni);
        $stmt->bindParam(":tambahan", $this->tambahan);
        $stmt->bindParam(":tanggal_komplain", $this->tanggal_komplain);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        $stmt->bindParam(":bukti", $this->bukti);

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
        $query = "UPDATE " . $this->table_name . " SET tanggal_komplain = :tanggal_komplain, deskripsi = :deskripsi, bukti = :bukti WHERE id_penghuni = :id_penghuni AND tambahan = :tambahan";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_penghuni", $this->id_penghuni);
        $stmt->bindParam(":tambahan", $this->tambahan);
        $stmt->bindParam(":tanggal_komplain", $this->tanggal_komplain);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        $stmt->bindParam(":bukti", $this->bukti);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_penghuni = :id_penghuni AND tambahan = :tambahan";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_penghuni", $this->id_penghuni);
        $stmt->bindParam(":tambahan", $this->tambahan);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
