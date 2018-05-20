<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 11/05/18
 * Time: 20:01
 */

require '../Helper/StringHelper/StringHelper.php';

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

    //Funzione per recuperare la lista degli utenti presenti del DB (Andrea)
    public function testGetStudenti()
    {
        $utenti = array(); //risultato: array bidimensionale
        $table = $this->tabelleDB[7]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2"
            "SELECT " . $campi[0] . ", " . $campi[1] . ", " .
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

    //Funzione per recuperare la lista degli utenti presenti del DB (Andrea)
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

    //Funzione di accesso (Andrea)
    public function login($email, $password)
    {
        $table1 = $this->tabelleDB[7]; //Tabella per la query
        $table2 = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table1];
        /*  query: "SELECT matricola, nome, cognome, email, 'studente' as tabella FROM studente WHERE email = ? AND password = ?
                    UNION
                    SELECT matricola, nome, cognome, email, 'docente' as tabella FROM docente WHERE email = ? AND password = ?" */
        $query =
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            "'" . $table1 . "' as tabella " .
            "FROM " .
            $table1 . " " .
            "WHERE " .
            $campi[3] . " = ? AND " .
            $campi[4] . " = ? " .
            "UNION " .
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            "'" . $table2 . "' as tabella " .
            "FROM " .
            $table2 . " " .
            "WHERE " .
            $campi[3] . " = ? AND " .
            $campi[4] . " = ?";

        $stmt = $this->connection->prepare($query);
        //$stmt->bind_param("ss", $email, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->bind_param("ssss", $email, $password, $email, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($matricola, $nome, $cognome, $email, $table);
            $utente = array(); //risultato: array
            //Indicizzo con key i dati nell'array

            while ($stmt->fetch()) {
                $temp = array();
                $temp[$campi[0]] = $matricola;
                $temp[$campi[1]] = $nome;
                $temp[$campi[2]] = $cognome;
                $temp[$campi[3]] = $email;
                $temp["tabella"] = $table;
                array_push($utente, $temp);
            }
            //Controllo se ha trovato matching tra dati inseriti e campi del db
            return $utente;
        } else {
            return null;
        }

    }

    //Funzione di recupero (Danilo)
    public function recover($email)
    {
        $table1 = $this->tabelleDB[7]; //Tabella per la query
        $table2 = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table1];

        /*  query: "SELECT email FROM studente WHERE email = ?
                    UNION
                    SELECT email FROM docente WHERE email = ?" */
        $query =
            "SELECT " .
            $campi[3] . " " .
            "FROM " .
            $table1 . " " .
            "WHERE " .
            $campi[3] . " = ? " .
            "UNION " .
            "SELECT " .
            $campi[3] . " " .
            "FROM " .
            $table2 . " " .
            "WHERE " .
            $campi[3] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    // Funzione Modifica Profilo (Gigi) //Fnuzionante
    public function updateProfile($matricola, $nome, $cognome, $password, $tabella)
    {
        $table = $this->tabelleDB[$tabella];
        $campi = $this->campiTabelleDB[$table];
        $query = //query:  "UPDATE TABLE SET nome = ?, cognome = ?, password = ? WHERE matricola = ?"
            "UPDATE " .
            $table . " " .
            "SET " .
            $campi[1] . " = ?, " .
            $campi[2] . " = ?, " .
            $campi[4] . " = ? " .
            "WHERE " .
            $campi[0] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssss", $nome, $cognome, $password, $matricola); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $result = true;
        try {
            $stmt->execute();
            $stmt->store_result();
        } catch (Exception $exception) {
            $result = false;
        }
        //Controllo se ha trovato matching tra dati inseriti e capi del db
        return $result;
    }

    // Funzione Modifica Password (Andrea)
    public function updatePassword($email, $password)
    {
        //Controllare il discorso del cds, va discusso sul come fare

        $stringHelper = new StringHelper();
        $substr = $stringHelper->subString($email);
        $tabella = $this->tabelleDB[7];
        $stmt = null;
        if ($substr == "unimol") {
            $tabella = $this->tabelleDB[3];
        }

        $campi = $this->campiTabelleDB[$tabella];
        $query = //query:  "UPDATE TABLE SET password = ? WHERE email = ?"
            "UPDATE " .
            $tabella . " " .
            "SET " .
            $campi[4] . " = ? " .
            "WHERE " .
            $campi[3] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $password, $email); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $result = true;
        try {
            $stmt->execute();
            $stmt->store_result();
        } catch (Exception $exception) {
            $result = false;
        }
        //Controllo se ha trovato matching tra dati inseriti e capi del db
        return $result;
    }

    // Funzione registrazione (Francesco) dovrebbe essere funzionante...non sono certo per quanto riguarda i non studenti che forse ritorna sempre falso
    public function registration($matricola, $nome, $cognome, $email, $password)
    {

        //Controllare il discorso del cds, va discusso sul come fare

        $stringHelper = new StringHelper();
        $substr = $stringHelper->subString($email);
        $table = $this->tabelleDB[7];
        $campi = $this->campiTabelleDB[$table];
        $stmt = null;

        if ($substr == "studenti") {
            $cds = 1;
            $query = //query: "INSERT INTO TABLE (matricola, nome, cognome, email, password, cod_cds) VALUES (?,?,?,?,?,?)"
                //INSERT INTO studente (matricola, nome, cognome, email, password, cod_cds) VALUES ('155975', 'Andrea', 'Petrella', 'a.petrella@studenti.unimol.it', 123456, '1')
                "INSERT INTO " .
                $table . " (" .
                $campi[0] . ", " .
                $campi[1] . ", " .
                $campi[2] . ", " .
                $campi[3] . ", " .
                $campi[4] . ", " .
                $campi[5] . ") " .

                "VALUES (?,?,?,?,?,?)";

            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("issssi", $matricola, $nome, $cognome, $email, $password, $cds); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)

            $result = ($stmt->execute()) ? 1 : 2;

        } else if ($substr == "unimol"){
            $table = $this->tabelleDB[3];
            $query = //query: "INSERT INTO TABLE (matricola, nome, cognome, email, password) VALUES (?,?,?,?,?)"
                "INSERT INTO " .
                $table . " (" .
                $campi[0] . ", " .
                $campi[1] . ", " .
                $campi[2] . ", " .
                $campi[3] . ", " .
                $campi[4] . ") " .

                "VALUES (?,?,?,?,?)";

            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("issss", $matricola, $nome, $cognome, $email, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)

            $result = ($stmt->execute()) ? 1 : 2;
        }
        else {
            $result = 0;
        }

        /* OK per debug ma a regime vanno tolte queste righe di codice altrimenti ci manda in crash l'applicativo
        if ($result == 2) {
            throw new Exception($stmt->error);
        }
        */

        return $result;
    }

    //------------ OK ------------

    /**** COMMENTO DI ANDREA: FORSE NON SERVE NEL NOSTRO PROGETTO ****/
    //Funzione che restituisce il tipo attore in base al suo id (serve per la specializzazione degli utenti)
    public function getTypeByIdAttore($idattore)
    {
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        // N.B. Probabilmente effettuando una query più accurata si può migliorare la logica che serve a filtrare i dati
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2 WHERE idattore = ?"
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

    //------------ NUOVI DA CONTROLLARE --------------
//gigi
    public function visualizzaDocumento($idDocumento)
    {
        $documento = array();

        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT id, titolo, cod_docente, cod_studente, cod_materia, link FROM documento WHERE id = ?"
            "SELECT " .
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

//Funzionante michela
    public function visualizzaProfiloDocente($matricola)
    {
        $profilo = array();
        $table = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2 WHERE idattore = ? AND password = ?"
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            "FROM " .
            $table .
            " WHERE " .
            $campi[0] . " = ? ";
        $stmt = $this->connection->prepare($query);

        $stmt->bind_param("s", $matricola); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($nome, $cognome, $email);
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array
                $profilo[1] = $nome;
                $profilo[2] = "$cognome";
                $profilo[3] = $email;

                //array_push($profilo, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
            }
            //Controllo se ha trovato matching tra dati inseriti e campi del db
            return $profilo;
        } else return null;
    }

