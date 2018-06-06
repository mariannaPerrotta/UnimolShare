<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 11/05/18
 * Time: 20:01
 */

require '../Helper/StringHelper/StringHelper.php';

class DBUtenti
{
    //Variabili di classe
    private $connection;
    private $tabelleDB = [ //Array di tabelle del db
        "annuncio",
        "cdl",
        "docente",
        "documento",
        "libro",
        "materia",
        "studente",
        "valutazione",
        "cdl_doc"
    ];
    private $campiTabelleDB = [ //Campi delle tabelle (array bidimensionale indicizzato con key)
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
            "password",
            "attivo"
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
            "attivo",
            "cod_cds"
        ],
        "valutazione" => [
            "id",
            "valutazione",
            "cod_documento"
        ],
        "cdl_doc" =>[
            "id",
            "cod_doc"
        ]
    ];

    //Costruttore
    public function __construct()
    {
        //Setup della connessione col DB
        $db = new DBConnectionManager();
        $this->connection = $db->runConnection();
    }

    //---- METODI PER GESTIRE LE QUERY ----

    //Funzione di accesso (Andrea)
    public function login($email, $password)
    {
        $studenteTab = $this->tabelleDB[6];
        $docenteTab = $this->tabelleDB[2];
        $campi = $this->campiTabelleDB[$studenteTab];
        $attivo = 1;
        /*  query: "SELECT matricola, nome, cognome, email, 'studente' as tabella FROM studente WHERE email = ? AND password = ? AND attivo = 1
                    UNION
                    SELECT matricola, nome, cognome, email, 'docente' as tabella FROM docente WHERE email = ? AND password = ? AND attivo = 1" */
        $query = (
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            "'" . $studenteTab . "' as tabella " .
            "FROM " .
            $studenteTab . " " .
            "WHERE " .
            $campi[3] . " = ? AND " .
            $campi[4] . " = ? AND " .
            $campi[5] . " = ? " .
            "UNION " .
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            "'" . $docenteTab . "' as tabella " .
            "FROM " .
            $docenteTab . " " .
            "WHERE " .
            $campi[3] . " = ? AND " .
            $campi[4] . " = ? AND " .
            $campi[5] . " = ?"
        );

        //Invio la query
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssissi", $email, $password, $attivo, $email, $password, $attivo); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        //Ricevo la risposta del DB
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($matricola, $nome, $cognome, $email, $table);
            $utente = array();

            while ($stmt->fetch()) {
                $temp = array();
                $temp[$campi[0]] = $matricola;
                $temp[$campi[1]] = $nome;
                $temp[$campi[2]] = $cognome;
                $temp[$campi[3]] = $email;
                $temp["tabella"] = $table;
                array_push($utente, $temp);
            }

            return $utente;
        } else {
            return null;
        }

    }

    //danilo per visualizzare il corso di studio
    public function visualizzaCdlPerid($idcdl)
    {

        $table = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT id, nome FROM cdl"
            "SELECT " .

            $campi[1] . " " .
            "FROM " .
            $table." ".
            "WHERE ". $campi[0]." = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param(i , $idcdl);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($nome);

            $CDL = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array
                $temp[$campi[1]] = $nome;
                array_push($CDL, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $cdl
            }
            return $CDL;
        }else return null;
    }
    public function visualizzaCdl()
    {

        $table = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT id, nome FROM cdl"
            "SELECT " .
            $campi[0].", ".
            $campi[1]." ".
            "FROM " .
            $table;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id,$nome);

            $CDL = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array
                $temp[$campi[0]] = $id;
                $temp[$campi[1]] = $nome;
                array_push($CDL, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $cdl
            }
            return $CDL;
        }else return null;
    }

    //Funzione di recupero (Danilo)
    public function recupero($email)
    {
        $studenteTab = $this->tabelleDB[6]; //Tabella per la query
        $docenteTab = $this->tabelleDB[2]; //Tabella per la query
        $campi = $this->campiTabelleDB[$studenteTab];
        /*  query: "SELECT email FROM studente WHERE email = ?
                    UNION
                    SELECT email FROM docente WHERE email = ?" */
        $query = (
            "SELECT " .
            $campi[3] . " " .
            "FROM " .
            $studenteTab . " " .
            "WHERE " .
            $campi[3] . " = ? " .
            "UNION " .
            "SELECT " .
            $campi[3] . " " .
            "FROM " .
            $docenteTab . " " .
            "WHERE " .
            $campi[3] . " = ?"
        );
        //Invio la query
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        //Ricevo la risposta del DB
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    // Funzione conferma Profilo (Andrea)
    public function confermaProfilo($email, $matricola)
    {
        $stringHelper = new StringHelper();
        $substr = $stringHelper->subString($email);
        $tabella = $this->tabelleDB[6];
        if ($substr == "unimol") {
            $tabella = $this->tabelleDB[2];
        }
        $campi = $this->campiTabelleDB[$tabella];
        //query:  "UPDATE docente/studente SET attivo = true WHERE matricola = ?"
        $query = (
            "UPDATE " .
            $tabella . " " .
            "SET " .
            $campi[5] . " = 1 " .
            "WHERE " .
            $campi[0] . " = ?"
        );
        //Invio la query
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $matricola); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        return($stmt->execute());
    }

    // Funzione Modifica Profilo (Gigi)// da cambiare il ritotno ok
    public function modificaProfilo($matricola, $nome, $cognome, $password, $tab)
    {
        $tabella = $this->tabelleDB[$tab];
        $campi = $this->campiTabelleDB[$tabella];
        //query:  "UPDATE TABLE SET nome = ?, cognome = ?, password = ? WHERE matricola = ?"
        $query = (
            "UPDATE " .
            $tabella . " " .
            "SET " .
            $campi[1] . " = ?, " .
            $campi[2] . " = ?, " .
            $campi[4] . " = ? " .
            "WHERE " .
            $campi[0] . " = ?"
        );
        //Invio la query
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssss", $nome, $cognome, $password, $matricola); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)

        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->execute();
    }

    // Funzione Modifica Password (Andrea)
    public function modificaPassword($email, $password)
    {
        $stringHelper = new StringHelper();
        $substr = $stringHelper->subString($email);
        $tabella = $this->tabelleDB[6];
        if ($substr == "unimol") {
            $tabella = $this->tabelleDB[2];
        }
        $campi = $this->campiTabelleDB[$tabella];
        //query:  "UPDATE TABLE SET password = ? WHERE email = ?"
        $query = (
            "UPDATE " .
            $tabella . " " .
            "SET " .
            $campi[4] . " = ? " .
            "WHERE " .
            $campi[3] . " = ?"
        );
        //Invio la query
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $password, $email); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        return $stmt->execute();
    }

    // Funzione registrazione (Francesco)
    public function registrazione($matricola, $nome, $cognome, $email, $password, $cds)
    {
        $stringHelper = new StringHelper();
        $substr = $stringHelper->subString($email);
        $tabella = $this->tabelleDB[6];
        $campi = $this->campiTabelleDB[$tabella];
        $attivo = 0;

        if ($substr == "studenti") {
            //query: "INSERT INTO TABLE (matricola, nome, cognome, email, password, attivo, cod_cds) VALUES (?,?,?,?,?,0,?)"
            $query = (
                "INSERT INTO " .
                $tabella . " (" .
                $campi[0] . ", " .
                $campi[1] . ", " .
                $campi[2] . ", " .
                $campi[3] . ", " .
                $campi[4] . ", " .
                $campi[5] . ", " .
                $campi[6] . ") " .

                "VALUES (?,?,?,?,?,?,?)"
            );
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssssii", $matricola, $nome, $cognome, $email, $password, $attivo, $cds); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
            $result = ($stmt->execute()) ? 1 : 2;
        } else if ($substr == "unimol"){
            $tabella = $this->tabelleDB[2];
            //query: "INSERT INTO TABLE (matricola, nome, cognome, email, password, attivo) VALUES (?,?,?,?,?,0)"
            $query = (
                "INSERT INTO " .
                $tabella . " (" .
                $campi[0] . ", " .
                $campi[1] . ", " .
                $campi[2] . ", " .
                $campi[3] . ", " .
                $campi[4] . ", " .
                $campi[5] . ") " .

                "VALUES (?,?,?,?,?,?)"
            );
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssssi", $matricola, $nome, $cognome, $email, $password, $attivo); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
            $result = ($stmt->execute()) ? 1 : 2;
        } else {
            $result = 0;
        }
        return $result;
    }

    // Funzione visualizza documento (Gigi)
    public function visualizzaDocumento($idDocumento)
    {
        $tabelal = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabelal];
        //query: "SELECT id, titolo, cod_docente, cod_studente, cod_materia, link FROM documento WHERE id = ?"
        $query = (
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            $campi[4] . " " .
            $campi[5] . " " .
            "FROM " .
            $tabelal . " " .
            "WHERE " .
            $campi[0] . " = ?"
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idDocumento);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($idDocumento, $titolo, $cod_docente, $cod_studente, $cod_materia, $link);
            $documento = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
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
        } else {
            return null;
        }
    }

    //Funzione carica documento (Jonathan)
    public function caricaDocumento($titolo, $cod_docente, $cod_studente, $cod_materia, $link)
    {
        $tabella = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query: "INSERT INTO documento (id, titolo, cod_docente, cod_studente, cod_materia,link) VALUES (?,?,?,?,?)"
        $query = (
            "INSERT INTO  " .
            $tabella . " ( " .

            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[5] . " ) " .

            "VALUES (?,?,?,?,?)"
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssis", $titolo, $cod_docente, $cod_studente, $cod_materia, $link);
        return $stmt->execute();
    }


    //Funzione rimuovi documento (Domenico e Jonathan)
    public function rimuoviDocumento($idDocumento)
    {
        $tabella = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query:  " DELETE FROM DOCUMENTO WHERE ID = $idDocumento"
        $query = (
            "DELETE FROM " .
            $tabella . " WHERE " .
            $campi[0] . " = ? "
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idDocumento);
        if($stmt){
            $result=true;
        }else{
            $result=false;

        }
        $stmt->store_result();


        return $result;
    }

    //Funzione visualizza documento per id (Danilo)
    public function visualizzaDocumentoPerId($Matricola)
    {
        $tabella = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];

            //query= SELECT nome,link FROM documento WHERE cod_studente/cod_docente=$matricols
            $query = (
                "SELECT " .
                $campi[0].", ".
                $campi[1] . ", " .
                $campi[5] . " " .
                "FROM " .
                $tabella . " " .
                "WHERE " .
                $campi[3] . " = ? OR ". $campi[2] . " = ? "
            );

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $Matricola,$Matricola);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
            $stmt->bind_result($id,$titolo,$link);
            $documento= array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array
                $temp[$campi[0]] = $id;
                $temp[$campi[1]] = $titolo;
                $temp[$campi[5]] = $link;
                array_push($documento, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
            }
            return $documento; //ritorno array Documento riempito con i risultati della query effettuata.
        } else {
            return null;
        }
    }


}


?>