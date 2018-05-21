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
    private $connection;
    private $host = "unimolshare.mysql.database.azure.com";
    private $username = "mariannaPerrotta@unimolshare";
    private $passwd = "Unimolshare@";
    private $dbname = "unimolshare";
    private $port = "3306";
     private  $cert= "..\ssl\BaltimoreCyberTrustRoot.crt.pem";


    function runConnection()
    {  $this->connection = mysqli_init();
        mysqli_ssl_set($this->connection, NULL, NULL,$this->cert, NULL, NULL);
        mysqli_real_connect($this->connection, $this->host, $this->username, $this->passwd, $this->dbname,$this->port);

        if (mysqli_connect_errno($this->connection)) {
            die('Failed to connect to MySQL: '.mysqli_connect_error());
        }


        return $this->connection;
    }

}

?>