<?php

/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 11/05/18
 * Time: 19:50
 */

//Classe che gestisce la connessione col database relazionale
class DBConnectionManager
{
    //Variabili di classe
    private $connection;
    private $host = 'localhsot';
    private $username = 'root';
    private $passwd = '';
    private $dbname = 'db'; //Va inserito il nome del database creato dal sottogruppo di Doro

    //Funzione di connessione al db
    function runConnection(){
        $this->connection = new mysqli($this->host, $this->username, $this->passwd, $this->dbname);
        return $this->connection;
    }

}