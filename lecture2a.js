const elements = {
    regPC: document.querySelector('#reg-pc p:last-child'),
    regIR: document.querySelector('#reg-ir p:last-child'),
    regMAR: document.querySelector('#reg-mar p:last-child'),
    regMBR: document.querySelector('#reg-mbr p:last-child'),
    regAC: document.querySelector('#reg-ac p:last-child'),
    regIOAR: document.querySelector('#reg-ioar p:last-child'),
    regIOBR: document.querySelector('#reg-iobr p:last-child'),
    regFlags: document.querySelector('#reg-flags p:last-child'),
    regSP: document.querySelector('#reg-sp p:last-child'), // Stack Pointer
    memoryGrid: document.getElementById('memory-grid'),
    stackGrid: document.getElementById('stack-grid'),
    stepBtn: document.getElementById('step-btn'),
    resetBtn: document.getElementById('reset-btn'),
    scenarioSelect: document.getElementById('scenario-select'),
    explanationText: document.getElementById('explanation-text'),
    cycleStateText: document.getElementById('cycle-state-text'),
    printer: {
        status: document.getElementById('printer-status'),
        progress: document.getElementById('printer-progress'),
        device: document.getElementById('printer'),
    },
    disk: {
        status: document.getElementById('disk-status'),
        progress: document.getElementById('disk-progress'),
        device: document.getElementById('disk'),
    },
    network: {
        status: document.getElementById('network-status'),
        progress: document.getElementById('network-progress'),
        device: document.getElementById('network'),
    },
    dma: {
        mar: document.querySelector('#dma-mar p:last-child'),
        count: document.querySelector('#dma-count p:last-child'),
        status: document.querySelector('#dma-status span'),
        panel: document.getElementById('dma-controller-panel'),
    },
    ioPanel: document.getElementById('io-device-panel'),
    busContainer: document.getElementById('bus-container'),
    builderModal: document.getElementById('builder-modal'),
    jsonEditorModal: document.getElementById('json-editor-modal'),
    ioDevicesContainer: document.getElementById('io-devices-container'),
    stackPanel: document.getElementById('stack-panel'),
    ioDevices: {}, // To be populated dynamically
};

let customScenario = {
    name: "Custom Scenario",
    pc_start: 0x100,
    memory: {},
    interrupts: true,
    hide: [],
    ioDevices: {},
    explanation: ""
};

// currentLang будет передан из PHP

// CPU and Memory State - Enhanced with interrupt mask and flags
let cpu = {
    pc: 0, ir: 0, mar: 0, mbr: 0, ac: 0, ioar: 0, iobr: 0,
    sp: 0xF00, // Stack Pointer initial value
    interruptsEnabled: true,
    interruptMask: false, // Новый флаг маски прерываний
    flags: 0, // Служебные флаги (биты для различных состояний)
    isHalted: false,
    currentInterruptPriority: 0, // 0 = no interrupt active
};

let dma = {
    mar: 0,
    count: 0,
    active: false,
    sourceDevice: null,
};

let memory = new Array(0x1000).fill(0);
let cycleState = 'START';
let currentScenario = 'vonNeumann';
let pendingInterrupts = [];

const DEFAULT_IO_OPERATIONS = {
    printer: { active: false, progress: 0, duration: 20, priority: 2, isr: 0x050, isrState: 'idle' },
    disk: { active: false, progress: 0, duration: 20, priority: 4, isr: 0x060, isrState: 'idle' },
    network: { active: false, progress: 0, duration: 20, priority: 5, isr: 0x070, isrState: 'idle' },
};

let ioOperations = JSON.parse(JSON.stringify(DEFAULT_IO_OPERATIONS));


// Расширенные сценарии с новыми возможностями
const scenarios = {
    vonNeumann: {
        name: "Von Neumann Concept",
        pc_start: 0x100,
        memory: {
            // Instructions
            0x100: 0x1104, // LOAD data from 0x104
            0x101: 0x5105, // ADD data from 0x105
            0x102: 0x2106, // STORE result to 0x106
            0x103: 0x0000, // HALT
            // Data
            0x104: 0x0042, // 42
            0x105: 0x0025, // 25
            0x106: 0x0000, // result placeholder
        },
        interrupts: false,
        hide: ['ioPanel', 'dma.panel', 'reg-ioar', 'reg-iobr', 'reg-flags']
    },
    program1: {
        name: "Simple Addition",
        pc_start: 0x300,
        memory: {
            0x300: 0x1940, 0x301: 0x5941, 0x302: 0x2941, 0x303: 0x0000,
            0x940: 0x0003, 0x941: 0x0002,
        },
        interrupts: true,
        hide: ['dma.panel', 'reg-ioar', 'reg-iobr']
    },
    program2: {
        name: "I/O Wait (No Interrupts)",
        pc_start: 0x100,
        memory: {
            0x100: 0x1900, 0x101: 0xF001, 0x102: 0x1901, 0x103: 0x0000,
            0x900: 0x00AA, 0x901: 0x00BB,
        },
        interrupts: false,
        hide: ['dma.panel', 'reg-flags']
    },
    program3: {
        name: "I/O with Interrupts",
        pc_start: 0x100,
        memory: {
            0x100: 0x1900, // LOAD from 0x900
            0x101: 0xF001, // START Printer I/O
            0x102: 0x1901, // LOAD from 0x901
            0x103: 0x5902, // ADD from 0x902
            0x104: 0x5902, // ADD from 0x902 again
            0x105: 0x5902, // ADD from 0x902 again
            0x106: 0x5902, // ADD from 0x902 again
            0x107: 0x0000, // HALT
            0x050: 0xEEEE, // IRET
            0x900: 0x00AA,
            0x901: 0x00BB,
            0x902: 0x0001,
        },
        interrupts: true,
        hide: ['dma.panel']
    },
    program4: {
        name: "Prioritized Interrupts",
        pc_start: 0x200,
        memory: {
            // User program starts two I/O ops
            0x200: 0xF001, // Start Printer I/O
            0x201: 0xF003, // Start Network I/O
            0x202: 0xF002, // Start Disk I/O (Prio 4, 15 ticks)
            0x203: 0x100A, // Some work
            0x204: 0x100B,
            0x205: 0x0000, // HALT
            // ISRs - no interrupt disabling to allow preemption
            0x050: 0x58F0, // Printer ISR (low prio) - ADD
            0x051: 0xEEEE, // IRET
            0x060: 0x58F1, // Disk ISR (mid prio) - ADD
            0x061: 0xEEEE, // IRET
            0x070: 0x58F2, // Network ISR (high prio) - ADD
            0x071: 0xEEEE, // IRET
            // Data locations for ISRs
            0x8F0: 0x000B, // 11 for printer
            0x8F1: 0x00DE, // 222 for disk
            0x8F2: 0x0AAA, // AAA for network
        },
        interrupts: true,
        hide: ['dma.panel']
    },
    dma: {
        name: "Direct Memory Access (DMA)",
        pc_start: 0x400,
        memory: {
            0x400: 0xF120, // 1. Tell DMA to transfer 5 words from Disk to 0x500
            0x401: 0x1001, // 2. Do some other work... (executes while DMA runs)
            0x402: 0x1002,
            0x403: 0x0000, // 3. HALT
            0x080: 0xEEEE, // DMA Interrupt Service Routine -> IRET
            // Disk's data buffer in memory
            0x800: 0xAAAA, 0x801: 0xBBBB, 0x802: 0xCCCC, 0x803: 0xDDDD, 0x804: 0xFFFF,
        },
        interrupts: true,
        hide: ['reg-ioar', 'reg-iobr']
    },
    ioRegisters: {
        name: "I/O with Registers",
        pc_start: 0x600,
        memory: {
            0x600: 0x1950, // LOAD AC with data from 0x950
            0x601: 0x9001, // LOAD I/OAR with device address 1 (printer)
            0x602: 0xA000, // MOVE AC to I/OBR
            0x603: 0xB000, // START I/O operation
            0x604: 0x0000, // HALT
            0x950: 0xABCD, // Data to send to printer
            0x050: 0xEEEE, // Printer Interrupt Service Routine -> IRET
        },
        interrupts: true,
        hide: ['dma.panel']
    },
    conditionalJumps: {
        name: "Conditional Jumps",
        pc_start: 0x700,
        memory: {
            0x700: 0x1800, // LOAD AC with 0 from 0x800
            0x701: 0x4705, // JUMPZ to 0x705 (should jump)
            0x702: 0x1801, // LOAD AC with non-zero (skipped)
            0x703: 0x3707, // JUMP to 0x707 (skipped)
            0x704: 0x0000, // HALT (skipped)
            0x705: 0x1801, // LOAD AC with non-zero from 0x801
            0x706: 0x4709, // JUMPZ to 0x709 (should not jump)
            0x707: 0x5802, // ADD value from 0x802
            0x708: 0x2803, // STORE result to 0x803
            0x709: 0x0000, // HALT
            // Data
            0x800: 0x0000, // Zero for first test
            0x801: 0x0005, // Non-zero value
            0x802: 0x0003, // Value to add
            0x803: 0x0000, // Result location
        },
        interrupts: true,
        hide: ['ioPanel', 'dma.panel', 'reg-ioar', 'reg-iobr']
    },
    flagsAndJumps: {
        name: "Flags and Conditional Jumps",
        pc_start: 0x700,
        memory: {
            // 1. Тест флага Z (Zero)
            0x700: 0x1800, // LOAD AC, 5 (from 0x800)
            0x701: 0x5801, // ADD -5 (from 0x801) -> AC becomes 0, Z flag SET
            0x702: 0x4704, // JUMPZ to 0x704 (should jump)
            0x703: 0x0000, // HALT (should be skipped)
            // 2. Тест флага N (Negative)
            0x704: 0x1802, // LOAD AC, 10 (from 0x802)
            0x705: 0x5803, // ADD -20 (from 0x803) -> AC becomes -10, N flag SET
            // 3. Тест флага V (Overflow)
            0x706: 0x1804, // LOAD AC, 0x7FFF (32767)
            0x707: 0x5805, // ADD 1 (from 0x805) -> Overflow, V flag SET
            0x708: 0x0000, // HALT
            // Data
            0x800: 0x0005, // 5
            0x801: 0xFFFB, // -5 (в доп. коде)
            0x802: 0x000A, // 10
            0x803: 0xFFEC, // -20 (в доп. коде)
            0x804: 0x7FFF, // 32767
            0x805: 0x0001, // 1
        },
        interrupts: true,
        hide: ['ioPanel', 'dma.panel', 'reg-ioar', 'reg-iobr']
    }
};

