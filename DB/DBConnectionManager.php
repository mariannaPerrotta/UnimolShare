<?php

/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 11/05/18
 * Time: 19:50
 */

//Classe che gestisce la connessione col database relazionale
class DBConnectionManager {
    //Variabili di classe

    //Valori del mio server online dove è ospitato il DB
    private $connection;
    private $host = 'it30.siteground.eu';
    private $username = 'valeri91_unimol';
    private $passwd = 'projectUnimol300518';
    private $dbname = 'valeri91_unimolshare';
    //private $dbname = 'diariostraordinari'; //Vecchio db per test rest in locale

    //Funzione di connessione al db
    function runConnection() {
        $this->connection = new mysqli($this->host, $this->username, $this->passwd, $this->dbname);
        return $this->connection;
    }
}

?>