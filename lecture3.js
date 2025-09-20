const lecture3Scenarios = {
    memoryHierarchy: {
        nameKey: 'scenario-memory-hierarchy',
        descriptionKey: 'memory-hierarchy-description',
        steps: [
            {
                titleKey: 'memory-hierarchy-step-1-title',
                textKey: 'memory-hierarchy-step-1-text',
                highlight: ['cpu', 'l1'],
                statusUpdates: {
                    cpu: { key: 'lecture3-status-requesting', params: { address: '0x1A2C' } },
                    l1: { key: 'lecture3-status-l1-check', params: { line: '12' } }
                }
            },
            {
                titleKey: 'memory-hierarchy-step-2-title',
                textKey: 'memory-hierarchy-step-2-text',
                highlight: ['l1', 'l2'],
                statusUpdates: {
                    l1: { key: 'lecture3-status-miss' },
                    l2: { key: 'lecture3-status-l1-check', params: { line: '48' } }
                }
            },
            {
                titleKey: 'memory-hierarchy-step-3-title',
                textKey: 'memory-hierarchy-step-3-text',
                highlight: ['l2', 'memory'],
                statusUpdates: {
                    l2: { key: 'lecture3-status-fetching', params: { address: '0x1A20' } },
                    memory: { key: 'lecture3-status-memory-transfer' }
                }
            },
            {
                titleKey: 'memory-hierarchy-step-4-title',
                textKey: 'memory-hierarchy-step-4-text',
                highlight: ['cpu', 'l1', 'l2'],
                statusUpdates: {
                    l1: { key: 'lecture3-status-delivering' },
                    cpu: { key: 'lecture3-status-ready' },
                    l2: { key: 'lecture3-status-ready' }
                }
            }
        ]
    },
    cacheHit: {
        nameKey: 'scenario-cache-hit',
        descriptionKey: 'cache-hit-description',
        steps: [
            {
                titleKey: 'cache-hit-step-1-title',
                textKey: 'cache-hit-step-1-text',
                highlight: ['cpu', 'l1'],
                statusUpdates: {
                    cpu: { key: 'lecture3-status-requesting', params: { address: '0x3F20' } },
                    l1: { key: 'lecture3-status-l1-check', params: { line: '7' } }
                }
            },
            {
                titleKey: 'cache-hit-step-2-title',
                textKey: 'cache-hit-step-2-text',
                highlight: ['l1'],
                statusUpdates: {
                    l1: { key: 'lecture3-status-hit' }
                }
            },
            {
                titleKey: 'cache-hit-step-3-title',
                textKey: 'cache-hit-step-3-text',
                highlight: ['cpu', 'l1'],
                statusUpdates: {
                    l1: { key: 'lecture3-status-delivering' },
                    cpu: { key: 'lecture3-status-executing' }
                }
            },
            {
                titleKey: 'cache-hit-step-4-title',
                textKey: 'cache-hit-step-4-text',
                highlight: ['os', 'l1'],
                statusUpdates: {
                    os: { key: 'lecture3-status-os-metrics' },
                    l1: { key: 'lecture3-status-ready' },
                    cpu: { key: 'lecture3-status-ready' }
                }
            }
        ]
    },
    cacheMiss: {
        nameKey: 'scenario-cache-miss',
        descriptionKey: 'cache-miss-description',
        steps: [
            {
                titleKey: 'cache-miss-step-1-title',
                textKey: 'cache-miss-step-1-text',
                highlight: ['cpu', 'l1'],
                statusUpdates: {
                    cpu: { key: 'lecture3-status-requesting', params: { address: '0x8AC4' } },
                    l1: { key: 'lecture3-status-miss' }
                }
            },
            {
                titleKey: 'cache-miss-step-2-title',
                textKey: 'cache-miss-step-2-text',
                highlight: ['l2', 'memory'],
                statusUpdates: {
                    l2: { key: 'lecture3-status-miss' },
                    memory: { key: 'lecture3-status-fetching', params: { address: '0x8A80' } }
                }
            },
            {
                titleKey: 'cache-miss-step-3-title',
                textKey: 'cache-miss-step-3-text',
                highlight: ['memory', 'disk'],
                statusUpdates: {
                    memory: { key: 'lecture3-status-memory-transfer' },
                    disk: { key: 'lecture3-status-writeback' }
                }
            },
            {
                titleKey: 'cache-miss-step-4-title',
                textKey: 'cache-miss-step-4-text',
                highlight: ['cpu', 'l1', 'l2'],
                statusUpdates: {
                    l1: { key: 'lecture3-status-delivering' },
                    cpu: { key: 'lecture3-status-ready' },
                    l2: { key: 'lecture3-status-l2-update' }
                }
            }
        ]
    },
    virtualMemory: {
        nameKey: 'scenario-virtual-memory',
        descriptionKey: 'virtual-memory-description',
        steps: [
            {
                titleKey: 'virtual-memory-step-1-title',
                textKey: 'virtual-memory-step-1-text',
                highlight: ['cpu', 'tlb'],
                statusUpdates: {
                    cpu: { key: 'lecture3-status-requesting', params: { address: '0x7F45' } },
                    tlb: { key: 'lecture3-status-l1-check', params: { line: '21' } }
                }
            },
            {
                titleKey: 'virtual-memory-step-2-title',
                textKey: 'virtual-memory-step-2-text',
                highlight: ['tlb', 'page-table', 'os'],
                statusUpdates: {
                    tlb: { key: 'lecture3-status-miss' },
                    'page-table': { key: 'lecture3-status-page-fault', params: { page: '0x7F' } },
                    os: { key: 'lecture3-status-os-handling' }
                }
            },
            {
                titleKey: 'virtual-memory-step-3-title',
                textKey: 'virtual-memory-step-3-text',
                highlight: ['os', 'disk', 'memory'],
                statusUpdates: {
                    os: { key: 'lecture3-status-os-handling' },
                    disk: { key: 'lecture3-status-disk-fetch', params: { page: '0x7F' } },
                    memory: { key: 'lecture3-status-memory-allocate', params: { frame: '14' } }
                }
            },
            {
                titleKey: 'virtual-memory-step-4-title',
                textKey: 'virtual-memory-step-4-text',
                highlight: ['page-table', 'tlb', 'cpu'],
                statusUpdates: {
                    'page-table': { key: 'lecture3-status-page-loaded', params: { page: '0x7F', frame: '14' } },
                    tlb: { key: 'lecture3-status-tlb-update' },
                    cpu: { key: 'lecture3-status-ready' }
                }
            }
        ]
    }
};

