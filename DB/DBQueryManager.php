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
    private $tabelleDB = [ //Array di tabelle del db
        "annuncio",
        "cdl",
        "docente",
        "documento",
        "libro",
        "materia",
        "studente",
        "valutazione"
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
        //Setup della connessione col DB
        $db = new DBConnectionManager();
        $this->connection = $db->runConnection();
    }

    //Metodi per effettuare le query

    //Funzione di accesso (Andrea)
    public function login($email, $password)
    {
        $studenteTab = $this->tabelleDB[6];
        $docenteTab = $this->tabelleDB[2];
        $campi = $this->campiTabelleDB[$studenteTab];
        /*  query: "SELECT matricola, nome, cognome, email, 'studente' as tabella FROM studente WHERE email = ? AND password = ?
                    UNION
                    SELECT matricola, nome, cognome, email, 'docente' as tabella FROM docente WHERE email = ? AND password = ?" */
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
            $campi[4] . " = ? " .
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
            $campi[4] . " = ?"
        );

        //Invio la query
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssss", $email, $password, $email, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
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
    //danilo
    public function VisualizzaCDL()
    {
        $CDL = array();
        $table = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query: "SELECT idattore, tipo, nome, cognome FROM attoriNew2 WHERE idattore = ? AND password = ?"
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            "FROM " .
            $table;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id,$nome);


        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
//Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $id;
            $temp[$campi[1]] = $nome;
            array_push($CDL, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
        }
        return $CDL;

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

    // Funzione Modifica Profilo (Gigi)
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
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
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
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    // Funzione registrazione (Francesco)
    public function registrazione($matricola, $nome, $cognome, $email, $password, $cds)
    {
        $stringHelper = new StringHelper();
        $substr = $stringHelper->subString($email);
        $tabella = $this->tabelleDB[6];
        $campi = $this->campiTabelleDB[$tabella];

        if ($substr == "studenti") {
            //query: "INSERT INTO TABLE (matricola, nome, cognome, email, password, cod_cds) VALUES (?,?,?,?,?,?)"
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
            $stmt->bind_param("sssssi", $matricola, $nome, $cognome, $email, $password, $cds); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
            $result = ($stmt->execute()) ? 1 : 2;
        } else if ($substr == "unimol"){
            $tabella = $this->tabelleDB[2];
            //query: "INSERT INTO TABLE (matricola, nome, cognome, email, password) VALUES (?,?,?,?,?)"
            $query = (
                "INSERT INTO " .
                $tabella . " (" .
                $campi[0] . ", " .
                $campi[1] . ", " .
                $campi[2] . ", " .
                $campi[3] . ", " .
                $campi[4] . ") " .

                "VALUES (?,?,?,?,?)"
            );
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssss", $matricola, $nome, $cognome, $email, $password); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
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
        $stmt->bind_param("i", $matricola); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
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

    //Funzione visualizzare profilo studente (Michela e Danilo)
    public function VisualizzaProfiloStudente($matricola)
    {
        $tabella = $this->tabelleDB[6]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query: "SELECT nome, cognome, email FROM studente WHERE matricola = ?"
        $query = (
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . " " .
            "FROM " .
            $tabella . " " .
            "WHERE " .
            $campi[0] . " = ? "
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $matricola);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($nome, $cognome, $email);
            $profilo = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campi[1]] = $nome;
                $temp[$campi[2]] = $cognome;
                $temp[$campi[3]] = $email;
                array_push($profilo, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $profilo
            }
            return $profilo;
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
        $stmt->bind_param("isssis", $titolo, $cod_docente, $cod_studente, $cod_materia, $link);
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    //Funzione per scaricare un documento (Andrea)
    public function downloadDocumento($id)
    {
        $tabella = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        /*  query: "SELECT link FROM documento WHERE id = ?" */
        $query = (
            "SELECT " .
            $campi[5] . " " .
            "FROM " .
            $tabella . " " .
            "WHERE " .
            $campi[0] . " = ?"
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($link);
            $url = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campi[0]] = $link;
                array_push($url, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $profilo
            }
            return $url;
        } else {
            return null;
        }
    }

    //Funzione rimuovi documento (Domenico e Jonathan)
    public function rimuoviDocumento($idDocumento)
    {
        $tabella = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query:  " DELETE FROM DOCUMENTO WHERE ID = $idDocumento"
        $query = (
            "DELETE FROM" .
            $tabella . "WHERE " .
            $campi[0] . " = ? "
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idDocumento);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    //Funzione rimuovi annuncio (Domenico e Jonathan)
    public function rimuoviAnnuncio($idAnnuncio)
    {
        $tabella = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query:  " DELETE FROM ANNUNCIO WHERE ID = $idAnnuncio"
        $query = (
            "DELETE FROM" .
            $tabella . "WHERE " .
            $campi[0] . " = ? "
        );

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idAnnuncio);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    //Funzione visualizza documento per materia (Danilo)
    public function visualizzaDocumentoPerMateria($Materia)
    {
        $documentiTab = $this->tabelleDB[3];
        $campiDocumento = $this->campiTabelleDB[$documentiTab];
        $materieTab = $this->tabelleDB[5];
        $campiMateria = $this->campiTabelleDB[$materieTab];
        //query: "SELECT id=0, titolo=1, cod_docente=2, cod materia=5,link=6, id_materia=0, FROM documento inner join materie on codmateria = id materia"
        $query = (
            "SELECT " .
            $campiDocumento[0] . ", " .
            $campiDocumento[1] . ", " .
            $campiDocumento[2] . ", " .
            $campiDocumento[5] . ", " .
            $campiDocumento[6] . ", " .

            "FROM " .
            $documentiTab . ", " .
            $materieTab . " " .
            "WHERE" . $campiMateria[1] . '= ? ' .
            "AND " .
            $campiDocumento[4] . " = " .
            $campiMateria[0]
        );

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $Materia);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($idDocumento, $titolo, $cod_docente, $cod_materia, $link);
            $documento = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campiDocumento[0]] = $idDocumento;
                $temp[$campiDocumento[1]] = $titolo;
                $temp[$campiDocumento[2]] = $cod_docente;
                $temp[$campiDocumento[5]] = $cod_materia;
                $temp[$campiDocumento[6]] = $link;
                array_push($documento, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
            }
            return $documento; //ritorno array $documento riempito con i risultati della query effettuata.
        } else {
            return null;
        }
    }

    //Funzione visualizza documento per docente (Danilo)
    public function visualizzaDocumentoPerDocente($nomeDocente)
    {
        $documentiTab = $this->tabelleDB[3]; //Tabella per la query
        $campiDocumento = $this->campiTabelleDB[$documentiTab];
        $docentiTab = $this->tabelleDB[2];
        $campiDocente = $this->campiTabelleDB[$docentiTab];
        $query = //query: "SELECT id=0, titolo=1, cod_docente=2, cod materia=5,link=6, id_materia=0, FROM documento inner join materie on codmateria = id materia"
            "SELECT " .
            $campiDocumento[0] . ", " .
            $campiDocumento[1] . ", " .
            $campiDocumento[2] . ", " .
            $campiDocumento[5] . ", " .
            $campiDocumento[6] . ", " .

            "FROM " .
            $documentiTab . ", " .
            $docentiTab . " " .
            "WHERE" . $campiDocente[1] . '= ? ' .
            "AND " .
            $campiDocumento[2] . " = " .
            $campiDocente[0];

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $nomeDocente);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($idDocumento, $titolo, $cod_docente, $cod_materia, $link);
            $documento = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campiDocumento[0]] = $idDocumento;
                $temp[$campiDocumento[1]] = $titolo;
                $temp[$campiDocumento[2]] = $cod_docente;
                $temp[$campiDocumento[5]] = $cod_materia;
                $temp[$campiDocumento[6]] = $link;
                array_push($documento, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
            }
            return $documento; //ritorno array $documento riempito con i risultati della query effettuata.
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

    //Funzione carica annuncio (Jonathan e Danilo)
    public function caricaAnnuncio($titolo, $contatto, $prezzo, $edizione, $casa_editrice, $cod_studente, $autori, $cod_materia, $link)
    {
        $tabella = $this->tabelleDB[0];
        $campi = $this->campiTabelleDB[$tabella];
        //query: "INSERT INTO annuncio (id, titolo, contatto, prezzo, edizione, casa_editrice, cod_studente, autori, cod_materia, link) VALUES (?,?,?,?,?,?,?,?)"
        $query = ("INSERT INTO  " .
            $tabella . " ( " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[5] . ", " .
            $campi[6] . ", " .
            $campi[7] . ", " .
            $campi[8] . " ) " .
            "VALUES (?,?,?,?,?,?,?)"
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssssisi", $titolo, $contatto, $prezzo, $edizione, $casa_editrice, $cod_studente, $autori, $cod_materia, $link);
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    //Funzione contatta venditore (Domenico)
    public function contattaVenditore($idAnnuncio)
    {
        $annunciTab = $this->tabelleDB[0]; //Tabella per la query (annuncio)
        $studentiTab = $this->tabelleDB[6]; //Tabella per la query (studente): per ricavare l'email
        $campiAnnuncio = $this->campiTabelleDB[$annunciTab];
        $campiStudente = $this->campiTabelleDB[$studentiTab];
        /*  query: "SELECT annuncio.contatto, studente.email
                    FROM studente, annuncio
                    WHERE annuncio.id = ? AND annuncio.cod_stud = studente.matricola*/
        $query = (
            "SELECT " .
            $annunciTab . "." . $campiAnnuncio[2] . ", " . $studentiTab . "." . $campiStudente[3] . " " .
            "FROM " .
            $annunciTab . ", " . $studentiTab . " " .
            "WHERE " .
            $annunciTab . "." . $campiAnnuncio[0] . " = ? " .
            "AND " . $annunciTab . "." . $campiAnnuncio[6] . " = " . $studentiTab . "." . $campiStudente[0]
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idAnnuncio);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($contatto, $email);
            $venditore = array();
            while ($stmt->fetch()) { // Scansiono la risposta della query
                $temp = array();
                $temp[$campiAnnuncio[2]] = $contatto;
                $temp[$campiStudente[3]] = $email;
                array_push($venditore, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $profilo
            }
            return $venditore; //ritorno array Documento riempito con i risultati della query effettuata.
        } else{
            return null;
        }
    }

    //Funzione valutazione documenti (Andrea)
    public function valutazioneDocumento($valutazione, $cod_documento)
    {
        $tabella = $this->tabelleDB[7]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query: "INSERT INTO valutazione (valutazione, cod_documento) VALUES (?,?)"
        $query = (
            "INSERT INTO  " .
            $tabella . " ( " .
            $campi[1] . ", " .
            $campi[2] . " ) " .
            "VALUES (?,?)"
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $valutazione, $cod_documento);
        $stmt->execute();
        $stmt->store_result();
        //Controllo se ha trovato matching tra dati inseriti e campi del db
        return $stmt->num_rows > 0;
    }

    //Funzione per ricercare tra documenti, libri e annunci (Andrea)
    public function ricerca($key)
    {
        $annunci = $this->tabelleDB[1];
        $docs = $this->tabelleDB[4];
        $libri = $this->tabelleDB[5];
        $campi = $this->campiTabelleDB[$annunci];
        /*query: "  SELECT id, titolo, tabella FROM annuncio WHERE titolo LIKE '%?%'
                    UNION
                    SELECT id, titolo, tabella FROM documento WHERE titolo LIKE '%?%'
                    UNION
                    SELECT id, titolo, tabella FROM libro WHERE titolo LIKE '%?%' */
        $query = (
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            "'" . $annunci . "' as tabella " .
            "FROM " .
            $annunci . " " .
            "WHERE " .
            $campi[1] . " LIKE"." '%".$key."%' " .
            "UNION " .
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            "'" . $docs . "' as tabella " .
            "FROM " .
            $docs . " " .
            "WHERE " .
            $campi[1] . " LIKE"." '%".$key."%' " .
            "UNION " .
            "SELECT " .
            $campi[0] . ", " .
            $campi[1] . ", " .
            "'" . $libri . "' as tabella " .
            "FROM " .
            $libri . " " .
            "WHERE " .
            $campi[1] . " LIKE"." '%".$key."%'"
        );
        $stmt = $this->connection->prepare($query); //Preparo la query
        $stmt->execute();//Esegue la query
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $titolo, $tabella);
            $risultato = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array
                $temp[$campi[0]] = $id;
                $temp[$campi[1]] = $titolo;
                $temp['tabella'] = $tabella;
                array_push($risultato, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $utenti
            }
            return $risultato;
        } else {
            return null;
        }
    }

    //------------ 22/05/18 00:20 FIN QUI OK ------------------

