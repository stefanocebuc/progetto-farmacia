<?php
session_start();

if (!isset($_SESSION['session_id'])) {
    header('Location: signup.php');
    exit;
}

require_once('config/db_connection.php');
include('config/get_page.php');

$farmaci_info = "SELECT `id`,
                        `codice_minsan`,
                        `denominazione`,
                        `data_scadenza`,
                        `prezzo`
                         FROM farmaci 
                         ORDER BY data_scadenza DESC";

$farmaci = $conn->query($farmaci_info);
// per ottenere elenco farmaci

$get_farmaci = $farmaci->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="d-flex h-100 text-center text-white bg-dark">

    <section class="container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <?php include('template/header.php'); ?>

        <main class="px-3">
            <h2>Elenco farmaci</h2>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>MinSan</th>
                        <th>Denominazione</th>
                        <th>Data Scadenza &nbsp;</th>
                        <th>Prezzo</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ($get_farmaci) {
                        foreach ($get_farmaci as $farmaco) {
                    ?>
                            <tr>
                                <td><?= $farmaco['codice_minsan']; ?> &nbsp;</td>
                                <td><?= $farmaco['denominazione']; ?></td>
                                <td><?= date('d/m/Y', strtotime($farmaco['data_scadenza'])); ?></td>
                                <td><?= $farmaco['prezzo']; ?></td>
                                <td> &nbsp;<a class="me-2" title="view" href="farmaco.php?action=view&id=<?= $farmaco['id']; ?>"><i class="bi bi-eye-fill"></i></a>
                                    <a class="me-2" title="update" href="farmaco.php?action=update&id=<?= $farmaco['id']; ?>"><i class="bi bi-pencil"></i></a>
                                    <a class="me-2" title="delete" href="farmaco.php?action=delete&id=<?= $farmaco['id']; ?>"><i class="bi bi-x-circle"></i></a>
                                </td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </main>

        <?php include('template/footer.php'); ?>
    </section>
</body>

</html>