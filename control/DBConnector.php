<?php
class DBConnector {
    private $conn = null;
    
    private $host = 'localhost';
    private $user = 'codemexi_admin';
    private $pswd = 'Admin_code10';
    private $db   = 'codemexi_biblioteca';
    
    public function __construct() {
        $dsn = $this->createDSN();
        
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pswd);
        } catch(PDOException $ex) {
            echo $ex->getCode().' - '.$ex->getMessage();
        }
    }
    
    private function createDSN() {
        return 'mysql:host='.$this->host.';dbname='.$this->db;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function __destruct() {
        if($this->conn != null) {
            $this->conn = null;
        }
    }
}
?>
