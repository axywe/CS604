<?php
require_once 'translations.php';

// Get current language
$currentLang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!isset($translations[$currentLang])) {
    $currentLang = 'en';
}

// Save language in session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['lang'] = $currentLang;

include 'header.php';
?>

<div class="w-full max-w-4xl mx-auto z-10">
    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
        <h2 class="text-2xl font-semibold mb-6"><?php echo getTranslation('lecture-selection', $currentLang); ?></h2>
        <div class="space-y-4">
            <a href="lecture2a.php" class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-4 rounded-lg text-xl transition-transform transform hover:scale-105">
                <?php echo getTranslation('lecture2a-title', $currentLang); ?>
            </a>
            <a href="lecture2b.php" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-4 rounded-lg text-xl transition-transform transform hover:scale-105">
                <?php echo getTranslation('lecture2b-title', $currentLang); ?>
            </a>
        </div>
    </div>
</div>

</body>
</html>
