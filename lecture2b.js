const elements = {
    scenarioSelect: document.getElementById('scenario-select'),
    stepBtn: document.getElementById('step-btn'),
    resetBtn: document.getElementById('reset-btn'),
    explanationText: document.getElementById('explanation-text'),
    cycleStateText: document.getElementById('cycle-state-text'),
    visualizationPanel: document.getElementById('visualization-panel'),
    busContainer: document.getElementById('bus-container'),
};

let currentScenario = 'bus';
let simulationStepCounter = 0;
let components = {}; // Will store component elements and their coords

const scenarios = {
    bus: {
        name: "Bus Architecture",
        steps: 5
    },
    'bus-arbitration': {
        name: "Bus Arbitration",
        steps: 10
    },
    'point-to-point': {
        name: "Point-to-Point Interconnect", 
        steps: 4
    },
    'qpi-detailed': {
        name: "QPI Protocol Details",
        steps: 6
    },
    'pcie-layers': {
        name: "PCIe Layered Protocol",
        steps: 4
    },
    'pcie-split-transactions': {
        name: "PCIe Split Transactions",
        steps: 6
    },
    'pcie-encoding': {
        name: "PCIe 128b/130b Encoding",
        steps: 5
    },
    'pcie-multilane': {
        name: "PCIe Multi-Lane",
        steps: 4
    },
    'pcie-ack-nak': {
        name: "PCIe ACK/NAK Mechanism",
        steps: 7
    }
};


// --- UTILITY FUNCTIONS ---
function getTranslation(key, lang = 'en', params = {}) {
    return translations[lang] && translations[lang][key] ? 
           translations[lang][key].replace(/\{(\w+)\}/g, (match, p1) => params[p1] || match) : 
           key;
}

function applyLanguage(lang) {
    currentLang = lang;

    document.querySelector('#scenario-select option[value="bus"]').textContent = getTranslation('scenario-bus', currentLang);
    document.querySelector('#scenario-select option[value="bus-arbitration"]').textContent = getTranslation('scenario-bus-arbitration', currentLang);
    document.querySelector('#scenario-select option[value="point-to-point"]').textContent = getTranslation('scenario-point-to-point', currentLang);
    document.querySelector('#scenario-select option[value="qpi-detailed"]').textContent = getTranslation('scenario-qpi-detailed', currentLang);
    document.querySelector('#scenario-select option[value="pcie-layers"]').textContent = getTranslation('scenario-pcie-layers', currentLang);
    document.querySelector('#scenario-select option[value="pcie-split-transactions"]').textContent = getTranslation('scenario-pcie-split-transactions', currentLang);
    document.querySelector('#scenario-select option[value="pcie-encoding"]').textContent = getTranslation('scenario-pcie-encoding', currentLang);
    document.querySelector('#scenario-select option[value="pcie-multilane"]').textContent = getTranslation('scenario-pcie-multilane', currentLang);
    document.querySelector('#scenario-select option[value="pcie-ack-nak"]').textContent = getTranslation('scenario-pcie-ack-nak', currentLang);

    resetSimulation();
}


function setExplanation(textKey, state = '', params = {}) {
    let text = getTranslation(textKey, currentLang, params);
    elements.explanationText.innerHTML = text; // Use innerHTML to allow for formatting
    const stateText = getTranslation('cycle-state', currentLang, {state: state});
    elements.cycleStateText.textContent = state ? stateText : '';
}

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

async function animateTransfer(startId, endId, text, type, options = {}) {
    const { duration = 800, oneway = false } = options;
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
    
    await new Promise(resolve => setTimeout(resolve, duration));

    bus.classList.remove('active');
    await new Promise(resolve => setTimeout(resolve, 300));

    elements.busContainer.removeChild(bus);
}


// --- DRAWING FUNCTIONS ---

function drawComponent(id, name, top, left) {
    const component = document.createElement('div');
    component.id = id;
    component.className = 'absolute border-2 border-gray-600 bg-gray-100 p-4 rounded-lg text-center shadow-md';
    component.style.top = top;
    component.style.left = left;
    component.innerHTML = `<p class="font-bold">${name}</p>`;
    elements.visualizationPanel.appendChild(component);
    components[id] = component;
}

function drawBusScenario() {
    elements.visualizationPanel.innerHTML = `
        <div id="system-bus" class="absolute bg-gray-400 h-4 inset-x-6" style="top: 50%;"></div>
        <div id="address-bus-label" class="absolute text-sm text-gray-600" style="top: calc(50% - 40px); left: 10px;">Address Bus</div>
        <div id="data-bus-label" class="absolute text-sm text-gray-600" style="top: calc(50% - 20px); left: 10px;">Data Bus</div>
        <div id="control-bus-label" class="absolute text-sm text-gray-600" style="top: calc(50% + 20px); left: 10px;">Control Bus</div>
    `;
    drawComponent('cpu', 'CPU', '20%', '10%');
    drawComponent('memory', 'Memory', '20%', '70%');
    drawComponent('io-device', 'I/O Device', '60%', '40%');
}

function drawBusArbitrationScenario() {
    elements.visualizationPanel.innerHTML = `
        <div id="system-bus" class="absolute bg-gray-400 h-4 inset-x-6" style="top: 50%;"></div>
        <div id="address-bus-label" class="absolute text-sm text-gray-600" style="top: calc(50% - 40px); left: 10px;">Address Bus</div>
        <div id="data-bus-label" class="absolute text-sm text-gray-600" style="top: calc(50% - 20px); left: 10px;">Data Bus</div>
        <div id="control-bus-label" class="absolute text-sm text-gray-600" style="top: calc(50% + 20px); left: 10px;">Control Bus</div>
        
        <!-- Bus control signals -->
        <div id="bus-request-lines" class="absolute" style="top: calc(50% + 40px); left: 10px;">
            <div class="text-xs text-gray-600 mb-1">Bus Control:</div>
            <div id="cpu-breq" class="text-xs bg-red-100 p-1 rounded mb-1">CPU BREQ: Idle</div>
            <div id="dma-breq" class="text-xs bg-blue-100 p-1 rounded mb-1">DMA BREQ: Idle</div>
            <div id="io-breq" class="text-xs bg-green-100 p-1 rounded mb-1">I/O BREQ: Idle</div>
            <div id="bus-grant" class="text-xs bg-yellow-100 p-1 rounded">BGRANT: None</div>
        </div>
    `;
    drawComponent('cpu', 'CPU', '20%', '10%');
    drawComponent('memory', 'Memory', '20%', '70%');
    drawComponent('dma-controller', 'DMA Controller', '60%', '20%');
    drawComponent('io-device', 'I/O Device', '60%', '70%');
    
    // Add arbitration logic display
    const arbitratorDiv = document.createElement('div');
    arbitratorDiv.id = 'bus-arbitrator';
    arbitratorDiv.className = 'absolute border-2 border-purple-600 bg-purple-100 p-2 rounded-lg text-center text-xs';
    arbitratorDiv.style.top = '10%';
    arbitratorDiv.style.left = '45%';
    arbitratorDiv.innerHTML = '<p class="font-bold text-purple-800">Bus Arbitrator</p><p id="arbitrator-state">Idle</p>';
    elements.visualizationPanel.appendChild(arbitratorDiv);
}

