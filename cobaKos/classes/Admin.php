<?php
require_once '../includes/db_connect.php';

class Admin {
    private $conn;
    private $table_name = "Admin";

    public $id_admin;
    public $nama_admin;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nama_admin) VALUES (:nama_admin)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nama_admin", $this->nama_admin);

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
        $query = "UPDATE " . $this->table_name . " SET nama_admin = :nama_admin WHERE id_admin = :id_admin";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_admin", $this->id_admin);
        $stmt->bindParam(":nama_admin", $this->nama_admin);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_admin = :id_admin";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_admin", $this->id_admin);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
