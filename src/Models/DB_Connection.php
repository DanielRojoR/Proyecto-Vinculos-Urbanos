<?php

// Esta clase maneja las conexiones a la base de datos usando un patron singleton para evitar que haya mas de una instancia de la conexion, esto 
// para prevenir leaks de memoria o de recursos 

class DB
{
    private $host;
    private $user;
    private $pass;
    private $dbName;

    private static $instance;

    private $connection;
    private $results;
    private $numRows; // Opcional ya que se puede obtener del mismo resultado

    private function __construct()
    {      
        $this->host = getenv('DB_HOST');
        $this->user = getenv('DB_USERNAME');
        $this->pass = getenv('DB_PASSWORD');
        $this->dbName = getenv('DB_NAME');
    }

    // singleton pattern
    static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function connect() {
        try {
            $this->connection = mysqli_connect($this->host,
                                            $this->user,
                                            $this->pass,
                                            $this->dbName);
            if (!$this->connection) {
                throw new Exception(mysqli_connect_error());
            }
        } 
        
        catch (Exception $e) {
            // Se maneja y loggea el error
            error_log("Database connection failed: " . $e->getMessage());
            return false;
        }
        return true;
    }
    public function isConnected() {
        return $this->connection !== null && mysqli_ping($this->connection);
    }

    public function __destruct() {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }
}