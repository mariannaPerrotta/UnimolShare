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
        "attoriNew2", //E' per dei test verrà eliminato
        "annuncio",
        "cdl",
        "docente",
        "documento",
        "libro",
        "materia",
        "studente",
        "valutazione"
    ];

    //DA MODIFICARE ANCHE QUESTO IN BASE AL DB
    private $campiTabelleDB = [ //Ogni tabella ha i suoi campi e li salvo in un array bidimensionale indicizzato con key
        "attoriNew2" => [ //E' per dei test verrà eliminato
            "idattore",
            "tipo",
            "nome",
            "cognome",
            "password"
        ],
        "annuncio" => [
            "id",
            "titolo",
            "contatto",
            "prezzo",
            "edizione",
            "casa_editrice",
            "cod_stud",
            "autore",
            "cod_materia"
        ],
        "cdl" => [
            "id",
            "nome"
        ],
        "docente" => [
            "matricola",
            "nome",
            "cognome",
            "email",
            "password"
        ],
        "documento" => [
            "id",
            "titolo",
            "cod_docente",
            "cod_studente",
            "cod_materia",
            "link"
        ],
        "libro" => [
            "id",
            "titolo",
            "autore",
            "casa_editrice",
            "edizione",
            "cod_docente",
            "cod_materia",
            "link"
        ],
        "materia" => [
            "id",
            "nome",
            "cod_docente",
            "cod_cdl"
        ],
        "studente" => [
            "matricola",
            "nome",
            "cognome",
            "email",
            "password",
            "cod_cds"
        ],
        "valutazione" => [
            "id",
            "valutazione",
            "cod_documento"
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
    public function testGetStudenti()
    {
        $utenti = array(); //risultato: array bidimensionale
        $table = $this->tabelleDB[7]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2"
            "SELECT " .$campi[0] . ", " .$campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[5] . " " .
            "FROM " .
            $table;

        $stmt = $this->connection->prepare($query); //Preparo la query
        $stmt->execute();//Esegue la query
        //Salvo il risultato della query in alcune variabili
        $stmt->bind_result($codice, $nome, $cognome, $email, $psw, $cds);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $codice;
            $temp[$campi[1]] = $nome;
            $temp[$campi[2]] = $cognome;
            $temp[$campi[3]] = $email;
            $temp[$campi[4]] = $psw;
            $temp[$campi[5]] = $cds;
            array_push($utenti, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
        }
        return $utenti;
    }

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
    public function login($email, $password)
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
        $stmt->execute();
        $stmt->bind_param("ss", $email, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)

        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    //Funzione di recupero
    public function recover($email)
    {
        $table = $this->tabelleDB[7]; //Tabella per la query
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
            "INSERT INTO  " .
            $table." ( *".
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
    public function visualizzaDocumento($idDocumento)
    {
        $documento = array();

        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT id, titolo, cod_docente, cod_studente, cod_materia, link FROM documento WHERE id = ?"
            "SELECT " .    //avrei potuto ussare anche
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            $campi[4] . " " .
            $campi[5] . " " .
            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[0] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $idDocumento);
        $stmt->execute();
        $stmt->store_result();

        //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
        $stmt->bind_result($idDocumento, $titolo, $cod_docente, $cod_studente, $cod_materia, $link);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $idDocumento;
            $temp[$campi[1]] = $titolo;
            $temp[$campi[2]] = $cod_docente;
            $temp[$campi[3]] = $cod_studente;
            $temp[$campi[4]] = $cod_materia;
            $temp[$campi[5]] = $link;
            array_push($documento, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
        }
        return $documento; //ritorno array Documento riempito con i risultati della query effettuata.
    }

    public function visualizzaProfiloDocente($matricola )
    {
        $profilo = array(); //risultato: array bidimensionale
        $table = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query =//query di matricola nome cognome email
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .

            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[0] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $matricola);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($matricola,  $nome, $cognome, $email);
        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $matricola;
            $temp[$campi[1]] = $nome;
            $temp[$campi[2]] = $cognome;
            $temp[$campi[3]] = $email;

            array_push($profilo, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
        }
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $profilo ;
    }
//funzione per visualizzare il profilo studenti
    public function VisualizzaProfiloStudente($matricola)
    {
        $profilo = array();
        $table = $this->tabelleDB[8]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2 WHERE idattore = ? AND password = ?"
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[0] . " = ? ";


        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $matricola); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($matricola, $nome, $cognome, $email);
        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $matricola;
            $temp[$campi[1]] = $nome;
            $temp[$campi[2]] = $cognome;
            $temp[$campi[3]] = $email;

            array_push($profilo, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
        }
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $profilo;
//Controllo se ha trovato matching tra dati inseriti e campi del db

    }

    public function caricaDocumento($titolo, $cod_docente, $cod_studente, $cod_materia, $link)
    {
        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];

        $query = //query: "INSERT INTO documento (id, titolo, cod_docente, cod_studente, cod_materia,link) VALUES (?,?,?,?,?)"
            "INSERT INTO  " .
            $table." ( ".
            //$campi[0] .", ". Non setto l'ID del documento perchè è AUTO_INCREMENTALE, si setta in automatico
            $campi[1] .", ".
            $campi[2] .", ".
            $campi[3] .", ".
            $campi[4] .", ".
            $campi[5] ." ) ".

            "VALUES (?,?,?,?,?)" ;

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssss", $titolo, $cod_docente, $cod_studente, $cod_materia, $link);
        $result = $stmt->execute();

        return $result;
    }

    public function rimuoviDocumento($idDocumento)
    {
        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];

        $query = //query:  " DELETE FROM DOCUMENTO WHERE ID = $idDocumento"
            "DELETE FROM" .
            $table . "WHERE " .
            $campi[0] . " = ? ";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idDocumento);
        $stmt->execute();
        $stmt->store_result();
    }

    public function rimuoviAnnuncio($idAnnuncio)
    {
        $table = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];

        $query = //query:  " DELETE FROM ANNUNCIO WHERE ID = $idAnnuncio"
            "DELETE FROM" .
            $table . "WHERE " .
            $campi[0] . " = ? ";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idAnnuncio);
        $stmt->execute();
        $stmt->store_result();
    }
    public function visualizzaDocumentoPerMateria($Materia)
    {
        $documento = array();

        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $table2 = $this->tabelleDB[6];
        $campi2= $this->campitabelleDB[$table2];
        $query = //query: "SELECT id=0, titolo=1, cod_docente=2, cod materia=5,link=6, id_materia=0, FROM documento inner join materie on codmateria = id materia"
            "SELECT " .    //avrei potuto ussare anche
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[5] . ", " .
            $campi[6] . ", " .

            "FROM " .
            $table . ", ".
            $table2. " ".
            "WHERE". $campi2[2] .'= ? '.
            "AND ".
            $campi[0]. " = ".
            $campi2[0];

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $Materia);
        $stmt->execute();
        $stmt->store_result();

        //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
        $stmt->bind_result($idDocumento, $titolo, $cod_docente, $cod_materia, $link);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $idDocumento;
            $temp[$campi[1]] = $titolo;
            $temp[$campi[2]] = $cod_docente;

            $temp[$campi[5]] = $cod_materia;
            $temp[$campi[6]] = $link;
            array_push($documento, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
        }
        return $documento; //ritorno array Documento riempito con i risultati della query effettuata.
    }

}
?>