<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 11/05/18
 * Time: 20:01
 */

class DBQueryManager
{
    //Variabili di classe
    private $connection;

    //DA MODIFICARE IN BASE AL DB DEL SOTTOGRUPPO DI DORO
    private $tabelleDB = [ //Array di tabelle del db
        "attoriNew2"
    ];

    //DA MODIFICARE ANCHE QUESTO IN BASE AL DB
    private $campiTabelleDB = [ //Ogni tabella ha i suoi campi e li salvo in un array bidimensionale indicizzato con key
        "attoriNew2" => [
            "idattore",
            "tipo",
            "nome",
            "cognome",
            "password"
        ]
    ];

    //Costruttore
    public function __construct()
    {
        //Setup del DB
        $db = new DBConnectionManager();
        $this->connection = $db->runConnection();
    }

    /*********** FUNZIONE DI ESEMPIO ***********/
    //Funzione per recuperare la lista degli utenti presenti del DB
    public function getUtenti()
    {
        $utenti = array(); //risultato: array bidimensionale
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2"
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            "FROM " .
            $table;

        $stmt = $this->connection->prepare($query); //Preparo la query
        $stmt->execute();//Esegue la query
        //Salvo il risultato della query in alcune variabili
        $stmt->bind_result($idattore, $tipo, $nome, $cognome);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $idattore;
            $temp[$campi[1]] = $tipo;
            $temp[$campi[2]] = $nome;
            $temp[$campi[3]] = $cognome;
            array_push($utenti, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
        }
        return $utenti;
    }

    /*********** FUNZIONI DEL PROGETTO ***********/

    //Funzione di accesso
    public function login($idattore, $password)
    {
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2 WHERE idattore = ? AND password = ?"
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[0] . " = ? AND " .
            $campi[4] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $idattore, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    //Funzione di recupero
    public function recover($email)
    {
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query:  "SELECT email FROM attoriNew2 WHERE email = ?"
            "SELECT " .
            $campi[0] . ", " .
            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[0] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    // Funzione Modifica Profilo
    public function updateProfile($idattore, $nome, $cognome, $password)
    {
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query:  " UPDATE TABLE, SET CAMPI WHERE ID ATTORE"
            "UPDATE " .
            $table . ", " .
            "SET " .
            $campi[2] . " = ? , " .
            $campi[3] . " = ?, " .
            $campi[4] . " = ?, " .
            "WHERE " .
            $campi[0] . " = ? ";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssss", $nome, $cognome, $password, $idattore); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e capi del db
        return $stmt->num_rows > 0;
    }

    // Funzione registrazione
    public function registration($email, $tipo, $nome, $cognome, $password)
    {
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        // N.B. Probabilmente effettuando una query più accurata si può migliorare la logica che serve a filtrare i dati
        $query = //query: "INSERT INTO attoriNew2 (idattore, tipo, nome, cognome, password) VALUES (?,?,?,?,?)"
            "INSERT INTO " .
            $table." ( ".
            $campi[0] .", ".
            $campi[1] .", ".
            $campi[2] .", ".
            $campi[3] .", ".
            $campi[4] ." ) ".

            "VALUES (?,?,?,?,?)" ;

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssss", $email, $tipo, $nome, $cognome, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $result = $stmt->execute();

        return $result;
    }


    /**** COMMENTO DI ANDREA: FORSE NON SERVE NEL NOSTRO PROGETTO ****/
    //Funzione che restituisce il tipo attore in base al suo id (serve per la specializzazione degli utenti)
    public function getTypeByIdAttore($idattore)
    {
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        // N.B. Probabilmente effettuando una query più accurata si può migliorare la logica che serve a filtrare i dati
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2 WHERE idattore = ?"
            "SELECT ".
            $campi[0].", ".
            $campi[1].", ".
            $campi[2].", ".
            $campi[3]." ".
            "FROM ".
            $table." ".
            "WHERE ".
            $campi[0]." = ?"
        ;

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $idattore); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        //Salvo il risultato della query in alcune variabili
        $stmt->bind_result($idattore, $tipo, $nome, $cognome);
        $stmt->fetch();

        $tipoUtente = $tipo;
        /* N.B. posso passare anche tutti i restanti dati dell'attore utilizzando un array
         *
         * $utente['idattore'] = $idattore;
         * $utente['tipo'] = $tipo;
         * $utente['nome'] = $nome;
         * $utente['cognome'] = $cognome;
         * ecc...
         *
         * */
        return $tipoUtente;
    }


}

?>