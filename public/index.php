<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 11/05/18
 * Time: 21:11
 */

/* In questo file php vengono elencati tutti gli endpoint disponibili al servizio REST */

//Importiamo Slim e le sue librerie
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../vendor/autoload.php';
require '../DB/DBConnectionManager.php';
require '../DB/DBQueryManager.php';
require '../Helper/EmailHelper/EmailHelper.php';
require '../Helper/RandomPasswordHelper/RandomPasswordHelper.php';

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new App($settings); //"Contenitore" per gli endpoint da riempire


$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


/*  Gli endpoint sono delle richieste http accessibili al Client gestite poi dal nostro Server REST.
    Tra i tipi di richieste http, le piÃ¹ usate sono:
    - get (richiesta dati -> elaborazione server -> risposta server)
    - post (invio dati criptati e richiesta dati -> elaborazione server -> risposta server)
    - delete (invio dato (id di solito) e richiesta eliminazione -> elaborazione server -> risposta server)

    Slim facilita per noi la gestione della richiesta http mettendo a disposizione funzioni facili da implementare
    hanno la forma:

    app->"richiesta http"('/nome endpoint', function (Request "dati inviati dal client", Response "dati risposti dal server") {

        //logica del servizio

        return "risposta";

    }

 */

/*************** LISTA DI ENDPOINT ***************/

/* aggiungo ad $app tutta la lista di endpoint che voglio */

/**** ENDPOINT DI TEST ****/

// endpoint: /listaUtenti (Andrea)
$app->get('/listaUtenti', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $responseData = $db->getUtenti();//Risposta del DB
    //metto in un json e lo inserisco nella risposta del servizio REST
    $response->getBody()->write(json_encode(array("utenti" => $responseData)));
    //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
    $newResponse = $response->withHeader('Content-type', 'application/json');
    return $newResponse; //Invio la risposta del servizio REST al client
});

// endpoint: /testGetStudenti (Andrea)
$app->get('/testGetUtenti', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $responseData = $db->testGetStudenti();//Risposta del DB
    //metto in un json e lo inserisco nella risposta del servizio REST
    $response->getBody()->write(json_encode(array("studenti" => $responseData)));
    //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
    $newResponse = $response->withHeader('Content-type', 'application/json');
    return $newResponse; //Invio la risposta del servizio REST al client
});

