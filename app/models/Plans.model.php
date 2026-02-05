<?php

class Plans{
    private $db;

    public function __construct($db_conn) {
        $this->db = $db_conn;
    }

    /* * 1. INSERISCI PIANO
     */
    public function aggiungiPiano($data){
        // CORRETTO: 'max_ticekts' -> 'max_tickets' (refuso corretto nella query)
        $this->db->query("INSERT INTO plan (name, description, price, max_users, max_tickets_monthly, db_isolation_level, duration_months)
                          VALUES (:name, :description, :price, :max_users, :max_tickets_monthly, :db_isolation_level, :duration_months)");

        $this->db->bind(":name", $data["name"]);
        $this->db->bind(":description", $data["description"]);
        $this->db->bind(":price", $data["price"]);
        $this->db->bind(":max_users", $data["max_users"]);
        // CORRETTO: Bind deve corrispondere al placeholder della query (:max_tickets_monthly)
        $this->db->bind(":max_tickets_monthly", $data["max_tickets_monthly"]); 
        $this->db->bind(":db_isolation_level", $data["db_isolation_level"]);
        $this->db->bind(":duration_months", $data["duration_months"]);

        // Esegue la query
        if($this->db->execute()){
            // CORRETTO: Ritorna l'ID appena creato recuperandolo dal DB
            // Nota: Assicurati che la tua classe Database abbia il metodo lastInsertId() o esponga public $dbh
            return $this->db->lastInsertId(); 
        } else {
            return false;
        }
    }

    /* * 2. MODIFICA PIANO (Update dinamico)
     */
    public function modificaPiano($data, $id){
        // Costruzione dinamica della query (Molto avanzata, bravo!)
        $query = "UPDATE plan SET ";

        $chiavi = array_keys($data);
        foreach($chiavi as $key){
            // Aggiunge "nome_colonna = :nome_colonna, "
            $query .= $key . " = :" . $key . ", ";
        }
        
        // Rimuove l'ultima virgola e spazio
        $query = rtrim($query, ", ");
        
        // Aggiunge la clausola WHERE fondamentale
        $query .= " WHERE plan_id = :plan_id";

        $this->db->query($query);

        // Bind dinamico dei valori
        foreach($data as $key => $value){
            $this->db->bind(":" . $key, $value);
        }

        // Bind dell'ID (che non è nell'array $data ma passato a parte)
        $this->db->bind(":plan_id", $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    /* * 3. RIMUOVI PIANO
     */
    public function rimuoviPiano($id){
        // CORRETTO: Mancava il punto e virgola finale
        $query = "DELETE FROM plan WHERE plan_id = :plan_id";
        
        $this->db->query($query);
        $this->db->bind(":plan_id", $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    /* * 4. VISUALIZZA TUTTI I PIANI
     */
    public function visualizzaPiani(){
        // Prende tutti i piani ordinati per prezzo
        $this->db->query("SELECT * FROM plan ORDER BY price ASC");
        
        // Usa resultObj perché ci aspettiamo MOLTE righe (array di oggetti)
        return $this->db->resultObj();
    }

    /* * 5. VISUALIZZA PIANO PER ID
     */
    public function visualizzaPianoId($id){
        $this->db->query("SELECT * FROM plan WHERE plan_id = :id");
        $this->db->bind(":id", $id);

        // Usa single() perché ci aspettiamo UNA sola riga (un oggetto singolo)
        return $this->db->singleResult();
    }
}