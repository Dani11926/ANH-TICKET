<?php

/*
 * Classe Core (Router)
 * Questa classe crea URL e carica il controller principale.
 * FORMATO URL: /controller/metodo/parametro1/parametro2
 */
class Core
{
    // Imposta il controller di default se nessuno è specificato nell'URL
    private $controller = 'Pages';

    // Imposta il metodo di default se nessuno è specificato
    private $method = 'index';

    // Array vuoto per ospitare eventuali parametri passati via URL
    private $params = [];

    public function __construct()
    {
        // 1. Richiama la funzione urlHandler() per ottenere l'URL diviso in un array
        $url = $this->urlHandler();

        // --- GESTIONE DEL CONTROLLER (Indice 0 dell'array $url) ---

        // Controlla se l'indice 0 è settato (es. sito.it/prodotti)
        if (isset($url[0])) {

            // Controlla se il file del controller esiste nella cartella controllers
            // ucwords() converte la prima lettera in maiuscolo (es. prodotti -> Prodotti)
            // [NOTA] Qui cerchi nella cartella "controllers" (plurale)
            if (file_exists("../app/controllers/" . ucwords($url[0]) . ".php")) {

                // Se il file esiste, imposta la proprietà controller con il nome trovato
                $this->controller = ucwords($url[0]);

                // Rimuove l'indice 0 dall'array $url per non confonderlo con i parametri dopo
                unset($url[0]);

            } else {
                // --- GESTIONE ERRORE 404 (File non trovato) ---

                // Se il file non esiste, imposta il controller a "_404"
                $this->controller = "_404";

                // Richiede il file della classe di errore
                // [ATTENZIONE] Qui hai scritto "controller" (singolare), sopra era "controllers" (plurale). 
                // Devono essere uguali o darà errore.
                require_once "../app/controllerS/" . $this->controller . ".php";

                // Istanzia la classe di errore (es. $this->controller = new _404())
                $this->controller = new $this->controller();

                // Chiama il metodo di default della classe errore ed esce dallo script
                call_user_func_array([$this->controller, $this->method], $this->params);
                exit();
            }
        }

        // --- ISTANZIAZIONE DEL CONTROLLER ---

        // Richiede il file del controller (quello trovato o quello di default 'Pages')
        // [ATTENZIONE] Verifica sempre se la cartella è "controller" o "controllers"
        require_once "../app/controllerS/" . $this->controller . ".php";

        // Crea un nuovo oggetto della classe controller (es. new Pages())
        $this->controller = new $this->controller();


        // --- GESTIONE DEL METODO (Indice 1 dell'array $url) ---

        // Controlla se c'è una seconda parte nell'URL (es. sito.it/prodotti/modifica)
        if (isset($url[1])) {

            // Controlla se il metodo esiste all'interno della classe controller istanziata
            if (method_exists($this->controller, $url[1])) {

                // Se esiste, sovrascrive il metodo di default ('index')
                $this->method = $url[1];

                // Rimuove l'indice 1 dall'array, lasciando solo i parametri
                unset($url[1]);
            }
        }


        // --- GESTIONE DEI PARAMETRI (Indici rimanenti) ---

        // Se $url non è vuoto (sono rimasti elementi dopo gli unset), riordina gli indici.
        // Altrimenti imposta params come array vuoto.
        // Esempio: se c'erano [2=>'5', 3=>'admin'], array_values lo trasforma in [0=>'5', 1=>'admin']
        $this->params = $url ? array_values($url) : [];


        // --- ESECUZIONE FINALE ---

        // Chiama il metodo del controller passando i parametri
        // Sintassi: call_user_func_array([Oggetto, 'NomeMetodo'], [ArrayParametri])
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    // Funzione per parsare l'URL
    private function urlHandler()
    {
        // Controlla se è stata passata la variabile 'url' via GET (grazie al .htaccess)
        if (isset($_GET['url'])) {

            // Rimuove lo slash finale se presente (es. prodotti/ -> prodotti)
            $url = rtrim($_GET['url'], '/');

            // Pulisce l'URL da caratteri illegali per sicurezza
            $url = filter_var($url, FILTER_SANITIZE_URL);

            // Divide la stringa in un array basandosi sugli slash
            return explode('/', $url);
        }

        // Se non c'è URL, ritorna array vuoto (userà i default)
        return [];
    }
}