// --- UTILITY FUNCTIONS ---
const toHex = (val, len = 4) => val.toString(16).toUpperCase().padStart(len, '0');

function getElementCoords(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return null;
    const rect = el.getBoundingClientRect();
    const containerRect = elements.busContainer.getBoundingClientRect();
    return {
        x: rect.left - containerRect.left + rect.width / 2,
        y: rect.top - containerRect.top + rect.height / 2,
        width: rect.width,
        height: rect.height,
    };
}

async function showBusTransfer(startId, endId, text, type) {
    const startCoords = getElementCoords(startId);
    const endCoords = getElementCoords(endId);
    if (!startCoords || !endCoords) return;

    const bus = document.createElement('div');
    bus.className = `bus ${type}-bus`;
    bus.textContent = text;
    elements.busContainer.appendChild(bus);

    const x1 = startCoords.x;
    const y1 = startCoords.y;
    const x2 = endCoords.x;
    const y2 = endCoords.y;

    const angle = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI;
    const distance = Math.sqrt((x2 - x1)**2 + (y2 - y1)**2);

    bus.style.width = `${distance}px`;
    bus.style.left = `${x1}px`;
    bus.style.top = `${y1 - bus.offsetHeight / 2}px`;
    bus.style.transformOrigin = 'left center';
    bus.style.transform = `rotate(${angle}deg)`;
    
    bus.classList.add('active');
    
    await new Promise(resolve => setTimeout(resolve, 800));

    bus.classList.remove('active');
    await new Promise(resolve => setTimeout(resolve, 300));

    elements.busContainer.removeChild(bus);
}

function setLanguage(lang) {
    currentLang = lang;
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white');
        btn.classList.add('bg-gray-300', 'text-gray-700');
    });
    document.getElementById(`lang-${lang}`).classList.add('bg-blue-500', 'text-white');
    document.getElementById(`lang-${lang}`).classList.remove('bg-gray-300', 'text-gray-700');
    
    // Обновляем опции сценариев
    updateScenarioOptions();
    
    localStorage.setItem('cs604-lang', lang);
    resetSimulation(); // Reload simulation to apply new explanation language
}

function updateScenarioOptions() {
    const scenarioSelect = document.getElementById('scenario-select');
    const scenarios = {
        'vonNeumann': getTranslation('scenario-0', currentLang),
        'program1': getTranslation('scenario-1', currentLang),
        'program2': getTranslation('scenario-2', currentLang),
        'program3': getTranslation('scenario-3', currentLang),
        'program4': getTranslation('scenario-4', currentLang),
        'dma': getTranslation('scenario-5', currentLang),
        'ioRegisters': getTranslation('scenario-6', currentLang),
        'conditionalJumps': getTranslation('scenario-7', currentLang),
        'flagsAndJumps': getTranslation('scenario-8', currentLang)
    };
    
    Array.from(scenarioSelect.options).forEach(option => {
        if (scenarios[option.value]) {
            option.textContent = scenarios[option.value];
        }
    });
}

function updateUI() {
    elements.regPC.textContent = toHex(cpu.pc);
    elements.regIR.textContent = toHex(cpu.ir);
    elements.regMAR.textContent = toHex(cpu.mar);
    elements.regMBR.textContent = toHex(cpu.mbr);
    elements.regAC.textContent = toHex(cpu.ac);
    elements.regIOAR.textContent = toHex(cpu.ioar);
    elements.regIOBR.textContent = toHex(cpu.iobr);
    elements.regSP.textContent = toHex(cpu.sp);
    
    // Обновление регистра флагов
    if (elements.regFlags) {
        let flagsText = toHex(cpu.flags);
        let flagNames = [];
        
        if (cpu.flags & 0x01) flagNames.push('Z'); // Zero flag
        if (cpu.flags & 0x02) flagNames.push('N'); // Negative flag  
        if (cpu.flags & 0x04) flagNames.push('V'); // Overflow flag
        if (cpu.interruptMask) flagNames.push('I'); // Interrupt disabled
        
        if (flagNames.length > 0) {
            flagsText += ` (${flagNames.join(',')})`;
        }
        
        elements.regFlags.textContent = flagsText;
    }

    elements.dma.mar.textContent = toHex(dma.mar);
    elements.dma.count.textContent = dma.count;

    const dmaStatusSpan = document.querySelector('#dma-status span');
    if(dma.active){
        dmaStatusSpan.textContent = getTranslation('dma-status-transferring', currentLang, {device: dma.sourceDevice});
    } else {
        dmaStatusSpan.textContent = getTranslation('status-idle', currentLang);
    }
    
    document.querySelectorAll('.highlight-pc, .highlight-mar, .highlight-data-read, .highlight-data-write').forEach(el => {
        el.classList.remove('highlight-pc', 'highlight-mar', 'highlight-data-read', 'highlight-data-write');
    });
    
    const pcCell = document.getElementById(`mem-${toHex(cpu.pc)}`);
    if (pcCell) pcCell.classList.add('highlight-pc');

    const marCell = document.getElementById(`mem-${toHex(cpu.mar)}`);
    if (marCell) marCell.classList.add('highlight-mar');
}

function updateStackUI() {
    if (!elements.stackGrid) return; 
    elements.stackGrid.innerHTML = '';
    const sp = cpu.sp;
    const range = 4; // Words to show above/below SP

    // Display memory from SP+range down to SP-range
    for (let i = range; i >= -range; i--) {
        const addr = sp + i;
        if (addr >= 0 && addr < memory.length) {
            const val = memory[addr] || 0;
            const cell = document.createElement('div');
            cell.id = `stack-${toHex(addr)}`;
            let classNames = 'memory-cell bg-gray-200 p-2 rounded transition-all duration-300';
            
            // Highlight the cell pointed to by SP
            if (addr === sp) {
                classNames += ' bg-orange-200 border-2 border-orange-500';
            }
            
            cell.className = classNames;
            cell.innerHTML = `<span class="font-bold text-gray-500">${toHex(addr)}:</span> <span class="font-mono">${toHex(val)}</span>`;
            elements.stackGrid.appendChild(cell);
        }
    }
}

