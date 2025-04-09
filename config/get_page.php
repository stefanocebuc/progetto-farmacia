<?php

$page = str_replace('/farmacia/', '', $_SERVER['PHP_SELF']); // ritorna nome del file .php 

switch ($page) { // switchiamo il file
    case 'index.php':
        $title = "Benvenuto utente";
        break;
    case 'farmaci.php':
        $title = "Elenco Farmaci";
        break;
    case 'login.php':
        $title = "Log-in Farmacia";
        break;
    case 'signup.php':
        $title = "Register";
        break;
    case 'principiattivi.php':
        $title = "Principi Attivi";
        break;
    case 'aziende.php':
        $title = "Aziende produttrici";
        break;
    default:
        "Pagina non esistente";
        break;
}
