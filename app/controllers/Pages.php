<?php

class Pages extends Controller{
    private $tenantModel;
    private $planModel;
    private $db_conn;
    
    public function __construct(){
        $this->db_conn = new Database();

        try{
            $this->tenantModel = $this->model("Tenants", $this->db_conn);
        }
        catch(Exception $e){
            die("Errore impossibile trovare il Tenants model");
        }

        try{
            $this->planModel = $this->model("Plans", $this->db_conn);
        }
        catch(Exception $e){
            die("Errore impossibile trovare il Plans model");
        }
        
    }

    public function index(){
        
        $numeroTenant = $this->tenantModel->tenantsNumber(); //calcolo in numero di tenant
     
        $data = [
            'numeroTenant' => $numeroTenant
        ];
        
        $this->view("pages/index", $data);
    }

    public function pricing(){
        $plans = $this->planModel->visualizzaPiani();
        $plansGruoped = []; //array in cui inseriro gli array e gli oggetti sistemati
        
        foreach($plans as $plan){
            $parts = explode(" ", trim($plan->name));
            $gruopKey = $parts[0];

            if($gruopKey == "Enterprise"){
                $gruopKey .= " ". $parts[1];
            }

            $durationKey = match($plan->duration_months) {
                1 => 'monthly',
                3 => 'quarterly',
                12 => 'yearly',
                default => 'custom_' . $plan->duration_months
            };

            $monthlyPrice = $plan->price / $plan->duration_months;
            $plan->monthly_price = number_format($monthlyPrice,2);

            $plansGruoped[$gruopKey][$durationKey] = $plan;
        }

        $this->view("pages/pricing" , $plansGruoped);
    }

    public function about(){
        $this->view("pages/about");
    }
}