function updateMemoryUI() {
    elements.memoryGrid.innerHTML = '';
    let displayAddresses = new Set();
    for(let i = -4; i < 8; i++) {
        let addr = cpu.pc + i;
        if(addr >=0 && addr < memory.length) displayAddresses.add(addr);
    }
    Object.keys(scenarios[currentScenario].memory).forEach(addr => displayAddresses.add(parseInt(addr)));
    
    // Always show stack area
    for(let i = 0; i < 8; i++) {
        let addr = cpu.sp + i;
        if(addr >= 0 && addr < memory.length) displayAddresses.add(addr);
        let addr_before = cpu.sp - i;
        if(addr_before >= 0 && addr_before < memory.length) displayAddresses.add(addr_before);
    }


    const sortedAddresses = Array.from(displayAddresses).sort((a,b)=>a-b);

    // Если активен сценарий DMA, показываем целевую область памяти.
    if (currentScenario === 'dma') {
        const dma_target_start = 0x500;
        const dma_target_count = 5;
        for (let i = 0; i < dma_target_count; i++) {
            if (!displayAddresses.has(dma_target_start + i)) {
                 // Добавляем, только если еще не добавлено
                sortedAddresses.push(dma_target_start + i);
            }
        }
        sortedAddresses.sort((a,b)=>a-b); // Пересортировываем
    }

    sortedAddresses.forEach(addr => {
        const val = memory[addr] || 0;
        const cell = document.createElement('div');
        cell.id = `mem-${toHex(addr)}`;
        cell.className = 'memory-cell bg-gray-200 p-2 rounded';
        cell.innerHTML = `<span class="font-bold text-gray-500">${toHex(addr)}:</span> <span class="font-mono">${toHex(val)}</span>`;
        elements.memoryGrid.appendChild(cell);
    });
    updateUI();
    updateStackUI(); // Update stack along with memory
}

function setExplanation(textKey, state = '', params = {}) {
    let text = getTranslation(textKey, currentLang, params);
    elements.explanationText.textContent = text;
    const stateText = getTranslation('cycle-state', currentLang, {state: state});
    elements.cycleStateText.textContent = state ? stateText : '';
}

function highlightRegister(reg, temp = false) {
    const regEl = document.getElementById(`reg-${reg.toLowerCase()}`);
    if(regEl) {
        regEl.classList.add('highlight-active');
        if (temp) {
            setTimeout(() => regEl.classList.remove('highlight-active'), 1000);
        }
    }
}

function removeAllHighlights() {
    document.querySelectorAll('.highlight-active').forEach(el => el.classList.remove('highlight-active'));
}

// Функция для получения переводов из PHP (заглушка, так как это JavaScript)
function getTranslation(key, lang = 'en', params = {}) {
    // Эта функция будет переопределена через PHP для получения переводов
    return translations[lang] && translations[lang][key] ? 
           translations[lang][key].replace(/\{(\w+)\}/g, (match, p1) => params[p1] || match) : 
           key;
}

// --- SIMULATION LOGIC ---

function resetSimulation() {
    removeAllHighlights();
    const scenario = scenarios[currentScenario];

    // Handle custom scenarios' I/O devices
    if (scenario.ioDevices) { // If the property exists, even if it's an empty object
        ioOperations = JSON.parse(JSON.stringify(scenario.ioDevices)); // Deep copy
    } else {
        // Reset to default for built-in scenarios that don't have the ioDevices property
        ioOperations = JSON.parse(JSON.stringify(DEFAULT_IO_OPERATIONS));
    }
    
    // Dynamically create I/O device UI
    elements.ioDevicesContainer.innerHTML = '';
    elements.ioDevices = {}; // Reset references
    for (const deviceName in ioOperations) {
        createIoDeviceElement(deviceName, ioOperations[deviceName].priority);
    }


    cpu = {
        pc: scenario.pc_start,
        ir: 0, mar: 0, mbr: 0, ac: 0, ioar: 0, iobr: 0,
        sp: 0xF00, // Reset Stack Pointer
        isHalted: false,
        interruptsEnabled: scenario.interrupts,
        interruptMask: false, // Флаг маски прерываний
        flags: 0, // Служебные флаги
        currentInterruptPriority: 0,
    };
    memory.fill(0);
    for (const addr in scenario.memory) {
        memory[parseInt(addr)] = scenario.memory[addr];
    }

    dma = { mar: 0, count: 0, active: false, sourceDevice: null };
    
    pendingInterrupts = [];
    for(const device in ioOperations) {
        ioOperations[device].active = false;
        ioOperations[device].progress = 0;
        ioOperations[device].isrState = 'idle';
        updateIoDeviceUI(device);
        if (elements.ioDevices[device]) {
             elements.ioDevices[device].device.classList.remove('interrupting');
        }
    }

    cycleState = 'START';
    updateMemoryUI();
    updateStackUI();
    
    // Получаем начальное объяснение
    let initialExplanationKey = 'scenario-loaded';
    let params = {};
    
    switch(currentScenario) {
        case 'vonNeumann': initialExplanationKey = 'scenario-von-neumann-init'; break;
        case 'program1': initialExplanationKey = 'scenario-simple-addition-init'; break;
        case 'program2': initialExplanationKey = 'scenario-io-wait-init'; break;
        case 'program3': initialExplanationKey = 'scenario-interrupts-init'; break;
        case 'program4': initialExplanationKey = 'scenario-prioritized-interrupts-init'; break;
        case 'dma': initialExplanationKey = 'scenario-dma-init'; break;
        case 'ioRegisters': initialExplanationKey = 'scenario-io-registers-init'; break;
        case 'conditionalJumps': initialExplanationKey = 'scenario-conditional-jumps-init'; break;
        case 'flagsAndJumps': initialExplanationKey = 'scenario-flags-and-jumps-init'; break;
        default: 
            initialExplanationKey = 'scenario-loaded';
            params = { scenarioName: document.querySelector(`#scenario-select option[value="${currentScenario}"]`).textContent };
    }

    setExplanation(initialExplanationKey, '', params);
    elements.stepBtn.disabled = false;
    elements.stepBtn.classList.remove('disabled-btn');
    isStepInProgress = false; // Сбрасываем флаг блокировки

    // Hide/show panels based on scenario
    const allPanels = {
        'ioPanel': elements.ioPanel,
        'dma.panel': elements.dma.panel,
        'reg-ioar': document.getElementById('reg-ioar'),
        'reg-iobr': document.getElementById('reg-iobr'),
        'reg-flags': document.getElementById('reg-flags'),
        'reg-sp': document.getElementById('reg-sp'),
        'stackPanel': elements.stackPanel
    };

    Object.values(allPanels).forEach(p => {
        if (p) p.classList.remove('hidden-scenario');
    });
    if (scenario.hide) {
        scenario.hide.forEach(id => {
            const el = id.includes('.') ? elements[id.split('.')[0]][id.split('.')[1]] : allPanels[id];
            if(el) el.classList.add('hidden-scenario');

            // If SP is hidden, also hide the stack panel
            if (id === 'reg-sp') {
                if(allPanels['stackPanel']) allPanels['stackPanel'].classList.add('hidden-scenario');
            }
        });
    }

    // Special rule for DMA panel based on hide array
    if (scenario.hide && !scenario.hide.includes('dma.panel')) {
        elements.dma.panel.classList.remove('hidden');
    } else {
        elements.dma.panel.classList.add('hidden');
    }
}

function tickIO() {
    for(const deviceName in ioOperations) {
        const device = ioOperations[deviceName];
        if(device.active) {
            device.progress++;
            updateIoDeviceUI(deviceName);
            if(device.progress >= device.duration) {
                device.active = false;
                device.progress = 0;
                if(cpu.interruptsEnabled && !cpu.interruptMask) {
                   pendingInterrupts.push({ name: deviceName, priority: device.priority, isr: device.isr });
                   if (elements.ioDevices[deviceName]) {
                        elements.ioDevices[deviceName].device.classList.add('interrupting');
                   }
                   setExplanation('io-complete-interrupt', 'INTERRUPT PENDING', { deviceName: deviceName });
                } else {
                   const deviceStatusEl = document.getElementById(`${deviceName.toLowerCase()}-status`);
                   if(deviceStatusEl) {
                        deviceStatusEl.textContent = getTranslation('status-waiting', currentLang);
                   }
                }
                updateIoDeviceUI(deviceName);
            }
        }
    }
}

