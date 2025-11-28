# ANH-TICKET

1. Obiettivo del progetto
L'obiettivo è creare una piattaforma di Gestione Ticket (Help Desk) Multi-Tenant. Il sistema non serve solo un'unica entità di supporto, ma è progettato come una piattaforma SaaS (Software as a Service) che permette a diverse Aziende Fornitrici (i tuoi clienti diretti) di erogare servizi di assistenza ai propri Clienti Finali.
Ogni Azienda Fornitrice avrà il proprio ambiente isolato, i propri agenti e i propri clienti, gestendo le richieste in modo centralizzato e strutturato.
2. Architettura clienti
Per supportare questo modello, l'architettura si divide in tre livelli gerarchici:
2.1 Livello Piattaforma (Tu)
Super Admin: È il gestore della piattaforma (tu). Ha il controllo globale, crea le istanze per le Aziende Fornitrici e gestisce la fatturazione verso di loro. Non vede i ticket specifici dei clienti finali per motivi di privacy, ma monitora l'uso delle risorse.
2.2 Livello Tenant (Azienda Fornitrice)
È l'entità che acquista il tuo servizio per fare supporto (es. un'azienda IT, un call center).
Tenant Admin: L'amministratore dell'Azienda Fornitrice. Configura i propri team, le regole di automazione e gestisce i propri clienti.
Agente: Il dipendente dell'Azienda Fornitrice che risolve i ticket.
2.3 Livello Cliente (Destinatario del supporto)
Sono i clienti dell'Azienda Fornitrice.
Organizzazione (Cliente B2B): Rappresenta un'azienda che riceve assistenza dal Tenant. (Es. Lo studio legale assistito dall'azienda IT).
Utente Finale (Requester): La persona fisica che apre il ticket. Può essere un privato (B2C) o un dipendente di un'Organizzazione (B2B).

Logica di Funzionamento:

Utente B2C (Individuale): Un Utente non associato a un'Organizzazione specifica. Può creare ticket e visualizzare solo lo storico dei propri ticket.
Utente B2B (Aziendale): Un Utente la cui email (es. @azienda-cliente.it) corrisponde al dominio registrato di un'Organizzazione.
Associazione Automatica: Quando un utente si registra con un'email aziendale, viene automaticamente collegato alla sua Organizzazione.

3. Il modello di servizio: piani e SLA

Rimossa la logica a "Piani" (Base/Premium). Il sistema permette all'Azienda Fornitrice di definire i livelli di servizio (SLA) in modo granulare per ogni specifico contratto cliente.
3.1 Configurazione SLA (Service Level Agreement)
L'SLA definisce i tempi massimi di Prima Risposta e di Risoluzione. Invece di dipendere da un piano generico, gli SLA sono assegnati:
SLA di Default: Si applica a tutti i ticket se non diversamente specificato.
SLA per Organizzazione: L'Azienda Fornitrice può configurare regole specifiche per un cliente importante (es. "Il Cliente X ha un tempo di risposta garantito di 1 ora").
3.2 Visibilità Ticket (B2B)
La visibilità dei ticket è una configurazione attivabile per singola Organizzazione:
Visibilità Personale: L'utente vede solo i propri ticket.
Visibilità Organizzazione: L'utente (se abilitato) può vedere tutti i ticket aperti dai colleghi della propria azienda. Questa impostazione è gestita dal Tenant Admin nelle scheda dell'Organizzazione.




4. Il Ticket 
Il Ticket è l'oggetto centrale del sistema. È il record digitale di una singola richiesta o problema.

4.1 Creazione Ticket
Il sistema accetta ticket da più canali:
Email: Un'email inviata a un indirizzo monitorato (es. supporto@) crea automaticamente un ticket.
Modulo Pubblico: Un form "Contattaci" sul sito web, che non richiede login.
Portale Clienti: Un'area riservata (con login) dove l'Utente può aprire un ticket e vedere il proprio storico.
4.2 Stati del Ticket
Ogni ticket si muove attraverso un ciclo di vita definito da stati chiari:
Nuovo: Appena creato, in attesa di essere classificato o assegnato.
Aperto: Assegnato a un Agente, che ci sta lavorando attivamente.
In Attesa: In attesa di una risposta o di un'azione da parte del cliente (il tempo di SLA si ferma).
Regola Aggiuntiva: Il sistema chiude automaticamente il ticket se il cliente non risponde entro un tempo definito (es. 7 giorni) dallo stato "In Attesa".
Risolto: L'Agente ha fornito la soluzione.
Regola di Riapertura: Se il cliente risponde a un ticket nello stato Risolto, lo stato torna automaticamente su Aperto per la riattivazione.
Chiuso: Il caso è archiviato (spesso automaticamente dopo X giorni dallo stato "Risolto").
Regola di Archiviazione: Se il cliente risponde a un ticket nello stato Chiuso, il sistema deve notificare l'utente che deve aprire un nuovo ticket ("Follow-up").
4.3 Classificazione dei servizi
Per evitare la complessità di un sistema ITSM completo, ma permettere comunque di gestire "servizi specifici", l'applicazione utilizza una Classificazione Strutturata.
Come Funziona: Quando un Utente apre un ticket dal portale, deve compilare dei campi dropdown obbligatori:
Categoria: Il macro-argomento (es. "Supporto Tecnico", "Fatturazione", "Richiesta Commerciale").
Sotto-Categoria: Un elenco dinamico basato sulla Categoria (es. "PC Lento", "Reset Password", "Errore Fattura").
Scopo: Questi campi permettono ai Trigger di instradare automaticamente e con precisione il ticket al team giusto.
Il Fallback (La Rete di Sicurezza): Per l'utente che "non si ritrova", la Categoria include sempre l'opzione "Altro / Non Specificato". Un Trigger specifico intercetterà questi ticket e li assegnerà a una coda speciale chiamata "Triage", dove un Agente li classificherà manualmente prima di inoltrarli.











