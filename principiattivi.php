<?php
session_start();

require_once('config/db_connection.php');
include('config/get_page.php');

if (!isset($_SESSION['session_id'])) {
    header('Location: signup.php');
    exit;
}


?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="d-flex h-100 text-center text-white bg-dark">
    <section class="container d-flex w-200 h-100 p-3 mx-auto flex-column">
        <?php include('template/header.php'); ?>



        <?php include('template/footer.php'); ?>
    </section>

</body>

</html>