/**** ENDPOINT DEL PROGETTO ****/
$app->post('/insertmateria', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $id = $requestData['id'];
    $nome = $requestData['nome'];
    $cod_doc = $requestData['cod_doc'];
    $cdl = $requestData['cdl'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta Ã¨ un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->testInsertMateria($id, $nome, $cod_doc, $cdl)) { //Se la registrazione Ã¨ andata a buon fine
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Registrazione avvenuta con successo'; //Messaggio di esito positivo

    } else {
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Email associata a un account giÃ  esistente!'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

// endpoint: /login (Andrea)
$app->post('/login', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $email = $requestData['email'];
    $password = $requestData['password'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta Ã¨ un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    $utente = $db->login($email, $password);
    if ($utente) { //Se l'utente esiste ed Ã¨ corretta la password
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Accesso effettuato'; //Messaggio di esiso positivo
        $responseData['utente'] = $utente[0];

    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Credenziali errate'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

// endpoint: /registration (Francesco)
$app->post('/registration', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $matricola = $requestData['matricola'];
    $nome = $requestData['nome'];
    $cognome = $requestData['cognome'];
    $email = $requestData['email'];
    $password = $requestData['password'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta Ã¨ un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    $responseDB = $db->registrazione($matricola, $nome, $cognome, $email, $password);
    if ($responseDB == 1) { //Se la registrazione è andata a buon fine
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Registrazione avvenuta con successo'; //Messaggio di esito positivo

    } else if ($responseDB == 2){ //Se l'email è già presente nel DB
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Account già  esistente!'; //Messaggio di esito negativo
    }
    else{//Se l'email non è istituzionale
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = "Email non valida! Usare un'email istituzionale."; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

// endpoint: /update (Gigi)
$app->post('/update', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $matricola = $requestData['matricola'];
    $nome = $requestData['nome'];
    $cognome = $requestData['cognome'];
    $password = $requestData['password'];
    $tabella = $requestData['tabella'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta Ã¨ un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->modificaProfilo($matricola, $nome, $cognome, $password, $tabella)) {
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Update effettuato'; //Messaggio di esiso positivo

    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = "Impossibile effettuare l'update"; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});


//endpoint /recover (Danilo)
$app->post('/recover', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $email = $requestData['email'];

    //Risposta del servizio REST
    $responseData = array();
    $emailSender = new EmailHelper();
    $randomizerPassword = new RandomPasswordHelper();

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->recupero($email)) { //Se l'email viene trovata
        $nuovaPassword = $randomizerPassword->generatePassword(4);

        if($db->modificaPassword($email, $nuovaPassword)) {
            $messaggio = "Usa questa password temporanea";

            if ($emailSender->sendEmail($messaggio, $email, $nuovaPassword)) {
                $responseData['error'] = false; //Campo errore = false
                $responseData['message'] = "Email di recupero password inviata"; //Messaggio di esito positivo
            } else {
                $responseData['error'] = true; //Campo errore = true
                $responseData['message'] = "Impossibile inviare l'email di recupero"; //Messaggio di esito negativo
            }
        }
        else { //Se le credenziali non sono corrette
            $responseData['error'] = true; //Campo errore = true
            $responseData['message'] = 'Impossibile comunicare col Database'; //Messaggio di esito negativo
        }


    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Email non presente nel DB'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});
$app->post('/visualizzamateriapercdl', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $cod_cdl = $requestData['cod_cdl'];

//Risposta del servizio REST
    $responseData = array();

//Controllo la risposta dal DB e compilo i campi della risposta
    $responseData = $db->visualizzaMateriaPerCdl($cod_cdl);
    if ($responseData != null) {
        $responseData['error'] = false; //Campo errore = false

        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
        $response->getBody()->write(json_encode(array("nomi_materie" => $responseData)));
        //metto in un json e lo inserisco nella risposta del servizio REST

        //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse; //Invio la risposta del servizio REST al client
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto';
        return $response->withJson($responseData);
    }
    //Invio la risposta del servizio REST al client
});
$app->post('/visualizzadocumentopermateria', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $materia = $requestData['materia'];

//Controllo la risposta dal DB e compilo i campi della risposta
    $responseData = $db->visualizzaDocumentoPerMateria($materia);
    if ($responseData != null) {
        $responseData['error'] = false; //Campo errore = false

        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
        $response->getBody()->write(json_encode(array("documenti" => $responseData)));
        //metto in un json e lo inserisco nella risposta del servizio REST

        //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse; //Invio la risposta del servizio REST al client
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto';
        return $response->withJson($responseData);
    }

});
$app->post('/VisualizzaCDL', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST

//Controllo la risposta dal DB e compilo i campi della risposta
    $responseData = $db->VisualizzaCDL();
    if ($responseData != null) {
        $responseData['error'] = false; //Campo errore = false

        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
        $response->getBody()->write(json_encode(array("CDL" => $responseData)));
        //metto in un json e lo inserisco nella risposta del servizio REST

        //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse; //Invio la risposta del servizio REST al client
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto';
        return $response->withJson($responseData);
    }

});
$app->post('/visualizzaannunciopermateria', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $materia = $requestData['materia'];

//Controllo la risposta dal DB e compilo i campi della risposta
    $responseData = $db->visualizzaAnnuncioPerMateria($materia);
    if ($responseData != null) {
        $responseData['error'] = false; //Campo errore = false

        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
        $response->getBody()->write(json_encode(array("annunci" => $responseData)));
        //metto in un json e lo inserisco nella risposta del servizio REST

        //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse; //Invio la risposta del servizio REST al client
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto';
        return $response->withJson($responseData);
    }

});
//fin qui danilo

//endpoint rimuovi by jo dom
$app->delete('/rimuovidocumento', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $idDocumento = $requestData['idDocumento'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta è un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    $esito = $db->rimuoviDocumento($idDocumento);
    if ($esito) { //Se è stato possibile rimuovere il documento
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Documento rimosso'; //Messaggio di esito positivo

    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Non Ã¨ stato possibile rimuovere il documento'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

$app->delete('/rimuoviAnnuncio', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $id = $requestData['idannuncio'];


    //Risposta del servizio REST
    $responseData = array(); //La risposta è un array di informazioni da compilare


    if ($db->rimuoviAnnuncio($id)) { //Se l'utente esiste ed è corretta la password
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Documento eliminato'; //Messaggio di esiso positivo


    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Erorre imprevisto'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});
//-----

//endpoint /visualizzaprofilostudente (Michela)
$app->post('/visualizzaprofilostudente', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $matricola = $requestData['matricola'];

//Risposta del servizio REST
    $responseData = array();

//Controllo la risposta dal DB e compilo i campi della risposta
    $temp = $db->visualizzaProfiloStudente($matricola);
    if ($temp != null) {
        $responseData['error'] = false; //Campo errore = false
        $responseData['nome'] = $temp[1];
        $responseData['cognome'] = $temp[2];
        $responseData['email'] = $temp[3];
        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto'; //Messaggio di esiso positivo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});
$app->post('/visualizzaprofilodocente', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $matricola = $requestData['matricola'];

//Risposta del servizio REST
    $responseData = array();

//Controllo la risposta dal DB e compilo i campi della risposta
    $temp = $db->visualizzaProfiloDocente($matricola);
    if ($temp != null) {
        $responseData['error'] = false; //Campo errore = false
        $responseData['nome'] = $temp[1];
        $responseData['cognome'] = $temp[2];
        $responseData['email'] = $temp[3];
        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto'; //Messaggio di esiso positivo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});


// endpoint: /caricaDocumento (Jonathan)
$app->post('/caricadocumento', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $titolo = $requestData['titolo'];
    $codice_docente = $requestData['codice_docente'];
    $codice_studente = $requestData['codice_studente'];
    $codice_materia = $requestData['codice_materia'];
    $link = $requestData['link'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta Ã¨ un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->caricaDocumento($titolo, $codice_docente, $codice_studente, $codice_materia, $link)) { //Se il caricamento del doc Ã¨ andata a buon fine
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Caricamento avvenuto con successo'; //Messaggio di esito positivo

    } else {
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Caricamento non effettuato'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

// endpoint: /downloadDocumento (Andrea)
$app->post('/downloadDocumento', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $id = $requestData['id'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta Ã¨ un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    $link = $db->downloadDocumento($id);
    if ($link != null) { //Se l'utente esiste ed Ã¨ corretta la password
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'In download'; //Messaggio di esiso positivo
        $responseData['link'] = $link;

    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Impossibile scaricare il file'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});


//contattavenditore by domenico
$app->post('/contattavenditore', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $idAnnuncio = $requestData['id'];

//Risposta del servizio REST
    $responseData = array();

//Controllo la risposta dal DB e compilo i campi della risposta
    $temp = $db->contattaVenditore($idAnnuncio);

    if ($temp != null) {
        $responseData['error'] = false; //Campo errore = false
        $responseData['contatto'] = $temp[1];
        $responseData['email'] = $temp[2];
        $responseData['message'] = 'Elementi visualizzati con successo'; //Messaggio di esito positivo
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

// endpoint: /valutaizonedocumento (Andrea)
$app->post('/valutazionedocumento', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $valutazione = $requestData['valutazione'];
    $cod_documento = $requestData['cod_documento'];

    //Risposta del servizio REST
    $responseData = array();

    //Controllo la risposta dal DB
    if ($db->valutazioneDocumento($valutazione, $cod_documento)) { //Se il caricamento della valutaizone è andato a buon fine
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Valutazione avvenuta con successo'; //Messaggio di esito positivo

    } else {
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Valutaizone non effettuata'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});


// endpoint: /ricerca (Andrea)
$app->post('/ricerca', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $key = $requestData['key'];

    $responseData = $db->ricerca($key);//Risposta del DB
    //metto in un json e lo inserisco nella risposta del servizio REST
    $response->getBody()->write(json_encode(array("lista" => $responseData)));
    //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
    $newResponse = $response->withHeader('Content-type', 'application/json');
    return $newResponse; //Invio la risposta del servizio REST al client
});

//------------------------ FIN QUI REVISIONA ANDREA ------------------------------------------------

$app->post('/visualizzadocumentoperid', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $matricola = $requestData['matricola'];
    $tabella_utente=$requestData['$tabella_utente'];

//Controllo la risposta dal DB e compilo i campi della risposta
    $responseData = $db->visualizzaDocumentoPerId($matricola,$tabella_utente);
    if ($responseData != null) {
        $responseData['error'] = false; //Campo errore = false

        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
        $response->getBody()->write(json_encode(array("documenti" => $responseData)));
        //metto in un json e lo inserisco nella risposta del servizio REST

        //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse; //Invio la risposta del servizio REST al client
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto';
        return $response->withJson($responseData);
    }

});
$app->post('/visualizzaannunciopermatricola', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $matricola = $requestData['matricola'];

//Controllo la risposta dal DB e compilo i campi della risposta
    $responseData = $db->visualizzaAnnuncioPerId($matricola);
    if ($responseData != null) {
        $responseData['error'] = false; //Campo errore = false

        $responseData['message'] = 'Elemento visualizzato con successo'; //Messaggio di esiso positivo
        $response->getBody()->write(json_encode(array("annunci" => $responseData)));
        //metto in un json e lo inserisco nella risposta del servizio REST

        //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
        $newResponse = $response->withHeader('Content-type', 'application/json');
        return $newResponse; //Invio la risposta del servizio REST al client
    } else {
        $responseData['error'] = true; //Campo errore = false
        $responseData['message'] = 'Errore imprevisto';
        return $response->withJson($responseData);
    }

});
// Run app = ho riempito $app e avvio il servizio REST
$app->run();

?>
