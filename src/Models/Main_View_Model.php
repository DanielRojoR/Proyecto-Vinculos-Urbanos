<?php
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

// Esta version de almacenar el archivo env en la variable permite usarla en el scope del sistema, es decir, habilita la funcion getenv()
// para asi no tener que acceder directamente a traves de la variable global $_ENV que crea composer
$dotenv = Dotenv::createImmutable("../", null, true); // third param enables putenv()
// $dotenv = Dotenv::createImmutable("../");
$dotenv->load();

$databaseUser = $_ENV["DB_USERNAME"];  
$databasePassword = $_ENV["DB_PASSWORD"];

echo $databaseUser, $databasePassword;
?>