5. Automazione del sistema 
Questa è la logica che fa risparmiare tempo, garantisce gli SLA e rende il sistema "intelligente". È composta da tre elementi:

Nome Regola
Tipo di Esecuzione
Scopo
Esempio (Logica IF/THEN)
Trigger
Istantanea (Azione immediata dopo un evento, es. creazione/aggiornamento ticket) 
Instradamento e Prioritizzazione 
SE Categoria = "Fatturazione" E Piano Cliente = "Premium" ALLORA Assegna a Team "Amministrazione VIP" 
Automazione
Basata sul Tempo (Eseguita a intervalli regolari, es. ogni ora) 
Monitoraggio SLA e Escalation 
SE Stato = "Aperto" E Piano = "Premium" E Ore Trascorse > 2 ALLORA Imposta Priorità = "Urgente" E Notifica Capo Team 
Macro
Manuale (Azione dell'Agente con 1 clic) 
Efficienza e Risposte Standard 
Macro "Dati Mancanti": Imposta Stato = "In Attesa" E Invia risposta predefinita "Ciao, abbiamo bisogno di..." 58

6. Il personale di supporto
6.1 Team
I Team sono il cuore dell'assegnazione. Un ticket non viene quasi mai assegnato a una persona, ma a un Team (es. "Supporto Livello 1", "Fatturazione", "Team Triage"). Gli Agenti disponibili "pescano" i ticket dalla coda del loro Team.
6.2 Ruoli del Personale Interno
Il personale è diviso in tre ruoli gerarchici:
Agente (Membro del Team):
È l'operatore quotidiano.
Appartiene a uno o più Team.
Lavora sui ticket assegnati al suo Team, risponde ai clienti, usa le Macro e aggiorna gli stati.
Permesso Aggiuntivo: Può riassegnare un ticket verso un altro Team se si rende conto di un errore di instradamento (vedi Sezione 6.3).
Non può configurare il sistema.
Capo Team (Team Leader):
È un "Agente con superpoteri" limitati al suo Team.
Fa tutto ciò che fa un Agente.
In più: Può vedere tutti i ticket del suo Team, riassegnarli manualmente tra i suoi membri.
Riceve le notifiche di Escalation dalle Automazioni per il suo Team.
Il suo ruolo è di supervisione operativa della coda del suo Team. Non è tenuto a monitorare le code degli altri Team.
Non può configurare il sistema (non è un Admin).
Amministratore (Admin):
È il "Super Utente" che configura l'applicazione.
Gestisce la fatturazione, i Piani di Servizio e le Organizzazioni dei clienti.
Crea e gestisce i Team e invita gli Agenti e i Capi Team.
Crea e modifica tutte le regole del Motore di Automazione (Trigger, Automazioni, Macro).
NON può lavorare sui ticket, rispondere ai clienti, o modificare lo stato di un ticket. Il suo accesso è limitato alle funzionalità di configurazione e reportistica di alto livello.
6.3 Gestione Flusso Errori (Riassegnazione Agenti)
Per garantire la fluidità del servizio, a tutti gli Agenti è permesso riassegnare un ticket a un altro Team se individuano un errore di instradamento (sia che provenga da un Trigger, sia da una creazione manuale).
Tracciabilità: Quando un Agente sposta un ticket, è obbligatorio inserire una Nota Interna che motivi lo spostamento. Questo garantisce che il Capo Team (o l'Amministratore) possa monitorare la correttezza delle riassegnazioni.
Visibilità nel Team di Destinazione: Il ticket riassegnato appare nella coda del nuovo Team. Viene visualizzato con un indicatore visivo (es. un tag o un'icona) che ne segnala la provenienza da un altro reparto.
7. Moduli applicativi (Aggiornata)
Pannello Amministrazione (Backend): L'interfaccia usata solo dagli Amministratori per configurare tutto quanto descritto nelle sezioni 3, 5 e 6. Questo modulo non include la possibilità di visualizzare o modificare singoli ticket operativi.
Pannello Agente (Backend): L'interfaccia di lavoro quotidiana per Agenti e Capi Team. Mostra le "Viste" e le "Code" dei ticket. Qui si visualizza, si risponde e si risolve il ticket.
Portale Clienti (Front End): L'area con login per i Clienti Finali (Utenti B2C e B2B). Permette di creare nuovi ticket (con i campi Categoria) e di visualizzare lo storico (con la logica di visibilità B2B/Premium).



