<?php
class Database {
    private $host = "localhost";
    private $db_name = "motorparts_inventory2";
    private $username = "root";
    private $password = "";
    public $conn;

    // Get PDO connection
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            die("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }

    // Get mysqli connection
    public function connectMysqli() {
        try {
            $mysqli = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($mysqli->connect_error) {
                throw new Exception("Connection failed: " . $mysqli->connect_error);
            }
            return $mysqli;
        } catch(Exception $exception) {
            die("Connection error: " . $exception->getMessage());
        }
    }
}

// Create global database connection instances
function db_connect() {
    $database = new Database();
    return $database->connectMysqli(); // Return mysqli connection for existing code
}

function db_connect_pdo() {
    $database = new Database();
    return $database->connect(); // Return PDO connection when needed
}
?>
