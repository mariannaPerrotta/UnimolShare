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

// endpoint: /listaUtenti
$app->get('/listaUtenti', function (Request $request, Response $response) {
    $db = new DBQueryManager();

    $responseData = $db->getUtenti();//Risposta del DB
    //metto in un json e lo inserisco nella risposta del servizio REST
    $response->getBody()->write(json_encode(array("utenti" => $responseData)));
    //Definisco il Content-type come json, i dati sono strutturati e lo dichiaro al browser
    $newResponse = $response->withHeader('Content-type', 'application/json');
    return $newResponse; //Invio la risposta del servizio REST al client
});

// endpoint: /login
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

//endpoint Recover

$app->post('/recover', function (Request $request, Response $response){

$db = new DBQueryManager();

    $requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
    $email = $requestData['email'];


    //Risposta del servizio REST
    $responseData = array();

    //Controllo la risposta dal DB e compilo i campi della risposta
    if ($db->registration($email)) { //Se l'email viene trovata
        $responseData['error'] = false; //Campo errore = false
        $responseData['message'] = "invio l'email"; //Messaggio di esiso positivo


    } else { //Se le credenziali non sono corrette
        $responseData['error'] = true; //Campo errore = true
        $responseData['message'] = 'email non trovata'; //Messaggio di esito negativo
    }
    return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});

// Run app = ho riempito $app e avvio il servizio REST
$app->run();

?>
