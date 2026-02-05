<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <nav>
        <a href="<?php echo URLROOT; ?>/Pages/index">Home</a>
        <a href="<?php echo URLROOT; ?>/Pages/about">About</a>
    </nav>
    <br>
    <section id="plansContainer">

    </section>

    <template id="plansSection">
        <div class="plan-group">
            <h1 class="plansSectionTitle"></h1>
            <div class="plans-list"></div>
        </div>
    </template>

    <template id="plansCard">
        <div class="plan-card" style="border:1px solid #ccc; padding:10px; margin:5px;">
            <h2 class="plansCardName"></h2>
            <h4 class="plansCardDuration"></h4>
            <p class="plansCardDescription"></p>
            <hr>
            <p>Utenti Max: <span class="plansCardMaxUsers"></span></p>
            <p>Ticket: <span class="plansCardMaxTickets"></span></p>
            <h3 class="plansCardPrice">Prezzo al mese: </h3>
        </div>
    </template>

    <script>
        // 1. Recuperiamo i dati da PHP
        const plansData = <?= json_encode($data); ?>;
        // Nota: Assumo tu stia usando l'array raggruppato che abbiamo creato prima.

        const mainContainer = document.getElementById("plansContainer");
        const sectionTemplate = document.getElementById("plansSection");
        const cardTemplate = document.getElementById("plansCard");

        // 2. Iteriamo sui GRUPPI (Starter, Pro, Enterprise...)
        // Object.entries trasforma l'oggetto in array di coppie [chiave, valore]
        Object.entries(plansData).forEach(([groupName, durations]) => {

            // --- A. CLONIAMO LA SEZIONE ---
            const sectionClone = sectionTemplate.content.cloneNode(true);

            // Usiamo querySelector perché stiamo cercando dentro il clone, non nel documento intero
            // E usiamo la CLASSE (.plansSectionTitle) non l'ID (#)
            sectionClone.querySelector('.plansSectionTitle').textContent = groupName;

            // Riferimento al div interno dove mettere le card
            const cardsContainer = sectionClone.querySelector('.plans-list');

            // --- B. ITERIAMO SULLE VARIANTI (Monthly, Yearly...) ---
            Object.entries(durations).forEach(([durationKey, planObj]) => {

                // Cloniamo la Card
                const cardClone = cardTemplate.content.cloneNode(true);

                // Riempiamo i dati usando le classi
                cardClone.querySelector('.plansCardName').textContent = planObj.name;
                cardClone.querySelector('.plansCardDuration').textContent = durationKey.toUpperCase(); // Es. MONTHLY
                cardClone.querySelector('.plansCardDescription').textContent = planObj.description;
                cardClone.querySelector('.plansCardMaxUsers').textContent = planObj.max_users;
                cardClone.querySelector('.plansCardMaxTickets').textContent = planObj.max_tickets_monthly;
                cardClone.querySelector('.plansCardPrice').textContent += "€ " + planObj.price;

                // Appendiamo la card alla lista della sezione
                cardsContainer.appendChild(cardClone);
            });

            // --- C. APPENDIAMO LA SEZIONE COMPLETA AL MAIN ---
            mainContainer.appendChild(sectionClone);
        });
    </script>
</body>

</html>