# 🎫 ANH-Ticket: Piattaforma di Gestione Ticket Multi-Tenant (SaaS)

Benvenuti nel progetto **ANH-Ticket**, una piattaforma Help Desk di nuova generazione progettata con un modello **Software as a Service (SaaS) Multi-Tenant**.

L'obiettivo è fornire alle **Aziende Fornitrici (Tenant)** uno strumento potente e isolato per gestire in modo strutturato l'assistenza verso i loro **Clienti Finali**.

## 1. 🎯 Obiettivo del Progetto

Creare una **piattaforma di Gestione Ticket (Help Desk) Multi-Tenant** che consenta a diverse Aziende Fornitrici di erogare servizi di assistenza ai propri Clienti Finali, garantendo per ciascuna un **ambiente isolato** con la propria configurazione, i propri Agenti e i propri clienti.

## 2. 🏛️ Architettura Clienti: Il Modello a 3 Livelli

L'architettura è la base del modello multi-tenant e si articola in tre livelli gerarchici.



| Livello | Entità Principale | Ruolo Chiave | Scopo |
| :--- | :--- | :--- | :--- |
| **Livello Piattaforma** | Tu (Gestore SaaS) | **Super Admin** | Controllo globale, creazione istanze Tenant e gestione fatturazione. **Non vede i ticket operativi.** |
| **Livello Tenant** | Azienda Fornitrice (Cliente SaaS) | **Tenant Admin / Agente** | Fornisce supporto, configura il proprio ambiente, gestisce team e automazioni. |
| **Livello Cliente** | Destinatario del Supporto | **Organizzazione / Utente Finale** | Apre i ticket e riceve l'assistenza. |

### Logica di Funzionamento B2B/B2C

* **Utente B2C (Individuale):** Utente non associato a un'Organizzazione. Vede solo lo storico dei **propri** ticket.
* **Utente B2B (Aziendale):** Utente associato a un'Organizzazione. L'associazione avviene **automaticamente** tramite la corrispondenza del dominio email aziendale (`@dominio.it`) con l'Organizzazione registrata dal Tenant Admin.

---

## 3. 🛡️ Modello di Servizio e SLA Granulari

Il sistema si basa sulla definizione di **Service Level Agreement (SLA)** configurabili a livello granulare.

| Configurazione SLA | Descrizione |
| :--- | :--- |
| **SLA di Default** | Si applica a tutti i ticket se non diversamente specificato. |
| **SLA per Organizzazione** | Permette al Tenant Admin di definire tempi specifici di **Prima Risposta** e **Risoluzione** per singoli clienti B2B importanti. |

### Visibilità Ticket per le Organizzazioni (B2B)

Questa è una configurazione attivabile per singola Organizzazione, gestita dal Tenant Admin:

* **Visibilità Personale:** L'utente vede solo i propri ticket.
* **Visibilità Organizzazione:** L'utente (se abilitato) può vedere tutti i ticket aperti dai colleghi della propria azienda/Organizzazione.

---

## 4. 📝 Il Ticket: Oggetto Centrale

Il Ticket è il record digitale di una singola richiesta e segue un ciclo di vita rigoroso.

### 4.1 Canali di Creazione

* **Email:** Invio a un indirizzo monitorato (es. `supporto@`).
* **Modulo Pubblico:** Form "Contattaci" senza necessità di login.
* **Portale Clienti:** Area riservata con login (consigliata per storici e Classificazione).

### 4.2 Stati del Ticket e Regole di Transizione