function drawPointToPointScenario() {
    elements.visualizationPanel.innerHTML = ''; // Clear previous
    drawComponent('cpu-core-a', 'CPU Core A', '20%', '10%');
    drawComponent('cpu-core-b', 'CPU Core B', '20%', '70%');
    drawComponent('io-hub', 'I/O Hub (IOH)', '70%', '40%');
    drawComponent('dram', 'Main Memory (DRAM)', '70%', '70%');
    // We will draw lines in the simulation step
}

function drawQpiDetailedScenario() {
    elements.visualizationPanel.innerHTML = `
        <div class="relative w-full h-full p-4">
            <div class="flex justify-between items-center mb-4">
                <div id="core-a" class="border-2 border-blue-600 bg-blue-100 p-4 rounded-lg text-center">
                    <p class="font-bold text-blue-800">Core A</p>
                    <div id="core-a-credits" class="text-xs mt-2">Credits: 8</div>
                </div>
                <div id="core-b" class="border-2 border-green-600 bg-green-100 p-4 rounded-lg text-center">
                    <p class="font-bold text-green-800">Core B</p>
                    <div id="core-b-credits" class="text-xs mt-2">Credits: 8</div>
                </div>
            </div>
            
            <!-- QPI Link visualization -->
            <div id="qpi-link" class="bg-gray-300 h-16 rounded-lg flex items-center justify-center relative mb-4">
                <div class="text-xs absolute left-2 top-1">20 Data Lanes (each direction)</div>
                <div class="text-xs absolute left-2 bottom-1">Clock Lane</div>
                <div id="phit-display" class="bg-white p-2 rounded text-center font-mono text-xs opacity-0">
                    Phit: 20 bits
                </div>
            </div>
            
            <!-- Flit composition -->
            <div id="flit-builder" class="border-2 border-gray-400 bg-gray-100 p-4 rounded-lg">
                <p class="font-bold mb-2">Flit Assembly (80 bits)</p>
                <div class="flex space-x-2">
                    <div id="phit-1" class="bg-blue-200 p-2 rounded text-xs opacity-0">Phit 1</div>
                    <div id="phit-2" class="bg-blue-200 p-2 rounded text-xs opacity-0">Phit 2</div>
                    <div id="phit-3" class="bg-blue-200 p-2 rounded text-xs opacity-0">Phit 3</div>
                    <div id="phit-4" class="bg-blue-200 p-2 rounded text-xs opacity-0">Phit 4</div>
                </div>
                <div id="flit-status" class="mt-2 text-xs">Status: Empty</div>
            </div>
            
            <!-- Error handling -->
            <div id="error-handling" class="mt-4 border-2 border-red-400 bg-red-100 p-4 rounded-lg opacity-0">
                <p class="font-bold text-red-800">Error Detection & Recovery</p>
                <div id="crc-check" class="text-xs mt-2">CRC Status: OK</div>
                <div id="retransmit-status" class="text-xs">Retransmit Queue: Empty</div>
            </div>
        </div>
    `;
}

function drawPcieLayersScenario() {
    elements.visualizationPanel.innerHTML = `
        <div id="packet-container" class="relative w-full h-full flex flex-col items-center justify-center p-4">
            <div id="layer-app" class="layer border-2 border-purple-400 bg-purple-100 p-4 rounded-lg w-3/4 mb-2">
                <p class="font-bold text-purple-800">Application Layer</p>
                <div id="app-data" class="p-2 bg-white rounded mt-2 text-center font-mono">User Data</div>
            </div>
            <div id="layer-trans" class="layer border-2 border-blue-400 bg-blue-100 p-4 rounded-lg w-3/4 mb-2 opacity-0">
                <p class="font-bold text-blue-800">Transaction Layer</p>
                <div id="trans-packet" class="p-2 bg-white rounded mt-2 flex font-mono">
                    <div id="trans-header" class="bg-blue-200 p-2">Header</div>
                    <div id="trans-data" class="p-2">Data</div>
                </div>
            </div>
            <div id="layer-dll" class="layer border-2 border-green-400 bg-green-100 p-4 rounded-lg w-3/4 mb-2 opacity-0">
                <p class="font-bold text-green-800">Data Link Layer</p>
                <div id="dll-packet" class="p-2 bg-white rounded mt-2 flex font-mono text-sm">
                    <div id="dll-seq" class="bg-green-200 p-2">Seq</div>
                    <div id="dll-tlp" class="p-2 flex-grow">TLP</div>
                    <div id="dll-lcrc" class="bg-green-200 p-2">LCRC</div>
                </div>
            </div>
             <div id="layer-phy" class="layer border-2 border-red-400 bg-red-100 p-4 rounded-lg w-3/4 opacity-0">
                <p class="font-bold text-red-800">Physical Layer</p>
                <div id="phy-packet" class="p-2 bg-white rounded mt-2 text-center font-mono text-xs overflow-hidden">
                   [ Framing | Packet... | Framing ]
                </div>
            </div>
        </div>
    `;
}