function tickDMA() {
    if (!dma.active) return;

    // In a real system, DMA would lock the bus. We simulate one word transfer per "tick"
    const data = memory[0x800 + (5 - dma.count)]; // Get data from disk buffer
    memory[dma.mar] = data;
    
    const memCellId = `mem-${toHex(dma.mar)}`;
    showBusTransfer('disk', memCellId, `DATA: ${toHex(data)}`, 'data');
    showBusTransfer('dma-controller-panel', memCellId, `ADDR: ${toHex(dma.mar)}`, 'address');

    dma.mar++;
    dma.count--;

    if (dma.count <= 0) {
        dma.active = false;
        pendingInterrupts.push({ name: 'DMA Controller', priority: 6, isr: 0x080 }); // Highest priority
        setExplanation('dma-complete', 'DMA COMPLETE');
    }
    updateMemoryUI();
    updateStackUI();
}

function updateIoDeviceUI(deviceName){
    const device = ioOperations[deviceName];
    const ui = elements.ioDevices[deviceName];
    if (!ui) return; 

    const pendingInterrupt = pendingInterrupts.find(p => p.name === deviceName);
    const statusEl = ui.status;

    ui.device.classList.remove('waiting', 'interrupting');

    if (device.active) {
        statusEl.textContent = getTranslation('status-processing', currentLang);
        ui.device.classList.add('waiting');
    } else if (device.isrState === 'running') {
        statusEl.textContent = getTranslation('status-servicing', currentLang);
    } else if (device.isrState === 'preempted') {
        statusEl.textContent = getTranslation('status-pending', currentLang);
    } else if (pendingInterrupt) {
        const highestPendingPriority = Math.max(0, ...pendingInterrupts.map(p => p.priority));
        if (pendingInterrupt.priority < highestPendingPriority || pendingInterrupt.priority <= cpu.currentInterruptPriority) {
            statusEl.textContent = getTranslation('status-pending', currentLang);
        } else {
            statusEl.textContent = getTranslation('status-interrupting', currentLang);
            ui.device.classList.add('interrupting');
        }
    } else {
        statusEl.textContent = getTranslation('status-idle', currentLang);
    }

    const progressPercent = device.duration > 0 ? (device.progress / device.duration) * 100 : 0;
    ui.progress.style.width = `${progressPercent}%`;
}

// Переменная для предотвращения множественных нажатий
let isStepInProgress = false;

