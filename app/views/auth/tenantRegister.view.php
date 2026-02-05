<?php
// Assumiamo che $data sia passato dal controller
// Se $tenantsInfo contiene oggetti (es. da PDO::FETCH_OBJ), usiamo ->id
$tenantsInfo = $data ?? [];
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione Azienda - ANH-Ticket</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            padding: 20px;
        }

        .error-message {
            color: red;
            font-size: 0.85em;
            margin-top: 5px;
            display: none;
        }

        .input-error {
            border: 1px solid red;
        }

        input,
        textarea,
        select {
            width: 100%;
            max-width: 400px;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Registra qui la tua Azienda!</h1>
    <h3>Entra anche te a far parte di ANH-Ticket</h3>
    <br>

    <form action="<?= htmlspecialchars(URLROOT . '/auth/register'); ?>" method="post" id="registrationForm" novalidate>

        <div>
            <label for="name">Inserisci il nome della tua azienda:</label><br>
            <input type="text" id="name" name="name" placeholder="Scrivi qui....">
            <div class="error-message" id="error-name">Nome obbligatorio</div>
        </div>
        <br>

        <div>
            <label for="description">Inserisci una breve descrizione (opzionale)</label><br>
            <textarea name="description" id="description" rows="5"></textarea>
        </div>

        <hr>
        <h2>Inserisci le credenziali per il tuo Admin:</h2>

        <div>
            <label for="name-admin">Inserisci il tuo nome</label><br>
            <input type="text" name="name-admin" id="name-admin">
            <div class="error-message" id="error-name-admin">Il nome è obbligatorio</div>
        </div>
        <br>

        <div>
            <label for="surname">Inserisci il tuo cognome</label><br>
            <input type="text" name="surname" id="surname">
            <div class="error-message" id="error-surname">Il cognome è obbligatorio</div>
        </div>
        <br>

        <div>
            <label for="email">Inserisci l'Email</label><br>
            <input type="email" id="email" name="email">
            <div class="error-message" id="error-email">Email non valida</div>
        </div>
        <br>

        <div>
            <label for="password">Inserisci la Password:</label><br>
            <input type="password" id="password" name="password">
            <div class="error-message" id="error-password">Password obbligatoria</div>
        </div>
        <br>

        <div>
            <label for="confirm-password">Conferma la Password:</label><br>
            <input type="password" id="confirm-password" name="confirm-password">
            <div class="error-message" id="error-confirm-password">Le password non corrispondono</div>
        </div>
        <br>

        <div>
            <label for="planSelect">Seleziona un Piano</label><br>
            <select name="plan" id="planSelect">
                <option value="">-- Seleziona un piano --</option>
                <?php foreach ($tenantsInfo as $tenantInfo): ?>
                    <option value="<?= htmlspecialchars($tenantInfo->id); ?>">
                        <?= htmlspecialchars($tenantInfo->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>

        <button type="submit">Registra Azienda</button>
    </form>

    <script>
        const registrationForm = document.getElementById('registrationForm');

        const handleError = (inputId, errorId, condition, errorMessage) => {
            const element = document.getElementById(inputId);
            const errorDiv = document.getElementById(errorId);

            if (condition) {
                element.classList.add("input-error");
                errorDiv.style.display = "block";
                if (errorMessage) errorDiv.textContent = errorMessage;
                return false;
            } else {
                element.classList.remove("input-error");
                errorDiv.style.display = "none";
                return true;
            }
        }

        registrationForm.addEventListener('submit', function (e) {
            e.preventDefault();

            let isFormValid = true;

            // 1. Validazione Nome Azienda
            const companyNameInput = document.getElementById("name");
            const companyNameCondition = companyNameInput.value.trim() === "";
            if (!handleError("name", "error-name", companyNameCondition, "Il nome dell'azienda è obbligatorio")) {
                isFormValid = false;
            }

            // 2. Validazione Nome Admin
            const adminNameInput = document.getElementById("name-admin");
            const adminNameCondition = adminNameInput.value.trim() === "";
            if (!handleError("name-admin", "error-name-admin", adminNameCondition, "Il nome admin è obbligatorio")) {
                isFormValid = false;
            }

            // 3. Validazione Cognome
            const surnameInput = document.getElementById("surname");
            const surnameCondition = surnameInput.value.trim() === "";
            if (!handleError("surname", "error-surname", surnameCondition, "Il cognome è obbligatorio")) {
                isFormValid = false;
            }

            // 4. Validazione Email
            const emailInput = document.getElementById("email");
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let emailErrorMsg = "";
            let emailCondition = false;

            if (emailInput.value.trim() === "") {
                emailCondition = true;
                emailErrorMsg = "Campo email obbligatorio";
            } else if (!regexEmail.test(emailInput.value)) {
                emailCondition = true;
                emailErrorMsg = "Formato della mail non valido";
            }

            if (!handleError("email", "error-email", emailCondition, emailErrorMsg)) {
                isFormValid = false;
            }

            // 5. Validazione Password
            const passwordInput = document.getElementById("password");
            let passCondition = false;
            let passMsg = "";

            if (passwordInput.value === "") {
                passCondition = true;
                passMsg = "La password è obbligatoria";
            } else if (passwordInput.value.length < 6) {
                passCondition = true;
                passMsg = "La password deve essere di almeno 6 caratteri";
            }

            if (!handleError("password", "error-password", passCondition, passMsg)) {
                isFormValid = false;
            }

            // 6. Validazione Conferma Password
            const confirmInput = document.getElementById("confirm-password");
            let confirmCondition = false;
            let confirmMsg = "";

            if (confirmInput.value === "") {
                confirmCondition = true;
                confirmMsg = "La conferma della password è obbligatoria";
            } else if (confirmInput.value !== passwordInput.value) {
                confirmCondition = true;
                confirmMsg = "Le password non corrispondono";
            }

            if (!handleError("confirm-password", "error-confirm-password", confirmCondition, confirmMsg)) {
                isFormValid = false;
            }

            // Invio del form se tutto è OK
            if (isFormValid) {
                registrationForm.submit();
            }
        });

        // Rimuovi errore durante la digitazione
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function () {
                this.classList.remove('input-error');
                // Cerca il div di errore successivo
                let next = this.nextElementSibling;
                while (next && !next.classList.contains('error-message')) {
                    next = next.nextElementSibling;
                }
                if (next) next.style.display = 'none';
            });
        });
    </script>
</body>

</html>