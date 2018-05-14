<?php
public function accesso_utente(Request $request, Response $response) {

$result = false;

$// richiamo lo script responsabile della connessione a MySQL
require 'DBConnectionManager.php';

if ($con) {

$requestData = $request->getParsedBody();
$username = $requestData['username'];

$password = $requestData['password'];

if ($username && $password) {

$query = "SELECT id FROM utente WHERE username = ? AND password = ?";
$stmt = $con->prepare($query);

$stmt->bind_param("ss", $username, md5($password));

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows) {
    $result = true;

    $this->message = "accesso effettuato";

    $response = self::get_response($response, $result, 'accesso', true);
    } else {

        $this->message = "username o password non validi";

        $response = self::get_response($response, $result, 'accesso', true);
            }
            } else {

                $this->message = "parametri mancanti";

                $response = self::get_response($response, $result, 'accesso', false);
                    }
                    } else {

                        $this->message = "database non connesso";

                        $response = self::get_response($response, $result, 'accesso', false);
                            }

                            mysqli_close($con);

                            return $response;
                            }
?>