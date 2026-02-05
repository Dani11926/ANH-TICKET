<?php
// Carica Config
require_once 'config/config.php';

// Carica Librerie Base
require_once 'core/Core.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';

// Carica tutte le librerie installate con Composer (incluso PHPMailer)
require_once dirname(APPROOT) . '/vendor/autoload.php';
