<?php
require_once '../includes/db_connect.php';

class TagihanDenda {
    private $conn;
    private $table_name = "Tagihan_Denda";

    public $id;
    public $id_denda_pelanggaran;
    public $bulan;
    public $tanggal_maksimal_bayar;
    public $harga_tagihan;
    public $denda_keterlambatan;
    public $tanggal_bayar;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (id_denda_pelanggaran, bulan, tanggal_maksimal_bayar, harga_tagihan, denda_keterlambatan, tanggal_bayar) VALUES (:id_denda_pelanggaran, :bulan, :tanggal_maksimal_bayar, :harga_tagihan, :denda_keterlambatan, :tanggal_bayar)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_denda_pelanggaran", $this->id_denda_pelanggaran);
        $stmt->bindParam(":bulan", $this->bulan);
        $stmt->bindParam(":tanggal_maksimal_bayar", $this->tanggal_maksimal_bayar);
        $stmt->bindParam(":harga_tagihan", $this->harga_tagihan);
        $stmt->bindParam(":denda_keterlambatan", $this->denda_keterlambatan);
        $stmt->bindParam(":tanggal_bayar", $this->tanggal_bayar);

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
        $query = "UPDATE " . $this->table_name . " SET id_denda_pelanggaran = :id_denda_pelanggaran, bulan = :bulan, tanggal_maksimal_bayar = :tanggal_maksimal_bayar, harga_tagihan = :harga_tagihan, denda_keterlambatan = :denda_keterlambatan, tanggal_bayar = :tanggal_bayar WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_denda_pelanggaran", $this->id_denda_pelanggaran);
        $stmt->bindParam(":bulan", $this->bulan);
        $stmt->bindParam(":tanggal_maksimal_bayar", $this->tanggal_maksimal_bayar);
        $stmt->bindParam(":harga_tagihan", $this->harga_tagihan);
        $stmt->bindParam(":denda_keterlambatan", $this->denda_keterlambatan);
        $stmt->bindParam(":tanggal_bayar", $this->tanggal_bayar);

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
}
?>
