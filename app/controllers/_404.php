<?php

class _404 extends Controller{

    public function index(){
        try{
            $this->view("_404", []);
        }
        catch(Exception $e){
            echo "Errore interno al server";
        }

    }
}