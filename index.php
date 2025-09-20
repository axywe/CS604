<?php
require_once __DIR__ . '/includes/bootstrap.php';

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
            <a href="lecture3.php" class="block w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-4 px-4 rounded-lg text-xl transition-transform transform hover:scale-105">
                <?php echo getTranslation('lecture3-title', $currentLang); ?>
            </a>
        </div>
    </div>
</div>

</body>
</html>
