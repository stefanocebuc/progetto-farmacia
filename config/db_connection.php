<?php
define('HOST', 'localhost');
define('DB', 'farmacia');
define('USER', 'root');
define('PASSW', '');


try {
    $conn = new PDO('mysql:host=' . HOST . ';dbname=' . DB. '', USER, PASSW);
} 
catch (PDOException $e) {
    echo "Connessione NON andata a buon fine" . $e;
}
?>