//funzione per visualizzare il profilo studenti FUNZIONANTE michela e danilo
    public function VisualizzaProfiloStudente($matricola)
    {
        $profilo = array();
        $table = $this->tabelleDB[7]; //Tabella per la query
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

        $stmt->bind_param("i", $matricola);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($nome, $cognome, $email);
        if ($stmt->num_rows > 0) {
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array
                $profilo[1] = $nome;
                $profilo[2] = $cognome;
                $profilo[3] = $email;

                //array_push($profilo, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
            }
            //Controllo se ha trovato matching tra dati inseriti e campi del db
            return $profilo;
        } else return null;
//Controllo se ha trovato matching tra dati inseriti e campi del db

    }
//jonathan
    public function caricaDocumento($titolo, $cod_docente, $cod_studente, $cod_materia, $link)
    {
        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];

        $query = //query: "INSERT INTO documento (id, titolo, cod_docente, cod_studente, cod_materia,link) VALUES (?,?,?,?,?)"
            "INSERT INTO  " .
            $table . " ( " .

            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[5] . " ) " .

            "VALUES (?,?,?,?,?)";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssis", $titolo, $cod_docente, $cod_studente, $cod_materia, $link);
        $result = $stmt->execute();

        return $result;
    }

    //Funzione per scaricare un documento (Andrea)
    public function downloadDocumento($id)
    {
        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        /*  query: "SELECT link FROM documento WHERE id = ?" */
        $query =
            "SELECT " .
            $campi[5] . " " .
            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[0] . " = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $id);

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($link);
            return $link;
        } else {
            return null;
        }
    }

    // domenico e jo
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
        return $stmt->num_rows > 0;
    }

    //domenico e jo
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
        return $stmt->num_rows > 0;
    }
