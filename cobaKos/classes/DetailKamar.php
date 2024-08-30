<?php
require_once '../includes/db_connect.php';
require_once 'TagihanKamar.php';



class DetailKamar {
    private $conn;
    private $table_name = "Detail_Kamar";

    public $id;
    public $id_penghuni;
    public $nomor_kamar;
    public $durasi_kamar;
    public $tanggal_mulai_sewa;
    public $tanggal_selesai_sewa;
    public $total_harga;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $this->total_harga = $this->fetchRoomPrice($this->nomor_kamar) * $this->durasi_kamar;
    
        $query = "INSERT INTO " . $this->table_name . " (id_penghuni, nomor_kamar, durasi_kamar, tanggal_mulai_sewa, tanggal_selesai_sewa, total_harga) VALUES (:id_penghuni, :nomor_kamar, :durasi_kamar, :tanggal_mulai_sewa, :tanggal_selesai_sewa, :total_harga)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(":id_penghuni", $this->id_penghuni);
        $stmt->bindParam(":nomor_kamar", $this->nomor_kamar);
        $stmt->bindParam(":durasi_kamar", $this->durasi_kamar);
        $stmt->bindParam(":tanggal_mulai_sewa", $this->tanggal_mulai_sewa);
        $stmt->bindParam(":tanggal_selesai_sewa", $this->tanggal_selesai_sewa);
        $stmt->bindParam(":total_harga", $this->total_harga);
    
        if ($stmt->execute()) {
            $tagihanKamar = new TagihanKamar($this->conn);
            $this->id = $this->conn->lastInsertId();
            $detailKamar =  $tagihanKamar->createTagihanKamarSelamaKos($this);

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
        $query = "UPDATE " . $this->table_name . " SET id_penghuni = :id_penghuni, nomor_kamar = :nomor_kamar, durasi_kamar = :durasi_kamar, tanggal_mulai_sewa = :tanggal_mulai_sewa, tanggal_selesai_sewa = :tanggal_selesai_sewa, total_harga = :total_harga WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_penghuni", $this->id_penghuni);
        $stmt->bindParam(":nomor_kamar", $this->nomor_kamar);
        $stmt->bindParam(":durasi_kamar", $this->durasi_kamar);
        $stmt->bindParam(":tanggal_mulai_sewa", $this->tanggal_mulai_sewa);
        $stmt->bindParam(":tanggal_selesai_sewa", $this->tanggal_selesai_sewa);
        $stmt->bindParam(":total_harga", $this->total_harga);

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
    public function checkRoomStatus($nomor_kamar) {
        $stmt_status = $this->conn->prepare("SELECT status FROM kamar WHERE nomor_kamar = :nomor_kamar");
        $stmt_status->bindParam(":nomor_kamar", $nomor_kamar);
        $stmt_status->execute();
        $room_status = $stmt_status->fetch(PDO::FETCH_ASSOC);
        return $room_status;
    }

    public function checkAvailability($nomor_kamar, $tanggal_mulai_sewa, $tanggal_selesai_sewa) {
        $stmt_check = $this->conn->prepare("SELECT * FROM detail_kamar WHERE nomor_kamar = :nomor_kamar AND (tanggal_mulai_sewa <= :tanggal_selesai_sewa AND tanggal_selesai_sewa >= :tanggal_mulai_sewa)");
        $stmt_check->bindParam(":nomor_kamar", $nomor_kamar);
        $stmt_check->bindParam(":tanggal_mulai_sewa", $tanggal_mulai_sewa);
        $stmt_check->bindParam(":tanggal_selesai_sewa", $tanggal_selesai_sewa);
        $stmt_check->execute();
        return $stmt_check;
    }
    public function getRoomDetails($nomor_kamar) {
        $stmt = $this->conn->prepare("SELECT * FROM detail_kamar WHERE nomor_kamar = :nomor_kamar");
        $stmt->bindParam(":nomor_kamar", $nomor_kamar);
        $stmt->execute();
        return $stmt;
    }

    public function fetchRoomPrice($nomor_kamar) {
        $stmt = $this->conn->prepare("SELECT harga_kamar FROM kamar WHERE nomor_kamar = :nomor_kamar");
        $stmt->bindParam(":nomor_kamar", $nomor_kamar);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['harga_kamar'];
    }

    public function getActivePenghuni() {
        $query = "SELECT dk.id_penghuni, p.nama_penghuni, dk.tanggal_mulai_sewa, dk.tanggal_selesai_sewa 
                  FROM " . $this->table_name . " dk
                  JOIN Penghuni p ON dk.id_penghuni = p.id
                  WHERE p.status != 'non active' AND dk.tanggal_selesai_sewa > CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function getPenghuniDetail($id_penghuni) {
        $query = "SELECT dk.tanggal_mulai_sewa, dk.tanggal_selesai_sewa 
                  FROM " . $this->table_name . " dk
                  WHERE dk.id_penghuni = :id_penghuni";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_penghuni', $id_penghuni);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
