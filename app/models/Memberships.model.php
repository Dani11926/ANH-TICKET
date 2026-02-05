<?php

class Memberships
{
    private $db;

    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }

    /* ----------------------------------------------------------------
       1. INSERISCI (Crea il collegamento Utente <-> Azienda)
    ---------------------------------------------------------------- */
    public function inserisciMembership($data)
    {
        // La chiave primaria è composta (tenant_id + global_id), quindi niente ID autoincrementale da ritornare.
        // Usiamo INSERT IGNORE o ON DUPLICATE KEY UPDATE se vogliamo evitare errori su duplicati,
        // ma per ora stiamo sul semplice INSERT.

        $query = "INSERT INTO tenant_membership (tenant_id, global_id, role) 
                  VALUES (:tenant_id, :global_id, :role)";

        $this->db->query($query);

        $this->db->bind(":tenant_id", $data['tenant_id']);
        $this->db->bind(":global_id", $data['global_id']);
        // Se il ruolo non è specificato, usa il default del DB ('User')
        $this->db->bind(":role", $data['role'] ?? 'User');

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /* ----------------------------------------------------------------
       2. MODIFICA (Aggiorna il Ruolo)
       Nota: In una tabella di collegamento, di solito si modifica solo il 'role'.
       Le chiavi (tenant_id, global_id) sono fisse.
    ---------------------------------------------------------------- */
    public function modificaMembership($data)
    {
        // Ci servono assolutamente gli ID per sapere chi modificare
        if (empty($data['tenant_id']) || empty($data['global_id'])) {
            return false;
        }

        // Modifichiamo il ruolo
        $query = "UPDATE tenant_membership SET role = :role 
                  WHERE tenant_id = :tenant_id AND global_id = :global_id";

        $this->db->query($query);

        $this->db->bind(":role", $data['role']);
        $this->db->bind(":tenant_id", $data['tenant_id']);
        $this->db->bind(":global_id", $data['global_id']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /* ----------------------------------------------------------------
       3. ELIMINA DINAMICO (Come richiesto)
       Costruisce la WHERE in base ai campi passati.
       - Se passi ['tenant_id' => 5, 'global_id' => 10] -> Rimuove QUEL singolo utente da QUEL tenant.
       - Se passi ['global_id' => 10] -> Rimuove l'utente da TUTTI i tenant (pericoloso ma potente).
       - Se passi ['tenant_id' => 5] -> Svuota il tenant (rimuove tutti i membri).
    ---------------------------------------------------------------- */
    public function eliminaMembership($data)
    {
        // SICUREZZA: Se l'array è vuoto, fermati. Altrimenti farebbe "DELETE FROM..." cancellando tutto!
        if (empty($data)) {
            return false;
        }

        $query = "DELETE FROM tenant_membership WHERE ";

        // Costruiamo le condizioni dinamicamente (es: "tenant_id = :tenant_id AND global_id = :global_id")
        $condizioni = [];
        foreach (array_keys($data) as $key) {
            $condizioni[] = "$key = :$key";
        }

        // Unisce le condizioni con " AND "
        $query .= implode(" AND ", $condizioni);

        $this->db->query($query);

        // Bind dinamico dei valori
        foreach ($data as $key => $value) {
            $this->db->bind(":$key", $value);
        }

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /* ----------------------------------------------------------------
       4. VISUALIZZA (SELECT)
    ---------------------------------------------------------------- */

    // Trova tutti i tenant a cui appartiene un utente
    public function visualizzaAppartenanzaTenant($global_id)
    {
        // Ho aggiunto 'role' alla select perché è utile sapere che ruolo ha
        $query = "SELECT tenant_id, role FROM tenant_membership WHERE global_id = :global_id";

        $this->db->query($query);
        $this->db->bind(":global_id", $global_id);

        $result = $this->db->resultObj(); // FetchAll

        // Ritorna l'array vuoto se non trova nulla, o i risultati
        return $result;
    }

    // Trova tutti i membri di uno specifico tenant
    public function visualizzaMembriTenant($tenant_id)
    {
        // CORREZIONE QUERY: Selezionavo tenant_id (inutile), ora seleziono global_id e role
        // In futuro qui vorrai fare una JOIN con global_identity per avere le email
        $query = "SELECT global_id, role FROM tenant_membership WHERE tenant_id = :tenant_id";

        $this->db->query($query);

        // CORREZIONE BIND: Prima usavi :global_id bindato a $tenant_id (errore)
        $this->db->bind(":tenant_id", $tenant_id);

        $result = $this->db->resultObj();

        return $result;
    }
}