async function simulationStep() {
    if (isStepInProgress) return;
    isStepInProgress = true;
    elements.stepBtn.disabled = true;
    elements.stepBtn.classList.add('disabled-btn');

    try {
        // 1. Устройства всегда работают параллельно.
        tickIO();
        tickDMA();

        // 2. Проверяем прерывания в первую очередь. 
        // Процессор должен реагировать на них даже в состоянии HALT.
        if ((cycleState === 'START' || cpu.isHalted) && cpu.interruptsEnabled && !cpu.interruptMask && pendingInterrupts.length > 0) {
            pendingInterrupts.sort((a, b) => b.priority - a.priority);
            const interrupt = pendingInterrupts[0];

            if (interrupt.priority > cpu.currentInterruptPriority) {
                if(cpu.isHalted) cpu.isHalted = false; // "Пробуждаем" процессор
                cycleState = 'INTERRUPT_START';
                simulationStep.currentInterrupt = interrupt;
            }
        }
        
        // 3. Если процессор остановлен, он не выполняет инструкции, только ждет прерывания.
        if (cpu.isHalted) {
            setExplanation("halted-waiting", "EXECUTION HALTED (Waiting for interrupts)");
            return; 
        }

        removeAllHighlights();
        
        switch (cycleState) {
            case 'START':
                // Логика опроса для режима без прерываний остается здесь, т.к. это активный цикл ЦПУ
                if(!cpu.interruptsEnabled) {
                    let isWaiting = false;
                    for(const op in ioOperations) { if(ioOperations[op].active) isWaiting = true; }
                    if (isWaiting) {
                        setExplanation("io-wait-explanation", "I/O WAIT");
                        return; // tickIO уже был вызван в начале
                    }
                }
                
                if (cpu.interruptMask) {
                    setExplanation("interrupts-disabled", "INTERRUPTS DISABLED");
                }
                if (scenarios[currentScenario].name === "Von Neumann Concept" && cpu.pc >= 0x104) {
                     setExplanation("von-neumann-data-warning", "Data Location", { address: toHex(cpu.pc), data: toHex(memory[cpu.pc]) });
                     return;
                } else {
                     setExplanation("instruction-cycle-start", "Instruction Fetch");
                }
                cycleState = 'FETCH_1_PC_TO_MAR';
                break;
            
            case 'INTERRUPT_START':
                const interrupt = simulationStep.currentInterrupt;
                
                if (cpu.currentInterruptPriority > 0) {
                    for (const deviceName in ioOperations) {
                        if (ioOperations[deviceName].priority === cpu.currentInterruptPriority) {
                            ioOperations[deviceName].isrState = 'preempted';
                            break;
                        }
                    }
                }
                
                // Расширенное сохранение контекста: PC, AC, флаги и текущий приоритет
                setExplanation('interrupt-start-stack', "Interrupt Cycle: Save Context to Stack", { 
                    interruptName: interrupt.name, 
                    pc: toHex(cpu.pc),
                    ac: toHex(cpu.ac),
                    sp: toHex(cpu.sp)
                });
                // Push to stack (grows down)
                cpu.sp--; memory[cpu.sp] = cpu.currentInterruptPriority; // Save old priority
                cpu.sp--; memory[cpu.sp] = cpu.flags;
                cpu.sp--; memory[cpu.sp] = cpu.ac;
                cpu.sp--; memory[cpu.sp] = cpu.pc;
                
                // Update current interrupt priority
                cpu.currentInterruptPriority = interrupt.priority;

                updateMemoryUI();
                updateStackUI();

                highlightRegister('PC', true);
                highlightRegister('AC', true);
                highlightRegister('FLAGS', true);
                highlightRegister('SP', true);
                cycleState = 'INTERRUPT_2_JUMP';
                break;

            case 'INTERRUPT_2_JUMP':
                const int_jump = simulationStep.currentInterrupt;
                setExplanation('interrupt-jump', "Interrupt Cycle: Jump to ISR", { interruptName: int_jump.name, isr: toHex(int_jump.isr) });
                cpu.pc = int_jump.isr;
                pendingInterrupts.shift(); 
                
                // Исправление: Проверяем, что прерывание пришло от I/O устройства, а не от DMA
                if (ioOperations[int_jump.name]) {
                    ioOperations[int_jump.name].isrState = 'running';
                
                    if (elements.ioDevices[int_jump.name]) {
                        elements.ioDevices[int_jump.name].device.classList.remove('interrupting');
                        updateIoDeviceUI(int_jump.name);
                    }
                }
                
                simulationStep.currentInterrupt = null;
                updateUI();
                updateStackUI();
                cycleState = 'START';
                break;

            case 'FETCH_1_PC_TO_MAR':
                cpu.mar = cpu.pc;
                await showBusTransfer('reg-pc', 'reg-mar', `ADDR: ${toHex(cpu.pc)}`, 'address');
                updateUI();
                highlightRegister('PC');
                highlightRegister('MAR', true);
                setExplanation("fetch-1", "Instruction Fetch");
                cycleState = 'FETCH_2_READ_MEMORY';
                break;
                
            case 'FETCH_2_READ_MEMORY':
                cpu.mbr = memory[cpu.mar];
                const memCellId = `mem-${toHex(cpu.mar)}`;
                await showBusTransfer(memCellId, 'reg-mbr', `DATA: ${toHex(cpu.mbr)}`, 'data');
                updateUI();
                highlightRegister('MAR');
                highlightRegister('MBR', true);
                document.getElementById(memCellId).classList.add('highlight-data-read');
                setExplanation("fetch-2", "Instruction Fetch");
                cycleState = 'FETCH_3_MBR_TO_IR';
                break;
                
            case 'FETCH_3_MBR_TO_IR':
                cpu.ir = cpu.mbr;
                cpu.pc++;
                await showBusTransfer('reg-mbr', 'reg-ir', `INST: ${toHex(cpu.mbr)}`, 'control');
                updateUI();
                highlightRegister('MBR');
                highlightRegister('IR', true);
                highlightRegister('PC', true);
                setExplanation("fetch-3", "Instruction Fetch");
                cycleState = 'EXECUTE_DECODE';
                break;

            case 'EXECUTE_DECODE':
                const opcode = cpu.ir >> 12;
                const address = cpu.ir & 0x0FFF;
                highlightRegister('IR', true);
                
                // Opcode names are technical terms, so we'll get them from the English map.
                const opcodeInfo = (opcodeMap['en'] && opcodeMap['en'][opcode]) || "Unknown: Unknown Operation";
                const instructionName = opcodeInfo.substring(0, opcodeInfo.indexOf(':'));

                setExplanation('decode', "Instruction Decode", { 
                    instruction: toHex(cpu.ir), 
                    opcode: toHex(opcode,1), 
                    address: toHex(address,3),
                    instructionName: instructionName
                });

                switch (opcode) {
                    case 0x1: cycleState = 'EXECUTE_LOAD_1'; break;
                    case 0x5: cycleState = 'EXECUTE_ADD_1'; break;
                    case 0x2: cycleState = 'EXECUTE_STORE_1'; break;
                    case 0x3: cycleState = 'EXECUTE_JUMP_1'; break;        // Безусловный переход
                    case 0x4: cycleState = 'EXECUTE_JUMPZ_1'; break;       // Условный переход
                    case 0x6: cycleState = 'EXECUTE_DISABLE_INT_1'; break; // Отключить прерывания
                    case 0x7: cycleState = 'EXECUTE_ENABLE_INT_1'; break;  // Включить прерывания
                    case 0x0:
                        cpu.isHalted = true;
                        setExplanation("halted", "EXECUTION HALTED");
                        cycleState = 'START';
                        break;
                    case 0x9: cycleState = 'EXECUTE_LOAD_IOAR_1'; break;
                    case 0xA: cycleState = 'EXECUTE_MOVE_AC_IOBR_1'; break;
                    case 0xB: cycleState = 'EXECUTE_START_IO_1'; break;
                    case 0xF: cycleState = 'EXECUTE_IO_1'; break;
                    case 0xE: cycleState = 'EXECUTE_IRET_1'; break;
                    default:
                        setExplanation('error-opcode', "ERROR", { opcode: toHex(opcode, 1) });
                        cpu.isHalted = true;
                }
                break;
                
            // LOAD instruction execution
            case 'EXECUTE_LOAD_1':
                cpu.mar = cpu.ir & 0x0FFF;
                await showBusTransfer('reg-ir', 'reg-mar', `ADDR: ${toHex(cpu.mar)}`, 'address');
                updateUI();
                setExplanation("load-1", "Operand Address Calculation", { address: toHex(cpu.mar) });
                cycleState = 'EXECUTE_LOAD_2';
                break;
            case 'EXECUTE_LOAD_2':
                cpu.mbr = memory[cpu.mar];
                const loadMemCellId = `mem-${toHex(cpu.mar)}`;
                await showBusTransfer(loadMemCellId, 'reg-mbr', `DATA: ${toHex(cpu.mbr)}`, 'data');
                updateUI();
                const loadMemCellEl = document.getElementById(loadMemCellId);
                if (loadMemCellEl) loadMemCellEl.classList.add('highlight-data-read');
                highlightRegister('MBR', true);
                setExplanation("load-2", "Operand Fetch");
                cycleState = 'EXECUTE_LOAD_3';
                break;
            case 'EXECUTE_LOAD_3':
                cpu.ac = cpu.mbr;
                await showBusTransfer('reg-mbr', 'reg-ac', `DATA: ${toHex(cpu.mbr)}`, 'control');
                updateUI();
                highlightRegister('AC', true);
                setExplanation("load-3", "Data Operation");
                cycleState = 'START';
                break;

            // ADD instruction execution
            case 'EXECUTE_ADD_1':
                cpu.mar = cpu.ir & 0x0FFF;
                await showBusTransfer('reg-ir', 'reg-mar', `ADDR: ${toHex(cpu.mar)}`, 'address');
                updateUI();
                setExplanation("add-1", "Operand Address Calculation", { address: toHex(cpu.mar) });
                cycleState = 'EXECUTE_ADD_2';
                break;
            case 'EXECUTE_ADD_2':
                cpu.mbr = memory[cpu.mar];
                const addMemCellId = `mem-${toHex(cpu.mar)}`;
                await showBusTransfer(addMemCellId, 'reg-mbr', `DATA: ${toHex(cpu.mbr)}`, 'data');
                updateUI();
                document.getElementById(addMemCellId).classList.add('highlight-data-read');
                highlightRegister('MBR', true);
                setExplanation("add-2", "Operand Fetch");
                cycleState = 'EXECUTE_ADD_3';
                break;
            case 'EXECUTE_ADD_3':
                const oldAC = cpu.ac;
                const result = cpu.ac + cpu.mbr;
                cpu.ac = result & 0xFFFF; // Обрезаем до 16 бит
                
                // Установка флагов состояния
                cpu.flags = 0;
                if (cpu.ac === 0) cpu.flags |= 0x01;                      // Zero flag (Z)
                if ((cpu.ac & 0x8000) !== 0) cpu.flags |= 0x02;           // Negative flag (N)
                
                // Overflow flag (V) - проверяем и знаковое, и беззнаковое переполнение
                if (result > 0xFFFF) {
                    cpu.flags |= 0x04;
                }
                if ((oldAC & 0x8000) === (cpu.mbr & 0x8000) && (oldAC & 0x8000) !== (cpu.ac & 0x8000)) {
                    cpu.flags |= 0x04;
                }
                
                await showBusTransfer('reg-mbr', 'reg-ac', `DATA: ${toHex(cpu.mbr)}`, 'control');
                updateUI();
                highlightRegister('AC', true);
                highlightRegister('FLAGS', true);
                setExplanation("add-3", "Data Operation");
                cycleState = 'START';
                break;

            // STORE instruction execution
            case 'EXECUTE_STORE_1':
                cpu.mar = cpu.ir & 0x0FFF;
                cpu.mbr = cpu.ac;
                await Promise.all([
                    showBusTransfer('reg-ir', 'reg-mar', `ADDR: ${toHex(cpu.mar)}`, 'address'),
                    showBusTransfer('reg-ac', 'reg-mbr', `DATA: ${toHex(cpu.mbr)}`, 'control')
                ]);
                updateUI();
                highlightRegister('AC'); highlightRegister('MBR'); highlightRegister('MAR', true);
                setExplanation("store-1", "Operand Address Calculation", { address: toHex(cpu.mar) });
                cycleState = 'EXECUTE_STORE_2';
                break;
            case 'EXECUTE_STORE_2':
                memory[cpu.mar] = cpu.mbr;
                const storeMemCellId = `mem-${toHex(cpu.mar)}`;
                await showBusTransfer('reg-mbr', storeMemCellId, `DATA: ${toHex(cpu.mbr)}`, 'data');
                const storeMemCellEl = document.getElementById(storeMemCellId);
                if (storeMemCellEl) {
                    storeMemCellEl.querySelector('span:last-child').textContent = toHex(cpu.mbr);
                    storeMemCellEl.classList.add('highlight-data-write');
                }
                setExplanation("store-2", "Operand Store");
                cycleState = 'START';
                break;

            // JUMP instruction execution (безусловный переход)
            case 'EXECUTE_JUMP_1':
                const jumpAddress = cpu.ir & 0x0FFF;
                cpu.pc = jumpAddress;
                await showBusTransfer('reg-ir', 'reg-pc', `ADDR: ${toHex(jumpAddress)}`, 'control');
                updateUI();
                highlightRegister('PC', true);
                setExplanation("jump-1", "Control", { address: toHex(jumpAddress) });
                cycleState = 'START';
                break;

            // JUMPZ instruction execution (условный переход)
            case 'EXECUTE_JUMPZ_1':
                const jumpzAddress = cpu.ir & 0x0FFF;
                const zeroFlag = (cpu.flags & 0x01) !== 0;
                const condition = zeroFlag ? "Z=1" : "Z=0";
                setExplanation("jump-conditional-1", "Control", { 
                    acValue: toHex(cpu.ac), 
                    condition: condition 
                });
                cycleState = 'EXECUTE_JUMPZ_2';
                simulationStep.jumpAddress = jumpzAddress;
                break;
            case 'EXECUTE_JUMPZ_2':
                const jumpAddr = simulationStep.jumpAddress;
                const jumpTaken = (cpu.flags & 0x01) !== 0;
                if (jumpTaken) {
                    cpu.pc = jumpAddr;
                    await showBusTransfer('reg-ir', 'reg-pc', `ADDR: ${toHex(jumpAddr)}`, 'control');
                    highlightRegister('PC', true);
                }
                updateUI();
                setExplanation("jump-conditional-2", "Control", { 
                    jumpTaken: jumpTaken ? getTranslation('jump-taken', currentLang, {address: toHex(jumpAddr)}) : 
                                           getTranslation('jump-not-taken', currentLang),
                    explanation: jumpTaken ? getTranslation('jump-condition-met-z', currentLang) : 
                                             getTranslation('jump-condition-not-met-z', currentLang)
                });
                cycleState = 'START';
                break;

            // Disable interrupts instruction
            case 'EXECUTE_DISABLE_INT_1':
                cpu.interruptMask = true;
                highlightRegister('FLAGS', true);
                setExplanation("disable-interrupts", "Control");
                cycleState = 'START';
                break;

            // Enable interrupts instruction
            case 'EXECUTE_ENABLE_INT_1':
                cpu.interruptMask = false;
                highlightRegister('FLAGS', true);
                setExplanation("enable-interrupts", "Control");
                cycleState = 'START';
                break;
            
            // I/O Instructions
            case 'EXECUTE_LOAD_IOAR_1':
                cpu.ioar = cpu.ir & 0x0FFF;
                await showBusTransfer('reg-ir', 'reg-ioar', `DEV_ADDR: ${toHex(cpu.ioar)}`, 'address');
                updateUI();
                highlightRegister('IOAR', true);
                setExplanation("load-ioar", "Control");
                cycleState = 'START';
                break;

            case 'EXECUTE_MOVE_AC_IOBR_1':
                cpu.iobr = cpu.ac;
                await showBusTransfer('reg-ac', 'reg-iobr', `DATA: ${toHex(cpu.iobr)}`, 'control');
                updateUI();
                highlightRegister('IOBR', true);
                setExplanation("move-ac-iobr", "Data Operation");
                cycleState = 'START';
                break;
            
            case 'EXECUTE_START_IO_1':
                const deviceAddr = cpu.ioar;
                let deviceName = '';
                if(deviceAddr === 1) deviceName = 'printer';
                else if(deviceAddr === 2) deviceName = 'disk';
                else if(deviceAddr === 3) deviceName = 'network';

                if (!deviceName || !ioOperations[deviceName]) {
                    setExplanation('error-io-device-not-found', "I/O ERROR", { deviceCode: deviceAddr });
                    cpu.isHalted = true;
                } else if (ioOperations[deviceName].active) {
                    setExplanation('error-io-device-busy', "I/O ERROR", { deviceName: deviceName });
                    cpu.isHalted = true;
                } else {
                    ioOperations[deviceName].active = true;
                    ioOperations[deviceName].progress = 0;
                    await showBusTransfer('reg-ioar', deviceName.toLowerCase(), `CMD: START`, 'control');
                    await showBusTransfer('reg-iobr', deviceName.toLowerCase(), `DATA: ${toHex(cpu.iobr)}`, 'data');
                    updateIoDeviceUI(deviceName);
                    setExplanation("start-io", "Data Operation");
                }
                cycleState = 'START';
                break;

            case 'EXECUTE_IO_1':
                const addressPart = cpu.ir & 0x0FFF;
                const ioCommand = (cpu.ir & 0x0F00) >> 8;

                if (ioCommand === 0) { // Standard I/O
                    const deviceCode = addressPart & 0x000F;
                    let deviceNameIo = '';
                    if(deviceCode === 1) deviceNameIo = 'printer';
                    else if(deviceCode === 2) deviceNameIo = 'disk';
                    else if(deviceCode === 3) deviceNameIo = 'network';
                    
                    if (!deviceNameIo || !ioOperations[deviceNameIo]) {
                        setExplanation('error-io-device-not-found', "I/O ERROR", { deviceCode: deviceCode });
                        cpu.isHalted = true;
                    } else if (ioOperations[deviceNameIo].active) {
                        setExplanation('error-io-device-busy', "I/O ERROR", { deviceName: deviceNameIo });
                        cpu.isHalted = true;
                    } else {
                        ioOperations[deviceNameIo].active = true;
                        ioOperations[deviceNameIo].progress = 0;
                        await showBusTransfer('reg-ir', deviceNameIo.toLowerCase(), `CMD: START`, 'control');
                        updateIoDeviceUI(deviceNameIo);
                        setExplanation("io-write", "Data Operation", { deviceName: deviceNameIo });
                    }
                } else if (ioCommand === 1) { // DMA command
                     const dmaDeviceCode = (addressPart >> 4) & 0xF;
                     const dmaDevice = dmaDeviceCode === 2 ? 'disk' : null;

                     if (!dmaDevice || !ioOperations[dmaDevice]) {
                         setExplanation('error-dma-no-disk', 'DMA ERROR');
                         cpu.isHalted = true;
                     } else {
                         dma.active = true;
                         dma.sourceDevice = dmaDevice;
                         dma.mar = 0x500; // Hardcoded destination address
                         dma.count = 5; // Hardcoded word count
                         await showBusTransfer('reg-ir', 'dma-controller-panel', 'CMD: DMA START', 'control');
                         setExplanation('dma-init', 'DMA INITIATION', { count: 5, device: dmaDevice, address: toHex(0x500) });
                     }
                }
                cycleState = 'START';
                break;
                 
            case 'EXECUTE_IRET_1':
                setExplanation("iret-stack", "Control: Restore Context from Stack");

                // Find the device whose ISR is finishing and reset its state
                for (const deviceName in ioOperations) {
                    if (ioOperations[deviceName].priority === cpu.currentInterruptPriority) {
                        ioOperations[deviceName].isrState = 'idle';
                        updateIoDeviceUI(deviceName); // Update UI to "Idle"
                        break;
                    }
                }

                // Restore context from stack
                cpu.pc = memory[cpu.sp]; cpu.sp++;
                cpu.ac = memory[cpu.sp]; cpu.sp++;
                cpu.flags = memory[cpu.sp]; cpu.sp++;
                cpu.currentInterruptPriority = memory[cpu.sp]; cpu.sp++; // Restore old priority
                
                // If another ISR was preempted, set its state to "running"
                if (cpu.currentInterruptPriority > 0) {
                    for (const deviceName in ioOperations) {
                        if (ioOperations[deviceName].priority === cpu.currentInterruptPriority) {
                            ioOperations[deviceName].isrState = 'running';
                            updateIoDeviceUI(deviceName); // Update UI to "Servicing"
                            break;
                        }
                    }
                }

                updateUI();
                updateStackUI();
                highlightRegister('PC', true);
                highlightRegister('AC', true);
                highlightRegister('FLAGS', true);
                highlightRegister('SP', true);
                cycleState = 'START';
                break;
        }
    } finally {
        const isAnyIoActive = () => {
            for(const op in ioOperations) { if(ioOperations[op].active) return true; }
            return dma.active;
        }

        // Разблокируем кнопку, если симуляция не завершена полностью
        if (cpu.isHalted && pendingInterrupts.length === 0 && !isAnyIoActive()) {
            // Ничего не делаем, кнопка останется заблокированной
        } else {
            elements.stepBtn.disabled = false;
            elements.stepBtn.classList.remove('disabled-btn');
        }
        isStepInProgress = false;
    }
}

