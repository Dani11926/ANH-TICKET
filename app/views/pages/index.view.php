<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <nav>
        <a href="<?php echo URLROOT; ?>/Pages/pricing">Piani</a>
        <a href="<?php echo URLROOT; ?>/Pages/about">About</a>
    </nav>
    <h1>ANH-Ticket</h1>
    <h3>Piattoforma moderna per la gestione di ticket</h3>

    <br>

    <p>Numero di tenant attualmente attivi: <strong><?php echo $data["numeroTenant"] ?></strong></p>

    <br>

    <a href="<?php echo URLROOT; ?>/Auth/register">Prova ANH-Ticket</a>
</body>
</html>