//danilo
    public function visualizzaDocumentoPerMateria($Materia)
    {
        $documento = array();

        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $table2 = $this->tabelleDB[6];
        $campi2 = $this->campiTabelleDB[$table2];
        $query = //query: "SELECT id=0, titolo=1, cod_docente=2, cod materia=5,link=6, id_materia=0, FROM documento inner join materie on codmateria = id materia"
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[5] . ", " .
            $campi[6] . ", " .

            "FROM " .
            $table . ", " .
            $table2 . " " .
            "WHERE" . $campi2[2] . '= ? ' .
            "AND " .
            $campi[0] . " = " .
            $campi2[0];

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $Materia);
        $stmt->execute();
        $stmt->store_result();

        //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp
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
//danilo
    public function visualizzaDocumentoPerDocente($nomeDocente)
    {
        $documento = array();

        $table = $this->tabelleDB[4]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $table2 = $this->tabelleDB[3];
        $campi2 = $this->campitabelleDB[$table2];
        $query = //query: "SELECT id=0, titolo=1, cod_docente=2, cod materia=5,link=6, id_materia=0, FROM documento inner join materie on codmateria = id materia"
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[5] . ", " .
            $campi[6] . ", " .

            "FROM " .
            $table . ", " .
            $table2 . " " .
            "WHERE" . $campi2[1] . '= ? ' .
            "AND " .
            $campi[2] . " = " .
            $campi2[0];

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $nomeDocente);
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

    //danilo da controllare
    public function visualizzaMateriaPerCdl($cdlid)
    {
        $materie = array();
        $table = $this->tabelleDB[6]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT nome, FROM materia where cod_cdl=?"
            "SELECT " .
            $campi[1] . " " .
            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[0] . ' = ? ';
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $cdlid);
        $stmt->execute();
        $stmt->store_result();

//Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
        $stmt->bind_result($nome_materia);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
//Indicizzo con key i dati nell'array
            $temp[$campi[1]] = $materie;

            array_push($materie, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
        }
        return $materie; //ritorno array Documento riempito con i risultati della query effettuata.
    }

