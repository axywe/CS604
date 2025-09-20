<?php
if (!function_exists('loadLocalizationData')) {
    function loadLocalizationData(): array
    {
        static $data = null;

        if ($data === null) {
            $translations = [];
            $opcodeMap = [];

            require __DIR__ . '/../translations.php';

            $data = [
                'translations' => $translations,
                'opcodeMap' => $opcodeMap,
            ];
        }

        return $data;
    }

    function getTranslations(): array
    {
        $data = loadLocalizationData();
        return $data['translations'];
    }

    function getOpcodeMap(): array
    {
        $data = loadLocalizationData();
        return $data['opcodeMap'];
    }

    function normalizeLanguage(?string $lang): string
    {
        $lang = strtolower($lang ?? '');
        $translations = getTranslations();

        if (array_key_exists($lang, $translations)) {
            return $lang;
        }

        return 'en';
    }

    function setCurrentLanguage(?string $lang = null): string
    {
        $normalized = normalizeLanguage($lang);

        $_SESSION['lang'] = $normalized;

        $cookieLang = $_COOKIE['lang'] ?? null;
        if ($cookieLang !== $normalized) {
            setcookie('lang', $normalized, time() + 365 * 24 * 60 * 60, '/');
            $_COOKIE['lang'] = $normalized;
        }

        return $normalized;
    }

    function getCurrentLanguage(): string
    {
        $candidate = $_COOKIE['lang'] ?? $_SESSION['lang'] ?? null;
        return setCurrentLanguage($candidate);
    }

    function getTranslation(string $key, ?string $lang = null, array $params = []): string
    {
        $translations = getTranslations();
        $language = normalizeLanguage($lang ?? getCurrentLanguage());

        $text = $translations[$language][$key] ?? $translations['en'][$key] ?? $key;

        foreach ($params as $param => $value) {
            $text = str_replace('{' . $param . '}', $value, $text);
        }

        return $text;
    }
}
