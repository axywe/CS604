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
        'stack-memory' => 'Stack Memory',
        
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
        
        'lecture2a-title' => 'Lecture 2a: CPU Simulation',
        'lecture2b-title' => 'Lecture 2b: Interconnects',
        'interconnection-structures' => 'Interconnection Structures',
        'scenario-bus' => 'Scenario: Bus Architecture',
        'scenario-point-to-point' => 'Scenario: Point-to-Point Interconnect',
        'scenario-pcie-layers' => 'Scenario: PCIe Layered Protocol',
        'welcome-text-lecture2b' => "Welcome to the Interconnects simulation. Select a scenario to visualize different data transfer methods between computer components.",

        'bus-scenario-init' => "This scenario demonstrates a memory read operation using a shared system bus, which consists of separate lines for addresses, data, and control signals.",
        'bus-step-0' => "<b>Step 1: Obtain Bus Control</b><br>The CPU needs to send data. First, it must request and be granted control of the shared system bus. Only one device can transmit at a time to avoid signal interference.",
        'bus-step-1' => "<b>Step 2: Send Address</b><br>The CPU places the desired memory address (e.g., 0x1A4) onto the address lines of the bus. All devices on the bus see this address, but only the memory controller will recognize and respond to it.",
        'bus-step-2' => "<b>Step 3: Send Control Signal</b><br>The CPU asserts the 'Memory Read' signal on the control lines. This command informs the memory module that it should retrieve the data from the specified address and place it on the bus.",
        'bus-step-3' => "<b>Step 4: Memory Responds</b><br>The memory module places the requested data (e.g., 0xBEEF) onto the data lines of the bus.",
        'bus-step-4' => "<b>Step 5: CPU Receives Data</b><br>The CPU reads the data from the data lines and copies it into one of its internal registers (like the MBR). The memory read operation is now complete.",

        'ptp-scenario-init' => "This scenario illustrates a Point-to-Point (PTP) interconnect, like Intel's QPI. Instead of a shared bus, components have direct, dedicated links, allowing multiple simultaneous data transfers.",
        'ptp-step-0' => "<b>Step 1: System View</b><br>The components are connected directly. CPU Core A has links to Core B and the I/O Hub. Core B and the I/O hub are connected to Memory. This forms a network or 'fabric'.",
        'ptp-step-1' => "<b>Step 2: Core A to I/O Hub</b><br>Core A sends a data packet directly to the I/O Hub. This transfer does not interfere with any other component.",
        'ptp-step-2' => "<b>Step 3: Core B to Memory</b><br>Simultaneously, Core B can access Main Memory through its own dedicated link. This parallelism is the key advantage over a shared bus, significantly improving performance.",
        'ptp-step-3' => "<b>Step 4: Multiple Transfers</b><br>The simulation shows two independent data transfers occurring at the same time, which would be impossible with a single shared bus.",
        
        'pcie-scenario-init' => "This scenario shows how a data packet is constructed as it moves down the layers of the PCIe protocol stack. Each layer adds its own header and control information, encapsulating the data from the layer above.",
        'pcie-step-0' => "<b>Step 1: Transaction Layer</b><br>The process begins when the software layer sends data. The Transaction Layer receives this data and adds a header, creating a Transaction Layer Packet (TLP). The header contains information like the destination address and transaction type (e.g., memory read/write).",
        'pcie-step-1' => "<b>Step 2: Data Link Layer</b><br>The TLP is passed to the Data Link Layer. This layer adds a sequence number for tracking and an LCRC (Link CRC) for error detection. This ensures reliable delivery across a single link.",
        'pcie-step-2' => "<b>Step 3: Physical Layer</b><br>The packet arrives at the Physical Layer, which adds framing bytes to mark the start and end of the packet. It then encodes the data (e.g., 128b/130b encoding) and sends it over the physical wires as a serial bitstream.",
        'pcie-step-3' => "<b>Step 4: Transmission</b><br>The fully formed packet, with information from all three layers, is now ready for transmission across the physical PCIe link to its destination.",

        // New scenarios for complete lecture 2b coverage
        'scenario-bus-arbitration' => 'Scenario: Bus Arbitration',
        'scenario-qpi-detailed' => 'Scenario: QPI Protocol Details',
        'scenario-pcie-split-transactions' => 'Scenario: PCIe Split Transactions',
        'scenario-pcie-encoding' => 'Scenario: PCIe 128b/130b Encoding',
        'scenario-pcie-multilane' => 'Scenario: PCIe Multi-Lane',
        'scenario-pcie-ack-nak' => 'Scenario: PCIe ACK/NAK Mechanism',

        // Bus Arbitration
        'bus-arbitration-init' => "This scenario demonstrates bus arbitration when multiple masters (CPU, DMA, I/O) compete for access to the shared system bus. The bus arbitrator resolves conflicts using priority-based allocation.",
        'bus-arb-step-0' => "<b>Step 1: Simultaneous Requests</b><br>Multiple devices request bus access simultaneously. CPU, DMA Controller, and I/O Device all assert their Bus Request (BREQ) signals. The arbitrator must decide who gets priority.",
        'bus-arb-step-1' => "<b>Step 2: Priority Resolution</b><br>The bus arbitrator grants access to the highest priority device (usually CPU). It asserts Bus Grant (BGRANT) signal to the CPU while other requests remain pending.",
        'bus-arb-step-2' => "<b>Step 3: CPU Transfer</b><br>CPU performs its memory access operation using the granted bus. During this time, no other device can use the bus, ensuring data integrity.",
        'bus-arb-step-3' => "<b>Step 4: CPU Release</b><br>CPU completes its transfer and releases its Bus Request. The bus is now free, and the arbitrator checks for other pending requests.",
        'bus-arb-step-4' => "<b>Step 5: Grant to DMA</b><br>The arbitrator sees the pending DMA request and grants bus access to the DMA Controller as it has the next highest priority.",
        'bus-arb-step-5' => "<b>Step 6: DMA Transfer</b><br>DMA Controller performs its memory transfer. This demonstrates how DMA can access memory directly without CPU intervention.",
        'bus-arb-step-6' => "<b>Step 7: DMA Release</b><br>After its transfer, the DMA controller releases the bus. The arbitrator now sees the pending I/O request.",
        'bus-arb-step-7' => "<b>Step 8: Grant to I/O</b><br>Finally, the I/O device is granted bus access and can begin its operation.",
        'bus-arb-step-8' => "<b>Step 9: I/O Transfer</b><br>The I/O device completes its memory operation, demonstrating device-to-memory communication.",
        'bus-arb-step-9' => "<b>Step 10: Arbitration Complete</b><br>All pending requests have been serviced. The bus returns to an idle state, ready for new requests. Notice how arbitration prevents bus conflicts.",

        // QPI Details
        'qpi-detailed-init' => "This scenario shows Intel QPI (QuickPath Interconnect) protocol details including phit/flit assembly, credit-based flow control, and error recovery mechanisms.",
        'qpi-step-0' => "<b>Step 1: Credit Check</b><br>Before sending data, Core A checks its credit count. QPI uses credit-based flow control to prevent buffer overflow at the receiver.",
        'qpi-step-1' => "<b>Step 2: Phit to Flit Assembly</b><br>Data is transmitted as 20-bit phits (physical units) that are assembled into 80-bit flits (flow control units). Four phits make one complete flit.",
        'qpi-step-2' => "<b>Step 3: Flit Transmission</b><br>The complete 80-bit flit is transmitted over the QPI link using 20 parallel data lanes plus clock lanes for synchronization.",
        'qpi-step-3' => "<b>Step 4: Credit Return</b><br>Core B processes the flit and returns a credit to Core A, indicating buffer space is available for the next transmission.",
        'qpi-step-4' => "<b>Step 5: Error Detection & Recovery</b><br>QPI includes CRC error detection. When an error is detected, the sender retransmits the corrupted flit, ensuring reliable communication.",
        'qpi-step-5' => "<b>Step 6: Protocol Complete</b><br>The QPI protocol ensures reliable, high-speed communication between cores using credits, error detection, and automatic retransmission.",

        // PCIe Split Transactions
        'pcie-split-init' => "This scenario demonstrates PCIe split transactions where requests and completions are separated in time, allowing the CPU to continue other work while waiting for responses.",
        'pcie-split-step-0' => "<b>Step 1: Request Initiation</b><br>CPU sends a Memory Read Request TLP to a PCIe endpoint. The request includes a unique tag for matching with the eventual completion.",
        'pcie-split-step-1' => "<b>Step 2: Request Transmission</b><br>The request TLP travels through the PCIe fabric to the target endpoint device, which will process the memory read operation.",
        'pcie-split-step-2' => "<b>Step 3: CPU Continues</b><br>Unlike synchronous operations, the CPU doesn't wait idle. It continues executing other instructions while the endpoint processes the request.",
        'pcie-split-step-3' => "<b>Step 4: Completion Preparation</b><br>The endpoint completes the memory read and prepares a Completion TLP containing the requested data and matching tag.",
        'pcie-split-step-4' => "<b>Step 5: Completion Transmission</b><br>The Completion TLP travels back to the CPU, carrying the requested data and the original tag for proper routing.",
        'pcie-split-step-5' => "<b>Step 6: Transaction Complete</b><br>CPU receives the completion and matches it with the original request using the tag. This split transaction model improves overall system efficiency.",

        // PCIe Encoding
        'pcie-encoding-init' => "This scenario demonstrates PCIe's 128b/130b encoding scheme and scrambling techniques used to ensure reliable high-speed serial transmission.",
        'pcie-enc-step-0' => "<b>Step 1: Raw Data</b><br>Starting with 128 bits of raw data that needs to be transmitted over the PCIe link.",
        'pcie-enc-step-1' => "<b>Step 2: Scrambling</b><br>Data is scrambled to improve transition density and spectral properties, helping with clock recovery at the receiver.",
        'pcie-enc-step-2' => "<b>Step 3: 128b/130b Encoding</b><br>The 128-bit data block is encoded into a 130-bit block by adding a 2-bit sync header (10 for data blocks). This adds 1.54% overhead but improves signal quality.",
        'pcie-enc-step-3' => "<b>Step 4: Serial Transmission</b><br>The 130-bit encoded blocks are transmitted serially over differential pairs, with framing sequences marking packet boundaries.",
        'pcie-enc-step-4' => "<b>Step 5: Encoding Complete</b><br>The encoding process balances efficiency (98.46%) with signal integrity, enabling reliable high-speed communication.",

        // PCIe Multi-Lane
        'pcie-multilane-init' => "This scenario shows how PCIe distributes data across multiple lanes using round-robin distribution to achieve higher aggregate bandwidth.",
        'pcie-multi-step-0' => "<b>Step 1: Data Distribution</b><br>Source data is divided byte-by-byte for distribution across multiple parallel lanes in a round-robin fashion.",
        'pcie-multi-step-1' => "<b>Step 2: Lane Assignment</b><br>Each byte is assigned to a specific lane in sequence. PCIe x4 uses 4 differential pairs, allowing 4 bytes to be transmitted simultaneously.",
        'pcie-multi-step-2' => "<b>Step 3: Parallel Transmission</b><br>All lanes transmit simultaneously, with the receiver reconstructing the original data stream from the parallel inputs.",
        'pcie-multi-step-3' => "<b>Step 4: Throughput Calculation</b><br>PCIe x4 at 8 GT/s provides 31.4 GT/s effective bandwidth after 128b/130b encoding overhead, resulting in ~3.9 GB/s aggregate throughput.",

        // PCIe ACK/NAK
        'pcie-ack-nak-init' => "This scenario demonstrates PCIe's Data Link Layer ACK/NAK mechanism for reliable packet delivery, including error detection and automatic retransmission.",
        'pcie-ack-step-0' => "<b>Step 1: Setup</b><br>The transmitter prepares TLPs for transmission. Each TLP is stored in a retransmission buffer and assigned a sequence number for tracking.",
        'pcie-ack-step-1' => "<b>Step 2: Successful Transmission</b><br>TLP #1 is transmitted successfully to the receiver. The Data Link Layer adds sequence number and LCRC for error detection.",
        'pcie-ack-step-2' => "<b>Step 3: ACK Response</b><br>The receiver validates the LCRC, finds no errors, and sends an ACK DLLP back to the transmitter. The transmitter can now remove TLP #1 from its retransmission buffer.",
        'pcie-ack-step-3' => "<b>Step 4: Error Injection</b><br>TLP #2 is corrupted during transmission (simulated error). The receiver detects the LCRC mismatch, indicating data corruption.",
        'pcie-ack-step-4' => "<b>Step 5: NAK Response</b><br>The receiver sends a NAK DLLP for sequence #2, indicating the packet was corrupted and needs retransmission.",
        'pcie-ack-step-5' => "<b>Step 6: Retransmission</b><br>Upon receiving NAK, the transmitter retransmits TLP #2 and all subsequent packets (TLP #3) to maintain ordering. This is the 'Go-Back-N' protocol.",
        'pcie-ack-step-6' => "<b>Step 7: Recovery Complete</b><br>Both retransmitted TLPs are received successfully and ACK'd. The link has recovered from the error transparently to higher layers.",
        'home' => 'Home',
        'lecture-selection' => 'Select a Lecture to Begin'
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
        'stack-memory' => 'Stack Memory',

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
        
        'lecture2a-title' => 'Лекция 2a: Симуляция ЦПУ',
        'lecture2b-title' => 'Лекция 2b: Межсоединения',
        'interconnection-structures' => 'Структуры межсоединений',
        'scenario-bus' => 'Сценарий: Шинная архитектура',
        'scenario-point-to-point' => 'Сценарий: Точка-точка',
        'scenario-pcie-layers' => 'Сценарий: Уровни протокола PCIe',
        'welcome-text-lecture2b' => "Добро пожаловать в симуляцию межсоединений. Выберите сценарий для визуализации различных методов передачи данных между компонентами компьютера.",
        
        'bus-scenario-init' => "Этот сценарий демонстрирует операцию чтения из памяти с использованием общей системной шины, которая состоит из отдельных линий для адресов, данных и управляющих сигналов.",
        'bus-step-0' => "<b>Шаг 1: Получение контроля над шиной</b><br>ЦПУ необходимо отправить данные. Сначала он должен запросить и получить контроль над общей системной шиной. Только одно устройство может передавать данные в один момент времени, чтобы избежать интерференции сигналов.",
        'bus-step-1' => "<b>Шаг 2: Отправка адреса</b><br>ЦПУ помещает требуемый адрес памяти (например, 0x1A4) на адресные линии шины. Все устройства на шине видят этот адрес, но только контроллер памяти распознает и ответит на него.",
        'bus-step-2' => "<b>Шаг 3: Отправка управляющего сигнала</b><br>ЦПУ выставляет сигнал 'Чтение памяти' на линиях управления. Эта команда информирует модуль памяти, что он должен извлечь данные по указанному адресу и поместить их на шину.",
        'bus-step-3' => "<b>Шаг 4: Ответ памяти</b><br>Модуль памяти помещает запрошенные данные (например, 0xBEEF) на линии данных шины.",
        'bus-step-4' => "<b>Шаг 5: ЦПУ получает данные</b><br>ЦПУ считывает данные с линий данных и копирует их в один из своих внутренних регистров (например, MBR). Операция чтения из памяти завершена.",

        'ptp-scenario-init' => "Этот сценарий иллюстрирует межсоединение Точка-Точка (PTP), такое как Intel QPI. Вместо общей шины компоненты имеют прямые, выделенные каналы, что позволяет осуществлять несколько одновременных передач данных.",
        'ptp-step-0' => "<b>Шаг 1: Обзор системы</b><br>Компоненты соединены напрямую. Ядро ЦПУ A имеет соединения с Ядром B и Концентратором В/В. Ядро B и Концентратор В/В соединены с Памятью. Это образует сеть или 'фабрику'.",
        'ptp-step-1' => "<b>Шаг 2: Ядро A к Концентратору В/В</b><br>Ядро A отправляет пакет данных напрямую в Концентратор В/В. Эта передача не мешает никакому другому компоненту.",
        'ptp-step-2' => "<b>Шаг 3: Ядро B к Памяти</b><br>Одновременно Ядро B может обращаться к Основной Памяти через свое собственное выделенное соединение. Этот параллелизм является ключевым преимуществом перед общей шиной, значительно повышая производительность.",
        'ptp-step-3' => "<b>Шаг 4: Множественные передачи</b><br>Симуляция показывает две независимые передачи данных, происходящие одновременно, что было бы невозможно с одной общей шиной.",
        
        'pcie-scenario-init' => "Этот сценарий показывает, как конструируется пакет данных по мере его продвижения вниз по уровням стека протокола PCIe. Каждый уровень добавляет свой собственный заголовок и управляющую информацию, инкапсулируя данные с вышележащего уровня.",
        'pcie-step-0' => "<b>Шаг 1: Транзакционный уровень</b><br>Процесс начинается, когда программный уровень отправляет данные. Транзакционный уровень получает эти данные и добавляет заголовок, создавая пакет транзакционного уровня (TLP). Заголовок содержит информацию, такую как адрес назначения и тип транзакции (например, чтение/запись в память).",
        'pcie-step-1' => "<b>Шаг 2: Канальный уровень</b><br>TLP передается на канальный уровень. Этот уровень добавляет порядковый номер для отслеживания и LCRC (Link CRC) для обнаружения ошибок. Это обеспечивает надежную доставку по одному каналу.",
        'pcie-step-2' => "<b>Шаг 3: Физический уровень</b><br>Пакет поступает на физический уровень, который добавляет байты кадрирования для обозначения начала и конца пакета. Затем он кодирует данные (например, кодирование 128b/130b) и отправляет их по физическим проводам в виде последовательного потока битов.",
        'pcie-step-3' => "<b>Шаг 4: Передача</b><br>Полностью сформированный пакет с информацией со всех трех уровней теперь готов к передаче по физическому каналу PCIe к месту назначения.",

        // Новые сценарии для полного покрытия лекции 2b
        'scenario-bus-arbitration' => 'Сценарий: Арбитраж шины',
        'scenario-qpi-detailed' => 'Сценарий: Детали протокола QPI',
        'scenario-pcie-split-transactions' => 'Сценарий: Разделенные транзакции PCIe',
        'scenario-pcie-encoding' => 'Сценарий: Кодирование PCIe 128b/130b',
        'scenario-pcie-multilane' => 'Сценарий: Многолинейный PCIe',
        'scenario-pcie-ack-nak' => 'Сценарий: Механизм ACK/NAK PCIe',

        // Арбитраж шины
        'bus-arbitration-init' => "Этот сценарий демонстрирует арбитраж шины, когда несколько мастеров (ЦПУ, DMA, В/В) конкурируют за доступ к общей системной шине. Арбитратор шины разрешает конфликты, используя приоритетное распределение.",
        'bus-arb-step-0' => "<b>Шаг 1: Одновременные запросы</b><br>Несколько устройств одновременно запрашивают доступ к шине. ЦПУ, контроллер DMA и устройство В/В активируют свои сигналы запроса шины (BREQ). Арбитратор должен решить, кому отдать приоритет.",
        'bus-arb-step-1' => "<b>Шаг 2: Разрешение приоритета</b><br>Арбитратор шины предоставляет доступ устройству с наивысшим приоритетом (обычно ЦПУ). Он активирует сигнал предоставления шины (BGRANT) для ЦПУ, в то время как другие запросы остаются в ожидании.",
        'bus-arb-step-2' => "<b>Шаг 3: Передача ЦПУ</b><br>ЦПУ выполняет свою операцию доступа к памяти, используя предоставленную шину. В это время никакое другое устройство не может использовать шину, обеспечивая целостность данных.",
        'bus-arb-step-3' => "<b>Шаг 4: Освобождение ЦПУ</b><br>ЦПУ завершает свою передачу и снимает запрос на использование шины. Шина теперь свободна, и арбитр проверяет наличие других ожидающих запросов.",
        'bus-arb-step-4' => "<b>Шаг 5: Предоставление DMA</b><br>Арбитр видит ожидающий запрос от DMA и предоставляет доступ к шине контроллеру DMA, так как у него следующий по величине приоритет.",
        'bus-arb-step-5' => "<b>Шаг 6: Передача DMA</b><br>Контроллер DMA выполняет свою передачу в память. Это демонстрирует, как DMA может обращаться к памяти напрямую без вмешательства ЦПУ.",
        'bus-arb-step-6' => "<b>Шаг 7: Освобождение DMA</b><br>После своей передачи контроллер DMA освобождает шину. Теперь арбитр видит ожидающий запрос от устройства В/В.",
        'bus-arb-step-7' => "<b>Шаг 8: Предоставление В/В</b><br>Наконец, устройство В/В получает доступ к шине и может начать свою операцию.",
        'bus-arb-step-8' => "<b>Шаг 9: Передача В/В</b><br>Устройство В/В завершает свою операцию с памятью, демонстрируя связь устройство-память.",
        'bus-arb-step-9' => "<b>Шаг 10: Арбитраж завершен</b><br>Все ожидающие запросы обслужены. Шина возвращается в состояние ожидания, готовая к новым запросам. Обратите внимание, как арбитраж предотвращает конфликты шины.",

        // Детали QPI
        'qpi-detailed-init' => "Этот сценарий показывает детали протокола Intel QPI (QuickPath Interconnect), включая сборку phit/flit, управление потоком на основе кредитов и механизмы восстановления после ошибок.",
        'qpi-step-0' => "<b>Шаг 1: Проверка кредитов</b><br>Перед отправкой данных Ядро A проверяет свой счет кредитов. QPI использует управление потоком на основе кредитов для предотвращения переполнения буфера у получателя.",
        'qpi-step-1' => "<b>Шаг 2: Сборка Phit в Flit</b><br>Данные передаются как 20-битные phit (физические единицы), которые собираются в 80-битные flit (единицы управления потоком). Четыре phit составляют один полный flit.",
        'qpi-step-2' => "<b>Шаг 3: Передача Flit</b><br>Полный 80-битный flit передается по каналу QPI, используя 20 параллельных линий данных плюс тактовые линии для синхронизации.",
        'qpi-step-3' => "<b>Шаг 4: Возврат кредита</b><br>Ядро B обрабатывает flit и возвращает кредит Ядру A, указывая, что буферное пространство доступно для следующей передачи.",
        'qpi-step-4' => "<b>Шаг 5: Обнаружение ошибок и восстановление</b><br>QPI включает обнаружение ошибок CRC. При обнаружении ошибки отправитель повторно передает поврежденный flit, обеспечивая надежную связь.",
        'qpi-step-5' => "<b>Шаг 6: Протокол завершен</b><br>Протокол QPI обеспечивает надежную высокоскоростную связь между ядрами, используя кредиты, обнаружение ошибок и автоматическую повторную передачу.",

        // Разделенные транзакции PCIe
        'pcie-split-init' => "Этот сценарий демонстрирует разделенные транзакции PCIe, где запросы и завершения разделены во времени, позволяя ЦПУ продолжать другую работу в ожидании ответов.",
        'pcie-split-step-0' => "<b>Шаг 1: Инициация запроса</b><br>ЦПУ отправляет TLP запроса чтения памяти на конечную точку PCIe. Запрос включает уникальный тег для сопоставления с возможным завершением.",
        'pcie-split-step-1' => "<b>Шаг 2: Передача запроса</b><br>TLP запроса проходит через фабрику PCIe к целевому конечному устройству, которое обработает операцию чтения памяти.",
        'pcie-split-step-2' => "<b>Шаг 3: ЦПУ продолжает</b><br>В отличие от синхронных операций, ЦПУ не ждет в простое. Он продолжает выполнять другие инструкции, пока конечная точка обрабатывает запрос.",
        'pcie-split-step-3' => "<b>Шаг 4: Подготовка завершения</b><br>Конечная точка завершает чтение памяти и готовит TLP завершения, содержащий запрошенные данные и соответствующий тег.",
        'pcie-split-step-4' => "<b>Шаг 5: Передача завершения</b><br>TLP завершения возвращается к ЦПУ, неся запрошенные данные и исходный тег для правильной маршрутизации.",
        'pcie-split-step-5' => "<b>Шаг 6: Транзакция завершена</b><br>ЦПУ получает завершение и сопоставляет его с исходным запросом, используя тег. Эта модель разделенных транзакций повышает общую эффективность системы.",

        // Кодирование PCIe
        'pcie-encoding-init' => "Этот сценарий демонстрирует схему кодирования PCIe 128b/130b и методы скремблирования, используемые для обеспечения надежной высокоскоростной последовательной передачи.",
        'pcie-enc-step-0' => "<b>Шаг 1: Исходные данные</b><br>Начинаем со 128 бит исходных данных, которые необходимо передать по каналу PCIe.",
        'pcie-enc-step-1' => "<b>Шаг 2: Скремблирование</b><br>Данные скремблируются для улучшения плотности переходов и спектральных свойств, помогая с восстановлением тактовой частоты у получателя.",
        'pcie-enc-step-2' => "<b>Шаг 3: Кодирование 128b/130b</b><br>128-битный блок данных кодируется в 130-битный блок добавлением 2-битного заголовка синхронизации (10 для блоков данных). Это добавляет 1,54% накладных расходов, но улучшает качество сигнала.",
        'pcie-enc-step-3' => "<b>Шаг 4: Последовательная передача</b><br>130-битные кодированные блоки передаются последовательно по дифференциальным парам с последовательностями кадрирования, отмечающими границы пакетов.",
        'pcie-enc-step-4' => "<b>Шаг 5: Кодирование завершено</b><br>Процесс кодирования балансирует эффективность (98,46%) с целостностью сигнала, обеспечивая надежную высокоскоростную связь.",

        // Многолинейный PCIe
        'pcie-multilane-init' => "Этот сценарий показывает, как PCIe распределяет данные по нескольким линиям, используя циклическое распределение для достижения более высокой совокупной пропускной способности.",
        'pcie-multi-step-0' => "<b>Шаг 1: Распределение данных</b><br>Исходные данные делятся побайтно для распределения по нескольким параллельным линиям циклическим способом.",
        'pcie-multi-step-1' => "<b>Шаг 2: Назначение линий</b><br>Каждый байт назначается определенной линии последовательно. PCIe x4 использует 4 дифференциальные пары, позволяя передавать 4 байта одновременно.",
        'pcie-multi-step-2' => "<b>Шаг 3: Параллельная передача</b><br>Все линии передают одновременно, при этом получатель восстанавливает исходный поток данных из параллельных входов.",
        'pcie-multi-step-3' => "<b>Шаг 4: Расчет пропускной способности</b><br>PCIe x4 на 8 ГТ/с обеспечивает 31,4 ГТ/с эффективной пропускной способности после накладных расходов кодирования 128b/130b, что дает ~3,9 ГБ/с совокупной пропускной способности.",

        // ACK/NAK PCIe
        'pcie-ack-nak-init' => "Этот сценарий демонстрирует механизм ACK/NAK канального уровня PCIe для надежной доставки пакетов, включая обнаружение ошибок и автоматическую повторную передачу.",
        'pcie-ack-step-0' => "<b>Шаг 1: Настройка</b><br>Передатчик готовит TLP для передачи. Каждый TLP сохраняется в буфере повторной передачи и получает порядковый номер для отслеживания.",
        'pcie-ack-step-1' => "<b>Шаг 2: Успешная передача</b><br>TLP #1 успешно передается получателю. Канальный уровень добавляет порядковый номер и LCRC для обнаружения ошибок.",
        'pcie-ack-step-2' => "<b>Шаг 3: Ответ ACK</b><br>Получатель проверяет LCRC, не находит ошибок и отправляет ACK DLLP обратно передатчику. Передатчик теперь может удалить TLP #1 из буфера повторной передачи.",
        'pcie-ack-step-3' => "<b>Шаг 4: Внесение ошибки</b><br>TLP #2 повреждается во время передачи (симулированная ошибка). Получатель обнаруживает несоответствие LCRC, указывающее на повреждение данных.",
        'pcie-ack-step-4' => "<b>Шаг 5: Ответ NAK</b><br>Получатель отправляет NAK DLLP для последовательности #2, указывая, что пакет был поврежден и требует повторной передачи.",
        'pcie-ack-step-5' => "<b>Шаг 6: Повторная передача</b><br>Получив NAK, передатчик повторно передает TLP #2 и все последующие пакеты (TLP #3) для сохранения порядка. Это протокол 'Go-Back-N'.",
        'pcie-ack-step-6' => "<b>Шаг 7: Восстановление завершено</b><br>Оба повторно переданных TLP получены успешно и подтверждены ACK. Канал восстановился от ошибки прозрачно для верхних уровней.",
        'home' => 'Главная',
        'lecture-selection' => 'Выберите лекцию, чтобы начать'
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