//------------------- SERVONO ANCORA QUESTI 2 SOTTO? ----------------

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




    //------------------------ FIN QUI REVISIONA ANDREA ------------------------------------------------


//Danilo serve per vedere i propri annunci/documenti
    public function visualizzaDocumentoPerId($Matricola,$tabella)
    {
        $documento = array();

        $table = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        if($tabella==2){
        $query =
            "SELECT " .
            $campi[1] . ", " .
            $campi[5] . " " .
            "FROM " .
            $table . " " .
            "WHERE " .
            $campi[2] . ' = ? ';
        }
            else{$query =
                "SELECT " .
                $campi[1] . ", " .
                $campi[5] . " " .
                "FROM " .
                $table . " " .
                "WHERE " .
                $campi[3] . ' = ? ';
            }
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $Matricola);
        $stmt->execute();
        $stmt->store_result();

//Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
        $stmt->bind_result($documento);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
//Indicizzo con key i dati nell'array
            $temp[$campi[1]] = $documento;

            array_push($documento, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
        }
        return $documento; //ritorno array Documento riempito con i risultati della query effettuata.
    }


public function visualizzaAnnuncioPerId($Matricola)
{
    $annunci = array();
    $table = $this->tabelleDB[6]; //Tabella per la query
    $campi = $this->campiTabelleDB[$table];
    $query = //query: "SELECT nome, FROM materia WHERE cod_cdl = ? "
        "SELECT " .
        $campi[1] . ", " .
        $campi[2] . ", " .
        $campi[3] . ", " .
        $campi[4] . ", " .
        $campi[5] . ", " .
        $campi[7] . " " .

        "FROM " .
        $table . " " .
        "WHERE " .
        $campi[6] . ' = ? ';
    $stmt = $this->connection->prepare($query);
    $stmt->bind_param("i", $Matricola);
    $stmt->execute();
    $stmt->store_result();

//Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
    $stmt->bind_result($annunci);

    while ($stmt->fetch()) { //Scansiono la risposta della query
        $temp = array(); //Array temporaneo per l'acquisizione dei dati
//Indicizzo con key i dati nell'array
        $temp[$campi[1]] = $annunci;

        array_push($annunci, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
    }
    return $annunci; //ritorno array Documento riempito con i risultati della query effettuata.
}
    public function visualizzaAnnuncioPerMateria($Materia)
    {
        $annunci = array();

        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $table2 = $this->tabelleDB[5];
        $campi2 = $this->campiTabelleDB[$table2];
        $query = //query: "SELECT id=0, titolo=1, cod_docente=2, cod materia=5,link=6, id_materia=0, FROM documento inner join materie on codmateria = id materia"
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[5] . ", " .
            $campi[7] . ", " .

            "FROM " .
            $table . ", " .
            $table2 . " " .
            "WHERE" . $campi2[1] . '= ? ' .
            "AND " .
            $campi[8] . " = " .
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
            array_push($annunci, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
        }
        return $annunci; //ritorno array Documento riempito con i risultati della query effettuata.
    }
}
?>