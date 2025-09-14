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
                        <div class="flex gap-2">
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
                            <button id="delete-scenario-btn" class="bg-red-500 hover:bg-red-600 text-white font-bold p-2 rounded-lg hidden" title="<?php echo getTranslation('delete-scenario', $currentLang); ?>">🗑️</button>
                        </div>
                        <button id="step-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                            <?php echo getTranslation('next-step', $currentLang); ?>
                        </button>
                        <button id="reset-btn" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('reset-simulation', $currentLang); ?></button>
                        <button id="edit-scenario-btn" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">
                            <?php echo getTranslation('edit-scenario', $currentLang); ?>
                        </button>
                        <button id="opcodes-btn" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg mt-2"><?php echo getTranslation('instruction-list', $currentLang); ?></button>
                    </div>
                </div>
            </div>

            <!-- Middle Panel: I/O and DMA -->
            <div id="middle-panel" class="lg:w-1/3 w-full flex flex-col gap-6">
                <div id="io-device-panel" class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-xl font-semibold mb-3 text-center border-b pb-2"><?php echo getTranslation('io-devices', $currentLang); ?></h3>
                    <div id="io-devices-container" class="space-y-3">
                        <!-- I/O Devices will be generated here by JavaScript -->
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

    <!-- Modal for scenario builder -->
    <div id="builder-modal" class="modal-overlay hidden">
        <div class="modal-content max-h-screen overflow-y-auto w-full max-w-4xl">
            <h2 class="text-2xl font-bold mb-4 text-center"><?php echo getTranslation('scenario-builder', $currentLang); ?></h2>
            
            <div class="mb-4">
                <label for="builder-scenario-name" class="block text-sm font-medium text-gray-700"><?php echo getTranslation('scenario-name', $currentLang); ?></label>
                <input type="text" id="builder-scenario-name" class="mt-1 p-2 w-full border rounded" value="My Custom Scenario">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left side: Components and I/O -->
                <div>
                    <!-- Components -->
                    <div class="bg-gray-100 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold mb-2 border-b pb-2"><?php echo getTranslation('components', $currentLang); ?></h3>
                        <div class="mb-2">
                            <label for="builder-pc-start" class="block text-sm font-medium text-gray-700"><?php echo getTranslation('pc-start', $currentLang); ?></label>
                            <input type="text" id="builder-pc-start" name="builder-pc-start" class="mt-1 p-2 w-full border rounded" value="0x100">
                        </div>
                        <p class="text-sm mb-2 text-gray-600">PC, IR, MAR, MBR, AC are always included.</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input type="checkbox" id="builder-reg-ioar" name="builder-reg-ioar" checked>
                                <label for="builder-reg-ioar">I/O AR</label>
                            </div>
                            <div>
                                <input type="checkbox" id="builder-reg-iobr" name="builder-reg-iobr" checked>
                                <label for="builder-reg-iobr">I/O BR</label>
                            </div>
                            <div>
                                <input type="checkbox" id="builder-reg-flags" name="builder-reg-flags" checked>
                                <label for="builder-reg-flags">Status Flags</label>
                            </div>
                             <div>
                                <input type="checkbox" id="builder-reg-sp" name="builder-reg-sp" checked>
                                <label for="builder-reg-sp">Stack Pointer (SP)</label>
                            </div>
                            <div>
                                <input type="checkbox" id="builder-enable-dma" name="builder-enable-dma">
                                <label for="builder-enable-dma"><?php echo getTranslation('enable-dma', $currentLang); ?></label>
                            </div>
                        </div>
                    </div>

                    <!-- I/O Devices -->
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2 border-b pb-2"><?php echo getTranslation('io-devices', $currentLang); ?></h3>
                        <div>
                            <input type="checkbox" id="builder-enable-io" name="builder-enable-io" checked>
                            <label for="builder-enable-io"><?php echo getTranslation('enable-io-devices', $currentLang); ?></label>
                        </div>
                        <div id="builder-io-options" class="mt-2 space-y-4">
                            <h4 class="font-semibold mt-4"><?php echo getTranslation('default-devices', $currentLang); ?></h4>
                            <div id="builder-default-devices-list">
                                <!-- Default devices will be populated here -->
                            </div>
                            <h4 class="font-semibold mt-4"><?php echo getTranslation('custom-device', $currentLang); ?></h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                                <input type="text" id="builder-device-name" placeholder="<?php echo getTranslation('device-name', $currentLang); ?>" class="p-2 border rounded col-span-2">
                                <input type="number" id="builder-device-timing" placeholder="<?php echo getTranslation('timing-duration', $currentLang); ?>" class="p-2 border rounded">
                                <input type="number" id="builder-device-priority" placeholder="<?php echo getTranslation('priority', $currentLang); ?>" class="p-2 border rounded">
                            </div>
                            <button id="builder-add-device-btn" class="w-full bg-blue-500 text-white p-2 mt-2 rounded"><?php echo getTranslation('add-device', $currentLang); ?></button>
                            <div id="builder-custom-devices-list" class="mt-2 space-y-1">
                                <!-- Custom devices will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side: Memory and Explanation -->
                <div>
                    <!-- Memory Editor -->
                    <div class="bg-gray-100 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold mb-2 border-b pb-2"><?php echo getTranslation('memory-editor', $currentLang); ?></h3>
                        <div class="grid grid-cols-1 md:grid-cols-[1fr,1fr,auto] gap-2 mb-2">
                             <input type="text" id="builder-mem-address" placeholder="<?php echo getTranslation('address', $currentLang); ?> (e.g., 0x100)" class="p-2 border rounded min-w-0">
                             <input type="text" id="builder-mem-value" placeholder="<?php echo getTranslation('value', $currentLang); ?> (e.g., 0x1104)" class="p-2 border rounded min-w-0">
                             <button id="builder-add-mem-btn" class="bg-blue-500 text-white p-2 rounded"><?php echo getTranslation('add-memory-entry', $currentLang); ?></button>
                        </div>
                        <div id="builder-memory-list" class="max-h-48 overflow-y-auto border rounded bg-white p-2">
                            <!-- Memory entries will be listed here -->
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2 border-b pb-2"><?php echo getTranslation('explanation-editor', $currentLang); ?></h3>
                        <textarea id="builder-explanation" class="w-full h-32 p-2 border rounded" placeholder="Enter scenario explanation here..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col md:flex-row gap-4">
                <button id="builder-import-btn" class="w-full md:w-auto bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('import-json', $currentLang); ?></button>
                <input type="file" id="builder-import-file" class="hidden" accept=".json">
                <button id="builder-export-btn" class="w-full md:w-auto bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('export-json', $currentLang); ?></button>
                <button id="builder-edit-json-btn" class="w-full md:w-auto bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('edit-as-json', $currentLang); ?></button>
                <div class="flex-grow"></div> <!-- Spacer -->
                <button id="builder-apply-btn" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('apply-changes', $currentLang); ?></button>
                <button id="builder-save-btn" class="w-full md:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('save-scenario', $currentLang); ?></button>
                <button id="builder-close-btn" class="w-full md:w-auto bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('close', $currentLang); ?></button>
            </div>
        </div>
    </div>

    <!-- Modal for JSON text editor -->
    <div id="json-editor-modal" class="modal-overlay hidden">
        <div class="modal-content max-h-screen overflow-y-auto w-full max-w-2xl flex flex-col">
            <h2 class="text-2xl font-bold mb-4 text-center"><?php echo getTranslation('json-text-editor', $currentLang); ?></h2>
            <textarea id="json-editor-textarea" class="w-full flex-grow font-mono text-sm p-2 border rounded bg-gray-800 text-white" style="min-height: 400px;"></textarea>
            <div class="mt-4 flex justify-end gap-4">
                 <button id="json-editor-apply-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('apply-json', $currentLang); ?></button>
                 <button id="json-editor-close-btn" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('close', $currentLang); ?></button>
            </div>
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
