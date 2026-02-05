<?php
session_start();
class Auth extends Controller
{

    private $currentTenant;
    private $tenantModel;

    private $userModel;
    private $planModel;
    private $db_conn;

    public function __construct()
    {
        $this->db_conn = new Database();

        try {
            $this->tenantModel = $this->model("Tenants", $this->db_conn);
            $this->planModel = $this->model("Plans", $this->db_conn);
            $this->userModel = $this->model("Users", $this->db_conn);
        } catch (Exception $e) {
            die("Impossibile collogarsi ai model");
        }
    }

    public function register()
    {
        $hostName = trim($_SERVER["HTTP_HOST"]);
        $parts = explode(".", $hostName);

        if (count($parts) > 1) {
            // 1. Prendi il nome "sporco" dall'URL (es. "pizzeria-bella-napoli")
            $rawTenant = $parts[0];

            // 2. Sostituisci i trattini con gli spazi
            $formattedTenantName = str_replace('-', ' ', $rawTenant);

            // 3. (Opzionale ma consigliato) Cerca nel DB in modo "Case Insensitive"
            // Passi $formattedTenantName che ora è "pizzeria bella napoli"
            $checkTenant = $this->tenantModel->findTenantByName($formattedTenantName);

            if (!$checkTenant) {
                header("Location: " . URLROOT . '/helpCenter');
                return; // IMPORTANTE: ferma lo script
            }

            $this->currentTenant = $checkTenant;
            $this->userRegister(); //Gestisco la registrazioni degli utenti per quel tenant, semplicemente rimando a quella funzione
            return;
        }

        //QUA INVECE VUOL DIRE CHE SIAMO NELLA REGISTRAZIONE PER I TENANT

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Gestire i dati della form
            $name = $_POST["name"];
            $description = $_POST["description"];
            $plan_id = $_POST["plan"];
            $adminName = $_POST["name-admin"];
            $adminSurname = $_POST["surname"];
            $emailAdmin = $_POST["email"];
            $passwordAdmin = $_POST["password"];
            $confimPasswordAdmin = $_POST["confirm-password"];


            //Inserire qui i controlli



            //fine controlli

            //Transazione per inserire sia tenant che utente nel database 
            //Inserire tenant nel database
            $tenantInfo = [
                "name" => $name,
                "description" => $description,
                "plan_id" => $plan_id
            ];

            $adminInfo = [
                "email" => $emailAdmin,
                "password" => $passwordAdmin
            ];

            try {
                $this->db_conn->beginTransaction();

                $tenantId = $this->tenantModel->inserisciTenant($tenantInfo);
                $globaId = $this->userModel->register($adminInfo);

                $this->db_conn->commitTransaction();
                echo "Registrazione azienda e amministratore completata!";
            } catch (Exception $e) {
                if ($this->db_conn->inTransaction()) {
                    $this->db_conn->rollBackTransaction();
                }

                echo "Errore nella registrazione dell'azienda: " . $e->getMessage();
            }



            $_SESSION["adminName"] = $adminName;
            $_SESSION["adminSurname"] = $adminSurname;
            $_SESSION["tenantId"] = $tenantId;
            //Salva in sessione anche UUID utente servirà per la tebella dentro 

            exit();
        }

        //PRENDO IL NOME DI TUTTI I PIANI
        $plans = $this->planModel->visualizzaPiani();

        //ARRAY IN CUI INSERISCO I NOMI
        $data = [];

        foreach ($plans as $plan) {
            $data[] = (object) array(
                "id" => $plan->plan_id,
                "name" => $plan->name,
            );
        }

        $this->view("auth/tenantRegister", $data); //di default mostro la form di registrazione dei tenant
    }

    private function userRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Gestire i dati della form
            $name = $_POST["name"];
            $surname = $_POST["surname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $confirmPassword = $_POST["confirm-password"];

            //Inserire qui i controlli



            //fine controlli

            //Inserire tenant nel database

            $passwordHash = hash("sha256", $password);
            $userInfo = [
                "email" => $email,
                "password" => $passwordHash,
            ];

            $result = $this->userModel->register($userInfo); //Se è giusto ritorna l'ultimo id inserito$result = $this->userModel->register($userInfo); //Se è giusto ritorna l'ultimo id inserito

            if (!$result) {
                echo "Errore interno al server";
                exit();
            }

            $_SESSION["name"] = $name;
            $_SESSION["surname"] = $surname;
            $_SESSION["email"] = $email;
            $_SESSION["tenantId"] = $this->currentTenant->tenant_id;

            $this->verificaMail();

            exit();
        }

        $tenant = [
            "name" => $this->currentTenant->name,
            "description" => $this->currentTenant->description,
        ];

        $this->view("auth/userRegister", $tenant);
    }
    private function verificaMail()
    {
        header("Location: http://" . $this->currentTenant->name . ".localhost/ANH-TICKET/email/verifyEmail");
    }
}