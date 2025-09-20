<?php
require_once __DIR__ . '/includes/bootstrap.php';

include 'header.php';
?>

<div class="w-full max-w-7xl mx-auto z-10">
    <div class="flex flex-col xl:flex-row gap-6">

        <!-- Control Panel -->
        <div class="xl:w-1/4 w-full bg-white p-6 rounded-xl shadow-lg flex flex-col gap-4">
            <h2 class="text-2xl font-semibold text-center border-b pb-2"><?php echo getTranslation('lecture3-title', $currentLang); ?></h2>
            <p id="intro-text" class="text-sm text-gray-600 text-center"><?php echo getTranslation('welcome-text-lecture3', $currentLang); ?></p>

            <div>
                <label for="scenario-select" class="block text-sm font-semibold text-gray-700 mb-2"><?php echo getTranslation('control', $currentLang); ?></label>
                <select id="scenario-select" class="w-full p-2 border rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="memoryHierarchy"><?php echo getTranslation('scenario-memory-hierarchy', $currentLang); ?></option>
                    <option value="cacheHit"><?php echo getTranslation('scenario-cache-hit', $currentLang); ?></option>
                    <option value="cacheMiss"><?php echo getTranslation('scenario-cache-miss', $currentLang); ?></option>
                    <option value="virtualMemory"><?php echo getTranslation('scenario-virtual-memory', $currentLang); ?></option>
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <button id="step-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105"><?php echo getTranslation('next-step', $currentLang); ?></button>
                <button id="reset-btn" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg"><?php echo getTranslation('reset-simulation', $currentLang); ?></button>
            </div>

            <div class="bg-gray-100 rounded-lg p-4 text-center">
                <h3 id="scenario-name" class="text-lg font-semibold text-gray-800"><?php echo getTranslation('scenario-memory-hierarchy', $currentLang); ?></h3>
                <p id="scenario-description" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('memory-hierarchy-description', $currentLang); ?></p>
                <p id="step-indicator" class="text-sm font-semibold text-blue-600 mt-3">—</p>
            </div>
        </div>

        <!-- Visualization Panel -->
        <div class="xl:w-1/2 w-full flex flex-col gap-6">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-2xl font-semibold mb-4 text-center border-b pb-2"><?php echo getTranslation('lecture3-topology-heading', $currentLang); ?></h2>
                <div id="component-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div data-component="cpu" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-cpu', $currentLang); ?></h3>
                        <p data-component-status="cpu" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                    <div data-component="l1" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-l1', $currentLang); ?></h3>
                        <p data-component-status="l1" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                    <div data-component="l2" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-l2', $currentLang); ?></h3>
                        <p data-component-status="l2" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                    <div data-component="memory" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-memory', $currentLang); ?></h3>
                        <p data-component-status="memory" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                    <div data-component="disk" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-disk', $currentLang); ?></h3>
                        <p data-component-status="disk" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                    <div data-component="page-table" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-page-table', $currentLang); ?></h3>
                        <p data-component-status="page-table" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                    <div data-component="tlb" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-tlb', $currentLang); ?></h3>
                        <p data-component-status="tlb" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                    <div data-component="os" class="component-card border-2 border-gray-200 rounded-xl p-4 bg-white shadow-sm text-center transition-transform duration-300">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo getTranslation('lecture3-component-os', $currentLang); ?></h3>
                        <p data-component-status="os" class="text-sm text-gray-600 mt-2"><?php echo getTranslation('status-idle', $currentLang); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Explanation & Timeline -->
        <div class="xl:w-1/4 w-full flex flex-col gap-6">
            <div class="bg-white p-6 rounded-xl shadow-lg min-h-[220px]">
                <h2 class="text-2xl font-semibold mb-2 text-center border-b pb-2"><?php echo getTranslation('explanation', $currentLang); ?></h2>
                <div id="explanation-box" class="text-center text-lg text-gray-700 p-4">
                    <p id="explanation-text" style="white-space: pre-line;"><?php echo getTranslation('welcome-text-lecture3', $currentLang); ?></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-xl font-semibold mb-3 text-center border-b pb-2"><?php echo getTranslation('lecture3-timeline-heading', $currentLang); ?></h2>
                <ul id="timeline-list" class="space-y-2 text-sm"></ul>
            </div>
        </div>

    </div>
</div>

<script>
const translations = <?php echo json_encode($translations); ?>;
let currentLang = '<?php echo $currentLang; ?>';
</script>
<script src="lecture3.js"></script>

</body>
</html>
