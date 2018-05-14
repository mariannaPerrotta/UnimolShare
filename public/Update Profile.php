// Funzione Modifica Profilo (da rivedere) //Gigi
    public function updateProfile ($idattore, $nome, $cognome, $password)
    {
        $table = $this->tabelleDB[0]; //Tabella per la query
        $campi = $this->campiTabelleDB[$table];
        $query = //query:  " UPDATE TABLE, SET CAMPO WHERE ID ATTORE"
            "UPDATE ".
                    $table." ".
            "SET ".
                    $campi[2]." = ? , ".
                    $campi[3]." = ?, ".
                    $campi[4]." = ?, ".
            "WHERE ".
                $campi[0]." = ? ";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssss", $nome, $cognome, $password, $idattore); //ss se sono 2 stringhe, ssi 2 string e un int (sostituisce ? della query)
        $stmt->execute();
        $stmt->store_result();






























































    }
?>