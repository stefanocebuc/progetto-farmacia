<?php

/* se l'utente è loggato = home */
if (isset($_SESSION['session_id'])) {
    header('Location: index.php');
    exit;
}

include('config/get_page.php');
require_once('config/db_connection.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-up to Farmacia Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="vh-100 p-1 text-white bg-dark">
    <section class="container d-flex w-200 p-3 mx-auto flex-column">
        <?php include('template/header.php'); ?>
        <main class="container d-flex justify-content-center row mt-5">
            <h1 class="text-center mt-3">Registrati prima di accedere alla Farmacia Online 😉</h1>
            <div class="text-center col-5 p-3">
                <form method="post">
                    <div class="form-group mb-2">
                        <label for="Name">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Inserisci username">
                    </div>
                    <div class="form-group mb-2">
                        <label for="Name">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Inserisci password">
                    </div>
                    <div class="form-group mb-2">
                        <label for="Name">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Conferma password">
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Register" class="btn btn-primary">
                        <input type="reset" value="Reset" class="btn btn-secondary ml-2">
                    </div>
                    <p>Sei già registrato? <a href="login.php" style="text-decoration: none;">Vai alla pagina di login</a></p>
                    <?php
                    //controlli input
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        $username = trim($_POST['username'] ?? '');
                        $password = trim($_POST['password'] ?? '');
                        $confirm_password = trim($_POST['confirm_password'] ?? '');
                        $message = '';

                        // Controllo se i campi sono vuoti
                        if (empty($username) || empty($password) || empty($confirm_password)) {
                            $message = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Verifica se hai compilato correttamente tutti i campi!</p>";
                        } elseif ($password !== $confirm_password) {
                            $message = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Le password non coincidono!</p>";
                        } else {
                            $passw_length = mb_strlen($password);

                            if ($passw_length < 8 || $passw_length > 20) {
                                $message = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">La password deve essere tra 8 e 20 caratteri!</p>";
                            } else {
                                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                                // Controllo se l'username esiste già
                                $query = "SELECT id FROM utenti WHERE username = :username";
                                $check = $conn->prepare($query);
                                $check->bindParam(':username', $username, PDO::PARAM_STR);
                                $check->execute();

                                if ($check->rowCount() > 0) {
                                    $message = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Username già in uso 😭</p>";
                                } else {
                                    // Inserisco il nuovo utente
                                    $query = "INSERT INTO utenti (username, password) VALUES (:username, :password)";
                                    $check = $conn->prepare($query);
                                    $check->bindParam(':username', $username, PDO::PARAM_STR);
                                    $check->bindParam(':password', $password_hash, PDO::PARAM_STR);

                                    if ($check->execute()) {
                                        $message = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Registrazione eseguita con successo!😊</p>";
                                    } else {
                                        $message = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Registrazione NON avvenuta, riprovare!</p>";
                                    }
                                }
                            }
                        }

                        echo $message;
                        unset($conn);
                    }
                    ?>
            </div>
        </main>

        <?php include('template/footer.php'); ?>
    </section>

</body>

</html>