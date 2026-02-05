<?php

class Users
{
    private $db;

    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }

    // Registra un nuovo utente
    public function register($data)
    {
        $this->db->query('INSERT INTO global_identity (email, password_hash) VALUES (:email, :password)');
        // Bind dei parametri
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        // Esegue la query
        if (!$this->db->execute()) { //Se la transazioen non va, non faccio niente
            return false;
        }

        //Vado a prendere l'UUID, cercando per la mail
    }

    public function login($data)
    {
        $this->db->query('SELECT * FROM global_identity WHERE email = :email AND password = :password');
        // Bind dei parametri
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        // Esegue la query
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Trova utente per email (Fondamentale per il Login)
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM global_identity WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if ($this->db->rowCount() > 0) {
            return $row; // Ritorna l'oggetto utente (con global_id, password_hash, ecc.)
        } else {
            return false;
        }
    }

    // Trova utente per ID
    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM global_identity WHERE global_id = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }
}