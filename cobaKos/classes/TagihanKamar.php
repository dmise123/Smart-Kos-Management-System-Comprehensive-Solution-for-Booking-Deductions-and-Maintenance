<?php
require_once '../includes/db_connect.php';
require_once 'Tagihan.php';

class TagihanKamar extends Tagihan {
    private $conn;
    private $table_name = "Tagihan_Kamar";

    public $id;
    public $detail_kamar;
    public $bulan;
    public $tanggal_maksimal_bayar;
    public $harga_tagihan;
    public $denda_keterlambatan;
    public $tanggal_bayar;

    public $fillable = [
        'id',
        'detail_kamar',
        'bulan',
        'tanggal_maksimal_bayar',
        'harga_tagihan',
        'denda_keterlambatan',
        'tanggal_bayar'
    ];
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (detail_kamar, bulan, tanggal_maksimal_bayar, harga_tagihan, denda_keterlambatan, tanggal_bayar) VALUES (:detail_kamar, :bulan, :tanggal_maksimal_bayar, :harga_tagihan, :denda_keterlambatan, :tanggal_bayar)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":detail_kamar", $this->detail_kamar);
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
        $query = "UPDATE " . $this->table_name . " SET detail_kamar = :detail_kamar, bulan = :bulan, tanggal_maksimal_bayar = :tanggal_maksimal_bayar, harga_tagihan = :harga_tagihan, denda_keterlambatan = :denda_keterlambatan, tanggal_bayar = :tanggal_bayar WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":detail_kamar", $this->detail_kamar);
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

    public function getDetailPayment(){

        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $currentDate = new DateTime();
            $dueDate = new DateTime($row['tanggal_maksimal_bayar']);
            
            if($currentDate > $dueDate) {
                $daysLate = $dueDate->diff($currentDate)->days;
                $row['denda_keterlambatan'] = $daysLate * 50000; // Rp 50.000 per hari terlambat
            } else {
                $row['denda_keterlambatan'] = 0;
            }

            return $row;
        }

        return false;
    }

    /**
         * Generate tagihan
        * @param id_detail_kamar, bulan, harga_per_bulan
    */
    public function createTagihanKamarSelamaKos(DetailKamar $detailKamar) {

        $bulan = $detailKamar->durasi_kamar;
        $date  = date('Y-m-d', strtotime('+7 days', strtotime($detailKamar->tanggal_mulai_sewa)));
        for($b = 1; $b <= $bulan; $b++){
            $this->detail_kamar = $detailKamar->id;
            $this->bulan = $b;
            $this->tanggal_maksimal_bayar = $date;
            $this->harga_tagihan = $detailKamar->total_harga / $bulan;
            $this->denda_keterlambatan = 0;
            $this->tanggal_bayar = null;
            $this->create();
            $date = date('Y-m-d', strtotime('+1 month', strtotime($date)));

        }
    }
}
?>
