<?php
session_start();

if (!isset($_SESSION['session_id'])) {
    header('Location: signup.php');
    exit;
}

require_once('config/db_connection.php');
include('config/get_page.php');

$aziende_info = "SELECT *
                 FROM ditta ";
$aziende = $conn->query($aziende_info);
$get_aziende = $aziende->fetchAll(PDO::FETCH_ASSOC);

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
        <main class="px-3">
            <h2>Elenco dettagliato delle Aziende Produttrici</h2>
            <table class="table table-dark table-borderless">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ragione sociale</th>
                        <th>Indirizzo</th>
                        <th>Sito Web Azienda</th>
                    </tr>
                </thead>
                <hr>
                <tbody>
                    <?php
                    if ($get_aziende) {
                        foreach ($get_aziende as $azienda) {
                    ?>
                            <tr>
                                <td><?= $azienda['id']; ?> &nbsp;</td>
                                <td><?= $azienda['ragione_sociale']; ?></td>
                                <td><?= $azienda['indirizzo']; ?></td>
                                <td><a href="<?= $azienda['link_az']; ?>" class="text-white"><?= $azienda['link_az']; ?></a></td>
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