| Stato | Descrizione | Regola Aggiuntiva |
| :--- | :--- | :--- |
| **Nuovo** | Appena creato, in attesa di classificazione/assegnazione. | - |
| **Aperto** | Assegnato a un Agente, in lavorazione attiva. | - |
| **In Attesa** | In attesa di risposta/azione del cliente (**l'SLA si ferma**). | Chiusura automatica dopo X giorni se il cliente non risponde. |
| **Risolto** | L'Agente ha fornito la soluzione. | Se il cliente risponde, lo stato torna automaticamente su **Aperto (Riapertura)**. |
| **Chiuso** | Il caso è archiviato (spesso automaticamente dopo X giorni da "Risolto"). | Se il cliente risponde, il sistema deve notificare l'utente di **aprire un nuovo ticket (Follow-up)**. |

### 4.3 Classificazione Strutturata (Routing)

Per l'instradamento automatico, la creazione del ticket dal Portale Clienti richiede campi obbligatori:

* **Categoria:** Macro-argomento (es. "Supporto Tecnico").
* **Sotto-Categoria:** Dettaglio dinamico in base alla Categoria (es. "Reset Password").

#### Fallback di Sicurezza (Triage)
L'opzione "**Altro / Non Specificato**" in Categoria instrada il ticket a una coda speciale chiamata **Triage**, dove un Agente dedicato ne completa manualmente la classificazione.

---

## 5. 🤖 Automazione del Sistema

L'automazione garantisce efficienza e il rispetto degli SLA. Si basa su tre tipi di logiche:

| Nome Regola | Tipo di Esecuzione | Scopo Principale | Esempio (Logica IF/THEN) |
| :--- | :--- | :--- | :--- |
| **Trigger** | Istantanea (Su creazione/aggiornamento) | Instradamento e Prioritizzazione immediata. | `SE Categoria = "Fatturazione" ALLORA Assegna a Team "Amministrazione"` |
| **Automazione** | Basata sul Tempo (A intervalli regolari) | Monitoraggio SLA ed Escalation. | `SE Stato = "Aperto" E Ore Trascorse > X ALLORA Notifica Capo Team` |
| **Macro** | Manuale (Azione 1-clic dell'Agente) | Efficienza e Risposte Standard. | **Macro "Dati Mancanti":** `Imposta Stato = "In Attesa" E Invia risposta predefinita` |

---

## 6. 🧑‍💻 Il Personale di Supporto (Tenant)

### 6.1 Team

I **Team** sono l'unità fondamentale di assegnazione (es. "Supporto Livello 1"). Gli Agenti lavorano estraendo i ticket dalle code del proprio Team.

### 6.2 Ruoli Gerarchici

| Ruolo | Funzionalità e Permessi | Accesso ai Ticket | Accesso Configurazione |
| :--- | :--- | :--- | :--- |
| **Agente** | Operatore quotidiano, risponde ai clienti, usa le Macro. | Lavora sui ticket assegnati al suo Team. | **No** |
| **Capo Team** | Fa tutto ciò che fa un Agente, più supervisione operativa del Team. Riceve notifiche di Escalation. | Vede tutti i ticket del suo Team, può riassegnare internamente. | **No** |
| **Amministratore (Admin)** | Configura l'applicazione (SLA, Automazioni, Team, Clienti). | **Non può lavorare sui ticket** né modificarne lo stato. | **Sì (Totale)** |

### 6.3 Gestione Flusso Errori (Riassegnazione)

* **Permesso Agenti:** A tutti gli Agenti è permesso riassegnare un ticket verso un altro Team in caso di errore di instradamento.
* **Tracciabilità:** Ogni riassegnazione richiede l'inserimento obbligatorio di una **Nota Interna** per la tracciabilità.
* **Visibilità Destinazione:** Il ticket appare nella coda del nuovo Team con un **indicatore visivo** della sua provenienza (es. "Riassegnato da Fatturazione").

---

## 7. 💻 Moduli Applicativi (Interfacce Utente)

La piattaforma si compone di tre interfacce principali:

1.  **Pannello Amministrazione (Backend):**
    * **Utenti:** Solo l'**Amministratore** (Tenant Admin).
    * **Funzione:** Configurazione completa del sistema (SLA, Automazioni, Team, Clienti). **Non gestisce i singoli ticket operativi.**
2.  **Pannello Agente (Backend):**
    * **Utenti:** **Agenti** e **Capi Team**.
    * **Funzione:** Interfaccia di lavoro quotidiana. Visualizzazione, risposta, risoluzione e gestione degli stati dei ticket (Viste e Code).
3.  **Portale Clienti (Front End):**
    * **Utenti:** **Clienti Finali** (Utenti B2C e B2B).
    * **Funzione:** Creazione nuovi ticket (con Classificazione Categoria/Sotto-Categoria) e consultazione dello storico personale/Organizzazione.

---

## 🔗 Risorse del Progetto

| Risorsa | Descrizione | Link |
| :--- | :--- | :--- |
| **Prototipo Funzionale (Figma/Marvel)** | Visualizzazione interattiva dell'interfaccia utente. | [https://anh-helpdesk-suite.lovable.app](https://anh-helpdesk-suite.lovable.app) |
| **Documentazione Tecnica** | Dettagli sull'implementazione del backend e API. | *(Da definire)* |
| **Repository Git** | Codice sorgente del progetto. | *(Da definire)* |
