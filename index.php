<?php
// Подключаем файл с переводами
require_once 'translations.php';

// Получаем текущий язык
$currentLang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!isset($translations[$currentLang])) {
    $currentLang = 'en';
}

// Сохраняем язык в сессии
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['lang'] = $currentLang;

// Подключаем заголовок
include 'header.php';
?>

    <div id="bus-container" class="absolute top-0 left-0 w-full h-full pointer-events-none"></div>

    <div class="w-full max-w-7xl mx-auto z-10">
        <div class="flex flex-col lg:flex-row gap-6">

            <!-- Left Panel: CPU and Control -->
            <div id="left-panel" class="lg:w-1/3 w-full bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-2xl font-semibold mb-4 text-center border-b pb-2">CPU</h2>
                
                <div id="cpu-registers" class="grid grid-cols-2 gap-4">
                    <div id="reg-pc" class="register bg-blue-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-blue-800">Program Counter (PC)
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('pc-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-2xl font-mono">0300</p>
                    </div>
                    <div id="reg-ir" class="register bg-purple-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-purple-800">Instruction Register (IR)
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('ir-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-2xl font-mono">0000</p>
                    </div>
                    <div id="reg-mar" class="register bg-red-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-red-800">Mem Address Reg (MAR)
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('mar-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-2xl font-mono">0000</p>
                    </div>
                    <div id="reg-mbr" class="register bg-green-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-green-800">Mem Buffer Reg (MBR)
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('mbr-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-2xl font-mono">0000</p>
                    </div>
                    <div id="reg-ioar" class="register bg-cyan-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-cyan-800">I/O Address Reg (I/OAR)
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('ioar-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-2xl font-mono">0000</p>
                    </div>
                    <div id="reg-iobr" class="register bg-teal-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-teal-800">I/O Buffer Reg (I/OBR)
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('iobr-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-2xl font-mono">0000</p>
                    </div>
                    <!-- Новый регистр флагов -->
                    <div id="reg-flags" class="register bg-gray-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-gray-800">
                            Status Flags
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('flags-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-lg font-mono">0000</p>
                    </div>
                    <!-- Новый регистр SP -->
                    <div id="reg-sp" class="register bg-orange-100 p-3 rounded-lg text-center">
                        <p class="font-bold text-sm text-orange-800">Stack Pointer (SP)</p>
                        <p class="text-2xl font-mono">0000</p>
                    </div>
                    <div id="reg-ac" class="register bg-yellow-100 p-3 rounded-lg text-center col-span-2">
                        <p class="font-bold text-sm text-yellow-800">Accumulator (AC)
                            <span class="tooltip">&#9432;
                                <span class="tooltiptext"><?php echo getTranslation('ac-reg-desc', $currentLang); ?></span>
                            </span>
                        </p>
                        <p class="text-3xl font-mono">0000</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-3 text-center border-b pb-2"><?php echo getTranslation('control', $currentLang); ?></h3>
                    <div class="flex flex-col gap-3">
                        <select id="scenario-select" class="w-full p-2 border rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="vonNeumann"><?php echo getTranslation('scenario-0', $currentLang); ?></option>
                            <option value="program1"><?php echo getTranslation('scenario-1', $currentLang); ?></option>
                            <option value="program2"><?php echo getTranslation('scenario-2', $currentLang); ?></option>
                            <option value="program3"><?php echo getTranslation('scenario-3', $currentLang); ?></option>
                            <option value="program4"><?php echo getTranslation('scenario-4', $currentLang); ?></option>
                            <option value="dma"><?php echo getTranslation('scenario-5', $currentLang); ?></option>
                            <option value="ioRegisters"><?php echo getTranslation('scenario-6', $currentLang); ?></option>
                            <option value="conditionalJumps"><?php echo getTranslation('scenario-7', $currentLang); ?></option>
                            <option value="flagsAndJumps"><?php echo getTranslation('scenario-8', $currentLang); ?></option>
                        </select>
                        <button id="step-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                            <?php echo getTranslation('next-step', $currentLang); ?>
                        </button>
                        <button id="reset-btn" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('reset-simulation', $currentLang); ?></button>
                        <button id="opcodes-btn" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg mt-2"><?php echo getTranslation('instruction-list', $currentLang); ?></button>
                    </div>
                </div>
            </div>

            <!-- Middle Panel: I/O and DMA -->
            <div id="middle-panel" class="lg:w-1/3 w-full flex flex-col gap-6">
                <div id="io-device-panel" class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-xl font-semibold mb-3 text-center border-b pb-2"><?php echo getTranslation('io-devices', $currentLang); ?></h3>
                    <div class="space-y-3">
                        <div id="printer" class="i-o-device border-2 border-gray-300 p-3 rounded-lg text-center">
                            <p class="font-bold"><?php echo getTranslation('printer', $currentLang); ?> (<?php echo getTranslation('priority-2', $currentLang); ?>)</p>
                            <p id="printer-status" class="text-sm"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                <div id="printer-progress" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div id="disk" class="i-o-device border-2 border-gray-300 p-3 rounded-lg text-center">
                            <p class="font-bold"><?php echo getTranslation('disk', $currentLang); ?> (<?php echo getTranslation('priority-4', $currentLang); ?>)</p>
                            <p id="disk-status" class="text-sm"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                <div id="disk-progress" class="bg-orange-500 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div id="network" class="i-o-device border-2 border-gray-300 p-3 rounded-lg text-center">
                            <p class="font-bold"><?php echo getTranslation('network', $currentLang); ?> (<?php echo getTranslation('priority-5', $currentLang); ?>)</p>
                            <p id="network-status" class="text-sm"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                <div id="network-progress" class="bg-red-500 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="dma-controller-panel" class="bg-white p-6 rounded-xl shadow-lg hidden">
                    <h3 class="text-xl font-semibold mb-3 text-center border-b pb-2"><?php echo getTranslation('dma-controller', $currentLang); ?></h3>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div id="dma-mar" class="register bg-gray-200 p-2 rounded">
                            <p class="font-bold text-sm"><?php echo getTranslation('memory-address', $currentLang); ?></p>
                            <p class="font-mono text-lg">0000</p>
                        </div>
                        <div id="dma-count" class="register bg-gray-200 p-2 rounded">
                            <p class="font-bold text-sm"><?php echo getTranslation('data-counter', $currentLang); ?></p>
                            <p class="font-mono text-lg">0</p>
                        </div>
                        <div id="dma-status" class="col-span-2 mt-2">
                            <p class="font-bold text-sm"><?php echo getTranslation('status', $currentLang); ?>: <span class="font-normal"><?php echo getTranslation('status-idle', $currentLang); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Memory and Explanations -->
            <div id="right-panel" class="lg:w-2/3 w-full flex flex-col gap-6">
                <div id="memory-panel" class="bg-white p-6 rounded-xl shadow-lg">
                    <h2 class="text-2xl font-semibold mb-4 text-center border-b pb-2"><?php echo getTranslation('main-memory', $currentLang); ?></h2>
                    <div id="memory-grid" class="grid grid-cols-4 md:grid-cols-8 gap-2 text-sm">
                        <!-- Memory cells will be generated here by JavaScript -->
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg min-h-[200px]">
                    <h2 class="text-2xl font-semibold mb-2 text-center border-b pb-2"><?php echo getTranslation('explanation', $currentLang); ?></h2>
                    <div id="explanation-box" class="text-center text-lg text-gray-700 p-4">
                        <p id="explanation-text" style="white-space: pre-line;"><?php echo getTranslation('welcome-text', $currentLang); ?></p>
                        <p id="cycle-state-text" class="font-bold text-blue-600 mt-2"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal for opcodes -->
    <div id="opcodes-modal" class="modal-overlay hidden">
        <div class="modal-content max-h-screen overflow-y-auto">
            <h2 class="text-2xl font-bold mb-4 text-center"><?php echo getTranslation('instruction-set', $currentLang); ?></h2>
            <div class="max-h-96 overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white">
                        <tr>
                            <th class="border-b-2 p-2"><?php echo getTranslation('opcode-hex', $currentLang); ?></th>
                            <th class="border-b-2 p-2"><?php echo getTranslation('instruction', $currentLang); ?></th>
                            <th class="border-b-2 p-2"><?php echo getTranslation('description', $currentLang); ?></th>
                        </tr>
                    </thead>
                    <tbody id="opcodes-table-body">
                        <!-- Opcode rows will be generated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <button id="close-modal-btn" class="mt-6 w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('close', $currentLang); ?></button>
        </div>
    </div>

    <script>
    // Передаем переводы в JavaScript
    const translations = <?php echo json_encode($translations); ?>;
    const opcodeMap = <?php echo json_encode($opcodeMap); ?>;
    let currentLang = '<?php echo $currentLang; ?>';
    </script>
    <script src="scripts.js"></script>

</body>
</html>
