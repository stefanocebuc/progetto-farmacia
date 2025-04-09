<?php
session_start();

if (!isset($_SESSION['session_id'])) {
    header('Location: signup.php');
    exit;
}

require_once('config/db_connection.php');
include('config/get_page.php');

$action = $_GET['action']; // in base all'action, scegliamo l'operazione crud da eseguire in base al valore del parametro 
$id = ($_GET['id']) ? $_GET['id'] : null;

//Viene eseguita una query per recuperare i farmaci dal database
$farmaci_inf = "SELECT  `codice_minsan`,
                        `denominazione`,
                        `data_scadenza`,
                        `prezzo`,
                        `descrizione`
                         FROM farmaci 
                         ORDER BY data_scadenza DESC";

// Il risultato viene poi memorizzato in un array associativo
$farmaci = $conn->query($farmaci_inf);
$get_info = $farmaci->fetchAll(PDO::FETCH_ASSOC);
$row = null;
switch ($action) {
    case 'view': // dettaglio del farmaco con action = view
        // titolo pagina
        $title = 'Dettaglio del farmaco:';
        // Viene eseguita una query che unisce più tabelle (farmaci, ditta e principio_attivo) per ottenere tutte le informazioni necessarie sul farmaco.
        $farmaco_info = "SELECT `codice_minsan`,
                        `denominazione`,
                        DATE_FORMAT(data_scadenza, '%d/%m/%Y') AS data_scadenza_format,
                        `prezzo`,
                        `descrizione`,
                        `id_ditta`,
                        farmaci.codice_atc,
                        principio_attivo.id AS id_principio_attivo,
                        ditta.ragione_sociale,
                        principio_attivo.principio_attivo
                         FROM `farmaci`
                         JOIN `ditta` ON farmaci.id_ditta = ditta.id
                         JOIN `principio_attivo` ON farmaci.codice_atc = principio_attivo.codice_atc
                         WHERE farmaci.id = $id";

        // vengono estratti i dati dal database e salvati su un array associativo
        $farmaco = $conn->query($farmaco_info);
        $get_farmaco = $farmaco->fetchAll(PDO::FETCH_ASSOC);
        break;

    //UPDATE DEL FARMACO
    case 'update':
        $title = 'Modifica farmaco';

        // ottengo la lista di ditte per l'option
        $ditte_info = "SELECT ditta.id, ragione_sociale
                       FROM ditta";
        $ditte_result = $conn->query($ditte_info);
        $ditte = $ditte_result->fetchAll(PDO::FETCH_ASSOC);

        //ottengo la lista dei principi attivi per l'option
        $principi_info = "SELECT codice_atc, principio_attivo
                        FROM principio_attivo";
        $principi_result = $conn->query($principi_info);
        $principi = $principi_result->fetchAll(PDO::FETCH_ASSOC);


        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($id) && !empty($_POST['id_farmaco'])) { // id prodotto + pulsante update (input-hidden)
                // get hidden id
                $id = trim($_POST['id_farmaco']);
                $codice_atc = $descrizione = $ditta = $prezzo = $data_scadenza =  $codice_minsan = '';
                $codice_minsan_err = $data_scadenza_err = $prezzo_err = $ditta_err = $descrizione_err = $codice_atc_err = '';

                // INIZIO CONTROLLI INPUT UPDATE
                //CODICE MINSAN
                $codice_minsan = trim($_POST['minsan']);
                if (empty($codice_minsan)) {
                    $codice_minsan_err = "Codice Min.San obbligatorio.";
                } elseif (!ctype_digit($codice_minsan)) {
                    $codice_minsan_err = "Scrivi un codice Min.San. valido!";
                } else {
                    $codice_minsan;
                }

                //DATA SCADENZA
                $data_scadenza = new DateTime($_POST['data_scadenza']);
                $data_gg = new DateTime();
                if ($data_scadenza < $data_gg) {
                    $data_scadenza_err = "Il prodotto è scaduto!";
                } else {
                    $data_scadenza;
                }

                //PREZZO
                $prezzo = trim($_POST['prezzo']);
                if (ctype_digit($prezzo) && floatval($prezzo) > 0) {
                    $prezzo_err = "";
                } else {
                    $prezzo_err = "Il prezzo deve essere un numero positivo.";
                }

                // DITTA
                $ditta = trim($_POST['ditta']);
                if (empty($ditta)) {
                    $ditta_err = "Devi selezionare un produttore fra quelli già esistenti.";
                }

                // DESCRIZIONE

                $descrizione = trim($_POST['descrizione']);
                if (empty($descrizione)) {
                    $descrizione_err = "La descrizione del prodotto non può essere vuota.";
                } elseif (strlen($descrizione) < 10) {
                    $descrizione_err = "La descrizione deve contenere almeno 10 caratteri";
                }

                // PRINCIPIO ATTIVO

                $codice_atc = trim($_POST['principio_attivo']);
                if (empty($codice_atc)) {
                    $codice_atc_err = "Il principio attivo non può essere vuoto.";
                } elseif (strlen($codice_atc) < 3) {
                    $codice_atc_err = "Il principio attivo deve contenere almeno 3 caratteri.";
                } elseif (!preg_match("/[a-zA-ZÀ-ÖØ-öø-ÿ\s'-].+/", $codice_atc)) {
                    $codice_atc_err =  "Il principio attivo contiene caratteri non validi.";
                } else {
                    $codice_atc;
                }
                //FINE CONTROLLO INPUT UPDATE


                if (empty($codice_minsan_err) && empty($data_scadenza_err) && empty($prezzo_err) && empty($ditta_err) && empty($descrizione_err) && empty($codice_atc_err)) {
                    //Query di update
                    $update_farmaco_info = "UPDATE farmaci SET 
                                    codice_minsan = :cod_minsan,
                                    data_scadenza = :data_scadenza,
                                    prezzo = :prezzo,
                                    id_ditta = :ditta,
                                    descrizione = :descrizione,
                                    codice_atc = :codice_atc
                                    WHERE id = :id";

                    if ($update_farmaco = $conn->prepare($update_farmaco_info)) {

                        //settare i parametri
                        $param_cod_minsan = $codice_minsan;
                        $param_data_scadenza = $data_scadenza->format('Y-m-d');
                        $param_prezzo = $prezzo;
                        $param_ditta = $ditta;
                        $param_descrizione = $descrizione;
                        $param_codice_atc = $codice_atc;
                        $param_id = $id;

                        //bind dei parametri
                        $update_farmaco->bindParam(':cod_minsan', $param_cod_minsan, PDO::PARAM_INT);
                        $update_farmaco->bindParam(':data_scadenza', $param_data_scadenza, PDO::PARAM_STR);
                        $update_farmaco->bindParam(':prezzo', $param_prezzo, PDO::PARAM_INT);
                        $update_farmaco->bindParam(':ditta', $param_ditta, PDO::PARAM_STR);
                        $update_farmaco->bindParam(':descrizione', $param_descrizione, PDO::PARAM_STR);
                        $update_farmaco->bindParam(':codice_atc', $param_codice_atc, PDO::PARAM_STR);
                        $update_farmaco->bindParam(':id', $id, PDO::PARAM_INT);

                        //eseguiamo il prepare statement 
                        if ($update_farmaco->execute()) {
                            echo "<p class=\"d-flex text-center\">Dati aggiornati correttamente: controlla il database.</p>";
                            //exit();
                        } else {
                            $errorInfo = $update_farmaco->errorInfo();
                            echo "Errore durante l'aggiornamento: " . $errorInfo[2];
                        }
                        if (isset($id) && !empty(trim($id))) {
                            // get hidden id
                            $id;

                            $farmaco_info = "SELECT *, ditta.ragione_sociale , principio_attivo.principio_attivo
                                            FROM farmaci AS f
                                            JOIN ditta ON f.id_ditta = ditta.id
                                            JOIN principio_attivo ON f.codice_atc = principio_attivo.codice_atc
                                            WHERE f.id = :id";
                            if ($farmaco = $conn->prepare($farmaco_info)) { // controlla che ci sia qualcosa dentro e se c'è facciamo il bind qui sotto
                                $farmaco->bindParam(':id', $param_id);

                                $param_id = $id;

                                if ($farmaco->execute()) {
                                    if ($farmaco->rowCount() == 1) { // controlla che la riga sia almeno una
                                        $row = $farmaco->fetch(PDO::FETCH_ASSOC);
                                    }
                                } else {
                                    $row = null;
                                    echo "Qualcosa è andato storto...";
                                }
                            }
                            unset($farmaco);
                            unset($conn);
                        }
                    }
                    //chiudiamo statement
                    unset($update_farmaco);
                }
                //chiudo connessione
                unset($conn);
            }
        } else {
            if (isset($id) && !empty(trim($id))) {
                // get hidden id
                $id;

                $farmaco_info = "SELECT *, ditta.ragione_sociale , principio_attivo.principio_attivo
                                FROM farmaci AS f
                                JOIN ditta ON f.id_ditta = ditta.id
                                JOIN principio_attivo ON f.codice_atc = principio_attivo.codice_atc
                                WHERE f.id = :id";
                if ($farmaco = $conn->prepare($farmaco_info)) { // controlla che ci sia qualcosa dentro e se c'è facciamo il bind qui sotto
                    $farmaco->bindParam(':id', $param_id);

                    $param_id = $id;

                    if ($farmaco->execute()) {
                        if ($farmaco->rowCount() == 1) { // controlla che la riga sia almeno una
                            $row = $farmaco->fetch(PDO::FETCH_ASSOC);
                        }
                    } else {
                        $row = null;
                        echo "Qualcosa è andato storto...";
                    }
                }
                unset($farmaco);
                unset($conn);
            }
        }
        break;
    // FINE UPDATE    

    //CASE DELETE
    case 'delete':  /* delete del farmaco con action = delete */
        /* //echo "cancella"; */
        $title = 'Eliminazione dati';

        if ($id && !empty($_POST['id'])) { /* // $id è del farmaco che abbiamo selezionato , il secondo 'id' è del form */


            $delete_info = "DELETE FROM `farmaci` WHERE id = :id"; // : /* -> ci consente di fare il bind di quel parametro */

            $delete = $conn->prepare($delete_info);
            $delete->bindParam(':id', $id, PDO::PARAM_INT);

            if ($delete->execute()) {
                echo 'Farmaco' . $id . ' eliminato correttamente';
                header('Location: farmaci.php');
                exit();
            } else {
                echo "Qualcosa è andato in f*ga!";
            }
        } else {
            if (!isset($action)) {
                echo "Manca il parametro 'action'";
            } elseif (!isset($id)) {
                echo "Manca il parametro 'id'";
            }
        }
        unset($delete);
        unset($conn);
        break;
}
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

        <main class="px-5">
            <h1><?= $title; ?></h1>

            <?php
            if ($action == 'delete') {
            ?>

                <form method="post">
                    <div class="alert alert-danger">
                        <p>Vuoi eliminare il farmaco selezionato?</p>
                        <input type="hidden" name="id" value="<?= trim($id); ?>">
                        <input type="submit" value="Si" class="btn btn-danger">
                        <a href="farmaci.php" class="btn btn-primary ml-2">No</a>
                    </div>
                </form>
            <?php } elseif ($action == 'view') {
            ?>
                <table class="table table-dark table-striped">
                    <h2><?= $get_farmaco[0]['denominazione']; ?></h2>
                    <thead>
                        <tr>
                            <th>MinSan</th>
                            <th>Data Scadenza &nbsp;</th>
                            <th>Prezzo</th>
                            <th>Ragione Sociale</th>
                            <th>Descrizione</th>
                            <th>Principio Attivo</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td><?= $get_farmaco[0]['codice_minsan']; ?> &nbsp;</td>
                            <td><?= $get_farmaco[0]['data_scadenza_format']; ?></td>
                            <td><?= $get_farmaco[0]['prezzo']; ?></td>
                            <td><a href="azienda-produttrice.php?id=<?= $get_farmaco[0]['id_ditta']; ?>"><?= $get_farmaco[0]['ragione_sociale']; ?></a></td>
                            <td><?= $get_farmaco[0]['descrizione']; ?></td>
                            <td><a href="principio-attivo.php?id=<?= $get_farmaco[0]['id_principio_attivo']; ?>"><?= $get_farmaco[0]['principio_attivo']; ?></a></td>
                        </tr>
                    </tbody>
                </table>
            <?php
            } elseif ($action == 'update') {
            ?>
                <h2 class="mb-5"><?= 'Titolo del farmaco'; ?></h2>
                <form method="post">
                    <table class="table table-dark table-striped-columns table-borderless">
                        <thead>
                            <tr>
                                <th>MinSan</th>
                                <td><input class="form-control <?= (!empty($codice_minsan_err)) ? 'is-invalid' : ''; ?>" name="minsan" type="text" value="<?= $row['codice_minsan']; ?>" placeholder="Modifica codice minsan">
                                    <span class="invalid-feedback"><?= $codice_minsan_err; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Data Scadenza</th>
                                <td><input class="form-control <?= (!empty($data_scadenza_err)) ? 'is-invalid' : ''; ?>" name="data_scadenza" type="date" value="<?= $row['data_scadenza']; ?>" placeholder="Modifica data scadenza"></td>
                            </tr>
                            <tr>
                                <th>Prezzo</th>
                                <td><input class="form-control <?= (!empty($prezzo_err)) ? 'is-invalid' : ''; ?>" name="prezzo" type="number" value="<?= $row['prezzo']; ?>" placeholder="Modifica prezzo"></td>
                            </tr>
                            <tr>
                                <th>Produttore</th>
                                <td>
                                    <select name="ditta" id="">
                                        <option value="">--seleziona--</option>
                                        <?php foreach ($ditte as $ditta) { ?>
                                            <option class="form-control <?= (!empty($ditta_err)) ? 'is-invalid' : ''; ?>" <?= ($row['id_ditta'] == $ditta['id']) ? 'selected' : ''; ?> value="<?= $ditta['id']; ?>"><?= $ditta['ragione_sociale']; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Descrizione</th>
                                <td><input class="form-control <?= (!empty($descrizione_err)) ? 'is-invalid' : ''; ?>" name="descrizione" type="text" value="<?= $row['descrizione']; ?>" placeholder="Modifica descrizione"></td>
                            </tr>
                            <tr>
                                <th>Principio Attivo</th>
                                <td>
                                    <select name="principio_attivo" id="">
                                        <option value="">--seleziona</option>
                                        <?php foreach ($principi as $principio) { ?>
                                            <option class="form-control <?= (!empty($codice_atc_err)) ? 'is-invalid' : ''; ?>" <?= ($row['codice_atc'] == $principio['codice_atc']) ? 'selected' : ''; ?> value="<?= $principio['codice_atc']; ?>"><?= $principio['principio_attivo']; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="hidden" value="<?= trim($id); ?>" name="id_farmaco"></td> <!-- ci gestisce l'id del form -->
                                <td><button class="btn btn-primary" type="submit">Update</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            <?php } ?>
        </main>

        <?php include('template/footer.php'); ?>
    </section>


</body>

</html>