<?php
// Определение переводов для интерфейса
$translations = [
    'en' => [
        'status-idle' => 'Idle',
        'status-processing' => 'Processing...',
        'status-waiting' => 'Finished (Waiting)',
        'status-interrupting' => 'Interrupting!',
        'status-pending' => 'Pending (Blocked)',
        'status-servicing' => 'Servicing...',
        'status-interrupts-disabled' => 'Interrupts Disabled',
        'dma-status-transferring' => 'Transferring from {device}...',
        'cycle-state' => 'Current State: {state}',
        'welcome-text' => 'Welcome! Select a scenario and press "Next Step" to begin the simulation.',
        'scenario-loaded' => 'Scenario "{scenarioName}" loaded. Press "Next Step" to begin.',
        
        // Scenario descriptions
        'scenario-von-neumann-init' => "🏗️ Von Neumann Architecture Demonstration\n\n📋 Learning objective: Understanding the fundamental principle where instructions and data share the same memory space\n\n🎯 Initial state:\n• PC = 0x100 (pointing to first instruction)\n• Memory contains program: LOAD → ADD → STORE → HALT\n• Data values: 42 and 25 stored at addresses 0x104 and 0x105\n• AC = 0 (accumulator empty)\n\n🔄 Execution process:\n1. CPU fetches instructions sequentially from 0x100-0x103\n2. For data operations, CPU accesses the same memory but different addresses (0x104-0x106)\n3. Demonstrates the stored program concept\n\n✅ Final state:\n• AC will contain 67 (42 + 25)\n• Memory location 0x106 will store the result\n• Program halts after 4 instruction cycles\n\n💡 Key concept: The CPU treats memory uniformly. The Program Counter determines whether to interpret memory contents as instructions or data.",
        
        'scenario-dma-init' => "🚛 Direct Memory Access (DMA)\n\n📋 Learning objective: Understanding how DMA controllers operate independently of the CPU\n\n🎯 Initial state:\n• PC = 0x400, 4-instruction program\n• DMA Controller: Idle, ready for commands\n• Source data in disk buffer (0x800-0x804): 5 words of data\n• Destination memory (0x500+): Empty\n• CPU and DMA can operate simultaneously\n\n🔄 Execution process:\n1. CPU sends DMA command: Transfer 5 words from disk to 0x500\n2. CPU delegates task to DMA controller and continues program execution\n3. DMA controller transfers data independently, word by word\n4. DMA uses cycle stealing to access bus when CPU is not using it\n5. CPU executes remaining instructions in parallel with DMA operation\n6. DMA completes transfer and interrupts CPU to report completion\n\n✅ Final state:\n• 5 data words successfully transferred to destination\n• CPU completed its program execution\n• Total execution time reduced compared to CPU-only transfer\n\n💡 Key concept: Modern systems employ multiple DMA controllers for graphics, network, and storage operations, enabling true parallel processing.",
        
        'scenario-simple-addition-init' => "🧮 Basic Program Execution (Slide 11 Reference)\n\n📋 Learning objective: Understanding the complete fetch-decode-execute cycle\n\n🎯 Initial state:\n• PC = 0x300 (program start address)\n• Three instructions: LOAD, ADD, STORE\n• Memory 0x940 = 3, Memory 0x941 = 2 (input data)\n• AC = 0 (accumulator empty)\n• All registers cleared\n\n🔄 Execution process:\n1. LOAD: Fetches value 3 from memory into AC (requires 3 memory cycles)\n2. ADD: Fetches value 2 and adds it to AC (AC becomes 5)\n3. STORE: Writes result back to memory (requires 2 memory cycles)\n4. HALT: Program terminates\n\n✅ Final state:\n• AC = 5 (result of 3 + 2)\n• Memory 0x941 = 5 (original data overwritten with result)\n• Program counter stopped\n• Total execution: approximately 12 simulation steps\n\n💡 Key concept: Each instruction requires multiple CPU cycles. Modern programs execute billions of these cycles per second.",
        
        'scenario-io-wait-init' => "⏳ Programmed I/O Without Interrupts\n\n📋 Learning objective: Understanding the inefficiency of early I/O methods\n\n🎯 Initial state:\n• PC = 0x100, simple 4-instruction program\n• Printer device status: Idle\n• Interrupt system disabled\n• AC = 0\n\n🔄 Execution process:\n1. CPU loads data and starts printer I/O operation\n2. CPU enters wait state: approximately 5 steps with no productive work\n3. CPU continuously polls printer status\n4. CPU can only continue after printer completes operation\n\n✅ Final state:\n• Printer completes operation\n• AC contains final data\n• Program halts\n• Wasted cycles: approximately 3+ CPU cycles spent waiting\n\n💡 Key concept: This demonstrates 'Programmed I/O' inefficiency. The CPU could execute hundreds of instructions during the wait period. This limitation led to the development of interrupt systems.",
        
        'scenario-interrupts-init' => "⚡ I/O with Interrupt System\n\n📋 Learning objective: Understanding how interrupts enable efficient multitasking\n\n🎯 Initial state:\n• PC = 0x100, a program with several arithmetic operations.\n• Printer device: Idle, set to take 20 steps.\n• Interrupt system enabled\n• Interrupt Service Routine (ISR) loaded at 0x050\n\n🔄 Execution process:\n1. CPU starts a printer I/O operation.\n2. The CPU continues to execute the main program (several ADD instructions) while the printer works in parallel.\n3. After 20 steps, the printer finishes and sends an interrupt signal before the main program can HALT.\n4. The CPU saves its current context (PC, AC, etc.) and jumps to the ISR.\n5. The CPU executes the ISR to handle the I/O completion.\n6. The CPU restores its state and resumes the main program from where it left off.\n\n✅ Final state:\n• The printer operation is completed via interrupt handling.\n• The main program also completes its execution.\n• No CPU cycles were wasted waiting for the I/O to complete.\n\n💡 Key concept: Interrupts form the foundation of modern multitasking. Systems handle thousands of interrupts per second from various devices (keyboard, mouse, network, timers).",
        
        'scenario-prioritized-interrupts-init' => "🏆 Priority-Based Interrupt Management\n\n📋 Learning objective: Understanding how systems handle multiple simultaneous interrupts and perform tasks within an ISR.\n\n🎯 Initial state:\n• PC = 0x200, program initiates three I/O operations.\n• AC = 0 (accumulator is empty).\n• Devices: Printer (Priority 2), Disk (Priority 4), and Network (Priority 5).\n• Each device's ISR is programmed to add a unique value to the AC.\n\n🔄 Execution process:\n1. CPU starts Printer, Network, and Disk operations and continues its main program.\n2. The Printer finishes first and interrupts the CPU. The Printer's ISR begins, adding its value to AC.\n3. While the Printer ISR is running, the Network device finishes. Since Network (5) has higher priority than Printer (2), it preempts the Printer ISR.\n4. The state of the Printer ISR is saved, and the Network ISR begins, adding its value to AC.\n5. While the Network ISR runs, the Disk finishes. Its priority (4) is lower than the Network's (5), so its interrupt is held pending.\n6. The Network ISR completes. The system now sees the pending Disk interrupt.\n7. Since the Disk's priority (4) is higher than the original, paused Printer ISR (2), the Disk ISR runs next, adding its value to AC.\n8. The Disk ISR completes. Finally, a system restores the state of the Printer ISR, which runs to completion.\n9. The main program resumes.\n\n✅ Final state:\n• AC will contain the sum of all values added by the ISRs.\n• Demonstrates interrupt preemption based on priority.",
        
        'scenario-io-registers-init' => "🎛️ I/O Control Using Dedicated Registers\n\n📋 Learning objective: Understanding precise I/O device management using the specialized I/OAR and I/OBR registers.\n\n🎯 Initial state:\n• PC = 0x600, 5-instruction sequence\n• I/O Address Register (I/OAR) = 0\n• I/O Buffer Register (I/OBR) = 0\n• Data 0xABCD stored at 0x950\n• Printer device ready for commands\n\n🔄 Execution process:\n1. LOAD: Transfer data 0xABCD into AC\n2. LOAD I/OAR: Set device address (device #1 - printer)\n3. MOVE AC→I/OBR: Transfer data to I/O buffer\n4. START I/O: Initiate operation using prepared registers\n5. System uses I/OAR for device selection, I/OBR for data\n\n✅ Final state:\n• Printer receives data 0xABCD and begins processing\n• I/O registers contain device selection and data information\n• CPU available for other operations while printer works\n\n💡 Key concept: This method provides precise control over I/O operations, essential for device driver development. It contrasts with simpler direct I/O methods.",
        
        'scenario-conditional-jumps-init' => "🔄 Conditional Jump Instructions\n\n📋 Learning objective: Understanding how computers implement decision-making and control flow\n\n🎯 Initial state:\n• PC = 0x700, 10-instruction program with branching logic\n• Test data: 0 (zero) and 5 (non-zero) stored in memory\n• AC = 0 (will be loaded with test values)\n• Status flags configured to track conditions\n\n🔄 Execution process:\n1. LOAD 0 into AC, execute JUMPZ (condition true: AC=0)\n2. Jump taken: PC jumps to 0x705, bypassing intermediate instructions\n3. LOAD 5 into AC, execute JUMPZ (condition false: AC≠0)\n4. Sequential execution: PC continues through remaining instructions\n5. ADD operation processes values\n6. STORE saves final result\n\n✅ Final state:\n• Demonstrates both conditional jump scenarios (taken/not taken)\n• Final result (8) stored in memory\n• PC execution path shows branching behavior\n\n💡 Key concept: Conditional jumps form the foundation of programming logic structures including if/else statements, loops, and function calls.",
        
        'scenario-flags-and-jumps-init' => "🚦 Flags and Conditional Jumps\n\n📋 Learning objective: Understanding how the Status Flags register (Z, N, V) works in conjunction with arithmetic operations and conditional jumps.\n\n🎯 Initial state:\n• PC = 0x700, program with multiple tests\n• Status Flags register is clear (0x0000)\n\n🔄 Execution process:\n1. Zero Flag (Z) Test: An ADD operation results in zero, setting the Z flag.\n2. JUMPZ instruction then reads the Z flag and alters the program flow.\n3. Negative Flag (N) Test: An ADD operation results in a negative value, setting the N flag.\n4. Overflow Flag (V) Test: An ADD operation on the largest positive number (0x7FFF) causes a signed overflow, setting the V flag.\n\n✅ Final state:\n• Demonstrates how Z, N, and V flags are set automatically by the ALU after an arithmetic operation.\n• Shows a practical use of the Z flag for decision-making.\n\n💡 Key concept: Status flags are crucial for implementing high-level programming concepts like if-statements, loops, and error handling (e.g., detecting arithmetic overflows).",
        
        // Execution explanations
        'io-wait-explanation' => "The CPU is in a wait state, constantly checking if the I/O device has finished. This is inefficient, as the CPU can't do other work. This is called 'Programmed I/O' and is the reason interrupts were invented.",
        
        'von-neumann-data-warning' => "The Program Counter now points to a DATA location ({address}). If the CPU were to fetch this, it would try to interpret '{data}' as an instruction, leading to an error. This highlights the core principle of Von Neumann: memory is just a collection of bits, and it's the program's logic (like the PC) that gives them meaning as either instruction or data.",
        
        'instruction-cycle-start' => "The Instruction Cycle begins. The CPU must fetch the next instruction from memory. The Program Counter (PC) holds the address of this instruction. It's the 'pointer' that tells the CPU what to do next.",
        
        'interrupt-start' => "Interrupt detected from {interruptName}! The CPU stops its current work to handle this event. Why? Interrupts have higher priority than normal program flow to ensure that time-sensitive events (like data arriving from a network) are not missed. The current PC value ({pc}) and AC value ({ac}) are saved to known locations so the program can resume later without losing its state.",
        
        'interrupt-start-stack' => "Interrupt detected from {interruptName}! The CPU saves its context (PC: {pc}, AC: {ac}, Flags, etc.) by pushing it onto the stack (at SP: {sp}). Using a stack allows for nested interrupts, where a high-priority interrupt can safely pause a lower-priority one.",
        
        'interrupt-jump' => "The PC is loaded with the starting address of the Interrupt Service Routine (ISR) for the {interruptName}. An ISR is a special mini-program whose only job is to handle a specific event. After it runs, the CPU will return to the original program.",
        
        'interrupts-disabled' => "Interrupts are currently disabled. The CPU will not respond to interrupt signals until they are re-enabled. This is often done during critical sections of code to prevent interference.",
        
        'fetch-1' => "Step 1 (Fetch): The address in the PC is placed on the Address Bus and copied to the Memory Address Register (MAR). The MAR is the CPU's 'gateway' to memory; it can only request data from the single address currently held in the MAR.",
        
        'fetch-2' => "Step 2 (Fetch): The CPU asserts a 'read' signal on the Control Bus. The memory controller sees this, finds the address from MAR, and places the contents of that memory location onto the Data Bus. This data is then copied into the Memory Buffer Register (MBR).",
        
        'fetch-3' => "Step 3 (Fetch): The instruction is moved internally from the MBR to the Instruction Register (IR). The MBR is just a temporary holding area. The IR holds the instruction while it is being decoded. The PC is also incremented to point to the next instruction, preparing for the next cycle in advance.",
        
        'decode' => "The Control Unit (CU) decodes the instruction {instruction} in the IR. It identifies the operation as '{instructionName}' (Opcode: {opcode}). The remaining bits specify the address: {address}.",
        
        'halted' => "HALT instruction. The program has finished its execution. The CPU will stop fetching new instructions.",
        'halted-waiting' => "HALT: The CPU has halted but is still waiting for active I/O devices or pending interrupts to complete.",
        
        'error-opcode' => "Unknown opcode: {opcode}. The Control Unit doesn't recognize this instruction pattern, leading to a program crash or error.",
        
        // LOAD instruction
        'load-1' => "EXECUTE (LOAD): The address part of the instruction ({address}) is moved to the MAR. This is the 'Operand Address Calculation' step. The CPU needs to prepare to fetch the data required for the operation.",
        'load-2' => "EXECUTE (LOAD): The CPU requests a read from the memory address in the MAR. The data is fetched from memory into the MBR. This is the 'Operand Fetch' step.",
        'load-3' => "EXECUTE (LOAD): The data is transferred internally from the MBR to the Accumulator (AC). The instruction is now complete. The goal was to load a value from memory into a CPU register to work with it.",
        
        // ADD instruction
        'add-1' => "EXECUTE (ADD): The address part of the instruction ({address}) is moved to the MAR. The CPU needs to fetch the second number for the addition.",
        'add-2' => "EXECUTE (ADD): The data at the specified address is fetched from memory into the MBR.",
        'add-3' => "EXECUTE (ADD): The Arithmetic Logic Unit (ALU) performs the addition. It takes the current value from the Accumulator (AC) and the value from the MBR, adds them, and stores the result back in the AC. The CPU also updates the Status Flags register to reflect the result: Zero flag (Z) is set if result is 0, Negative flag (N) if result is negative, and Overflow flag (V) if the addition caused an overflow. These flags are essential for conditional operations and error detection.",
        
        // STORE instruction
        'store-1' => "EXECUTE (STORE): The target memory address ({address}) is sent to MAR, and the data from the AC is sent to the MBR. The CPU is preparing to write data *out* to memory.",
        'store-2' => "EXECUTE (STORE): The CPU asserts a 'write' signal on the Control Bus. The memory controller takes the data from the MBR and stores it in the memory location specified by the MAR. The instruction is complete.",
        
        // JUMP instructions
        'jump-1' => "EXECUTE (JUMP): Unconditional jump to address {address}. The Program Counter is directly set to the target address, changing the execution sequence.",
        'jump-conditional-1' => "EXECUTE (JUMP IF ZERO): Checking the Zero (Z) flag. Current AC value: {acValue}. Status Flags: {condition}",
        'jump-conditional-2' => "EXECUTE (JUMP IF ZERO): {jumpTaken}. {explanation}",
        
        // I/O Instructions
        'load-ioar' => "EXECUTE (LOAD I/OAR): The address part of the instruction, which represents a specific I/O device, is loaded into the I/O Address Register. This tells the I/O module which device the CPU wants to communicate with.",
        'move-ac-iobr' => "EXECUTE (MOVE AC->IOBR): Data is moved from the Accumulator to the I/O Buffer Register. This is the data that will be sent to the I/O device.",
        'start-io' => "EXECUTE (START I/O): The I/O operation is initiated. A command is sent via the control bus to the I/O module. The module then uses the device address from I/OAR and the data from I/OBR to perform the operation.",
        'start-io-fail' => "START I/O: Device busy or unknown. The I/O module cannot start a new operation until the current one is finished.",
        'io-write' => "I/O WRITE: A command is sent to the I/O module for {deviceName} to begin an operation. This is a direct I/O method where the device is specified in the instruction itself (not using I/OAR/IOBR). With interrupts enabled, the CPU can continue executing other instructions while the device works in parallel.",
        'io-write-fail' => "I/O WRITE: Device busy or unknown.",
        
        // Interrupt instructions
        'disable-interrupts' => "EXECUTE (DISABLE INTERRUPTS): The interrupt mask flag is set, preventing the CPU from responding to interrupt signals. This is typically done during critical code sections.",
        'enable-interrupts' => "EXECUTE (ENABLE INTERRUPTS): The interrupt mask flag is cleared, allowing the CPU to respond to interrupt signals again.",
        'iret' => "IRET (Return from Interrupt): The ISR is complete. The saved Program Counter and AC values are restored from memory locations 0xFF and 0xFE. This ensures the original program can continue exactly where it left off, as if nothing happened.",
        
        'iret-stack' => "IRET (Return from Interrupt): The ISR is complete. The CPU restores its context by popping the saved values (Flags, AC, PC) from the stack. The Stack Pointer is updated, and the original program resumes exactly where it left off.",
        
        // DMA
        'dma-init' => "DMA: The CPU issues a command to the DMA controller. This command is a 'request' that essentially says: 'Please transfer {count} words of data from {device} to memory starting at address {address}'. The CPU is now completely free to execute other instructions in parallel, which is a huge performance gain.",
        'dma-fail' => "DMA: Unknown device for DMA.",
        'dma-active' => "DMA Active (Cycle Stealing): The DMA controller is transferring data. On each step, it 'steals' a bus cycle to move one word. The CPU can continue to execute instructions, but memory access is shared, slightly slowing down both processes. This is much more efficient than halting the CPU.",
        'dma-complete' => "DMA transfer complete! The DMA controller sends an interrupt to the CPU to report that the entire block of data has been transferred.",
        
        // I/O completion
        'io-complete-interrupt' => "I/O on {deviceName} complete! The device sends a signal to the CPU to report it's finished. An interrupt is now pending.",
        
        // Jump explanations
        'jump-taken' => "Jump taken - PC set to {address}",
        'jump-not-taken' => "Jump not taken - PC remains sequential",
        'jump-condition-met' => "Condition met (AC = 0)",
        'jump-condition-not-met' => "Condition not met (AC ≠ 0)",
        'jump-condition-met-z' => "Condition met (Z flag is 1)",
        'jump-condition-not-met-z' => "Condition not met (Z flag is 0)",
        
        // Interface elements
        'printer' => 'Printer',
        'disk' => 'Disk', 
        'network' => 'Network',
        'main-memory' => 'Main Memory',
        'next-step' => 'Next Step',
        'reset-simulation' => 'Reset Simulation', 
        'instruction-list' => 'Instruction List',
        'control' => 'Control',
        'management' => 'Management',
        'close' => 'Close',
        'instruction-set' => 'Processor Instruction Set',
        'opcode-hex' => 'Opcode (Hex)',
        'instruction' => 'Instruction',
        'description' => 'Description',
        'explanation' => 'Explanation',
        'io-devices' => 'I/O Devices',
        'dma-controller' => 'DMA Controller',
        'memory-address' => 'Memory Address',
        'data-counter' => 'Data Counter',
        'status' => 'Status',
        'scenario-0' => 'Scenario 0: Von Neumann Concept',
        'scenario-1' => 'Scenario 1: Simple Addition', 
        'scenario-2' => 'Scenario 2: I/O Wait (No Interrupts)',
        'scenario-3' => 'Scenario 3: I/O with Interrupts',
        'scenario-4' => 'Scenario 4: Prioritized Interrupts',
        'scenario-5' => 'Scenario 5: Direct Memory Access (DMA)',
        'scenario-6' => 'Scenario 6: I/O with Registers',
        'scenario-7' => 'Scenario 7: Conditional Jumps',
        'scenario-8' => 'Scenario 8: Flags and Jumps',
        'flags-reg-desc' => "The Status Flags register tracks the outcome of arithmetic operations.\nZ (Zero): Set if the result is 0.\nN (Negative): Set if the result is negative.\nV (Overflow): Set if the result exceeds the storable value.\nI (Interrupt Mask): Set if interrupts are disabled.",
        'pc-reg-desc' => "The Program Counter (PC) holds the address of the next instruction to be fetched from memory.",
        'ac-reg-desc' => "The Accumulator (AC) is a general-purpose register used to hold data for arithmetic and logic operations.",
        'mar-reg-desc' => "The Memory Address Register (MAR) holds the memory address for the next read or write operation.",
        'mbr-reg-desc' => "The Memory Buffer Register (MBR) contains the data to be written into memory or receives the data read from memory.",
        'ir-reg-desc' => "The Instruction Register (IR) holds the current instruction while it is being decoded and executed.",
        'ioar-reg-desc' => "The I/O Address Register (I/OAR) specifies the address of a particular I/O device.",
        'iobr-reg-desc' => "The I/O Buffer Register (IOBR) is used for exchanging data between the CPU and an I/O device.",
        'priority-2' => 'Priority 2',
        'priority-4' => 'Priority 4', 
        'priority-5' => 'Priority 5',
        'simulator-title' => 'CS604 Simulator',
        'create-scenario' => 'Create Scenario',
        'scenario-builder' => 'Scenario Builder',
        'components' => 'Components',
        'pc-start' => 'PC Start Address',
        'optional-components' => 'Optional Components',
        'enable-io-devices' => 'Enable I/O Devices',
        'default-devices' => 'Default Devices',
        'custom-device' => 'Custom Device',
        'device-name' => 'Device Name',
        'timing-duration' => 'Timing (duration)',
        'priority' => 'Priority',
        'add-device' => 'Add Device',
        'memory-editor' => 'Memory Editor',
        'address' => 'Address',
        'value' => 'Value',
        'add-memory-entry' => 'Add Memory Entry',
        'explanation-editor' => 'Explanation Editor',
        'import-json' => 'Import from JSON',
        'export-json' => 'Export to JSON',
        'save-scenario' => 'Save as New Scenario',
        'scenario-name' => 'Scenario Name',
        'enable-dma' => 'Enable DMA Controller',
        'delete-scenario' => 'Delete Scenario',
        'edit-scenario' => 'Edit Scenario',
        'apply-changes' => 'Apply Changes',
        'edit-as-json' => 'Edit as JSON',
        'json-text-editor' => 'JSON Text Editor',
        'apply-json' => 'Apply JSON & Close',
        'error-io-device-not-found' => 'I/O Error: Device with code {deviceCode} does not exist in the current scenario.',
        'error-io-device-busy' => 'I/O Error: Device "{deviceName}" is currently busy.',
        'error-dma-no-disk' => 'DMA Error: The "disk" device, required for this DMA operation, is not present in the current scenario.',
    ],
    
    'ru' => [
        'status-idle' => 'Ожидание',
        'status-processing' => 'Обработка...',
        'status-waiting' => 'Завершено (Ожидание)',
        'status-interrupting' => 'Прерывание!',
        'status-pending' => 'Ожидает (Блокировано)',
        'status-servicing' => 'Обслуживание...',
        'status-interrupts-disabled' => 'Прерывания отключены',
        'dma-status-transferring' => 'Передача от {device}...',
        'cycle-state' => 'Текущее Состояние: {state}',
        'welcome-text' => 'Добро пожаловать! Выберите сценарий и нажмите "Следующий шаг", чтобы начать симуляцию.',
        'scenario-loaded' => 'Сценарий "{scenarioName}" загружен. Нажмите "Следующий шаг", чтобы начать.',
        
        // Scenario descriptions
        'scenario-von-neumann-init' => "🏗️ Демонстрация архитектуры Фон Неймана\n\n📋 Цель обучения: Понимание фундаментального принципа совместного хранения инструкций и данных в одном пространстве памяти\n\n🎯 Начальное состояние:\n• PC = 0x100 (указывает на первую инструкцию)\n• Память содержит программу: LOAD → ADD → STORE → HALT\n• Значения данных: 42 и 25 хранятся по адресам 0x104 и 0x105\n• AC = 0 (аккумулятор пуст)\n\n🔄 Процесс выполнения:\n1. ЦПУ последовательно извлекает инструкции из 0x100-0x103\n2. Для операций с данными ЦПУ обращается к той же памяти, но по разным адресам (0x104-0x106)\n3. Демонстрирует концепцию хранимой программы\n\n✅ Конечное состояние:\n• AC будет содержать 67 (42 + 25)\n• Ячейка памяти 0x106 сохранит результат\n• Программа остановится после 4 циклов инструкций\n\n💡 Ключевая концепция: ЦПУ обращается с памятью единообразно. Счетчик команд определяет, интерпретировать ли содержимое памяти как инструкции или данные.",
        
        'scenario-dma-init' => "🚛 Прямой доступ к памяти (DMA)\n\n📋 Цель обучения: Понимание того, как контроллеры DMA работают независимо от ЦПУ\n\n🎯 Начальное состояние:\n• PC = 0x400, 4-инструкционная программа\n• Контроллер DMA: Простаивает, готов к командам\n• Исходные данные в буфере диска (0x800-0x804): 5 слов данных\n• Память назначения (0x500+): Пуста\n• ЦПУ и DMA могут работать одновременно\n\n🔄 Процесс выполнения:\n1. ЦПУ посылает команду DMA: Передать 5 слов с диска в 0x500\n2. ЦПУ делегирует задачу контроллеру DMA и продолжает выполнение программы\n3. Контроллер DMA передает данные независимо, слово за словом\n4. DMA использует кражу циклов для доступа к шине когда ЦПУ ею не пользуется\n5. ЦПУ выполняет оставшиеся инструкции параллельно с операцией DMA\n6. DMA завершает передачу и прерывает ЦПУ для отчета о завершении\n\n✅ Конечное состояние:\n• 5 слов данных успешно переданы в место назначения\n• ЦПУ завершил выполнение своей программы\n• Общее время выполнения сокращено по сравнению с передачей только ЦПУ\n\n💡 Ключевая концепция: Современные системы используют множественные контроллеры DMA для графики, сети и операций хранения, обеспечивая истинную параллельную обработку.",
        
        'scenario-simple-addition-init' => "🧮 Базовое выполнение программы (Ссылка на Слайд 11)\n\n📋 Цель обучения: Понимание полного цикла выборка-декодирование-выполнение\n\n🎯 Начальное состояние:\n• PC = 0x300 (адрес начала программы)\n• Три инструкции: LOAD, ADD, STORE\n• Память 0x940 = 3, Память 0x941 = 2 (входные данные)\n• AC = 0 (аккумулятор пуст)\n• Все регистры очищены\n\n🔄 Процесс выполнения:\n1. LOAD: Извлекает значение 3 из памяти в AC (требует 3 цикла памяти)\n2. ADD: Извлекает значение 2 и добавляет его к AC (AC становится 5)\n3. STORE: Записывает результат обратно в память (требует 2 цикла памяти)\n4. HALT: Программа завершается\n\n✅ Конечное состояние:\n• AC = 5 (результат 3 + 2)\n• Память 0x941 = 5 (исходные данные перезаписаны результатом)\n• Счетчик команд остановлен\n• Общее выполнение: приблизительно 12 шагов симуляции\n\n💡 Ключевая концепция: Каждая инструкция требует несколько циклов ЦПУ. Современные программы выполняют миллиарды таких циклов в секунду.",
        
        'scenario-io-wait-init' => "⏳ Программный ввод-вывод без прерываний\n\n📋 Цель обучения: Понимание неэффективности ранних методов ввода-вывода\n\n🎯 Начальное состояние:\n• PC = 0x100, простая 4-инструкционная программа\n• Статус принтера: Простаивает\n• Система прерываний отключена\n• AC = 0\n\n🔄 Процесс выполнения:\n1. ЦПУ загружает данные и запускает операцию принтера\n2. ЦПУ входит в состояние ожидания: приблизительно 5 шагов без продуктивной работы\n3. ЦПУ непрерывно опрашивает статус принтера\n4. ЦПУ может продолжить работу только после завершения операции принтера\n\n✅ Конечное состояние:\n• Принтер завершает операцию\n• AC содержит финальные данные\n• Программа останавливается\n• Потерянные циклы: приблизительно 3+ циклов ЦПУ потрачено на ожидание\n\n💡 Ключевая концепция: Демонстрирует неэффективность 'Программного В/В'. ЦПУ мог бы выполнить сотни инструкций во время ожидания. Это ограничение привело к разработке систем прерываний.",
        
        'scenario-interrupts-init' => "⚡ Ввод-вывод с системой прерываний\n\n📋 Цель обучения: Понимание того, как прерывания обеспечивают эффективную многозадачность\n\n🎯 Начальное состояние:\n• PC = 0x100, программа с несколькими арифметическими операциями.\n• Принтер: Простаивает, настроен на выполнение за 20 шагов.\n• Система прерываний включена\n• Подпрограмма обработки прерывания (ISR) загружена по адресу 0x050\n\n🔄 Процесс выполнения:\n1. ЦПУ запускает операцию ввода-вывода на принтере.\n2. ЦПУ продолжает выполнять основную программу (несколько инструкций ADD), пока принтер работает параллельно.\n3. Через 20 шагов принтер завершает работу и посылает сигнал прерывания до того, как основная программа сможет остановиться (HALT).\n4. ЦПУ сохраняет свой текущий контекст (PC, AC и т.д.) и переходит к ISR.\n5. ЦПУ выполняет ISR для обработки завершения операции ввода-вывода.\n6. ЦПУ восстанавливает свое состояние и возобновляет выполнение основной программы с того места, где она была прервана.\n\n✅ Конечное состояние:\n• Операция принтера завершена с помощью обработки прерывания.\n• Основная программа также завершает свое выполнение.\n• Циклы ЦПУ не были потрачены впустую на ожидание завершения ввода-вывода.\n\n💡 Ключевая концепция: Прерывания составляют основу современной многозадачности. Системы обрабатывают тысячи прерываний в секунду от различных устройств (клавиатура, мышь, сеть, таймеры).",
        
        'scenario-prioritized-interrupts-init' => "🏆 Управление прерываниями на основе приоритетов\n\n📋 Цель обучения: Понимание того, как системы обрабатывают несколько одновременных прерываний и выполняют задачи в рамках ISR.\n\n🎯 Начальное состояние:\n• PC = 0x200, программа инициирует три операции В/В.\n• AC = 0 (аккумулятор пуст).\n• Устройства: Принтер (Приоритет 2), Диск (Приоритет 4) и Сеть (Приоритет 5).\n• ISR каждого устройства запрограммирован на добавление уникального значения в AC.\n\n🔄 Процесс выполнения:\n1. ЦПУ запускает операции Принтера, Сети и Диска и продолжает свою основную программу.\n2. Принтер завершает работу первым и прерывает ЦПУ. Начинается ISR Принтера, добавляя свое значение в AC.\n3. Во время выполнения ISR Принтера завершает работу Сетевое устройство. Поскольку приоритет Сети (5) выше, чем у Принтера (2), оно вытесняет ISR Принтера.\n4. Состояние ISR Принтера сохраняется, и начинает выполняться ISR Сети, добавляя свое значение в AC.\n5. Пока работает ISR Сети, завершает работу Диск. Его приоритет (4) ниже, чем у Сети (5), поэтому его прерывание удерживается в ожидании.\n6. ISR Сети завершается. Теперь система видит ожидающее прерывание от Диска.\n7. Поскольку приоритет Диска (4) выше, чем у исходного, приостановленного ISR Принтера (2), следующим запускается ISR Диска, добавляя свое значение в AC.\n8. ISR Диска завершается. Наконец, система восстанавливает состояние ISR Принтера, который выполняется до конца.\n9. Основная программа возобновляется.\n\n✅ Конечное состояние:\n• AC будет содержать сумму всех значений, добавленных ISR.\n• Демонстрирует вытеснение прерываний на основе приоритета.",
        
        'scenario-io-registers-init' => "🎛️ Управление В/В с использованием выделенных регистров\n\n📋 Цель обучения: Понимание точного управления устройствами В/В с использованием специализированных регистров I/OAR и I/OBR.\n\n🎯 Начальное состояние:\n• PC = 0x600, последовательность из 5 инструкций\n• Регистр Адреса В/В (I/OAR) = 0\n• Буферный Регистр В/В (I/OBR) = 0\n• Данные 0xABCD хранятся по адресу 0x950\n• Принтер готов к командам\n\n🔄 Процесс выполнения:\n1. LOAD: Передача данных 0xABCD в AC\n2. LOAD I/OAR: Установка адреса устройства (устройство №1 - принтер)\n3. MOVE AC→I/OBR: Передача данных в буфер В/В\n4. START I/O: Инициация операции с использованием подготовленных регистров\n5. Система использует I/OAR для выбора устройства, I/OBR для данных\n\n✅ Конечное состояние:\n• Принтер получает данные 0xABCD и начинает обработку\n• Регистры В/В содержат информацию о выборе устройства и данных\n• ЦПУ доступен для других операций пока принтер работает\n\n💡 Ключевая концепция: Этот метод обеспечивает точный контроль над операциями В/В, необходимый для разработки драйверов устройств. Он контрастирует с более простыми прямыми методами В/В.",
        
        'scenario-conditional-jumps-init' => "🔄 Инструкции условного перехода\n\n📋 Цель обучения: Понимание того, как компьютеры реализуют принятие решений и управление потоком\n\n🎯 Начальное состояние:\n• PC = 0x700, 10-инструкционная программа с логикой ветвления\n• Тестовые данные: 0 (ноль) и 5 (не ноль) хранятся в памяти\n• AC = 0 (будет загружен тестовыми значениями)\n• Флаги состояния настроены для отслеживания условий\n\n🔄 Процесс выполнения:\n1. LOAD 0 в AC, выполнение JUMPZ (условие истинно: AC=0)\n2. Переход выполнен: PC переходит к 0x705, обходя промежуточные инструкции\n3. LOAD 5 в AC, выполнение JUMPZ (условие ложно: AC≠0)\n4. Последовательное выполнение: PC продолжает через оставшиеся инструкции\n5. Операция ADD обрабатывает значения\n6. STORE сохраняет финальный результат\n\n✅ Конечное состояние:\n• Демонстрирует оба сценария условного перехода (выполнен/не выполнен)\n• Финальный результат (8) сохранен в памяти\n• Путь выполнения PC показывает поведение ветвления\n\n💡 Ключевая концепция: Условные переходы составляют основу структур логики программирования включая операторы if/else, циклы и вызовы функций.",
        
        'scenario-flags-and-jumps-init' => "🚦 Флаги и условные переходы\n\n📋 Цель обучения: Понимание того, как регистр флагов состояния (Z, N, V) работает совместно с арифметическими операциями и условными переходами.\n\n🎯 Начальное состояние:\n• PC = 0x700, программа с несколькими тестами\n• Регистр флагов состояния сброшен (0x0000)\n\n🔄 Процесс выполнения:\n1. Тест флага нуля (Z): Операция ADD дает в результате ноль, устанавливая флаг Z.\n2. Инструкция JUMPZ затем читает флаг Z и изменяет поток выполнения программы.\n3. Тест флага отрицательного числа (N): Операция ADD дает в результате отрицательное значение, устанавливая флаг N.\n4. Тест флага переполнения (V): Операция ADD с максимальным положительным числом (0x7FFF) вызывает знаковое переполнение, устанавливая флаг V.\n\n✅ Конечное состояние:\n• Демонстрирует, как флаги Z, N и V автоматически устанавливаются АЛУ после арифметической операции.\n• Показывает практическое применение флага Z для принятия решений.\n\n💡 Ключевая концепция: Флаги состояния критически важны для реализации высокоуровневых концепций программирования, таких как операторы if, циклы и обработка ошибок (например, обнаружение арифметических переполнений).",
        
        // Execution explanations
        'io-wait-explanation' => "ЦПУ находится в состоянии ожидания, постоянно проверяя, завершило ли устройство ввода-вывода работу. Это неэффективно, так как ЦПУ не может выполнять другую работу. Этот метод называется 'Программный Ввод-Вывод' и является причиной изобретения прерываний.",
        
        'von-neumann-data-warning' => "Счетчик команд теперь указывает на ячейку с ДАННЫМИ ({address}). Если ЦПУ попытается извлечь это, он интерпретирует '{data}' как инструкцию, что приведет к ошибке. Это подчеркивает ключевой принцип Фон Неймана: память — это просто набор битов, и только логика программы (в лице PC) придает им значение как инструкции или данным.",
        
        'instruction-cycle-start' => "Начинается цикл инструкции. ЦПУ должен извлечь следующую инструкцию из памяти. Счетчик команд (PC) содержит адрес этой инструкции. Это 'указатель', который говорит ЦПУ, что делать дальше.",
        
        'interrupt-start' => "Обнаружено прерывание от {interruptName}! ЦПУ прекращает свою текущую работу, чтобы обработать это событие. Почему? Прерывания имеют более высокий приоритет, чем обычный ход программы, чтобы гарантировать, что чувствительные ко времени события (например, прибытие данных из сети) не будут пропущены. Текущее значение PC ({pc}) и AC ({ac}) сохраняются в известные места, чтобы программа могла возобновиться позже, не теряя своего состояния.",
        
        'interrupt-start-stack' => "Обнаружено прерывание от {interruptName}! ЦПУ сохраняет свой контекст (PC: {pc}, AC: {ac}, флаги и др.), помещая его в стек (по адресу SP: {sp}). Использование стека позволяет обрабатывать вложенные прерывания, когда прерывание с высоким приоритетом может безопасно приостановить обработчик прерывания с более низким приоритетом.",

        'interrupt-jump' => "В PC загружается начальный адрес подпрограммы обработки прерывания (ISR) для {interruptName}. ISR — это специальная мини-программа, единственная задача которой — обработать определенное событие. После ее выполнения ЦПУ вернется к исходной программе.",
        
        'interrupts-disabled' => "Прерывания в настоящее время отключены. ЦПУ не будет отвечать на сигналы прерывания, пока они не будут повторно включены. Это часто делается во время критических участков кода для предотвращения вмешательства.",
        
        'fetch-1' => "Шаг 1 (Выборка): Адрес из PC помещается на шину адреса и копируется в регистр адреса памяти (MAR). MAR является 'шлюзом' ЦПУ к памяти; он может запрашивать данные только с одного адреса, который в данный момент в нем хранится.",
        
        'fetch-2' => "Шаг 2 (Выборка): ЦПУ подает сигнал 'чтение' на шину управления. Контроллер памяти видит это, находит адрес из MAR и помещает содержимое этой ячейки памяти на шину данных. Эти данные затем копируются в регистр-буфер памяти (MBR).",
        
        'fetch-3' => "Шаг 3 (Выборка): Инструкция внутренне перемещается из MBR в регистр инструкций (IR). MBR — это просто временная область хранения. IR удерживает инструкцию во время ее декодирования. PC также увеличивается, чтобы указывать на следующую инструкцию, заранее готовясь к следующему циклу.",
        
        'decode' => "Устройство управления (УУ) декодирует инструкцию {instruction} в IR. Оно определяет операцию как '{instructionName}' (Код операции: {opcode}). Оставшиеся биты указывают на адрес: {address}.",
        
        'halted' => "Инструкция HALT. Программа завершила свое выполнение. ЦПУ прекратит выборку новых инструкций.",
        'halted-waiting' => "HALT: Процессор остановлен, но все еще ожидает завершения активных операций В/В или обработки ожидающих прерываний.",

        'error-opcode' => "Неизвестный код операции: {opcode}. Устройство управления не распознает этот паттерн инструкции, что приводит к сбою программы или ошибке.",
        
        // LOAD instruction
        'load-1' => "ВЫПОЛНЕНИЕ (LOAD): Адресная часть инструкции ({address}) перемещается в MAR. Это шаг 'Вычисление адреса операнда'. ЦПУ необходимо подготовиться к выборке данных, необходимых для операции.",
        'load-2' => "ВЫПОЛНЕНИЕ (LOAD): ЦПУ запрашивает чтение из адреса памяти в MAR. Данные извлекаются из памяти в MBR. Это шаг 'Выборка операнда'.",
        'load-3' => "ВЫПОЛНЕНИЕ (LOAD): Данные передаются внутренне из MBR в аккумулятор (AC). Инструкция теперь завершена. Цель состояла в том, чтобы загрузить значение из памяти в регистр ЦПУ для дальнейшей работы с ним.",
        
        // ADD instruction
        'add-1' => "ВЫПОЛНЕНИЕ (ADD): Адресная часть инструкции ({address}) перемещается в MAR. ЦПУ необходимо извлечь второе число для сложения.",
        'add-2' => "ВЫПОЛНЕНИЕ (ADD): Данные по указанному адресу извлекаются из памяти в MBR.",
        'add-3' => "ВЫПОЛНЕНИЕ (ADD): Арифметико-логическое устройство (АЛУ) выполняет сложение. Оно берет текущее значение из аккумулятора (AC) и значение из MBR, складывает их и сохраняет результат обратно в AC. ЦПУ также обновляет регистр флагов состояния, чтобы отразить результат: флаг нуля (Z) устанавливается, если результат равен 0, флаг отрицательности (N), если результат отрицательный, и флаг переполнения (V), если сложение вызвало переполнение. Эти флаги необходимы для условных операций и обнаружения ошибок.",
        
        // STORE instruction
        'store-1' => "ВЫПОЛНЕНИЕ (STORE): Целевой адрес памяти ({address}) отправляется в MAR, а данные из AC — в MBR. ЦПУ готовится к записи данных *в* память.",
        'store-2' => "ВЫПОЛНЕНИЕ (STORE): ЦПУ подает сигнал 'запись' на шину управления. Контроллер памяти берет данные из MBR и сохраняет их в ячейку памяти, указанную в MAR. Инструкция завершена.",
        
        // JUMP instructions
        'jump-1' => "ВЫПОЛНЕНИЕ (JUMP): Безусловный переход на адрес {address}. Счетчик команд напрямую устанавливается на целевой адрес, изменяя последовательность выполнения.",
        'jump-conditional-1' => "ВЫПОЛНЕНИЕ (JUMP IF ZERO): Проверка флага нуля (Z). Текущее значение AC: {acValue}. Флаги состояния: {condition}",
        'jump-conditional-2' => "ВЫПОЛНЕНИЕ (JUMP IF ZERO): {jumpTaken}. {explanation}",
        
        // I/O Instructions
        'load-ioar' => "ВЫПОЛНЕНИЕ (LOAD I/OAR): Адресная часть инструкции, представляющая конкретное устройство ввода-вывода, загружается в регистр адреса ввода-вывода. Это сообщает модулю В/В, с каким устройством ЦПУ хочет общаться.",
        'move-ac-iobr' => "ВЫПОЛНЕНИЕ (MOVE AC->IOBR): Данные перемещаются из аккумулятора в буферный регистр ввода-вывода. Эти данные будут отправлены на устройство В/В.",
        'start-io' => "ВЫПОЛНЕНИЕ (START I/O): Инициируется операция ввода-вывода. Команда отправляется через шину управления в модуль В/В. Затем модуль использует адрес устройства из I/OAR и данные из I/OBR для выполнения операции.",
        'start-io-fail' => "START I/O: Устройство занято или неизвестно. Модуль В/В не может начать новую операцию, пока не завершена текущая.",
        'io-write' => "ЗАПИСЬ В/В: Команда отправляется в модуль В/В для {deviceName} для начала операции. Это прямой метод В/В, при котором устройство указывается в самой инструкции (без использования I/OAR/IOBR). С включенными прерываниями ЦПУ может продолжать выполнение программы, пока устройство работает параллельно.",
        'io-write-fail' => "ЗАПИСЬ В/В: Устройство занято или неизвестно.",
        
        // Interrupt instructions
        'disable-interrupts' => "ВЫПОЛНЕНИЕ (ОТКЛЮЧИТЬ ПРЕРЫВАНИЯ): Флаг маски прерываний установлен, предотвращая ответ ЦПУ на сигналы прерывания. Это обычно делается во время критических участков кода.",
        'enable-interrupts' => "ВЫПОЛНЕНИЕ (ВКЛЮЧИТЬ ПРЕРЫВАНИЯ): Флаг маски прерываний сброшен, позволяя ЦПУ снова отвечать на сигналы прерывания.",
        'iret' => "IRET (Возврат из прерывания): ISR завершена. Сохраненные значения счетчика команд и AC восстанавливаются из ячеек памяти 0xFF и 0xFE. Это гарантирует, что исходная программа может продолжить работу точно с того места, где она была прервана, как будто ничего не произошло.",
        
        'iret-stack' => "IRET (Возврат из прерывания): ISR завершена. ЦПУ восстанавливает свой контекст, извлекая сохраненные значения (флаги, AC, PC) из стека. Указатель стека обновляется, и исходная программа возобновляется точно с того места, где была прервана.",
        
        // DMA
        'dma-init' => "DMA: ЦПУ выдает команду контроллеру DMA. Эта команда является 'запросом', который по сути гласит: 'Пожалуйста, передай {count} слов данных с {device} в память, начиная с адреса {address}'. Теперь ЦПУ полностью свободен для параллельного выполнения других инструкций, что является огромным выигрышем в производительности.",
        'dma-fail' => "DMA: Неизвестное устройство для DMA.",
        'dma-active' => "DMA активен ('Кража циклов'): Контроллер DMA передает данные. На каждом шаге он 'крадет' один цикл шины для перемещения одного слова. ЦПУ может продолжать выполнять инструкции, но доступ к памяти разделяется, что немного замедляет оба процесса. Это намного эффективнее, чем останавливать ЦПУ.",
        'dma-complete' => "Передача DMA завершена! Контроллер DMA отправляет прерывание ЦПУ, чтобы сообщить, что весь блок данных был передан.",
        
        // I/O completion
        'io-complete-interrupt' => "В/В на {deviceName} завершено! Устройство отправляет сигнал ЦПУ, чтобы сообщить о завершении. Теперь ожидается прерывание.",
        
        // Jump explanations
        'jump-taken' => "Переход выполнен - PC установлен на {address}",
        'jump-not-taken' => "Переход не выполнен - PC остается последовательным",
        'jump-condition-met' => "Условие выполнено (AC = 0)",
        'jump-condition-not-met' => "Условие не выполнено (AC ≠ 0)",
        'jump-condition-met-z' => "Условие выполнено (Z флаг равен 1)",
        'jump-condition-not-met-z' => "Условие не выполнено (Z флаг равен 0)",
        
        // Interface elements
        'printer' => 'Принтер',
        'disk' => 'Диск',
        'network' => 'Сеть', 
        'main-memory' => 'Основная память',
        'next-step' => 'Следующий шаг',
        'reset-simulation' => 'Сброс симуляции',
        'instruction-list' => 'Список команд',
        'control' => 'Управление',
        'management' => 'Управление',
        'close' => 'Закрыть',
        'instruction-set' => 'Набор команд процессора',
        'opcode-hex' => 'Opcode (Hex)',
        'instruction' => 'Инструкция',
        'description' => 'Описание',
        'explanation' => 'Объяснение',
        'io-devices' => 'Устройства I/O',
        'dma-controller' => 'Контроллер DMA',
        'memory-address' => 'Адрес памяти',
        'data-counter' => 'Счетчик данных',
        'status' => 'Статус',
        'scenario-0' => 'Сценарий 0: Концепция Фон Неймана',
        'scenario-1' => 'Сценарий 1: Простое сложение',
        'scenario-2' => 'Сценарий 2: Ожидание I/O (без прерываний)',
        'scenario-3' => 'Сценарий 3: I/O с прерываниями',
        'scenario-4' => 'Сценарий 4: Приоритетные прерывания',
        'scenario-5' => 'Сценарий 5: Прямой доступ к памяти (DMA)',
        'scenario-6' => 'Сценарий 6: I/O с регистрами',
        'scenario-7' => 'Сценарий 7: Условные переходы',
        'scenario-8' => 'Сценарий 8: Флаги и переходы',
        'flags-reg-desc' => "Регистр флагов состояния отслеживает результат арифметических операций.\nZ (Ноль): Устанавливается, если результат равен 0.\nN (Отрицательный): Устанавливается, если результат отрицательный.\nV (Переполнение): Устанавливается, если результат превышает доступное значение.\nI (Маска прерываний): Устанавливается, если обработка прерываний отключена.",
        'pc-reg-desc' => "Счетчик команд (PC) содержит адрес следующей инструкции, которую необходимо извлечь из памяти.",
        'ac-reg-desc' => "Аккумулятор (AC) — это регистр общего назначения, используемый для хранения данных для арифметических и логических операций.",
        'mar-reg-desc' => "Регистр адреса памяти (MAR) содержит адрес памяти для следующей операции чтения или записи.",
        'mbr-reg-desc' => "Буферный регистр памяти (MBR) содержит данные для записи в память или получает данные, считанные из памяти.",
        'ir-reg-desc' => "Регистр инструкций (IR) содержит текущую инструкцию во время ее декодирования и выполнения.",
        'ioar-reg-desc' => "Регистр адреса ввода-вывода (I/OAR) указывает адрес конкретного устройства ввода-вывода.",
        'iobr-reg-desc' => "Буферный регистр ввода-вывода (IOBR) используется для обмена данными между ЦП и устройством ввода-вывода.",
        'priority-2' => 'Приоритет 2',
        'priority-4' => 'Приоритет 4',
        'priority-5' => 'Приоритет 5',
        'simulator-title' => 'Симулятор CS604',
        'create-scenario' => 'Создать сценарий',
        'scenario-builder' => 'Конструктор сценариев',
        'components' => 'Компоненты',
        'pc-start' => 'Начальный адрес PC',
        'optional-components' => 'Опциональные компоненты',
        'enable-io-devices' => 'Включить устройства в/в',
        'default-devices' => 'Устройства по умолчанию',
        'custom-device' => 'Пользовательское устройство',
        'device-name' => 'Имя устройства',
        'timing-duration' => 'Тайминг (длительность)',
        'priority' => 'Приоритет',
        'add-device' => 'Добавить устройство',
        'memory-editor' => 'Редактор памяти',
        'address' => 'Адрес',
        'value' => 'Значение',
        'add-memory-entry' => 'Добавить запись в память',
        'explanation-editor' => 'Редактор объяснения',
        'import-json' => 'Импорт из JSON',
        'export-json' => 'Экспорт в JSON',
        'save-scenario' => 'Сохранить как новый сценарий',
        'scenario-name' => 'Название сценария',
        'enable-dma' => 'Включить контроллер DMA',
        'delete-scenario' => 'Удалить сценарий',
        'edit-scenario' => 'Редактировать сценарий',
        'apply-changes' => 'Применить изменения',
        'edit-as-json' => 'Редактировать как JSON',
        'json-text-editor' => 'Текстовый редактор JSON',
        'apply-json' => 'Применить JSON и закрыть',
        'error-io-device-not-found' => 'Ошибка В/В: Устройство с кодом {deviceCode} не существует в текущем сценарии.',
        'error-io-device-busy' => 'Ошибка В/В: Устройство "{deviceName}" в данный момент занято.',
        'error-dma-no-disk' => 'Ошибка DMA: Устройство "disk", необходимое для этой операции DMA, отсутствует в текущем сценарии.',
    ]
];

