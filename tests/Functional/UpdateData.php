<?php
// Classe che gestisce la modifica di un profilo

// richiamo lo script responsabile della connessione a MySQL
require 'DBConnectionManager.php';

if($_POST && isset($_GET['id']))
{
    aggiorna_record();
}

function aggiorna_record()
{
// recupero i campi di tipo "stringa"
    $tipo = trim($_POST['tipo']);
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $password = trim($_POST['password']);

// preparo la query
$query = "UPDATE utenti SET
                tipo = '$tipo'
				nome = '$nome',
				cognome = '$cognome',
				password = '$password',
				WHERE id = $idattore";

// invio la query
$result = mysql_query($query);

// controllo l'esito
if (!$result) {
    die("Errore nella query $query: " . mysql_error());

// chiudo la connessione a MySQL
    mysql_close();

}


$messaggio = urlencode('Aggiornamento effettuato con successo');
header("location: $_SERVER[PHP_SELF]?msg=$messaggio");
}

?>