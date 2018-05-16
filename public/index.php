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

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new App($settings); //"Contenitore" per gli endpoint da riempire

/*  Gli endpoint sono delle richieste http accessibili al Client gestite poi dal nostro Server REST.
    Tra i tipi di richieste http, le più usate sono:
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

// endpoint: /login (Andrea)
$app->post('/login', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $idattore = $requestData['idattore'];
    $password = $requestData['password'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta è un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->login($idattore, $password)) { //Se l'utente esiste ed è corretta la password
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Accesso effettuato con successo'; //Messaggio di esiso positivo
        $responseData['tipoUtente'] = $db->getTypeByIdAttore($idattore); //Restituisco il tipo attore per la specializzazione dell'utente

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
    $email = $requestData['email'];
    $password = $requestData['password'];
    $nome = $requestData['nome'];
    $cognome = $requestData['cognome'];
    $idattore=$requestData['idattore'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta è un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    if (!$db->registration($email,$nome,$cognome,$password,$idattore)) { //Se la registrazione è andata a buon fine
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Registrazione avvenuta con successo'; //Messaggio di esito positivo

    } else {
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Email associata a un account già esistente!'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

// endpoint: /update (Gigi)
$app->post('/update', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $idattore = $requestData['idattore'];
    $password = $requestData['password'];
    $nome = $requestData['nome'];
    $cognome= $requestData['cognome'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta è un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->updateProfile($idattore, $nome, $cognome, $password)) {
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Update effettuato con successo'; //Messaggio di esiso positivo

    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Errore nel DB'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});


//endpoint /recover (Danilo)

$app->post('/recover', function (Request $request, Response $response){

$db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $email = $requestData['email'];

    //Risposta del servizio REST
    $responseData = array();

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->recover($email)) { //Se l'email viene trovata
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = "Invio email di recupero"; //Messaggio di esito positivo

    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Email non presente nel DB'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});


//endpoint /visualizzaprofilostudente
$app->post('/visualizzaprofilostudente', function (Request $request, Response $response) {

    $db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $matricola = $requestData['matricola'];

//Risposta del servizio REST
    $responseData = array();

//Controllo la risposta dal DB e compilo i campi della risposta
    $responseData['profilo']=$db->visualizzaProfiloStudente($matricola);//Se l'email viene trovata
    $responseData['error'] = false; //Campo errore = false
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
    $link=$requestData['link'];

    //Risposta del servizio REST
    $responseData = array(); //La risposta è un array di informazioni da compilare

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->caricaDocumento($titolo,$codice_docente,$codice_studente,$codice_materia,$link)) { //Se il caricamento del doc è andata a buon fine
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = 'Caricamento avvenuto con successo'; //Messaggio di esito positivo

    } else {
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'Caricamento non effettuato'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client

});


// Run app = ho riempito $app e avvio il servizio REST
$app->run();

?>
