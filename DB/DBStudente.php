<?php
//require '../DB/DBUtenti.php';
class DBStudente
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
        ]
    ];

    //Costruttore
    public function __construct()
    {
        //Setup della connessione col DB
        $db = new DBConnectionManager();
        $this->connection = $db->runConnection();
    }

    //Funzione visualizza profilo studente ()
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
        $stmt->bind_param("s", $matricola);
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

    //Funzione rimuovi annuncio (Domenico e Jonathan)
    public function rimuoviAnnuncio($idAnnuncio)
    {
        $tabella = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query:  " DELETE FROM ANNUNCIO WHERE ID = $idAnnuncio"
        $query = (
            "DELETE FROM " .
            $tabella . " WHERE " .
            $campi[0] . " = ? "
        );

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $idAnnuncio);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    //funzione visualizza documento per nome materia(Danilo)
    public function visualizzaDocumentoPerMateria($Materia)
    {
        $documentiTab = $this->tabelleDB[3];
        $campiDocumento = $this->campiTabelleDB[$documentiTab];
        $materieTab = $this->tabelleDB[5];
        $campiMateria = $this->campiTabelleDB[$materieTab];
        //query: SELECT documento.titolo,documento.cod_docente,documento.cod_studente,documento.link FROM documento Inner join materia ON documento.cod_materia = materia.id Where materia.nome = "Ingegneria" AND id_materia=cod_materia"
        $query = (
            "SELECT " .
            $documentiTab.".". $campiDocumento[0] . ", " .
            $documentiTab.".".$campiDocumento[1] . ", " .
            $documentiTab.".".$campiDocumento[2] . ", " .
            $documentiTab.".".$campiDocumento[3] . ", " .
            $documentiTab.".".$campiDocumento[5] . " " .
            "FROM ".$documentiTab." ".
            "Inner join " .
            $materieTab . " " .
            "ON " .
            $documentiTab.".".$campiDocumento[4] . " = " .
            $materieTab.".".$campiMateria[0].
            " WHERE " . $materieTab.".".$campiMateria[1] . '= ? '

        );

        $stmt = $this->connection->prepare(/*"SELECT documento.titolo,documento.cod_docente,documento.cod_studente,documento.link FROM documento Inner join materia ON documento.cod_materia = materia.id Where materia.nome = ? "*/$query);
        $stmt->bind_param("s", $Materia);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($idDocumento, $titolo, $cod_docente, $cod_studente, $link);
            $documento = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campiDocumento[0]] = $idDocumento;
                $temp[$campiDocumento[1]] = $titolo;
                $temp[$campiDocumento[2]] = $cod_docente;
                $temp[$campiDocumento[3]] = $cod_studente;
                $temp[$campiDocumento[5]] = $link;

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
        $query =  //query: "SELECT id=0, titolo=1, cod_docente=2, cod materia=5,link=6, id_materia=0, FROM documento,docenti WHERE nomedocente = ? AND matricoladocente=cod_docente"
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

    //Funzione carica annuncio (Jonathan e Danilo)
    public function caricaAnnuncio($titolo, $contatto, $prezzo, $edizione, $casa_editrice, $cod_studente, $autori, $cod_materia)
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
            "VALUES (?,?,?,?,?,?,?,?)"
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssdssssi", $titolo, $contatto, $prezzo, $edizione, $casa_editrice, $cod_studente, $autori, $cod_materia);
        return $stmt->execute();
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
        return $stmt->execute();
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

    //Funzione visualizza documenti studenti(danilo)
    public function visualizzaDocumentistudenti()
    {
        $documenti = array();

        $table = $this->tabelleDB[3]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $table2 = $this->tabelleDB[6];
        $campi2 = $this->campiTabelleDB[$table2];
        $query = //"SELECT titolo,link FROM documenti INNER JOIN studenti ON cod_stud=matricola"
            "SELECT " .
            $campi[1] . ", " .
            $campi[5] . " " .
            "FROM " .
            $table . " " .

            "INNER JOIN " .
            $table2 . ' ON '.
            $campi[3] ." = ". $campi2[0] ;


        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $stmt->store_result();

        //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp
        $stmt->bind_result($titolo,$link);

        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[1]] = $titolo;
            $temp[$campi[5]] = $link;
            array_push($documenti, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
        }
        return $documenti; //ritorno array libri riempito con i risultati della query effettuata.

    }

    //Funzione visualizza documento per id (Danilo)
    public function visualizzaAnnuncioPerId($Matricola)
    {
        $tabella = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$tabella];
        //query: "SELECT titolo, contatto, prezzo,edizione, casaeditrice,autore FROM annunci WHERE cod_stud= $matricola"
        $query = (
            "SELECT " .
            $campi[1] . ", " .
            $campi[2] . ", " .
            $campi[3] . ", " .
            $campi[4] . ", " .
            $campi[5] . ", " .
            $campi[7] . " " .

            " FROM " .
            $tabella . " " .
            " WHERE " .
            $campi[6] . " = ? "
        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $Matricola);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp //
            $stmt->bind_result($titolo, $contatto, $prezzo, $edizione, $casaeditrice, $autore);
            $annunci = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array

                $temp[$campi[1]] = $titolo;
                $temp[$campi[2]] = $contatto;
                $temp[$campi[3]] = $prezzo;
                $temp[$campi[4]] = $edizione;
                $temp[$campi[5]] = $casaeditrice;
                $temp[$campi[7]] = $autore;
                array_push($annunci, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
            }
            return $annunci; //ritorno array annunci riempito con i risultati della query effettuata.
        }
        else {
            return null;
        }
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


    //Funzione visualizza annuncio per materia (Danilo)
    public function visualizzaAnnuncioPerMateria($Materia)
    {
        $annuncioTabella = $this->tabelleDB[0]; //Tabella per la query
        $campiAnnuncio = $this->campiTabelleDB[$annuncioTabella];
        $materiaTabella = $this->tabelleDB[5];
        $campiMateria = $this->campiTabelleDB[$materiaTabella];
        //query: "SELECT titolo, contatto, prezzo,edizione, casaeditrice,autore FROM annuncio,materia WHERE nome_materia=$materia AND cod_materia=idmateria"
        $query = (
            "SELECT " .
            $annuncioTabella.".".$campiAnnuncio[1] . ", " .
            $annuncioTabella.".".$campiAnnuncio[2] . ", " .
            $annuncioTabella.".".$campiAnnuncio[3] . ", " .
            $annuncioTabella.".".$campiAnnuncio[4] . ", " .
            $annuncioTabella.".".$campiAnnuncio[5] . ", " .
            $annuncioTabella.".".$campiAnnuncio[7] . " " .

            "FROM " .
            $annuncioTabella . " " .
            "Inner join ".
            $materiaTabella . " ON " .
            $annuncioTabella.".".$campiAnnuncio[8] . " = " .
            $materiaTabella.".".$campiMateria[0].
            " WHERE " . $materiaTabella.".".$campiMateria[1] . '= ? '


        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $Materia);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp
            $stmt->bind_result($titolo, $contatto, $prezzo, $edizione, $casaeditrice, $autore);
            $annunci = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array(); //Array temporaneo per l'acquisizione dei dati
                //Indicizzo con key i dati nell'array
                $temp[$campiAnnuncio[1]] = $titolo;
                $temp[$campiAnnuncio[2]] = $contatto;
                $temp[$campiAnnuncio[3]] = $prezzo;
                $temp[$campiAnnuncio[4]] = $edizione;
                $temp[$campiAnnuncio[5]] = $casaeditrice;
                $temp[$campiAnnuncio[7]] = $autore;
                array_push($annunci, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $documento
            }
            return $annunci; //ritorno array annunci riempito con i risultati della query effettuata.
        }
        else {
            return null;
        }
    }

    //Funzione visualizza libro per materia (Danilo)
    public function visualizzaLibroPerMateria($materia)
    {
        $libroTabella = $this->tabelleDB[4]; //Tabella per la query
        $campiLibro = $this->campiTabelleDB[$libroTabella];
        $materiaTabella = $this->tabelleDB[5];
        $campiMateria = $this->campiTabelleDB[$materiaTabella];
        //"SELECT titolo,autore,casaeditrice,edizione,link FROM libri,materie where nome=$materia AND cod_materia=idmateria"
        $query = (
            "SELECT " .
            $libroTabella.".".$campiLibro[1] . ", " .
            $libroTabella.".".$campiLibro[2] . ", " .
            $libroTabella.".".$campiLibro[3] . ", " .
            $libroTabella.".".$campiLibro[4] . ", " .
            $libroTabella.".".$campiLibro[7] . " " .

            " FROM " .
            $libroTabella . " " .
            " Inner join ".
            $materiaTabella . " " .
            " ON " .
            $libroTabella.".".$campiLibro[6] . " = " .
            $materiaTabella.".".$campiMateria[0].
            " WHERE " . $materiaTabella.".".$campiMateria[1] . " = ? "

        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $materia);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {
            $stmt->bind_result($titolo, $autore, $casaeditrice, $edizione, $link);
            $libri = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campiLibro[1]] = $titolo;
                $temp[$campiLibro[2]] = $autore;
                $temp[$campiLibro[3]] = $casaeditrice;
                $temp[$campiLibro[4]] = $edizione;

                $temp[$campiLibro[7]] = $link;
//                $temp['query']=$query;
                array_push($libri, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
            }
            return $libri; //ritorno array libri riempito con i risultati della query effettuata.
        } else {
            return null;
        }
    }

    //Funzione visualizza libro per nome docente (Danilo)
    public function visualizzaLibroPerCognomeDocente($cognomedocente)
    {
        $libroTabella = $this->tabelleDB[4]; //Tabella per la query
        $campiLibro = $this->campiTabelleDB[$libroTabella];
        $docenteTabella = $this->tabelleDB[2];
        $campiDocente = $this->campiTabelleDB[$docenteTabella];
        //query: "SELECT titolo,autore,casaeditrice,edizione,link FROM libri,docenti WHERE nome=$nomedocente AND cod_docente=iddocente "
        $query = (
            "SELECT " .
            $libroTabella.".".$campiLibro[1] . ", " .
            $libroTabella.".".$campiLibro[2] . ", " .
            $libroTabella.".".$campiLibro[3] . ", " .
            $libroTabella.".".$campiLibro[4] . ", " .
            $libroTabella.".".$campiLibro[7] . " " .

            "FROM " .
            $libroTabella . " " .
            "Inner join ".
            $docenteTabella . " ON " .
            $libroTabella.".".$campiLibro[5] . " = " .
            $docenteTabella.".".$campiDocente[0].
            " WHERE " .$docenteTabella.".".$campiDocente[2] . "= ? "


        );
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $cognomedocente);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($titolo, $autore, $casaeditrice, $edizione, $link);
            $libri = array();
            while ($stmt->fetch()) { //Scansiono la risposta della query
                $temp = array();
                //Indicizzo con key i dati nell'array
                $temp[$campiLibro[1]] = $titolo;
                $temp[$campiLibro[2]] = $autore;
                $temp[$campiLibro[3]] = $casaeditrice;
                $temp[$campiLibro[4]] = $edizione;

                $temp[$campiLibro[7]] = $link;
                array_push($libri, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
            }
            return $libri; //ritorno array libri riempito con i risultati della query effettuata.
        } else {
            return null;
        }
    }
    public function visualizzaCdlStudente($matricola)
    {
        $cdl = array();

        $table = $this->tabelleDB[1]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $table2 = $this->tabelleDB[6];
        $campi2 = $this->campiTabelleDB[$table2];
        $query = //"SELECT id,titolo FROM cdl , studenti where matricola = ? and idcdl=cdl"
            ("SELECT " . $table . "." .
                $campi[0] . ", " . $table . "." .
                $campi[1] . " " .
                "FROM " .
                $table . " " .
                "inner join " . $table2 . " on " .
                $table . "." . $campi[0] . " = " .
                $table2 . "." . $campi2[6] .
                " WHERE " .
                $table2 . "." . $campi2[0] . " = ? ");
//            "Select cdl.id,cdl.nome from cdl inner join studente on cdl.id = studente.cod_cds Where studente.matricola = ?";


        $stmt = $this->connection->prepare(/*"Select cdl.id,cdl.nome from cdl inner join studente on cdl.id = studente.cod_cds Where studente.matricola = ?"*/$query);
        $stmt->bind_param(s ,$matricola);
        $result=$stmt->execute();
//        if (!$result){
//            throw new Exception($stmt->error);
//        }
        $stmt->store_result();

        //Salvo il risultato della query in alcune variabili che andranno a comporre l'array temp
        $stmt->bind_result($id,$titolo);
        while ($stmt->fetch()) { //Scansiono la risposta della query
            $temp = array(); //Array temporaneo per l'acquisizione dei dati
            //Indicizzo con key i dati nell'array
            $temp[$campi[0]] = $id;
            $temp[$campi[1]] = $titolo;
            array_push($cdl, $temp); //Inserisco l'array $temp all'ultimo posto dell'array $annunci
        }
        return $cdl; //ritorno array libri riempito con i risultati della query effettuata.

    }
}