// Код операций с описаниями
$opcodeMap = [
    'en' => [
        0x0 => "HALT: Stops the program.",
        0x1 => "LOAD: Loads data from memory into the Accumulator (AC).",
        0x2 => "STORE: Stores data from the Accumulator (AC) into memory.",
        0x3 => "JUMP: Unconditional jump to address.",
        0x4 => "JUMPZ: Jump to address if AC equals zero.",
        0x5 => "ADD: Adds data from memory to the Accumulator (AC).",
        0x6 => "DISABLE_INT: Disables interrupt handling.",
        0x7 => "ENABLE_INT: Enables interrupt handling.",
        0x9 => "LOAD I/OAR: Loads a device address into the I/O Address Register.",
        0xA => "MOVE AC->IOBR: Moves data from the AC to the I/O Buffer Register.",
        0xB => "START I/O: Starts an I/O operation using I/OAR and I/OBR.",
        0xE => "IRET: Returns from an interrupt.",
        0xF => "I/O or DMA: Initiates a complex I/O or DMA operation."
    ],
    'ru' => [
        0x0 => "HALT: Останавливает программу.",
        0x1 => "LOAD: Загружает данные из памяти в Аккумулятор (AC).",
        0x2 => "STORE: Сохраняет данные из Аккумулятора (AC) в память.",
        0x3 => "JUMP: Безусловный переход на адрес.",
        0x4 => "JUMPZ: Переход на адрес, если AC равен нулю.",
        0x5 => "ADD: Добавляет данные из памяти к Аккумулятору (AC).",
        0x6 => "DISABLE_INT: Отключает обработку прерываний.",
        0x7 => "ENABLE_INT: Включает обработку прерываний.",
        0x9 => "LOAD I/OAR: Загружает адрес устройства в Регистр Адреса Ввода-Вывода.",
        0xA => "MOVE AC->IOBR: Перемещает данные из AC в Буферный Регистр Ввода-Вывода.",
        0xB => "START I/O: Начинает операцию ввода-вывода, используя I/OAR и I/OBR.",
        0xE => "IRET: Возвращает из прерывания.",
        0xF => "I/O or DMA: Инициирует сложную операцию ввода-вывода или DMA."
    ]
];

// Функция для получения перевода
function getTranslation($key, $lang = 'en', $params = []) {
    global $translations;
    
    $text = $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
    
    // Заменяем параметры в тексте
    foreach ($params as $param => $value) {
        $text = str_replace('{' . $param . '}', $value, $text);
    }
    
    return $text;
}

// Получение текущего языка из параметров или сессии
$currentLang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!isset($translations[$currentLang])) {
    $currentLang = 'en';
}

// Сохранение языка в сессии
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['lang'] = $currentLang;
?>
