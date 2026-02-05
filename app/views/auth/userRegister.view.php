<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - ANH-TICKET</title>
    <style>
        /* Lo stile semplice che mi hai inviato */
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
    <h1>Benvenuto su ANH-TICKET</h1>
    <h2>Ti stai registrando per ricevere i servizi dell'azienda: <strong>
            <?php echo htmlspecialchars($data['name']); ?>
        </strong></h2>
    <br>

    <form action="<?= htmlspecialchars("http://" . $data["name"] . ".localhost/ANH-TICKET/auth/register") ?>"
        method="post" id="registrationForm" novalidate>

        <div>
            <label for="name">Inserisci il tuo nome</label><br>
            <input type="text" name="name" id="name">
            <div class="error-message" id="error-name">Il nome inserito non è valido</div>
        </div>
        <br>

        <div>
            <label for="surname">Inserisci il tuo cognome</label><br>
            <input type="text" name="surname" id="surname">
            <div class="error-message" id="error-surname">Il cognome inserito non è valido</div>
        </div>
        <br>

        <div>
            <label for="email">Inserisci la tua email</label><br>
            <input type="email" id="email" name="email">
            <div class="error-message" id="error-email">La mail inserita non è valida</div>
        </div>
        <br>

        <div>
            <label for="password">Inserisci la tua password</label><br>
            <input type="password" id="password" name="password">
            <div class="error-message" id="error-password">La password inserita non è valida</div>
        </div>
        <br>

        <div>
            <label for="confirm-password">Conferma la password</label><br>
            <input type="password" id="confirm-password" name="confirm-password">
            <div class="error-message" id="error-confirm-password">Le password non corrispondono</div>
        </div>

        <button type="submit">Crea Account</button>
    </form>

    <script>
        const registrationForm = document.getElementById("registrationForm");

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

        registrationForm.addEventListener("submit", (e) => {
            e.preventDefault();

            let isFormValid = true;

            // 1. Validazione Nome
            const nameInput = document.getElementById("name");
            const nameCondition = nameInput.value.trim() === "";
            if (!handleError("name", "error-name", nameCondition, "Il nome è obbligatorio")) {
                isFormValid = false;
            }

            // 2. Validazione Cognome
            const surnameInput = document.getElementById("surname");
            const surnameCondition = surnameInput.value.trim() === "";
            if (!handleError("surname", "error-surname", surnameCondition, "Il cognome è obbligatorio")) {
                isFormValid = false;
            }

            // 3. Validazione Email
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

            // 4. Validazione Password
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

            // 5. Validazione Conferma Password
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

            if (isFormValid) {
                registrationForm.submit();
            }
        });

        // Listener per pulire gli errori mentre scrivi
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function () {
                this.classList.remove('input-error');
                let nextSibling = this.nextElementSibling;
                while (nextSibling) {
                    if (nextSibling.classList && nextSibling.classList.contains('error-message')) {
                        nextSibling.style.display = 'none';
                        break;
                    }
                    nextSibling = nextSibling.nextElementSibling;
                }
            });
        });
    </script>
</body>

</html>