// Event Listeners
elements.stepBtn.addEventListener('click', simulationStep);
elements.resetBtn.addEventListener('click', () => {
    currentScenario = elements.scenarioSelect.value;
    resetSimulation();
});
elements.scenarioSelect.addEventListener('change', (e) => {
    currentScenario = e.target.value;
    updateDeleteButtonVisibility(); // Update visibility on scenario change
    resetSimulation();
});

document.getElementById('lang-en').addEventListener('click', () => setLanguage('en'));
document.getElementById('lang-ru').addEventListener('click', () => setLanguage('ru'));

document.getElementById('opcodes-btn').addEventListener('click', () => {
    const modal = document.getElementById('opcodes-modal');
    const tableBody = document.getElementById('opcodes-table-body');
    tableBody.innerHTML = ''; // Clear previous content

    const opcodes = opcodeMap[currentLang];

    for(const code in opcodes) {
        const hexCode = `0x${parseInt(code).toString(16).toUpperCase()}`;
        const description = opcodes[code];
        const instructionName = description.substring(0, description.indexOf(':'));
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="border-b p-2 font-mono">${hexCode}</td>
            <td class="border-b p-2 font-bold">${instructionName}</td>
            <td class="border-b p-2">${description}</td>
        `;
        tableBody.appendChild(row);
    }

    modal.classList.remove('hidden');
});

document.getElementById('close-modal-btn').addEventListener('click', () => {
    document.getElementById('opcodes-modal').classList.add('hidden');
});

document.getElementById('builder-close-btn').addEventListener('click', () => {
    elements.builderModal.classList.add('hidden');
});

document.getElementById('json-editor-close-btn').addEventListener('click', () => {
    elements.jsonEditorModal.classList.add('hidden');
});


function createIoDeviceElement(name, priority) {
    const deviceId = name.toLowerCase().replace(/[^a-z0-9]/g, '-'); // Sanitize name for ID
    const deviceEl = document.createElement('div');
    deviceEl.id = deviceId;
    deviceEl.className = 'i-o-device border-2 border-gray-300 p-3 rounded-lg text-center';
    deviceEl.innerHTML = `
        <p class="font-bold">${name} (Priority ${priority})</p>
        <p id="${deviceId}-status" class="text-sm">${getTranslation('status-idle', currentLang)}</p>
        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
            <div id="${deviceId}-progress" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
        </div>
    `;
    elements.ioDevicesContainer.appendChild(deviceEl);
    
    elements.ioDevices[name] = {
        status: document.getElementById(`${deviceId}-status`),
        progress: document.getElementById(`${deviceId}-progress`),
        device: deviceEl,
    };
}

document.getElementById('edit-scenario-btn').addEventListener('click', () => {
    const scenario = scenarios[currentScenario];
    populateBuilder(scenario);
    elements.builderModal.classList.remove('hidden');
});

function populateBuilder(scenario) {
    // Populate simple fields
    document.getElementById('builder-scenario-name').value = scenario.name;
    document.getElementById('builder-pc-start').value = `0x${toHex(scenario.pc_start)}`;
    document.getElementById('builder-explanation').value = scenario.explanation || '';

    // Populate components checkboxes
    const hide = scenario.hide || [];
    document.getElementById('builder-reg-ioar').checked = !hide.includes('reg-ioar');
    document.getElementById('builder-reg-iobr').checked = !hide.includes('reg-iobr');
    document.getElementById('builder-reg-flags').checked = !hide.includes('reg-flags');
    document.getElementById('builder-reg-sp').checked = !hide.includes('reg-sp');
    document.getElementById('builder-enable-dma').checked = !hide.includes('dma.panel');

    // Enable/disable apply button
    const applyBtn = document.getElementById('builder-apply-btn');
    applyBtn.disabled = false;
    applyBtn.title = 'Applies changes to the current scenario for this session.';


    // Populate I/O
    const ioEnabled = !hide.includes('ioPanel');
    document.getElementById('builder-enable-io').checked = ioEnabled;
    document.getElementById('builder-io-options').style.display = ioEnabled ? 'block' : 'none';
    
    // Deep copy for editing to avoid modifying original built-in scenarios
    customScenario = {
        name: scenario.name,
        pc_start: scenario.pc_start,
        memory: JSON.parse(JSON.stringify(scenario.memory || {})),
        interrupts: scenario.interrupts,
        hide: [...hide],
        ioDevices: JSON.parse(JSON.stringify(scenario.ioDevices || DEFAULT_IO_OPERATIONS)),
        explanation: scenario.explanation || ''
    };

    initializeBuilder(); // Resets default checkboxes based on default ioOperations
    
    // Check devices from the specific scenario against the defaults
    const scenarioDevices = customScenario.ioDevices;
    for (const deviceName in DEFAULT_IO_OPERATIONS) {
       document.getElementById(`builder-device-${deviceName}`).checked = !!scenarioDevices[deviceName];
    }
    
    updateBuilderMemoryList();
    updateBuilderCustomDeviceList();
}


function initializeBuilder() {
    // Populate default devices
    const defaultDevicesContainer = document.getElementById('builder-default-devices-list');
    defaultDevicesContainer.innerHTML = '';
    for (const deviceName in DEFAULT_IO_OPERATIONS) {
        const device = DEFAULT_IO_OPERATIONS[deviceName];
        const div = document.createElement('div');
        div.innerHTML = `
            <input type="checkbox" id="builder-device-${deviceName}" name="builder-device-${deviceName}" checked>
            <label for="builder-device-${deviceName}">${deviceName} (Priority: ${device.priority}, Duration: ${device.duration})</label>
        `;
        defaultDevicesContainer.appendChild(div);
    }
}

document.getElementById('edit-scenario-btn').addEventListener('click', () => {
    const scenario = scenarios[currentScenario];
    populateBuilder(scenario);
    elements.builderModal.classList.remove('hidden');
});

function updateBuilderMemoryList() {
    const memoryList = document.getElementById('builder-memory-list');
    memoryList.innerHTML = '';
    for (const addr in customScenario.memory) {
        const val = customScenario.memory[addr];
        const div = document.createElement('div');
        div.className = 'flex justify-between items-center p-1 bg-gray-50';
        div.innerHTML = `<span><span class="font-bold">${toHex(parseInt(addr))}:</span> ${toHex(val)}</span>
                         <button class="text-red-500 font-bold remove-mem-btn" data-addr="${addr}">X</button>`;
        memoryList.appendChild(div);
    }
    document.querySelectorAll('.remove-mem-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const addrToRemove = e.target.getAttribute('data-addr');
            delete customScenario.memory[addrToRemove];
            updateBuilderMemoryList();
        });
    });
}

document.getElementById('builder-add-mem-btn').addEventListener('click', () => {
    const addrInput = document.getElementById('builder-mem-address');
    const valInput = document.getElementById('builder-mem-value');
    const addr = parseInt(addrInput.value);
    const val = parseInt(valInput.value);

    if (!isNaN(addr) && !isNaN(val)) {
        customScenario.memory[addr] = val;
        addrInput.value = '';
        valInput.value = '';
        updateBuilderMemoryList();
    } else {
        alert('Invalid address or value. Please use hex (0x...) or decimal numbers.');
    }
});

function updateBuilderCustomDeviceList() {
    const deviceList = document.getElementById('builder-custom-devices-list');
    deviceList.innerHTML = '';
    for (const deviceName in customScenario.ioDevices) {
        if (DEFAULT_IO_OPERATIONS[deviceName]) continue; // Don't show default devices here
        const device = customScenario.ioDevices[deviceName];
        const div = document.createElement('div');
        div.className = 'flex justify-between items-center p-1 bg-gray-50';
        div.innerHTML = `<span>${deviceName} (P: ${device.priority}, D: ${device.duration})</span>
                         <button class="text-red-500 font-bold remove-device-btn" data-name="${deviceName}">X</button>`;
        deviceList.appendChild(div);
    }
    document.querySelectorAll('.remove-device-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const nameToRemove = e.target.getAttribute('data-name');
            delete customScenario.ioDevices[nameToRemove];
            updateBuilderCustomDeviceList();
        });
    });
}


document.getElementById('builder-add-device-btn').addEventListener('click', () => {
    const nameInput = document.getElementById('builder-device-name');
    const timingInput = document.getElementById('builder-device-timing');
    const priorityInput = document.getElementById('builder-device-priority');
    
    const name = nameInput.value.trim();
    const duration = parseInt(timingInput.value);
    const priority = parseInt(priorityInput.value);

    if (name && !isNaN(duration) && !isNaN(priority)) {
        customScenario.ioDevices[name] = {
            active: false, progress: 0, duration, priority, isr: 0, isrState: 'idle'
        };
        nameInput.value = '';
        timingInput.value = '';
        priorityInput.value = '';
        updateBuilderCustomDeviceList();
    } else {
        alert('Please fill all device fields with valid values.');
    }
});

function gatherScenarioDataFromBuilder() {
    const scenarioData = {};
    scenarioData.name = document.getElementById('builder-scenario-name').value;
    scenarioData.pc_start = parseInt(document.getElementById('builder-pc-start').value);
    scenarioData.explanation = document.getElementById('builder-explanation').value;
    
    scenarioData.hide = [];
    if (!document.getElementById('builder-reg-ioar').checked) scenarioData.hide.push('reg-ioar');
    if (!document.getElementById('builder-reg-iobr').checked) scenarioData.hide.push('reg-iobr');
    if (!document.getElementById('builder-reg-flags').checked) scenarioData.hide.push('reg-flags');
    if (!document.getElementById('builder-reg-sp').checked) scenarioData.hide.push('reg-sp');
    if (!document.getElementById('builder-enable-dma').checked) scenarioData.hide.push('dma.panel');

    if (!document.getElementById('builder-enable-io').checked) {
        scenarioData.hide.push('ioPanel');
        scenarioData.ioDevices = {};
    } else {
        const currentDevices = {};
        // Add selected default devices
        for (const deviceName in DEFAULT_IO_OPERATIONS) {
            if (document.getElementById(`builder-device-${deviceName}`).checked) {
                 currentDevices[deviceName] = { ...DEFAULT_IO_OPERATIONS[deviceName] };
            }
        }
        // Add custom devices that were added in the builder
        for (const deviceName in customScenario.ioDevices) {
            if (!DEFAULT_IO_OPERATIONS[deviceName]) { // if it is a truly custom one
                currentDevices[deviceName] = customScenario.ioDevices[deviceName];
            }
        }
        scenarioData.ioDevices = currentDevices;
    }
    
    scenarioData.memory = { ...customScenario.memory };
    scenarioData.interrupts = true; // Always true for custom/edited

    return scenarioData;
}


document.getElementById('builder-save-btn').addEventListener('click', () => {
    const newScenarioData = gatherScenarioDataFromBuilder();
    
    const scenarioId = `custom-${Date.now()}`;
    scenarios[scenarioId] = newScenarioData;
    
    const option = document.createElement('option');
    option.value = scenarioId;
    option.textContent = newScenarioData.name;
    elements.scenarioSelect.appendChild(option);

    elements.scenarioSelect.value = scenarioId;
    currentScenario = scenarioId;
    updateDeleteButtonVisibility(); 
    resetSimulation();
    
    if(newScenarioData.explanation) {
        elements.explanationText.textContent = newScenarioData.explanation;
        elements.cycleStateText.textContent = '';
    }

    elements.builderModal.classList.add('hidden');
});

document.getElementById('builder-apply-btn').addEventListener('click', () => {
    const updatedScenarioData = gatherScenarioDataFromBuilder();
    scenarios[currentScenario] = updatedScenarioData;

    // Update dropdown text if name changed
    const option = elements.scenarioSelect.querySelector(`option[value="${currentScenario}"]`);
    if (option) {
        option.textContent = updatedScenarioData.name;
    }

    resetSimulation();

    if(updatedScenarioData.explanation) {
        elements.explanationText.textContent = updatedScenarioData.explanation;
        elements.cycleStateText.textContent = '';
    }
    
    elements.builderModal.classList.add('hidden');
});


document.getElementById('builder-export-btn').addEventListener('click', () => {
    const dataToExport = gatherScenarioDataFromBuilder();
    const dataStr = JSON.stringify(dataToExport, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = `${dataToExport.name.replace(/\s/g, '_')}.json`;
    
    let linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
});

document.getElementById('builder-import-btn').addEventListener('click', () => {
    document.getElementById('builder-import-file').click();
});

document.getElementById('builder-import-file').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const importedData = JSON.parse(e.target.result);
                repopulateBuilder(importedData);
            } catch (error) {
                alert('Error parsing JSON file: ' + error.message);
            }
        };
        reader.readAsText(file);
    }
    // Clear the file input so the change event fires again for the same file
    event.target.value = '';
});

document.getElementById('builder-enable-io').addEventListener('change', (e) => {
    document.getElementById('builder-io-options').style.display = e.target.checked ? 'block' : 'none';
});

function repopulateBuilder(scenarioData) {
    customScenario = scenarioData;
    // Repopulate UI
    document.getElementById('builder-scenario-name').value = customScenario.name;
    document.getElementById('builder-pc-start').value = toHex(customScenario.pc_start);
    document.getElementById('builder-explanation').value = customScenario.explanation;
    
    document.getElementById('builder-reg-ioar').checked = !customScenario.hide.includes('reg-ioar');
    document.getElementById('builder-reg-iobr').checked = !customScenario.hide.includes('reg-iobr');
    document.getElementById('builder-reg-flags').checked = !customScenario.hide.includes('reg-flags');
    document.getElementById('builder-reg-sp').checked = !customScenario.hide.includes('reg-sp');
    
    const ioEnabled = !customScenario.hide.includes('ioPanel');
    document.getElementById('builder-enable-io').checked = ioEnabled;

    initializeBuilder(); // Resets default checkboxes
    for (const deviceName in DEFAULT_IO_OPERATIONS) {
        if (customScenario.ioDevices[deviceName]) {
            document.getElementById(`builder-device-${deviceName}`).checked = true;
        } else {
            document.getElementById(`builder-device-${deviceName}`).checked = false;
        }
    }

    updateBuilderMemoryList();
    updateBuilderCustomDeviceList();
}


document.getElementById('builder-edit-json-btn').addEventListener('click', () => {
    const scenarioData = gatherScenarioDataFromBuilder();
    document.getElementById('json-editor-textarea').value = JSON.stringify(scenarioData, null, 2);
    elements.jsonEditorModal.classList.remove('hidden');
});

document.getElementById('json-editor-apply-btn').addEventListener('click', () => {
    try {
        const jsonData = document.getElementById('json-editor-textarea').value;
        const scenarioData = JSON.parse(jsonData);
        repopulateBuilder(scenarioData);
        elements.jsonEditorModal.classList.add('hidden');
    } catch (error) {
        alert('Invalid JSON: ' + error.message);
    }
});


function updateDeleteButtonVisibility() {
    const deleteBtn = document.getElementById('delete-scenario-btn');
    if (currentScenario.startsWith('custom-')) {
        deleteBtn.classList.remove('hidden');
    } else {
        deleteBtn.classList.add('hidden');
    }
}

elements.scenarioSelect.addEventListener('change', (e) => {
    currentScenario = e.target.value;
    updateDeleteButtonVisibility();
    resetSimulation();
});

document.getElementById('delete-scenario-btn').addEventListener('click', () => {
    if (currentScenario.startsWith('custom-') && confirm(`Are you sure you want to delete "${scenarios[currentScenario].name}"?`)) {
        // Remove from select dropdown
        const option = elements.scenarioSelect.querySelector(`option[value="${currentScenario}"]`);
        if (option) {
            option.remove();
        }
        
        // Remove from scenarios object
        delete scenarios[currentScenario];
        
        // Switch to a default scenario
        currentScenario = 'vonNeumann';
        elements.scenarioSelect.value = currentScenario;
        updateDeleteButtonVisibility();
        resetSimulation();
    }
});


// Initial Load
window.onload = () => {
    // currentLang уже установлен из PHP, но проверим localStorage
    const savedLang = localStorage.getItem('cs604-lang');
    if (savedLang && savedLang !== currentLang) {
        setLanguage(savedLang);
    } else {
        // Инициализация с текущим языком из PHP
        updateScenarioOptions(); // Обновляем переводы сценариев
        setLanguage(currentLang);
    }
};
