<?php
require_once __DIR__ . '/includes/bootstrap.php';

include 'header.php';
?>

    <div id="bus-container" class="absolute top-0 left-0 w-full h-full pointer-events-none"></div>

    <div class="w-full max-w-7xl mx-auto z-10">
        <div class="flex flex-col lg:flex-row gap-6">

            <!-- Left Panel: Control -->
            <div id="left-panel" class="lg:w-1/3 w-full bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-2xl font-semibold mb-4 text-center border-b pb-2"><?php echo getTranslation('interconnection-structures', $currentLang); ?></h2>
                
                 <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-3 text-center border-b pb-2"><?php echo getTranslation('control', $currentLang); ?></h3>
                    <div class="flex flex-col gap-3">
                        <select id="scenario-select" class="w-full p-2 border rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="bus"><?php echo getTranslation('scenario-bus', $currentLang); ?></option>
                            <option value="bus-arbitration"><?php echo getTranslation('scenario-bus-arbitration', $currentLang); ?></option>
                            <option value="point-to-point"><?php echo getTranslation('scenario-point-to-point', $currentLang); ?></option>
                            <option value="qpi-detailed"><?php echo getTranslation('scenario-qpi-detailed', $currentLang); ?></option>
                            <option value="pcie-layers"><?php echo getTranslation('scenario-pcie-layers', $currentLang); ?></option>
                            <option value="pcie-split-transactions"><?php echo getTranslation('scenario-pcie-split-transactions', $currentLang); ?></option>
                            <option value="pcie-encoding"><?php echo getTranslation('scenario-pcie-encoding', $currentLang); ?></option>
                            <option value="pcie-multilane"><?php echo getTranslation('scenario-pcie-multilane', $currentLang); ?></option>
                            <option value="pcie-ack-nak"><?php echo getTranslation('scenario-pcie-ack-nak', $currentLang); ?></option>
                        </select>
                        <button id="step-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                            <?php echo getTranslation('next-step', $currentLang); ?>
                        </button>
                        <button id="reset-btn" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('reset-simulation', $currentLang); ?></button>
                    </div>
                </div>

            </div>

            <!-- Middle Panel: Visualization -->
            <div id="middle-panel" class="lg:w-2/3 w-full flex flex-col gap-6">
                <div id="visualization-panel" class="bg-white p-6 rounded-xl shadow-lg relative h-[700px]">
                     <!-- Visualization will be dynamically created here -->
                </div>
                 <div class="bg-white p-6 rounded-xl shadow-lg min-h-[200px]">
                    <h2 class="text-2xl font-semibold mb-2 text-center border-b pb-2"><?php echo getTranslation('explanation', $currentLang); ?></h2>
                    <div id="explanation-box" class="text-center text-lg text-gray-700 p-4">
                        <p id="explanation-text" style="white-space: pre-line;"><?php echo getTranslation('welcome-text-lecture2b', $currentLang); ?></p>
                        <p id="cycle-state-text" class="font-bold text-blue-600 mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Передаем переводы в JavaScript
    const translations = <?php echo json_encode($translations); ?>;
    let currentLang = '<?php echo $currentLang; ?>';
    </script>
    <script src="lecture2b.js"></script>

</body>
</html>