function getTranslation(key, lang = 'en', params = {}) {
    if (!translations) {
        return key;
    }

    let language = translations[lang] ? lang : 'en';
    let text = translations[language] && translations[language][key];

    if (!text && language !== 'en') {
        text = translations['en'] && translations['en'][key];
    }

    if (!text) {
        return key;
    }

    if (params && typeof params === 'object') {
        Object.entries(params).forEach(([paramKey, value]) => {
            const regex = new RegExp(`\\{${paramKey}\\}`, 'g');
            text = text.replace(regex, value);
        });
    }

    return text;
}

document.addEventListener('DOMContentLoaded', () => {
    const scenarioSelect = document.getElementById('scenario-select');
    const stepBtn = document.getElementById('step-btn');
    const resetBtn = document.getElementById('reset-btn');
    const explanationText = document.getElementById('explanation-text');
    const scenarioNameEl = document.getElementById('scenario-name');
    const scenarioDescriptionEl = document.getElementById('scenario-description');
    const stepIndicatorEl = document.getElementById('step-indicator');
    const timelineList = document.getElementById('timeline-list');

    if (window.LanguageManager) {
        currentLang = LanguageManager.getCurrentLanguage();
    }

    const componentElements = {};
    const statusElements = {};

    document.querySelectorAll('[data-component]').forEach(card => {
        const id = card.dataset.component;
        componentElements[id] = card;
        statusElements[id] = card.querySelector('[data-component-status]');
    });

    function resetStatuses() {
        Object.values(statusElements).forEach(statusEl => {
            if (statusEl) {
                statusEl.textContent = getTranslation('status-idle', currentLang);
            }
        });
    }

    function resetHighlights() {
        Object.values(componentElements).forEach(card => {
            card.classList.remove('ring-4', 'ring-blue-400', 'shadow-xl', 'bg-blue-50', 'scale-105', 'border-blue-400');
            card.classList.add('border-gray-200');
        });
    }

    function highlightComponents(ids = []) {
        ids.forEach(id => {
            const card = componentElements[id];
            if (card) {
                card.classList.remove('border-gray-200');
                card.classList.add('ring-4', 'ring-blue-400', 'shadow-xl', 'bg-blue-50', 'scale-105', 'border-blue-400');
            }
        });
    }

    function applyStatusUpdates(updates = {}) {
        Object.entries(updates).forEach(([componentId, value]) => {
            const statusEl = statusElements[componentId];
            if (!statusEl) {
                return;
            }

            let key;
            let params = {};
            if (typeof value === 'string') {
                key = value;
            } else if (value && typeof value === 'object') {
                key = value.key;
                params = value.params || {};
            }

            if (key) {
                statusEl.textContent = getTranslation(key, currentLang, params);
            }
        });
    }

    function buildTimeline(scenario) {
        timelineList.innerHTML = '';
        scenario.steps.forEach(step => {
            const item = document.createElement('li');
            item.className = 'timeline-step border border-gray-200 rounded-lg px-3 py-2 bg-white transition';
            const title = getTranslation(step.titleKey, currentLang);
            item.innerHTML = `<span class="block font-semibold">${title}</span>`;
            timelineList.appendChild(item);
        });
    }

    function updateTimelineHighlight(index) {
        const items = timelineList.querySelectorAll('.timeline-step');
        items.forEach((item, idx) => {
            item.classList.remove('bg-blue-100', 'border-blue-400', 'text-blue-900', 'font-semibold', 'bg-green-100', 'border-green-300', 'text-green-800', 'text-gray-600');
            item.classList.add('text-gray-600');
            if (idx < index) {
                item.classList.remove('text-gray-600');
                item.classList.add('bg-green-100', 'border-green-300', 'text-green-800');
            } else if (idx === index) {
                item.classList.remove('text-gray-600');
                item.classList.add('bg-blue-100', 'border-blue-400', 'text-blue-900', 'font-semibold');
            }
        });
    }

    function updateStepIndicator(scenario, index) {
        stepIndicatorEl.textContent = getTranslation('lecture3-step-indicator', currentLang, {
            current: index + 1,
            total: scenario.steps.length
        });
    }

    function setExplanation(step) {
        explanationText.textContent = getTranslation(step.textKey, currentLang);
    }

    function updateScenarioInfo(scenario) {
        scenarioNameEl.textContent = getTranslation(scenario.nameKey, currentLang);
        scenarioDescriptionEl.textContent = getTranslation(scenario.descriptionKey, currentLang);
    }

    function updateStepControls(scenario, index) {
        if (index >= scenario.steps.length - 1) {
            stepBtn.disabled = true;
            stepBtn.classList.add('disabled-btn');
        } else {
            stepBtn.disabled = false;
            stepBtn.classList.remove('disabled-btn');
        }
    }

    function setStep(index) {
        const scenario = lecture3Scenarios[currentScenarioId];
        if (!scenario) {
            return;
        }
        const targetIndex = Math.max(0, Math.min(index, scenario.steps.length - 1));
        currentStepIndex = targetIndex;
        const step = scenario.steps[targetIndex];

        resetStatuses();
        resetHighlights();
        highlightComponents(step.highlight);
        applyStatusUpdates(step.statusUpdates);
        setExplanation(step);
        updateStepIndicator(scenario, targetIndex);
        updateTimelineHighlight(targetIndex);
        updateStepControls(scenario, targetIndex);
    }

    function loadScenario(scenarioId) {
        const scenario = lecture3Scenarios[scenarioId];
        if (!scenario) {
            return;
        }
        currentScenarioId = scenarioId;
        currentStepIndex = 0;
        updateScenarioInfo(scenario);
        buildTimeline(scenario);
        setStep(0);
    }

    function handleNextStep() {
        const scenario = lecture3Scenarios[currentScenarioId];
        if (!scenario) {
            return;
        }
        if (currentStepIndex < scenario.steps.length - 1) {
            setStep(currentStepIndex + 1);
        }
    }

    function handleReset() {
        setStep(0);
    }

    function handleScenarioChange(event) {
        loadScenario(event.target.value);
    }

    let currentScenarioId = scenarioSelect.value;
    let currentStepIndex = 0;

    scenarioSelect.addEventListener('change', handleScenarioChange);
    stepBtn.addEventListener('click', handleNextStep);
    resetBtn.addEventListener('click', handleReset);

    loadScenario(currentScenarioId);
});
