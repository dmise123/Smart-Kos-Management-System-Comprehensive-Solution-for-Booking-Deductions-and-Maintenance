<?php
require_once '../includes/db_connect.php';

class DendaPelanggaran {
    private $conn;
    private $table_name = "Denda_Pelanggaran";

    public $id;
    public $total_denda;
    public $keterangan;
    public $id_admin;
    public $id_penghuni;

    public $fillable = [
        'id',
        'total_denda',
        'keterangan',
        'id_admin',
        'id_penghuni'
    ];

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (total_denda, keterangan, id_admin, id_penghuni) VALUES (:total_denda, :keterangan, :id_admin, :id_penghuni)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":total_denda", $this->total_denda);
        $stmt->bindParam(":keterangan", $this->keterangan);
        $stmt->bindParam(":id_admin", $this->id_admin);
        $stmt->bindParam(":id_penghuni", $this->id_penghuni);

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

    public function readWithAdminPenghuni() {
        $query = "SELECT d.*, a.nama_admin, p.nama_penghuni FROM `denda_pelanggaran` d
        left JOIN admin a on d.id_admin = d.id_admin
        LEFT JOIN penghuni p on d.id_penghuni = p.id;";
        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT d.*, a.nama_admin, p.nama_penghuni FROM `denda_pelanggaran` d
        left JOIN admin a on d.id_admin = d.id_admin
        LEFT JOIN penghuni p on d.id_penghuni = p.id WHERE d.id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET total_denda = :total_denda, keterangan = :keterangan, id_admin = :id_admin, id_penghuni = :id_penghuni WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":total_denda", $this->total_denda);
        $stmt->bindParam(":keterangan", $this->keterangan);
        $stmt->bindParam(":id_admin", $this->id_admin);
        $stmt->bindParam(":id_penghuni", $this->id_penghuni);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePartial(){
        $query = "UPDATE " . $this->table_name . " SET ";
        $set = "";
        foreach($this->fillable as $key){
            if($this->{$key} != null){
                $set .= $key . " = :" . $key . ", ";
            }
        }
        $set = rtrim($set, ", "); //hapus coma di akhir
        $query .= $set . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        foreach($this->fillable as $key){
            if($this->{$key} != null){
                $stmt->bindParam(":" . $key, $this->{$key});
            }
        }

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
