<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/localization.php';

$currentLang = getCurrentLanguage();
$translations = getTranslations();
$opcodeMap = getOpcodeMap();
