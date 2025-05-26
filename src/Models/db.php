<!-- este archivo va a estar explicado en detalle para que se entienda que estamos haciendo -->

<?php
// cargamos el autoload del composer para poder acceder a las enviroment variables
require_once __DIR__ . '/../../vendor/autoload.php';

// primero cargamos el archivo env para que podamos usar las variables de ambiente (enviroment variables, investiguen que significa porfa) y poder asi de manera segura acceder a la db y a las apis
Dotenv\Dotenv::createImmutable(__DIR__ . '/../..')->safeLoad();

// creamos la clase database, que va a contener todas las funciones necesarias para conectar a la base de datos y hacer querys
class Database{

    //aca declaramos la variable para guardar la conexion a la base de datos, el signo '?' significa que la variable puede ser un objeto PDO o NULL (sirve para permitir objetos nulos)
    //la gracia de declararlo asi, es que la variable se mantenga nula hasta que se establezca una conexion a la db, hacerla privada permite encapsular la conexion a la db para mas seguridad
    private ?PDO $connection = null; 

    // Este array se usa para configurar conexiones y ajustes de base de datos más allá del DSN (el DSN siendo el metodo de conexion que utiliza PDO, este consiste de directivas de configuracion, 
    // ya que a diferencia de mysqli, permite el uso de sqlite entre otros), el nombre de usuario y la contraseña básicos. 
    // Es un arreglo de pares clave-valor que se pasa como el tercer argumento al constructor de PDO, lo que permite un comportamiento personalizado del controlador de base de datos.
    private array $options = [
        PDO::ATTR_TIMEOUT => 5, //timeout en minutos para la base de datos
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    // Constructor público para poder instancial la clase inicializada, al instanciarse genera una conexion automaticamente
    public function __construct() {
        $this->connect();
    }

    // Este metodo controla la conexiona a la base de datos de manera segura usando las enviroment variables
    private function connect(): void {
        try {

            // Configura la conexion usando un dsn adecuado para nuestra base de datos
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=utf8mb4",
                $_ENV['DB_HOST'],
                $_ENV['DB_NAME']
            );

            // Cambia el estado de la variable 'connection' para que ahora si contenga la instancia de la db y maneja cualquier potencial error
            $this->connection = new PDO(
                $dsn,
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD'],
                $this->options
            );
        } catch (PDOException $e) {
            throw new PDOException(
                "Conexión fallida: " . $e->getMessage(), 
                (int)$e->getCode()
            );
        }
    }

    // Este metodo, como dice el nombre, hace una consulta sql (todas las que usen SELECT) con prepared statements y retorna el resultado como un array
    // Ejemplo de consulta - $users = $db->query("SELECT * FROM users WHERE role = ?", ['admin']);
    public function query(string $sql, array $params = []): array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException(
                "Query fallida: " . $e->getMessage(), 
                (int)$e->getCode()
            );
        }
    }

    // muy similar a la funcion query pero con la gran diferencia de que retorna el numero de filas afectadas (y que sirve para todas las query con INSERT) y se usa de la siguiente manera
    // $affected = $db->execute("INSERT INTO users (name, email) VALUES (?, ?)", ['John', 'john@example.com']);
    public function execute(string $sql, array $params = []): int {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException(
                "Ejecución fallida: " . $e->getMessage(), 
                (int)$e->getCode()
            );
        }
    }

    // Esta funcion se describe sola
    public function isConnected(): bool {
        return $this->connection instanceof PDO;
    }
}