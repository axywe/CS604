(function (window, document) {
    const STORAGE_KEY = 'cs604-lang';
    const COOKIE_NAME = 'lang';
    const SUPPORTED_LANGUAGES = ['en', 'ru'];
    const DEFAULT_LANGUAGE = 'en';
    let currentLanguage = null;

    function normalizeLanguage(lang) {
        const value = (lang || '').toLowerCase();
        return SUPPORTED_LANGUAGES.includes(value) ? value : DEFAULT_LANGUAGE;
    }

    function readCookie(name) {
        const cookies = document.cookie ? document.cookie.split(';') : [];
        for (const cookie of cookies) {
            const trimmed = cookie.trim();
            if (!trimmed) {
                continue;
            }
            if (trimmed.startsWith(`${name}=`)) {
                return decodeURIComponent(trimmed.substring(name.length + 1));
            }
        }
        return null;
    }

    function writeCookie(name, value) {
        const maxAge = 60 * 60 * 24 * 365; // one year
        document.cookie = `${name}=${value}; path=/; max-age=${maxAge}; samesite=lax`;
    }

    function getStoredLanguage() {
        try {
            const stored = window.localStorage.getItem(STORAGE_KEY);
            if (stored) {
                return normalizeLanguage(stored);
            }
        } catch (error) {
            // localStorage may be unavailable (e.g., privacy mode)
        }

        const cookieValue = readCookie(COOKIE_NAME);
        if (cookieValue) {
            return normalizeLanguage(cookieValue);
        }

        return null;
    }

    function persistLanguage(lang) {
        const normalized = normalizeLanguage(lang);

        try {
            window.localStorage.setItem(STORAGE_KEY, normalized);
        } catch (error) {
            // Ignore storage errors
        }

        if (readCookie(COOKIE_NAME) !== normalized) {
            writeCookie(COOKIE_NAME, normalized);
        }

        currentLanguage = normalized;
        return normalized;
    }

    function highlightButtons(lang) {
        const buttons = document.querySelectorAll('.lang-btn');
        if (!buttons.length) {
            return;
        }

        buttons.forEach((button) => {
            button.classList.remove('bg-blue-500', 'text-white');
            button.classList.add('bg-gray-300', 'text-gray-700');
        });

        const activeButton = document.getElementById(`lang-${lang}`);
        if (activeButton) {
            activeButton.classList.add('bg-blue-500', 'text-white');
            activeButton.classList.remove('bg-gray-300', 'text-gray-700');
        }
    }

    function getCurrentLanguage() {
        if (currentLanguage) {
            return currentLanguage;
        }

        const stored = getStoredLanguage();
        if (stored) {
            currentLanguage = stored;
            return currentLanguage;
        }

        currentLanguage = normalizeLanguage(document.documentElement.lang);
        return currentLanguage;
    }

    function initializeLanguage() {
        const serverLanguage = normalizeLanguage(document.documentElement.lang);
        const storedLanguage = getStoredLanguage();

        if (storedLanguage && storedLanguage !== serverLanguage) {
            persistLanguage(storedLanguage);
            highlightButtons(storedLanguage);
            window.location.reload();
            return;
        }

        const activeLanguage = storedLanguage || serverLanguage;
        persistLanguage(activeLanguage);
        highlightButtons(activeLanguage);
    }

    function handleLanguageClick(event) {
        const target = event.currentTarget;
        const lang = normalizeLanguage(target.dataset.lang || target.id.replace('lang-', ''));
        const active = getCurrentLanguage();

        if (lang === active) {
            return;
        }

        persistLanguage(lang);
        highlightButtons(lang);
        window.location.reload();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initializeLanguage();

        document.querySelectorAll('.lang-btn').forEach((button) => {
            button.addEventListener('click', handleLanguageClick);
        });
    });

    window.LanguageManager = {
        getCurrentLanguage,
        persistLanguage,
        highlightButtons,
        normalizeLanguage,
    };
})(window, document);
