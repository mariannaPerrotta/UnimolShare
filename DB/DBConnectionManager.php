<?php

class DBConnectionManager
{

    //ALTERVISTA UFFICIALE
    private $connection;
    private $host = "localhost";
    private $username = "unimolshare";
    private $passwd = "";
    private $dbname = "my_unimolshare";


    /*SITEGROUND PER IL TESTING

    private $connection;
    private $host = "it30.siteground.eu";
    private $username = "valeri91_unimol";
    private $passwd = "projectUnimol300518";
    private $dbname = "valeri91_unimolshare";

        //AZURE

/*    private $connection;
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

        /*
        if (mysqli_connect_errno($this->connection)) {
            die('Failed to connect to MySQL: '.mysqli_connect_error());
        }
        */

    function runConnection()
    {

        $this->connection = new mysqli($this->host, $this->username, $this->passwd, $this->dbname);
        return $this->connection;
    }

}

?>