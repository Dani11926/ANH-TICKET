<?php

class Tenants
{

    private $db;

    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }

    /* ----------------------------------------------------------------
       1. CREAZIONE (INSERT)
       Ritorna l'ID del nuovo tenant creato
    ---------------------------------------------------------------- */
    public function inserisciTenant($data)
    {
        if (empty($data)) {
            return false;
        }

        // Nota: registration_date e subscription_date hanno un default nel DB (CURRENT_TIMESTAMP),
        // quindi non serve passarli a meno che tu non voglia forzare una data specifica.
        $query = "INSERT INTO tenant (name, description, state, plan_id) 
                  VALUES (:name, :description, :state, :plan_id)";

        $this->db->query($query);

        $this->db->bind(":name", $data['name']);
        $this->db->bind(":description", $data['description']);
        // Se lo stato non è passato, mettiamo 'Trial' come default di sicurezza, anche se il DB lo fa già
        $this->db->bind(":state", $data['state'] ?? 'Trial');
        $this->db->bind(":plan_id", $data['plan_id']);

        try {
            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (Exception $e) {
            die("Eccezione SQL: " . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------------
       2. MODIFICA (UPDATE DINAMICO)
       Accetta i dati da cambiare e l'ID del tenant
    ---------------------------------------------------------------- */
    public function modificaTenant($data, $id)
    {
        if (empty($data) || empty($id)) {
            return false;
        }

        $query = "UPDATE tenant SET ";

        // Costruiamo la query dinamicamente in base ai campi passati in $data
        $chiavi = array_keys($data);
        foreach ($chiavi as $key) {
            $query .= $key . " = :" . $key . ", ";
        }

        // Rimuoviamo l'ultima virgola
        $query = rtrim($query, ", ");

        $query .= " WHERE tenant_id = :tenant_id";

        $this->db->query($query);

        // Bind dinamico dei valori
        foreach ($data as $key => $value) {
            $this->db->bind(":" . $key, $value);
        }

        // Bind dell'ID
        $this->db->bind(":tenant_id", $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /* ----------------------------------------------------------------
       3. ELIMINA (DELETE)
    ---------------------------------------------------------------- */
    public function eliminaTenant($id)
    {
        if (empty($id)) {
            return false;
        }

        $query = "DELETE FROM tenant WHERE tenant_id = :tenant_id";

        $this->db->query($query);
        $this->db->bind(":tenant_id", $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /* ----------------------------------------------------------------
       4. METODI DI LETTURA (SELECT)
    ---------------------------------------------------------------- */

    public function visualizzaTenants()
    {
        $query = "SELECT * FROM tenant";
        $this->db->query($query);

        return $this->db->resultObj(); // Ritorna array di oggetti
    }

    public function cercaTenantId($id)
    {
        if (empty($id)) {
            return false;
        }

        $query = "SELECT * FROM tenant WHERE tenant_id = :tenant_id";
        $this->db->query($query);

        $this->db->bind(":tenant_id", $id);

        $result = $this->db->singleResult(); // Ritorna singolo oggetto

        // Se non trova nulla ritorna false
        if (!$result) {
            return false;
        }
        return $result;
    }

    public function findTenantByName($name)
    {
        if (empty($name)) {
            return false;
        }

        $query = "SELECT * FROM tenant WHERE name = :name";
        $this->db->query($query);

        $this->db->bind(":name", $name);

        $result = $this->db->singleResult();

        if (!$result) {
            return false;
        }
        return $result;
    }

    public function cercaTenantsPiano($plan_id)
    {
        if (empty($plan_id)) {
            return false;
        }

        $query = "SELECT * FROM tenant WHERE plan_id = :plan_id";
        $this->db->query($query);

        $this->db->bind(":plan_id", $plan_id);

        $result = $this->db->resultObj(); // Ritorna array perché un piano può avere molti tenant

        if (!$result) {
            return false; // O ritorna array vuoto [] a seconda delle preferenze
        }
        return $result;
    }

    /* ----------------------------------------------------------------
       5. INFORMAZIONI TENANT
    ---------------------------------------------------------------- */

    public function tenantsNumber()
    { //Serve per contare in numero di tenant registrati
        $query = "SELECT COUNT(T.tenant_id) AS numeroTenant FROM tenant T";
        $this->db->query($query);


        $result = $this->db->singleResult();
        if (!$result) {
            return false;
        }
        return $result->numeroTenant; //ritorna una colonna chiamta numero_tenant
    }
}