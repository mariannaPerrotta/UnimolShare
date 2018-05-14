
// endpoint: /registration (Francesco)
$app->post('/registration', function (Request $request, Response $response) {
$db = new DBQueryManager();

$requestData = $request->getParsedBody();//Dati richiesti dal servizio REST
$email = $requestData['email'];
$password = $requestData['password'];
$nome = $requestData['nome'];
$cognome = $requestData['cognome'];

//Risposta del servizio REST
$responseData = array(); //La risposta è un array di informazioni da compilare

//Controllo la risposta dal DB e compilo i campi della risposta
if (!$db->registration($email)) { //Se l'email non esiste prosegue con la registrazione
$responseData['error'] = false; //Campo errore = false
    //to do funzione di popolamento del database
$responseData['message'] = 'Registrazione'; //Messaggio di esito positivo

} else { //Se l'email già è stata registrata in precedenza
$responseData['error'] = true; //Campo errore = true
$responseData['message'] = 'Email associata a un account già esistente!'; //Messaggio di esito negativo
}
return $response->withJson($responseData); //Invio la risposta del servizio REST al client
});
