<?php
session_start();

/* se l'utente è loggato viene indirizzato alla home, altrimenti resta sulla pagina di registrazione */
if (isset($_SESSION['session_id']) && $_SESSION['session_id'] === true) {
    header('Location: index.php');
    exit();
} /* else {
    header('Location: signup.php');
    exit();
} */

require_once('config/db_connection.php');
include('config/get_page.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-in Farmacia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="vh-100 p-1 text-white bg-dark">
    <section class="container d-flex w-200 p-3 mx-auto flex-column">
        <?php include('template/header.php'); ?>
        <main class="d-flex justify-content-center row mt-5">
            <h1 class="text-center mt-4">Ci sei quasi... Effettua il Log in per accedere alla nostra farmacia 😊</h1>
            <div class="text-center col-3 p-3">
                <form method="post">
                    <div class="form-group mt-4">
                        <label for="user">Username</label>
                        <input type="text" name="username" id="user" class="form-control" placeholder="Inserire username">
                    </div>
                    <div class="form-group mt-4">
                        <label for="passw">Password</label>
                        <input type="password" name="password" id="passw" class="form-control" placeholder="Inserire password">
                    </div>
                    <div class="form-group mt-3">
                        <input type="submit" name="login" id="login" value="Login" class="btn btn-primary">
                    </div>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === "POST") {
                        // Estrazione e pulizia dei dati provenienti dal form
                        $username = trim($_POST['username'] ?? '');
                        $password = trim($_POST['password'] ?? '');

                        // Inizializzazione dei messaggi di errore
                        $username_err = $password_err = $login_err = '';

                        // Validazione dei campi
                        if (empty($username)) {
                            $username_err = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Inserire username valido!</p>";
                        }
                        if (empty($password)) {
                            $password_err = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Inserire password valida!</p>";
                        }

                        // Procedo se non ci sono errori di validazione
                        if (empty($username_err) && empty($password_err)) {
                            try {
                                // faccio la query
                                $query = "SELECT `username`, `password` FROM utenti WHERE username = ?";
                                $stmt = $conn->prepare($query);
                                // associo il parametro alla variabile
                                $stmt->bindValue(1, $username, PDO::PARAM_STR);
                                $stmt->execute();

                                // Recupero il risultato come array associativo
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Se l'utente esiste e la password è corretta
                                if ($user && password_verify($password, $user['password'])) {
                                    $_SESSION['session_id'] = true;
                                    $_SESSION['username'] = $user['username'];
                                    $_SESSION['password'] = $user['password'];
                                    header('Location: index.php');
                                    exit();
                                } else {
                                    $login_err = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Credenziali non valide, riprova!</p>";
                                }
                            } catch (PDOException $e) {
                                $login_err = "<p style=\"text-align: center;\" class=\"alert alert-warning mt-4\">Qualcosa è andato storto. Riprova più tardi!</p>";
                            }
                        }
                        echo $username_err;
                        echo $password_err;
                        echo $login_err;

                        //chiusura connessioni
                        unset($stmt);
                        unset($conn);
                    }
                    ?>
                </form>
            </div>
        </main>
        <?php include('template/footer.php'); ?>
    </section>
</body>

</html>