//---------------------------
    public function testGetMateria()
    {
        $materie = array(); //risultato: array bidimensionale
        $table = $this->tabelleDB[6]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2"
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            "FROM " .
            $table;

        $stmt = $this->connection->prepare($query); //Preparo la query
        $stmt->execute();//Esegue la query
        //Salvo il risultato della query in alcune variabili
        $stmt->bind_result($campi[1], $campi[2], $campi[3]);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array

            $temp[$campi[1]] = $campi[1];
            $temp[$campi[2]] = $campi[2];
            $temp[$campi[3]] = $campi[3];

            array_push($materie, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
        }
        return $materie;
    }

    public function testInsertMateria($id, $nome, $cod_doc, $cdl)
    {

        $table = $this->tabelleDB[6]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $stmt = null;
        $query = //query:
            "INSERT INTO  " .
            $table . " SET " .
            $campi[0] . " =? ," .
            $campi[1] . "=? , " .
            $campi[2] . "=? , " .
            $campi[3] . "=? ";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("isii", $id, $nome, $cod_doc, $cdl); //Preparo la query

        $result = $stmt->execute();//Esegue la query
        if (!$result) {
            throw new Exception($stmt->error);
        }
        return $result;
    }
    //------------------------------------------------- sopra Danilo
    //jonathan e danilo
    public function caricaAnnuncio($titolo, $contatto, $prezzo, $edizione, $casa_editrice, $cod_studente, $autori, $cod_materia, $link)
    {
        $table = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];

        $query = //query: "INSERT INTO annuncio (id, titolo, cod_docente, cod_studente, cod_materia,link) VALUES (?,?,?,?,?)"
            "INSERT INTO  " .
            $table . " ( " .
// Non setto l'ID dell'annuncio perchè è AUTO_INCREMENTALE, si setta in automatico
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[5] . ", " .
            $campi[6] . ", " .
            $campi[7] . ", " .
            $campi[8] . " ) " .
            "VALUES (?,?,?,?,?,?,?)";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssssisi", $titolo, $contatto, $prezzo, $edizione, $casa_editrice, $cod_studente, $autori, $cod_materia, $link);
        $result = $stmt->execute();

        return $result;
    }

    //domenico, da controllare
    public function contattaVenditore($idAnnuncio)
    {
        $table1 = $this->tabelleDB[1]; //Tabella per la query (annuncio)
        $table2 = $this->tabelleDB[7]; //Tabella per la query (studente): per ricavare l'email
        $campi1 = $this->campiTabelleDB[$table1];
        $campi2 = $this->campiTabelleDB[$table2];

        /*  query: "SELECT annuncio.contatto, studente.email
                    FROM studente, annuncio
                    WHERE annuncio.id = ? AND annuncio.cod_stud = studente.matricola*/
        $query =
            "SELECT " .
            $table1 . "." . $campi1[2] . ", " . $table2 . "." . $campi2[3] . " " .
            "FROM " .
            $table1 . ", " . $table2 . " " .
            "WHERE " .
            $table1 . "." . $campi1[0] . " = ? " .
            "AND " . $table1 . "." . $campi1[6] . " = " . $table2 . "." . $campi2[0];

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $idAnnuncio);
        $stmt->execute();
        $stmt->store_result();

        //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
        $stmt->bind_result($contatto, $email);

        if ($stmt->num_rows > 0) {
            while ($stmt->fetch()) { // Scansiono la risposta della query
                // Indicizzo i dati nell'array
                $venditore[1] = $contatto;
                $venditore[2] = $email;
            }
            return $venditore; //ritorno array Documento riempito con i risultati della query effettuata.
        } else return null;
    }

    //Funzione per la valutazione dei documenti (Andrea)
    public function valutazioneDocumento($valutaizone, $cod_documento)
    {
        $table = $this->tabelleDB[8]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];

        $query = //query: "INSERT INTO valutazione (valutazione, cod_documento) VALUES (?,?)"
            "INSERT INTO  " .
            $table . " ( " .
// Non setto l'ID dell'annuncio perchè è AUTO_INCREMENTALE, si setta in automatico
            $campi[1] . ", " .
            $campi[2] . " ) " .
            "VALUES (?,?)";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $valutaizone, $cod_documento);
        $result = $stmt->execute();

        return $result;
    }


}

?>