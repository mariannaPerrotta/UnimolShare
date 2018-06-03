<?php

require "DBQueryManager.php";

class DBDocenti
{
    //Variabili di classe
    private $connection;
    private $tabelleDB;
    private $campiTabelleDB;

    //Costruttore
    public function __construct()
    {
        //Setup della connessione col DB
        $db = new DBQueryManager();
        $queryManager = new DBQueryManager();

        $this->connection = $db->runConnection();
        $this->tabelleDB = $queryManager->getTabelleDB();
        $this->campiTabelleDB = $queryManager->getCampiTabelleDB();
    }

    //Funzionante visualizza profilo docente (Michela)
    public function visualizzaProfiloDocente($matricola)
    {
        $tabella = $this->tabelleDB[2];
        $campi = $this->campiTabelleDB[$tabella];
        //query: "SELECT nome, cognome, email FROM docente WHERE matricola = ?"
        $query = (
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            "FROM " .
            $tabella .
            " WHERE " .
            $campi[0] . " = ? "
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $matricola); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($nome, $cognome, $email);
            $profilo = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campi[1]] = $nome;
                $temp[$campi[2]] = "$cognome";
                $temp[$campi[3]] = $email;

                array_push($profilo, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $profilo
            }
            return $profilo;
        } else {
            return null;
        }
    }

    //Funzione visualizza libro per codice docente (Danilo)
    public function visualizzaLibroPerCodiceDocente($matricola)
    {
        $tabella = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query: "SELECT titolo,autore,casaeditrice,edizione,link FROM libri WHERE cod_docente = $matricola "
        $query = (
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[7] . " " .

            "FROM " .
            $tabella . " " .
            "WHERE " .
            $campi[6] . ' = ? '
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $Matricola);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($titolo, $autore, $casaeditrice, $edizione, $link);
            $libri = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campi[1]] = $titolo;
                $temp[$campi[2]] = $autore;
                $temp[$campi[3]] = $casaeditrice;
                $temp[$campi[4]] = $edizione;
                $temp[$campi[7]] = $link;
                array_push($libri, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
            }
            return $libri; //ritorno array libri riempito con i risultati della query effettuata.
        } else {
            return null;
        }
    }

//Funzione visualizza materia per cdl (Danilo)
    public function visualizzaMateriaPerCdl($cdlid)
    {
        $tabella = $this->tabelleDB[5]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        $query = //query: "SELECT nome, FROM materia WHERE cod_cdl = ? "
            "SELECT " .
            $campi[1] . " " .
            "FROM " .
            $tabella . " " .
            "WHERE " .
            $campi[0] . ' = ? ';
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $cdlid);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($nome_materia);
            $materie = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campi[1]] = $materie;
                array_push($materie, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $materie
            }
            return $materie; //ritorno array $materie riempito con i risultati della query effettuata.
        } else {
            return null;
        }
    }
}