<!DOCTYPE html>
<html lang="<?php echo $currentLang ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPU Instruction Cycle Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .register, .memory-cell {
            transition: all 0.3s ease-in-out;
        }
        .highlight-pc {
            background-color: #60a5fa !important; /* blue-400 */
            color: white;
        }
        .highlight-mar {
            background-color: #f87171 !important; /* red-400 */
            color: white;
        }
        .highlight-active {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }
        .highlight-data-read {
            background-color: #4ade80 !important; /* green-400 */
            color: white;
        }
        .highlight-data-write {
            background-color: #facc15 !important; /* yellow-400 */
            color: black;
        }
        .bus {
            position: absolute;
            background-color: rgba(107, 114, 128, 0.7);
            border: 1px solid #4b5563;
            border-radius: 4px;
            color: white;
            font-size: 0.75rem;
            text-align: center;
            padding: 2px 4px;
            z-index: 50;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
            white-space: nowrap;
        }
        .bus.active {
            opacity: 1;
        }
        #address-bus { height: 8px; }
        #data-bus { height: 8px; }
        #control-bus { height: 4px; top: 4px; }
        .arrow {
            position: absolute;
            width: 0;
            height: 0;
            border-style: solid;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .disabled-btn {
            background-color: #9ca3af;
            cursor: not-allowed;
        }
        .i-o-device.waiting {
            border-color: #fbbf24; /* amber-400 */
        }
        .i-o-device.interrupting {
            border-color: #ef4444; /* red-500 */
            animation: blink 1s step-start infinite;
        }
        @keyframes blink {
            50% { border-color: transparent; }
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 100;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.75rem;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .hidden-scenario {
            display: none !important;
        }
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 250px;
            background-color: #555;
            color: #fff;
            text-align: left;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -125px;
            opacity: 0;
            transition: opacity 0.3s;
            white-space: pre-line;
        }
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 flex flex-col items-center justify-center min-h-screen p-4 relative">

    <header class="w-full max-w-7xl mx-auto mb-6">
        <nav class="bg-white p-4 rounded-xl shadow-lg mb-4">
            <div class="flex justify-between items-center">
                <div class="flex space-x-4">
                    <a href="index.php" class="text-gray-700 hover:text-blue-800 font-semibold"><?php echo getTranslation('home', $currentLang); ?></a>
                    <a href="lecture2a.php" class="text-blue-600 hover:text-blue-800 font-semibold"><?php echo getTranslation('lecture2a-title', $currentLang); ?></a>
                    <a href="lecture2b.php" class="text-blue-600 hover:text-blue-800 font-semibold"><?php echo getTranslation('lecture2b-title', $currentLang); ?></a>
                    <a href="lecture3.php" class="text-blue-600 hover:text-blue-800 font-semibold"><?php echo getTranslation('lecture3-title', $currentLang); ?></a>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="lang-en" class="lang-btn bg-blue-500 text-white font-bold py-2 px-4 rounded-l">EN</button>
                    <button id="lang-ru" class="lang-btn bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-r">RU</button>
                </div>
            </div>
        </nav>
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">CS604: Interactive Computer Architecture</h1>
        </div>
    </header>