function drawPcieSplitTransactionsScenario() {
    elements.visualizationPanel.innerHTML = `
        <div class="relative w-full h-full p-4">
            <div class="flex justify-between items-center mb-8">
                <div id="cpu-root" class="border-2 border-blue-600 bg-blue-100 p-4 rounded-lg text-center">
                    <p class="font-bold text-blue-800">CPU/Root Complex</p>
                    <div id="cpu-state" class="text-xs mt-2">State: Ready</div>
                </div>
                <div id="pcie-endpoint" class="border-2 border-green-600 bg-green-100 p-4 rounded-lg text-center">
                    <p class="font-bold text-green-800">PCIe Endpoint</p>
                    <div id="endpoint-state" class="text-xs mt-2">State: Ready</div>
                </div>
            </div>
            
            <!-- Transaction tracking -->
            <div id="transaction-tracker" class="bg-gray-100 p-4 rounded-lg mb-4">
                <p class="font-bold mb-2">Active Transactions</p>
                <div id="pending-transactions" class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-white p-2 rounded">Request ID #1: Pending</div>
                    <div class="bg-white p-2 rounded">Request ID #2: Pending</div>
                </div>
            </div>
            
            <!-- TLP Flow -->
            <div id="tlp-flow" class="border-2 border-gray-400 bg-white p-4 rounded-lg">
                <p class="font-bold mb-2">TLP Flow</p>
                <div class="flex space-x-4">
                    <div id="request-tlp" class="bg-blue-200 p-3 rounded opacity-0">
                        <div class="font-bold text-xs">Memory Read Request</div>
                        <div class="text-xs">Tag: 0x5A, Addr: 0x1000</div>
                    </div>
                    <div class="text-2xl self-center">→</div>
                    <div id="completion-tlp" class="bg-green-200 p-3 rounded opacity-0">
                        <div class="font-bold text-xs">Completion</div>
                        <div class="text-xs">Tag: 0x5A, Data: 0xBEEF</div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function drawPcieEncodingScenario() {
    elements.visualizationPanel.innerHTML = `
        <div class="relative w-full h-full p-4">
            <!-- Raw Data Input -->
            <div id="raw-data-section" class="mb-6">
                <p class="font-bold mb-2">Raw Data (128 bits)</p>
                <div id="raw-data-bits" class="bg-white border-2 p-4 rounded-lg font-mono text-xs">
                    11010011 10110100 01001101 11100010 01110110 10010011 11001100 10101010
                    00110110 11010101 10100111 01001110 11110001 10010110 01110100 11001001
                </div>
            </div>
            
            <!-- Scrambling Stage -->
            <div id="scrambling-section" class="mb-6 opacity-0">
                <p class="font-bold mb-2">After Scrambling</p>
                <div id="scrambled-data" class="bg-yellow-100 border-2 p-4 rounded-lg font-mono text-xs">
                    01010110 01001011 10110010 00011101 10001001 01101100 00110011 01010101
                    11001001 00101010 01011000 10110001 00001110 01101001 10001011 00110110
                </div>
                <div class="text-xs text-gray-600 mt-2">Purpose: Improve transition density for clock recovery</div>
            </div>
            
            <!-- 128b/130b Encoding -->
            <div id="encoding-section" class="mb-6 opacity-0">
                <p class="font-bold mb-2">128b/130b Encoding</p>
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <div class="font-bold text-xs mb-2">Input: 128 bits</div>
                        <div class="font-mono text-xs">Data Block</div>
                    </div>
                    <div class="text-2xl">→</div>
                    <div class="bg-green-100 p-4 rounded-lg">
                        <div class="font-bold text-xs mb-2">Output: 130 bits</div>
                        <div class="font-mono text-xs">
                            <span class="bg-red-200 px-1">10</span> + Data Block
                        </div>
                        <div class="text-xs mt-1">Header: 10 = Data block</div>
                    </div>
                </div>
                <div class="mt-4 bg-gray-100 p-3 rounded">
                    <div class="text-xs">Efficiency: 128/130 = 98.46%</div>
                    <div class="text-xs">Overhead: 2 bits per 128 bits = 1.54%</div>
                </div>
            </div>
            
            <!-- Lane Distribution -->
            <div id="lane-section" class="opacity-0">
                <p class="font-bold mb-2">Serial Transmission</p>
                <div id="transmission-visual" class="bg-gray-200 p-4 rounded-lg">
                    <div class="text-xs mb-2">130-bit blocks transmitted serially over differential pair</div>
                    <div class="bg-white p-2 rounded font-mono text-xs overflow-hidden whitespace-nowrap">
                        [Framing] 10|Data|Data|Data... [Framing]
                    </div>
                </div>
            </div>
        </div>
    `;
}

function drawPcieMultilaneScenario() {
    elements.visualizationPanel.innerHTML = `
        <div class="relative w-full h-full p-4">
            <!-- Source and Destination -->
            <div class="flex justify-between items-center mb-6">
                <div id="transmitter" class="border-2 border-blue-600 bg-blue-100 p-4 rounded-lg">
                    <p class="font-bold text-blue-800">Transmitter</p>
                    <div class="text-xs mt-2">Data Rate: 16 GB/s</div>
                </div>
                <div id="receiver" class="border-2 border-green-600 bg-green-100 p-4 rounded-lg">
                    <p class="font-bold text-green-800">Receiver</p>
                    <div class="text-xs mt-2">Reconstruction</div>
                </div>
            </div>
            
            <!-- Data Distribution -->
            <div id="data-dist-section" class="mb-6">
                <p class="font-bold mb-2">Round-Robin Data Distribution</p>
                <div id="source-data" class="bg-white border-2 p-3 rounded-lg mb-4">
                    <div class="font-mono text-sm">Source Data: 0xDEADBEEF12345678</div>
                    <div class="flex space-x-1 mt-2">
                        <div id="byte-0" class="bg-red-200 p-1 rounded text-xs">DE</div>
                        <div id="byte-1" class="bg-blue-200 p-1 rounded text-xs">AD</div>
                        <div id="byte-2" class="bg-green-200 p-1 rounded text-xs">BE</div>
                        <div id="byte-3" class="bg-yellow-200 p-1 rounded text-xs">EF</div>
                        <div id="byte-4" class="bg-red-200 p-1 rounded text-xs">12</div>
                        <div id="byte-5" class="bg-blue-200 p-1 rounded text-xs">34</div>
                        <div id="byte-6" class="bg-green-200 p-1 rounded text-xs">56</div>
                        <div id="byte-7" class="bg-yellow-200 p-1 rounded text-xs">78</div>
                    </div>
                </div>
            </div>
            
            <!-- x4 Lanes -->
            <div id="lanes-section" class="mb-6">
                <p class="font-bold mb-2">PCIe x4 Lanes (4 differential pairs)</p>
                <div class="grid grid-cols-4 gap-4">
                    <div id="lane-0" class="bg-red-100 border-2 p-3 rounded-lg text-center">
                        <div class="font-bold text-xs">Lane 0</div>
                        <div id="lane-0-data" class="font-mono text-xs mt-2 opacity-0">DE, 12</div>
                    </div>
                    <div id="lane-1" class="bg-blue-100 border-2 p-3 rounded-lg text-center">
                        <div class="font-bold text-xs">Lane 1</div>
                        <div id="lane-1-data" class="font-mono text-xs mt-2 opacity-0">AD, 34</div>
                    </div>
                    <div id="lane-2" class="bg-green-100 border-2 p-3 rounded-lg text-center">
                        <div class="font-bold text-xs">Lane 2</div>
                        <div id="lane-2-data" class="font-mono text-xs mt-2 opacity-0">BE, 56</div>
                    </div>
                    <div id="lane-3" class="bg-yellow-100 border-2 p-3 rounded-lg text-center">
                        <div class="font-bold text-xs">Lane 3</div>
                        <div id="lane-3-data" class="font-mono text-xs mt-2 opacity-0">EF, 78</div>
                    </div>
                </div>
            </div>
            
            <!-- Throughput calculation -->
            <div id="throughput-section" class="opacity-0">
                <p class="font-bold mb-2">Throughput Calculation</p>
                <div class="bg-gray-100 p-4 rounded-lg text-xs">
                    <div>PCIe 3.0: 8 GT/s per lane</div>
                    <div>x4 configuration: 4 lanes × 8 GT/s = 32 GT/s</div>
                    <div>After 128b/130b encoding: 32 × (128/130) = 31.4 GT/s</div>
                    <div class="font-bold mt-2">Effective bandwidth: ~3.9 GB/s</div>
                </div>
            </div>
        </div>
    `;
}

function drawPcieAckNakScenario() {
    elements.visualizationPanel.innerHTML = `
        <div class="relative w-full h-full p-4">
            <!-- Transmitter and Receiver -->
            <div class="flex justify-between items-center mb-6">
                <div id="transmitter-dll" class="border-2 border-blue-600 bg-blue-100 p-4 rounded-lg text-center">
                    <p class="font-bold text-blue-800">Transmitter DLL</p>
                    <div id="tx-buffer" class="text-xs mt-2">TX Buffer: Empty</div>
                    <div id="tx-seq" class="text-xs">Next Seq: #1</div>
                </div>
                <div id="receiver-dll" class="border-2 border-green-600 bg-green-100 p-4 rounded-lg text-center">
                    <p class="font-bold text-green-800">Receiver DLL</p>
                    <div id="rx-buffer" class="text-xs mt-2">RX Buffer: Empty</div>
                    <div id="rx-expected" class="text-xs">Expected Seq: #1</div>
                </div>
            </div>
            
            <!-- TLP and DLLP Flow -->
            <div id="packet-flow" class="mb-6">
                <p class="font-bold mb-2">Packet Flow</p>
                <div class="grid grid-cols-3 gap-4">
                    <div id="tlp-packet" class="bg-white border-2 p-3 rounded-lg opacity-0">
                        <div class="font-bold text-xs text-blue-600">TLP</div>
                        <div class="text-xs">Seq: #1</div>
                        <div class="text-xs">Data: 0xABCD</div>
                        <div class="text-xs">LCRC: OK</div>
                    </div>
                    <div class="text-2xl self-center text-center">→</div>
                    <div id="dllp-response" class="bg-white border-2 p-3 rounded-lg opacity-0">
                        <div class="font-bold text-xs text-green-600">DLLP</div>
                        <div id="ack-nak-type" class="text-xs">Type: ACK</div>
                        <div id="ack-nak-seq" class="text-xs">Seq: #1</div>
                    </div>
                </div>
            </div>
            
            <!-- Error Simulation -->
            <div id="error-section" class="mb-6 opacity-0">
                <p class="font-bold mb-2 text-red-600">Error Simulation</p>
                <div class="bg-red-100 border-2 border-red-400 p-3 rounded-lg">
                    <div class="text-xs">🚨 LCRC Error Detected!</div>
                    <div class="text-xs mt-1">Packet corrupted during transmission</div>
                    <div class="text-xs mt-1">Receiver will send NAK</div>
                </div>
            </div>
            
            <!-- Retransmission Buffer -->
            <div id="retransmit-section" class="mb-6">
                <p class="font-bold mb-2">Retransmission Buffer</p>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <div id="buffer-1" class="bg-gray-100 p-2 rounded opacity-0">Seq #1: TLP Ready</div>
                    <div id="buffer-2" class="bg-gray-100 p-2 rounded opacity-0">Seq #2: TLP Ready</div>
                    <div id="buffer-3" class="bg-gray-100 p-2 rounded opacity-0">Seq #3: TLP Ready</div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div id="stats-section" class="opacity-0">
                <p class="font-bold mb-2">Link Statistics</p>
                <div class="bg-gray-100 p-3 rounded-lg text-xs">
                    <div id="packets-sent">Packets Sent: 0</div>
                    <div id="acks-received">ACKs Received: 0</div>
                    <div id="naks-received">NAKs Received: 0</div>
                    <div id="retransmissions">Retransmissions: 0</div>
                </div>
            </div>
        </div>
    `;
}


// --- SIMULATION LOGIC ---

function resetSimulation() {
    simulationStepCounter = 0;
    currentScenario = elements.scenarioSelect.value;
    components = {};
    elements.busContainer.innerHTML = ''; // Clear any leftover bus lines

    switch (currentScenario) {
        case 'bus':
            drawBusScenario();
            setExplanation('bus-scenario-init', 'Bus Architecture');
            break;
        case 'bus-arbitration':
            drawBusArbitrationScenario();
            setExplanation('bus-arbitration-init', 'Bus Arbitration');
            break;
        case 'point-to-point':
            drawPointToPointScenario();
            setExplanation('ptp-scenario-init', 'Point-to-Point');
            break;
        case 'qpi-detailed':
            drawQpiDetailedScenario();
            setExplanation('qpi-detailed-init', 'QPI Details');
            break;
        case 'pcie-layers':
            drawPcieLayersScenario();
            setExplanation('pcie-scenario-init', 'PCIe Layers');
            break;
        case 'pcie-split-transactions':
            drawPcieSplitTransactionsScenario();
            setExplanation('pcie-split-init', 'PCIe Split Transactions');
            break;
        case 'pcie-encoding':
            drawPcieEncodingScenario();
            setExplanation('pcie-encoding-init', 'PCIe Encoding');
            break;
        case 'pcie-multilane':
            drawPcieMultilaneScenario();
            setExplanation('pcie-multilane-init', 'PCIe Multi-Lane');
            break;
        case 'pcie-ack-nak':
            drawPcieAckNakScenario();
            setExplanation('pcie-ack-nak-init', 'PCIe ACK/NAK');
            break;
    }
    
    elements.stepBtn.disabled = false;
}

async function simulationStep() {
    elements.stepBtn.disabled = true;
    const maxSteps = scenarios[currentScenario].steps;

    if (simulationStepCounter >= maxSteps) {
        return;
    }

    switch (currentScenario) {
        case 'bus':
            await runBusScenarioStep(simulationStepCounter);
            break;
        case 'bus-arbitration':
            await runBusArbitrationScenarioStep(simulationStepCounter);
            break;
        case 'point-to-point':
            await runPointToPointScenarioStep(simulationStepCounter);
            break;
        case 'qpi-detailed':
            await runQpiDetailedScenarioStep(simulationStepCounter);
            break;
        case 'pcie-layers':
            await runPcieLayersScenarioStep(simulationStepCounter);
            break;
        case 'pcie-split-transactions':
            await runPcieSplitTransactionsScenarioStep(simulationStepCounter);
            break;
        case 'pcie-encoding':
            await runPcieEncodingScenarioStep(simulationStepCounter);
            break;
        case 'pcie-multilane':
            await runPcieMultilaneScenarioStep(simulationStepCounter);
            break;
        case 'pcie-ack-nak':
            await runPcieAckNakScenarioStep(simulationStepCounter);
            break;
    }

    simulationStepCounter++;
    if (simulationStepCounter < maxSteps) {
        elements.stepBtn.disabled = false;
    }
}

async function runBusScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('bus-step-0', 'Step 1/5');
            // 1. CPU obtains the use of the bus
            document.getElementById('cpu').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 500));
            break;
        case 1:
            // 2. CPU puts address on Address Bus
            setExplanation('bus-step-1', 'Step 2/5');
            await animateTransfer('cpu', 'memory', 'ADDR: 0x1A4', 'address');
            document.getElementById('memory').classList.add('highlight-mar');
            break;
        case 2:
            // 3. CPU signals "Memory Read" on Control Bus
            setExplanation('bus-step-2', 'Step 3/5');
            await animateTransfer('cpu', 'memory', 'CMD: READ', 'control');
            break;
        case 3:
            // 4. Memory puts data on Data Bus
            setExplanation('bus-step-3', 'Step 4/5');
            document.getElementById('memory').classList.remove('highlight-mar');
            document.getElementById('memory').classList.add('highlight-data-read');
            await animateTransfer('memory', 'cpu', 'DATA: 0xBEEF', 'data');
            break;
        case 4:
            // 5. CPU reads data from Data Bus
            setExplanation('bus-step-4', 'Step 5/5');
            document.getElementById('cpu').classList.remove('highlight-active');
            document.getElementById('memory').classList.remove('highlight-data-read');
            // alert('Bus transfer scenario complete.');
            break;
    }
}

async function runBusArbitrationScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('bus-arb-step-0', 'Step 1/10');
            // Multiple devices request bus simultaneously
            document.getElementById('cpu-breq').textContent = 'CPU BREQ: Active';
            document.getElementById('cpu-breq').classList.add('bg-red-300');
            document.getElementById('dma-breq').textContent = 'DMA BREQ: Active';
            document.getElementById('dma-breq').classList.add('bg-blue-300');
            document.getElementById('io-breq').textContent = 'I/O BREQ: Active';
            document.getElementById('io-breq').classList.add('bg-green-300');
            document.getElementById('arbitrator-state').textContent = 'Arbitrating...';
            await new Promise(resolve => setTimeout(resolve, 1000));
            break;
        case 1:
            setExplanation('bus-arb-step-1', 'Step 2/10');
            // Arbitrator grants bus to highest priority (CPU)
            document.getElementById('bus-grant').textContent = 'BGRANT: CPU';
            document.getElementById('bus-grant').classList.add('bg-yellow-300');
            document.getElementById('cpu').classList.add('highlight-active');
            document.getElementById('arbitrator-state').textContent = 'CPU Granted';
            await new Promise(resolve => setTimeout(resolve, 800));
            break;
        case 2:
            setExplanation('bus-arb-step-2', 'Step 3/10');
            // CPU performs its transfer
            await animateTransfer('cpu', 'memory', 'ADDR: 0x100', 'address');
            await animateTransfer('cpu', 'memory', 'CMD: READ', 'control');
            await animateTransfer('memory', 'cpu', 'DATA: 0xABCD', 'data');
            break;
        case 3:
            setExplanation('bus-arb-step-3', 'Step 4/10');
            // CPU releases bus
            document.getElementById('cpu-breq').textContent = 'CPU BREQ: Idle';
            document.getElementById('cpu-breq').classList.remove('bg-red-300');
            document.getElementById('cpu').classList.remove('highlight-active');
            document.getElementById('arbitrator-state').textContent = 'Arbitrating...';
            document.getElementById('bus-grant').textContent = 'BGRANT: None';
            document.getElementById('bus-grant').classList.remove('bg-yellow-300');
            await new Promise(resolve => setTimeout(resolve, 800));
            break;
        case 4:
            setExplanation('bus-arb-step-4', 'Step 5/10');
            // Arbitrator grants bus to next highest priority (DMA)
            document.getElementById('bus-grant').textContent = 'BGRANT: DMA';
            document.getElementById('bus-grant').classList.add('bg-yellow-300');
            document.getElementById('dma-controller').classList.add('highlight-active');
            document.getElementById('arbitrator-state').textContent = 'DMA Granted';
            await new Promise(resolve => setTimeout(resolve, 800));
            break;
        case 5:
            setExplanation('bus-arb-step-5', 'Step 6/10');
            // DMA performs transfer
            await animateTransfer('dma-controller', 'memory', 'ADDR: 0x200', 'address');
            await animateTransfer('dma-controller', 'memory', 'CMD: WRITE', 'control');
            await animateTransfer('dma-controller', 'memory', 'DATA: 0x1234', 'data');
            break;
        case 6:
            setExplanation('bus-arb-step-6', 'Step 7/10');
            // DMA releases bus
            document.getElementById('dma-breq').textContent = 'DMA BREQ: Idle';
            document.getElementById('dma-breq').classList.remove('bg-blue-300');
            document.getElementById('dma-controller').classList.remove('highlight-active');
            document.getElementById('arbitrator-state').textContent = 'Arbitrating...';
            document.getElementById('bus-grant').textContent = 'BGRANT: None';
            document.getElementById('bus-grant').classList.remove('bg-yellow-300');
            await new Promise(resolve => setTimeout(resolve, 800));
            break;
        case 7:
            setExplanation('bus-arb-step-7', 'Step 8/10');
            // Arbitrator grants bus to I/O
            document.getElementById('bus-grant').textContent = 'BGRANT: I/O';
            document.getElementById('bus-grant').classList.add('bg-yellow-300');
            document.getElementById('io-device').classList.add('highlight-active');
            document.getElementById('arbitrator-state').textContent = 'I/O Granted';
            await new Promise(resolve => setTimeout(resolve, 800));
            break;
        case 8:
            setExplanation('bus-arb-step-8', 'Step 9/10');
            // I/O performs transfer
            await animateTransfer('io-device', 'memory', 'ADDR: 0x300', 'address');
            await animateTransfer('io-device', 'memory', 'CMD: WRITE', 'control');
            await animateTransfer('io-device', 'memory', 'DATA: 0x5678', 'data');
            break;
        case 9:
            setExplanation('bus-arb-step-9', 'Step 10/10');
            // All transfers complete
            document.getElementById('io-breq').textContent = 'I/O BREQ: Idle';
            document.getElementById('io-breq').classList.remove('bg-green-300');
            document.getElementById('io-device').classList.remove('highlight-active');
            document.getElementById('bus-grant').textContent = 'BGRANT: None';
            document.getElementById('bus-grant').classList.remove('bg-yellow-300');
            document.getElementById('arbitrator-state').textContent = 'Idle';
            // alert('Bus arbitration scenario complete. Notice how the arbitrator managed conflicting requests.');
            break;
    }
}

async function runPointToPointScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('ptp-step-0', 'Step 1/4');
            // Draw direct connections
            await animateTransfer('cpu-core-a', 'cpu-core-b', '', 'control', { duration: 100, oneway: true });
            await animateTransfer('cpu-core-a', 'io-hub', '', 'control', { duration: 100, oneway: true });
            await animateTransfer('cpu-core-b', 'dram', '', 'control', { duration: 100, oneway: true });
            await animateTransfer('io-hub', 'dram', '', 'control', { duration: 100, oneway: true });
            document.getElementById('cpu-core-a').classList.add('highlight-active');
            break;
        case 1:
            setExplanation('ptp-step-1', 'Step 2/4');
            // Core A sends a packet to the I/O Hub
            await animateTransfer('cpu-core-a', 'io-hub', 'Packet', 'data');
            document.getElementById('io-hub').classList.add('highlight-active');
            break;
        case 2:
            setExplanation('ptp-step-2', 'Step 3/4');
            // At the same time, Core B can access memory
            await animateTransfer('cpu-core-b', 'dram', 'Packet', 'data');
             document.getElementById('dram').classList.add('highlight-active');
            break;
        case 3:
            setExplanation('ptp-step-3', 'Step 4/4');
            // Cleanup highlights
            document.querySelectorAll('.highlight-active').forEach(el => el.classList.remove('highlight-active'));
            // alert('Point-to-Point scenario complete. Notice how two transfers happened simultaneously without contention.');
            break;
    }
}

async function runQpiDetailedScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('qpi-step-0', 'Step 1/6');
            // Core A wants to send data, checks credits
            document.getElementById('core-a').classList.add('highlight-active');
            document.getElementById('core-a-credits').textContent = 'Credits: 7 (sending 1 flit)';
            await new Promise(resolve => setTimeout(resolve, 1000));
            break;
        case 1:
            setExplanation('qpi-step-1', 'Step 2/6');
            // Show phit assembly into flit
            document.getElementById('phit-display').style.opacity = '1';
            document.getElementById('phit-display').textContent = 'Phit 1: 20 bits';
            await new Promise(resolve => setTimeout(resolve, 500));
            document.getElementById('phit-1').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 500));
            document.getElementById('phit-display').textContent = 'Phit 2: 20 bits';
            document.getElementById('phit-2').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 500));
            document.getElementById('phit-display').textContent = 'Phit 3: 20 bits';
            document.getElementById('phit-3').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 500));
            document.getElementById('phit-display').textContent = 'Phit 4: 20 bits';
            document.getElementById('phit-4').style.opacity = '1';
            document.getElementById('flit-status').textContent = 'Status: Complete Flit (80 bits)';
            break;
        case 2:
            setExplanation('qpi-step-2', 'Step 3/6');
            // Flit transmitted to Core B
            await animateTransfer('core-a', 'core-b', 'Flit (80 bits)', 'data');
            document.getElementById('core-b').classList.add('highlight-active');
            document.getElementById('core-b-credits').textContent = 'Credits: 7 (received 1 flit)';
            break;
        case 3:
            setExplanation('qpi-step-3', 'Step 4/6');
            // Core B sends credit back to Core A
            await animateTransfer('core-b', 'core-a', 'Credit Return', 'control');
            document.getElementById('core-a-credits').textContent = 'Credits: 8 (credit returned)';
            break;
        case 4:
            setExplanation('qpi-step-4', 'Step 5/6');
            // Simulate CRC error and retransmission
            document.getElementById('error-handling').style.opacity = '1';
            document.getElementById('crc-check').textContent = 'CRC Status: ERROR DETECTED!';
            document.getElementById('crc-check').style.color = '#ef4444';
            document.getElementById('retransmit-status').textContent = 'Retransmit Queue: 1 flit pending';
            await new Promise(resolve => setTimeout(resolve, 1000));
            // Retransmit
            await animateTransfer('core-a', 'core-b', 'Retransmit Flit', 'data');
            document.getElementById('crc-check').textContent = 'CRC Status: OK (after retransmit)';
            document.getElementById('crc-check').style.color = '#10b981';
            document.getElementById('retransmit-status').textContent = 'Retransmit Queue: Empty';
            break;
        case 5:
            setExplanation('qpi-step-5', 'Step 6/6');
            // Cleanup
            document.getElementById('core-a').classList.remove('highlight-active');
            document.getElementById('core-b').classList.remove('highlight-active');
            // alert('QPI protocol demonstration complete. Notice the credit-based flow control and error recovery.');
            break;
    }
}

async function runPcieLayersScenarioStep(step) {
    switch(step) {
        case 0:
            setExplanation('pcie-step-0', 'Step 1/4');
            // Transaction Layer receives data and adds a header
            document.getElementById('layer-trans').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 1000));
            document.getElementById('trans-header').style.backgroundColor = '#60a5fa'; // blue-400
            break;
        case 1:
            setExplanation('pcie-step-1', 'Step 2/4');
            // Data Link Layer adds sequence number and CRC
            document.getElementById('layer-dll').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 1000));
            document.getElementById('dll-seq').style.backgroundColor = '#4ade80'; // green-400
            document.getElementById('dll-lcrc').style.backgroundColor = '#4ade80';
            break;
        case 2:
            setExplanation('pcie-step-2', 'Step 3/4');
            // Physical Layer adds framing and prepares for transmission
            document.getElementById('layer-phy').style.opacity = '1';
             await new Promise(resolve => setTimeout(resolve, 1000));
            document.getElementById('phy-packet').style.color = '#ef4444'; // red-500
            break;
        case 3:
            setExplanation('pcie-step-3', 'Step 4/4');
            // alert('PCIe packet encapsulation complete. The final packet is now ready to be sent over the physical link.');
            break;
    }
}

async function runPcieSplitTransactionsScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('pcie-split-step-0', 'Step 1/6');
            // CPU initiates memory read request
            document.getElementById('cpu-root').classList.add('highlight-active');
            document.getElementById('cpu-state').textContent = 'State: Sending Request';
            document.getElementById('request-tlp').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 1000));
            break;
        case 1:
            setExplanation('pcie-split-step-1', 'Step 2/6');
            // Request TLP travels to endpoint
            await animateTransfer('cpu-root', 'pcie-endpoint', 'Memory Read Request TLP', 'data');
            document.getElementById('pcie-endpoint').classList.add('highlight-active');
            document.getElementById('endpoint-state').textContent = 'State: Processing Request';
            break;
        case 2:
            setExplanation('pcie-split-step-2', 'Step 3/6');
            // CPU can do other work while waiting
            document.getElementById('cpu-root').classList.remove('highlight-active');
            document.getElementById('cpu-state').textContent = 'State: Executing Other Instructions';
            // Update transaction tracker
            document.querySelector('#pending-transactions div:first-child').textContent = 'Request ID #1: Waiting for completion';
            document.querySelector('#pending-transactions div:first-child').classList.add('bg-yellow-200');
            await new Promise(resolve => setTimeout(resolve, 1500));
            break;
        case 3:
            setExplanation('pcie-split-step-3', 'Step 4/6');
            // Endpoint prepares completion
            document.getElementById('endpoint-state').textContent = 'State: Preparing Completion';
            document.getElementById('completion-tlp').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 1000));
            break;
        case 4:
            setExplanation('pcie-split-step-4', 'Step 5/6');
            // Completion TLP travels back
            await animateTransfer('pcie-endpoint', 'cpu-root', 'Completion TLP', 'data');
            document.getElementById('cpu-root').classList.add('highlight-active');
            document.getElementById('cpu-state').textContent = 'State: Received Data';
            break;
        case 5:
            setExplanation('pcie-split-step-5', 'Step 6/6');
            // Transaction complete
            document.querySelector('#pending-transactions div:first-child').textContent = 'Request ID #1: Complete';
            document.querySelector('#pending-transactions div:first-child').classList.remove('bg-yellow-200');
            document.querySelector('#pending-transactions div:first-child').classList.add('bg-green-200');
            document.getElementById('cpu-root').classList.remove('highlight-active');
            document.getElementById('pcie-endpoint').classList.remove('highlight-active');
            // alert('PCIe split transaction complete. Notice how the CPU could continue working while waiting for the response.');
            break;
    }
}

async function runPcieEncodingScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('pcie-enc-step-0', 'Step 1/5');
            // Show raw data
            document.getElementById('raw-data-bits').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 1000));
            break;
        case 1:
            setExplanation('pcie-enc-step-1', 'Step 2/5');
            // Apply scrambling
            document.getElementById('scrambling-section').style.opacity = '1';
            document.getElementById('scrambled-data').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 1500));
            break;
        case 2:
            setExplanation('pcie-enc-step-2', 'Step 3/5');
            // Show 128b/130b encoding
            document.getElementById('encoding-section').style.opacity = '1';
            await new Promise(resolve => setTimeout(resolve, 1500));
            break;
        case 3:
            setExplanation('pcie-enc-step-3', 'Step 4/5');
            // Serial transmission
            document.getElementById('lane-section').style.opacity = '1';
            document.getElementById('transmission-visual').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 1500));
            break;
        case 4:
            setExplanation('pcie-enc-step-4', 'Step 5/5');
            // Cleanup and summary
            document.querySelectorAll('.highlight-active').forEach(el => el.classList.remove('highlight-active'));
            // alert('PCIe encoding demonstration complete. The 128b/130b encoding adds 1.54% overhead but improves signal quality.');
            break;
    }
}

async function runPcieMultilaneScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('pcie-multi-step-0', 'Step 1/4');
            // Show data distribution
            document.getElementById('transmitter').classList.add('highlight-active');
            const bytes = ['byte-0', 'byte-1', 'byte-2', 'byte-3', 'byte-4', 'byte-5', 'byte-6', 'byte-7'];
            for (let i = 0; i < bytes.length; i++) {
                document.getElementById(bytes[i]).classList.add('highlight-active');
                await new Promise(resolve => setTimeout(resolve, 200));
            }
            break;
        case 1:
            setExplanation('pcie-multi-step-1', 'Step 2/4');
            // Distribute to lanes
            document.getElementById('lane-0-data').style.opacity = '1';
            document.getElementById('lane-0').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 300));
            document.getElementById('lane-1-data').style.opacity = '1';
            document.getElementById('lane-1').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 300));
            document.getElementById('lane-2-data').style.opacity = '1';
            document.getElementById('lane-2').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 300));
            document.getElementById('lane-3-data').style.opacity = '1';
            document.getElementById('lane-3').classList.add('highlight-active');
            break;
        case 2:
            setExplanation('pcie-multi-step-2', 'Step 3/4');
            // Show throughput calculation
            document.getElementById('throughput-section').style.opacity = '1';
            document.getElementById('receiver').classList.add('highlight-active');
            await new Promise(resolve => setTimeout(resolve, 1500));
            break;
        case 3:
            setExplanation('pcie-multi-step-3', 'Step 4/4');
            // Cleanup
            document.querySelectorAll('.highlight-active').forEach(el => el.classList.remove('highlight-active'));
            // alert('PCIe x4 multi-lane demonstration complete. Notice how data is distributed across lanes for higher throughput.');
            break;
    }
}

async function runPcieAckNakScenarioStep(step) {
    switch (step) {
        case 0:
            setExplanation('pcie-ack-step-0', 'Step 1/7');
            // Setup buffers and show first TLP
            document.getElementById('buffer-1').style.opacity = '1';
            document.getElementById('buffer-2').style.opacity = '1';
            document.getElementById('buffer-3').style.opacity = '1';
            document.getElementById('stats-section').style.opacity = '1';
            document.getElementById('tlp-packet').style.opacity = '1';
            document.getElementById('transmitter-dll').classList.add('highlight-active');
            document.getElementById('tx-buffer').textContent = 'TX Buffer: TLP #1';
            await new Promise(resolve => setTimeout(resolve, 1000));
            break;
        case 1:
            setExplanation('pcie-ack-step-1', 'Step 2/7');
            // Send TLP successfully
            await animateTransfer('transmitter-dll', 'receiver-dll', 'TLP #1', 'data');
            document.getElementById('rx-buffer').textContent = 'RX Buffer: TLP #1';
            document.getElementById('receiver-dll').classList.add('highlight-active');
            document.getElementById('packets-sent').textContent = 'Packets Sent: 1';
            await new Promise(resolve => setTimeout(resolve, 800));
            break;
        case 2:
            setExplanation('pcie-ack-step-2', 'Step 3/7');
            // Receiver sends ACK
            document.getElementById('dllp-response').style.opacity = '1';
            document.getElementById('ack-nak-type').textContent = 'Type: ACK';
            document.getElementById('ack-nak-seq').textContent = 'Seq: #1';
            await animateTransfer('receiver-dll', 'transmitter-dll', 'ACK #1', 'control');
            document.getElementById('acks-received').textContent = 'ACKs Received: 1';
            document.getElementById('buffer-1').classList.add('bg-green-200');
            document.getElementById('buffer-1').textContent = 'Seq #1: ACK\'d ✓';
            break;
        case 3:
            setExplanation('pcie-ack-step-3', 'Step 4/7');
            // Send second TLP with error
            document.getElementById('tx-seq').textContent = 'Next Seq: #2';
            document.getElementById('rx-expected').textContent = 'Expected Seq: #2';
            document.getElementById('error-section').style.opacity = '1';
            
            // Simulate corrupted transmission
            await animateTransfer('transmitter-dll', 'receiver-dll', 'TLP #2 (CORRUPTED)', 'data');
            document.getElementById('tlp-packet').innerHTML = `
                <div class="font-bold text-xs text-red-600">TLP</div>
                <div class="text-xs">Seq: #2</div>
                <div class="text-xs">Data: 0x????</div>
                <div class="text-xs text-red-600">LCRC: ERROR</div>
            `;
            document.getElementById('packets-sent').textContent = 'Packets Sent: 2';
            await new Promise(resolve => setTimeout(resolve, 1000));
            break;
        case 4:
            setExplanation('pcie-ack-step-4', 'Step 5/7');
            // Receiver detects error and sends NAK
            document.getElementById('ack-nak-type').textContent = 'Type: NAK';
            document.getElementById('ack-nak-type').style.color = '#ef4444';
            document.getElementById('ack-nak-seq').textContent = 'Seq: #2';
            await animateTransfer('receiver-dll', 'transmitter-dll', 'NAK #2', 'control');
            document.getElementById('naks-received').textContent = 'NAKs Received: 1';
            document.getElementById('buffer-2').classList.add('bg-red-200');
            document.getElementById('buffer-2').textContent = 'Seq #2: NAK\'d ✗';
            break;
        case 5:
            setExplanation('pcie-ack-step-5', 'Step 6/7');
            // Retransmit from NAK'ed packet onwards
            document.getElementById('transmitter-dll').classList.add('highlight-active');
            
            // Retransmit TLP #2
            await animateTransfer('transmitter-dll', 'receiver-dll', 'TLP #2 (RETRY)', 'data');
            document.getElementById('tlp-packet').innerHTML = `
                <div class="font-bold text-xs text-blue-600">TLP</div>
                <div class="text-xs">Seq: #2</div>
                <div class="text-xs">Data: 0xEFGH</div>
                <div class="text-xs text-green-600">LCRC: OK</div>
            `;
            document.getElementById('retransmissions').textContent = 'Retransmissions: 1';
            
            // Also retransmit TLP #3
            await new Promise(resolve => setTimeout(resolve, 500));
            await animateTransfer('transmitter-dll', 'receiver-dll', 'TLP #3 (RETRY)', 'data');
            document.getElementById('retransmissions').textContent = 'Retransmissions: 2';
            break;
        case 6:
            setExplanation('pcie-ack-step-6', 'Step 7/7');
            // Final ACKs
            await animateTransfer('receiver-dll', 'transmitter-dll', 'ACK #2', 'control');
            document.getElementById('buffer-2').classList.remove('bg-red-200');
            document.getElementById('buffer-2').classList.add('bg-green-200');
            document.getElementById('buffer-2').textContent = 'Seq #2: ACK\'d ✓';
            
            await new Promise(resolve => setTimeout(resolve, 500));
            await animateTransfer('receiver-dll', 'transmitter-dll', 'ACK #3', 'control');
            document.getElementById('buffer-3').classList.add('bg-green-200');
            document.getElementById('buffer-3').textContent = 'Seq #3: ACK\'d ✓';
            
            document.getElementById('acks-received').textContent = 'ACKs Received: 3';
            document.querySelectorAll('.highlight-active').forEach(el => el.classList.remove('highlight-active'));
            // alert('PCIe ACK/NAK mechanism complete. Notice how the DLL ensures reliable delivery through retransmission.');
            break;
    }
}


// --- EVENT LISTENERS ---
elements.stepBtn.addEventListener('click', simulationStep);
elements.resetBtn.addEventListener('click', resetSimulation);
elements.scenarioSelect.addEventListener('change', resetSimulation);

// Initial Load
window.addEventListener('load', () => {
    const activeLanguage = window.LanguageManager
        ? LanguageManager.getCurrentLanguage()
        : currentLang;

    applyLanguage(activeLanguage);
});
