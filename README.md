# CS604 Interactive Computer Architecture Simulator

This project is an interactive, web-based simulator designed to visually explain fundamental concepts of computer architecture as covered in the CS604 course. It provides a hands-on experience for understanding the CPU instruction cycle, memory operations, I/O handling, and various data interconnection structures.

The simulator is bilingual (English and Russian) and features detailed, step-by-step explanations for each process.

## Features

- **Interactive CPU Simulation**: Step-by-step execution of machine-level instructions, showing how data moves between the CPU, registers, and memory.
- **Detailed Visualizations**: Clear, color-coded diagrams that highlight active components and data paths during each cycle.
- **Bilingual Support**: Full user interface and explanation text available in both English and Russian.
- **Multiple Scenarios**: A comprehensive set of pre-built scenarios covering key topics from two lectures.
- **Scenario Editor**: A built-in tool to create, edit, and save custom simulation scenarios to explore different programs and hardware configurations.
- **In-depth Explanations**: Each step of every simulation is accompanied by a detailed explanation of the underlying concepts.

## Covered Topics

The simulator is divided into two main parts, corresponding to two lectures.

### Lecture 2a: CPU Simulation

This part focuses on the internal workings of the CPU and its interaction with memory and I/O devices.

- **Von Neumann Architecture**: Demonstrates the core concept of a shared memory space for instructions and data.
- **Instruction Cycle**: Visualizes the complete fetch-decode-execute cycle for instructions like `LOAD`, `ADD`, and `STORE`.
- **CPU Registers**: Shows the role and state of key registers (PC, AC, MAR, MBR, IR, SP, Flags).
- **I/O Operations**:
    - **Programmed I/O**: Illustrates the inefficiency of polling-based I/O.
    - **Interrupt-Driven I/O**: Shows how interrupts allow the CPU to perform other tasks while waiting for I/O.
    - **Prioritized Interrupts**: Simulates a nested interrupt scenario where higher-priority devices can preempt lower-priority ones.
- **Direct Memory Access (DMA)**: Explains how a DMA controller can manage data transfers independently of the CPU using "cycle stealing".
- **Control Flow**: Demonstrates how conditional jumps (`JUMPZ`) and status flags (Zero, Negative, Overflow) are used to implement program logic.

### Lecture 2b: Interconnection Structures

This part visualizes how different components in a computer system communicate with each other.

- **Shared Bus Architecture**: Simulates a traditional system bus, including the process of **Bus Arbitration** to manage contention.
- **Point-to-Point Interconnects**: Demonstrates the advantages of modern PTP fabrics like Intel's QuickPath Interconnect (QPI), allowing for multiple simultaneous data transfers.
- **PCI Express (PCIe) Protocol**:
    - **Layered Protocol**: Shows how a data packet is encapsulated as it moves through the Transaction, Data Link, and Physical layers.
    - **Split Transactions**: Illustrates how a request and its completion are handled asynchronously.
    - **ACK/NAK Mechanism**: Demonstrates the error-checking and retransmission process for reliable data delivery.
    - **128b/130b Encoding**: Visualizes the encoding and scrambling process used for signal integrity.

## How to Use

1.  **Web Server**: This is a PHP-based project. You need a web server with PHP support (e.g., Apache, Nginx) to run it. Tools like XAMPP, MAMP, or WAMP provide an easy setup.
2.  **Deployment**: Place all the project files in a directory served by your web server (e.g., `htdocs` in XAMPP).
3.  **Access**: Open your web browser and navigate to the project's URL (e.g., `index.php`). From there, you can navigate to the specific lecture simulators.
4.  **Interact**:
    -   Use the dropdown menu to select a scenario.
    -   Click the **"Next Step"** button to advance the simulation one step at a time.
    -   Read the explanations to understand what is happening at each stage.

## File Structure

-   `index.php`: The main landing page with links to the simulators.
-   `lecture2a.php`: Main page for the CPU and Instruction Cycle simulator.
-   `lecture2b.php`: Main page for the Interconnection Structures simulator.
-   `lecture2a.js`: JavaScript logic for the CPU simulation.
-   `lecture2b.js`: JavaScript logic for the Interconnects simulation.
-   `header.php`: Common HTML header, including styles and navigation.
-   `translations.php`: Contains all English and Russian text strings for the UI and explanations.
-   `*.md`: Markdown files containing lecture scripts and project coverage details.

## Development Philosophy

This project was entirely **vibe-coded**.
